<?php
require_once "root.php";
require_once "includes/config.php";
require_once "includes/checkauth.php";
if (ifgroup("admin") || ifgroup("superadmin")) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

//action add or update
	if (isset($_REQUEST["id"])) {
		$action = "update";
		$call_center_queue_id = check_str($_REQUEST["id"]);
	}
	else {
		$action = "add";
	}

//get http post variables and set them to php variables
	if (count($_POST)>0) {
		//$v_id = check_str($_POST["v_id"]);
		$queue_name = check_str($_POST["queue_name"]);
		$queue_extension = check_str($_POST["queue_extension"]);
		$queue_strategy = check_str($_POST["queue_strategy"]);
		$queue_moh_sound = check_str($_POST["queue_moh_sound"]);
		$queue_record_template = check_str($_POST["queue_record_template"]);
		$queue_time_base_score = check_str($_POST["queue_time_base_score"]);
		$queue_max_wait_time = check_str($_POST["queue_max_wait_time"]);
		$queue_max_wait_time_with_no_agent = check_str($_POST["queue_max_wait_time_with_no_agent"]);
		$queue_tier_rules_apply = check_str($_POST["queue_tier_rules_apply"]);
		$queue_tier_rule_wait_second = check_str($_POST["queue_tier_rule_wait_second"]);
		$queue_tier_rule_wait_multiply_level = check_str($_POST["queue_tier_rule_wait_multiply_level"]);
		$queue_tier_rule_no_agent_no_wait = check_str($_POST["queue_tier_rule_no_agent_no_wait"]);
		$queue_discard_abandoned_after = check_str($_POST["queue_discard_abandoned_after"]);
		$queue_abandoned_resume_allowed = check_str($_POST["queue_abandoned_resume_allowed"]);
		$queue_description = check_str($_POST["queue_description"]);
	}

