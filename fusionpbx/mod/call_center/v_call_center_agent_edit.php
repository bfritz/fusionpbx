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
		$call_center_agent_id = check_str($_REQUEST["id"]);
	}
	else {
		$action = "add";
	}

//get http post variables and set them to php variables
	if (count($_POST)>0) {
		$agent_name = check_str($_POST["agent_name"]);
		$agent_type = check_str($_POST["agent_type"]);
		$agent_call_timeout = check_str($_POST["agent_call_timeout"]);
		$agent_contact = check_str($_POST["agent_contact"]);
		$agent_status = check_str($_POST["agent_status"]);
		$agent_max_no_answer = check_str($_POST["agent_max_no_answer"]);
		$agent_wrap_up_time = check_str($_POST["agent_wrap_up_time"]);
		$agent_reject_delay_time = check_str($_POST["agent_reject_delay_time"]);
		$agent_busy_delay_time = check_str($_POST["agent_busy_delay_time"]);
	}

if (count($_POST)>0 && strlen($_POST["persistformvar"]) == 0) {

	$msg = '';

	if ($action == "update") {
		$call_center_agent_id = check_str($_POST["call_center_agent_id"]);
	}

	//check for all required data
		//if (strlen($v_id) == 0) { $msg .= "Please provide: v_id<br>\n"; }
		//if (strlen($agent_name) == 0) { $msg .= "Please provide: Agent Name<br>\n"; }
		//if (strlen($agent_type) == 0) { $msg .= "Please provide: Type<br>\n"; }
		//if (strlen($agent_call_timeout) == 0) { $msg .= "Please provide: Call Timeout<br>\n"; }
		//if (strlen($agent_contact) == 0) { $msg .= "Please provide: Contact<br>\n"; }
		//if (strlen($agent_status) == 0) { $msg .= "Please provide: Status<br>\n"; }
		//if (strlen($agent_max_no_answer) == 0) { $msg .= "Please provide: Max No Answer<br>\n"; }
		//if (strlen($agent_wrap_up_time) == 0) { $msg .= "Please provide: Wrap Up Time<br>\n"; }
		//if (strlen($agent_reject_delay_time) == 0) { $msg .= "Please provide: Reject Delay Time<br>\n"; }
		//if (strlen($agent_busy_delay_time) == 0) { $msg .= "Please provide: Busy Delay Time<br>\n"; }
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
			$sql = "insert into v_call_center_agent ";
			$sql .= "(";
			$sql .= "v_id, ";
			$sql .= "agent_name, ";
			$sql .= "agent_type, ";
			$sql .= "agent_call_timeout, ";
			$sql .= "agent_contact, ";
			$sql .= "agent_status, ";
			$sql .= "agent_max_no_answer, ";
			$sql .= "agent_wrap_up_time, ";
			$sql .= "agent_reject_delay_time, ";
			$sql .= "agent_busy_delay_time ";
			$sql .= ")";
			$sql .= "values ";
			$sql .= "(";
			$sql .= "'$v_id', ";
			$sql .= "'$agent_name', ";
			$sql .= "'$agent_type', ";
			$sql .= "'$agent_call_timeout', ";
			$sql .= "'$agent_contact', ";
			$sql .= "'$agent_status', ";
			$sql .= "'$agent_max_no_answer', ";
			$sql .= "'$agent_wrap_up_time', ";
			$sql .= "'$agent_reject_delay_time', ";
			$sql .= "'$agent_busy_delay_time' ";
			$sql .= ")";
			$db->exec(check_sql($sql));
			unset($sql);

			//syncrhonize configuration
			sync_package_v_call_center();

			require_once "includes/header.php";
			echo "<meta http-equiv=\"refresh\" content=\"2;url=v_call_center_agent.php\">\n";
			echo "<div align='center'>\n";
			echo "Add Complete\n";
			echo "</div>\n";
			require_once "includes/footer.php";
			return;
		} //if ($action == "add")

		if ($action == "update") {
			$sql = "update v_call_center_agent set ";
			//$sql .= "v_id = '$v_id', ";
			$sql .= "agent_name = '$agent_name', ";
			$sql .= "agent_type = '$agent_type', ";
			$sql .= "agent_call_timeout = '$agent_call_timeout', ";
			$sql .= "agent_contact = '$agent_contact', ";
			$sql .= "agent_status = '$agent_status', ";
			$sql .= "agent_max_no_answer = '$agent_max_no_answer', ";
			$sql .= "agent_wrap_up_time = '$agent_wrap_up_time', ";
			$sql .= "agent_reject_delay_time = '$agent_reject_delay_time', ";
			$sql .= "agent_busy_delay_time = '$agent_busy_delay_time' ";
			$sql .= "where call_center_agent_id = '$call_center_agent_id'";
			$db->exec(check_sql($sql));
			unset($sql);

			//syncrhonize configuration
			sync_package_v_call_center();

			require_once "includes/header.php";
			echo "<meta http-equiv=\"refresh\" content=\"2;url=v_call_center_agent.php\">\n";
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
		$call_center_agent_id = $_GET["id"];
		$sql = "";
		$sql .= "select * from v_call_center_agent ";
		$sql .= "where call_center_agent_id = '$call_center_agent_id' ";
		$prep_statement = $db->prepare(check_sql($sql));
		$prep_statement->execute();
		$result = $prep_statement->fetchAll();
		foreach ($result as &$row) {
			$agent_name = $row["agent_name"];
			$agent_type = $row["agent_type"];
			$agent_call_timeout = $row["agent_call_timeout"];
			$agent_contact = $row["agent_contact"];
			$agent_status = $row["agent_status"];
			$agent_max_no_answer = $row["agent_max_no_answer"];
			$agent_wrap_up_time = $row["agent_wrap_up_time"];
			$agent_reject_delay_time = $row["agent_reject_delay_time"];
			$agent_busy_delay_time = $row["agent_busy_delay_time"];
			break; //limit to 1 row
		}
		unset ($prep_statement);
	}

