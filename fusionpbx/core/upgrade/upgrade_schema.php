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

//check the permission
	if(defined('STDIN')) {
		$document_root = str_replace("\\", "/", $_SERVER["PHP_SELF"]);
		preg_match("/^(.*)\/core\/.*$/", $document_root, $matches);
		$document_root = $matches[1];
		set_include_path($document_root);
		require_once "includes/config.php";
		$_SERVER["DOCUMENT_ROOT"] = $document_root;
		$display_type = 'text'; //html, text
	}
	else {
		include "root.php";
		require_once "includes/config.php";
		require_once "includes/checkauth.php";
		if (permission_exists('upgrade_schema') || ifgroup("superadmin")) {
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

//load the default database into memory and compare it with the active database
	require_once "includes/lib_schema.php";
	db_upgrade_schema ($db, $db_type, $db_name, $display_results);
	unset($apps);

//get the list of installed apps from the core and mod directories
	$config_list = glob($_SERVER["DOCUMENT_ROOT"] . PROJECT_PATH . "/*/*/v_config.php");
	$x=0;
	foreach ($config_list as &$config_path) {
		include($config_path);
		$x++;
	}

//if there are no permissions listed in v_group_permissions then set the default permissions
	$sql = "";
	$sql .= "select count(*) as count from v_group_permissions ";
	$sql .= "where v_id = $v_id ";
	$prep_statement = $db->prepare($sql);
	$prep_statement->execute();
	$result = $prep_statement->fetch();
	unset ($prep_statement);
	if ($result['count'] > 0) {
		if ($display_type == "text") {
			echo "Goup Permissions: 	no change\n";
		}
	}
	else {
		if ($display_type == "text") {
			echo "Goup Permissions: 	added\n";
		}
		//no permissions found add the defaults
			$db->beginTransaction();
			foreach($apps as $app) {
				foreach ($app['permissions'] as $row) {
					foreach ($row['groups'] as $group) {
						//add the record
						$sql = "insert into v_group_permissions ";
						$sql .= "(";
						$sql .= "v_id, ";
						$sql .= "permission_id, ";
						$sql .= "group_id ";
						$sql .= ")";
						$sql .= "values ";
						$sql .= "(";
						$sql .= "'$v_id', ";
						$sql .= "'".$row['name']."', ";
						$sql .= "'".$group."' ";
						$sql .= ")";
						$db->exec($sql);
						unset($sql);
					}
				}
			}
			$db->commit();
	}

//if there are no groups listed in v_menu_groups then add the default groups
	$sql = "";
	$sql .= "select count(*) as count from v_menu_groups ";
	$sql .= "where v_id = $v_id ";
	$prep_statement = $db->prepare($sql);
	$prep_statement->execute();
	$result = $prep_statement->fetch();
	unset ($prep_statement);
	if ($result['count'] > 0) {
		if ($display_type == "text") {
			echo "Menu Groups: 		no change\n";
		}
	}
	else {
		if ($display_type == "text") {
			echo "Menu Groups: 		added\n";
		}
		//no menu groups found add the defaults
			$db->beginTransaction();
			foreach($apps as $app) {
				foreach ($app['menu'] as $row) {
					foreach ($row['groups'] as $group) {
						//add the record
						$sql = "insert into v_menu_groups ";
						$sql .= "(";
						$sql .= "v_id, ";
						$sql .= "menu_guid, ";
						$sql .= "group_id ";
						$sql .= ")";
						$sql .= "values ";
						$sql .= "(";
						$sql .= "'$v_id', ";
						$sql .= "'".$row['guid']."', ";
						$sql .= "'".$group."' ";
						$sql .= ")";
						$db->exec($sql);
						unset($sql);
					}
				}
			}
			$db->commit();
	}

//get the list of installed apps from the core and mod directories
	$default_list = glob($_SERVER["DOCUMENT_ROOT"] . PROJECT_PATH . "/*/*/v_defaults.php");
	foreach ($default_list as &$default_path) {
		include($default_path);
	}

if ($display_results && $display_type == "html") {
	echo "<br />\n";
	echo "<br />\n";
	require_once "includes/footer.php";
}

?>