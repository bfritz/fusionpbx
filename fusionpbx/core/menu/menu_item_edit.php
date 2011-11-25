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
include "root.php";
require_once "includes/config.php";
require_once "includes/checkauth.php";
if (permission_exists('menu_add') || permission_exists('menu_edit') || permission_exists('menu_delete')) {
	//access granted
}
else {
	echo "access denied";
	return;
}

//include the header
	require_once "includes/header.php";

//get the menu id and guid
	$id = check_str($_REQUEST["id"]);
	$menu_id = check_str($_REQUEST["menu_id"]);
	$menu_guid = check_str($_REQUEST["menu_guid"]);
	$menu_item_guid = check_str($_REQUEST['menu_item_guid']);
	$group_id = check_str($_REQUEST['group_id']);

//delete the group from the user
	if ($_REQUEST["a"] == "delete" && permission_exists("menu_delete")) {
		//delete the group from the users
			$sql = "delete from v_menu_item_groups  ";
			$sql .= "where menu_guid = '$menu_guid' ";
			$sql .= "and group_id = '".$group_id."' ";
			$sql .= "and menu_group_id = '".$id."' ";
			$db->exec(check_sql($sql));
	}

//add a group to the menu
	if (strlen($group_id) > 0 && permission_exists('menu_add')) {
		//add the group to the menu
			if (strlen($menu_item_guid) > 0 && strlen($group_id) > 0) {
				$sqlinsert = "insert into v_menu_item_groups ";
				$sqlinsert .= "(";
				$sqlinsert .= "menu_guid, ";
				$sqlinsert .= "menu_item_guid, ";
				$sqlinsert .= "group_id ";
				$sqlinsert .= ")";
				$sqlinsert .= "values ";
				$sqlinsert .= "(";
				$sqlinsert .= "'$menu_guid', ";
				$sqlinsert .= "'".$menu_item_guid."', ";
				$sqlinsert .= "'".$group_id."' ";
				$sqlinsert .= ")";
				$db->exec($sqlinsert);
			}
	}

//action add or update
	if (isset($_REQUEST["menu_item_id"])) {
		$action = "update";
		$menu_item_id = check_str($_REQUEST["menu_item_id"]);
	}
	else {
		$action = "add";
	}

//clear the menu session so it will rebuild with the update
	$_SESSION["menu"] = "";

//get the HTTP POST variables and set them as PHP variables
	if (count($_POST)>0) {
		$menu_id = check_str($_POST["menu_id"]);
		$menu_item_id = check_str($_POST["menu_item_id"]);
		$menu_item_title = check_str($_POST["menu_item_title"]);
		$menu_item_str = check_str($_POST["menu_item_str"]);
		$menu_item_category = check_str($_POST["menu_item_category"]);
		$menu_item_desc = check_str($_POST["menu_item_desc"]);
		$menu_item_protected = check_str($_POST["menu_item_protected"]);
		//$menu_item_guid = check_str($_POST["menu_item_guid"]);
		$menu_item_parent_guid = check_str($_POST["menu_item_parent_guid"]);
		$menu_item_order = check_str($_POST["menu_item_order"]);
	}

