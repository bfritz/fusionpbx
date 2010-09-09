<?php
/*
	FusionPBX
	Version: MPL 1.1

	The contents of this file are subject to the Mozilla Public License Version
	1.1 (the "License"); you may not use this file except in compliance with
	the License. You may obtain a copy of the License at
	http://www.mozilla.org/MPL/

	Software distributed under the License is distributed on an "AS IS" basis,
	WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
	for the specific language governing rights and limitations under the
	License.

	The Original Code is FusionPBX

	The Initial Developer of the Original Code is
	Mark J Crane <markjcrane@fusionpbx.com>
	Portions created by the Initial Developer are Copyright (C) 2008-2010
	the Initial Developer. All Rights Reserved.

	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>
*/
include "root.php";
require_once "includes/config.php";
if (!isset($display_results)) {
	$display_results = true;
}
if (strlen($_SERVER['HTTP_USER_AGENT']) > 0) {
	require_once "includes/checkauth.php";
	if (ifgroup("superadmin")) {
		//echo "access granted";
		//exit;
	}
	else {
		echo "access denied";
		exit;
	}
}
else {
	$display_results = false; //true false
	//$display_type = 'csv'; //html, csv
}


ini_set('display_errors', '0');
ini_set(max_execution_time,3600);
clearstatcache();

if ($display_results) {
	require_once "includes/header.php";
}


//set path_array
	$sql = "";
	$sql .= "select * from v_src ";
	$sql .= "where v_id = '$v_id' ";
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	foreach ($result as &$row) {
		$path = trim($row["path"]);
		$path_array[$path][type] = $row["type"];
		$path_array[$path][last_mod] = $row["last_mod"];
	}
	unset ($prepstatement);
	//print_r($path_array);
	//exit;


$svn_url = 'http://fusionpbx.googlecode.com/svn';
$svn_path = '/trunk/fusionpbx';
$xml_str = file_get_contents($svn_url.$svn_path.'/includes/install/source.xml');
//echo $xml_str;

try {
	$xml = new SimpleXMLElement($xml_str);
}
catch(Exception $e) {
	//echo $e->getMessage();
}
//print_r($xml);
//$db->beginTransaction();

if ($display_results) {
	echo "<table width='100%' border='0' cellpadding='20' cellspacing='0'>\n";
	echo "<tr>\n";
	echo "<th>Type</th>\n";
	echo "<th>Last Modified</th>\n";
	echo "<th>Path</th>\n";
	echo "<th>Size</th>\n";
	//echo "<th>MD5 file</th>\n";
	//echo "<th>MD5 xml</th>\n";
	echo "<th>Action</th>\n";
	echo "<tr>\n";
}

