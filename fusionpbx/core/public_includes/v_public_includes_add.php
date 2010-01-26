<?php
/* $Id$ */
/*
	v_public_includes_add.php
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
		$extension_name = check_str($_POST["extension_name"]);
		$publicorder = check_str($_POST["publicorder"]);
		$condition_field_1 = check_str($_POST["condition_field_1"]);
		$condition_expression_1 = check_str($_POST["condition_expression_1"]);
		$condition_field_2 = check_str($_POST["condition_field_2"]);
		$condition_expression_2 = check_str($_POST["condition_expression_2"]);
		$action_application_1 = check_str($_POST["action_application_1"]);
		$action_data_1 = check_str($_POST["action_data_1"]);
		$action_application_2 = check_str($_POST["action_application_2"]);
		$action_data_2 = check_str($_POST["action_data_2"]);
		$enabled = check_str($_POST["enabled"]);
		$description = check_str($_POST["description"]);
		if (strlen($enabled) == 0) { $enabled = "true"; } //set default to enabled
	}

if (count($_POST)>0 && strlen($_POST["persistformvar"]) == 0) {
	//check for all required data
		if (strlen($v_id) == 0) { $msg .= "Please provide: v_id<br>\n"; }
		if (strlen($extension_name) == 0) { $msg .= "Please provide: Extension Name<br>\n"; }
		if (strlen($condition_field_1) == 0) { $msg .= "Please provide: Condition Field<br>\n"; }
		if (strlen($condition_expression_1) == 0) { $msg .= "Please provide: Condition Expression<br>\n"; }
		if (strlen($action_application_1) == 0) { $msg .= "Please provide: Action Application<br>\n"; }
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

	//add the main public include entry
		$sql = "insert into v_public_includes ";
		$sql .= "(";
		$sql .= "v_id, ";
		$sql .= "extensionname, ";
		$sql .= "publicorder, ";
		$sql .= "context, ";
		$sql .= "enabled, ";
		$sql .= "descr ";
		$sql .= ") ";
		$sql .= "values ";
		$sql .= "(";
		$sql .= "'$v_id', ";
		$sql .= "'$extension_name', ";
		$sql .= "'$publicorder', ";
		$sql .= "'default', ";
		$sql .= "'$enabled', ";
		$sql .= "'$description' ";
		$sql .= ")";
		$db->exec(check_sql($sql));
		$public_include_id = $db->lastInsertId($id);
		unset($sql);

	//add condition public context
		$sql = "insert into v_public_includes_details ";
		$sql .= "(";
		$sql .= "v_id, ";
		$sql .= "public_include_id, ";
		$sql .= "tag, ";
		$sql .= "fieldtype, ";
		$sql .= "fielddata, ";
		$sql .= "fieldorder ";
		$sql .= ") ";
		$sql .= "values ";
		$sql .= "(";
		$sql .= "'$v_id', ";
		$sql .= "'$public_include_id', ";
		$sql .= "'condition', ";
		$sql .= "'context', ";
		$sql .= "'public', ";
		$sql .= "'0' ";
		$sql .= ")";
		$db->exec(check_sql($sql));
		//$lastinsertid = $db->lastInsertId($id);
		unset($sql);

	//add condition 1
		$sql = "insert into v_public_includes_details ";
		$sql .= "(";
		$sql .= "v_id, ";
		$sql .= "public_include_id, ";
		$sql .= "tag, ";
		$sql .= "fieldtype, ";
		$sql .= "fielddata, ";
		$sql .= "fieldorder ";
		$sql .= ") ";
		$sql .= "values ";
		$sql .= "(";
		$sql .= "'$v_id', ";
		$sql .= "'$public_include_id', ";
		$sql .= "'condition', ";
		$sql .= "'$condition_field_1', ";
		$sql .= "'$condition_expression_1', ";
		$sql .= "'1' ";
		$sql .= ")";
		$db->exec(check_sql($sql));
		//$lastinsertid = $db->lastInsertId($id);
		unset($sql);

	//add condition 2
		if (strlen($condition_field_2) > 0) {
			$sql = "insert into v_public_includes_details ";
			$sql .= "(";
			$sql .= "v_id, ";
			$sql .= "public_include_id, ";
			$sql .= "tag, ";
			$sql .= "fieldtype, ";
			$sql .= "fielddata, ";
			$sql .= "fieldorder ";
			$sql .= ") ";
			$sql .= "values ";
			$sql .= "(";
			$sql .= "'$v_id', ";
			$sql .= "'$public_include_id', ";
			$sql .= "'condition', ";
			$sql .= "'$condition_field_2', ";
			$sql .= "'$condition_expression_2', ";
			$sql .= "'2' ";
			$sql .= ")";
			$db->exec(check_sql($sql));
			//$lastinsertid = $db->lastInsertId($id);
			unset($sql);
		}

	//add action 1
		$sql = "insert into v_public_includes_details ";
		$sql .= "(";
		$sql .= "v_id, ";
		$sql .= "public_include_id, ";
		$sql .= "tag, ";
		$sql .= "fieldtype, ";
		$sql .= "fielddata, ";
		$sql .= "fieldorder ";
		$sql .= ") ";
		$sql .= "values ";
		$sql .= "(";
		$sql .= "'$v_id', ";
		$sql .= "'$public_include_id', ";
		$sql .= "'action', ";
		$sql .= "'$action_application_1', ";
		$sql .= "'$action_data_1', ";
		$sql .= "'3' ";
		$sql .= ")";
		$db->exec(check_sql($sql));
		//$lastinsertid = $db->lastInsertId($id);
		unset($sql);

	//add action 2
		if (strlen($action_application_2) > 0) {
			$sql = "insert into v_public_includes_details ";
			$sql .= "(";
			$sql .= "v_id, ";
			$sql .= "public_include_id, ";
			$sql .= "tag, ";
			$sql .= "fieldtype, ";
			$sql .= "fielddata, ";
			$sql .= "fieldorder ";
			$sql .= ") ";
			$sql .= "values ";
			$sql .= "(";
			$sql .= "'$v_id', ";
			$sql .= "'$public_include_id', ";
			$sql .= "'action', ";
			$sql .= "'$action_application_2', ";
			$sql .= "'$action_data_2', ";
			$sql .= "'4' ";
			$sql .= ")";
			$db->exec(check_sql($sql));
			//$lastinsertid = $db->lastInsertId($id);
			unset($sql);
		}

	//commit the atomic transaction
		$count = $db->exec("COMMIT;"); //returns affected rows

	//synchronize the xml config
		sync_package_v_public_includes();

	require_once "includes/header.php";
	echo "<meta http-equiv=\"refresh\" content=\"2;url=v_public_includes.php\">\n";
	echo "<div align='center'>\n";
	echo "Update Complete\n";
	echo "</div>\n";
	require_once "includes/footer.php";
	return;

} //end if (count($_POST)>0 && strlen($_POST["persistformvar"]) == 0)

?>

<script type="text/javascript">
<!--
function type_onchange(field_type) {
var field_value = document.getElementById(field_type).value;

//desc_action_data_1
//desc_action_data_2

if (field_type == "condition_field_1") {
	if (field_value == "destination_number") {
		document.getElementById("desc_condition_expression_1").innerHTML = "expression: ^12081231234$";
	}
	else if (field_value == "zzz") {
		document.getElementById("desc_condition_expression_1").innerHTML = "";
	}
	else {
		document.getElementById("desc_condition_expression_1").innerHTML = "";
	}
}
if (field_type == "condition_field_2") {
	if (field_value == "destination_number") {
		document.getElementById("desc_condition_expression_2").innerHTML = "expression: ^12081231234$";
	}
	else if (field_value == "zzz") {
		document.getElementById("desc_condition_expression_2").innerHTML = "";
	}
	else {
		document.getElementById("desc_condition_expression_2").innerHTML = "";
	}
}
if (field_type == "action_application_1") {
	if (field_value == "transfer") {
		document.getElementById("desc_action_data_1").innerHTML = "Transfer the call through the dialplan to the destination. data: 1001 XML default";
	}
	else if (field_value == "bridge") {
		var tmp = "Bridge the call to a destination. <br />";
		tmp += "sip uri (voicemail): sofia/internal/*98@${domain}<br />\n";
		tmp += "sip uri (external number): sofia/gateway/gatewayname/12081231234<br />\n";
		tmp += "sip uri (hunt group): sofia/internal/7002@${domain}<br />\n";
		tmp += "sip uri (auto attendant): sofia/internal/5002@${domain}<br />\n";
		//tmp += "sip uri (user): /user/1001@${domain}<br />\n";
		document.getElementById("desc_action_data_1").innerHTML = tmp;
	}
	else if (field_value == "global_set") {
		document.getElementById("desc_action_data_1").innerHTML = "Sets a global variable. data: var1=1234";
	}
	else if (field_value == "javascript") {
		document.getElementById("desc_action_data_1").innerHTML = "Direct the call to a javascript file. data: disa.js";
	}
	else if (field_value == "set") {
		document.getElementById("desc_action_data_1").innerHTML = "Sets a variable. data: var2=1234";
	}
	else if (field_value == "voicemail") {
		document.getElementById("desc_action_data_1").innerHTML = "Send the call to voicemail. data: default ${domain} 1001";
	}
	else {
		document.getElementById("desc_action_data_1").innerHTML = "";
	}
}
if (field_type == "action_application_2") {
	if (field_value == "transfer") {
		document.getElementById("desc_action_data_2").innerHTML = "Transfer the call through the dialplan to the destination. data: 1001 XML default";
	}
	else if (field_value == "bridge") {
		var tmp = "Bridge the call to a destination. <br />";
		tmp += "sip uri (voicemail): sofia/internal/*98@${domain}<br />\n";
		tmp += "sip uri (external number): sofia/gateway/gatewayname/12081231234<br />\n";
		tmp += "sip uri (hunt group): sofia/internal/7002@${domain}<br />\n";
		tmp += "sip uri (auto attendant): sofia/internal/5002@${domain}<br />\n";
		//tmp += "sip uri (user): /user/1001@${domain}<br />\n";
		document.getElementById("desc_action_data_2").innerHTML = tmp;
	}
	else if (field_value == "global_set") {
		document.getElementById("desc_action_data_2").innerHTML = "Sets a global variable. data: var1=1234";
	}
	else if (field_value == "javascript") {
		document.getElementById("desc_action_data_2").innerHTML = "Direct the call to a javascript file. data: disa.js";
	}
	else if (field_value == "set") {
		document.getElementById("desc_action_data_2").innerHTML = "Sets a variable. data: var2=1234";
	}
	else if (field_value == "voicemail") {
		document.getElementById("desc_action_data_2").innerHTML = "Send the call to voicemail. data: default ${domain} 1001";
	}
	else {
		document.getElementById("desc_action_data_2").innerHTML = "";
	}
}

}
-->
</script>

<?php
echo "<div align='center'>";
echo "<table width='100%' border='0' cellpadding='0' cellspacing='2'>\n";

echo "<tr class='border'>\n";
echo "	<td align=\"left\">\n";
echo "		<br>";

echo "<form method='post' name='frm' action=''>\n";
echo "<div align='center'>\n";

echo " 	<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";
echo "	<tr>\n";
echo "		<td align='left'><span class=\"vexpl\"><span class=\"red\"><strong>Inbound Call Routing\n";
echo "			</strong></span></span>\n";
echo "		</td>\n";
echo "		<td align='right'>\n";
echo "			<input type='button' class='btn' name='' alt='back' onclick=\"window.location='v_public_includes.php'\" value='Back'>\n";
echo "		</td>\n";
echo "	</tr>\n";
echo "	<tr>\n";
echo "		<td align='left' colspan='2'>\n";
echo "			<span class=\"vexpl\">\n";
echo "			The public dialplan is used to route incoming calls to destinations based on one or more conditions and context. It can send incoming calls to an auto attendant, huntgroup, extension, external number, or a script.\n";
echo "		</span>\n";
echo "		<br />\n";
echo "			</span>\n";
echo "		</td>\n";
echo "	</tr>\n";
echo "	</table>";

echo "<br />\n";
echo "<br />\n";

echo "<table width='100%'  border='0' cellpadding='6' cellspacing='0'>\n";

echo "<tr>\n";
echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
echo "    Name:\n";
echo "</td>\n";
echo "<td class='vtable' align='left'>\n";
echo "    <input class='formfld' style='width: 60%;' type='text' name='extension_name' maxlength='255' value=\"$extension_name\">\n";
echo "<br />\n";
echo "\n";
echo "</td>\n";
echo "</tr>\n";

//echo "<tr>\n";
//echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
//echo "    Continue:\n";
//echo "</td>\n";
//echo "<td class='vtable' align='left'>\n";
//echo "    <select class='formfld' name='extensioncontinue' style='width: 60%;'>\n";
//echo "    <option value=''></option>\n";
//if ($extensioncontinue == "true") { 
//	echo "    <option value='true' SELECTED >true</option>\n";
//}
//else {
//	echo "    <option value='true'>true</option>\n";
//}
//if ($extensioncontinue == "false") { 
//	echo "    <option value='false' SELECTED >false</option>\n";
//}
//else {
//	echo "    <option value='false'>false</option>\n";
//}
//echo "    </select>\n";
//echo "<br />\n";
//echo "Extension Continue in most cases this is false. default: false\n";
//echo "</td>\n";
//echo "</tr>\n";

echo "<tr>\n";
echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
echo "	Condition 1:\n";
echo "</td>\n";
echo "<td class='vtable' align='left'>\n";
echo "	<table style='width: 60%;' border='0'>\n";
echo "	<tr>\n";
echo "	<td style='width: 62px;'>Field</td>\n";
echo "	<td style='width: 35%;'>\n";
echo "    <select class='formfld' name='condition_field_1' id='condition_field_1' onchange='type_onchange(\"condition_field_1\");' style='width:100%'>\n";
echo "    <option value=''></option>\n";
if (strlen($condition_field_1) > 0) {
	echo "    <option value='$condition_field_1' selected>$condition_field_1</option>\n";
}
echo "    <option value='context'>context</option>\n";
echo "    <option value='rdnis'>rdnis</option>\n";
echo "    <option value='destination_number'>destination_number</option>\n";
echo "    <option value='public'>public</option>\n";
echo "    <option value='caller_id_name'>caller_id_name</option>\n";
echo "    <option value='caller_id_number'>caller_id_number</option>\n";
echo "    <option value='ani'>ani</option>\n";
echo "    <option value='ani2'>ani2</option>\n";
echo "    <option value='uuid'>uuid</option>\n";
echo "    <option value='source'>source</option>\n";
echo "    <option value='chan_name'>chan_name</option>\n";
echo "    <option value='network_addr'>network_addr</option>\n";
echo "    </select><br />\n";
echo "	</td>\n";
echo "	<td style='width: 73px;'>&nbsp; Expression</td>\n";
echo "	<td>\n";
echo "		<input class='formfld' type='text' name='condition_expression_1' maxlength='255' style='width:100%' value=\"$condition_expression_1\">\n";
echo "	</td>\n";
echo "	</tr>\n";
echo "	</table>\n";
echo "	<div id='desc_condition_expression_1'></div>\n";
echo "</td>\n";
echo "</tr>\n";

echo "<tr>\n";
echo "<td class='vncell' valign='top' align='left' nowrap>\n";
echo "	Condition 2\n";
echo "</td>\n";
echo "<td class='vtable' align='left'>\n";

echo "	<table style='width: 60%;' border='0'>\n";
echo "	<tr>\n";
echo "	<td align='left' style='width: 62px;'>\n";
echo "		Field:\n";
echo "	</td>\n";
echo "	<td style='width: 35%;' align='left'>\n";
echo "    <select class='formfld' name='condition_field_2' id='condition_field_2' onchange='type_onchange(\"condition_field_2\");' style='width:100%'>\n";
echo "    <option value=''></option>\n";
if (strlen($condition_field_2) > 0) {
	echo "    <option value='$condition_field_2' selected>$condition_field_2</option>\n";
}
echo "    <option value='context'>context</option>\n";
echo "    <option value='rdnis'>rdnis</option>\n";
echo "    <option value='destination_number'>destination_number</option>\n";
echo "    <option value='public'>public</option>\n";
echo "    <option value='caller_id_name'>caller_id_name</option>\n";
echo "    <option value='caller_id_number'>caller_id_number</option>\n";
echo "    <option value='ani'>ani</option>\n";
echo "    <option value='ani2'>ani2</option>\n";
echo "    <option value='uuid'>uuid</option>\n";
echo "    <option value='source'>source</option>\n";
echo "    <option value='chan_name'>chan_name</option>\n";
echo "    <option value='network_addr'>network_addr</option>\n";
echo "    </select><br />\n";
echo "	</td>\n";
echo "	<td style='width: 73px;' align='left'>\n";
echo "		&nbsp; Expression:\n";
echo "	</td>\n";
echo "	<td>\n";
echo "		<input class='formfld' type='text' name='condition_expression_2' maxlength='255' style='width:100%' value=\"$condition_expression_2\">\n";
echo "	</td>\n";
echo "	</tr>\n";
echo "	</table>\n";
echo "	<div id='desc_condition_expression_2'></div>\n";
echo "</td>\n";
echo "</tr>\n";

echo "<tr>\n";
echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
echo "    Action 1:\n";
echo "</td>\n";
echo "<td class='vtable' align='left'>\n";
echo "	<table style='width: 60%;' border='0' >\n";
echo "	<tr>\n";
echo "	<td style='width: 62px;'>Application: </td>\n";
echo "	<td style='width: 35%;'>\n";
echo "    <select class='formfld' style='width:100%' id='action_application_1' name='action_application_1' onchange='type_onchange(\"action_application_1\");'>\n";
echo "    <option value=''></option>\n";
if (strlen($action_application_1) > 0) {
	echo "    <option value='$action_application_1' selected>$action_application_1</option>\n";
}
echo "    <option value='answer'>answer</option>\n";
echo "    <option value='bridge'>bridge</option>\n";
echo "    <option value='cond'>cond</option>\n";
echo "    <option value='db'>db</option>\n";
echo "    <option value='global_set'>global_set</option>\n";
echo "    <option value='group'>group</option>\n";
echo "    <option value='expr'>expr</option>\n";
echo "    <option value='hangup'>hangup</option>\n";
echo "    <option value='info'>info</option>\n";
echo "    <option value='javascript'>javascript</option>\n";
echo "    <option value='reject'>reject</option>\n";
echo "    <option value='playback'>playback</option>\n";
echo "    <option value='reject'>reject</option>\n";
echo "    <option value='respond'>respond</option>\n";
echo "    <option value='ring_ready'>ring_ready</option>\n";
echo "    <option value='set'>set</option>\n";
echo "    <option value='set_user'>set_user</option>\n";
echo "    <option value='sleep'>sleep</option>\n";
echo "    <option value='sofia_contact'>sofia_contact</option>\n";
echo "    <option value='transfer'>transfer</option>\n";
echo "    <option value='voicemail'>voicemail</option>\n";
echo "    </select><br />\n";
echo "	</td>\n";
echo "	<td style='width: 73px;'>\n";
echo "		&nbsp; Data: \n";
echo "	</td>\n";
echo "	<td>\n";
echo "		<input class='formfld' style='width: 100%;' type='text' name='action_data_1' maxlength='255' value=\"$action_data_1\">\n";
echo "	</td>\n";
echo "	</tr>\n";
echo "	</table>\n";
echo "	<div id='desc_action_data_1'></div>\n";
echo "</td>\n";
echo "</tr>\n";

echo "</td>\n";
echo "</tr>\n";

echo "<tr>\n";
echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
echo "    Action 2:\n";
echo "</td>\n";
echo "<td class='vtable' align='left'>\n";
echo "	<table style='width: 60%;' border='0' >\n";
echo "	<tr>\n";
echo "	<td style='width: 62px;'>Application: </td>\n";
echo "	<td style='width: 35%;'>\n";
echo "    <select class='formfld' style='width:100%' id='action_application_2' name='action_application_2' onchange='type_onchange(\"action_application_2\");'>\n";
echo "    <option value=''></option>\n";
if (strlen($action_application_2) > 0) {
	echo "    <option value='$action_application_2' selected>$action_application_2</option>\n";
}
echo "    <option value='answer'>answer</option>\n";
echo "    <option value='bridge'>bridge</option>\n";
echo "    <option value='cond'>cond</option>\n";
echo "    <option value='db'>db</option>\n";
echo "    <option value='global_set'>global_set</option>\n";
echo "    <option value='group'>group</option>\n";
echo "    <option value='expr'>expr</option>\n";
echo "    <option value='hangup'>hangup</option>\n";
echo "    <option value='info'>info</option>\n";
echo "    <option value='javascript'>javascript</option>\n";
echo "    <option value='reject'>reject</option>\n";
echo "    <option value='playback'>playback</option>\n";
echo "    <option value='reject'>reject</option>\n";
echo "    <option value='respond'>respond</option>\n";
echo "    <option value='ring_ready'>ring_ready</option>\n";
echo "    <option value='set'>set</option>\n";
echo "    <option value='set_user'>set_user</option>\n";
echo "    <option value='sleep'>sleep</option>\n";
echo "    <option value='sofia_contact'>sofia_contact</option>\n";
echo "    <option value='transfer'>transfer</option>\n";
echo "    <option value='voicemail'>voicemail</option>\n";
echo "    </select><br />\n";
echo "	</td>\n";
echo "	<td style='width: 73px;'>\n";
echo "		&nbsp; Data: \n";
echo "	</td>\n";
echo "	<td>\n";
echo "		<input class='formfld' style='width: 100%;' type='text' name='action_data_2' maxlength='255' value=\"$action_data_2\">\n";
echo "	</td>\n";
echo "	</tr>\n";
echo "	</table>\n";
echo "	<div id='desc_action_data_2'></div>\n";
echo "</td>\n";
echo "</tr>\n";

echo "<tr>\n";
echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
echo "    Order:\n";
echo "</td>\n";
echo "<td class='vtable' align='left'>\n";
echo "              <select name='publicorder' class='formfld' style='width: 60%;'>\n";
//echo "              <option></option>\n";
if (strlen(htmlspecialchars($publicorder))> 0) {
	echo "              <option selected='yes' value='".htmlspecialchars($publicorder)."'>".htmlspecialchars($publicorder)."</option>\n";
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
echo "</div>";
echo "</form>";

echo "</td>\n";
echo "</tr>";
echo "</table>";
echo "</div>";

echo "<br><br>";


require_once "includes/footer.php";
unset ($resultcount);
unset ($result);
unset ($key);
unset ($val);
unset ($c);
?>