if (count($_POST)>0 && strlen($_POST["persistformvar"]) == 0) {

	$msg = '';

	if ($action == "update") {
		$call_center_queue_id = check_str($_POST["call_center_queue_id"]);
	}

	//check for all required data
		if (strlen($v_id) == 0) { $msg .= "Please provide: v_id<br>\n"; }
		if (strlen($queue_name) == 0) { $msg .= "Please provide: Queue Name<br>\n"; }
		if (strlen($queue_extension) == 0) { $msg .= "Please provide: Extension<br>\n"; }
		if (strlen($queue_strategy) == 0) { $msg .= "Please provide: Strategy<br>\n"; }
		if (strlen($queue_moh_sound) == 0) { $msg .= "Please provide: Music on Hold<br>\n"; }
		//if (strlen($queue_record_template) == 0) { $msg .= "Please provide: Record Template<br>\n"; }
		//if (strlen($queue_time_base_score) == 0) { $msg .= "Please provide: Time Base Score<br>\n"; }
		//if (strlen($queue_max_wait_time) == 0) { $msg .= "Please provide: Max Wait Time<br>\n"; }
		//if (strlen($queue_max_wait_time_with_no_agent) == 0) { $msg .= "Please provide: Max Wait Time with no Agent<br>\n"; }
		//if (strlen($queue_tier_rules_apply) == 0) { $msg .= "Please provide: Tier Rules Apply<br>\n"; }
		//if (strlen($queue_tier_rule_wait_second) == 0) { $msg .= "Please provide: Tier Rule Wait Second<br>\n"; }
		//if (strlen($queue_tier_rule_wait_multiply_level) == 0) { $msg .= "Please provide: Tier Rule Wait Multiply Level<br>\n"; }
		//if (strlen($queue_tier_rule_no_agent_no_wait) == 0) { $msg .= "Please provide: Tier Rule No Agent No Wait<br>\n"; }
		//if (strlen($queue_discard_abandoned_after) == 0) { $msg .= "Please provide: Discard Abandoned After<br>\n"; }
		//if (strlen($queue_abandoned_resume_allowed) == 0) { $msg .= "Please provide: Abandoned Resume Allowed<br>\n"; }
		//if (strlen($queue_description) == 0) { $msg .= "Please provide: Description<br>\n"; }
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
			$sql = "insert into v_call_center_queue ";
			$sql .= "(";
			$sql .= "v_id, ";
			$sql .= "queue_name, ";
			$sql .= "queue_extension, ";
			$sql .= "queue_strategy, ";
			$sql .= "queue_moh_sound, ";
			$sql .= "queue_record_template, ";
			$sql .= "queue_time_base_score, ";
			$sql .= "queue_max_wait_time, ";
			$sql .= "queue_max_wait_time_with_no_agent, ";
			$sql .= "queue_tier_rules_apply, ";
			$sql .= "queue_tier_rule_wait_second, ";
			$sql .= "queue_tier_rule_wait_multiply_level, ";
			$sql .= "queue_tier_rule_no_agent_no_wait, ";
			$sql .= "queue_discard_abandoned_after, ";
			$sql .= "queue_abandoned_resume_allowed, ";
			$sql .= "queue_description ";
			$sql .= ")";
			$sql .= "values ";
			$sql .= "(";
			$sql .= "'$v_id', ";
			$sql .= "'$queue_name', ";
			$sql .= "'$queue_extension', ";
			$sql .= "'$queue_strategy', ";
			$sql .= "'$queue_moh_sound', ";
			$sql .= "'$queue_record_template', ";
			$sql .= "'$queue_time_base_score', ";
			$sql .= "'$queue_max_wait_time', ";
			$sql .= "'$queue_max_wait_time_with_no_agent', ";
			$sql .= "'$queue_tier_rules_apply', ";
			$sql .= "'$queue_tier_rule_wait_second', ";
			$sql .= "'$queue_tier_rule_wait_multiply_level', ";
			$sql .= "'$queue_tier_rule_no_agent_no_wait', ";
			$sql .= "'$queue_discard_abandoned_after', ";
			$sql .= "'$queue_abandoned_resume_allowed', ";
			$sql .= "'$queue_description' ";
			$sql .= ")";
			$db->exec(check_sql($sql));
			unset($sql);

			//syncrhonize configuration
			sync_package_v_call_center();

			require_once "includes/header.php";
			echo "<meta http-equiv=\"refresh\" content=\"2;url=v_call_center_queue.php\">\n";
			echo "<div align='center'>\n";
			echo "Add Complete\n";
			echo "</div>\n";
			require_once "includes/footer.php";
			return;
		} //if ($action == "add")

		if ($action == "update") {
			$sql = "update v_call_center_queue set ";
			$sql .= "v_id = '$v_id', ";
			$sql .= "queue_name = '$queue_name', ";
			$sql .= "queue_extension = '$queue_extension', ";
			$sql .= "queue_strategy = '$queue_strategy', ";
			$sql .= "queue_moh_sound = '$queue_moh_sound', ";
			$sql .= "queue_record_template = '$queue_record_template', ";
			$sql .= "queue_time_base_score = '$queue_time_base_score', ";
			$sql .= "queue_max_wait_time = '$queue_max_wait_time', ";
			$sql .= "queue_max_wait_time_with_no_agent = '$queue_max_wait_time_with_no_agent', ";
			$sql .= "queue_tier_rules_apply = '$queue_tier_rules_apply', ";
			$sql .= "queue_tier_rule_wait_second = '$queue_tier_rule_wait_second', ";
			$sql .= "queue_tier_rule_wait_multiply_level = '$queue_tier_rule_wait_multiply_level', ";
			$sql .= "queue_tier_rule_no_agent_no_wait = '$queue_tier_rule_no_agent_no_wait', ";
			$sql .= "queue_discard_abandoned_after = '$queue_discard_abandoned_after', ";
			$sql .= "queue_abandoned_resume_allowed = '$queue_abandoned_resume_allowed', ";
			$sql .= "queue_description = '$queue_description' ";
			$sql .= "where call_center_queue_id = '$call_center_queue_id'";
			$db->exec(check_sql($sql));
			unset($sql);

			//syncrhonize configuration
			sync_package_v_call_center();

			require_once "includes/header.php";
			echo "<meta http-equiv=\"refresh\" content=\"2;url=v_call_center_queue.php\">\n";
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
		$call_center_queue_id = $_GET["id"];
		$sql = "";
		$sql .= "select * from v_call_center_queue ";
		$sql .= "where call_center_queue_id = '$call_center_queue_id' ";
		$prep_statement = $db->prepare(check_sql($sql));
		$prep_statement->execute();
		$result = $prep_statement->fetchAll();
		foreach ($result as &$row) {
			//$v_id = $row["v_id"];
			$queue_name = $row["queue_name"];
			$queue_extension = $row["queue_extension"];
			$queue_strategy = $row["queue_strategy"];
			$queue_moh_sound = $row["queue_moh_sound"];
			$queue_record_template = $row["queue_record_template"];
			$queue_time_base_score = $row["queue_time_base_score"];
			$queue_max_wait_time = $row["queue_max_wait_time"];
			$queue_max_wait_time_with_no_agent = $row["queue_max_wait_time_with_no_agent"];
			$queue_tier_rules_apply = $row["queue_tier_rules_apply"];
			$queue_tier_rule_wait_second = $row["queue_tier_rule_wait_second"];
			$queue_tier_rule_wait_multiply_level = $row["queue_tier_rule_wait_multiply_level"];
			$queue_tier_rule_no_agent_no_wait = $row["queue_tier_rule_no_agent_no_wait"];
			$queue_discard_abandoned_after = $row["queue_discard_abandoned_after"];
			$queue_abandoned_resume_allowed = $row["queue_abandoned_resume_allowed"];
			$queue_description = $row["queue_description"];
			break; //limit to 1 row
		}
		unset ($prep_statement);
	}

