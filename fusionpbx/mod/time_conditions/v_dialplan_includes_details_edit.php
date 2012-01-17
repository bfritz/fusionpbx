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
if (permission_exists('time_conditions_add') || permission_exists('time_conditions_edit')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

//set the action as an add or an update
	if (isset($_REQUEST["id"])) {
		$action = "update";
		$dialplan_includes_detail_id = check_str($_REQUEST["id"]);
	}
	else {
		$action = "add";
		$dialplan_include_id = check_str($_REQUEST["id2"]);
	}

//set http values as variables
	if (isset($_REQUEST["id2"])) {
		$dialplan_include_id = check_str($_REQUEST["id2"]);
	}
	if (count($_POST)>0) {
		//$domain_uuid = check_str($_POST["domain_uuid"]);
		if (isset($_REQUEST["dialplan_include_id"])) {
			$dialplan_include_id = check_str($_POST["dialplan_include_id"]);
		}
		$tag = check_str($_POST["tag"]);
		$field_order = check_str($_POST["field_order"]);
		$field_type = check_str($_POST["field_type"]);
		$field_data = check_str($_POST["field_data"]);
	}

if (count($_POST)>0 && strlen($_POST["persistformvar"]) == 0) {

	$msg = '';
	if ($action == "update") {
		$dialplan_includes_detail_id = check_str($_POST["dialplan_includes_detail_id"]);
	}

	//check for all required data
		if (strlen($domain_uuid) == 0) { $msg .= "Please provide: domain_uuid<br>\n"; }
		if (strlen($tag) == 0) { $msg .= "Please provide: Tag<br>\n"; }
		if (strlen($field_order) == 0) { $msg .= "Please provide: Order<br>\n"; }
		//if (strlen($field_type) == 0) { $msg .= "Please provide: Type<br>\n"; }
		//if (strlen($field_data) == 0) { $msg .= "Please provide: Data<br>\n"; }
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
			if ($action == "add" && permission_exists('time_conditions_add')) {
				$sql = "insert into v_dialplan_includes_details ";
				$sql .= "(";
				$sql .= "domain_uuid, ";
				$sql .= "dialplan_include_id, ";
				$sql .= "tag, ";
				$sql .= "field_order, ";
				$sql .= "field_type, ";
				$sql .= "field_data ";
				$sql .= ")";
				$sql .= "values ";
				$sql .= "(";
				$sql .= "'$domain_uuid', ";
				$sql .= "'$dialplan_include_id', ";
				$sql .= "'$tag', ";
				$sql .= "'$field_order', ";
				$sql .= "'$field_type', ";
				$sql .= "'$field_data' ";
				$sql .= ")";
				$db->exec(check_sql($sql));
				unset($sql);

				//synchronize the xml config
				sync_package_v_dialplan_includes();

				require_once "includes/header.php";
				echo "<meta http-equiv=\"refresh\" content=\"2;url=v_dialplan_includes_edit.php?id=".$dialplan_include_id."\">\n";
				echo "<div align='center'>\n";
				echo "Add Complete\n";
				echo "</div>\n";
				require_once "includes/footer.php";
				return;
			} //if ($action == "add")

			if ($action == "update" && permission_exists('time_conditions_edit')) {
				$sql = "update v_dialplan_includes_details set ";
				$sql .= "domain_uuid = '$domain_uuid', ";
				$sql .= "dialplan_include_id = '$dialplan_include_id', ";
				$sql .= "tag = '$tag', ";
				$sql .= "field_order = '$field_order', ";
				$sql .= "field_type = '$field_type', ";
				$sql .= "field_data = '$field_data' ";
				$sql .= "where domain_uuid = '$domain_uuid' ";
				$sql .= "and dialplan_includes_detail_id = '$dialplan_includes_detail_id'";
				$db->exec(check_sql($sql));
				unset($sql);

				//synchronize the xml config
				sync_package_v_dialplan_includes();

				require_once "includes/header.php";
				echo "<meta http-equiv=\"refresh\" content=\"2;url=v_dialplan_includes_edit.php?id=".$dialplan_include_id."\">\n";
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
	$dialplan_includes_detail_id = $_GET["id"];
	$sql = "";
	$sql .= "select * from v_dialplan_includes_details ";
	$sql .= "where domain_uuid = '$domain_uuid' ";
	$sql .= "and dialplan_includes_detail_id = '$dialplan_includes_detail_id' ";
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	foreach ($result as &$row) {
		$domain_uuid = $row["domain_uuid"];
		$dialplan_include_id = $row["dialplan_include_id"];
		$tag = $row["tag"];
		$field_order = $row["field_order"];
		$field_type = $row["field_type"];
		$field_data = $row["field_data"];
		break; //limit to 1 row
	}
	unset ($prepstatement);
}

//include the header
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
		echo "<td align='left' width='30%' nowrap><b>Time Condition Detail Add</b></td>\n";
	}
	if ($action == "update") {
		echo "<td align='left' width='30%' nowrap><b>Time Condition Detail Update</b></td>\n";
	}
	echo "<td width='70%' align='right'><input type='button' class='btn' name='' alt='back' onclick=\"window.location='v_dialplan_includes_edit.php?id=".$dialplan_include_id."'\" value='Back'></td>\n";
	echo "</tr>\n";

	?>
	<script type="text/javascript">
	function public_include_details_tag_onchange() {
		var tag = document.getElementById("form_tag").value;
		if (tag == "condition") {
		  document.getElementById("label_field_type").innerHTML = "Field";
		  document.getElementById("label_field_data").innerHTML = "Expression";
		}
		else if (tag == "action") {
		  document.getElementById("label_field_type").innerHTML = "Application";
		  document.getElementById("label_field_data").innerHTML = "Data";
		}
		else if (tag == "anti-action") {
		  document.getElementById("label_field_type").innerHTML = "Application";
		  document.getElementById("label_field_data").innerHTML = "Data";
		}
		else if (tag == "param") {
		  document.getElementById("label_field_type").innerHTML = "Name";
		  document.getElementById("label_field_data").innerHTML = "Value";
		}
		if (tag == "") {
		  document.getElementById("label_field_type").innerHTML = "Type";
		  document.getElementById("label_field_data").innerHTML = "Data";
		}
	}
	</script>
	<?php
	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Tag:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "                <select name='tag' class='formfld' id='form_tag' onchange='public_include_details_tag_onchange();'>\n";
	echo "                <option></option>\n";
	switch (htmlspecialchars($tag)) {
	case "condition":
		echo "                <option selected='yes'>condition</option>\n";
		echo "                <option>action</option>\n";
		echo "                <option>anti-action</option>\n";
		//echo "                <option>param</option>\n";
		break;
	case "action":
		echo "                <option>condition</option>\n";
		echo "                <option selected='yes'>action</option>\n";
		echo "                <option>anti-action</option>\n";
		//echo "                <option>param</option>\n";
		break;
	case "anti-action":
		echo "                <option>condition</option>\n";
		echo "                <option>action</option>\n";
		echo "                <option selected='yes'>anti-action</option>\n";
		//echo "                <option>param</option>\n";
		break;
	case "param":
		echo "                <option>condition</option>\n";
		echo "                <option>action</option>\n";
		echo "                <option>anti-action</option>\n";
		//echo "                <option selected='yes'>param</option>\n";
		break;
	default:
		echo "                <option>condition</option>\n";
		echo "                <option>action</option>\n";
		echo "                <option>anti-action</option>\n";
		//echo "                <option>param</option>\n";
	}
	echo "                </select>\n";

	//condition
		//field expression
	//action
		//application
		//data
	//antiaction
		//application
		//data
	//param
		//name
		//value
	//echo "    <input class='formfld' type='text' name='tag' maxlength='255' value=\"$tag\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Order:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "              <select name='field_order' class='formfld'>\n";
	//echo "              <option></option>\n";
	if (strlen(htmlspecialchars($field_order))> 0) {
		echo "              <option selected='yes' value='".htmlspecialchars($field_order)."'>".htmlspecialchars($field_order)."</option>\n";
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
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "    Type:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' type='text' name='field_type' maxlength='255' value=\"$field_type\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "    Data:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' type='text' name='field_data' maxlength='255' value=\"$field_data\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "	<tr>\n";
	echo "		<td colspan='2' align='right'>\n";
	echo "				<input type='hidden' name='dialplan_include_id' value='$dialplan_include_id'>\n";
	if ($action == "update") {
		echo "				<input type='hidden' name='dialplan_includes_detail_id' value='$dialplan_includes_detail_id'>\n";
	}
	echo "				<input type='submit' name='submit' class='btn' value='Save'>\n";
	echo "		</td>\n";
	echo "	</tr>";
	echo "</table>";
	echo "</div>\n";
	echo "</form>";

	echo "    <table width='100%' cellpadding='0' cellspacing='0'>\n";
	echo "    <tr>\n";
	echo "    <td align='left'>\n";

	if ($v_path_show) {
		echo "<br />\n";
		echo "<br />\n";
		echo "<b>Additional Information</b>\n";
		echo "<br />\n";
		echo "<br />\n";
		echo "<a href='http://wiki.freeswitch.org/wiki/Dialplan_XML' target='_blank'>http://wiki.freeswitch.org/wiki/Dialplan_XML</a>";
	}
	?>
	<br />
	<br />
	<br />
	<br />

	<b>Conditions</b>
	<br />
	<br />
	Conditions are pattern matching tags that help decide if the current call should be processed in this extension or not. When matching conditions against the current call you have several <b>fields</b> that you can compare against.
	<ul>
		<li><b>context</b></li>
		<li><b>username</b> Extension Number, Also known as the extension number.</li>
		<li><b>rdnis</b> Redirected Number, the directory number to which the call was last presented.</li>
		<li><b>destination_number</b> Called Number, the number this call is trying to reach (within a given context)</li>
		<li><b>dialplan</b> Name of the dialplan module that are used, the name is provided by each dialplan module. Example: XML</li>
		<li><b>caller_id_name</b> Name of the caller (provided by the User Agent that has called us).</li>
		<li><b>caller_id_number</b> Directory Number of the party who called (callee) -- can be masked (hidden)</li>
		<li><b>ani</b> Automatic Number Identification, the number of the calling party (callee) -- cannot be masked</li>
		<li><b>ani2</b> The type of device placing the call [1]</li>
		<li><b>uuid</b> Unique identifier of the current call? (looks like a GUID)</li>
		<li><b>source</b> Name of the module that received the call (e.g. PortAudio)</li>
		<li><b>chan_name</b> Name of the current channel (Example: PortAudio/1234). Give us examples when this one can be used.</li>
		<li><b>network_addr</b> IP address of the signalling source for a VoIP call.</li>
	</ul>
	In addition to the above you can also do variables using the syntax ${variable} or api functions using the syntax %{api} {args}
	<br />
	<br />
	Variables may be used in either the field or the expression, as follows
	<br />
	<br />
	<br />
	<br />

	<b>Action and Anti-Actions</b>
	<br />
	<br />
	Actions are executed when the <b>condition matches</b>. Anti-Actions are executed when the <b>condition does NOT match</b>.
	<?php
	if ($v_path_show) {
		  echo "Additional information on applications for Actions and Anti-Actions.<br />\n";
		  echo "<a href='http://wiki.freeswitch.org/wiki/Modules#Applications' target='_blank'>http://wiki.freeswitch.org/wiki/Modules#Applications</a>\n";
		  echo "<br />\n";
		  echo "<a href='http://wiki.freeswitch.org/wiki/Dialplan_Functions' target='_blank'>http://wiki.freeswitch.org/wiki/Dialplan_Functions</a>\n";
	  }
	  ?>
	  <br />
	  <br />
	  <br />
	  The following is a partial list of <b>applications</b>.
	  <ul>
	  <li><b>answer</b> answer the call</li>
	  <li><b>bridge</b> bridge the call</li>
	  <li><b>cond</b></li>
	  <li><b>db</b> is a a runtime database either sqlite by default or odbc</li>
	  <li><b>global_set</b> allows setting of global vars similar to the ones found in vars.xml</li>
	  <li><b>group</b> allows grouping of several extensions for things like ring groups</li>
	  <li><b>expr</b></li>
	  <li><b>hangup</b> hangs up the call</li>
	  <li><b>info</b> sends call info to the console</li>
	  <li><b>javascript</b> run javascript .js files</li>
	  <li><b>playback</b></li>
	  <li><b>reject</b> reject the call</li>
	  <li><b>respond</b></li>
	  <li><b>ring_ready</b></li>
	  <li><b>set</b> set a variable</li>
	  <li><b>set_user</b></li>
	  <li><b>sleep</b></li>
	  <li><b>sofia_contact</b></li>
	  <li><b>transfer</b> transfer the call to another extension or number</li>
	  <li><b>voicemail</b> send the call to voicemail</li>
	  </ul>
	  <br />
	  <br />

	  <!--
	  <b>Param</b>
	  Example parameters by name and value
	  <br />
	  <?php
	  if ($v_path_show) {
		echo "<a href='http://wiki.freeswitch.org/wiki/Special:Search?search=param&go=Go' target='_blank'>http://wiki.freeswitch.org/wiki/Special:Search?search=param&go=Go</a>\n";
	  }
	  ?>
	  <ul>
	  <li><b>codec-ms</b> 20</li>
	  <li><b>codec-prefs</b> PCMU@20i</li>
	  <li><b>debug</b> 1</li>
	  <li><b>dialplan</b> XML</li>
	  <li><b>dtmf-duration</b> 100</li>
	  <li><b>rfc2833-pt</b>" 101</li>
	  <li><b>sip-port</b> 5060</li>
	  <li><b>use-rtp-timer</b> true</li>
	  </ul>
	  <br />
	  <br />
	  -->

	  <br />
	  <br />
	  <br />
	  <br />
	  <br />

	</td>
	</tr>
	</table>

<?php
	echo "	</td>";
	echo "	</tr>";
	echo "</table>";
	echo "</div>";
	
//include the footer
	require_once "includes/footer.php";
?>
