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
		$extension_name = check_str($_POST["extension_name"]);
		$extension_type = check_str($_POST["extension_type"]);
		$extension_number_1 = check_str($_POST["extension_number_1"]);
		$extension_number_2 = check_str($_POST["extension_number_2"]);
		$dialplanorder = check_str($_POST["dialplanorder"]);
		$pin_number = check_str($_POST["pin_number"]);
		$profile = check_str($_POST["profile"]);
		$flags = check_str($_POST["flags"]);
		$enabled = check_str($_POST["enabled"]);
		$description = check_str($_POST["description"]);
		if (strlen($enabled) == 0) { $enabled = "true"; } //set default to enabled
	}

if (count($_POST)>0 && strlen($_POST["persistformvar"]) == 0) {
	//check for all required data
		if (strlen($v_id) == 0) { $msg .= "Please provide: v_id<br>\n"; }
		if (strlen($extension_name) == 0) { $msg .= "Please provide: Extension Name<br>\n"; }
		if (strlen($extension_type) == 0) { $msg .= "Please provide: Extension Type<br>\n"; }
		if (strlen($extension_number_1) == 0) { $msg .= "Please provide: Extension Number 1<br>\n"; }
		if (strlen($extension_number_2) == 0) { $msg .= "Please provide: Extension Number 2<br>\n"; }
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

	// Caller Queue / Agent Queue
	if ($extension_type == "Caller Queue / Agent Queue") {
		//--------------------------------------------------------
		// Agent Queue [FIFO out]
		//<extension name="Agent_Wait">
		//	<condition field="destination_number" expression="^7010\$">
		//		<action application="set" data="fifo_music=\$\${hold_music}"/>
		//		<action application="answer"/>
		//		<action application="fifo" data="myq out wait"/>
		//	</condition>
		//</extension>
		//--------------------------------------------------------
			$extensionname = $extension_name."_agent_queue";
			$context = 'default';
			//$opt1name = 'zzz_id';
			//$opt1value = $row['zzz_id'];
			$dialplan_include_id = v_dialplan_includes_add($v_id, $extensionname, $dialplanorder, $context, $enabled, $description, $opt1name, $opt1value);
			if (strlen($dialplan_include_id) > 0) {
				//set the destination number
					$tag = 'condition'; //condition, action, antiaction
					$fieldtype = 'destination_number';
					$fielddata = '^'.$extension_number_1.'$';
					$fieldorder = '000';
					v_dialplan_includes_details_add($v_id, $dialplan_include_id, $tag, $fieldorder, $fieldtype, $fielddata);
				//set the hold music
					if (strlen($hold_music) > 0) {
						$tag = 'action'; //condition, action, antiaction
						$fieldtype = 'set';
						$fielddata = 'fifo_music=\$\${hold_music_1}';
						$fieldorder = '001';
						v_dialplan_includes_details_add($v_id, $dialplan_include_id, $tag, $fieldorder, $fieldtype, $fielddata);
					}
				//action answer
					$tag = 'action'; //condition, action, antiaction
					$fieldtype = 'answer';
					$fielddata = '';
					$fieldorder = '002';
					v_dialplan_includes_details_add($v_id, $dialplan_include_id, $tag, $fieldorder, $fieldtype, $fielddata);
				//action fifo
					//if (strlen($pin_number) > 0) { $pin_number = "+".$pin_number; }
					//if (strlen($flags) > 0) { $flags = "+{".$flags."}"; }
					//$queue_action_data = $extension_name."_\${domain_name}@".$profile.$flags.$pin_number;
					$queue_action_data = $extension_name."_\${domain_name}@ out wait";
					$tag = 'action'; //condition, action, antiaction
					$fieldtype = 'fifo';
					$fielddata = $queue_action_data;
					$fieldorder = '003';
					v_dialplan_includes_details_add($v_id, $dialplan_include_id, $tag, $fieldorder, $fieldtype, $fielddata);
			}

		//--------------------------------------------------------
		//Caller Queue [FIFO in]
		//<extension name="Queue_Call_In">
		//	<condition field="destination_number" expression="^7011\$">
		//		<action application="set" data="fifo_music=\$\${hold_music}"/>
		//		<action application="answer"/>
		//		<action application="fifo" data="myq in"/>
		//	</condition>
		//</extension>
		//--------------------------------------------------------
			$extensionname = $extension_name."_call_queue";
			$context = 'default';
			//$opt1name = 'zzz_id';
			//$opt1value = $row['zzz_id'];
			$dialplan_include_id = v_dialplan_includes_add($v_id, $extensionname, $dialplanorder, $context, $enabled, $description, $opt1name, $opt1value);
			if (strlen($dialplan_include_id) > 0) {
				//set the destination number
					$tag = 'condition'; //condition, action, antiaction
					$fieldtype = 'destination_number';
					$fielddata = '^'.$extension_number_2.'$';
					$fieldorder = '000';
					v_dialplan_includes_details_add($v_id, $dialplan_include_id, $tag, $fieldorder, $fieldtype, $fielddata);
				//set the hold music
					if (strlen($hold_music) > 0) {
						$tag = 'action'; //condition, action, antiaction
						$fieldtype = 'set';
						$fielddata = 'fifo_music=\$\${hold_music}';
						$fieldorder = '001';
						v_dialplan_includes_details_add($v_id, $dialplan_include_id, $tag, $fieldorder, $fieldtype, $fielddata);
					}
				//action answer
					$tag = 'action'; //condition, action, antiaction
					$fieldtype = 'answer';
					$fielddata = '';
					$fieldorder = '002';
					v_dialplan_includes_details_add($v_id, $dialplan_include_id, $tag, $fieldorder, $fieldtype, $fielddata);
				//action fifo
					//if (strlen($pin_number) > 0) { $pin_number = "+".$pin_number; }
					//if (strlen($flags) > 0) { $flags = "+{".$flags."}"; }
					//$queue_action_data = $extension_name."_\${domain_name}@".$profile.$flags.$pin_number;
					$queue_action_data = $extension_name."_\${domain_name}@ in";
					$tag = 'action'; //condition, action, antiaction
					$fieldtype = 'fifo';
					$fielddata = $queue_action_data;
					$fieldorder = '003';
					v_dialplan_includes_details_add($v_id, $dialplan_include_id, $tag, $fieldorder, $fieldtype, $fielddata);
			}
	} //end if Caller Queue / Agent Queue

	//commit the atomic transaction
		$count = $db->exec("COMMIT;"); //returns affected rows

	//synchronize the xml config
		sync_package_v_dialplan_includes();

	require_once "includes/header.php";
	echo "<meta http-equiv=\"refresh\" content=\"2;url=v_queues.php\">\n";
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
echo "			<strong>Queues</strong>\n";
echo "			</span></span>\n";
echo "		</td>\n";
echo "		<td align='right'>\n";
echo "			<input type='button' class='btn' name='' alt='back' onclick=\"window.location='v_queues.php'\" value='Back'>\n";
echo "		</td>\n";
echo "	</tr>\n";
echo "	<tr>\n";
echo "		<td align='left' colspan='2'>\n";
echo "			<span class=\"vexpl\">\n";
echo "			Queues are used to setup waiting lines for callers. Also known as FIFO Queues.\n";
echo "			</span>\n";
echo "		</td>\n";
echo "	</tr>\n";
echo "	</table>";

echo "<br />\n";
echo "<br />\n";

echo "<table width='100%'  border='0' cellpadding='6' cellspacing='0'>\n";

echo "<tr>\n";
echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
echo "    Queue Name:\n";
echo "</td>\n";
echo "<td class='vtable' align='left'>\n";
echo "    <input class='formfld' style='width: 60%;' type='text' name='extension_name' maxlength='255' value=\"$extension_name\">\n";
echo "<br />\n";
echo "The name the queue will be assigned.\n";
echo "</td>\n";
echo "</tr>\n";

echo "<tr>\n";
echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
echo "    Extension Number 1:\n";
echo "</td>\n";
echo "<td class='vtable' align='left'>\n";
echo "    <input class='formfld' style='width: 60%;' type='text' name='extension_number' maxlength='255' value=\"$extension_number_1\">\n";
echo "<br />\n";
echo "The number that will be assinged to the queue.\n";
echo "</td>\n";
echo "</tr>\n";

echo "<tr>\n";
echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
echo "    Extension Number 2:\n";
echo "</td>\n";
echo "<td class='vtable' align='left'>\n";
echo "    <input class='formfld' style='width: 60%;' type='text' name='extension_number' maxlength='255' value=\"$extension_number_2\">\n";
echo "<br />\n";
echo "The number that will be assinged to the queue.\n";
echo "</td>\n";
echo "</tr>\n";

echo "<tr>\n";
echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
echo "    Type:\n";
echo "</td>\n";
echo "<td class='vtable' align='left'>\n";
echo "    <select class='formfld' name='extension_type' style='width: 60%;'>\n";
echo "    <option value=''></option>\n";
if ($extension_type == "Caller Queue / Agent Queue") { echo "<option value='Caller Queue / Agent Queue' SELECTED >Caller Queue / Agent Queue</option>\n"; } else {	echo "<option value='Caller Queue / Agent Queue'>Caller Queue / Agent Queue</option>\n"; }
if ($extension_type == "Agent Login/Logout/Static") { echo "<option value='Agent Login/Logout/Static' SELECTED >Agent Login/Logout/Static</option>\n"; } else {	echo "<option value='Agent Login/Logout/Static'>Agent Login/Logout/Static</option>\n"; }
echo "    </select>\n";
echo "<br />\n";
echo "Set the type of queue you would like to use.\n";
echo "</td>\n";
echo "</tr>\n";

//echo "<tr>\n";
//echo "<td class='vncell' valign='top' align='left' nowrap>\n";
//echo "    Flags:\n";
//echo "</td>\n";
//echo "<td class='vtable' align='left'>\n";
//echo "    <input class='formfld' style='width: 60%;' type='text' name='flags' maxlength='255' value=\"$flags\">\n";
//echo "<br />\n";
//echo "Optional queue flags. examples: mute|deaf|waste|moderator\n";
//echo "</td>\n";
//echo "</tr>\n";

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
