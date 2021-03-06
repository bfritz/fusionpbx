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
	Copyright (C) 2008-2010 All Rights Reserved.

	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>
*/
include "root.php";
require_once "includes/config.php";

//set default variables
	$dir_count = 0;
	$file_count = 0;
	$row_count = 0;
	$tmp_array = '';

//test URL without rewrite
	//http://10.2.0.2/mod/provision/?mac=xx-xx-xx-xx-xx-xx

//define variables from HTTP GET
	$mac = $_GET['mac'];
	$mac = str_replace(":", "-", $mac);
	if (strlen($mac) == 12) { 
		$mac = substr($mac, 0,2).'-'.substr($mac, 2,2).'-'.substr($mac, 4,2).'-'.substr($mac, 6,2).'-'.substr($mac, 8,2).'-'.substr($mac, 10,2);
	}
	$file = $_GET['file'];

//check to see if the mac_address exists in v_hardware_phones
	if (mac_exists_in_v_hardware_phones($db, $mac)) {
		//get the phone_template
			$sql = "SELECT phone_template, phone_vendor FROM v_hardware_phones ";
			//$sql .= "where v_id=:v_id ";
			$sql .= "where phone_mac_address=:mac ";
			$prepstatement2 = $db->prepare(check_sql($sql));
			//$prepstatement2->bindParam(':v_id', $v_id);
			if ($prepstatement2) {
				$prepstatement2->bindParam(':mac', $mac);
				$prepstatement2->execute();
				$row = $prepstatement2->fetch();
				$phone_template = $row['phone_template'];
				$phone_vendor = $row['phone_vendor'];
			}
	}
	else {
		//mac does not exist in v_hardware_phones add it to the table
		//use the mac address to find the vendor
			switch (substr(strtolower($mac), 0, 8)) {
			case "00-08-5d":
				$phone_vendor = "aastra";
				break;
			case "00-0e-08":
				$phone_vendor = "linksys";
				break;
			case "00-04-f2":
				$phone_vendor = "polycom";
				break;
			case "00-90-7a":
				$phone_vendor = "polycom";
				break;
			case "00-18-73":
				$phone_vendor = "cisco";
				break;
			case "00-04-5a":
				$phone_vendor = "linksys";
				break;
			case "00-06-25":
				$phone_vendor = "linksys";
				break;
			default:
				$phone_vendor = "";
			}

		//the mac address does not exist in the table so add it
			$sql = "insert into v_hardware_phones ";
			$sql .= "(";
			$sql .= "phone_mac_address, ";
			$sql .= "phone_vendor, ";
			$sql .= "phone_model, ";
			$sql .= "phone_provision_enable, ";
			$sql .= "phone_username, ";
			$sql .= "phone_password, ";
			$sql .= "phone_description ";
			$sql .= ")";
			$sql .= "values ";
			$sql .= "(";
			$sql .= "'$mac', ";
			$sql .= "'$phone_vendor', ";
			$sql .= "'', ";
			$sql .= "'true', ";
			$sql .= "'', ";
			$sql .= "'', ";
			$sql .= "'auto' ";
			$sql .= ")";
			$db->exec(check_sql($sql));
			unset($sql);
	}

//if $file is not provided then look for a default file that exists
	if (strlen($file) == 0) { 
		if (file_exists($_SERVER["DOCUMENT_ROOT"].PROJECT_PATH."/includes/provision_templates/".$phone_template ."/{v_mac}")) {
			$file = "{v_mac}";
		}
		if (file_exists($_SERVER["DOCUMENT_ROOT"].PROJECT_PATH."/includes/provision_templates/".$phone_template ."/{v_mac}.xml")) {
			$file = "{v_mac}.xml";
		}
		if (file_exists($_SERVER["DOCUMENT_ROOT"].PROJECT_PATH."/includes/provision_templates/".$phone_template ."/{v_mac}.cfg")) {
			$file = "{v_mac}.cfg";
		}
		if (strlen($file) == 0) { 
			echo "file not found";
			exit;
		}
	}
	else {
		//make sure the file exists
		if (file_exists($_SERVER["DOCUMENT_ROOT"].PROJECT_PATH."/includes/provision_templates/".$phone_template ."/".$file)) {
			echo "file not found";
			exit;
		}
	}