//when a HTTP POST is available then process it
	if (count($_POST)>0 && strlen($_POST["persistformvar"]) == 0) {

		if ($action == "update") {
			$menu_item_id = check_str($_POST["menu_item_id"]);
		}

		//check for all required data
			$msg = '';
			if (strlen($menu_item_title) == 0) { $msg .= "Please provide: title<br>\n"; }
			if (strlen($menu_item_category) == 0) { $msg .= "Please provide: category<br>\n"; }
			//if (strlen($menu_item_str) == 0) { $msg .= "Please provide: menu_item_str<br>\n"; }
			if (strlen($msg) > 0 && strlen($_POST["persistformvar"]) == 0) {
				require_once "includes/header.php";
				require_once "includes/persistformvar.php";
				echo "<div align='center'>\n";
				echo "<table><tr><td>\n";
				echo $msg."<br />";
				echo "</td></tr></table>\n";
				persistformvar($_POST);
				echo "</div>\n";
				require_once "includes/footer.php";
				return;
			}

		//add or update the database
		if ($_POST["persistformvar"] != "true") {
			if ($action == "add" && permission_exists('menu_add')) {
				$sql = "SELECT menu_item_order FROM v_menu_items ";
				$sql .= "where menu_guid = '$menu_guid' ";
				$sql .= "and menu_item_parent_guid  = '$menu_item_parent_guid' ";
				$sql .= "order by menu_item_order desc ";
				$sql .= "limit 1 ";
				$prepstatement = $db->prepare(check_sql($sql));
				$prepstatement->execute();
				$result = $prepstatement->fetchAll();
				foreach ($result as &$row) {
					$highestmenu_item_order = $row[menu_item_order];
				}
				unset($prepstatement);

				$sql = "insert into v_menu_items ";
				$sql .= "(";
				$sql .= "menu_guid, ";
				$sql .= "menu_item_title, ";
				$sql .= "menu_item_str, ";
				$sql .= "menu_item_category, ";
				$sql .= "menu_item_desc, ";
				$sql .= "menu_item_protected, ";
				$sql .= "menu_item_guid, ";
				$sql .= "menu_item_parent_guid, ";
				$sql .= "menu_item_order, ";
				$sql .= "menu_item_add_user, ";
				$sql .= "menu_item_add_date ";
				$sql .= ")";
				$sql .= "values ";
				$sql .= "(";
				$sql .= "'$menu_guid', ";
				$sql .= "'$menu_item_title', ";
				$sql .= "'$menu_item_str', ";
				$sql .= "'$menu_item_category', ";
				$sql .= "'$menu_item_desc', ";
				$sql .= "'$menu_item_protected', ";
				$sql .= "'".guid()."', ";
				$sql .= "'$menu_item_parent_guid', ";
				$sql .= "'".($highestmenu_item_order+1)."', ";
				$sql .= "'".$_SESSION["username"]."', ";
				$sql .= "now() ";
				$sql .= ")";
				$db->exec(check_sql($sql));
				unset($sql);
//working
//http://voip.fusionpbx.com/core/menu/menu_item_edit.php?menu_id=1&menu_item_id=4&menu_guid=B4750C3F-2A86-B00D-B7D0-345C14ECA286


				require_once "includes/header.php";
	echo "<meta http-equiv=\"refresh\" content=\"2;url=menu_item_edit.php?menu_id=$menu_id&menu_item_id=$menu_item_id&menu_guid=$menu_guid\">\n";
				echo "<div align='center'>\n";
				echo "Add Complete\n";
				echo "</div>\n";
				require_once "includes/footer.php";
				return;
			}

			if ($action == "update" && permission_exists('menu_edit')) {
				$sql  = "update v_menu_items set ";
				$sql .= "menu_item_title = '$menu_item_title', ";
				$sql .= "menu_item_str = '$menu_item_str', ";
				$sql .= "menu_item_category = '$menu_item_category', ";
				$sql .= "menu_item_desc = '$menu_item_desc', ";
				$sql .= "menu_item_protected = '$menu_item_protected', ";
				$sql .= "menu_item_parent_guid = '$menu_item_parent_guid', ";
				$sql .= "menu_item_order = '$menu_item_order', ";
				$sql .= "menu_item_mod_user = '".$_SESSION["username"]."', ";
				$sql .= "menu_item_mod_date = now() ";
				$sql .= "where menu_guid = '$menu_guid' ";
				$sql .= "and menu_item_id = '$menu_item_id' ";
				$count = $db->exec(check_sql($sql));

				require_once "includes/header.php";
				echo "<meta http-equiv=\"refresh\" content=\"2;url=menu_item_edit.php?menu_id=$menu_id&menu_item_id=$menu_item_id&menu_guid=$menu_guid\">\n";

				//echo "<meta http-equiv=\"refresh\" content=\"2;url=v_menu_item_edit.php?id=$menu_item_id&menu_id=$menu_id&menu_guid=$menu_guid\">\n";
				//echo "<meta http-equiv=\"refresh\" content=\"2;url=menu_item_edit.php?id=$id&menu_item_id=".$_REQUEST['menu_item_id']."&menu_item_parent_guid=".$_REQUEST['menu_item_parent_guid']."\">\n";
				echo "<div align='center'>\n";
				echo "Edit Complete\n";
				echo "</div>\n";
				require_once "includes/footer.php";
				return;
			}
		} //if ($_POST["persistformvar"] != "true")
	} //(count($_POST)>0 && strlen($_POST["persistformvar"]) == 0)

