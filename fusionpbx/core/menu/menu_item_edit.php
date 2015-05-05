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
	Portions created by the Initial Developer are Copyright (C) 2008-2015
	the Initial Developer. All Rights Reserved.

	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>
*/
include "root.php";
require_once "resources/require.php";
require_once "resources/check_auth.php";
if (permission_exists('menu_add') || permission_exists('menu_edit')) {
	//access granted
}
else {
	echo "access denied";
	return;
}

//add multi-lingual support
	$language = new text;
	$text = $language->get();

//get the menu_uuid
	$menu_uuid = check_str($_REQUEST["id"]);
	$menu_item_uuid = check_str($_REQUEST['menu_item_uuid']);
	$group_name = check_str($_REQUEST['group_name']);

//delete the group from the user
	if ($_REQUEST["a"] == "delete" && permission_exists("menu_delete")) {
		//delete the group from the users
			$sql = "delete from v_menu_item_groups  ";
			$sql .= "where menu_uuid = '".$menu_uuid."' ";
			$sql .= "and menu_item_uuid = '".$menu_item_uuid."' ";
			$sql .= "and group_name = '".$group_name."' ";
			$db->exec(check_sql($sql));
		//redirect the browser
			$_SESSION["message"] = $text['message-delete'];
			header("Location: menu_item_edit.php?id=".$menu_uuid."&menu_item_uuid=".$menu_item_uuid."&menu_uuid=".$menu_uuid);
			return;
	}

//action add or update
	if (isset($_REQUEST["menu_item_uuid"])) {
		if (strlen($_REQUEST["menu_item_uuid"]) > 0) {
			$action = "update";
			$menu_item_uuid = check_str($_REQUEST["menu_item_uuid"]);
		}
		else {
			$action = "add";
		}
	}
	else {
		$action = "add";
	}

//clear the menu session so it will rebuild with the update
	$_SESSION["menu"] = "";

//get the HTTP POST variables and set them as PHP variables
	if (count($_POST) > 0) {
		$menu_uuid = check_str($_POST["menu_uuid"]);
		$menu_item_uuid = check_str($_POST["menu_item_uuid"]);
		$menu_item_title = check_str($_POST["menu_item_title"]);
		$menu_item_link = check_str($_POST["menu_item_link"]);
		$menu_item_category = check_str($_POST["menu_item_category"]);
		$menu_item_description = check_str($_POST["menu_item_description"]);
		$menu_item_protected = check_str($_POST["menu_item_protected"]);
		//$menu_item_uuid = check_str($_POST["menu_item_uuid"]);
		$menu_item_parent_uuid = check_str($_POST["menu_item_parent_uuid"]);
		$menu_item_order = check_str($_POST["menu_item_order"]);
	}

