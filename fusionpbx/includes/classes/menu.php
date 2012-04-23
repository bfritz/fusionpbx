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
		public $menu_uuid;

		//delete items in the menu that are not protected
			function delete() {
				//set the variable
					$db = $this->db;
				//remove the old menu
					$sql  = "delete from v_menu_items ";
					$sql .= "where menu_uuid = '".$this->menu_uuid."' ";
					$sql .= "and (menu_item_protected <> 'true' ";
					$sql .= "or menu_item_protected is null); ";
					$db->exec(check_sql($sql));
			}

		//restore the menu
			function restore() {
				//set the variables
					$db = $this->db;

				//get the $apps array from the installed apps from the core and mod directories
					$config_list = glob($_SERVER["DOCUMENT_ROOT"] . PROJECT_PATH . "/*/*/app_config.php");
					$x=0;
					foreach ($config_list as &$config_path) {
						include($config_path);
						$x++;
					}

				//use the app array to restore the default menu
					//$db->beginTransaction();
					foreach ($apps as $row) {
						foreach ($row['menu'] as $menu) {
							//set the variables
								$menu_item_title = $menu['title']['en'];
								$menu_item_language = 'en';
								$menu_item_uuid = $menu['uuid'];
								$menu_item_parent_uuid = $menu['parent_uuid'];
								$menu_item_category = $menu['category'];
								$menu_item_path = $menu['path'];
								$menu_item_order = $menu['order'];
								$menu_item_description = $menu['desc'];

							//if the item uuid is not currently in the db then add it
								$sql = "select * from v_menu_items ";
								$sql .= "where menu_uuid = '".$this->menu_uuid."' ";
								$sql .= "and menu_item_uuid = '$menu_item_uuid' ";
								$prep_statement = $db->prepare(check_sql($sql));
								if ($prep_statement) {
									$prep_statement->execute();
									$result = $prep_statement->fetchAll(PDO::FETCH_ASSOC);
									if (count($result) == 0) {
										//insert the default menu into the database
											$sql = "insert into v_menu_items ";
											$sql .= "(";
											$sql .= "menu_item_uuid, ";
											$sql .= "menu_uuid, ";
											//$sql .= "menu_item_language, ";
											$sql .= "menu_item_title, ";
											$sql .= "menu_item_link, ";
											$sql .= "menu_item_category, ";
											if (strlen($menu_item_order) > 0) {
												$sql .= "menu_item_order, ";
											}
											if (strlen($menu_item_uuid) > 0) {
												$sql .= "menu_item_uuid, ";
											}
											if (strlen($menu_item_parent_uuid) > 0) {
												$sql .= "menu_item_parent_uuid, ";
											}
											$sql .= "menu_item_description ";
											$sql .= ") ";
											$sql .= "values ";
											$sql .= "(";
											$sql .= "'".$menu_item_uuid."', ";
											$sql .= "'".$this->menu_uuid."', ";
											//$sql .= "'$menu_item_language', ";
											$sql .= "'$menu_item_title', ";
											$sql .= "'$menu_item_path', ";
											$sql .= "'$menu_item_category', ";
											if (strlen($menu_item_order) > 0) {
												$sql .= "'$menu_item_order', ";
											}
											if (strlen($menu_item_uuid) > 0) {
												$sql .= "'$menu_item_uuid', ";
											}
											if (strlen($menu_item_parent_uuid) > 0) {
												$sql .= "'$menu_item_parent_uuid', ";
											}
											$sql .= "'$menu_item_description' ";
											$sql .= ")";
											if ($menu_item_uuid == $menu_item_parent_uuid) {
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

				//if there are no groups listed in v_menu_item_groups under menu_uuid then add the default groups
					$sql = "select count(*) as count from v_menu_item_groups ";
					$sql .= "where menu_uuid = '".$this->menu_uuid."' ";
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
										$sql .= "menu_uuid, ";
										$sql .= "menu_item_uuid, ";
										$sql .= "group_name ";
										$sql .= ")";
										$sql .= "values ";
										$sql .= "(";
										$sql .= "'".$this->menu_uuid."', ";
										$sql .= "'".$sub_row['uuid']."', ";
										$sql .= "'".$group."' ";
										$sql .= ")";
										$db->exec($sql);
										unset($sql);
									}
								}
							}
					}

				//save the changes to the database
					//$db->commit();
			} //end function

		//create the menu
			function build_html($sql, $menu_item_level) {

				$db = $this->db;
				$db_menu_full = '';

				if (!isset($_SESSION['groups'])) {
					$_SESSION['groups'][0]['group_name'] = 'public';
				}

				if (strlen($sql) == 0) { //default sql for base of the menu
					$sql = "select * from v_menu_items ";
					$sql .= "where menu_uuid = '".$this->menu_uuid."' ";
					$sql .= "and menu_item_parent_uuid is null ";
					$sql .= "and menu_item_uuid in ";
					$sql .= "(select menu_item_uuid from v_menu_item_groups where menu_uuid = '".$this->menu_uuid."' ";
					$sql .= "and ( ";
					if (!isset($_SESSION['groups'])) {
						$sql .= "group_name = 'public' ";
					}
					else {
						$x = 0;
						foreach($_SESSION['groups'] as $row) {
							if ($x == 0) {
								$sql .= "group_name = '".$row['group_name']."' ";
							}
							else {
								$sql .= "or group_name = '".$row['group_name']."' ";
							}
							$x++;
						}
					}
					$sql .= ") ";
					$sql .= "and menu_item_uuid is not null ";
					$sql .= ") ";
					$sql .= "order by menu_item_order asc ";
				}
				$prep_statement = $db->prepare(check_sql($sql));
				$prep_statement->execute();
				$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
				foreach($result as $field) {
					$menu_tags = '';
					switch ($field['menu_item_category']) {
						case "internal":
							$menu_tags = "href='".PROJECT_PATH.$field['menu_item_link']."'";
							break;
						case "external":
							if (substr($field['menu_item_link'], 0,1) == "/") {
								$field['menu_item_link'] = PROJECT_PATH . $field['menu_item_link'];
							}
							$menu_tags = "href='".$field['menu_item_link']."' target='_blank'";
							break;
						case "email":
							$menu_tags = "href='mailto:".$field['menu_item_link']."'";
							break;
					}

					if ($menu_item_level == "main") {
						$db_menu  = "<ul class='menu_main'>\n";
						$db_menu .= "<li>\n";
						if (!isset($_SESSION["username"])) {
							$_SESSION["username"] = '';
						}
						if (strlen($_SESSION["username"]) == 0) {
							$db_menu .= "<a $menu_tags style='padding: 0px 0px; border-style: none; background: none;'><h2 align='center' style=''>".$field['menu_item_title']."</h2></a>\n";
						}
						else {
							if ($field['menu_item_link'] == "/login.php" || $field['menu_item_link'] == "/users/signup.php") {
								//hide login and sign-up when the user is logged in
							}
							else {
								$db_menu .= "<a ".$menu_tags." style='padding: 0px 0px; border-style: none; background: none;'><h2 align='center' style=''>".$field['menu_item_title']."</h2></a>\n";
							}
						}
					}

					$menu_item_level = 0;
					if (strlen($field['menu_item_uuid']) > 0) {
						$db_menu .= $this->build_child_html($menu_item_level, $field['menu_item_uuid']);
					}

					if ($menu_item_level == "main") {
						$db_menu .= "</li>\n";
						$db_menu .= "</ul>\n\n";
					}

					$db_menu_full .= $db_menu;
				} //end for each

				unset($prep_statement, $sql, $result);
				return $db_menu_full;
			}

		//create the sub menus
			function build_child_html($menu_item_level, $menu_item_uuid) {

				$db = $this->db;
				$menu_item_level = $menu_item_level+1;

				if (count($_SESSION['groups']) == 0) {
					$_SESSION['groups'][0]['group_name'] = 'public';
				}

				$sql = "select * from v_menu_items ";
				$sql .= "where menu_uuid = '".$this->menu_uuid."' ";
				$sql .= "and menu_item_parent_uuid = '$menu_item_uuid' ";
				$sql .= "and menu_item_uuid in ";
				$sql .= "(select menu_item_uuid from v_menu_item_groups where menu_uuid = '".$this->menu_uuid."' ";
				$sql .= "and ( ";
				if (count($_SESSION['groups']) == 0) {
					$sql .= "group_name = 'public' ";
				}
				else {
					$x = 0;
					foreach($_SESSION['groups'] as $row) {
						if ($x == 0) {
							$sql .= "group_name = '".$row['group_name']."' ";
						}
						else {
							$sql .= "or group_name = '".$row['group_name']."' ";
						}
						$x++;
					}
				}
				$sql .= ") ";
				$sql .= ") ";
				$sql .= "order by menu_item_order, menu_item_title asc ";
				$prep_statement_2 = $db->prepare($sql);
				$prep_statement_2->execute();
				$result_2 = $prep_statement_2->fetchAll(PDO::FETCH_NAMED);
				if (count($result_2) > 0) {
					//child menu found
					$db_menu_sub = "<ul class='menu_sub'>\n";

					foreach($result_2 as $row) {
						$menu_item_link = $row['menu_item_link'];
						$menu_item_category = $row['menu_item_category'];
						$menu_item_uuid = $row['menu_item_uuid'];
						$menu_item_parent_uuid = $row['menu_item_parent_uuid'];

						switch ($menu_item_category) {
							case "internal":
								$menu_tags = "href='".PROJECT_PATH.$menu_item_link."'";
								break;
							case "external":
								if (substr($menu_item_link, 0,1) == "/") {
									$menu_item_link = PROJECT_PATH . $menu_item_link;
								}
								$menu_tags = "href='".$menu_item_link."' target='_blank'";
								break;
							case "email":
								$menu_tags = "href='mailto:".$menu_item_link."'";
								break;
						}

						$db_menu_sub .= "<li>";

						//get sub menu for children
							if (strlen($menu_item_uuid) > 0) {
								$str_child_menu = $this->build_child_html($menu_item_level, $menu_item_uuid);
							}

						if (strlen($str_child_menu) > 1) {
							$db_menu_sub .= "<a ".$menu_tags.">".$row['menu_item_title']."</a>";
							$db_menu_sub .= $str_child_menu;
							unset($str_child_menu);
						}
						else {
							$db_menu_sub .= "<a ".$menu_tags.">".$row['menu_item_title']."</a>";
						}
						$db_menu_sub .= "</li>\n";
					}
					unset($sql, $result_2);
					$db_menu_sub .="</ul>\n";
					return $db_menu_sub;
				}
				unset($prep_statement_2, $sql);
			}
	}

?>