//pre-populate the form
	if (count($_GET)>0 && $_POST["persistformvar"] != "true") {
		$menu_item_id = $_GET["menu_item_id"];

		$sql = "";
		$sql .= "select * from v_menu_items ";
		$sql .= "where menu_guid = '$menu_guid' ";
		$sql .= "and menu_item_id = '$menu_item_id' ";
		$prepstatement = $db->prepare(check_sql($sql));
		$prepstatement->execute();
		$result = $prepstatement->fetchAll();
		foreach ($result as &$row) {
			$menu_item_guid = $row["menu_item_guid"];
			$menu_item_title = $row["menu_item_title"];
			$menu_item_str = $row["menu_item_str"];
			$menu_item_category = $row["menu_item_category"];
			$menu_item_desc = $row["menu_item_desc"];
			$menu_item_protected = $row["menu_item_protected"];
			$menu_item_parent_guid = $row["menu_item_parent_guid"];
			$menu_item_order = $row["menu_item_order"];
			$menu_item_add_user = $row["menu_item_add_user"];
			$menu_item_add_date = $row["menu_item_add_date"];
			//$menu_item_del_user = $row["menu_item_del_user"];
			//$menu_item_del_date = $row["menu_item_del_date"];
			$menu_item_mod_user = $row["menu_item_mod_user"];
			$menu_item_mod_date = $row["menu_item_mod_date"];
			break; //limit to 1 row
		}
	}