//when a HTTP POST is available then process it
	if (count($_POST) > 0 && strlen($_POST["persistformvar"]) == 0) {

		if ($action == "update") {
			$menu_item_uuid = check_str($_POST["menu_item_uuid"]);
		}

		//check for all required data
			$msg = '';
			if (strlen($menu_item_title) == 0) { $msg .= $text['message-required'].$text['label-title']."<br>\n"; }
			if (strlen($menu_item_category) == 0) { $msg .= $text['message-required'].$text['label-category']."<br>\n"; }
			//if (strlen($menu_item_link) == 0) { $msg .= $text['message-required'].$text['label-link']."<br>\n"; }
			if (strlen($msg) > 0 && strlen($_POST["persistformvar"]) == 0) {
				require_once "resources/header.php";
				require_once "resources/persist_form_var.php";
				echo "<div align='center'>\n";
				echo "<table><tr><td>\n";
				echo $msg."<br />";
				echo "</td></tr></table>\n";
				persistformvar($_POST);
				echo "</div>\n";
				require_once "resources/footer.php";
				return;
			}

		//add or update the database
		if ($_POST["persistformvar"] != "true") {
			//get the language from the menu
				$sql = "SELECT menu_language FROM v_menus ";
				$sql .= "where menu_uuid = '$menu_uuid' ";
				$prep_statement = $db->prepare(check_sql($sql));
				$prep_statement->execute();
				$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
				foreach ($result as &$row) {
					$menu_language = $row['menu_language'];
				}

			//get the highest menu item order
				if (strlen($menu_item_parent_uuid) == 0) {
					$sql = "SELECT menu_item_order FROM v_menu_items ";
					$sql .= "where menu_uuid = '$menu_uuid' ";
					$sql .= "and menu_item_parent_uuid is null ";
					$sql .= "order by menu_item_order desc ";
					$sql .= "limit 1 ";
					$prep_statement = $db->prepare(check_sql($sql));
					$prep_statement->execute();
					$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
					foreach ($result as &$row) {
						$highest_menu_item_order = $row['menu_item_order'];
					}
					unset($prep_statement);
				}

			//add a menu item
				if ($action == "add" && permission_exists('menu_add')) {
					$menu_item_uuid = uuid();
					$sql = "insert into v_menu_items ";
					$sql .= "(";
					$sql .= "menu_uuid, ";
					$sql .= "menu_item_title, ";
					$sql .= "menu_item_link, ";
					$sql .= "menu_item_category, ";
					$sql .= "menu_item_description, ";
					$sql .= "menu_item_protected, ";
					$sql .= "menu_item_uuid, ";
					$sql .= "menu_item_parent_uuid, ";
					if (strlen($menu_item_parent_uuid) == 0) {
						$sql .= "menu_item_order, ";
					}
					$sql .= "menu_item_add_user, ";
					$sql .= "menu_item_add_date ";
					$sql .= ")";
					$sql .= "values ";
					$sql .= "(";
					$sql .= "'$menu_uuid', ";
					$sql .= "'$menu_item_title', ";
					$sql .= "'$menu_item_link', ";
					$sql .= "'$menu_item_category', ";
					$sql .= "'$menu_item_description', ";
					$sql .= "'$menu_item_protected', ";
					$sql .= "'".$menu_item_uuid."', ";
					if (strlen($menu_item_parent_uuid) == 0) {
						$sql .= "null, ";
						$sql .= "'".($highest_menu_item_order+1)."', ";
					}
					else {
						$sql .= "'$menu_item_parent_uuid', ";
					}
					$sql .= "'".$_SESSION["username"]."', ";
					$sql .= "now() ";
					$sql .= ")";
					$db->exec(check_sql($sql));
					unset($sql);
				}

			//update the menu item
				if ($action == "update" && permission_exists('menu_edit')) {
					$sql  = "update v_menu_items set ";
					$sql .= "menu_item_title = '$menu_item_title', ";
					$sql .= "menu_item_link = '$menu_item_link', ";
					$sql .= "menu_item_category = '$menu_item_category', ";
					$sql .= "menu_item_description = '$menu_item_description', ";
					$sql .= "menu_item_protected = '$menu_item_protected', ";
					if (strlen($menu_item_parent_uuid) == 0) {
						$sql .= "menu_item_parent_uuid = null, ";
						if (strlen($menu_item_order) > 0) {
							$sql .= "menu_item_order = '$menu_item_order', ";
						}
						else {
							$sql .= "menu_item_order = '".($highest_menu_item_order+1)."', ";
						}
					}
					else {
						$sql .= "menu_item_parent_uuid = '$menu_item_parent_uuid', ";
					}
					$sql .= "menu_item_mod_user = '".$_SESSION["username"]."', ";
					$sql .= "menu_item_mod_date = now() ";
					$sql .= "where menu_uuid = '$menu_uuid' ";
					$sql .= "and menu_item_uuid = '$menu_item_uuid' ";
					$count = $db->exec(check_sql($sql));
				}

			//add a group to the menu
				if ($_REQUEST["a"] != "delete" && strlen($group_name) > 0 && permission_exists('menu_add')) {
					//add the group to the menu
						if (strlen($menu_item_uuid) > 0) {
							$menu_item_group_uuid = uuid();
							$sql_insert = "insert into v_menu_item_groups ";
							$sql_insert .= "(";
							$sql_insert .= "menu_item_group_uuid, ";
							$sql_insert .= "menu_uuid, ";
							$sql_insert .= "menu_item_uuid, ";
							$sql_insert .= "group_name ";
							$sql_insert .= ")";
							$sql_insert .= "values ";
							$sql_insert .= "(";
							$sql_insert .= "'".$menu_item_group_uuid."', ";
							$sql_insert .= "'".$menu_uuid."', ";
							$sql_insert .= "'".$menu_item_uuid."', ";
							$sql_insert .= "'".$group_name."' ";
							$sql_insert .= ")";
							$db->exec($sql_insert);
						}
				}

			//add title to menu languages
				if ($_REQUEST["a"] != "delete" && strlen($menu_item_title) > 0 && permission_exists('menu_add')) {
					$sql = "select count(*) as num_rows from v_menu_languages ";
					$sql .= "where menu_item_uuid = '".$menu_item_uuid."' ";
					$sql .= "and menu_language = '$menu_language' ";
					$prep_statement = $db->prepare($sql);
					$prep_statement->execute();
					$row = $prep_statement->fetch(PDO::FETCH_ASSOC);
					if ($row['num_rows'] == 0) {
						$sql_insert = "insert into v_menu_languages ";
						$sql_insert .= "(";
						$sql_insert .= "menu_language_uuid, ";
						$sql_insert .= "menu_uuid, ";
						$sql_insert .= "menu_item_uuid, ";
						$sql_insert .= "menu_language, ";
						$sql_insert .= "menu_item_title ";
						$sql_insert .= ")";
						$sql_insert .= "values ";
						$sql_insert .= "(";
						$sql_insert .= "'".uuid()."', ";
						$sql_insert .= "'".$menu_uuid."', ";
						$sql_insert .= "'".$menu_item_uuid."', ";
						$sql_insert .= "'".$menu_language."', ";
						$sql_insert .= "'".$menu_item_title."' ";
						$sql_insert .= ")";
						$db->exec($sql_insert);
					}
					else {
						$sql  = "update v_menu_languages set ";
						$sql .= "menu_item_title = '$menu_item_title' ";
						$sql .= "where menu_uuid = '$menu_uuid' ";
						$sql .= "and menu_item_uuid = '$menu_item_uuid' ";
						$sql .= "and menu_language = '$menu_language' ";
						$count = $db->exec(check_sql($sql));
					}
				}

			//redirect the user
				if ($action == "add") {
					$_SESSION["message"] = $text['message-add'];
				}
				if ($action == "update") {
					$_SESSION["message"] = $text['message-update'];
				}
				header("Location: menu_edit.php?id=".$menu_uuid);
				return;
		} //if ($_POST["persistformvar"] != "true")
	} //(count($_POST)>0 && strlen($_POST["persistformvar"]) == 0)

