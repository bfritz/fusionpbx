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
if (permission_exists('voicemail_greetings_add') || permission_exists('voicemail_greetings_edit')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

//set the action as an add or an update
	if (isset($_REQUEST["id"])) {
		$action = "update";
		$greeting_uuid = check_str($_REQUEST["id"]);
	}
	else {
		$action = "add";
	}

//get the form value and set to php variables
	$user_id = check_str($_REQUEST["user_id"]);
	if (count($_POST)>0) {
		$greeting_name = check_str($_POST["greeting_name"]);
		$greeting_description = check_str($_POST["greeting_description"]);

		//clean the filename and recording name
		$greeting_name = str_replace(" ", "_", $greeting_name);
		$greeting_name = str_replace("'", "", $greeting_name);
	}

if (count($_POST)>0 && strlen($_POST["persistformvar"]) == 0) {

	$msg = '';
	if ($action == "update") {
		$greeting_uuid = check_str($_POST["greeting_uuid"]);
	}

	//check for all required data
		//if (strlen($domain_uuid) == 0) { $msg .= "Please provide: domain_uuid<br>\n"; }
		if (strlen($greeting_name) == 0) { $msg .= "Please provide: Greeting Name (play)<br>\n"; }
		//if (strlen($greeting_description) == 0) { $msg .= "Please provide: Description<br>\n"; }
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
		if ($action == "add" && permission_exists('voicemail_greetings_add')) {
			$voicemail_greeting_uuid = uuid();
			$sql = "insert into v_voicemail_greetings ";
			$sql .= "(";
			$sql .= "domain_uuid, ";
			$sql .= "voicemail_greeting_uuid, ";
			$sql .= "greeting_name, ";
			$sql .= "greeting_description ";
			$sql .= ")";
			$sql .= "values ";
			$sql .= "(";
			$sql .= "'$domain_uuid', ";
			$sql .= "'$voicemail_greeting_uuid', ";
			$sql .= "'$greeting_name', ";
			$sql .= "'$greeting_description' ";
			$sql .= ")";
			$db->exec(check_sql($sql));
			unset($sql);

			require_once "includes/header.php";
			echo "<meta http-equiv=\"refresh\" content=\"2;url=v_voicemail_greetings.php?id=".$user_id."\">\n";
			echo "<div align='center'>\n";
			echo "Add Complete\n";
			echo "</div>\n";
			require_once "includes/footer.php";
			return;
		} //if ($action == "add")

		if ($action == "update" && permission_exists('voicemail_greetings_edit')) {
			//get the original filename
				$sql = "";
				$sql .= "select * from v_voicemail_greetings ";
				$sql .= "where greeting_uuid = '$greeting_uuid' ";
				$sql .= "and domain_uuid = '$domain_uuid' ";
				//echo "sql: ".$sql."<br />\n";
				$prep_statement = $db->prepare(check_sql($sql));
				$prep_statement->execute();
				$result = $prep_statement->fetchAll();
				foreach ($result as &$row) {
					$greeting_name_orig = $row["greeting_name"];
					break; //limit to 1 row
				}
				unset ($prep_statement);

			//if file name is not the same then rename the file
				if ($greeting_name != $greeting_name_orig) {
					//echo "orig: ".$v_voicemail_greetings_dir.'/'.$filename_orig."<br />\n";
					//echo "new: ".$v_voicemail_greetings_dir.'/'.$greeting_name."<br />\n";
					rename($v_voicemail_greetings_dir.'/'.$greeting_name_orig, $v_voicemail_greetings_dir.'/'.$greeting_name);
				}

			//update the database with the new data
				$sql = "update v_voicemail_greetings set ";
				$sql .= "greeting_name = '$greeting_name', ";
				$sql .= "greeting_description = '$greeting_description' ";
				$sql .= "where domain_uuid = '$domain_uuid' ";
				$sql .= "and greeting_uuid = '$greeting_uuid' ";
				$db->exec(check_sql($sql));
				unset($sql);

			require_once "includes/header.php";
			echo "<meta http-equiv=\"refresh\" content=\"2;url=v_voicemail_greetings.php?id=".$user_id."\">\n";
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
		$greeting_uuid = $_GET["id"];
		$sql = "";
		$sql .= "select * from v_voicemail_greetings ";
		$sql .= "where domain_uuid = '$domain_uuid' ";
		$sql .= "and greeting_uuid = '$greeting_uuid' ";
		$prep_statement = $db->prepare(check_sql($sql));
		$prep_statement->execute();
		$result = $prep_statement->fetchAll();
		foreach ($result as &$row) {
			$greeting_name = $row["greeting_name"];
			$greeting_description = $row["greeting_description"];
			break; //limit to 1 row
		}
		unset ($prep_statement);
	}

//show the header
	require_once "includes/header.php";

//show the content
	echo "<div align='center'>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='2'>\n";
	echo "<tr class='border'>\n";
	echo "	<td align=\"left\">\n";
	echo "      <br>";

	echo "<form method='post' name='frm' action=''>\n";

	echo "<div align='center'>\n";
	echo "<table width='100%'  border='0' cellpadding='6' cellspacing='0'>\n";

	echo "<tr>\n";
	if ($action == "add") {
		echo "<td align='left' width='30%' nowrap><b>Add Greeting</b></td>\n";
	}
	if ($action == "update") {
		echo "<td align='left' width='30%' nowrap><b>Edit Greeting</b></td>\n";
	}
	echo "<td width='70%' align='right'><input type='button' class='btn' name='' alt='back' onclick=\"window.location='v_voicemail_greetings.php?id=".$user_id."'\" value='Back'></td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Greeting Name:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' type='text' name='greeting_name' maxlength='255' value=\"$greeting_name\">\n";
	echo "<br />\n";
	echo "Greeting Name. example: greeting_x\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "    Description:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' type='text' name='greeting_description' maxlength='255' value=\"$greeting_description\">\n";
	echo "<br />\n";
	echo "You may enter a description here for your reference (not parsed).\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "	<tr>\n";
	echo "		<td colspan='2' align='right'>\n";
	if ($action == "update") {
		echo "				<input type='hidden' name='greeting_uuid' value='$greeting_uuid'>\n";
	}
	echo "				<input type='hidden' name='user_id' value='$user_id'>\n";
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