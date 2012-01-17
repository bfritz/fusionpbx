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
if (permission_exists('recordings_add') || permission_exists('recordings_edit')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

//set the action as an add or an update
	if (isset($_REQUEST["id"])) {
		$action = "update";
		$recording_id = check_str($_REQUEST["id"]);
	}
	else {
		$action = "add";
	}

//get the form value and set to php variables
	if (count($_POST)>0) {
		$recording_filename = check_str($_POST["recording_filename"]);
		$recording_name = check_str($_POST["recording_name"]);
		//$recording_uuid = check_str($_POST["recording_uuid"]);
		$recording_desc = check_str($_POST["recording_desc"]);

		//clean the recording filename and name
		$recording_filename = str_replace(" ", "_", $recording_filename);
		$recording_filename = str_replace("'", "", $recording_filename);
		$recording_name = str_replace(" ", "_", $recording_name);
		$recording_name = str_replace("'", "", $recording_name);
	}

if (count($_POST)>0 && strlen($_POST["persistformvar"]) == 0) {

	$msg = '';
	if ($action == "update") {
		$recording_id = check_str($_POST["recording_id"]);
	}

	//check for all required data
		//if (strlen($domain_uuid) == 0) { $msg .= "Please provide: domain_uuid<br>\n"; }
		if (strlen($recording_filename) == 0) { $msg .= "Please provide: Filename (download)<br>\n"; }
		if (strlen($recording_name) == 0) { $msg .= "Please provide: Recording Name (play)<br>\n"; }
		//if (strlen($recording_uuid) == 0) { $msg .= "Please provide: recording_uuid<br>\n"; }
		//if (strlen($recording_desc) == 0) { $msg .= "Please provide: Description<br>\n"; }
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
		if ($action == "add" && permission_exists('recordings_add')) {
			$sql = "insert into v_recordings ";
			$sql .= "(";
			$sql .= "domain_uuid, ";
			$sql .= "recording_filename, ";
			$sql .= "recording_name, ";
			//$sql .= "recording_uuid, ";
			$sql .= "recording_desc ";
			$sql .= ")";
			$sql .= "values ";
			$sql .= "(";
			$sql .= "'$domain_uuid', ";
			$sql .= "'$recording_filename', ";
			$sql .= "'$recording_name', ";
			//$sql .= "'$recording_uuid', ";
			$sql .= "'$recording_desc' ";
			$sql .= ")";
			$db->exec(check_sql($sql));
			unset($sql);

			require_once "includes/header.php";
			echo "<meta http-equiv=\"refresh\" content=\"2;url=v_recordings.php\">\n";
			echo "<div align='center'>\n";
			echo "Add Complete\n";
			echo "</div>\n";
			require_once "includes/footer.php";
			return;
		} //if ($action == "add")

		if ($action == "update" && permission_exists('recordings_edit')) {
			//get the original filename
				$sql = "";
				$sql .= "select * from v_recordings ";
				$sql .= "where recording_id = '$recording_id' ";
				$sql .= "and domain_uuid = '$domain_uuid' ";
				//echo "sql: ".$sql."<br />\n";
				$prepstatement = $db->prepare(check_sql($sql));
				$prepstatement->execute();
				$result = $prepstatement->fetchAll();
				foreach ($result as &$row) {
					$recording_filename_orig = $row["recording_filename"];
					break; //limit to 1 row
				}
				unset ($prepstatement);

			//if file name is not the same then rename the file
				if ($recording_filename != $recording_filename_orig) {
					//echo "orig: ".$v_recordings_dir.'/'.$recording_filename_orig."<br />\n";
					//echo "new: ".$v_recordings_dir.'/'.$recording_filename."<br />\n";
					rename($v_recordings_dir.'/'.$recording_filename_orig, $v_recordings_dir.'/'.$recording_filename);
				}

			//update the database with the new data
				$sql = "update v_recordings set ";
				$sql .= "domain_uuid = '$domain_uuid', ";
				$sql .= "recording_filename = '$recording_filename', ";
				$sql .= "recording_name = '$recording_name', ";
				//$sql .= "recording_uuid = '$recording_uuid', ";
				$sql .= "recording_desc = '$recording_desc' ";
				$sql .= "where domain_uuid = '$domain_uuid'";
				$sql .= "and recording_id = '$recording_id'";
				$db->exec(check_sql($sql));
				unset($sql);

			require_once "includes/header.php";
			echo "<meta http-equiv=\"refresh\" content=\"2;url=v_recordings.php\">\n";
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
		$recording_id = $_GET["id"];
		$sql = "";
		$sql .= "select * from v_recordings ";
		$sql .= "where domain_uuid = '$domain_uuid' ";
		$sql .= "and recording_id = '$recording_id' ";
		$prepstatement = $db->prepare(check_sql($sql));
		$prepstatement->execute();
		$result = $prepstatement->fetchAll();
		foreach ($result as &$row) {
			$domain_uuid = $row["domain_uuid"];
			$recording_filename = $row["recording_filename"];
			$recording_name = $row["recording_name"];
			//$recording_uuid = $row["recording_uuid"];
			$recording_desc = $row["recording_desc"];
			break; //limit to 1 row
		}
		unset ($prepstatement);
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
		echo "<td align='left' width='30%' nowrap><b>Add Recording</b></td>\n";
	}
	if ($action == "update") {
		echo "<td align='left' width='30%' nowrap><b>Edit Recording</b></td>\n";
	}
	echo "<td width='70%' align='right'><input type='button' class='btn' name='' alt='back' onclick=\"window.location='v_recordings.php'\" value='Back'></td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Filename (download):\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' type='text' name='recording_filename' maxlength='255' value=\"$recording_filename\">\n";
	echo "<br />\n";
	echo "Name of the file. example.wav\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Recording Name (play):\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' type='text' name='recording_name' maxlength='255' value=\"$recording_name\">\n";
	echo "<br />\n";
	echo "Recording Name. example: recordingx\n";
	echo "</td>\n";
	echo "</tr>\n";

	//echo "<tr>\n";
	//echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	//echo "    recording_uuid:\n";
	//echo "</td>\n";
	//echo "<td class='vtable' align='left'>\n";
	//echo "    <input class='formfld' type='text' name='recording_uuid' maxlength='255' value=\"$recording_uuid\">\n";
	//echo "<br />\n";
	//echo "\n";
	//echo "</td>\n";
	//echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "    Description:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' type='text' name='recording_desc' maxlength='255' value=\"$recording_desc\">\n";
	echo "<br />\n";
	echo "You may enter a description here for your reference (not parsed).\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "	<tr>\n";
	echo "		<td colspan='2' align='right'>\n";
	if ($action == "update") {
		echo "				<input type='hidden' name='recording_id' value='$recording_id'>\n";
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