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

//get any system -> variables defined in the 'provision;
	$sql = "";
	$sql .= "select * from v_vars ";
	$sql .= "where v_id = '$v_id' ";
	$sql .= "and var_enabled= 'true' ";
	$sql .= "and var_cat = 'Provision' ";
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$provision_variables_array = $prepstatement->fetchAll();
	foreach ($provision_variables_array as &$row) {
		if ($row['var_name'] == "password") {
			$var_name = $row['var_name'];
			$var_value = $row['var_value'];
			$$var_name = $var_value;
		}
	}

//if password was defined in the system -> variables page then require the password.
	if (strlen($password) > 0) {
		//deny access if the password doesn't match
			if ($password != $_GET['password']) {
				usleep(rand(1000000,3500000));//1-3.5 seconds.
				echo "access denied";
				return;
			}
	}

//test URL without rewrite
	//http://10.2.0.2/mod/provision/?mac=xx-xx-xx-xx-xx-xx&password=555555

//define variables from HTTP GET
	$mac = $_GET['mac'];
	$mac = strtolower($mac);
	$mac = str_replace(":", "-", $mac);
	if (strlen($mac) == 12) { 
		$mac = substr($mac, 0,2).'-'.substr($mac, 2,2).'-'.substr($mac, 4,2).'-'.substr($mac, 6,2).'-'.substr($mac, 8,2).'-'.substr($mac, 10,2);
	}
	$file = $_GET['file'];

//check to see if the mac_address exists in v_hardware_phones
	if (mac_exists_in_v_hardware_phones($db, $mac)) {
		//get the phone_template
			$sql = "SELECT phone_template, phone_vendor FROM v_hardware_phones ";
			$sql .= "where v_id=:v_id ";
			$sql .= "and phone_mac_address=:mac ";
			$prepstatement2 = $db->prepare(check_sql($sql));
			if ($prepstatement2) {
				$prepstatement2->bindParam(':v_id', $v_id);
				$prepstatement2->bindParam(':mac', $mac);
				$prepstatement2->execute();
				$row = $prepstatement2->fetch();
				$phone_template = $row['phone_template'];
				$phone_vendor = $row['phone_vendor'];
			}
		//find a template that was defined on another phone and use that as the default.
			if (strlen($phone_template) == 0) {
				$sql = "SELECT phone_template, phone_vendor FROM v_hardware_phones ";
				$sql .= "where v_id=:v_id ";
				$sql .= "and phone_template like '%/%' ";
				$prepstatement3 = $db->prepare(check_sql($sql));
				if ($prepstatement3) {
					$prepstatement3->bindParam(':v_id', $v_id);
					$prepstatement3->bindParam(':mac', $mac);
					$prepstatement3->execute();
					$row = $prepstatement3->fetch();
					$phone_template = $row['phone_template'];
					$phone_vendor = $row['phone_vendor'];
				}
			}
	}
	else {
		//mac does not exist in v_hardware_phones add it to the table
		//use the mac address to find the vendor
			switch (substr($mac, 0, 8)) {
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
			case "00-15-65":
				$phone_vendor = "yealink";
				break;
			default:
				$phone_vendor = "";
			}


		//use the user_agent to pre-assign a template for 1-hit provisioning. Enter the a unique string to match in the user agent, and the template it should match.
			$template_list=array(  
					"Linksys/SPA-2102"=>"linksys/spa2102",
					"Linksys/SPA-3102"=>"linksys/spa3102"
					);

			foreach ($template_list as $key=>$val){
				if(stripos($_SERVER['HTTP_USER_AGENT'],$key)!== false) {
					$phone_template=$val;
					break;
				}
			}
			unset($template_list);

		//the mac address does not exist in the table so add it
			$sql = "insert into v_hardware_phones ";
			$sql .= "(";
			$sql .= "v_id, ";
			$sql .= "phone_mac_address, ";
			$sql .= "phone_vendor, ";
			$sql .= "phone_model, ";
			$sql .= "phone_provision_enable, ";
			$sql .= "phone_template, ";
			$sql .= "phone_username, ";
			$sql .= "phone_password, ";
			$sql .= "phone_description ";
			$sql .= ")";
			$sql .= "values ";
			$sql .= "(";
			$sql .= "'$v_id', ";
			$sql .= "'$mac', ";
			$sql .= "'$phone_vendor', ";
			$sql .= "'', ";
			$sql .= "'true', ";
			$sql .= "'$phone_template', ";
			$sql .= "'', ";
			$sql .= "'', ";
			$sql .= "'auto' ";
			$sql .= ")";
			$db->exec(check_sql($sql));
			unset($sql);
	}

