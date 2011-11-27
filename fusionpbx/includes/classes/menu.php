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
				//set the variables
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

		//create the menu
			function build_html($sql, $menu_item_level) {

				$db = $this->db;
				$v_menu_guid = $this->menu_guid;
				$db_menu_full = '';

				if (count($_SESSION['groups']) == 0) {
					$_SESSION['groups'][0]['groupid'] = 'public';
				}

				if (strlen($sql) == 0) { //default sql for base of the menu
					$sql = "select * from v_menu_items ";
					$sql .= "where menu_guid = '$v_menu_guid' ";
					$sql .= "and (menu_item_parent_guid = '' or menu_item_parent_guid is null) ";
					$sql .= "and menu_item_guid in ";
					$sql .= "(select menu_item_guid from v_menu_item_groups where menu_guid = '$v_menu_guid' ";
					$sql .= "and ( ";
					if (count($_SESSION['groups']) == 0) {
						$sql .= "group_id = 'public' ";
					}
					else {
						$x = 0;
						foreach($_SESSION['groups'] as $row) {
							if ($x == 0) {
								$sql .= "group_id = '".$row['groupid']."' ";
							}
							else {
								$sql .= "or group_id = '".$row['groupid']."' ";
							}
							$x++;
						}
					}
					$sql .= ") ";
					$sql .= "and menu_item_guid <> '' ";
					$sql .= ") ";
					$sql .= "order by menu_item_order asc ";
				}
				$prep_statement = $db->prepare(check_sql($sql));
				$prep_statement->execute();
				$result = $prep_statement->fetchAll();

				foreach($result as $field) {
					$menu_item_id = $field['menu_item_id'];
					$menu_item_title = $field['menu_item_title'];
					$menu_item_str = $field['menu_item_str'];
					$menu_item_category = $field['menu_item_category'];
					$menu_item_desc = $field['menu_item_desc'];
					$menu_item_guid = $field['menu_item_guid'];
					$menu_item_parent_guid = $field['menu_item_parent_guid'];
					$menu_item_order = $field['menu_item_order'];
					$menu_item_language = $field['menu_item_language'];

					$menu_tags = '';
					switch ($menu_item_category) {
						case "internal":
							$menu_tags = "href='".PROJECT_PATH."$menu_item_str'";
							break;
						case "external":
							if (substr($menu_item_str, 0,1) == "/") {
								$menu_item_str = PROJECT_PATH . $menu_item_str;
							}
							$menu_tags = "href='$menu_item_str' target='_blank'";
							break;
						case "email":
							$menu_tags = "href='mailto:$menu_item_str'";
							break;
					}

					if ($menu_item_level == "main") {
						$db_menu  = "<ul class='menu_main'>\n";
						$db_menu .= "<li>\n";
						if (strlen($_SESSION["username"]) == 0) {
							$db_menu .= "<a $menu_tags style='padding: 0px 0px; border-style: none; background: none;'><h2 align='center' style=''>$menu_item_title</h2></a>\n";
						}
						else {
							if ($menu_item_str == "/login.php" || $menu_item_str == "/users/signup.php") {
								//hide login and sign-up when the user is logged in
							}
							else {
								$db_menu .= "<a $menu_tags style='padding: 0px 0px; border-style: none; background: none;'><h2 align='center' style=''>$menu_item_title</h2></a>\n";
							}
						}
					}

					$menu_item_level = 0;
					if (strlen($menu_item_guid) > 0) {
						$db_menu .= $this->build_child_html($menu_item_level, $menu_item_guid);
					}

					if ($menu_item_level == "main") {
						$db_menu .= "</li>\n";
						$db_menu .= "</ul>\n\n";
					}

					$db_menu_full .= $db_menu;

				} //end for each

				unset($menu_item_title);
				unset($menu_item_strv);
				unset($menu_item_category);
				unset($menu_item_guid);
				unset($menu_item_parent_guid);
				unset($prep_statement, $sql, $result);

				return $db_menu_full;
			}

		//create the sub menus
			function build_child_html($menu_item_level, $menu_item_guid) {

				$db = $this->db;
				$v_menu_guid = $this->menu_guid;
				$menu_item_level = $menu_item_level+1;

				if (count($_SESSION['groups']) == 0) {
					$_SESSION['groups'][0]['groupid'] = 'public';
				}

				$sql = "select * from v_menu_items ";
				$sql .= "where menu_guid = '$v_menu_guid' ";
				$sql .= "and menu_item_parent_guid = '$menu_item_guid' ";
				$sql .= "and menu_item_guid in ";
				$sql .= "(select menu_item_guid from v_menu_item_groups where menu_guid = '$v_menu_guid' ";
				$sql .= "and ( ";
				if (count($_SESSION['groups']) == 0) {
					$sql .= "group_id = 'public' ";
				}
				else {
					$x = 0;
					foreach($_SESSION['groups'] as $row) {
						if ($x == 0) {
							$sql .= "group_id = '".$row['groupid']."' ";
						}
						else {
							$sql .= "or group_id = '".$row['groupid']."' ";
						}
						$x++;
					}
				}
				$sql .= ") ";
				$sql .= ") ";
				$sql .= "order by menu_item_order, menu_item_title asc ";
				$prep_statement_2 = $db->prepare($sql);
				$prep_statement_2->execute();
				$result2 = $prep_statement_2->fetchAll();
				if (count($result2) > 0) {
					//child menu found
					$db_menu_sub .= "<ul class='menu_sub'>\n";

					foreach($result2 as $row) {
						$menu_item_id = $row['menu_item_id'];
						$menu_item_title = $row['menu_item_title'];
						$menu_item_str = $row['menu_item_str'];
						$menu_item_category = $row['menu_item_category'];
						$menu_item_guid = $row['menu_item_guid'];
						$menu_item_parent_guid = $row['menu_item_parent_guid'];

						$menuatags = '';
						switch ($menu_item_category) {
							case "internal":
								$menu_tags = "href='".PROJECT_PATH."$menu_item_str'";
								break;
							case "external":
								if (substr($menu_item_str, 0,1) == "/") {
									$menu_item_str = PROJECT_PATH . $menu_item_str;
								}
								$menu_tags = "href='$menu_item_str' target='_blank'";
								break;
							case "email":
								$menu_tags = "href='mailto:$menu_item_str'";
								break;
						}

						$db_menu_sub .= "<li>";

						//get sub menu for children
							if (strlen($menu_item_guid) > 0) {
								$str_child_menu = $this->build_child_html($menu_item_level, $menu_item_guid);
							}

						if (strlen($str_child_menu) > 1) {
							$db_menu_sub .= "<a $menu_tags>$menu_item_title</a>";
							$db_menu_sub .= $str_child_menu;
							unset($str_child_menu);
						}
						else {
							$db_menu_sub .= "<a $menu_tags>$menu_item_title</a>";
						}
						$db_menu_sub .= "</li>\n";
					}
					unset($sql, $result2);
					$db_menu_sub .="</ul>\n";
					return $db_menu_sub;
				}
				unset($prep_statement_2, $sql);
			}
	} //class

?>