//log file for testing
	//$tmp_file = "/tmp/provisioning_log.txt";
	//$fh = fopen($tmp_file, 'w') or die("can't open file");
	//$tmp_string = $mac."\n";
	//fwrite($fh, $tmp_string);
	//fclose($fh);

//lookup the provisioning information for this MAC address.
	$sql = "";
	$sql .= "select * from v_extensions ";
	$sql .= "where provisioning_list like '%$mac%' ";
	$sql .= "and v_id = '$v_id' ";
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	foreach ($result as &$row) {
		//print_r($row);
		$provisioning_list = $row["provisioning_list"];

		$provisioning_list_array = explode("|", $provisioning_list);
		foreach ($provisioning_list_array as &$prov_row) {
			$prov_row_array = explode(":", $prov_row);
			if ($prov_row_array[0] == $mac) {
				$line_number = $prov_row_array[1];
			}
		}

		$extension = $row["extension"];
		$password = $row["password"];

		//$line1_displayname= "1001";
		$variable_name = "line".$line_number."_displayname";
		$$variable_name = $row["extension"];

		//$line1_shortname= "1001";
		$variable_name = "line".$line_number."_shortname";
		$$variable_name = $row["extension"];

		//$line1_user_id= "1001";
		$variable_name = "line".$line_number."_user_id";
		$$variable_name = $row["extension"];
		//echo "line1_user_id: ".$$variable_name."<br />";

		//$line1_user_password= "1234.";
		$variable_name = "line".$line_number."_user_password";
		$$variable_name = $row["password"];
		//echo "line1_user_password: ".$$variable_name."<br />";

		//$line1_server_address= "10.2.0.2";
		$variable_name = "line".$line_number."_server_address";
		$$variable_name = $v_domain; //defined in /includes/lib_switch.php

		//$user_list = $row["user_list"];
		//$vm_password = $row["vm_password"];
		//$vm_password = str_replace("#", "", $vm_password); //preserves leading zeros
		//$accountcode = $row["accountcode"];
		//$effective_caller_id_name = $row["effective_caller_id_name"];
		//$effective_caller_id_number = $row["effective_caller_id_number"];
		//$outbound_caller_id_name = $row["outbound_caller_id_name"];
		//$outbound_caller_id_number = $row["outbound_caller_id_number"];
		//$vm_mailto = $row["vm_mailto"];
		//$vm_attach_file = $row["vm_attach_file"];
		//$vm_keep_local_after_email = $row["vm_keep_local_after_email"];
		//$user_context = $row["user_context"];
		//$callgroup = $row["callgroup"];
		//$auth_acl = $row["auth_acl"];
		//$cidr = $row["cidr"];
		//$sip_force_contact = $row["sip_force_contact"];
		//$enabled = $row["enabled"];
		//$description = $row["description"];
		break; //limit to 1 row
	}
	unset ($prepstatement);

	//set variables for testing
		//$line1_displayname= "1001";
		//$line1_shortname= "1001";
		//$line1_user_id= "1001";
		//$line1_user_password= "1234.";
		//$line1_server_address= "10.2.0.2";
		//$line2_server_address= "";
		//$line2_displayname= "";
		//$line2_shortname= "";
		//$line2_user_id= "";
		//$line2_user_password= "";
		//$line2_server_address= "";
		//$server1_address= "10.2.0.2";
		//$server2_address= "";
		//$server3_address= "";
		//$proxy1_address= "10.2.0.2";
		//$proxy2_address= "";
		//$proxy3_address= "";

//get the contents of the template
	$file_contents = file_get_contents($_SERVER["DOCUMENT_ROOT"].PROJECT_PATH."/includes/provision_templates/".$phone_template ."/".$file);

