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
		$call_center_tier_id = check_str($_REQUEST["id"]);
	}
	else {
		$action = "add";
	}

//get http post variables and set them to php variables
	if (count($_POST)>0) {
		//$v_id = check_str($_POST["v_id"]);
		$agent_name = check_str($_POST["agent_name"]);
		$queue_name = check_str($_POST["queue_name"]);
		$tier_level = check_str($_POST["tier_level"]);
		$tier_position = check_str($_POST["tier_position"]);
	}

if (count($_POST)>0 && strlen($_POST["persistformvar"]) == 0) {

	$msg = '';

	////recommend moving this to the config.php file
	$uploadtempdir = $_ENV["TEMP"]."\\";
	ini_set('upload_tmp_dir', $uploadtempdir);
	////$imagedir = $_ENV["TEMP"]."\\";
	////$filedir = $_ENV["TEMP"]."\\";

	if ($action == "update") {
		$call_center_tier_id = check_str($_POST["call_center_tier_id"]);
	}

	//check for all required data
		//if (strlen($v_id) == 0) { $msg .= "Please provide: v_id<br>\n"; }
		//if (strlen($agent_name) == 0) { $msg .= "Please provide: Agent Name<br>\n"; }
		//if (strlen($queue_name) == 0) { $msg .= "Please provide: Queue Name<br>\n"; }
		//if (strlen($tier_level) == 0) { $msg .= "Please provide: Tier Level<br>\n"; }
		//if (strlen($tier_position) == 0) { $msg .= "Please provide: Tier Position<br>\n"; }
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
			$sql = "insert into v_call_center_tier ";
			$sql .= "(";
			$sql .= "v_id, ";
			$sql .= "agent_name, ";
			$sql .= "queue_name, ";
			$sql .= "tier_level, ";
			$sql .= "tier_position ";
			$sql .= ")";
			$sql .= "values ";
			$sql .= "(";
			$sql .= "'$v_id', ";
			$sql .= "'$agent_name', ";
			$sql .= "'$queue_name', ";
			$sql .= "'$tier_level', ";
			$sql .= "'$tier_position' ";
			$sql .= ")";
			$db->exec(check_sql($sql));
			unset($sql);

			//syncrhonize configuration
			sync_package_v_call_center();

			require_once "includes/header.php";
			echo "<meta http-equiv=\"refresh\" content=\"2;url=v_call_center_tier.php\">\n";
			echo "<div align='center'>\n";
			echo "Add Complete\n";
			echo "</div>\n";
			require_once "includes/footer.php";
			return;
		} //if ($action == "add")

		if ($action == "update") {
			$sql = "update v_call_center_tier set ";
			$sql .= "v_id = '$v_id', ";
			$sql .= "agent_name = '$agent_name', ";
			$sql .= "queue_name = '$queue_name', ";
			$sql .= "tier_level = '$tier_level', ";
			$sql .= "tier_position = '$tier_position' ";
			$sql .= "where call_center_tier_id = '$call_center_tier_id'";
			$db->exec(check_sql($sql));
			unset($sql);

			//syncrhonize configuration
			sync_package_v_call_center();

			require_once "includes/header.php";
			echo "<meta http-equiv=\"refresh\" content=\"2;url=v_call_center_tier.php\">\n";
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
		$call_center_tier_id = $_GET["id"];
		$sql = "";
		$sql .= "select * from v_call_center_tier ";
		$sql .= "where call_center_tier_id = '$call_center_tier_id' ";
		$prep_statement = $db->prepare(check_sql($sql));
		$prep_statement->execute();
		$result = $prep_statement->fetchAll();
		foreach ($result as &$row) {
			//$v_id = $row["v_id"];
			$agent_name = $row["agent_name"];
			$queue_name = $row["queue_name"];
			$tier_level = $row["tier_level"];
			$tier_position = $row["tier_position"];
			break; //limit to 1 row
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
		echo "<td align='left' width='30%' nowrap='nowrap'><b>Call Center Tier Add</b></td>\n";
	}
	if ($action == "update") {
		echo "<td align='left' width='30%' nowrap='nowrap'><b>Call Center Tier Edit</b></td>\n";
	}
	echo "<td width='70%' align='right'><input type='button' class='btn' name='' alt='back' onclick=\"window.location='v_call_center_tier.php'\" value='Back'></td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td align='left' colspan='2'>\n";
	echo "List all tiers. Tiers assign agents to queues.<br /><br />\n";
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
	echo "Select the agent name.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Queue Name:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";

	//---- Begin Select List --------------------
	$sql = "SELECT * FROM v_call_center_queue ";
	$sql .= "where v_id = '$v_id' ";
	$sql .= "order by queue_name asc ";
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();

	echo "<select id=\"queue_name\" name=\"queue_name\" class='formfld'>\n";
	echo "<option value=\"\"></option>\n";
	$result = $prepstatement->fetchAll();
	//$catcount = count($result);
	foreach($result as $field) {
		if ($field[queue_name] == $queue_name) {
			echo "<option value='".$field[queue_name]."' selected='selected'>".$field[queue_name]."</option>\n";
		}
		else {
			echo "<option value='".$field[queue_name]."'>".$field[queue_name]."</option>\n";
		}
	}
	echo "</select>";
	unset($sql, $result);
	//---- End Select List --------------------

	echo "<br />\n";
	echo "Select the queue name.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Tier Level:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<select class='formfld' name='tier_level'>\n";
	//echo "	<option value=''></option>\n";
	if ($tier_level == "1") { 
		echo "	<option value='1' SELECTED >1</option>\n";
	}
	else {
		echo "	<option value='1'>1</option>\n";
	}
	if ($tier_level == "2") { 
		echo "	<option value='2' SELECTED >2</option>\n";
	}
	else {
		echo "	<option value='2'>2</option>\n";
	}
	if ($tier_level == "3") { 
		echo "	<option value='3' SELECTED >3</option>\n";
	}
	else {
		echo "	<option value='3'>3</option>\n";
	}
	if ($tier_level == "4") { 
		echo "	<option value='4' SELECTED >4</option>\n";
	}
	else {
		echo "	<option value='4'>4</option>\n";
	}
	if ($tier_level == "5") { 
		echo "	<option value='5' SELECTED >5</option>\n";
	}
	else {
		echo "	<option value='5'>5</option>\n";
	}
	if ($tier_level == "6") { 
		echo "	<option value='6' SELECTED >6</option>\n";
	}
	else {
		echo "	<option value='6'>6</option>\n";
	}
	if ($tier_level == "7") { 
		echo "	<option value='7' SELECTED >7</option>\n";
	}
	else {
		echo "	<option value='7'>7</option>\n";
	}
	if ($tier_level == "8") { 
		echo "	<option value='8' SELECTED >8</option>\n";
	}
	else {
		echo "	<option value='8'>8</option>\n";
	}
	if ($tier_level == "9") { 
		echo "	<option value='9' SELECTED >9</option>\n";
	}
	else {
		echo "	<option value='9'>9</option>\n";
	}
	echo "	</select>\n";
	echo "<br />\n";
	echo "Select the tier level.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Tier Position:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<select class='formfld' name='tier_position'>\n";
	//echo "	<option value=''></option>\n";
	if ($tier_position == "1") { 
		echo "	<option value='1' SELECTED >1</option>\n";
	}
	else {
		echo "	<option value='1'>1</option>\n";
	}
	if ($tier_position == "2") { 
		echo "	<option value='2' SELECTED >2</option>\n";
	}
	else {
		echo "	<option value='2'>2</option>\n";
	}
	if ($tier_position == "3") { 
		echo "	<option value='3' SELECTED >3</option>\n";
	}
	else {
		echo "	<option value='3'>3</option>\n";
	}
	if ($tier_position == "4") { 
		echo "	<option value='4' SELECTED >4</option>\n";
	}
	else {
		echo "	<option value='4'>4</option>\n";
	}
	if ($tier_position == "5") { 
		echo "	<option value='5' SELECTED >5</option>\n";
	}
	else {
		echo "	<option value='5'>5</option>\n";
	}
	if ($tier_position == "6") { 
		echo "	<option value='6' SELECTED >6</option>\n";
	}
	else {
		echo "	<option value='6'>6</option>\n";
	}
	if ($tier_position == "7") { 
		echo "	<option value='7' SELECTED >7</option>\n";
	}
	else {
		echo "	<option value='7'>7</option>\n";
	}
	if ($tier_position == "8") { 
		echo "	<option value='8' SELECTED >8</option>\n";
	}
	else {
		echo "	<option value='8'>8</option>\n";
	}
	if ($tier_position == "9") { 
		echo "	<option value='9' SELECTED >9</option>\n";
	}
	else {
		echo "	<option value='9'>9</option>\n";
	}
	echo "	</select>\n";
	echo "<br />\n";
	echo "Select the tier position.\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "	<tr>\n";
	echo "		<td colspan='2' align='right'>\n";
	if ($action == "update") {
		echo "				<input type='hidden' name='call_center_tier_id' value='$call_center_tier_id'>\n";
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
