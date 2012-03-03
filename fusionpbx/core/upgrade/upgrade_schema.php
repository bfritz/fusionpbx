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

//check the permission
	if(defined('STDIN')) {
		$document_root = str_replace("\\", "/", $_SERVER["PHP_SELF"]);
		preg_match("/^(.*)\/core\/.*$/", $document_root, $matches);
		$document_root = $matches[1];
		set_include_path($document_root);
		require_once "includes/require.php";
		$_SERVER["DOCUMENT_ROOT"] = $document_root;
		$display_type = 'text'; //html, text
	}
	else {
		include "root.php";
		require_once "includes/require.php";
		require_once "includes/checkauth.php";
		if (permission_exists('upgrade_schema') || if_group("superadmin")) {
			//echo "access granted";
		}
		else {
			echo "access denied";
			exit;
		}
		require_once "includes/header.php";
		$display_type = 'html'; //html, text
	}

//set the default
	if (!isset($display_results)) {
		$display_results = true;
	}

//copy the files and directories from includes/install
	require_once "includes/classes/install.php";
	$install = new install;
	$install->domain_uuid = $domain_uuid;
	$install->domain_name = $domain;
	$install->switch_conf_dir = $_SESSION['switch']['conf']['dir'];
	$install->switch_scripts_dir = $_SESSION['switch']['scripts']['dir'];
	$install->switch_sounds_dir = $_SESSION['switch']['sounds']['dir'];
	$install->switch_recordings_dir = $_SESSION['switch']['recordings']['dir'];
	$install->copy();
	//print_r($install->result);

//load the default database into memory and compare it with the active database
	require_once "includes/lib_schema.php";
	db_upgrade_schema ($db, $db_type, $db_name, $display_results);
	unset($apps);

//get the list of installed apps from the core and mod directories
	$config_list = glob($_SERVER["DOCUMENT_ROOT"] . PROJECT_PATH . "/*/*/app_config.php");
	$x=0;
	foreach ($config_list as &$config_path) {
		include($config_path);
		$x++;
	}

//loop through all domains
	$sql = "";
	$sql .= "select * from v_domains ";
	$v_prep_statement = $db->prepare(check_sql($sql));
	$v_prep_statement->execute();
	$main_result = $v_prep_statement->fetchAll(PDO::FETCH_ASSOC);
	$domain_count = count($main_result);
	foreach ($main_result as &$row) {
		//get the values from database and set them as php variables
			$domain_uuid = $row["domain_uuid"];
			$domain_name = $row["domain_name"];

		//get the context
			if ($domain_count == 1) {
				$context = "default";
			}
			else {
				$context = $domain_name;
			}

		//show the domain when display_type is set to text
			if ($display_type == "text") {
				echo "\n";
				echo $domain_name;
				echo "\n";
			}

		//get the default settings
			$sql = "select * from v_default_settings ";
			$sql .= "where default_setting_enabled = 'true' ";
			$prep_statement = $db->prepare($sql);
			$prep_statement->execute();
			$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
			foreach($result as $row) {
				$name = $row['default_setting_name'];
				$category = $row['default_setting_category'];
				$subcategory = $row['default_setting_subcategory'];	
				if (strlen($subcategory) == 0) {
					$_SESSION[$category][$name] = $row['default_setting_value'];
				}
				else {
					$_SESSION[$category][$subcategory][$name] = $row['default_setting_value'];
				}
			}

		//get the domains settings
			$sql = "select * from v_domain_settings ";
			$sql .= "where domain_uuid = '".$_SESSION["domain_uuid"]."' ";
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

		//recordings add the domain to the path if there is more than one domains
			if (count($_SESSION["domains"]) > 1) {
				if (strlen($_SESSION['switch']['recordings']['dir']) > 0) {
					if (substr($_SESSION['switch']['recordings']['dir'], -strlen($domain_name)) != $domain_name) {
						$_SESSION['switch']['recordings']['dir'] = $_SESSION['switch']['recordings']['dir'].'/'.$domain_name;
					}
				}
			}

		//get the list of installed apps from the core and mod directories and execute the php code in app_defaults.php
			$default_list = glob($_SERVER["DOCUMENT_ROOT"] . PROJECT_PATH . "/*/*/app_defaults.php");
			foreach ($default_list as &$default_path) {
				include($default_path);
			}

	} //end the loop
	unset ($v_prep_statement);

//clear the session variables
	unset($_SESSION['domain']);
	unset($_SESSION['switch']);

if ($display_results && $display_type == "html") {
	echo "<br />\n";
	echo "<br />\n";
	require_once "includes/footer.php";
}

?>