//show the content
	require_once "includes/header.php";
	echo "<div align='center'>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='2'>\n";
	echo "<tr class='border'>\n";
	echo "	<td align=\"left\">\n";
	echo "		<br>";

	echo "<form method='post' action=''>";
	echo "<table width='100%' cellpadding='6' cellspacing='0'>";

	echo "<tr>\n";
	echo "<td width='30%' align='left' valign='top' nowrap><b>Menu Item Edit</b></td>\n";
	echo "<td width='70%' align='right' valign='top'><input type='button' class='btn' name='' alt='back' onclick=\"window.history.back();\" value='Back'><br /><br /></td>\n";
	echo "</tr>\n";

	echo "	<tr>";
	echo "		<td class='vncellreq'>Title:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='menu_item_title' value='$menu_item_title'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncellreq'>Link:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='menu_item_str' value='$menu_item_str'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncellreq'>Category:</td>";
	echo "		<td class='vtable'>";
	echo "            <select name=\"menu_item_category\" class='formfld'>\n";
	echo "            <option value=\"\"></option>\n";
	if ($menu_item_category == "internal") { echo "<option value=\"internal\" selected>internal</option>\n"; } else { echo "<option value=\"internal\">internal</option>\n"; }
	if ($menu_item_category == "external") { echo "<option value=\"external\" selected>external</option>\n"; } else { echo "<option value=\"external\">external</option>\n"; }
	if ($menu_item_category == "email") { echo "<option value=\"email\" selected>email</option>\n"; } else { echo "<option value=\"email\">email</option>\n"; }
	echo "            </select>";
	echo "        </td>";
	echo "	</tr>";

	echo "	<tr>";
	echo "		<td class='vncell'>Parent Menu:</td>";
	echo "		<td class='vtable'>";
	$sql = "SELECT * FROM v_menu_items ";
	$sql .= "where menu_guid = '$menu_guid' ";
	$sql .= "order by menu_item_title asc ";
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	echo "<select name=\"menu_item_parent_guid\" class='formfld'>\n";
	echo "<option value=\"\"></option>\n";
	$result = $prepstatement->fetchAll();
	foreach($result as $field) {
			if ($menu_item_parent_guid == $field['menu_item_guid']) {
				echo "<option value='".$field['menu_item_guid']."' selected>".$field['menu_item_title']."</option>\n";
			}
			else {
				echo "<option value='".$field['menu_item_guid']."'>".$field['menu_item_title']."</option>\n";
			}
	}
	echo "</select>";
	unset($sql, $result);
	echo "        </td>";
	echo "	</tr>";

	echo "	<tr>";
	echo "		<td class='vncell' valign='top'>Groups:</td>";
	echo "		<td class='vtable'>";

	echo "<table width='52%'>\n";
	$sql = "SELECT * FROM v_menu_item_groups ";
	$sql .= "where menu_guid=:menu_guid ";
	$sql .= "and menu_item_guid=:menu_item_guid ";
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->bindParam(':menu_guid', $menu_guid);
	$prepstatement->bindParam(':menu_item_guid', $menu_item_guid);
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	$resultcount = count($result);
	foreach($result as $field) {
		if (strlen($field['group_id']) > 0) {
			echo "<tr>\n";
			echo "	<td class='vtable'>".$field['group_id']."</td>\n";
			echo "	<td>\n";
			if (permission_exists('group_member_delete') || ifgroup("superadmin")) {
				echo "		<a href='menu_item_edit.php?id=".$field['menu_group_id']."&menu_guid=".$field['menu_guid']."&group_id=".$field['group_id']."&menu_item_id=".$menu_item_id."&menu_item_parent_guid=".$menu_item_parent_guid."&a=delete' alt='delete' onclick=\"return confirm('Do you really want to delete this?')\">$v_link_label_delete</a>\n";
			}
			echo "	</td>\n";
			echo "</tr>\n";
		}
	}
	echo "</table>\n";

	echo "<br />\n";
	$sql = "SELECT * FROM v_groups ";
	$sql .= "where v_id = '".$v_id."' ";
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	echo "<select name=\"group_id\" class='frm'>\n";
	echo "<option value=\"\"></option>\n";
	$result = $prepstatement->fetchAll();
	foreach($result as $field) {
		if ($field['groupid'] == "superadmin") {
			//only show the superadmin group to other users in the superadmin group
			if (ifgroup("superadmin")) {
				echo "<option value='".$field['groupid']."'>".$field['groupid']."</option>\n";
			}
		}
		else {
			echo "<option value='".$field['groupid']."'>".$field['groupid']."</option>\n";
		}
	}
	echo "</select>";
	echo "<input type=\"submit\" class='btn' value=\"Add\">\n";
	unset($sql, $result);
	echo "		</td>";
	echo "	</tr>";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "    Protected:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <select class='formfld' name='menu_item_protected'>\n";
	echo "    <option value=''></option>\n";
	if ($menu_item_protected == "true") { 
		echo "    <option value='true' selected='selected' >true</option>\n";
	}
	else {
		echo "    <option value='true'>true</option>\n";
	}
	if ($menu_item_protected == "false") { 
		echo "    <option value='false' selected='selected' >false</option>\n";
	}
	else {
		echo "    <option value='false'>false</option>\n";
	}
	echo "    </select><br />\n";
	echo "Protect this item in the menu so that is is not removed by 'Restore Default.'<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	if ($action == "update") {
		echo "	<tr>";
		echo "		<td class='vncell'>Menu Order:</td>";
		echo "		<td class='vtable'><input type='text' class='formfld' name='menu_item_order' value='$menu_item_order'></td>";
		echo "	</tr>";
		//echo "	<tr>";
		//echo "		<td class='vncell'>Added By:</td>";
		//echo "		<td class='vtable'>$menu_item_add_user &nbsp;</td>";
		//echo "	</tr>";
		//echo "	<tr>";
		//echo "		<td class='vncell'>Add Date:</td>";
		//echo "		<td class='vtable'>$menu_item_add_date &nbsp;</td>";
		//echo "	</tr>";
		//echo "	<tr>";
		//echo "		<td class='vncell'>menu_item_del_user:</td>";
		//echo "		<td><input type='text' name='menu_item_del_user' value='$menu_item_del_user'></td>";
		//echo "	</tr>";
		//echo "	<tr>";
		//echo "		<td class='vncell'>menu_item_del_date:</td>";
		//echo "		<td><input type='text' name='menu_item_del_date' value='$menu_item_del_date'></td>";
		//echo "	</tr>";
		//echo "	<tr>";
		//echo "		<td class='vncell'>Modified By:</td>";
		//echo "		<td class='vtable'>$menu_item_mod_user &nbsp;</td>";
		//echo "	</tr>";
		//echo "	<tr>";
		//echo "		<td class='vncell'>Modified Date:</td>";
		//echo "		<td class='vtable'>$menu_item_mod_date &nbsp;</td>";
		//echo "	</tr>";
	}

	echo "	<tr>";
	echo "		<td class='vncell'>Description:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='menu_item_desc' value='$menu_item_desc'></td>";
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
			echo "				<input type='hidden' name='menu_item_id' value='$menu_item_id'>";
		}
		echo "				<input type='hidden' name='menu_id' value='$menu_id'>";
		echo "				<input type='hidden' name='menu_item_guid' value='$menu_item_guid'>";
		echo "				<input type='submit' class='btn' name='submit' value='Save'>\n";
		echo "			</td>";
		echo "			</tr>";
		echo "			</table>";
		echo "		</td>";
		echo "	</tr>";
	}
	echo "</table>";
	echo "</form>";

	echo "	</td>";
	echo "	</tr>";
	echo "</table>";
	echo "</div>";

//include the footer
  require_once "includes/footer.php";
?>