//pre-populate the form
	if (count($_GET)>0 && $_POST["persistformvar"] != "true") {
		$menu_item_uuid = $_GET["menu_item_uuid"];

		$sql = "select * from v_menu_items ";
		$sql .= "where menu_uuid = '$menu_uuid' ";
		$sql .= "and menu_item_uuid = '$menu_item_uuid' ";
		$prep_statement = $db->prepare(check_sql($sql));
		$prep_statement->execute();
		$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
		foreach ($result as &$row) {
			$menu_item_title = $row["menu_item_title"];
			$menu_item_link = $row["menu_item_link"];
			$menu_item_category = $row["menu_item_category"];
			$menu_item_description = $row["menu_item_description"];
			$menu_item_protected = $row["menu_item_protected"];
			$menu_item_parent_uuid = $row["menu_item_parent_uuid"];
			$menu_item_order = $row["menu_item_order"];
			$menu_item_add_user = $row["menu_item_add_user"];
			$menu_item_add_date = $row["menu_item_add_date"];
			//$menu_item_del_user = $row["menu_item_del_user"];
			//$menu_item_del_date = $row["menu_item_del_date"];
			$menu_item_mod_user = $row["menu_item_mod_user"];
			$menu_item_mod_date = $row["menu_item_mod_date"];
		}
	}