//set default values
	if (strlen($queue_strategy) == 0) { $queue_strategy = "longest-idle-agent"; }
	if (strlen($queue_moh_sound) == 0) { $queue_moh_sound = "\$\${hold_music}"; }
	if (strlen($queue_time_base_score) == 0) { $queue_time_base_score = "system"; }
	if (strlen($queue_max_wait_time) == 0) { $queue_max_wait_time = "0"; }
	if (strlen($queue_max_wait_time_with_no_agent) == 0) { $queue_max_wait_time_with_no_agent = "0"; }
	if (strlen($queue_tier_rules_apply) == 0) { $queue_tier_rules_apply = "false"; }
	if (strlen($queue_tier_rule_wait_second) == 0) { $queue_tier_rule_wait_second = "300"; }
	if (strlen($queue_tier_rule_wait_multiply_level) == 0) { $queue_tier_rule_wait_multiply_level = "true"; }
	if (strlen($queue_tier_rule_no_agent_no_wait) == 0) { $queue_tier_rule_no_agent_no_wait = "false"; }
	if (strlen($queue_discard_abandoned_after) == 0) { $queue_discard_abandoned_after = "60"; }
	if (strlen($queue_abandoned_resume_allowed) == 0) { $queue_abandoned_resume_allowed = "false"; }

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
		echo "<td align='left' width='30%' nowrap><b>Call Center Queue Add</b></td>\n";
	}
	if ($action == "update") {
		echo "<td align='left' width='30%' nowrap><b>Call Center Queue Edit</b></td>\n";
	}
	echo "<td width='70%' align='right'>\n";
	if ($action == "update") {
		//callcenter_config queue load [queue_name]
		// /mod/call_center_active/v_call_center_active.php?queue_name=support%40voip.fusionpbx.com
		echo "  <input type='button' class='btn' value='View Queue' onclick=\"document.location.href='/mod/call_center_active/v_call_center_active.php?queue_name=$queue_name@$v_domain';\" />\n";
		echo "  <input type='button' class='btn' value='Queue Load' onclick=\"document.location.href='v_cmd.php?cmd=api+callcenter_config+queue+load+$queue_name@$v_domain';\" />\n";
		echo "  <input type='button' class='btn' value='Queue Unload' onclick=\"document.location.href='v_cmd.php?cmd=api+callcenter_config+queue+unload+$queue_name@$v_domain';\" />\n";
		echo "  <input type='button' class='btn' value='Queue Reload' onclick=\"document.location.href='v_cmd.php?cmd=api+callcenter_config+queue+reload+$queue_name@$v_domain';\" />\n";
	}
	echo "	<input type='button' class='btn' name='' alt='back' onclick=\"window.location='v_call_center_queue.php'\" value='Back'>\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td colspan='2'>\n";
	echo "List of queues for the call center.<br /><br />\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "	Queue Name:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='queue_name' maxlength='255' value=\"$queue_name\">\n";
	echo "<br />\n";
	echo "Enter the queue name.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "	Extension:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='queue_extension' maxlength='255' value=\"$queue_extension\">\n";
	echo "<br />\n";
	echo "Enter the extension number.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "	Strategy:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<select class='formfld' name='queue_strategy'>\n";
	echo "	<option value=''></option>\n";
	if ($queue_strategy == "ring-all") { 
		echo "	<option value='ring-all' SELECTED >ring-all</option>\n";
	}
	else {
		echo "	<option value='ring-all'>ring-all</option>\n";
	}
	if ($queue_strategy == "longest-idle-agent") { 
		echo "	<option value='longest-idle-agent' SELECTED >longest-idle-agent</option>\n";
	}
	else {
		echo "	<option value='longest-idle-agent'>longest-idle-agent</option>\n";
	}
	if ($queue_strategy == "agent-with-least-talk-time") { 
		echo "	<option value='agent-with-least-talk-time' SELECTED >agent-with-least-talk-time</option>\n";
	}
	else {
		echo "	<option value='agent-with-least-talk-time'>agent-with-least-talk-time</option>\n";
	}
	if ($queue_strategy == "agent-with-fewest-calls") { 
		echo "	<option value='agent-with-fewest-calls' SELECTED >agent-with-fewest-calls</option>\n";
	}
	else {
		echo "	<option value='agent-with-fewest-calls'>agent-with-fewest-calls</option>\n";
	}
	if ($queue_strategy == "sequentially-by-agent-order") { 
		echo "	<option value='sequentially-by-agent-order' SELECTED >sequentially-by-agent-order</option>\n";
	}
	else {
		echo "	<option value='sequentially-by-agent-order'>sequentially-by-agent-order</option>\n";
	}
	echo "	</select>\n";
	echo "<br />\n";
	echo "Enter the queue strategy.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "	Music on Hold:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='queue_moh_sound' maxlength='255' value=\"$queue_moh_sound\">\n";
	echo "<br />\n";
	echo "Enter the music on hold information.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Record Template:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='queue_record_template' maxlength='255' value=\"$queue_record_template\">\n";
	echo "<br />\n";
	echo "Enter a record template.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Time Base Score:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<select class='formfld' name='queue_time_base_score'>\n";
	echo "	<option value=''></option>\n";
	if ($queue_time_base_score == "system") { 
		echo "	<option value='system' SELECTED >system</option>\n";
	}
	else {
		echo "	<option value='system'>system</option>\n";
	}
	if ($queue_time_base_score == "queue") { 
		echo "	<option value='queue' SELECTED >queue</option>\n";
	}
	else {
		echo "	<option value='queue'>queue</option>\n";
	}
	echo "	</select>\n";
	echo "<br />\n";
	echo "Enter the time base score.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Max Wait Time:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "  <input class='formfld' type='text' name='queue_max_wait_time' maxlength='255' value='$queue_max_wait_time'>\n";
	echo "<br />\n";
	echo "Enter the max wait time.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Max Wait Time with no Agent:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "  <input class='formfld' type='text' name='queue_max_wait_time_with_no_agent' maxlength='255' value='$queue_max_wait_time_with_no_agent'>\n";
	echo "<br />\n";
	echo "Enter the max wait time with no agent.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Tier Rules Apply:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<select class='formfld' name='queue_tier_rules_apply'>\n";
	echo "	<option value=''></option>\n";
	if ($queue_tier_rules_apply == "true") { 
		echo "	<option value='true' SELECTED >true</option>\n";
	}
	else {
		echo "	<option value='true'>true</option>\n";
	}
	if ($queue_tier_rules_apply == "false") { 
		echo "	<option value='false' SELECTED >false</option>\n";
	}
	else {
		echo "	<option value='false'>false</option>\n";
	}
	echo "	</select>\n";
	echo "<br />\n";
	echo "Set the tier rule rules apply to true or false.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Tier Rule Wait Second:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "  <input class='formfld' type='text' name='queue_tier_rule_wait_second' maxlength='255' value='$queue_tier_rule_wait_second'>\n";
	echo "<br />\n";
	echo "Enter the tier rule wait seconds.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Tier Rule Wait Multiply Level:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<select class='formfld' name='queue_tier_rule_wait_multiply_level'>\n";
	echo "	<option value=''></option>\n";
	if ($queue_tier_rule_wait_multiply_level == "true") { 
		echo "	<option value='true' SELECTED >true</option>\n";
	}
	else {
		echo "	<option value='true'>true</option>\n";
	}
	if ($queue_tier_rule_wait_multiply_level == "false") { 
		echo "	<option value='false' SELECTED >false</option>\n";
	}
	else {
		echo "	<option value='false'>false</option>\n";
	}
	echo "	</select>\n";
	echo "<br />\n";
	echo "Set the tier rule wait multiply level to true or false.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Tier Rule No Agent No Wait:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<select class='formfld' name='queue_tier_rule_no_agent_no_wait'>\n";
	echo "	<option value=''></option>\n";
	if ($queue_tier_rule_no_agent_no_wait == "true") { 
		echo "	<option value='true' SELECTED >true</option>\n";
	}
	else {
		echo "	<option value='true'>true</option>\n";
	}
	if ($queue_tier_rule_no_agent_no_wait == "false") { 
		echo "	<option value='false' SELECTED >false</option>\n";
	}
	else {
		echo "	<option value='false'>false</option>\n";
	}
	echo "	</select>\n";
	echo "<br />\n";
	echo "Enter the tier rule no agent no wait.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Discard Abandoned After:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "  <input class='formfld' type='text' name='queue_discard_abandoned_after' maxlength='255' value='$queue_discard_abandoned_after'>\n";
	echo "<br />\n";
	echo "Set the discard abandoned after seconds.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Abandoned Resume Allowed:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<select class='formfld' name='queue_abandoned_resume_allowed'>\n";
	echo "	<option value=''></option>\n";
	if ($queue_abandoned_resume_allowed == "true") { 
		echo "	<option value='true' SELECTED >true</option>\n";
	}
	else {
		echo "	<option value='true'>true</option>\n";
	}
	if ($queue_abandoned_resume_allowed == "false") { 
		echo "	<option value='false' SELECTED >false</option>\n";
	}
	else {
		echo "	<option value='false'>false</option>\n";
	}
	echo "	</select>\n";
	echo "<br />\n";
	echo "Set the abandoned resume allowed to true or false.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Description:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='queue_description' maxlength='255' value=\"$queue_description\">\n";
	echo "<br />\n";
	echo "Enter the description.\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "	<tr>\n";
	echo "		<td colspan='2' align='right'>\n";
	if ($action == "update") {
		echo "				<input type='hidden' name='call_center_queue_id' value='$call_center_queue_id'>\n";
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