//replace the variables in the template in the future loop through all the line numbers to do a replace for each possible line number
	$file_contents = str_replace("{v_mac}", $mac, $file_contents);
	$file_contents = str_replace("{v_domain}", $v_domain, $file_contents);
	$file_contents = str_replace("{v_line1_server_address}", $line1_server_address, $file_contents);
	$file_contents = str_replace("{v_line1_displayname}", $line1_displayname, $file_contents);
	$file_contents = str_replace("{v_line1_shortname}", $line1_shortname, $file_contents);
	$file_contents = str_replace("{v_line1_user_id}", $line1_user_id, $file_contents);
	$file_contents = str_replace("{v_line1_user_password}", $line1_user_password, $file_contents);
	$file_contents = str_replace("{v_line2_server_address}", $line2_server_address, $file_contents);
	$file_contents = str_replace("{v_line2_displayname}", $line2_displayname, $file_contents);
	$file_contents = str_replace("{v_line2_shortname}", $line2_shortname, $file_contents);
	$file_contents = str_replace("{v_line2_user_id}", $line2_user_id, $file_contents);
	$file_contents = str_replace("{v_line2_user_password}", $line2_user_password, $file_contents);
	$file_contents = str_replace("{v_line2_server_address}", $line2_server_address, $file_contents);
	$file_contents = str_replace("{v_server1_address}", $server1_address, $file_contents);
	//$file_contents = str_replace("{v_server2_address}", $server2_address, $file_contents);
	//$file_contents = str_replace("{v_server3_address}", $server3_address, $file_contents);
	$file_contents = str_replace("{v_proxy1_address}", $proxy1_address, $file_contents);
	//$file_contents = str_replace("{v_proxy2_address}", $proxy2_address, $file_contents);
	//$file_contents = str_replace("{v_proxy3_address}", $proxy3_address, $file_contents);

//deliver the customized config over HTTP/HTTPS

	//need to make sure content-type is correct
	$cfg_ext = ".cfg";
	if ($phone_vendor === "aastra" && strrpos($file, $cfg_ext, 0) === strlen($file) - strlen($cfg_ext)) {
		header ("content-type: text/plain");
	} else {
		header ("content-type: text/xml");
	}
	echo $file_contents;


function mac_exists_in_v_hardware_phones($db, $mac) {
	global $v_id;
	$sql = "SELECT count(*) as count FROM v_hardware_phones ";
	//$sql .= "where v_id=:v_id ";
	$sql .= "where phone_mac_address=:mac ";
	$prepstatement = $db->prepare(check_sql($sql));
	//$prepstatement->bindParam(':v_id', $v_id);
	if ($prepstatement) {
		$prepstatement->bindParam(':mac', $mac);
		$prepstatement->execute();
		$row = $prepstatement->fetch();
		$count = $row['count'];
		if ($row['count'] > 0) {
			return true;
		}
		else {
			return false;
		}
	}
	else {
		return false;
	}
}


