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
include "root.php";
require_once "includes/config.php";
require_once "includes/checkauth.php";
if (permission_exists('conferences_add') || permission_exists('conferences_edit')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}
require_once "includes/paging.php";

$order_by = $_GET["order_by"];
$order = $_GET["order"];

//action add or update
	if (isset($_REQUEST["id"])) {
		$action = "update";
		$dialplan_uuid = check_str($_REQUEST["id"]);
	}
	else {
		$action = "add";
	}

//check if the user has been assigned this conference room
	if (permission_exists('conferences_add') && permission_exists('conferences_edit')) {
		//allow users that have been assigned conferences_add or conferences_edit to all conference rooms
	}
	else {
		//get the list of conference numbers the user is assigned to
			$sql = "select * from v_dialplan_details ";
			$sql .= "where domain_uuid = '$domain_uuid' ";
			$prep_statement = $db->prepare(check_sql($sql));
			$prep_statement->execute();
			$x = 0;
			$result = $prep_statement->fetchAll();
			foreach ($result as &$row) {
				$tmp_dialplan_uuid = $row["dialplan_uuid"];
				$field_type = $row["field_type"];
				if ($field_type == "conference") {
					$conference_array[$x]['dialplan_uuid'] = $tmp_dialplan_uuid;
					$x++;
				}
			}
			unset ($prep_statement);

		//get the list of assigned conference numbers for this user
			foreach ($conference_array as &$row) {
				$sql = "select * from v_dialplan_details ";
				$sql .= "where domain_uuid = '$domain_uuid' ";
				$sql .= "and dialplan_uuid = '".$row['dialplan_uuid']."' ";
				$sql .= "and field_data like 'conference_user_list%' and field_data like '%|".$_SESSION['username']."|%' ";
				$tmp_row = $db->query($sql)->fetch();
				if (strlen($tmp_row['dialplan_uuid']) > 0) {
					$conference_auth_array[$tmp_row['dialplan_uuid']] = $tmp_row['dialplan_uuid'];
				}
			}

		//check if the user has been assigned to this conference room
			if (strlen($conference_auth_array[$dialplan_uuid]) == 0) {
				echo "access denied";
				exit;
			}
	}

//show the header
	require_once "includes/header.php";

//http post to php variables
	if (count($_POST)>0) {
		$dialplan_name = check_str($_POST["dialplan_name"]);
		$dialplan_number = check_str($_POST["dialplan_number"]);
		$dialplan_order = check_str($_POST["dialplan_order"]);
		$pin_number = check_str($_POST["pin_number"]);

		//replace common characters that can cause problems
		$dialplan_name = str_replace(" ", "-", $dialplan_name);
		$dialplan_name = str_replace("'", "", $dialplan_name);

		//prepare the user list for the database
		$user_list = $_POST["user_list"];
		if (strlen($user_list) > 0) {
			$user_list_array = explode("\n", $user_list);
			if (count($user_list_array) == 0) {
				$user_list = '';
			}
			else {
				$user_list = '|';
				foreach($user_list_array as $user){
					if(strlen(trim($user)) > 0) {
						$user_list .= check_str(trim($user))."|";
					}
				}
			}
		}

		$profile = check_str($_POST["profile"]);
		$flags = check_str($_POST["flags"]);
		$dialplan_enabled = check_str($_POST["dialplan_enabled"]);
		$description = check_str($_POST["description"]);
		if (strlen($dialplan_enabled) == 0) { $dialplan_enabled = "true"; } //set default to dialplan_enabled
	}

