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
	Copyright (C) 2010
	All Rights Reserved.

	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>
*/

//define the follow me class
	class menu {
		var $menu_guid;

		//delete items in the menu that are not protected
			function delete() {
				//set the variable
					$db = $this->db;
					$menu_guid = $this->menu_guid;
				//remove the old menu
					$sql  = "delete from v_menu_items ";
					$sql .= "where menu_guid = '$menu_guid' ";
					$sql .= "and (menu_item_protected <> 'true' ";
					$sql .= "or menu_item_protected is null ";
					$sql .= "or menu_item_protected = '');";
					$db->exec(check_sql($sql));
			}

		//restore the menu
			function restore() {
				//set the variable
					$db = $this->db;
					$menu_guid = $this->menu_guid;

				//get the $apps array from the installed apps from the core and mod directories
					$config_list = glob($_SERVER["DOCUMENT_ROOT"] . PROJECT_PATH . "/*/*/v_config.php");
					$x=0;
					foreach ($config_list as &$config_path) {
						include($config_path);
						$x++;
					}

				//use the app array to restore the default menu
					$db->beginTransaction();
					foreach ($apps as $row) {
						foreach ($row['menu'] as $menu) {
							//set the variables
								$menu_item_title = $menu['title']['en'];
								$menu_item_language = 'en';
								$menu_item_guid = $menu['guid'];
								$menu_item_parent_guid = $menu['parent_guid'];
								$menu_item_category = $menu['category'];
								$menu_item_path = $menu['path'];
								$menu_item_order = $menu['order'];
								$menu_item_desc = $menu['desc'];
								if (strlen($menu_item_order) == 0) {
									$menu_item_order = 1;
								}

							//if the item guid is not currently in the db then add it
								$sql = "select * from v_menu_items ";
								$sql .= "where menu_guid = '$menu_guid' ";
								$sql .= "and menu_item_guid = '$menu_item_guid' ";
								$prepstatement = $db->prepare(check_sql($sql));
								if ($prepstatement) {
									$prepstatement->execute();
									$result = $prepstatement->fetchAll(PDO::FETCH_ASSOC);
									if (count($result) == 0) {
										//insert the default menu into the database
											$sql = "insert into v_menu_items ";
											$sql .= "(";
											$sql .= "menu_guid, ";
											//$sql .= "menu_item_language, ";
											$sql .= "menu_item_title, ";
											$sql .= "menu_item_str, ";
											$sql .= "menu_item_category, ";
											$sql .= "menu_item_desc, ";
											$sql .= "menu_item_order, ";
											$sql .= "menu_item_guid, ";
											$sql .= "menu_item_parent_guid ";
											$sql .= ") ";
											$sql .= "values ";
											$sql .= "(";
											$sql .= "'$menu_guid', ";
											//$sql .= "'$menu_item_language', ";
											$sql .= "'$menu_item_title', ";
											$sql .= "'$menu_item_path', ";
											$sql .= "'$menu_item_category', ";
											$sql .= "'$menu_item_desc', ";
											$sql .= "'$menu_item_order', ";
											$sql .= "'$menu_item_guid', ";
											$sql .= "'$menu_item_parent_guid' ";
											$sql .= ")";
											if ($menu_item_guid == $menu_item_parent_guid) {
												//echo $sql."<br />\n";
											}
											else {
												$db->exec(check_sql($sql));
											}
											unset($sql);
									}
								}
						}
					}

				//if there are no groups listed in v_menu_item_groups under menu_guid then add the default groups
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

				//save the changes to the database
					$db->commit();
			} //end function
	} //class

?>