//set default values
	if (strlen($agent_type) == 0) { $agent_type = "callback"; }
	if (strlen($agent_call_timeout) == 0) { $agent_call_timeout = 10; }
	if (strlen($agent_max_no_answer) == 0) { $agent_max_no_answer = "3"; }
	if (strlen($agent_wrap_up_time) == 0) { $agent_wrap_up_time = "10"; }
	if (strlen($agent_reject_delay_time) == 0) { $agent_reject_delay_time = "10"; }
	if (strlen($agent_busy_delay_time) == 0) { $agent_busy_delay_time = "60"; }

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
		echo "<td align='left' width='30%' nowrap><b>Call Center Agent Add</b></td>\n";
	}
	if ($action == "update") {
		echo "<td align='left' width='30%' nowrap><b>Call Center Agent Edit</b></td>\n";
	}
	echo "<td width='70%' align='right'><input type='button' class='btn' name='' alt='back' onclick=\"window.location='v_call_center_agent.php'\" value='Back'></td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td colspan='2'>\n";
	echo "List of call center agents.<br /><br />\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Agent Name:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	//---- Begin Select List --------------------
	$sql = "SELECT * FROM v_users ";
	$sql .= "where v_id = '$v_id' ";
	$sql .= "order by username asc ";
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();

	echo "<select id=\"agent_name\" name=\"agent_name\" class='formfld'>\n";
	echo "<option value=\"\"></option>\n";
	$result = $prepstatement->fetchAll();
	//$catcount = count($result);
	foreach($result as $field) {
		if ($field[username] == $agent_name) {
			echo "<option value='".$field[username]."' selected='selected'>".$field[username]."</option>\n";
		}
		else {
			echo "<option value='".$field[username]."'>".$field[username]."</option>\n";
		}
	}
	echo "</select>";
	unset($sql, $result);
	//---- End Select List --------------------
	echo "<br />\n";
	echo "Select the agents name.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Type:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='agent_type' maxlength='255' value=\"$agent_type\">\n";
	echo "<br />\n";
	echo "Enter the agent type.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Call Timeout:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "  <input class='formfld' type='text' name='agent_call_timeout' maxlength='255' value='$agent_call_timeout'>\n";
	echo "<br />\n";
	echo "Enter the call timeout.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Contact:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";

	//switch_select_destination(select_type, select_label, select_name, select_value, select_style, action);
	switch_select_destination("call_center_contact", "", "agent_contact", $agent_contact, "", "");

	echo "<br />\n";
	echo "Select the contact number.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Status:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<select class='formfld' name='agent_status'>\n";
	echo "	<option value=''></option>\n";
	if ($agent_status == "Logged Out") { 
		echo "	<option value='Logged Out' SELECTED >Logged Out</option>\n";
	}
	else {
		echo "	<option value='Logged Out'>Logged Out</option>\n";
	}
	if ($agent_status == "Available") { 
		echo "	<option value='Available' SELECTED >Available</option>\n";
	}
	else {
		echo "	<option value='Available'>Available</option>\n";
	}
	if ($agent_status == "Available (On Demand)") { 
		echo "	<option value='Available (On Demand)' SELECTED >Available (On Demand)</option>\n";
	}
	else {
		echo "	<option value='Available (On Demand)'>Available (On Demand)</option>\n";
	}
	if ($agent_status == "On Break") { 
		echo "	<option value='On Break' SELECTED >On Break</option>\n";
	}
	else {
		echo "	<option value='On Break'>On Break</option>\n";
	}
	echo "	</select>\n";
	echo "<br />\n";
	echo "Select the agent status.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Max No Answer:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "  <input class='formfld' type='text' name='agent_max_no_answer' maxlength='255' value='$agent_max_no_answer'>\n";
	echo "<br />\n";
	echo "Enter max no answer.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Wrap Up Time:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "  <input class='formfld' type='text' name='agent_wrap_up_time' maxlength='255' value='$agent_wrap_up_time'>\n";
	echo "<br />\n";
	echo "Enter the wrap up time.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Reject Delay Time:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "  <input class='formfld' type='text' name='agent_reject_delay_time' maxlength='255' value='$agent_reject_delay_time'>\n";
	echo "<br />\n";
	echo "Enter the reject delay time.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Busy Delay Time:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "  <input class='formfld' type='text' name='agent_busy_delay_time' maxlength='255' value='$agent_busy_delay_time'>\n";
	echo "<br />\n";
	echo "Enter the agent busy delay time.\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "	<tr>\n";
	echo "		<td colspan='2' align='right'>\n";
	if ($action == "update") {
		echo "				<input type='hidden' name='call_center_agent_id' value='$call_center_agent_id'>\n";
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