//process the http post
	if (count($_POST)>0 && strlen($_POST["persistformvar"]) == 0) {
		//check for all required data
			if (strlen($dialplan_name) == 0) { $msg .= "Please provide: Conference Name<br>\n"; }
			if (strlen($dialplan_number) == 0) { $msg .= "Please provide: Extension Number<br>\n"; }
			//if (strlen($pin_number) == 0) { $msg .= "Please provide: PIN Number<br>\n"; }
			if (strlen($profile) == 0) { $msg .= "Please provide: profile<br>\n"; }
			//if (strlen($flags) == 0) { $msg .= "Please provide: Flags<br>\n"; }
			if (strlen($dialplan_enabled) == 0) { $msg .= "Please provide: Enabled True or False<br>\n"; }
			//if (strlen($description) == 0) { $msg .= "Please provide: Description<br>\n"; }
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

		//start the atomic transaction
			$count = $db->exec("BEGIN;"); //returns affected rows

		//prepare the fieldata so that it combines the conference name, profile, pin number and flags
			if (strlen($action) > 0) {
				$tmp_pin_number = ''; if (strlen($pin_number) > 0) { $tmp_pin_number = "+".$pin_number; }
				$tmp_flags = ''; if (strlen($flags) > 0) { $tmp_flags = "+flags{".$flags."}"; }
				$tmp_field_data = $dialplan_name.'-'.$v_domain."@".$profile.$tmp_pin_number.$tmp_flags;
			}

		if ($action == "add" && permission_exists('conferences_add')) {

			//add the main dialplan include entry
				$dialplan_uuid = uuid();
				$sql = "insert into v_dialplan ";
				$sql .= "(";
				$sql .= "domain_uuid, ";
				$sql .= "dialplan_uuid, ";
				$sql .= "dialplan_name, ";
				$sql .= "dialplan_order, ";
				$sql .= "dialplan_continue, ";
				$sql .= "dialplan_context, ";
				$sql .= "dialplan_enabled, ";
				$sql .= "dialplan_description ";
				$sql .= ") ";
				$sql .= "values ";
				$sql .= "(";
				$sql .= "'$domain_uuid', ";
				$sql .= "'$dialplan_uuid', ";
				$sql .= "'$dialplan_name', ";
				$sql .= "'$dialplan_order', ";
				$sql .= "'false', ";
				$sql .= "'default', ";
				$sql .= "'$dialplan_enabled', ";
				$sql .= "'$description' ";
				$sql .= ")";
				$db->exec(check_sql($sql));

			if (strlen($dialplan_uuid) > 0) {
				//add condition for the extension number
					$sql = "insert into v_dialplan_details ";
					$sql .= "(";
					$sql .= "domain_uuid, ";
					$sql .= "dialplan_uuid, ";
					$sql .= "dialplan_detail_uuid, ";
					$sql .= "tag, ";
					$sql .= "field_type, ";
					$sql .= "field_data, ";
					$sql .= "field_order ";
					$sql .= ") ";
					$sql .= "values ";
					$sql .= "(";
					$sql .= "'$domain_uuid', ";
					$sql .= "'$dialplan_uuid', ";
					$sql .= "'$dialplan_detail_uuid', ";
					$sql .= "'condition', ";
					$sql .= "'destination_number', ";
					$sql .= "'^".$dialplan_number."$', ";
					$sql .= "'1' ";
					$sql .= ")";
					$db->exec(check_sql($sql));
					unset($sql);

				//add action answer
					$sql = "insert into v_dialplan_details ";
					$sql .= "(";
					$sql .= "domain_uuid, ";
					$sql .= "dialplan_uuid, ";
					$sql .= "dialplan_detail_uuid, ";
					$sql .= "tag, ";
					$sql .= "field_type, ";
					$sql .= "field_data, ";
					$sql .= "field_order ";
					$sql .= ") ";
					$sql .= "values ";
					$sql .= "(";
					$sql .= "'$domain_uuid', ";
					$sql .= "'$dialplan_uuid', ";
					$sql .= "'$dialplan_detail_uuid', ";
					$sql .= "'action', ";
					$sql .= "'answer', ";
					$sql .= "'', ";
					$sql .= "'2' ";
					$sql .= ")";
					$db->exec(check_sql($sql));
					unset($sql);

				//add action set
					$sql = "insert into v_dialplan_details ";
					$sql .= "(";
					$sql .= "domain_uuid, ";
					$sql .= "dialplan_uuid, ";
					$sql .= "dialplan_detail_uuid, ";
					$sql .= "tag, ";
					$sql .= "field_type, ";
					$sql .= "field_data, ";
					$sql .= "field_order ";
					$sql .= ") ";
					$sql .= "values ";
					$sql .= "(";
					$sql .= "'$domain_uuid', ";
					$sql .= "'$dialplan_uuid', ";
					$sql .= "'$dialplan_detail_uuid', ";
					$sql .= "'action', ";
					$sql .= "'set', ";
					$sql .= "'conference_user_list=$user_list', ";
					$sql .= "'3' ";
					$sql .= ")";
					$db->exec(check_sql($sql));
					unset($sql);

				//add action conference
					$sql = "insert into v_dialplan_details ";
					$sql .= "(";
					$sql .= "domain_uuid, ";
					$sql .= "dialplan_uuid, ";
					$sql .= "dialplan_detail_uuid, ";
					$sql .= "tag, ";
					$sql .= "field_type, ";
					$sql .= "field_data, ";
					$sql .= "field_order ";
					$sql .= ") ";
					$sql .= "values ";
					$sql .= "(";
					$sql .= "'$domain_uuid', ";
					$sql .= "'$dialplan_uuid', ";
					$sql .= "'$dialplan_detail_uuid', ";
					$sql .= "'action', ";
					$sql .= "'conference', ";
					$sql .= "'".$tmp_field_data."', ";
					$sql .= "'4' ";
					$sql .= ")";
					$db->exec(check_sql($sql));
					unset($sql);
					unset($field_data);
			} //end if (strlen($dialplan_uuid) > 0)
		} //if ($action == "add")

		//update the data
			if ($action == "update" && permission_exists('conferences_edit')) {
				$sql = "update v_dialplan set ";
				$sql .= "dialplan_name = '$dialplan_name', ";
				$sql .= "dialplan_order = '$dialplan_order', ";
				//$sql .= "dialplan_continue = '$dialplan_continue', ";
				$sql .= "dialplan_context = '$dialplan_context', ";
				$sql .= "dialplan_enabled = '$dialplan_enabled', ";
				$sql .= "dialplan_description = '$description' ";
				$sql .= "where domain_uuid = '$domain_uuid' ";
				$sql .= "and dialplan_uuid = '$dialplan_uuid'";
				$db->exec(check_sql($sql));
				unset($sql);

				$sql = "";
				$sql .= "select * from v_dialplan_details ";
				$sql .= "where domain_uuid = '$domain_uuid' ";
				$sql .= "and dialplan_uuid = '$dialplan_uuid' ";
				$prep_statement = $db->prepare(check_sql($sql));
				$prep_statement->execute();
				$result = $prep_statement->fetchAll();
				unset($prep_statement);
				foreach ($result as $row) {
					if ($row['field_type'] == "destination_number") {
						$sql = "update v_dialplan_details set ";
						//$sql .= "tag = '$tag', ";
						//$sql .= "field_type = '$field_type', ";
						$sql .= "field_data = '^".$dialplan_number."$', ";
						$sql .= "field_order = '".$row['field_order']."' ";
						$sql .= "where domain_uuid = '$domain_uuid' ";
						$sql .= "and dialplan_uuid = '$dialplan_uuid' ";
						$sql .= "and dialplan_detail_uuid = '".$row['dialplan_detail_uuid']."' ";
						//echo $sql."<br />\n";
						$db->exec(check_sql($sql));
						unset($sql);
					}
					if (permission_exists('conferences_add') && permission_exists('conferences_edit')) {
						$field_data_array = explode("=", $row['field_data']);
						if ($field_data_array[0] == "conference_user_list") {
							$sql = "update v_dialplan_details set ";
							//$sql .= "tag = '$tag', ";
							//$sql .= "field_type = '$field_type', ";
							$sql .= "field_data = 'conference_user_list=".$user_list."', ";
							$sql .= "field_order = '".$row['field_order']."' ";
							$sql .= "where domain_uuid = '$domain_uuid' ";
							$sql .= "and dialplan_uuid = '$dialplan_uuid' ";
							$sql .= "and dialplan_detail_uuid = '".$row['dialplan_detail_uuid']."' ";
							//echo $sql."<br />\n";
							$db->exec(check_sql($sql));
							unset($sql);
						}
					}
					if ($row['field_type'] == "conference") {
						$sql = "update v_dialplan_details set ";
						//$sql .= "tag = '$tag', ";
						//$sql .= "field_type = '$field_type', ";
						$sql .= "field_data = '".$tmp_field_data."', ";
						$sql .= "field_order = '".$row['field_order']."' ";
						$sql .= "where domain_uuid = '$domain_uuid' ";
						$sql .= "and dialplan_uuid = '$dialplan_uuid' ";
						$sql .= "and dialplan_detail_uuid = '".$row['dialplan_detail_uuid']."' ";
						$db->exec(check_sql($sql));
						//echo $sql."<br />\n";
						unset($sql);
						unset($field_data);
					}
				}

			} //if ($action == "update")

		//commit the atomic transaction
			$count = $db->exec("COMMIT;"); //returns affected rows

		//synchronize the xml config
			sync_package_v_dialplan();

		require_once "includes/header.php";
		echo "<meta http-equiv=\"refresh\" content=\"2;url=v_conferences.php\">\n";
		echo "<div align='center'>\n";
		if ($action == "add") {
			echo "Add Complete\n";
		}
		if ($action == "update") {
			echo "Update Complete\n";
		}
		echo "</div>\n";
		require_once "includes/footer.php";
		return;

	} //end if (count($_POST)>0 && strlen($_POST["persistformvar"]) == 0)


