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
if (ifgroup("admin") || ifgroup("tenant")) {
	//access granted
}
else {
	echo "access denied";
	exit;
}


//Action add or update
if (isset($_REQUEST["id"])) {
	$action = "update";
	$call_broadcast_id = check_str($_REQUEST["id"]);
}
else {
	$action = "add";
}

//POST to PHP variables
if (count($_POST)>0) {
	$broadcast_name = check_str($_POST["broadcast_name"]);
	$broadcast_desc = check_str($_POST["broadcast_desc"]);
	$broadcast_timeout = check_str($_POST["broadcast_timeout"]);
	$broadcast_concurrent_limit = check_str($_POST["broadcast_concurrent_limit"]);
	$recordingid = check_str($_POST["recordingid"]);
	$broadcast_caller_id_name = check_str($_POST["broadcast_caller_id_name"]);
	$broadcast_caller_id_number = check_str($_POST["broadcast_caller_id_number"]);
	$broadcast_destination_type = check_str($_POST["broadcast_destination_type"]);
	$broadcast_destination_data = check_str($_POST["broadcast_destination_data"]);
}

if (count($_POST)>0 && strlen($_POST["persistformvar"]) == 0) {

	$msg = '';

	////recommend moving this to the config.php file
	$uploadtempdir = $_ENV["TEMP"]."\\";
	ini_set('upload_tmp_dir', $uploadtempdir);
	////$imagedir = $_ENV["TEMP"]."\\";
	////$filedir = $_ENV["TEMP"]."\\";

	if ($action == "update") {
		$call_broadcast_id = check_str($_POST["call_broadcast_id"]);
	}

	//check for all required data
		if (strlen($broadcast_name) == 0) { $msg .= "Please provide: Name<br>\n"; }
		//if (strlen($broadcast_desc) == 0) { $msg .= "Please provide: Description<br>\n"; }
		//if (strlen($broadcast_timeout) == 0) { $msg .= "Please provide: Timeout<br>\n"; }
		//if (strlen($broadcast_concurrent_limit) == 0) { $msg .= "Please provide: Concurrent Limit<br>\n"; }
		//if (strlen($recordingid) == 0) { $msg .= "Please provide: Recording<br>\n"; }
		//if (strlen($broadcast_caller_id_name) == 0) { $msg .= "Please provide: Caller ID Name<br>\n"; }
		//if (strlen($broadcast_caller_id_number) == 0) { $msg .= "Please provide: Caller ID Number<br>\n"; }
		//if (strlen($broadcast_destination_type) == 0) { $msg .= "Please provide: Type<br>\n"; }
		//if (strlen($broadcast_destination_data) == 0) { $msg .= "Please provide: Destination<br>\n"; }
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

	$tmp = "\n";
	$tmp .= "Name: $broadcast_name\n";
	$tmp .= "Description: $broadcast_desc\n";
	$tmp .= "Timeout: $broadcast_timeout\n";
	$tmp .= "Concurrent Limit: $broadcast_concurrent_limit\n";
	$tmp .= "Recording: $recordingid\n";
	$tmp .= "Caller ID Name: $broadcast_caller_id_name\n";
	$tmp .= "Caller ID Number: $broadcast_caller_id_number\n";
	$tmp .= "Type: $broadcast_destination_type\n";
	$tmp .= "Destination: $broadcast_destination_data\n";


	//Add or update the database
	if ($_POST["persistformvar"] != "true") {
		if ($action == "add") {
			$sql = "insert into v_call_broadcast ";
			$sql .= "(";
			$sql .= "broadcast_name, ";
			$sql .= "broadcast_desc, ";
			$sql .= "broadcast_timeout, ";
			$sql .= "broadcast_concurrent_limit, ";
			$sql .= "recordingid, ";
			$sql .= "broadcast_caller_id_name, ";
			$sql .= "broadcast_caller_id_number, ";
			$sql .= "broadcast_destination_type, ";
			$sql .= "broadcast_destination_data ";
			$sql .= ")";
			$sql .= "values ";
			$sql .= "(";
			$sql .= "'$broadcast_name', ";
			$sql .= "'$broadcast_desc', ";
			$sql .= "'$broadcast_timeout', ";
			$sql .= "'$broadcast_concurrent_limit', ";
			$sql .= "'$recordingid', ";
			$sql .= "'$broadcast_caller_id_name', ";
			$sql .= "'$broadcast_caller_id_number', ";
			$sql .= "'$broadcast_destination_type', ";
			$sql .= "'$broadcast_destination_data' ";
			$sql .= ")";
			$db->exec(check_sql($sql));
			unset($sql);

			require_once "includes/header.php";
			echo "<meta http-equiv=\"refresh\" content=\"2;url=v_call_broadcast.php\">\n";
			echo "<div align='center'>\n";
			echo "Add Complete\n";
			echo "</div>\n";
			require_once "includes/footer.php";
			return;
		} //if ($action == "add")

		if ($action == "update") {
			$sql = "update v_call_broadcast set ";
			$sql .= "broadcast_name = '$broadcast_name', ";
			$sql .= "broadcast_desc = '$broadcast_desc', ";
			$sql .= "broadcast_timeout = '$broadcast_timeout', ";
			$sql .= "broadcast_concurrent_limit = '$broadcast_concurrent_limit', ";
			$sql .= "recordingid = '$recordingid', ";
			$sql .= "broadcast_caller_id_name = '$broadcast_caller_id_name', ";
			$sql .= "broadcast_caller_id_number = '$broadcast_caller_id_number', ";
			$sql .= "broadcast_destination_type = '$broadcast_destination_type', ";
			$sql .= "broadcast_destination_data = '$broadcast_destination_data' ";
			$sql .= "where call_broadcast_id = '$call_broadcast_id'";
			$db->exec(check_sql($sql));
			unset($sql);

			require_once "includes/header.php";
			echo "<meta http-equiv=\"refresh\" content=\"2;url=v_call_broadcast.php\">\n";
			echo "<div align='center'>\n";
			echo "Update Complete\n";
			echo "</div>\n";
			require_once "includes/footer.php";
			return;
		} //if ($action == "update")
	} //if ($_POST["persistformvar"] != "true") { 

} //(count($_POST)>0 && strlen($_POST["persistformvar"]) == 0)