//reserved for future use
	/*
	//get a list of mac addresses with arp -a
			//$tmp_arp = shell_exec('arp -a');
			//http://www.coffer.com/mac_find/
			/*
			$pattern = "/[0-9a-f][0-9a-f][:-]".
			"[0-9a-f][0-9a-f][:-]".
			"[0-9a-f][0-9a-f][:-]".
			"[0-9a-f][0-9a-f][:-]".
			"[0-9a-f][0-9a-f][:-]".
			"[0-9a-f][0-9a-f]/i";
			preg_match_all($pattern, $tmp_arp, $matches);
			$mac_array = $matches[0];
			$x = 0;
			foreach ($mac_array as $mac_address){
				switch (substr(strtolower($mac_address), 0, 8)) {
				case "00-0e-08":
					$phone_vendor = "linksys";
					break;
				default:
					$phone_vendor = "";
				}
				$x++;
			}

	//useful for saving the entire template to a directory for ftp or tftp
		clearstatcache();
		function recur_dir($dir) {
			global $tmp_array;
			global $dir_count;
			global $file_count;
			global $row_count;

			$htmldirlist = '';
			$htmlfilelist = '';
			$dirlist = opendir($dir);
			while ($file = readdir ($dirlist)) {
				if ($file != '.' && $file != '..') {
					$newpath = $dir.'/'.$file;
					$level = explode('/',$newpath);

					if (is_dir($newpath)) { //directories
						if (strlen($newpath) > 0) {
							//$relative_path = substr($newpath, strlen($_SERVER["DOCUMENT_ROOT"]), strlen($newpath)); //remove the $_SERVER["DOCUMENT_ROOT"]
							//$pos = strpos($relative_path, ".svn");
							$relative_path = $newpath;
							//if ($pos === false) {
								//echo $relative_path."<br />\n";
								$tmp_array[$row_count]['type'] = 'directory';
								$tmp_array[$row_count]['name'] = $file;
								$tmp_array[$row_count]['path'] = $relative_path;
								$tmp_array[$row_count]['last_mod'] = '';
								$tmp_array[$row_count]['md5'] = '';
								$tmp_array[$row_count]['size'] = '';
								$row_count++;
							//}
							$dir_count++;
						}

						$dirname = end($level);
						recur_dir($newpath);
					}
					else { //files
						if (strlen($newpath) > 0) {
							//$relative_path = substr($newpath, strlen($_SERVER["DOCUMENT_ROOT"]), strlen($newpath)); //remove the $_SERVER["DOCUMENT_ROOT"]
							//$pos = strpos($relative_path, ".svn");
							$relative_path = $newpath;
							//if ($pos === false) {
								//echo $relative_path."<br />\n";
								$tmp_array[$row_count]['type'] = 'file';
								$tmp_array[$row_count]['name'] = $file;
								$tmp_array[$row_count]['path'] = $relative_path;
								$tmp_array[$row_count]['last_mod'] = gmdate ("D, d M Y H:i:s T", filemtime($newpath));
								$tmp_array[$row_count]['md5'] = md5_file($newpath);
								$tmp_array[$row_count]['size'] = filesize($newpath); //round(filesize($newpath)/1024, 2);
								//echo $newpath."<br />\n";
								$row_count++;
							//}
							$file_count++;
						}
					}

				}
			}

			closedir($dirlist);
		}
		$prov_template_dir = $_SERVER["DOCUMENT_ROOT"].PROJECT_PATH."/includes/provision_templates/$template";
		recur_dir($prov_template_dir);

		foreach ($tmp_array as $row) {
			if (strlen($row['path']) > 0) {
				if ($row['type'] == "file") {
					$file_name = $row['name'];
					$path = $row['path'];
					$file_name = str_replace("{mac}", $mac, $file_name);
					$file_contents = file_get_contents($path);

					//replace the variables in the template
						$file_contents = str_replace("{v_mac}", $mac, $file_contents);
						$file_contents = str_replace("{v_domain}", $v_domain, $file_contents);
						$file_contents = str_replace("{v_line1_server_address}", $line1_server_address, $file_contents);
						$file_contents = str_replace("{v_line1_displayname}", $line1_displayname, $file_contents);
						$file_contents = str_replace("{v_line1_shortname}", $line1_shortname, $file_contents);
						$file_contents = str_replace("{v_line1_user_id}", $line1_user_id, $file_contents);
						$file_contents = str_replace("{v_line1_user_password}", $line1_user_password, $file_contents);
						$file_contents = str_replace("{v_line1_server_address}", $line1_server_address, $file_contents);
						$file_contents = str_replace("{v_line2_server_address}", $line2_server_address, $file_contents);
						$file_contents = str_replace("{v_line2_displayname}", $line2_displayname, $file_contents);
						$file_contents = str_replace("{v_line2_shortname}", $line2_shortname, $file_contents);
						$file_contents = str_replace("{v_line2_user_id}", $line2_user_id, $file_contents);
						$file_contents = str_replace("{v_line2_user_password}", $line2_user_password, $file_contents);
						$file_contents = str_replace("{v_line2_server_address}", $line2_server_address, $file_contents);
						$file_contents = str_replace("{v_server1_address}", $server1_address, $file_contents);
						//$file_contents = str_replace("{v_server2_address}", $server2_address, $file_contents);
						//$file_contents = str_replace("{v_server3_address}", $server3_address, $file_contents);
						$file_contents = str_replace("{v_proxy1_address}", $proxy1_address, $file_contents);
						//$file_contents = str_replace("{v_proxy2_address}", $proxy2_address, $file_contents);
						//$file_contents = str_replace("{v_proxy3_address}", $proxy3_address, $file_contents);

					//write the modified $file_contents for each file back to the ftp/tftp directory
						//$file_contents;
				}
			}
		}
	*/

exit;

?>