//pre-populate the form
	if (count($_GET)>0 && $_POST["persistformvar"] != "true") {

		$sql = "";
		$sql .= "select * from v_dialplans ";
		$sql .= "where domain_uuid = '$domain_uuid' ";
		$sql .= "and dialplan_uuid = '$dialplan_uuid' ";
		$row = $db->query($sql)->fetch();
		$dialplan_name = $row['dialplan_name'];
		$dialplan_name = str_replace("-", " ", $dialplan_name);
		$dialplan_context = $row['dialplan_context'];
		$dialplan_order = $row['dialplan_order'];
		$dialplan_enabled = $row['dialplan_enabled'];
		$description = $row['dialplan_description'];

		$sql = "";
		$sql .= "select * from v_dialplan_details ";
		$sql .= "where domain_uuid = '$domain_uuid' ";
		$sql .= "and dialplan_uuid = '$dialplan_uuid' ";
		$prep_statement = $db->prepare(check_sql($sql));
		$prep_statement->execute();
		$result = $prep_statement->fetchAll();
		foreach ($result as &$row) {
			if ($row['field_type'] == "destination_number") {
				$dialplan_number = $row['field_data'];
				$dialplan_number = trim($dialplan_number, '^$');
			}
			$field_data_array = explode("=", $row['field_data']);
			if ($field_data_array[0] == "conference_user_list") {
				$user_list = $field_data_array[1];
			}
			if ($row['field_type'] == "conference") {
				$field_data = $row['field_data'];
				$tmp_pos = stripos($field_data, "@");
				if ($tmp_pos !== false) {
					$tmp_field_data = substr($field_data, $tmp_pos+1, strlen($field_data));
					$tmp_field_data_array = explode("+",$tmp_field_data);
					foreach ($tmp_field_data_array as &$tmp_row) {
						if (is_numeric($tmp_row)) {
							$pin_number = $tmp_row;
						}
						if (substr($tmp_row, 0, 5) == "flags") {
							$flags = substr($tmp_row, 6, $tmp_row-1);
						}
					}
					$profile = $tmp_field_data_array[0];
				}
			}
		}
	}

