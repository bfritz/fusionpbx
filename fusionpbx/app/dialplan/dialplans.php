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
require_once "resources/require.php";
require_once "resources/check_auth.php";
if (permission_exists('dialplan_view')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

//add multi-lingual support
	require_once "app_languages.php";
	foreach($text as $key => $value) {
		$text[$key] = $value[$_SESSION['domain']['language']['code']];
	}

//set the http values as php variables
	$search = check_str($_REQUEST["search"]);
	$order_by = check_str($_REQUEST["order_by"]);
	$order = check_str($_REQUEST["order"]);
	$dialplan_context = check_str($_REQUEST["dialplan_context"]);
	$app_uuid = check_str($_REQUEST["app_uuid"]);

//includes
	require_once "resources/header.php";
	require_once "resources/paging.php";
	if ($app_uuid == "c03b422e-13a8-bd1b-e42b-b6b9b4d27ce4") {
		$document['title'] = $text['title-inbound_routes'];
	}
	elseif ($app_uuid == "8c914ec3-9fc0-8ab5-4cda-6c9288bdc9a3") {
		$document['title'] = $text['title-outbound_routes'];
	}
	elseif ($app_uuid == "16589224-c876-aeb3-f59f-523a1c0801f7") {
		$document['title'] = $text['title-queues'];
	}
	elseif ($app_uuid == "4b821450-926b-175a-af93-a03c441818b1") {
		$document['title'] = $text['title-time_conditions'];
	}
	else {
		$document['title'] = $text['title-dialplan_manager'];
	}

//show the content
	echo "<div align='center'>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='2'>\n";
	echo "<tr class='border'>\n";
	echo "<td align=\"center\">\n";
	echo "<br />";

	echo "	<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"3\">\n";
	echo "	<tr>\n";
	echo "	<td align='left'>\n";
	echo "		<span class=\"title\">\n";
	if ($app_uuid == "c03b422e-13a8-bd1b-e42b-b6b9b4d27ce4") {
		echo "			".$text['header-inbound_routes']."\n";
	}
	elseif ($app_uuid == "8c914ec3-9fc0-8ab5-4cda-6c9288bdc9a3") {
		echo "			".$text['header-outbound_routes']."\n";
	}
	elseif ($app_uuid == "16589224-c876-aeb3-f59f-523a1c0801f7") {
		echo "			".$text['header-queues']."\n";
	}
	elseif ($app_uuid == "4b821450-926b-175a-af93-a03c441818b1") {
		echo "			".$text['header-time_conditions']."\n";
	}
	else {
		echo "			".$text['header-dialplan_manager']."\n";
	}
	echo "		</span>\n";
	echo "	</td>\n";

	echo "	<form name='frm_search' method='get' action=''>\n";
	echo "	<td width='50%' align='right'>\n";
	echo "		<input type='text' class='txt' style='width: 150px' name='search' value='$search'>";
	if (strlen($app_uuid) > 0) {
		echo "		<input type='hidden' class='txt' name='app_uuid' value='$app_uuid'>";
	}
	if (strlen($order_by) > 0) {
		echo "		<input type='hidden' class='txt' name='order' value='$order_by'>";
		echo "		<input type='hidden' class='txt' name='order' value='$order'>";
	}
	echo "		<input type='submit' class='btn' name='submit' value='".$text['button-search']."'>";
	echo "	</td>\n";
	echo "	</form>\n";

	//echo "	<td align='right'>\n";
	//if (permission_exists('dialplan_advanced_view') && strlen($app_uuid) == 0) {
	//	echo "		<input type='button' class='btn' value='".$text['button-advanced']."' onclick=\"document.location.href='dialplan_advanced.php';\">\n";
	//}
	//else {
	//	echo "&nbsp;\n";
	//}
	//echo "	</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "	<td align='left' colspan='2'>\n";
	echo "		<span class=\"vexpl\">\n";

	if ($app_uuid == "c03b422e-13a8-bd1b-e42b-b6b9b4d27ce4") {
		echo $text['description-inbound_routes'];
	}
	elseif ($app_uuid == "8c914ec3-9fc0-8ab5-4cda-6c9288bdc9a3") {
		echo $text['description-outbound_routes'];
	}
	elseif ($app_uuid == "16589224-c876-aeb3-f59f-523a1c0801f7") {
		echo $text['description-queues'];
	}
	elseif ($app_uuid == "4b821450-926b-175a-af93-a03c441818b1") {
		echo $text['description-time_conditions'];
	}
	else {
		if (if_group("superadmin")) {
			echo $text['description-dialplan_manager-superadmin'];
		}
		else {
			echo $text['description-dialplan_manager'];
		}
	}
	echo "		</span>\n";
	echo "	</td>\n";
	echo "	</tr>\n";
	echo "	</table>";
	echo "	<br />";

	//get the number of rows in the dialplan
	$sql = "select count(*) as num_rows from v_dialplans ";
	$sql .= "where (domain_uuid = '$domain_uuid' or domain_uuid is null) ";
	if (strlen($app_uuid) == 0) {
		//hide inbound routes
			$sql .= "and app_uuid <> 'c03b422e-13a8-bd1b-e42b-b6b9b4d27ce4' ";
		//hide outbound routes
			$sql .= "and app_uuid <> '8c914ec3-9fc0-8ab5-4cda-6c9288bdc9a3' ";
	}
	else {
		$sql .= "and app_uuid = '".$app_uuid."' ";
	}
	if (strlen($search) > 0) {
		$sql .= "and (";
		$sql .= " 	dialplan_context like '%".$search."%' ";
		$sql .= " 	or dialplan_name like '%".$search."%' ";
		$sql .= " 	or dialplan_number like '%".$search."%' ";
		$sql .= " 	or dialplan_continue like '%".$search."%' ";
		if (is_numeric($search)) {
			$sql .= " 	or dialplan_order = '".$search."' ";
		}
		$sql .= " 	or dialplan_enabled like '%".$search."%' ";
		$sql .= " 	or dialplan_description like '%".$search."%' ";
		$sql .= ") ";
	}
	$prep_statement = $db->prepare(check_sql($sql));
	if ($prep_statement) {
		$prep_statement->execute();
		$row = $prep_statement->fetch(PDO::FETCH_ASSOC);
		if ($row['num_rows'] > 0) {
			$num_rows = $row['num_rows'];
		}
		else {
			$num_rows = '0';
		}
	}
	unset($prep_statement, $result);

	$rows_per_page = 150;
	$param = "";
	if (strlen($app_uuid) > 0) { $param = "&app_uuid=".$app_uuid; }
	$page = $_GET['page'];
	if (strlen($page) == 0) { $page = 0; $_GET['page'] = 0; }
	list($paging_controls, $rows_per_page, $var_3) = paging($num_rows, $param, $rows_per_page);
	$offset = $rows_per_page * $page;

	$sql = "select * from v_dialplans ";
	$sql .= "where (domain_uuid = '$domain_uuid' or domain_uuid is null) ";
	if (strlen($app_uuid) == 0) {
		//hide inbound routes
			$sql .= "and app_uuid <> 'c03b422e-13a8-bd1b-e42b-b6b9b4d27ce4' ";
		//hide outbound routes
			$sql .= "and app_uuid <> '8c914ec3-9fc0-8ab5-4cda-6c9288bdc9a3' ";
	}
	else {
		$sql .= "and app_uuid = '".$app_uuid."' ";
	}
	if (strlen($search) > 0) {
		$sql .= "and (";
		$sql .= " 	dialplan_context like '%".$search."%' ";
		$sql .= " 	or dialplan_name like '%".$search."%' ";
		$sql .= " 	or dialplan_number like '%".$search."%' ";
		$sql .= " 	or dialplan_continue like '%".$search."%' ";
		if (is_numeric($search)) {
			$sql .= " 	or dialplan_order = '".$search."' ";
		}
		$sql .= " 	or dialplan_enabled like '%".$search."%' ";
		$sql .= " 	or dialplan_description like '%".$search."%' ";
		$sql .= ") ";
	}
	if (strlen($order_by)> 0) { $sql .= "order by $order_by $order "; } else { $sql .= "order by dialplan_order asc, dialplan_name asc "; }
	$sql .= " limit $rows_per_page offset $offset ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
	$result_count = count($result);
	unset ($prep_statement, $sql);

	$c = 0;
	$row_style["0"] = "row_style0";
	$row_style["1"] = "row_style1";

	echo "<div align='center'>\n";
	echo "<form name='frm_delete' method='post' action='dialplan_delete.php'>\n";
	echo "<input type='hidden' name='app_uuid' value='".$app_uuid."'>\n";
	echo "<table class='tr_hover' width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
	echo "<tr>\n";
	if (permission_exists('dialplan_delete') && $result_count > 0) {
		echo "<th style='text-align: center;' style='text-align: center; padding: 3px 0px 0px 0px;'><input type='checkbox' onchange=\"(this.checked) ? check('all') : check('none');\"></th>";
	}
	echo th_order_by('dialplan_name', $text['label-name'], $order_by, $order, $app_uuid);
	echo th_order_by('dialplan_number', $text['label-number'], $order_by, $order, $app_uuid);
	echo th_order_by('dialplan_context', $text['label-context'], $order_by, $order, $app_uuid);
	echo th_order_by('dialplan_order', $text['label-order'], $order_by, $order, $app_uuid, "style='text-align: center;'");
	echo th_order_by('dialplan_enabled', $text['label-enabled'], $order_by, $order, $app_uuid, "style='text-align: center;'");
	echo th_order_by('dialplan_description', $text['label-description'], $order_by, $order, $app_uuid);
	echo "<td class='list_control_icons'>";
	if ($app_uuid == "c03b422e-13a8-bd1b-e42b-b6b9b4d27ce4" && permission_exists('inbound_route_add')) {
		echo "<a href='".PROJECT_PATH."/app/dialplan_inbound/dialplan_inbound_add.php' alt='".$text['button-add']."'>$v_link_label_add</a>";
	}
	elseif ($app_uuid == "8c914ec3-9fc0-8ab5-4cda-6c9288bdc9a3" && permission_exists('outbound_route_add')) {
		echo "<a href='".PROJECT_PATH."/app/dialplan_outbound/dialplan_outbound_add.php' alt='".$text['button-add']."'>$v_link_label_add</a>";
	}
	elseif ($app_uuid == "16589224-c876-aeb3-f59f-523a1c0801f7" && permission_exists('fifo_add')) {
		echo "<a href='".PROJECT_PATH."/app/fifo/fifo_add.php' alt='".$text['button-add']."'>$v_link_label_add</a>";
	}
	elseif ($app_uuid == "4b821450-926b-175a-af93-a03c441818b1" && permission_exists('time_condition_add')) {
		echo "<a href='".PROJECT_PATH."/app/time_conditions/time_condition_add.php' alt='".$text['button-add']."'>$v_link_label_add</a>";
	}
	elseif (permission_exists('dialplan_add')) {
		echo "<a href='dialplan_add.php' alt='".$text['button-add']."'>$v_link_label_add</a>";
	}
	if (permission_exists('dialplan_delete') && $result_count > 0) {
		echo "<a href='javascript:void(0);' onclick=\"if (confirm('".$text['confirm-delete']."')) { document.forms.frm_delete.submit(); }\" alt='".$text['button-delete']."'>".$v_link_label_delete."</a>";
	}
	echo "</td>\n";
	echo "</tr>\n";

	if ($result_count > 0) {
		foreach($result as $row) {
			$app_uuid = $row['app_uuid'];
			if (strlen($row['dialplan_number']) == 0) {
				$sql = "select * from v_dialplan_details ";
				$sql .= "where domain_uuid = '$domain_uuid' ";
				$sql .= "and dialplan_uuid = '".$row['dialplan_uuid']."' ";
				$sql .= "and dialplan_detail_type = 'destination_number' ";
				$prep_statement = $db->prepare(check_sql($sql));
				$prep_statement->execute();
				$tmp_result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
				foreach ($tmp_result as &$tmp) {
					//prepare the extension number
						preg_match_all('/[\|0-9\*]/',$tmp["dialplan_detail_data"], $tmp_match);
						$dialplan_number = implode("",$tmp_match[0]);
						$dialplan_number = str_replace("|", " ", $dialplan_number);
						$row['dialplan_number'] = $dialplan_number;
					//update the extension number
						$sql = "update v_dialplans set ";
						$sql .= "dialplan_number = '$dialplan_number' ";
						$sql .= "where domain_uuid = '$domain_uuid' ";
						$sql .= "and dialplan_uuid = '".$row['dialplan_uuid']."'";
						$db->exec($sql);
						unset($sql);
					break; //limit to 1 row
				}
				unset ($prep_statement);
			}

			// blank app id if doesn't match others, so will return to dialplan manager
			switch ($app_uuid) {
				case "c03b422e-13a8-bd1b-e42b-b6b9b4d27ce4" : // inbound route
				case "8c914ec3-9fc0-8ab5-4cda-6c9288bdc9a3" : // outbound route
				case "16589224-c876-aeb3-f59f-523a1c0801f7" : // fifo
				case "4b821450-926b-175a-af93-a03c441818b1" : // time condition
					break;
				default :
					unset($app_uuid);
			}

			if (
				($app_uuid == "c03b422e-13a8-bd1b-e42b-b6b9b4d27ce4" && permission_exists('inbound_route_edit')) ||
				($app_uuid == "8c914ec3-9fc0-8ab5-4cda-6c9288bdc9a3" && permission_exists('outbound_route_edit')) ||
				($app_uuid == "16589224-c876-aeb3-f59f-523a1c0801f7" && permission_exists('fifo_edit')) ||
				($app_uuid == "4b821450-926b-175a-af93-a03c441818b1" && permission_exists('time_condition_edit')) ||
				permission_exists('dialplan_edit')
				) {
				$tr_link = "href='dialplan_edit.php?id=".$row['dialplan_uuid'].(($app_uuid != '') ? "&app_uuid=".$app_uuid : null)."'";
			}
			echo "<tr ".$tr_link.">\n";
			if (permission_exists("dialplan_delete")) {
				echo "	<td valign='top' class='".$row_style[$c]." tr_link_void' style='text-align: center; padding: 3px 0px 0px 0px;'><input type='checkbox' name='id[]' id='checkbox_".$row['dialplan_uuid']."' value='".$row['dialplan_uuid']."'></td>\n";
				$dialplan_ids[] = 'checkbox_'.$row['dialplan_uuid'];
			}
			echo "	<td valign='top' class='".$row_style[$c]."'>";
			if (
				($app_uuid == "c03b422e-13a8-bd1b-e42b-b6b9b4d27ce4" && permission_exists('inbound_route_edit')) ||
				($app_uuid == "8c914ec3-9fc0-8ab5-4cda-6c9288bdc9a3" && permission_exists('outbound_route_edit')) ||
				($app_uuid == "16589224-c876-aeb3-f59f-523a1c0801f7" && permission_exists('fifo_edit')) ||
				($app_uuid == "4b821450-926b-175a-af93-a03c441818b1" && permission_exists('time_condition_edit')) ||
				permission_exists('dialplan_edit')
				) {
				echo "<a href='dialplan_edit.php?id=".$row['dialplan_uuid'].(($app_uuid != '') ? "&app_uuid=".$app_uuid : null)."'>".$row['dialplan_name']."</a>";
			}
			else {
				echo $row['dialplan_name'];
			}
			echo "	</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".((strlen($row['dialplan_number']) > 0) ? $row['dialplan_number'] : "&nbsp;")."</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".$row['dialplan_context']."</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."' style='text-align: center;'>".$row['dialplan_order']."</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."' style='text-align: center;'>".ucwords($row['dialplan_enabled'])."</td>\n";
			echo "	<td valign='top' class='row_stylebg' width='30%'>".((strlen($row['dialplan_description']) > 0) ? $row['dialplan_description'] : "&nbsp;")."</td>\n";
			echo "	<td class='list_control_icons'>\n";
			if (
				($app_uuid == "c03b422e-13a8-bd1b-e42b-b6b9b4d27ce4" && permission_exists('inbound_route_edit')) ||
				($app_uuid == "8c914ec3-9fc0-8ab5-4cda-6c9288bdc9a3" && permission_exists('outbound_route_edit')) ||
				($app_uuid == "16589224-c876-aeb3-f59f-523a1c0801f7" && permission_exists('fifo_edit')) ||
				($app_uuid == "4b821450-926b-175a-af93-a03c441818b1" && permission_exists('time_condition_edit')) ||
				permission_exists('dialplan_edit')
				) {
					echo "<a href='dialplan_edit.php?id=".$row['dialplan_uuid'].(($app_uuid != '') ? "&app_uuid=".$app_uuid : null)."' alt='".$text['button-edit']."'>$v_link_label_edit</a>";
			}
			if (
				($app_uuid == "c03b422e-13a8-bd1b-e42b-b6b9b4d27ce4" && permission_exists('inbound_route_delete')) ||
				($app_uuid == "8c914ec3-9fc0-8ab5-4cda-6c9288bdc9a3" && permission_exists('outbound_route_delete')) ||
				($app_uuid == "16589224-c876-aeb3-f59f-523a1c0801f7" && permission_exists('fifo_delete')) ||
				($app_uuid == "4b821450-926b-175a-af93-a03c441818b1" && permission_exists('time_condition_delete')) ||
				permission_exists('dialplan_delete')
				) {
					echo "<a href=\"dialplan_delete.php?id[]=".$row['dialplan_uuid'].(($app_uuid != '') ? "&app_uuid=".$app_uuid : null)."\" alt='".$text['button-delete']."' onclick=\"return confirm('".$text['confirm-delete']."')\">$v_link_label_delete</a>";
			}
			echo "	</td>\n";
			echo "</tr>\n";
			if ($c==0) { $c=1; } else { $c=0; }
		} //end foreach
		unset($sql, $result, $row_count);
	} //end if results

	echo "<tr>\n";
	echo "<td colspan='8'>\n";
	echo "	<table width='100%' cellpadding='0' cellspacing='0'>\n";
	echo "	<tr>\n";
	echo "		<td width='33.3%' nowrap>&nbsp;</td>\n";
	echo "		<td width='33.3%' align='center' nowrap>".$paging_controls."</td>\n";
	echo "		<td class='list_control_icons'>";
	if ($app_uuid == "c03b422e-13a8-bd1b-e42b-b6b9b4d27ce4" && permission_exists('inbound_route_add')) {
		echo "<a href='".PROJECT_PATH."/app/dialplan_inbound/dialplan_inbound_add.php' alt='".$text['button-add']."'>$v_link_label_add</a>";
	}
	elseif ($app_uuid == "8c914ec3-9fc0-8ab5-4cda-6c9288bdc9a3" && permission_exists('outbound_route_add')) {
		echo "<a href='".PROJECT_PATH."/app/dialplan_outbound/dialplan_outbound_add.php' alt='".$text['button-add']."'>$v_link_label_add</a>";
	}
	elseif ($app_uuid == "16589224-c876-aeb3-f59f-523a1c0801f7" && permission_exists('fifo_add')) {
		echo "<a href='".PROJECT_PATH."/app/fifo/fifo_add.php' alt='".$text['button-add']."'>$v_link_label_add</a>";
	}
	elseif ($app_uuid == "4b821450-926b-175a-af93-a03c441818b1" && permission_exists('time_condition_add')) {
		echo "<a href='".PROJECT_PATH."/app/time_conditions/time_condition_add.php' alt='".$text['button-add']."'>$v_link_label_add</a>";
	}
	elseif (permission_exists('dialplan_add')) {
		echo "<a href='dialplan_add.php' alt='".$text['button-add']."'>$v_link_label_add</a>";
	}
	if (permission_exists('dialplan_delete') && $result_count > 0) {
		echo "<a href='javascript:void(0);' onclick=\"if (confirm('".$text['confirm-delete']."')) { document.forms.frm_delete.submit(); }\" alt='".$text['button-delete']."'>".$v_link_label_delete."</a>";
	}
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "	</table>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "</table>";
	echo "</div>";
	echo "<br><br>";
	echo "<br><br>";

	echo "</td>";
	echo "</tr>";
	echo "</table>";
	echo "</form>";
	echo "</div>";
	echo "<br><br>";

	if (sizeof($dialplan_ids) > 0) {
		echo "<script>\n";
		echo "	function check(what) {\n";
		foreach ($dialplan_ids as $checkbox_id) {
			echo "document.getElementById('".$checkbox_id."').checked = (what == 'all') ? true : false;\n";
		}
		echo "	}\n";
		echo "</script>\n";
	}

//include the footer
	require_once "resources/footer.php";

//unset the variables
	unset ($result_count);
	unset ($result);
	unset ($key);
	unset ($val);
	unset ($c);

?>