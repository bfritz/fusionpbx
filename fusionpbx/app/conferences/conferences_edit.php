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
if (permission_exists('conference_add') || permission_exists('conference_edit')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

//action add or update
	if (isset($_REQUEST["id"])) {
		$action = "update";
		$conference_uuid = check_str($_REQUEST["id"]);
	}
	else {
		$action = "add";
	}

//get http post variables and set them to php variables
	if (count($_POST)>0) {
		$dialplan_uuid = check_str($_POST["dialplan_uuid"]);
		$conference_name = check_str($_POST["conference_name"]);
		$conference_extension = check_str($_POST["conference_extension"]);
		$conference_pin_number = check_str($_POST["conference_pin_number"]);
		$conference_profile = check_str($_POST["conference_profile"]);
		$conference_flags = check_str($_POST["conference_flags"]);
		$conference_order = check_str($_POST["conference_order"]);
		$conference_description = check_str($_POST["conference_description"]);
		$conference_enabled = check_str($_POST["conference_enabled"]);
	}

//delete the user from the v_conference_users
	if ($_GET["a"] == "delete" && permission_exists("conference_delete")) {
		//set the variables
			$user_uuid = check_str($_REQUEST["user_uuid"]);
			$conference_uuid = check_str($_REQUEST["id"]);
		//delete the group from the users
			$sql = "delete from v_conference_users ";
			$sql .= "where domain_uuid = '".$_SESSION['domain_uuid']."' ";
			$sql .= "and conference_uuid = '".$conference_uuid."' ";
			$sql .= "and user_uuid = '".$user_uuid."' ";
			$db->exec(check_sql($sql));
		//redirect the browser
			require_once "includes/header.php";
			echo "<meta http-equiv=\"refresh\" content=\"2;url=conferences_edit.php?id=$conference_uuid\">\n";
			echo "<div align='center'>Delete Complete</div>";
			require_once "includes/footer.php";
			return;
	}

//add the user to the v_conference_users
	if (strlen($_REQUEST["user_uuid"]) > 0 && strlen($_REQUEST["id"]) > 0 && $_GET["a"] != "delete") {
		//set the variables
			$user_uuid = check_str($_REQUEST["user_uuid"]);
			$conference_uuid = check_str($_REQUEST["id"]);
		//assign the user to the fax extension
			$sql_insert = "insert into v_conference_users ";
			$sql_insert .= "(";
			$sql_insert .= "conference_user_uuid, ";
			$sql_insert .= "domain_uuid, ";
			$sql_insert .= "conference_uuid, ";
			$sql_insert .= "user_uuid ";
			$sql_insert .= ")";
			$sql_insert .= "values ";
			$sql_insert .= "(";
			$sql_insert .= "'".uuid()."', ";
			$sql_insert .= "'".$_SESSION['domain_uuid']."', ";
			$sql_insert .= "'".$conference_uuid."', ";
			$sql_insert .= "'".$user_uuid."' ";
			$sql_insert .= ")";
			$db->exec($sql_insert);
		//redirect the browser
			require_once "includes/header.php";
			echo "<meta http-equiv=\"refresh\" content=\"2;url=conferences_edit.php?id=$conference_uuid\">\n";
			echo "<div align='center'>Add Complete</div>";
			require_once "includes/footer.php";
			return;
	}

if (count($_POST)>0 && strlen($_POST["persistformvar"]) == 0) {

	$msg = '';
	if ($action == "update") {
		$conference_uuid = check_str($_POST["conference_uuid"]);
	}

	//check for all required data
		//if (strlen($dialplan_uuid) == 0) { $msg .= "Please provide: Dialplan UUID<br>\n"; }
		if (strlen($conference_name) == 0) { $msg .= "Please provide: Name<br>\n"; }
		if (strlen($conference_extension) == 0) { $msg .= "Please provide: Extension<br>\n"; }
		//if (strlen($conference_pin_number) == 0) { $msg .= "Please provide: Pin Number<br>\n"; }
		if (strlen($conference_profile) == 0) { $msg .= "Please provide: Profile<br>\n"; }
		//if (strlen($conference_flags) == 0) { $msg .= "Please provide: Flags<br>\n"; }
		//if (strlen($conference_order) == 0) { $msg .= "Please provide: Order<br>\n"; }
		//if (strlen($conference_description) == 0) { $msg .= "Please provide: Description<br>\n"; }
		if (strlen($conference_enabled) == 0) { $msg .= "Please provide: Enabled<br>\n"; }
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
			if ($action == "add") {
				//prepare the uuids
					$conference_uuid = uuid();
					$dialplan_uuid = uuid();
				//add the conference
					$sql = "insert into v_conferences ";
					$sql .= "(";
					$sql .= "domain_uuid, ";
					$sql .= "conference_uuid, ";
					$sql .= "dialplan_uuid, ";
					$sql .= "conference_name, ";
					$sql .= "conference_extension, ";
					$sql .= "conference_pin_number, ";
					$sql .= "conference_profile, ";
					$sql .= "conference_flags, ";
					$sql .= "conference_order, ";
					$sql .= "conference_description, ";
					$sql .= "conference_enabled ";
					$sql .= ")";
					$sql .= "values ";
					$sql .= "(";
					$sql .= "'$domain_uuid', ";
					$sql .= "'$conference_uuid', ";
					$sql .= "'$dialplan_uuid', ";
					$sql .= "'$conference_name', ";
					$sql .= "'$conference_extension', ";
					$sql .= "'$conference_pin_number', ";
					$sql .= "'$conference_profile', ";
					$sql .= "'$conference_flags', ";
					$sql .= "'$conference_order', ";
					$sql .= "'$conference_description', ";
					$sql .= "'$conference_enabled' ";
					$sql .= ")";
					$db->exec(check_sql($sql));
					unset($sql);

				//create the dialplan entry for fax
					$dialplan_name = $conference_name;
					$dialplan_order ='333';
					$dialplan_context = $_SESSION['context'];
					$dialplan_enabled = 'true';
					$dialplan_description = $conference_description;
					$app_uuid = 'b81412e8-7253-91f4-e48e-42fc2c9a38d9';			
					dialplan_add($_SESSION['domain_uuid'], $dialplan_uuid, $dialplan_name, $dialplan_order, $dialplan_context, $dialplan_enabled, $dialplan_description, $app_uuid);

					//<condition destination_number="500" />
					$dialplan_detail_tag = 'condition'; //condition, action, antiaction
					$dialplan_detail_type = 'destination_number';
					$dialplan_detail_data = '^'.$conference_extension.'$';
					$dialplan_detail_order = '000';
					$dialplan_detail_group = '2';
					dialplan_details_add($_SESSION['domain_uuid'], $dialplan_uuid, $dialplan_detail_tag, $dialplan_detail_order, $dialplan_detail_group, $dialplan_detail_type, $dialplan_detail_data);

					//<action application="answer" />
					$dialplan_detail_tag = 'action'; //condition, action, antiaction
					$dialplan_detail_type = 'answer';
					$dialplan_detail_data = '';
					$dialplan_detail_order = '010';
					$dialplan_detail_group = '2';
					dialplan_details_add($_SESSION['domain_uuid'], $dialplan_uuid, $dialplan_detail_tag, $dialplan_detail_order, $dialplan_detail_group, $dialplan_detail_type, $dialplan_detail_data);

					//<action application="answer" />
					$dialplan_detail_tag = 'action'; //condition, action, antiaction
					$dialplan_detail_type = 'conference';
					$conference_pin_number = ''; if (strlen($conference_pin_number) > 0) { $conference_pin_number = "+".$conference_pin_number; }
					$conference_flags = ''; if (strlen($conference_flags) > 0) { $conference_flags = "+flags{".$conference_flags."}"; }
					$dialplan_detail_data = $conference_name.'-'.$_SESSION['domain_name']."@".$conference_profile.$conference_pin_number.$conference_flags;
					$dialplan_detail_order = '020';
					$dialplan_detail_group = '2';
					dialplan_details_add($_SESSION['domain_uuid'], $dialplan_uuid, $dialplan_detail_tag, $dialplan_detail_order, $dialplan_detail_group, $dialplan_detail_type, $dialplan_detail_data);

				//save the xml
					save_dialplan_xml();

				//apply settings reminder
					$_SESSION["reload_xml"] = true;

				//redirect the browser
					require_once "includes/header.php";
					echo "<meta http-equiv=\"refresh\" content=\"2;url=conferences.php\">\n";
					echo "<div align='center'>\n";
					echo "Add Complete\n";
					echo "</div>\n";
					require_once "includes/footer.php";
					return;
			} //if ($action == "add")

			if ($action == "update") {
				$sql = "update v_conferences set ";
				//$sql .= "dialplan_uuid = '$dialplan_uuid', ";
				$sql .= "conference_name = '$conference_name', ";
				$sql .= "conference_extension = '$conference_extension', ";
				$sql .= "conference_pin_number = '$conference_pin_number', ";
				$sql .= "conference_profile = '$conference_profile', ";
				$sql .= "conference_flags = '$conference_flags', ";
				$sql .= "conference_order = '$conference_order', ";
				$sql .= "conference_description = '$conference_description', ";
				$sql .= "conference_enabled = '$conference_enabled' ";
				$sql .= "where domain_uuid = '$domain_uuid' ";
				$sql .= "and conference_uuid = '$conference_uuid'";
				$db->exec(check_sql($sql));
				unset($sql);

				require_once "includes/header.php";
				echo "<meta http-equiv=\"refresh\" content=\"2;url=conferences.php\">\n";
				echo "<div align='center'>\n";
				echo "Update Complete\n";
				echo "</div>\n";
				require_once "includes/footer.php";
				return;
			} //if ($action == "update")
		} //if ($_POST["persistformvar"] != "true") 
} //(count($_POST)>0 && strlen($_POST["persistformvar"]) == 0)