foreach ($xml->src as $row) {
	//print_r($row);
	$xml_type = $row->type;
	$xml_relative_path = trim($row->path);
	$xml_last_mod = $row->last_mod;
	$md5_xml = $row->md5;
	$xml_size = $row->size;

	$new_path = $_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/'.$xml_relative_path;
	$md5_file = md5_file($new_path);
	if ($md5_xml == $md5_file){ 
		$md5_match = true; 
	}
	else {
		$md5_match = false; 
	}

	if (strlen($xml_relative_path) > 0) {
		if ($display_results) {
			if ($xml_type == 'file') {
				echo "<tr>\n";
				echo "<td class='rowstyle1'>$xml_type</td>\n";
				echo "<td class='rowstyle1'>$xml_last_mod</td>\n";
				echo "<td class='rowstyle1'>$xml_relative_path</td>\n";
				echo "<td class='rowstyle1'>$xml_size</td>\n";
				//echo "<td class='rowstyle1'>$md5_file</td>\n";
				//echo "<td class='rowstyle1'>$md5_xml</td>\n";
				//echo "<td class='rowstyle1'>$md5_match file_get_contents($svn_url.$svn_path.$xml_relative_path);</td>\n";
				echo "<td class='rowstyle1'>\n";
			}
		}

		//update the v_scr data
			if (strlen($path_array[$xml_relative_path]['type']) == 0) { 
				//insert a new record into the src table
					$sql = "insert into v_src ";
					$sql .= "(";
					$sql .= "v_id, ";
					$sql .= "type, ";
					$sql .= "last_mod, ";
					$sql .= "path ";
					$sql .= ")";
					$sql .= "values ";
					$sql .= "(";
					$sql .= "'$v_id', ";
					$sql .= "'$xml_type', ";
					$sql .= "'$xml_last_mod', ";
					$sql .= "'$xml_relative_path' ";
					$sql .= ")";
					//echo "$sql<br />\n";
			} 
			else {
				if ($md5_xml != md5_file($new_path)) {
					//update the src table
						$sql = "update v_src set ";
						$sql .= "type = '$xml_type', ";
						$sql .= "last_mod = '$xml_last_mod' ";
						$sql .= "where v_id = '$v_id' ";
						$sql .= "and path = '$xml_relative_path' ";
				}
			}

		if (file_exists($new_path)) {
			//if the path exists then compare the v_src $md5_xml to the md5_file($new_path) in the svn if they don't match save the new one
			if ($xml_type == 'file') {
				if ($md5_xml != md5_file($new_path)) {
					//get the remote file contents
						$file_content = file_get_contents($svn_url.$svn_path.$xml_relative_path);

					//make sure the string matches the file md5 that was recorded.
						if (strlen($file_content) > 0) {
							$tmp_fh = fopen($new_path, 'w');
							fwrite($tmp_fh, $file_content);
							fclose($tmp_fh);
						}

					//update the database
						if (strlen($sql) > 0) {
							$db->exec(check_sql($sql));
							//echo "$sql<br />\n";
						}
						unset($sql);

					//display the results
						if ($display_results) {
							echo "<strong style='color: #FF0000;'> ";
							if (is_writable($new_path)) {
								echo "updated ";
							}
							else {
								echo "not writable ";
							}
							//echo $md5_xml." ".md5($file_content)."<br />";
							//echo "length: ".strlen($file_content)."<br />";
							//echo "<textarea>$file_content</textarea>\n";

							echo "</strong>";
							echo "<br />\n";
							//echo "<strong style='color: #FF0000;'>updated ".strlen($file_content)." ".$svn_url.$svn_path.$xml_relative_path." ".$svn_url.$svn_path.$xml_relative_path.md5($file_content)."</strong>";
						}

				}
				else {
					if ($display_results) {
						echo "current "; //the file is up to date
					}
				}

				//unset the variable
					unset($file_content);
			}
		}
		else {
			//if the path does not exist create it and then add it to the database
			//echo "file is missing |";
			if ($xml_type == 'directory') {
				//make sure the directory exists
					mkdir (dirname($new_path), 0755, true);
			}
			if ($xml_type == 'file') {

				//make sure the directory exists
					if (!is_dir(dirname($new_path))){
						mkdir (dirname($new_path), 0755, true);
					}

				//get the remote file contents
					$file_content = file_get_contents($svn_url.$svn_path.$xml_relative_path);

				//make sure the string matches the file md5 that was recorded.
					if (strlen($file_content) > 0) {
						$tmp_fh = fopen($new_path, 'w');
						fwrite($tmp_fh, $file_content);
						fclose($tmp_fh);
					}
					unset($file_content);

				//display the results
					if ($display_results) {
						echo "updated ";
					}

				//unset the variable
					unset($file_content);
			}

			//update the database
				if (strlen($sql) > 0) {
					$db->exec(check_sql($sql));
					//echo "$sql<br />\n";
				}
				unset($sql);
		}

		if ($display_results) {
			if ($xml_type == 'file') {
				echo "&nbsp;";
				echo "</td>\n";
				echo "<tr>\n";
			}
		}
	}
}
//$db->commit();
if ($display_results) {
	echo "</table>\n";
	require_once "includes/footer.php";
}
?>