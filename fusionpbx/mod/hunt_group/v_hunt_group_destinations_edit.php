<?php
/* $Id$ */
/*
	v_hunt_group_destinations_edit.php
	Copyright (C) 2008 Mark J Crane
	All rights reserved.

	Redistribution and use in source and binary forms, with or without
	modification, are permitted provided that the following conditions are met:

	1. Redistributions of source code must retain the above copyright notice,
	   this list of conditions and the following disclaimer.

	2. Redistributions in binary form must reproduce the above copyright
	   notice, this list of conditions and the following disclaimer in the
	   documentation and/or other materials provided with the distribution.

	THIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES,
	INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY
	AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
	AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
	OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
	SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
	INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
	CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
	ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
	POSSIBILITY OF SUCH DAMAGE.
*/
include "root.php";
require_once "includes/config.php";
require_once "includes/checkauth.php";

if (ifgroup("admin") || ifgroup("superadmin")) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

//Action add or update
if (isset($_REQUEST["id"])) {
	$action = "update";
	$hunt_group_destination_id = checkstr($_REQUEST["id"]);
}
else {
	$action = "add";
}

if (isset($_REQUEST["id2"])) {
	$hunt_group_id = checkstr($_REQUEST["id2"]);
}

//POST to PHP variables
if (count($_POST)>0) {
	//$v_id = checkstr($_POST["v_id"]);
	if (isset($_POST["hunt_group_id"])) {
		$hunt_group_id = checkstr($_POST["hunt_group_id"]);
	}

	$destinationdata = checkstr($_POST["destinationdata"]);
	$destinationtype = checkstr($_POST["destinationtype"]);
	$destinationprofile = checkstr($_POST["destinationprofile"]);
	$destinationorder = checkstr($_POST["destinationorder"]);
	$destinationdescr = checkstr($_POST["destinationdescr"]);
}