//Pre-populate the form
if (count($_GET)>0 && $_POST["persistformvar"] != "true") {
	$call_broadcast_id = $_GET["id"];
	$sql = "";
	$sql .= "select * from v_call_broadcast ";
	$sql .= "where call_broadcast_id = '$call_broadcast_id' ";
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	while($row = $prepstatement->fetch()) {
		$broadcast_name = $row["broadcast_name"];
		$broadcast_desc = $row["broadcast_desc"];
		$broadcast_timeout = $row["broadcast_timeout"];
		$broadcast_concurrent_limit = $row["broadcast_concurrent_limit"];
		$recordingid = $row["recordingid"];
		$broadcast_caller_id_name = $row["broadcast_caller_id_name"];
		$broadcast_caller_id_number = $row["broadcast_caller_id_number"];
		$broadcast_destination_type = $row["broadcast_destination_type"];
		$broadcast_destination_data = $row["broadcast_destination_data"];
		break; //limit to 1 row
	}
	unset ($prepstatement);
}


	require_once "includes/header.php";


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
		echo "<td width='30%' nowrap><b>Call Broadcast</b></td>\n";
	}
	if ($action == "update") {
		echo "<td width='30%' nowrap><b>Call Broadcast Edit</b></td>\n";
	}
	echo "<td width='70%' align='right'><input type='button' class='btn' name='' alt='back' onclick=\"window.location='v_call_broadcast.php'\" value='Back'></td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "	Name:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='broadcast_name' maxlength='255' value=\"$broadcast_name\">\n";
	echo "<br />\n";
	echo "Enter a name here.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Timeout:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='broadcast_timeout' maxlength='255' value=\"$broadcast_timeout\">\n";
	echo "<br />\n";
	echo "Limit the length of the call. Leave this empty for no limit.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Concurrent Limit:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='broadcast_concurrent_limit' maxlength='255' value=\"$broadcast_concurrent_limit\">\n";
	echo "<br />\n";
	echo "Limit the approximate number of concurrent calls. Leave this empty for no limit.\n";
	echo "</td>\n";
	echo "</tr>\n";


	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Recording:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "		<select name='recordingid' class='formfld'>\n";
	echo "		<option></option>\n";
	$sql = "";
	$sql .= "select * from v_recordings ";
	$sql .= "where v_id = '$v_id' ";
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	while($row = $prepstatement->fetch()) {
		if ($recordingid == $row['recording_id']) {
			echo "		<option value='".$row['recording_id']."' selected='yes'>".$row['recordingname']."</option>\n";
		}
		else {
			echo "		<option value='".$row['recording_id']."'>".$row['recordingname']."</option>\n";
		}
	}
	unset ($prepstatement);
	echo "		</select>\n";
	echo "<br />\n";
	echo "Recording to play when the call is answered.<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";


	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Caller ID Name:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='broadcast_caller_id_name' maxlength='255' value=\"$broadcast_caller_id_name\">\n";
	echo "<br />\n";
	echo "Applicable if the provider allow the Caller ID Name to be set. default: anonymous\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Caller ID Number:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='broadcast_caller_id_number' maxlength='255' value=\"$broadcast_caller_id_number\">\n";
	echo "<br />\n";
	echo "Applicable if the provider that allow the Caller ID number to be sent. default: 0000000000\n";
	echo "</td>\n";
	echo "</tr>\n";
