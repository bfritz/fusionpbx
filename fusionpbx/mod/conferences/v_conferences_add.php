<?php
/* $Id$ */
/*
	v_conferences_add.php
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
require_once "includes/header.php";
require_once "includes/paging.php";

$orderby = $_GET["orderby"];
$order = $_GET["order"];


//POST to PHP variables
	if (count($_POST)>0) {
		$extension_name = checkstr($_POST["extension_name"]);
		$extension_number = checkstr($_POST["extension_number"]);
		$dialplanorder = checkstr($_POST["dialplanorder"]);
		$pin_number = checkstr($_POST["pin_number"]);
		$profile = checkstr($_POST["profile"]);
		$flags = checkstr($_POST["flags"]);
		$enabled = checkstr($_POST["enabled"]);
		$description = checkstr($_POST["description"]);
		if (strlen($enabled) == 0) { $enabled = "true"; } //set default to enabled
	}

if (count($_POST)>0 && strlen($_POST["persistformvar"]) == 0) {
	//check for all required data
		if (strlen($v_id) == 0) { $msg .= "Please provide: v_id<br>\n"; }
		if (strlen($extension_name) == 0) { $msg .= "Please provide: Extension Name<br>\n"; }
		if (strlen($extension_number) == 0) { $msg .= "Please provide: Extension Number<br>\n"; }
		//if (strlen($pin_number) == 0) { $msg .= "Please provide: PIN Number<br>\n"; }
		//if (strlen($profile) == 0) { $msg .= "Please provide: profile<br>\n"; }
		//if (strlen($flags) == 0) { $msg .= "Please provide: Flags<br>\n"; }
		//if (strlen($enabled) == 0) { $msg .= "Please provide: Enabled True or False<br>\n"; }
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

	//add the main dialplan include entry
		$sql = "insert into v_dialplan_includes ";
		$sql .= "(";
		$sql .= "v_id, ";
		$sql .= "extensionname, ";
		$sql .= "dialplanorder, ";
		$sql .= "extensioncontinue, ";
		$sql .= "context, ";
		$sql .= "enabled, ";
		$sql .= "descr ";
		$sql .= ") ";
		$sql .= "values ";
		$sql .= "(";
		$sql .= "'$v_id', ";
		$sql .= "'$extension_name', ";
		$sql .= "'$dialplanorder', ";
		$sql .= "'false', ";
		$sql .= "'default', ";
		$sql .= "'$enabled', ";
		$sql .= "'$description' ";
		$sql .= ")";
		$db->exec($sql);
		$dialplan_include_id = $db->lastInsertId($id);
		unset($sql);

	if (strlen($dialplan_include_id) > 0) {
		//add condition for the extension number
			$sql = "insert into v_dialplan_includes_details ";
			$sql .= "(";
			$sql .= "v_id, ";
			$sql .= "dialplan_include_id, ";
			$sql .= "tag, ";
			$sql .= "fieldtype, ";
			$sql .= "fielddata, ";
			$sql .= "fieldorder ";
			$sql .= ") ";
			$sql .= "values ";
			$sql .= "(";
			$sql .= "'$v_id', ";
			$sql .= "'$dialplan_include_id', ";
			$sql .= "'condition', ";
			$sql .= "'destination_number', ";
			$sql .= "'^".$extension_number."$', ";
			$sql .= "'1' ";
			$sql .= ")";
			$db->exec($sql);
			//$lastinsertid = $db->lastInsertId($id);
			unset($sql);

		//add action answer
			$sql = "insert into v_dialplan_includes_details ";
			$sql .= "(";
			$sql .= "v_id, ";
			$sql .= "dialplan_include_id, ";
			$sql .= "tag, ";
			$sql .= "fieldtype, ";
			$sql .= "fielddata, ";
			$sql .= "fieldorder ";
			$sql .= ") ";
			$sql .= "values ";
			$sql .= "(";
			$sql .= "'$v_id', ";
			$sql .= "'$dialplan_include_id', ";
			$sql .= "'action', ";
			$sql .= "'answer', ";
			$sql .= "'', ";
			$sql .= "'2' ";
			$sql .= ")";
			$db->exec($sql);
			//$lastinsertid = $db->lastInsertId($id);
			unset($sql);

		//add action conference
			if (strlen($pin_number) > 0) { $pin_number = "+".$pin_number; }
			if (strlen($flags) > 0) { $flags = "+{".$flags."}"; }
			$conference_action_data = $extension_name."_\${domain_name}@".$profile.$flags.$pin_number;
			$sql = "insert into v_dialplan_includes_details ";
			$sql .= "(";
			$sql .= "v_id, ";
			$sql .= "dialplan_include_id, ";
			$sql .= "tag, ";
			$sql .= "fieldtype, ";
			$sql .= "fielddata, ";
			$sql .= "fieldorder ";
			$sql .= ") ";
			$sql .= "values ";
			$sql .= "(";
			$sql .= "'$v_id', ";
			$sql .= "'$dialplan_include_id', ";
			$sql .= "'action', ";
			$sql .= "'conference', ";
			$sql .= "'".$conference_action_data."', ";
			$sql .= "'3' ";
			$sql .= ")";
			$db->exec($sql);
			//$lastinsertid = $db->lastInsertId($id);
			unset($sql);
	} //end if (strlen($dialplan_include_id) > 0)

	//commit the atomic transaction
		$count = $db->exec("COMMIT;"); //returns affected rows

	//synchronize the xml config
		sync_package_v_dialplan_includes();

	require_once "includes/header.php";
	echo "<meta http-equiv=\"refresh\" content=\"2;url=v_conferences.php\">\n";
	echo "<div align='center'>\n";
	echo "Update Complete\n";
	echo "</div>\n";
	require_once "includes/footer.php";
	return;

} //end if (count($_POST)>0 && strlen($_POST["persistformvar"]) == 0)


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
echo "			<input type='button' class='btn' name='' alt='back' onclick=\"window.location='v_conferences.php'\" value='Back'>\n";
echo "		</td>\n";
echo "	</tr>\n";
echo "	<tr>\n";
echo "		<td align='left' colspan='2'>\n";
echo "			<span class=\"vexpl\">\n";
echo "			Conferences is used to setup conference rooms with a name, description, and optional pin number.\n";
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
echo "    <input class='formfld' style='width: 60%;' type='text' name='extension_name' maxlength='255' value=\"$extension_name\">\n";
echo "<br />\n";
echo "The name the conference will be assigned.\n";
echo "</td>\n";
echo "</tr>\n";

echo "<tr>\n";
echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
echo "    Extension Number:\n";
echo "</td>\n";
echo "<td class='vtable' align='left'>\n";
echo "    <input class='formfld' style='width: 60%;' type='text' name='extension_number' maxlength='255' value=\"$extension_number\">\n";
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

echo "<tr>\n";
echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
echo "    Profile:\n";
echo "</td>\n";
echo "<td class='vtable' align='left'>\n";
echo "    <select class='formfld' name='profile' style='width: 60%;'>\n";
echo "    <option value=''></option>\n";
if ($profile == "default") { echo "<option value='default' SELECTED >default</option>\n"; } else {	echo "<option value='default'>default</option>\n"; }
if ($profile == "wideband") { echo "<option value='wideband' SELECTED >wideband</option>\n"; } else {	echo "<option value='wideband'>wideband</option>\n"; }
if ($profile == "ultrawideband") { echo "<option value='ultrawideband' SELECTED >ultrawideband</option>\n"; } else {	echo "<option value='ultrawideband'>ultrawideband</option>\n"; }
if ($profile == "cdquality") { echo "<option value='cdquality' SELECTED >cdquality</option>\n"; } else {	echo "<option value='cdquality'>cdquality</option>\n"; }
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
echo "              <select name='dialplanorder' class='formfld' style='width: 60%;'>\n";
//echo "              <option></option>\n";
if (strlen(htmlspecialchars($dialplanorder))> 0) {
	echo "              <option selected='yes' value='".htmlspecialchars($dialplanorder)."'>".htmlspecialchars($dialplanorder)."</option>\n";
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
echo "    <select class='formfld' name='enabled' style='width: 60%;'>\n";
//echo "    <option value=''></option>\n";
if ($enabled == "true") { 
	echo "    <option value='true' SELECTED >true</option>\n";
}
else {
	echo "    <option value='true'>true</option>\n";
}
if ($enabled == "false") { 
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
//echo "    <textarea class='formfld' name='descr' rows='4'>$descr</textarea>\n";
echo "    <input class='formfld' style='width: 60%;' type='text' name='description' maxlength='255' value=\"$description\">\n";
echo "<br />\n";
echo "\n";
echo "</td>\n";
echo "</tr>\n";

echo "<tr>\n";
echo "	<td colspan='5' align='right'>\n";
if ($action == "update") {
	echo "			<input type='hidden' name='dialplan_include_id' value='$dialplan_include_id'>\n";
}
echo "			<input type='submit' name='submit' class='btn' value='Save'>\n";
echo "	</td>\n";
echo "</tr>";

echo "</table>";
echo "</form>";
echo "</div>";

echo "<br><br>";


require_once "includes/footer.php";
unset ($resultcount);
unset ($result);
unset ($key);
unset ($val);
unset ($c);
?>
