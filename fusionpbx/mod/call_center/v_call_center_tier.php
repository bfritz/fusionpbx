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
require_once "root.php";
require_once "includes/require.php";
require_once "includes/checkauth.php";
if (permission_exists('call_center_tiers_view')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}
require_once "includes/header.php";
require_once "includes/paging.php";

//get http variables and set as php variables
	$order_by = $_GET["order_by"];
	$order = $_GET["order"];

//show the content
	echo "<div align='center'>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='2'>\n";
	echo "<tr class='border'>\n";
	echo "	<td align=\"center\">\n";
	echo "		<br>";

	echo "<table width='100%' border='0'>\n";
	echo "<tr>\n";
	echo "<td width='50%' align='left' nowrap='nowrap'><b>Call Center Tier List</b></td>\n";
	echo "<td width='50%' align='right'>\n";
	echo "	<input type='button' class='btn' name='' alt='add' onclick=\"window.location='v_call_center_queue.php'\" value='Back'>\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td align='left' colspan='2'>\n";
	echo "List all tiers. Tiers assign agents to queues.<br /><br />\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</tr></table>\n";

	$sql = "";
	$sql .= "select * from v_call_center_tiers ";
	$sql .= "where domain_uuid = '$domain_uuid' ";
	if (strlen($order_by) == 0) {
		$order_by = 'queue_name';
		$order = 'asc';
	}
	else {
		$sql .= "order by $order_by $order ";
	}
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$result = $prep_statement->fetchAll();
	$num_rows = count($result);
	unset ($prep_statement, $result, $sql);
	$rows_per_page = 100;
	$param = "";
	$page = $_GET['page'];
	if (strlen($page) == 0) { $page = 0; $_GET['page'] = 0; } 
	list($paging_controls, $rows_per_page, $var_3) = paging($num_rows, $param, $rows_per_page); 
	$offset = $rows_per_page * $page; 

	$sql = "";
	$sql .= "select * from v_call_center_tiers ";
	$sql .= "where domain_uuid = '$domain_uuid' ";
	if (strlen($order_by) == 0) {
		$order_by = 'queue_name';
		$order = 'asc';
	}
	else {
		$sql .= "order by $order_by $order ";
	}
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
	echo thorder_by('agent_name', 'Agent Name', $order_by, $order);
	echo thorder_by('queue_name', 'Queue Name', $order_by, $order);
	echo thorder_by('tier_level', 'Tier Level', $order_by, $order);
	echo thorder_by('tier_position', 'Tier Position', $order_by, $order);
	echo "<td align='right' width='42'>\n";
	if (permission_exists('call_center_tiers_add')) {
		echo "	<a href='v_call_center_tier_edit.php' alt='add'>$v_link_label_add</a>\n";
	}
	echo "</td>\n";
	echo "<tr>\n";

	if ($result_count == 0) { //no results
	}
	else { //received results
		foreach($result as $row) {
			echo "<tr >\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".$row[agent_name]."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".$row[queue_name]."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".$row[tier_level]."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".$row[tier_position]."&nbsp;</td>\n";
			echo "	<td valign='top' align='right'>\n";
			if (permission_exists('call_center_tiers_edit')) {
				echo "		<a href='v_call_center_tier_edit.php?id=".$row[call_center_tier_uuid]."' alt='edit'>$v_link_label_edit</a>\n";
			}
			if (permission_exists('call_center_tiers_delete')) {
				echo "		<a href='v_call_center_tier_delete.php?id=".$row[call_center_tier_uuid]."' alt='delete' onclick=\"return confirm('Do you really want to delete this?')\">$v_link_label_delete</a>\n";
			}
			echo "	</td>\n";
			echo "</tr>\n";
			if ($c==0) { $c=1; } else { $c=0; }
		} //end foreach
		unset($sql, $result, $row_count);
	} //end if results


	echo "<tr>\n";
	echo "<td colspan='6' align='left'>\n";
	echo "	<table width='100%' cellpadding='0' cellspacing='0'>\n";
	echo "	<tr>\n";
	echo "		<td width='33.3%' nowrap>&nbsp;</td>\n";
	echo "		<td width='33.3%' align='center' nowrap>$paging_controls</td>\n";
	echo "		<td width='33.3%' align='right'>\n";
	if (permission_exists('call_center_tiers_add')) {
		echo "			<a href='v_call_center_tier_edit.php' alt='add'>$v_link_label_add</a>\n";
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
	echo "</div>";
	echo "<br><br>";

//show the footer
	require_once "includes/footer.php";
?>