//pre-populate the form
	if (count($_GET)>0 && $_POST["persistformvar"] != "true") {
		$conference_uuid = $_GET["id"];
		$sql = "";
		$sql .= "select * from v_conferences ";
		$sql .= "where domain_uuid = '$domain_uuid' ";
		$sql .= "and conference_uuid = '$conference_uuid' ";
		$prep_statement = $db->prepare(check_sql($sql));
		$prep_statement->execute();
		$result = $prep_statement->fetchAll();
		foreach ($result as &$row) {
			$dialplan_uuid = $row["dialplan_uuid"];
			$conference_name = $row["conference_name"];
			$conference_extension = $row["conference_extension"];
			$conference_pin_number = $row["conference_pin_number"];
			$conference_profile = $row["conference_profile"];
			$conference_flags = $row["conference_flags"];
			$conference_order = $row["conference_order"];
			$conference_description = $row["conference_description"];
			$conference_enabled = $row["conference_enabled"];
		}
		unset ($prep_statement);
	}

//show the header
	require_once "includes/header.php";

//show the content
	echo "<div align='center'>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing=''>\n";
	echo "<tr class='border'>\n";
	echo "	<td align=\"left\">\n";
	echo "	  <br>";

	echo "<form method='post' name='frm' action=''>\n";
	echo "<div align='center'>\n";
	echo "<table width='100%'  border='0' cellpadding='6' cellspacing='0'>\n";
	echo "<tr>\n";
	if ($action == "add") {
		echo "<td align='left' width='30%' nowrap='nowrap'><b>Conference Add</b></td>\n";
	}
	if ($action == "update") {
		echo "<td align='left' width='30%' nowrap='nowrap'><b>Conference Edit</b></td>\n";
	}
	echo "<td width='70%' align='right'><input type='button' class='btn' name='' alt='back' onclick=\"window.location='conferences.php'\" value='Back'></td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td colspan='2'>\n";
	echo "Conferences is used to setup conference rooms with a name, description, and optional pin number. Show <a href='/app/conferences_active/v_conferences_active.php'>Active Conferences</a> and then select a conference to monitor and interact with it.<br /><br />\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	Name:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='conference_name' maxlength='255' value=\"$conference_name\">\n";
	echo "<br />\n";
	echo "Enter the conference name.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	Extension:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='conference_extension' maxlength='255' value=\"$conference_extension\">\n";
	echo "<br />\n";
	echo "Enter the conference extension number.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	Pin Number:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='conference_pin_number' maxlength='255' value=\"$conference_pin_number\">\n";
	echo "<br />\n";
	echo "Optional pin number to secure access to the conference.\n";
	echo "</td>\n";
	echo "</tr>\n";

	if (if_group("admin") || if_group("superadmin")) {
		if ($action == "update") {
			echo "	<tr>";
			echo "		<td class='vncell' valign='top'>User List:</td>";
			echo "		<td class='vtable'>";

			echo "			<table width='52%'>\n";
			$sql = "SELECT * FROM v_conference_users as e, v_users as u ";
			$sql .= "where e.user_uuid = u.user_uuid  ";
			$sql .= "and e.domain_uuid = '".$_SESSION['domain_uuid']."' ";
			$sql .= "and e.conference_uuid = '".$conference_uuid."' ";
			$prep_statement = $db->prepare(check_sql($sql));
			$prep_statement->execute();
			$result = $prep_statement->fetchAll(PDO::FETCH_ASSOC);
			$result_count = count($result);
			foreach($result as $field) {
				echo "			<tr>\n";
				echo "				<td class='vtable'>".$field['username']."</td>\n";
				echo "				<td>\n";
				echo "					<a href='conferences_edit.php?id=".$conference_uuid."&domain_uuid=".$_SESSION['domain_uuid']."&user_uuid=".$field['user_uuid']."&a=delete' alt='delete' onclick=\"return confirm('Do you really want to delete this?')\">$v_link_label_delete</a>\n";
				echo "				</td>\n";
				echo "			</tr>\n";
			}
			echo "			</table>\n";

			echo "			<br />\n";
			$sql = "SELECT * FROM v_users ";
			$sql .= "where domain_uuid = '".$_SESSION['domain_uuid']."' ";
			$prep_statement = $db->prepare(check_sql($sql));
			$prep_statement->execute();
			echo "			<select name=\"user_uuid\" class='frm'>\n";
			echo "			<option value=\"\"></option>\n";
			$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
			foreach($result as $field) {
				echo "			<option value='".$field['user_uuid']."'>".$field['username']."</option>\n";
			}
			echo "			</select>";
			echo "			<input type=\"submit\" class='btn' value=\"Add\">\n";
			unset($sql, $result);
			echo "			<br>\n";
			echo "			Assign the users that are can manage this conference extension.\n";
			echo "			<br />\n";
			echo "		</td>";
			echo "	</tr>";
		}
	}

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	Profile:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <select class='formfld' name='conference_profile'>\n";
	//if the profile has no value set it to default
	if ($conference_profile == "") { $conference_profile = "default"; }
	if ($conference_profile == "default") { echo "<option value='default' selected='selected'>default</option>\n"; } else {	echo "<option value='default'>default</option>\n"; }
	if ($conference_profile == "wideband") { echo "<option value='wideband' selected='selected'>wideband</option>\n"; } else {	echo "<option value='wideband'>wideband</option>\n"; }
	if ($conference_profile == "ultrawideband") { echo "<option value='ultrawideband' selected='selected'>ultrawideband</option>\n"; } else {	echo "<option value='ultrawideband'>ultrawideband</option>\n"; }
	if ($conference_profile == "cdquality") { echo "<option value='cdquality' selected='selected'>cdquality</option>\n"; } else {	echo "<option value='cdquality'>cdquality</option>\n"; }
	echo "    </select>\n";
	echo "<br />\n";
	echo "Conference Profile is a collection of settings for the conference.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	Flags:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='conference_flags' maxlength='255' value=\"$conference_flags\">\n";
	echo "<br />\n";
	echo "Optional conference flags. examples: mute|deaf|waste|moderator\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	Order:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "              <select name='conference_order' class='formfld'>\n";
	if (strlen(htmlspecialchars($dialplan_order))> 0) {
		echo "              <option selected='selected' value='".htmlspecialchars($dialplan_order)."'>".htmlspecialchars($dialplan_order)."</option>\n";
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
	echo "Enter the order number.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	Description:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='conference_description' maxlength='255' value=\"$conference_description\">\n";
	echo "<br />\n";
	echo "Enter the description.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	Enabled:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<select class='formfld' name='conference_enabled'>\n";
	echo "	<option value=''></option>\n";
	if ($conference_enabled == "true") { 
		echo "	<option value='true' selected='selected'>true</option>\n";
	}
	else {
		echo "	<option value='true'>true</option>\n";
	}
	if ($conference_enabled == "false") { 
		echo "	<option value='false' selected='selected'>false</option>\n";
	}
	else {
		echo "	<option value='false'>false</option>\n";
	}
	echo "	</select>\n";
	echo "<br />\n";
	echo "Select whether to enable or disable the conference.\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "	<tr>\n";
	echo "		<td colspan='2' align='right'>\n";
	if ($action == "update") {
		echo "				<input type='hidden' name='dialplan_uuid' value=\"$dialplan_uuid\">\n";
		echo "				<input type='hidden' name='conference_uuid' value='$conference_uuid'>\n";
	}
	echo "				<input type='submit' name='submit' class='btn' value='Save'>\n";
	echo "		</td>\n";
	echo "	</tr>";
	echo "</table>";
	echo "</form>";

	echo "	</td>";
	echo "	</tr>";
	echo "</table>";
	echo "</div>";

//include the footer
	require_once "includes/footer.php";
?>