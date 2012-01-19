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
	Copyright (C) 2010
	All Rights Reserved.

	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>
*/
include "root.php";
require_once "includes/config.php";
require_once "includes/checkauth.php";
if (permission_exists('fifo_add')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}
require_once "includes/header.php";
require_once "includes/paging.php";

//get http values and set them as variables
	if (count($_POST)>0) {
		$orderby = $_GET["orderby"];
		$order = $_GET["order"];
		$extension_name = check_str($_POST["extension_name"]);
		$queue_extension_number = check_str($_POST["queue_extension_number"]);
		$agent_queue_extension_number = check_str($_POST["agent_queue_extension_number"]);
		$agent_login_logout_extension_number = check_str($_POST["agent_login_logout_extension_number"]);		
		$dialplan_order = check_str($_POST["dialplan_order"]);
		$pin_number = check_str($_POST["pin_number"]);
		$profile = check_str($_POST["profile"]);
		$flags = check_str($_POST["flags"]);
		$enabled = check_str($_POST["enabled"]);
		$description = check_str($_POST["description"]);
		if (strlen($enabled) == 0) { $enabled = "true"; } //set default to enabled
	}

if (count($_POST)>0 && strlen($_POST["persistformvar"]) == 0) {
	//check for all required data
		if (strlen($domain_uuid) == 0) { $msg .= "Please provide: domain_uuid<br>\n"; }
		if (strlen($extension_name) == 0) { $msg .= "Please provide: Extension Name<br>\n"; }
		if (strlen($queue_extension_number) == 0) { $msg .= "Please provide: Extension Number 1<br>\n"; }
		//if (strlen($agent_queue_extension_number) == 0) { $msg .= "Please provide: Queue Extension Number<br>\n"; }
		//if (strlen($agent_queue_extension_number) == 0) { $msg .= "Please provide: Agent Login Logout Extension Number<br>\n"; }
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

	if (strlen($queue_extension_number) > 0) {
		//--------------------------------------------------------
		//Caller Queue [FIFO in]
		//<extension name="Queue_Call_In">
		//	<condition field="destination_number" expression="^7011\$">
		//		<action application="set" data="fifo_music=$${hold_music}"/>
		//		<action application="answer"/>
		//		<action application="fifo" data="myq in"/>
		//	</condition>
		//</extension>
		//--------------------------------------------------------
			$extension_name = $extension_name."_call_queue";
			$context = 'default';
			//$opt_1_name = 'zzz_id';
			//$opt_1_value = $row['zzz_id'];
			$dialplan_include_uuid = v_dialplan_includes_add($domain_uuid, $extension_name, $dialplan_order, $context, $enabled, $description, $opt_1_name, $opt_1_value);
			if (strlen($dialplan_include_uuid) > 0) {
				//set the destination number
					$tag = 'condition'; //condition, action, antiaction
					$field_type = 'destination_number';
					$field_data = '^'.$queue_extension_number.'$';
					$field_order = '000';
					v_dialplan_includes_details_add($domain_uuid, $dialplan_include_uuid, $tag, $field_order, $field_type, $field_data);
				//set the hold music
					//if (strlen($hold_music) > 0) {
						$tag = 'action'; //condition, action, antiaction
						$field_type = 'set';
						$field_data = 'fifo_music=$${hold_music}';
						$field_order = '001';
						v_dialplan_includes_details_add($domain_uuid, $dialplan_include_uuid, $tag, $field_order, $field_type, $field_data);
					//}
				//action answer
					$tag = 'action'; //condition, action, antiaction
					$field_type = 'answer';
					$field_data = '';
					$field_order = '002';
					v_dialplan_includes_details_add($domain_uuid, $dialplan_include_uuid, $tag, $field_order, $field_type, $field_data);
				//action fifo
					//if (strlen($pin_number) > 0) { $pin_number = "+".$pin_number; }
					//if (strlen($flags) > 0) { $flags = "+{".$flags."}"; }
					//$queue_action_data = $extension_name."@\${domain_name}".$profile.$flags.$pin_number;
					$queue_action_data = $extension_name."@\${domain_name} in";
					$tag = 'action'; //condition, action, antiaction
					$field_type = 'fifo';
					$field_data = $queue_action_data;
					$field_order = '003';
					v_dialplan_includes_details_add($domain_uuid, $dialplan_include_uuid, $tag, $field_order, $field_type, $field_data);
			}
	} //end if queue_extension_number


	// Caller Queue / Agent Queue
	if (strlen($agent_queue_extension_number) > 0) {
		//--------------------------------------------------------
		// Agent Queue [FIFO out]
		//<extension name="Agent_Wait">
		//	<condition field="destination_number" expression="^7010\$">
		//		<action application="set" data="fifo_music=$${hold_music}"/>
		//		<action application="answer"/>
		//		<action application="fifo" data="myq out wait"/>
		//	</condition>
		//</extension>
		//--------------------------------------------------------
			$extension_name = $extension_name."_agent_queue";
			$context = 'default';
			//$opt_1_name = 'zzz_id';
			//$opt_1_value = $row['zzz_id'];
			$dialplan_include_uuid = v_dialplan_includes_add($domain_uuid, $extension_name, $dialplan_order, $context, $enabled, $description, $opt_1_name, $opt_1_value);
			if (strlen($dialplan_include_uuid) > 0) {
				//set the destination number
					$tag = 'condition'; //condition, action, antiaction
					$field_type = 'destination_number';
					$field_data = '^'.$agent_queue_extension_number.'$';
					$field_order = '000';
					v_dialplan_includes_details_add($domain_uuid, $dialplan_include_uuid, $tag, $field_order, $field_type, $field_data);
				//set the hold music
					//if (strlen($hold_music) > 0) {
						$tag = 'action'; //condition, action, antiaction
						$field_type = 'set';
						$field_data = 'fifo_music=$${hold_music}';
						$field_order = '001';
						v_dialplan_includes_details_add($domain_uuid, $dialplan_include_uuid, $tag, $field_order, $field_type, $field_data);
					//}
				//action answer
					$tag = 'action'; //condition, action, antiaction
					$field_type = 'answer';
					$field_data = '';
					$field_order = '002';
					v_dialplan_includes_details_add($domain_uuid, $dialplan_include_uuid, $tag, $field_order, $field_type, $field_data);
				//action fifo
					//if (strlen($pin_number) > 0) { $pin_number = "+".$pin_number; }
					//if (strlen($flags) > 0) { $flags = "+{".$flags."}"; }
					//$queue_action_data = $extension_name."@\${domain_name}".$profile.$flags.$pin_number;
					$queue_action_data = $extension_name."@\${domain_name} out wait";
					$tag = 'action'; //condition, action, antiaction
					$field_type = 'fifo';
					$field_data = $queue_action_data;
					$field_order = '003';
					v_dialplan_includes_details_add($domain_uuid, $dialplan_include_uuid, $tag, $field_order, $field_type, $field_data);
			}
	}

	
	// agent or member login / logout
	if (strlen($agent_login_logout_extension_number) > 0) {
		//--------------------------------------------------------
		// Agent Queue [FIFO out]
		//<extension name="Agent_Wait">
		//	<condition field="destination_number" expression="^7010\$">
		//		<action application="set" data="fifo_music=$${hold_music}"/>
		//		<action application="answer"/>
		//		<action application="fifo" data="myq out wait"/>
		//	</condition>
		//</extension>
		//--------------------------------------------------------
			$extension_name = $extension_name."_agent_login_logout";
			$context = 'default';
			//$opt_1_name = 'zzz_id';
			//$opt_1_value = $row['zzz_id'];
			$dialplan_include_uuid = v_dialplan_includes_add($domain_uuid, $extension_name, $dialplan_order, $context, $enabled, $description, $opt_1_name, $opt_1_value);
			if (strlen($dialplan_include_uuid) > 0) {
				//set the destination number
					$tag = 'condition'; //condition, action, antiaction
					$field_type = 'destination_number';
					$field_data = '^'.$agent_login_logout_extension_number.'$';
					$field_order = '000';
					v_dialplan_includes_details_add($domain_uuid, $dialplan_include_uuid, $tag, $field_order, $field_type, $field_data);
				//set the queue_name
					$tag = 'action'; //condition, action, antiaction
					$field_type = 'set';
					$field_data = 'queue_name='.$extension_name.'@\${domain_name}';
					$field_order = '001';
					v_dialplan_includes_details_add($domain_uuid, $dialplan_include_uuid, $tag, $field_order, $field_type, $field_data);
				//set the fifo_simo
					$tag = 'action'; //condition, action, antiaction
					$field_type = 'set';
					$field_data = 'fifo_simo=1';
					$field_order = '002';
					v_dialplan_includes_details_add($domain_uuid, $dialplan_include_uuid, $tag, $field_order, $field_type, $field_data);
				//set the fifo_timeout
					$tag = 'action'; //condition, action, antiaction
					$field_type = 'set';
					$field_data = 'fifo_timeout=10';
					$field_order = '003';
					v_dialplan_includes_details_add($domain_uuid, $dialplan_include_uuid, $tag, $field_order, $field_type, $field_data);
				//set the fifo_lag
					$tag = 'action'; //condition, action, antiaction
					$field_type = 'set';
					$field_data = 'fifo_lag=10';
					$field_order = '004';
					v_dialplan_includes_details_add($domain_uuid, $dialplan_include_uuid, $tag, $field_order, $field_type, $field_data);
				//set the pin_number
					$tag = 'action'; //condition, action, antiaction
					$field_type = 'set';
					$field_data = 'pin_number=';
					$field_order = '005';
					v_dialplan_includes_details_add($domain_uuid, $dialplan_include_uuid, $tag, $field_order, $field_type, $field_data);
				//action lua
					$tag = 'action'; //condition, action, antiaction
					$field_type = 'lua';
					$field_data = 'fifo_member.lua';
					$field_order = '006';
					v_dialplan_includes_details_add($domain_uuid, $dialplan_include_uuid, $tag, $field_order, $field_type, $field_data);
			}
	}

	//commit the atomic transaction
		$count = $db->exec("COMMIT;"); //returns affected rows

	//synchronize the xml config
		sync_package_v_dialplan_includes();

	//redirect the user
		require_once "includes/header.php";
		echo "<meta http-equiv=\"refresh\" content=\"2;url=v_fifo.php\">\n";
		echo "<div align='center'>\n";
		echo "Update Complete\n";
		echo "</div>\n";
		require_once "includes/footer.php";
		return;

} //end if (count($_POST)>0 && strlen($_POST["persistformvar"]) == 0)