//show the content
	echo "<div align='center'>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='2'>\n";
	echo "<tr class='border'>\n";
	echo "	<td align=\"left\">\n";
	echo "		<br>";

	echo "<form method='post' name='frm' action=''>\n";
	echo "<div align='center'>\n";

	echo " 	<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";
	echo "	<tr>\n";
	echo "		<td align='left'><span class=\"vexpl\"><span class=\"red\">\n";
	echo "			<strong>Conferences</strong>\n";
	echo "			</span></span>\n";
	echo "		</td>\n";
	echo "		<td align='right'>\n";
	if (permission_exists('conferences_advanced_view') && $action == "update") {
		echo "			<input type='button' class='btn' name='' alt='back' onclick=\"window.location='v_conferences_edit_advanced.php?id=$dialplan_uuid'\" value='Advanced'>\n";
	}
	echo "			<input type='button' class='btn' name='' alt='back' onclick=\"window.location='v_conferences.php'\" value='Back'>\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td align='left' colspan='2'>\n";
	echo "			<span class=\"vexpl\">\n";
	echo "			Conferences are used to setup conference rooms with a name, description, and an optional pin number.\n";
	echo "			</span>\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "	</table>";

	echo "<br />\n";
	echo "<br />\n";

	echo "<table width='100%'  border='0' cellpadding='6' cellspacing='0'>\n";
	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Conference Name:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' style='width: 60%;' type='text' name='dialplan_name' maxlength='255' value=\"$dialplan_name\">\n";
	echo "<br />\n";
	echo "The name the conference will be assigned.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Extension Number:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' style='width: 60%;' type='text' name='dialplan_number' maxlength='255' value=\"$dialplan_number\">\n";
	echo "<br />\n";
	echo "The number that will be assinged to the conference.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "    PIN Number:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' style='width: 60%;' type='text' name='pin_number' maxlength='255' value=\"$pin_number\">\n";
	echo "<br />\n";
	echo "Optional PIN number to secure access to the conference.\n";
	echo "</td>\n";
	echo "</tr>\n";

	if (permission_exists('conferences_add') || permission_exists('conferences_edit')) {
		echo "<tr>\n";
		echo "<td class='vncell' valign='top' align='left' nowrap>\n";
		echo "		User List:\n";
		echo "</td>\n";
		echo "<td class='vtable' align='left'>\n";
		$onchange = "document.getElementById('user_list').value += document.getElementById('username').value + '\\n';";
		$table_name = 'v_users'; $field_name = 'username'; $field_current_value = ''; $sql_where_optional = "where domain_uuid = '$domain_uuid'"; 
		echo htmlselectonchange($db, $table_name, $field_name, $sql_where_optional, $field_current_value, $onchange);
		echo "<br />\n";
		echo "Use the select list to add users to the userlist. This will assign users to this extension.\n";
		echo "<br />\n";
		echo "<br />\n";
		//replace the vertical bar with a line feed to display in the textarea
		$user_list = trim($user_list, "|");
		$user_list_array = explode("|", $user_list);
		$user_list = '';
		foreach($user_list_array as $user){
			$user_list .= trim($user)."\n";
		}
		echo "		<textarea name=\"user_list\" id=\"user_list\" class=\"formfld\" cols=\"30\" rows=\"3\" style='width: 60%;' wrap=\"off\">$user_list</textarea>\n";
		echo "		<br>\n";
		echo "If a user is not in the select list it can be added manually to the user list and it will be created automatically.\n";
		echo "<br />\n";
		echo "</td>\n";
		echo "</tr>\n";
	}

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Profile:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <select class='formfld' name='profile' style='width: 60%;'>\n";
	//if the profile has no value set it to default
	if ($profile == "") { $profile = "default"; }

	if ($profile == "default") { echo "<option value='default' selected='selected'>default</option>\n"; } else {	echo "<option value='default'>default</option>\n"; }
	if ($profile == "wideband") { echo "<option value='wideband' selected='selected'>wideband</option>\n"; } else {	echo "<option value='wideband'>wideband</option>\n"; }
	if ($profile == "ultrawideband") { echo "<option value='ultrawideband' selected='selected'>ultrawideband</option>\n"; } else {	echo "<option value='ultrawideband'>ultrawideband</option>\n"; }
	if ($profile == "cdquality") { echo "<option value='cdquality' selected='selected'>cdquality</option>\n"; } else {	echo "<option value='cdquality'>cdquality</option>\n"; }
	echo "    </select>\n";
	echo "<br />\n";
	echo "Conference Profile is a collection of settings for the conference.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "    Flags:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' style='width: 60%;' type='text' name='flags' maxlength='255' value=\"$flags\">\n";
	echo "<br />\n";
	echo "Optional conference flags. examples: mute|deaf|waste|moderator\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Order:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "              <select name='dialplan_order' class='formfld' style='width: 60%;'>\n";
	if (strlen(htmlspecialchars($dialplan_order))> 0) {
		echo "              <option selected='yes' value='".htmlspecialchars($dialplan_order)."'>".htmlspecialchars($dialplan_order)."</option>\n";
	}
	$i=0;
	while($i<=999) {
		if (strlen($i) == 1) { echo "              <option value='00$i'>00$i</option>\n"; }
		if (strlen($i) == 2) { echo "              <option value='0$i'>0$i</option>\n"; }
		if (strlen($i) == 3) { echo "              <option value='$i'>$i</option>\n"; }
		$i++;
	}
	echo "              </select>\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Enabled:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <select class='formfld' name='dialplan_enabled' style='width: 60%;'>\n";
	if ($dialplan_enabled == "true") { 
		echo "    <option value='true' SELECTED >true</option>\n";
	}
	else {
		echo "    <option value='true'>true</option>\n";
	}
	if ($dialplan_enabled == "false") { 
		echo "    <option value='false' SELECTED >false</option>\n";
	}
	else {
		echo "    <option value='false'>false</option>\n";
	}
	echo "    </select>\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "    Description:\n";
	echo "</td>\n";
	echo "<td colspan='4' class='vtable' align='left'>\n";
	echo "    <input class='formfld' style='width: 60%;' type='text' name='description' maxlength='255' value=\"$description\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "	<td colspan='5' align='right'>\n";
	if ($action == "update") {
		echo "			<input type='hidden' name='dialplan_uuid' value='$dialplan_uuid'>\n";
	}
	echo "			<input type='submit' name='submit' class='btn' value='Save'>\n";
	echo "	</td>\n";
	echo "</tr>";

	echo "</table>";
	echo "</div>";
	echo "</form>";

	echo "</td>\n";
	echo "</tr>";
	echo "</table>";
	echo "</div>";

	echo "<br><br>";

//show the footer
	require_once "includes/footer.php";
?>
