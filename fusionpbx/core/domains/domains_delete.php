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
 Portions created by the Initial Developer are Copyright (C) 2008-2012
 the Initial Developer. All Rights Reserved.

 Contributor(s):
 Mark J Crane <markjcrane@fusionpbx.com>
*/
require_once "root.php";
require_once "includes/require.php";
require_once "includes/checkauth.php";
if (if_group("admin") || if_group("superadmin")) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

if (count($_GET)>0) {
	$id = check_str($_GET["id"]);
}

if (strlen($id) > 0) {
	//get the domain using the id
		$sql = "";
		$sql .= "select * from v_domains ";
		$sql .= "where domain_uuid = '$id' ";
		$prep_statement = $db->prepare(check_sql($sql));
		$prep_statement->execute();
		$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
		foreach ($result as &$row) {
			$domain_name = $row["domain_name"];
		}
		unset ($prep_statement);

	//delete the domain
		$sql = "";
		$sql .= "delete from v_domains ";
		$sql .= "where domain_uuid = '$domain_uuid' ";
		$sql .= "and domain_uuid = '$id' ";
		$prep_statement = $db->prepare(check_sql($sql));
		$prep_statement->execute();
		unset($sql);

	//get the $apps array from the installed apps from the core and mod directories
		$config_list = glob($_SERVER["DOCUMENT_ROOT"] . PROJECT_PATH . "/*/*/app_config.php");
		$x=0;
		foreach ($config_list as &$config_path) {
			include($config_path);
			$x++;
		}

	//delete the domain data from all tables in the database
		$db->beginTransaction();
		foreach ($apps as &$app) {
			foreach ($app['db'] as $row) {
				$table_name = $row['table'];
				foreach ($row['fields'] as $field) {
					if ($field['name'] == "domain_uuid") {
						$sql = "delete from $table_name where domain_uuid = '$id' ";
						$db->query($sql);
					}
				}
			}
		}
		$db->commit();

	//get the domains settings
		$sql = "select * from v_domain_settings ";
		$sql .= "where domain_uuid = '".$id."' ";
		$sql .= "and domain_setting_enabled = 'true' ";
		$prep_statement = $db->prepare($sql);
		$prep_statement->execute();
		$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
		foreach($result as $row) {
			$name = $row['domain_setting_name'];
			$category = $row['domain_setting_category'];
			$subcategory = $row['domain_setting_subcategory'];	
			if (strlen($subcategory) == 0) {
				//$$category[$name] = $row['domain_setting_value'];
				$_SESSION[$category][$name] = $row['domain_setting_value'];
			}
			else {
				//$$category[$subcategory][$name] = $row['domain_setting_value'];
				$_SESSION[$category][$subcategory][$name] = $row['domain_setting_value'];
			}
		}

	if (strlen($domain_name) > 0) {
		//set the needle
			if (count($_SESSION["domains"]) > 1) {
				$v_needle = 'v_'.$domain_name.'_';
			}
			else {
				$v_needle = 'v_';
			}

		//delete the dialplan
			unlink($_SESSION['switch']['dialplan']['dir'].'/'.$domain_name.'.xml');
			if (strlen($_SESSION['switch']['dialplan']['dir']) > 0) {
				system('rm -rf '.$_SESSION['switch']['dialplan']['dir'].'/'.$domain_name);
			}

		//delete the dialplan public
			unlink($_SESSION['switch']['dialplan']['dir'].'/public/'.$domain_name.'.xml');
			if (strlen($_SESSION['switch']['dialplan']['dir']) > 0) {
				system('rm -rf '.$_SESSION['switch']['dialplan']['dir'].'/public/'.$domain_name);	
			}

		//delete the extension
			unlink($_SESSION['switch']['extensions']['dir'].'/'.$domain_name.'.xml');
			if (strlen($_SESSION['switch']['extensions']['dir']) > 0) {
				system('rm -rf '.$_SESSION['switch']['extensions']['dir'].'/'.$domain_name);
			}

		//delete fax
			if (strlen($_SESSION['switch']['storage']['dir']) > 0) {
				system('rm -rf '.$_SESSION['switch']['storage']['dir'].'/fax/'.$domain_name);
			}

		//delete the gateways
			if($dh = opendir($_SESSION['switch']['gateways']['dir']."")) {
				$files = Array();
				while($file = readdir($dh)) {
					if($file != "." && $file != ".." && $file[0] != '.') {
						if(is_dir($dir . "/" . $file)) {
							//this is a directory do nothing
						} else {
							//check if file extension is xml
							if (strpos($file, $v_needle) !== false && substr($file,-4) == '.xml') {
								unlink($_SESSION['switch']['gateways']['dir']."/".$file);
							}
						}
					}
				}
				closedir($dh);
			}

		//delete the hunt group lua scripts
			$v_prefix = 'v_huntgroup_'.$domain_name.'_';
			if($dh = opendir($_SESSION['switch']['scripts']['dir'])) {
				$files = Array();
				while($file = readdir($dh)) {
					if($file != "." && $file != ".." && $file[0] != '.') {
						if(is_dir($dir . "/" . $file)) {
							//this is a directory
						} else {
							if (substr($file,0, strlen($v_prefix)) == $v_prefix && substr($file,-4) == '.lua') {
								unlink($_SESSION['switch']['scripts']['dir'].'/'.$file);
							}
						}
					}
				}
				closedir($dh);
			}

		//delete the ivr menu
			if($dh = opendir($_SESSION['switch']['conf']['dir']."/ivr_menus/")) {
				$files = Array();
				while($file = readdir($dh)) {
					if($file != "." && $file != ".." && $file[0] != '.') {
						if(is_dir($dir . "/" . $file)) {
							//this is a directory
						} else {
							if (strpos($file, $v_needle) !== false && substr($file,-4) == '.xml') {
								//echo "file: $file<br />\n";
								unlink($_SESSION['switch']['conf']['dir']."/ivr_menus/".$file);
							}
						}
					}
				}
				closedir($dh);
			}

		//delete the recordings
			if (strlen($_SESSION['switch'][recordings]['dir']) > 0) {
				system('rm -rf '.$_SESSION['switch']['recordings']['dir'].'/'.$domain_name);
			}

		//delete voicemail
			if (strlen($_SESSION['switch']['voicemail']['dir']) > 0) {
				system('rm -rf '.$_SESSION['switch']['voicemail']['dir'].'/'.$domain_name);
			}
	}

	//apply settings reminder
		$_SESSION["reload_xml"] = true;

	//clear the domains session array to update it
		unset($_SESSION["domains"]);
		unset($_SESSION["domain_uuid"]);
		unset($_SESSION["domain_name"]);
		unset($_SESSION['domain']);
		unset($_SESSION['switch']);
}

//redirect the browser
	require_once "includes/header.php";
	echo "<meta http-equiv=\"refresh\" content=\"2;url=domains.php\">\n";
	echo "<div align='center'>\n";
	echo "Delete Complete\n";
	echo "</div>\n";
	require_once "includes/footer.php";
	return;

?>