/*
	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Type:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='broadcast_destination_type' maxlength='255' value=\"$broadcast_destination_type\">\n";
	echo "<br />\n";
	echo "Optional, Destination Type: bridge, transfer, voicemail, conference, fifo, etc.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Destination:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='broadcast_destination_data' maxlength='255' value=\"$broadcast_destination_data\">\n";
	echo "<br />\n";
	echo "Optional, send the call to an auto attendant, conference room, or any other destination. <br /><br />\n";
	echo "conference (8khz): 01-\${domain}@default <br />\n";
	echo "bridge (external number): sofia/gateway/gatewayname/12081231234 <br />\n";
	echo "bridge (auto attendant): sofia/internal/5002@\${domain} <br />\n";
	echo "transfer (external number): 12081231234 XML default <br />\n";
	echo "</td>\n";
	echo "</tr>\n";
*/

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Description:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='broadcast_desc' maxlength='255' value=\"$broadcast_desc\">\n";
	//echo "	<textarea class='formfld' name='broadcast_desc' rows='4'>$broadcast_desc</textarea>\n";
	echo "<br />\n";
	echo "Enter a description here.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "	<tr>\n";
	echo "		<td colspan='2' align='right'>\n";
	if ($action == "update") {
		echo "				<input type='hidden' name='call_broadcast_id' value='$call_broadcast_id'>\n";
	}
	echo "				<input type='submit' name='submit' class='btn' value='Save'>\n";
	echo "		</td>\n";
	echo "	</tr>";
	echo "</table>";
	echo "</form>";

	echo "<br />\n";
	echo "<br />\n";
	echo "<br />\n";

	if ($action == "update") {

		echo "<table width='100%' border='0'>\n";
		echo "<tr>\n";
		echo "<td width='50%' nowrap><b>Call Broadcast</b></td>\n";
		echo "<td width='50%' align='right'>&nbsp;</td>\n";
		echo "</tr>\n";
		echo "</table>\n";

		echo "<form method='get' name='frm' action='v_call_broadcast_send.php'>\n";

		echo "<div align='center'>\n";
		echo "<table width='100%'  border='0' cellpadding='6' cellspacing='0'>\n";

		echo "<tr>\n";
		echo "<td width='30%' class='vncell' valign='top' align='left' nowrap>\n";
		echo "	Category:\n";
		echo "</td>\n";
		echo "<td width='70%' class='vtable' align='left'>\n";
		echo "		<select name='usercategory' class='formfld'>\n";
		echo "		<option></option>\n";
		$sql = "";
		$sql .= "select distinct(usercategory) as usercategory from v_users ";
		//$sql .= "where v_id = '$v_id' ";
		$prepstatement = $db->prepare(check_sql($sql));
		$prepstatement->execute();
		while($row = $prepstatement->fetch()) {
			if ($usercategory   == $row['usercategory']) {
				echo "		<option value='".$row['usercategory']."' selected='yes'>".$row['usercategory']."</option>\n";
			}
			else {
				echo "		<option value='".$row['usercategory']."'>".$row['usercategory']."</option>\n";
			}
		}
		unset ($prepstatement);
		echo "		</select>\n";
		echo "<br />\n";
		//echo "zzz.<br />\n";
		echo "\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<td width='30%' class='vncell' valign='top' align='left' nowrap>\n";
		echo "	Group:\n";
		echo "</td>\n";
		echo "<td width='70%' class='vtable' align='left'>\n";
		echo "		<select name='groupid' class='formfld'>\n";
		echo "		<option></option>\n";
		$sql = "";
		$sql .= "select * from v_groups ";
		//$sql .= "where v_id = '$v_id' ";
		$prepstatement = $db->prepare(check_sql($sql));
		$prepstatement->execute();
		while($row = $prepstatement->fetch()) {
			if ($recordingid == $row['groupid']) {
				echo "		<option value='".$row['groupid']."' selected='yes'>".$row['groupid']."</option>\n";
			}
			else {
				echo "		<option value='".$row['groupid']."'>".$row['groupid']."</option>\n";
			}
		}
		unset ($prepstatement);
		echo "		</select>\n";
		echo "<br />\n";
		//echo "zzz.<br />\n";
		echo "\n";
		echo "</td>\n";
		echo "</tr>\n";


		echo "<tr>\n";
		echo "<td width='30%' class='vncell' valign='top' align='left' nowrap>\n";
		echo "	Gateway:\n";
		echo "</td>\n";
		echo "<td width='70%' class='vtable' align='left'>\n";
		echo "		<select name='gateway' class='formfld'>\n";
		echo "		<option></option>\n";
		$sql = "";
		$sql .= "select * from v_gateways ";
		//$sql .= "where v_id = '$v_id' ";
		$prepstatement = $db->prepare(check_sql($sql));
		$prepstatement->execute();
		while($row = $prepstatement->fetch()) {
			if ($gateway == $row['gateway']) {
				echo "		<option value='".$row['gateway']."' selected='yes'>".$row['gateway']."</option>\n";
			}
			else {
				echo "		<option value='".$row['gateway']."'>".$row['gateway']."</option>\n";
			}
		}
		unset ($prepstatement);
		echo "		<option value='loopback'>loopback</option>\n";
		echo "		</select>\n";
		echo "<br />\n";
		//echo "zzz.<br />\n";
		echo "\n";
		echo "</td>\n";
		echo "</tr>\n";


		echo "<tr>\n";
		echo "<td class='vncell' valign='top' align='left' nowrap>\n";
		echo "	Phone Type:\n";
		echo "</td>\n";
		echo "<td class='vtable' align='left'>\n";
		echo "		<select name='phonetype1' class='formfld'>\n";
		echo "		<option></option>\n";
		echo "		<option value='phone1'>phone1</option>\n";
		echo "		<option value='phone2'>phone2</option>\n";
		echo "		<option value='cell'>cell</option>\n";
		//echo "		<option value='zzz'>cell</option>\n";
		echo "		</select>\n";
		echo "<br />\n";
		//echo "zzz.<br />\n";
		echo "\n";
		echo "</td>\n";
		echo "</tr>\n";


		echo "<tr>\n";
		echo "<td class='vncell' valign='top' align='left' nowrap>\n";
		echo "	Phone Type:\n";
		echo "</td>\n";
		echo "<td class='vtable' align='left'>\n";
		echo "		<select name='phonetype2' class='formfld'>\n";
		echo "		<option></option>\n";
		echo "		<option value='phone1'>phone1</option>\n";
		echo "		<option value='phone2'>phone2</option>\n";
		echo "		<option value='cell'>cell</option>\n";
		//echo "		<option value='zzz'>cell</option>\n";
		echo "		</select>\n";
		echo "<br />\n";
		//echo "zzz.<br />\n";
		echo "\n";
		echo "</td>\n";
		echo "</tr>\n";


		echo "	<tr>\n";
		echo "		<td colspan='2' align='right'>\n";
		echo "				<input type='hidden' name='call_broadcast_id' value='$call_broadcast_id'>\n";
		echo "				<input type='submit' name='submit' class='btn' value='Send Broadcast'>\n";
		echo "		</td>\n";
		echo "	</tr>";

		echo "</table>";
		echo "</form>";
	}



	echo "	</td>";
	echo "	</tr>";
	echo "</table>";
	echo "</div>";


require_once "includes/footer.php";
?>
