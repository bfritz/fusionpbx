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

//if there are no items in the menu then add the default menu
	$sql = "SELECT count(*) as count FROM v_menus ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$sub_result = $prep_statement->fetch(PDO::FETCH_ASSOC);
	unset ($prep_statement);
	if ($sub_result['count'] > 0) {
		if ($display_type == "text") {
			echo "	Menu:			no change\n";
		}
	}
	else {
		//create the guid
			$menu_guid = 'B4750C3F-2A86-B00D-B7D0-345C14ECA286';
		//set the defaults
			$menu_name = 'default';
			$menu_language = 'en';
			$menu_desc = '';
		//add the menu
			$sql = "insert into v_menus ";
			$sql .= "(";
			$sql .= "menu_guid, ";
			$sql .= "menu_name, ";
			$sql .= "menu_language, ";
			$sql .= "menu_desc ";
			$sql .= ")";
			$sql .= "values ";
			$sql .= "(";
			$sql .= "'".$menu_guid."', ";
			$sql .= "'$menu_name', ";
			$sql .= "'$menu_language', ";
			$sql .= "'$menu_desc' ";
			$sql .= ")";
			$db->exec(check_sql($sql));
			unset($sql);
		//add the menu items
			require_once "includes/classes/menu.php";
			$menu = new menu;
			$menu->db = $db;
			$menu->menu_guid = $menu_guid;
			$menu->restore();
			unset($menu);
			if ($display_type == "text") {
				echo "	Menu:			added\n";
			}
		//assign all tenants to the default menu
			$sql = "update v_system_settings ";
			$sql .= "set v_menu_guid = '".$menu_guid."' ";
			$db->exec(check_sql($sql));
			unset($sql);
	}
	unset($prep_statement, $sub_result);

//if there are no groups listed in v_menu_item_groups then add the default groups
	$sql = "SELECT * FROM v_menus ";
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	$resultcount = count($result);
	foreach($result as $field) {
		//get the menu_guid
			$menu_guid = $field['menu_guid'];
		//check each menu to see if there are items in the menu assigned to it
			$sql = "";
			$sql .= "select count(*) as count from v_menu_item_groups ";
			$sql .= "where menu_guid = '$menu_guid' ";
			$prep_statement = $db->prepare($sql);
			$prep_statement->execute();
			$sub_result = $prep_statement->fetch(PDO::FETCH_ASSOC);
			unset ($prep_statement);
			if ($sub_result['count'] == 0) {
				//no menu item groups found add the defaults
					foreach($apps as $app) {
						foreach ($app['menu'] as $sub_row) {
							foreach ($sub_row['groups'] as $group) {
								//add the record
								$sql = "insert into v_menu_item_groups ";
								$sql .= "(";
								$sql .= "menu_guid, ";
								$sql .= "menu_item_guid, ";
								$sql .= "group_id ";
								$sql .= ")";
								$sql .= "values ";
								$sql .= "(";
								$sql .= "'$menu_guid', ";
								$sql .= "'".$sub_row['guid']."', ";
								$sql .= "'".$group."' ";
								$sql .= ")";
								$db->exec($sql);
								unset($sql);
							}
						}
					}
			}
	}

?>