//include the header
	require_once "resources/header.php";
	if ($action == "update") {
		$document['title'] = $text['title-menu_item-edit'];
	}
	if ($action == "add") {
		$document['title'] = $text['title-menu_item-add'];
	}

	echo "<form method='post' action=''>";
	echo "<table width='100%' cellpadding='0' cellspacing='0'>";
	echo "<tr>\n";
	echo "<td width='30%' align='left' valign='top' nowrap><b>";
	if ($action == "update") {
		echo $text['header-menu_item-edit'];
	}
	if ($action == "add") {
		echo $text['header-menu_item-add'];
	}
	echo "</b></td>\n";
	echo "<td width='70%' align='right' valign='top'>";
	echo "	<input type='button' class='btn' name='' alt='".$text['button-back']."' onclick=\"window.location='menu_edit.php?id=".$menu_uuid."'\" value='".$text['button-back']."'>";
	echo "	<input type='submit' class='btn' name='submit' value='".$text['button-save']."'>\n";
	echo "	<br><br>";
	echo "</td>\n";
	echo "</tr>\n";

	echo "	<tr>";
	echo "		<td class='vncellreq'>".$text['label-title']."</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='menu_item_title' value='$menu_item_title'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncellreq'>".$text['label-link']."</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='menu_item_link' value='$menu_item_link'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncellreq'>".$text['label-category']."</td>";
	echo "		<td class='vtable'>";
	echo "            <select name=\"menu_item_category\" class='formfld'>\n";
	if ($menu_item_category == "internal") { echo "<option value=\"internal\" selected>".$text['option-internal']."</option>\n"; } else { echo "<option value=\"internal\">".$text['option-internal']."</option>\n"; }
	if ($menu_item_category == "external") { echo "<option value=\"external\" selected>".$text['option-external']."</option>\n"; } else { echo "<option value=\"external\">".$text['option-external']."</option>\n"; }
	if ($menu_item_category == "email") { echo "<option value=\"email\" selected>".$text['option-email']."</option>\n"; } else { echo "<option value=\"email\">".$text['option-email']."</option>\n"; }
	echo "            </select>";
	echo "        </td>";
	echo "	</tr>";

	echo "	<tr>";
	echo "		<td class='vncell'>".$text['label-parent_menu']."</td>";
	echo "		<td class='vtable'>";
	$sql = "SELECT * FROM v_menu_items ";
	$sql .= "where menu_uuid = '$menu_uuid' ";
	$sql .= "order by menu_item_title asc ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	echo "<select name=\"menu_item_parent_uuid\" class='formfld'>\n";
	echo "<option value=\"\"></option>\n";
	$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
	foreach($result as $field) {
			if ($menu_item_parent_uuid == $field['menu_item_uuid']) {
				echo "<option value='".$field['menu_item_uuid']."' selected>".$field['menu_item_title']."</option>\n";
			}
			else {
				echo "<option value='".$field['menu_item_uuid']."'>".$field['menu_item_title']."</option>\n";
			}
	}
	echo "</select>";
	unset($sql, $result);
	echo "		</td>";
	echo "	</tr>";

	echo "	<tr>";
	echo "		<td class='vncell' valign='top'>".$text['label-groups']."</td>";
	echo "		<td class='vtable'>";

	echo "<table width='52%'>\n";
	$sql = "SELECT * FROM v_menu_item_groups ";
	$sql .= "where menu_uuid=:menu_uuid ";
	$sql .= "and menu_item_uuid=:menu_item_uuid ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->bindParam(':menu_uuid', $menu_uuid);
	$prep_statement->bindParam(':menu_item_uuid', $menu_item_uuid);
	$prep_statement->execute();
	$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
	$result_count = count($result);
	foreach($result as $field) {
		if (strlen($field['group_name']) > 0) {
			echo "<tr>\n";
			echo "	<td class='vtable'>".$field['group_name']."</td>\n";
			echo "	<td>\n";
			if (permission_exists('group_member_delete') || if_group("superadmin")) {
				echo "		<a href='menu_item_edit.php?id=".$field['menu_uuid']."&group_name=".$field['group_name']."&menu_item_uuid=".$menu_item_uuid."&menu_item_parent_uuid=".$menu_item_parent_uuid."&a=delete' alt='".$text['button-delete']."' onclick=\"return confirm('".$text['confirm-delete']."')\">$v_link_label_delete</a>\n";
			}
			echo "	</td>\n";
			echo "</tr>\n";
			$assigned_groups[] = $field['group_name'];
		}
	}
	echo "</table>\n";

	echo "<br />\n";
	$sql = "SELECT * FROM v_groups ";
	$sql .= "where (domain_uuid = '".$domain_uuid."' or domain_uuid is null) ";
	$sql .= "order by group_name asc ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	echo "<select name=\"group_name\" class='formfld' style='width: auto; margin-right: 3px;'>\n";
	echo "<option value=\"\"></option>\n";
	$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
	foreach($result as $field) {
		if (!in_array($field['group_name'], $assigned_groups)) {
			if ($field['group_name'] == "superadmin" && if_group("superadmin")) {
				echo "<option value='".$field['group_name']."'>".$field['group_name']."</option>\n"; //only show the superadmin group to other users in the superadmin group
			}
			else {
				echo "<option value='".$field['group_name']."'>".$field['group_name']."</option>\n";
			}
		}
	}
	echo "</select>";
	echo "<input type=\"submit\" class='btn' value=\"".$text['button-add']."\">\n";
	unset($sql, $result);
	echo "		</td>";
	echo "	</tr>";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "    ".$text['label-protected']."\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <select class='formfld' name='menu_item_protected'>\n";
	if ($menu_item_protected == "false") {
		echo "    <option value='false' selected='selected' >".$text['label-false']."</option>\n";
	}
	else {
		echo "    <option value='false'>".$text['label-false']."</option>\n";
	}
	if ($menu_item_protected == "true") {
		echo "    <option value='true' selected='selected' >".$text['label-true']."</option>\n";
	}
	else {
		echo "    <option value='true'>".$text['label-true']."</option>\n";
	}
	echo "    </select><br />\n";
	echo $text['description-protected']."<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	if ($action == "update") {
		if ($menu_item_parent_uuid == "") {
			echo "	<tr>";
			echo "		<td class='vncell'>".$text['label-menu_order']."</td>";
			echo "		<td class='vtable'><input type='text' class='formfld' name='menu_item_order' value='$menu_item_order'></td>";
			echo "	</tr>";
		}
	}

	echo "	<tr>";
	echo "		<td class='vncell'>".$text['label-description']."</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='menu_item_description' value='$menu_item_description'></td>";
	echo "	</tr>";

	if (permission_exists('menu_add') || permission_exists('menu_edit')) {
		echo "	<tr>\n";
		echo "		<td colspan='2' align='right'>\n";
		echo "			<table width='100%'>";
		echo "			<tr>";
		echo "			<td align='left'>";
		echo "			</td>\n";
		echo "			<td align='right'>";
		if ($action == "update") {
			echo "			<input type='hidden' name='menu_item_uuid' value='$menu_item_uuid'>";
		}
		echo "				<input type='hidden' name='menu_uuid' value='$menu_uuid'>";
		echo "				<input type='hidden' name='menu_item_uuid' value='$menu_item_uuid'>";
		echo "				<br>";
		echo "				<input type='submit' class='btn' name='submit' value='".$text['button-save']."'>\n";
		echo "			</td>";
		echo "			</tr>";
		echo "			</table>";
		echo "		</td>";
		echo "	</tr>";
	}
	echo "</table>";
	echo "<br><br>";
	echo "</form>";

//include the footer
  require_once "resources/footer.php";
?>