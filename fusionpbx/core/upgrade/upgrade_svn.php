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
		$path = ltrim($row["path"], "/");
		$path_array[$path][type] = $row["type"];
		$path_array[$path][last_mod] = $row["last_mod"];
		$path_array[$path][status] = $row["status"];
	}
	unset ($prepstatement);
	//print_r($path_array);
	//exit;


$svn_url = 'http://fusionpbx.googlecode.com/svn';
$svn_path = '/trunk/fusionpbx/';
$xml_str = file_get_contents($svn_url.$svn_path.'includes/install/source.xml');
//echo $xml_str;
//exit;
try {
	$xml = new SimpleXMLElement($xml_str);
}
catch(Exception $e) {
	//echo $e->getMessage();
}
//print_r($xml);
//exit;


//$db->beginTransaction();

if ($display_results) {
	echo "<table width='100%' border='0' cellpadding='20' cellspacing='0'>\n";
	echo "<tr>\n";
	//echo "<th>type</th>\n";
	echo "<th>Last Modified</th>\n";
	echo "<th>Path</th>\n";
	echo "<th>Action</th>\n";
	echo "<tr>\n";
}
foreach ($xml->src as $row) {
	//print_r($row);
	$type = ltrim($row->type, "/");
	$path = $row->path;
	$last_mod = $row->last_mod;

	//$new_path = strlen($svn_path)$path;
	//$path = 'trunk/fusionpbx/mod/xml_edit/header.php';
	$relative_path = substr($path, strlen($svn_path), strlen($path)); //remove the svn_path
	$new_path = $_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/'.$relative_path;
	if (strlen($relative_path) > 0) {
		if ($display_results) {
			if ($type == 'file') {
				echo "<tr>\n";
				//echo "<td class='rowstyle1'>$type</td>\n";
				echo "<td class='rowstyle1'>$last_mod</td>\n";
				echo "<td class='rowstyle1'>$relative_path</td>\n";
				echo "<td class='rowstyle1'>\n";
//				echo "|$new_path|";
			}
		}

		//echo $path_array[$path]['last_mod']." ";
		if (strlen($path_array[$path]['last_mod']) == 0) { 
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
				$sql .= "'$type', ";
				$sql .= "'$last_mod', ";
				$sql .= "'$relative_path' ";
				$sql .= ")";
				//echo "[insert] ";
		} 
		else {
			if ($last_mod != $path_array[$path][last_mod]) {
				//update the src table
					$sql = "update v_src set ";
					$sql .= "type = '$type', ";
					$sql .= "last_mod = '$last_mod' ";
					$sql .= "where v_id = '$v_id' ";
					$sql .= "and path = '$relative_path' ";
					//echo "[update] ";
			}
		}

		if (file_exists($new_path)) {
			//echo "file exists |";
			//if the path exists then compare the v_src last_mod to the last_mod in the svn if they don't match save the new one
			if ($type == 'file') {
				if ($last_mod != $path_array[$relative_path][last_mod]) {
					$file_content = file_get_contents($svn_url.$svn_path.$relative_path);
					//echo "<td>$file_content</td>\n";
					if (strlen($file_content) > 0) {
						$fh = fopen($new_path, 'w');
						fwrite($fh, $file_content);
						fclose($fh);
					}

					if (strlen($sql) > 0) {
						$db->exec(check_sql($sql));
					}
					unset($sql);
					if ($display_results) {
						echo "<strong>updated</strong>";
					}
				}
				else {
					if ($display_results) {
						echo "current "; //the file is up to date
					}
				}
			}
		}
		else {
			//echo "file is missing |";
			//if the path does not exist create it and then add it to the database
			if ($type == 'directory') {
				mkdir ($new_path, 0755, true);
				//echo $new_path;
			}
			if ($type == 'file') {
				if (!is_dir($svn_url.$svn_path)){
					mkdir ($svn_url.$svn_path, 0755, true);
				}
				$file_content = file_get_contents($svn_url.$svn_path.$relative_path);
				//echo "<td>$file_content</td>\n";
				if (strlen($file_content) > 0) {
					$fh = fopen($new_path, 'w');
					fwrite($fh, $file_content);
					fclose($fh);
				}
				if ($display_results) {
					echo "updated ";
				}
			}
			if (strlen($sql) > 0) {
				$db->exec(check_sql($sql));
			}
			unset($sql);
		}
		if ($display_results) {
			if ($type == 'file') {
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