//if $file is not provided then look for a default file that exists
	if (strlen($file) == 0) { 
		if (file_exists($_SERVER["DOCUMENT_ROOT"].PROJECT_PATH."/includes/templates/provision/".$phone_template ."/{v_mac}")) {
			$file = "{v_mac}";
		}
		if (file_exists($_SERVER["DOCUMENT_ROOT"].PROJECT_PATH."/includes/templates/provision/".$phone_template ."/{v_mac}.xml")) {
			$file = "{v_mac}.xml";
		}
		if (file_exists($_SERVER["DOCUMENT_ROOT"].PROJECT_PATH."/includes/templates/provision/".$phone_template ."/{v_mac}.cfg")) {
			$file = "{v_mac}.cfg";
		}
		if (strlen($file) == 0) { 
			echo "file not found";
			exit;
		}
	}
	else {
		//make sure the file exists
		if (file_exists($_SERVER["DOCUMENT_ROOT"].PROJECT_PATH."/includes/templates/provision/".$phone_template ."/".$file)) {
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
	$file_contents = file_get_contents($_SERVER["DOCUMENT_ROOT"].PROJECT_PATH."/includes/templates/provision/".$phone_template ."/".$file);

//replace the variables in the template in the future loop through all the line numbers to do a replace for each possible line number

	//lookup the provisioning information for this MAC address.
		$sql = "";
		$sql .= "select * from v_extensions ";
		$sql .= "where provisioning_list like '%$mac%' ";
		$sql .= "and v_id = '$v_id' ";
		$prepstatement = $db->prepare(check_sql($sql));
		$prepstatement->execute();
		$result = $prepstatement->fetchAll();
		foreach ($result as &$row) {
			$provisioning_list = $row["provisioning_list"];
			$provisioning_list_array = explode("|", $provisioning_list);
			foreach ($provisioning_list_array as &$prov_row) {
				$prov_row_array = explode(":", $prov_row);
				if ($prov_row_array[0] == $mac) {
					$line_number = $prov_row_array[1];
					$file_contents = str_replace("{v_line".$line_number."_server_address}", $v_domain, $file_contents);
					$file_contents = str_replace("{v_line".$line_number."_displayname}", $row["effective_caller_id_name"], $file_contents);
					$file_contents = str_replace("{v_line".$line_number."_shortname}", $row["extension"], $file_contents);
					$file_contents = str_replace("{v_line".$line_number."_user_id}", $row["extension"], $file_contents);
					$file_contents = str_replace("{v_line".$line_number."_user_password}", $row["password"], $file_contents);
				}
			}

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
		}
		unset ($prepstatement);

	//replace the variables in the template in the future loop through all the line numbers to do a replace for each possible line number
		$file_contents = str_replace("{v_mac}", $mac, $file_contents);
		$file_contents = str_replace("{v_domain}", $v_domain, $file_contents);
		$file_contents = str_replace("{v_server1_address}", $server1_address, $file_contents);
		$file_contents = str_replace("{v_proxy1_address}", $proxy1_address, $file_contents);
		$file_contents = str_replace("{v_password}", $password, $file_contents);

	//cleanup any remaining variables
		for ($i = 1; $i <= 100; $i++) {
			$file_contents = str_replace("{v_line".$i."_server_address}", "", $file_contents);
			$file_contents = str_replace("{v_line".$i."_displayname}", "", $file_contents);
			$file_contents = str_replace("{v_line".$i."_shortname}", "", $file_contents);
			$file_contents = str_replace("{v_line".$i."_user_id}", "", $file_contents);
			$file_contents = str_replace("{v_line".$i."_user_password}", "", $file_contents);
		}

	//replace the dynamic provision variables that are defined in the system -> variables page
		foreach ($provision_variables_array as &$row) {
			if (substr($var_name, 0, 2) == "v_") {
				$file_contents = str_replace('{'.$row[var_name].'}', $row[var_value], $file_contents);
			}
		}

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
	$sql .= "where v_id=:v_id ";
	$sql .= "and phone_mac_address=:mac ";
	$prepstatement = $db->prepare(check_sql($sql));
	if ($prepstatement) {
		$prepstatement->bindParam(':v_id', $v_id);
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

exit;
?>
