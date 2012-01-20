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
	Portions created by the Initial Developer are Copyright (C) 2008-2012
	the Initial Developer. All Rights Reserved.

	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>
*/
include "root.php";
require_once "includes/config.php";
require_once "includes/checkauth.php";
if (permission_exists('public_includes_view')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}
require_once "includes/header.php";
require_once "includes/paging.php";

$order_by = $_GET["order_by"];
$order = $_GET["order"];

//-------------------------------------------------------------------------------------------
// Shortcut tool to add inbound routes (public include xml entries)
//-------------------------------------------------------------------------------------------

	//set http values to variables
		if (count($_POST)>0) {
			$extension_name = check_str($_POST["extension_name"]);
			$condition_field_1 = check_str($_POST["condition_field_1"]);
			$condition_expression_1 = check_str($_POST["condition_expression_1"]);
			$condition_field_2 = check_str($_POST["condition_field_2"]);
			$condition_expression_2 = check_str($_POST["condition_expression_2"]);
			$action_application_1 = check_str($_POST["action_application_1"]);
			$action_data_1 = check_str($_POST["action_data_1"]);
			$action_application_2 = check_str($_POST["action_application_2"]);
			$action_data_2 = check_str($_POST["action_data_2"]);
			$description = check_str($_POST["description"]);
		}

	if (count($_POST)>0 && strlen($_POST["persistformvar"]) == 0 && permission_exists('public_includes_add')) {
		//check for all required data
			if (strlen($domain_uuid) == 0) { $msg .= "Please provide: domain_uuid<br>\n"; }
			if (strlen($extension_name) == 0) { $msg .= "Please provide: Extension Name<br>\n"; }
			if (strlen($condition_field_1) == 0) { $msg .= "Please provide: Condition Field<br>\n"; }
			if (strlen($condition_expression_1) == 0) { $msg .= "Please provide: Condition Expression<br>\n"; }
			if (strlen($action_application_1) == 0) { $msg .= "Please provide: Action Application<br>\n"; }
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
			$public_uuid = uuid();
			$sql = "insert into v_public ";
			$sql .= "(";
			$sql .= "domain_uuid, ";
			$sql .= "public_uuid, ";
			$sql .= "extension_name, ";
			$sql .= "public_order, ";
			$sql .= "context, ";
			$sql .= "enabled, ";
			$sql .= "descr ";
			$sql .= ") ";
			$sql .= "values ";
			$sql .= "(";
			$sql .= "'$domain_uuid', ";
			$sql .= "'$public_uuid', ";
			$sql .= "'$extension_name', ";
			$sql .= "'0', ";
			$sql .= "'default', ";
			$sql .= "'true', ";
			$sql .= "'$description' ";
			$sql .= ")";
			$db->exec(check_sql($sql));
			unset($sql);

		//add condition public context
			$public_detail_uuid = uuid();
			$sql = "insert into v_public_details ";
			$sql .= "(";
			$sql .= "domain_uuid, ";
			$sql .= "public_uuid, ";
			$sql .= "public_detail_uuid, ";
			$sql .= "tag, ";
			$sql .= "field_type, ";
			$sql .= "field_data, ";
			$sql .= "field_order ";
			$sql .= ") ";
			$sql .= "values ";
			$sql .= "(";
			$sql .= "'$domain_uuid', ";
			$sql .= "'$public_uuid', ";
			$sql .= "'$public_detail_uuid', ";
			$sql .= "'condition', ";
			$sql .= "'context', ";
			$sql .= "'public', ";
			$sql .= "'0' ";
			$sql .= ")";
			$db->exec(check_sql($sql));
			unset($sql);

		//add condition 1
			$public_detail_uuid = uuid();
			$sql = "insert into v_public_details ";
			$sql .= "(";
			$sql .= "domain_uuid, ";
			$sql .= "public_uuid, ";
			$sql .= "public_detail_uuid, ";
			$sql .= "tag, ";
			$sql .= "field_type, ";
			$sql .= "field_data, ";
			$sql .= "field_order ";
			$sql .= ") ";
			$sql .= "values ";
			$sql .= "(";
			$sql .= "'$domain_uuid', ";
			$sql .= "'$public_uuid', ";
			$sql .= "'$public_detail_uuid', ";
			$sql .= "'condition', ";
			$sql .= "'$condition_field_1', ";
			$sql .= "'$condition_expression_1', ";
			$sql .= "'1' ";
			$sql .= ")";
			$db->exec(check_sql($sql));
			unset($sql);

		//add condition 2
			if (strlen($condition_field_2) > 0) {
				$public_detail_uuid = uuid();
				$sql = "insert into v_public_details ";
				$sql .= "(";
				$sql .= "domain_uuid, ";
				$sql .= "public_uuid, ";
				$sql .= "public_detail_uuid, ";
				$sql .= "tag, ";
				$sql .= "field_type, ";
				$sql .= "field_data, ";
				$sql .= "field_order ";
				$sql .= ") ";
				$sql .= "values ";
				$sql .= "(";
				$sql .= "'$domain_uuid', ";
				$sql .= "'$public_uuid', ";
				$sql .= "'$public_detail_uuid', ";
				$sql .= "'condition', ";
				$sql .= "'$condition_field_2', ";
				$sql .= "'$condition_expression_2', ";
				$sql .= "'2' ";
				$sql .= ")";
				$db->exec(check_sql($sql));
				unset($sql);
			}

		//add action 1
			$sql = "insert into v_public_details ";
			$sql .= "(";
			$sql .= "domain_uuid, ";
			$sql .= "public_uuid, ";
			$sql .= "public_detail_uuid, ";
			$sql .= "tag, ";
			$sql .= "field_type, ";
			$sql .= "field_data, ";
			$sql .= "field_order ";
			$sql .= ") ";
			$sql .= "values ";
			$sql .= "(";
			$sql .= "'$domain_uuid', ";
			$sql .= "'$public_uuid', ";
			$sql .= "'$public_detail_uuid', ";
			$sql .= "'action', ";
			$sql .= "'$action_application_1', ";
			$sql .= "'$action_data_1', ";
			$sql .= "'3' ";
			$sql .= ")";
			$db->exec(check_sql($sql));
			unset($sql);

		//add action 2
			if (strlen($action_application_2) > 0) {
				$sql = "insert into v_public_details ";
				$sql .= "(";
				$sql .= "domain_uuid, ";
				$sql .= "public_uuid, ";
				$sql .= "public_detail_uuid, ";
				$sql .= "tag, ";
				$sql .= "field_type, ";
				$sql .= "field_data, ";
				$sql .= "field_order ";
				$sql .= ") ";
				$sql .= "values ";
				$sql .= "(";
				$sql .= "'$domain_uuid', ";
				$sql .= "'$public_uuid', ";
				$sql .= "'$public_detail_uuid', ";
				$sql .= "'action', ";
				$sql .= "'$action_application_2', ";
				$sql .= "'$action_data_2', ";
				$sql .= "'4' ";
				$sql .= ")";
				$db->exec(check_sql($sql));
				unset($sql);
			}

		//commit the atomic transaction
			$count = $db->exec("COMMIT;"); //returns affected rows

		//synchronize the xml config
			sync_package_v_public();

		require_once "includes/header.php";
		echo "<meta http-equiv=\"refresh\" content=\"2;url=v_public.php\">\n";
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
	echo "<td align=\"center\">\n";
	echo "<br>";

	echo "<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";
	echo "	<tr>\n";
	echo "	<td align='left'><span class=\"vexpl\"><span class=\"red\"><strong>Inbound Call Routing\n";
	echo "		</strong></span></span>\n";
	echo "	</td>\n";
	echo "	<td width='70%' align='right'>";
	//echo "		<input type='button' class='btn' name='' alt='back' onclick=\"window.location='v_public.php'\" value='Back'>\n";
	echo "	</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "	<td align='left' colspan='2'>\n";
	echo "		<span class=\"vexpl\">\n";
	echo "			The public dialplan is used to route incoming calls to destinations based on one or more conditions and context. It can send incoming calls to an auto attendant, huntgroup, extension, external number, or a script.\n";
	echo "			Order is important when an anti-action is used or when there are multiple conditions that match.\n";
	echo "		</span>\n";
	echo "	</td>\n";
	echo "	</tr>\n";
	echo "</table>";

	echo "<br />\n";
	echo "<br />\n";

	$sql = "";
	$sql .= " select * from v_public ";
	$sql .= "where domain_uuid = '$domain_uuid' ";
	if (strlen($order_by)> 0) { $sql .= "order by $order_by $order "; } else { $sql .= "order by public_order asc, extension_name asc "; }
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$result = $prep_statement->fetchAll();
	$num_rows = count($result);
	unset ($prep_statement, $result, $sql);

	$rows_per_page = 150;
	$param = "";
	$page = $_GET['page'];
	if (strlen($page) == 0) { $page = 0; $_GET['page'] = 0; } 
	list($paging_controls, $rows_per_page, $var_3) = paging($num_rows, $param, $rows_per_page); 
	$offset = $rows_per_page * $page; 

	$sql = "";
	$sql .= " select * from v_public ";
	$sql .= " where domain_uuid = '$domain_uuid' ";
	if (strlen($order_by)> 0) { $sql .= "order by $order_by $order "; } else { $sql .= "order by public_order asc, extension_name asc "; }
	$sql .= " limit $rows_per_page offset $offset ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$result = $prep_statement->fetchAll();
	$result_count = count($result);
	unset ($prep_statement, $sql);

	$c = 0;
	$row_style["0"] = "row_style0";
	$row_style["1"] = "row_style1";

	echo "<div align='center'>\n";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";

	echo "<tr>\n";
	echo thorder_by('extension_name', 'Extension Name', $order_by, $order);
	echo thorder_by('public_order', 'Order', $order_by, $order);
	echo thorder_by('enabled', 'Enabled', $order_by, $order);
	echo thorder_by('descr', 'Description', $order_by, $order);
	if (ifgroup("superadmin")) {
		echo "<td align='right' width='42'>\n";
	}
	else {
		echo "<td align='right' width='21'>\n";
	}
	if (permission_exists('public_includes_view')) {
		echo "	<a href='v_public_add.php' alt='add'>$v_link_label_add</a>\n";
	}
	echo "</td>\n";
	echo "<tr>\n";

	if ($result_count > 0) {
		foreach($result as $row) {
			echo "<tr >\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>&nbsp;&nbsp;".$row[extension_name]."</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>&nbsp;&nbsp;".$row[public_order]."</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>&nbsp;&nbsp;".$row[enabled]."</td>\n";
			echo "	<td valign='top' class='row_stylebg' width='35%'>&nbsp;&nbsp;".$row[descr]."</td>\n";
			echo "	<td valign='top' align='right'>\n";
			if (permission_exists('public_includes_view')) {
				echo "		<a href='v_public_edit.php?id=".$row[public_uuid]."' alt='edit'>$v_link_label_edit</a>\n";
			}
			if (permission_exists('public_includes_view')) {
				echo "		<a href='v_public_delete.php?id=".$row[public_uuid]."' alt='delete' onclick=\"return confirm('Do you really want to delete this?')\">$v_link_label_delete</a>\n";
			}
			echo "	</td>\n";
			echo "</tr>\n";
			if ($c==0) { $c=1; } else { $c=0; }
		}
		unset($sql, $result, $row_count);
	} //end if results

	echo "<tr>\n";
	echo "<td colspan='5'>\n";
	echo "	<table width='100%' cellpadding='0' cellspacing='0'>\n";
	echo "	<tr>\n";
	echo "		<td width='33.3%' nowrap>&nbsp;</td>\n";
	echo "		<td width='33.3%' align='center' nowrap>$paging_controls</td>\n";
	echo "		<td width='33.3%' align='right'>\n";
	if (permission_exists('public_includes_view')) {
		echo "			<a href='v_public_add.php' alt='add'>$v_link_label_add</a>\n";
	}
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "	</table>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td colspan='5' align='left'>\n";
	echo "<br />\n";
	if ($v_path_show) {
		echo "<b>location:</b> ".$v_dialplan_public_dir."\n";
	}
	echo "</td>\n";
	echo "</tr>\n";

	echo "</table>";
	echo "</div>";
	echo "<br><br>";
	echo "<br><br>";

	echo "</td>";
	echo "</tr>";
	echo "</table>";
	echo "</div>";
	echo "<br><br>";

//include the footer
	require_once "includes/footer.php";
?>