if (count($_POST)>0 && strlen($_POST["persistformvar"]) == 0) {

	$msg = '';

	////recommend moving this to the config.php file
	$uploadtempdir = $_ENV["TEMP"]."\\";
	ini_set('upload_tmp_dir', $uploadtempdir);
	////$imagedir = $_ENV["TEMP"]."\\";
	////$filedir = $_ENV["TEMP"]."\\";

	if ($action == "update") {
		$hunt_group_destination_id = checkstr($_POST["hunt_group_destination_id"]);
	}

	//check for all required data
		if (strlen($v_id) == 0) { $msg .= "Please provide: v_id<br>\n"; }
		if (strlen($destinationdata) == 0) { $msg .= "Please provide: Destination<br>\n"; }
		if (strlen($destinationtype) == 0) { $msg .= "Please provide: Type<br>\n"; }
		if (strlen($destinationprofile) == 0) { $msg .= "Please provide: Profile<br>\n"; }
		if (strlen($destinationorder) == 0) { $msg .= "Please provide: Order<br>\n"; }
		//if (strlen($destinationdescr) == 0) { $msg .= "Please provide: Description<br>\n"; }
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
	//$tmp .= "v_id: $v_id\n";
	$tmp .= "Destination: $destinationdata\n";
	$tmp .= "Type: $destinationtype\n";
	$tmp .= "Profile: $destinationprofile\n";
	$tmp .= "Order: $destinationorder\n";
	$tmp .= "Description: $destinationdescr\n";


	//Add or update the database
	if ($_POST["persistformvar"] != "true") {
		if ($action == "add") {
			$sql = "insert into v_hunt_group_destinations ";
			$sql .= "(";
			$sql .= "v_id, ";
			$sql .= "hunt_group_id, ";		
			$sql .= "destinationdata, ";
			$sql .= "destinationtype, ";
			$sql .= "destinationprofile, ";
			$sql .= "destinationorder, ";
			$sql .= "destinationdescr ";
			$sql .= ")";
			$sql .= "values ";
			$sql .= "(";
			$sql .= "'$v_id', ";
			$sql .= "'$hunt_group_id', ";		
			$sql .= "'$destinationdata', ";
			$sql .= "'$destinationtype', ";
			$sql .= "'$destinationprofile', ";
			$sql .= "'$destinationorder', ";
			$sql .= "'$destinationdescr' ";
			$sql .= ")";
			$db->exec($sql);
			//$lastinsertid = $db->lastInsertId($id);
			unset($sql);

			//synchronize the xml config
			sync_package_v_hunt_group();
			
			require_once "includes/header.php";
			echo "<meta http-equiv=\"refresh\" content=\"2;url=v_hunt_group_edit.php?id=".$hunt_group_id."\">\n";
			echo "<div align='center'>\n";
			echo "Add Complete\n";
			echo "</div>\n";
			require_once "includes/footer.php";
			return;
		} //if ($action == "add")

		if ($action == "update") {
			$sql = "update v_hunt_group_destinations set ";
			$sql .= "v_id = '$v_id', ";
			$sql .= "hunt_group_id = '$hunt_group_id', ";		
			$sql .= "destinationdata = '$destinationdata', ";
			$sql .= "destinationtype = '$destinationtype', ";
			$sql .= "destinationprofile = '$destinationprofile', ";
			$sql .= "destinationorder = '$destinationorder', ";
			$sql .= "destinationdescr = '$destinationdescr' ";
			$sql .= "where v_id = '$v_id' ";
			$sql .= "and hunt_group_destination_id = '$hunt_group_destination_id'";
			$db->exec($sql);
			unset($sql);

			//synchronize the xml config
			sync_package_v_hunt_group();
			
			require_once "includes/header.php";
			echo "<meta http-equiv=\"refresh\" content=\"2;url=v_hunt_group_edit.php?id=".$hunt_group_id."\">\n";
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
	$hunt_group_destination_id = $_GET["id"];
	$sql = "";
	$sql .= "select * from v_hunt_group_destinations ";
	$sql .= "where v_id = '$v_id' ";
	$sql .= "and hunt_group_destination_id = '$hunt_group_destination_id' ";
	$prepstatement = $db->prepare($sql);
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	foreach ($result as &$row) {
		//$v_id = $row["v_id"];
		$hunt_group_id = $row["hunt_group_id"];
		$destinationdata = $row["destinationdata"];
		$destinationtype = $row["destinationtype"];
		$destinationprofile = $row["destinationprofile"];
		$destinationorder = $row["destinationorder"];
		$destinationdescr = $row["destinationdescr"];
		break; //limit to 1 row
	}
	unset ($prepstatement);
}


	require_once "includes/header.php";

	echo "<script language='javascript' src='/includes/calendar_popcalendar.js'></script>\n";
	echo "<script language='javascript' src='/includes/calendar_lw_layers.js'></script>\n";
	echo "<script language='javascript' src='/includes/calendar_lw_menu.js'></script>\n";

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
		echo "<td align='left' width='30%' nowrap><b>Destination Add</b></td>\n";
	}
	if ($action == "update") {
		echo "<td align='left' width='30%' nowrap><b>Destination Edit</b></td>\n";
	}
	echo "<td width='70%' align='right'><input type='button' class='btn' name='' alt='back' onclick=\"window.location='v_hunt_group_edit.php?id=".$hunt_group_id."'\" value='Back'></td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Destination:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' type='text' name='destinationdata' maxlength='255' value=\"$destinationdata\">\n";
	echo "<br />\n";
	echo "extension: 1001<br />\n";
	echo "voicemail: 1001<br />\n";
	echo "sip uri (voicemail): sofia/internal/*98@\${domain}<br />\n";
	echo "sip uri (external number): sofia/gateway/gatewayname/12081231234<br />\n";
	echo "sip uri (auto attendant): sofia/internal/5002@\${domain}<br />\n";
	echo "sip uri (user): /user/1001@\${domain}\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Type:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "                <select name='destinationtype' class='formfld'>\n";
	echo "                <option></option>\n";
	if ($destinationtype == "extension") {
		echo "                <option selected='yes'>extension</option>\n";
	}
	else {
		echo "                <option>extension</option>\n";
	}
	if ($destinationtype == "voicemail") {
		echo "                <option selected='yes'>voicemail</option>\n";
	}
	else {
		echo "                <option>voicemail</option>\n";
	}
	if ($destinationtype == "sip uri") {
		echo "                <option selected='yes'>sip uri</option>\n";
	}
	else {
		echo "                <option>sip uri</option>\n";
	}
	echo "                </select>\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Profile:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "                <select name='destinationprofile' class='formfld'>\n";
	echo "                <option></option>\n";
	if (htmlspecialchars($destinationprofile) == "auto") {
		echo "                <option selected='yes'>auto</option>\n";
	}
	else {
		echo "                <option>auto</option>\n";
	}
	foreach (ListFiles($v_conf_dir.'/sip_profiles') as $key=>$sip_profile_file){	
		$sip_profile_name = str_replace(".xml", "", $sip_profile_file);

		if (htmlspecialchars($destinationprofile) == $sip_profile_name) {
			echo "                <option selected='yes'>$sip_profile_name</option>\n";
		}
		else {
			echo "                <option>$sip_profile_name</option>\n";
		}
	}
	echo "                </select>\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Order:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "              <select name='destinationorder' class='formfld'>\n";
	//echo "              <option></option>\n";
	if (strlen(htmlspecialchars($pconfig['destinationorder']))> 0) {
		echo "              <option selected='yes' value='".htmlspecialchars($pconfig['destinationorder'])."'>".htmlspecialchars($pconfig['destinationorder'])."</option>\n";
	}
	$i=0;
	while($i<=999) {
		if (strlen($i) == 1) {
			echo "              <option value='00$i'>00$i</option>\n";
		}
		if (strlen($i) == 2) {
			echo "              <option value='0$i'>0$i</option>\n";
		}
		if (strlen($i) == 3) {
			echo "              <option value='$i'>$i</option>\n";
		}
		$i++;
	}
	echo "              </select>\n";
	echo "<br />\n";
	echo "Processing of each destination is determined by this order. \n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "    Description:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' type='text' name='destinationdescr' maxlength='255' value=\"$destinationdescr\">\n";
	echo "<br />\n";
	echo "You may enter a description here for your reference (not parsed).\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "	<tr>\n";
	echo "		<td colspan='2' align='right'>\n";
	echo "				<input type='hidden' name='hunt_group_id' value='$hunt_group_id'>\n";
	if ($action == "update") {
		echo "				<input type='hidden' name='hunt_group_destination_id' value='$hunt_group_destination_id'>\n";
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


require_once "includes/footer.php";
?>