//show the content
	echo "<div align='center'>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='2'>\n";
	echo "<tr class=''>\n";
	echo "	<td align=\"left\">\n";
	echo "		<br>";

	echo "<form method='post' name='frm' action=''>\n";
	echo " 	<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";
	echo "	<tr>\n";
	echo "		<td align='left'><span class=\"vexpl\"><span class=\"red\">\n";
	echo "			<strong>Queues</strong>\n";
	echo "			</span></span>\n";
	echo "		</td>\n";
	echo "		<td align='right'>\n";
	echo "			<input type='button' class='btn' name='' alt='back' onclick=\"window.location='v_fifo.php'\" value='Back'>\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td align='left' colspan='2'>\n";
	echo "			<span class=\"vexpl\">\n";
	echo "			In simple terms queues are holding patterns for callers to wait until someone is available to take the call. Also known as FIFO Queues.\n";
	echo "			</span>\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "	</table>";

	echo "<br />\n";
	echo "<br />\n";

	echo "	<table width='100%'  border='0' cellpadding='6' cellspacing='0'>\n";
	echo "	<tr>\n";
	echo "	<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "		Queue Name:\n";
	echo "	</td>\n";
	echo "	<td class='vtable' align='left'>\n";
	echo "		<input class='formfld' style='width: 60%;' type='text' name='extension_name' maxlength='255' value=\"$extension_name\">\n";
	echo "		<br />\n";
	echo "		The name the queue will be assigned.\n";
	echo "	</td>\n";
	echo "	</tr>\n";

	echo "	<tr>\n";
	echo "	<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "	Extension Number:\n";
	echo "	</td>\n";
	echo "	<td class='vtable' align='left'>\n";
	echo "		<input class='formfld' style='width: 60%;' type='text' name='queue_extension_number' maxlength='255' value=\"$queue_extension_number\">\n";
	echo "		<br />\n";
	echo "		The number that will be assigned to the queue.\n";
	echo "	</td>\n";
	echo "	</tr>\n";

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
	echo "    <select class='formfld' name='enabled' style='width: 60%;'>\n";
	if ($enabled == "true") { 
		echo "    <option value='true' selected='selected' >true</option>\n";
	}
	else {
		echo "    <option value='true'>true</option>\n";
	}
	if ($enabled == "false") { 
		echo "    <option value='false' selected='selected' >false</option>\n";
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
	echo "<td class='vtable' valign='top' align='left' nowrap>\n";
	echo "	<br /><br />\n";
	echo "	<b>Agent Details</b>\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    &nbsp\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td width='30%' class='vncell' valign='top' align='left' nowrap>\n";
	echo "    Queue Extension Number:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' style='width: 60%;' type='text' name='agent_queue_extension_number' maxlength='255' value=\"$agent_queue_extension_number\">\n";
	echo "<br />\n";
	echo "The extension number for the Agent FIFO Queue. This is the holding pattern for agents wating to service calls in the caller FIFO queue.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "    Login/Logout Extension Number:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' style='width: 60%;' type='text' name='agent_login_logout_extension_number' maxlength='255' value=\"$agent_login_logout_extension_number\">\n";
	echo "<br />\n";
	echo "Agents use this extension number to login or logout of the Queue. After logging into the agent will be ready to receive calls from the Queue. \n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";


	echo "<table width='100%' border='0' cellpadding='6' cellspacing='0'>\n";
	echo "<tr>\n";
	echo "	<td colspan='5' align='right'>\n";
	if ($action == "update") {
		echo "			<input type='hidden' name='dialplan_include_uuid' value='$dialplan_include_uuid'>\n";
	}
	echo "			<input type='submit' name='submit' class='btn' value='Save'>\n";
	echo "	</td>\n";
	echo "</tr>";
	echo "</table>";

	echo "</form>";

	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</div>";

	echo "<br><br>";

//show the footer
	require_once "includes/footer.php";
?>
