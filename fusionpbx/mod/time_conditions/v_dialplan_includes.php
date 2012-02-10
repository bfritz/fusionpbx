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
if (permission_exists('time_conditions_view')) {
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

//find the time condititions from the dialplan include details

	//define the time array
		$time_array = array ();

	//add data to the time array
		$sql = "";
		$sql .= "select * from v_dialplan_details ";
		$sql .= "where domain_uuid = '$domain_uuid' ";
		$prep_statement = $db->prepare(check_sql($sql));
		$prep_statement->execute();
		$x = 0;
		$result = $prep_statement->fetchAll();
		foreach ($result as &$row) {
			$dialplan_uuid = $row["dialplan_uuid"];
			//$dialplan_detail_tag = $row["dialplan_detail_tag"];
			//$dialplan_detail_order = $row["dialplan_detail_order"];
			$dialplan_detail_type = $row["dialplan_detail_type"];
			$dialplan_detail_data = $row["dialplan_detail_data"];
			switch ($row['dialplan_detail_type']) {
			case "hour":
				$time_array[$x]['dialplan_uuid'] = $dialplan_uuid;
				$x++;
				break;
			case "minute":
				$time_array[$x]['dialplan_uuid'] = $dialplan_uuid;
				$x++;
				break;
			case "minute-of-day":
				$time_array[$x]['dialplan_uuid'] = $dialplan_uuid;
				$x++;
				break;
			case "mday":
				$time_array[$x]['dialplan_uuid'] = $dialplan_uuid;
				$x++;
				break;
			case "mweek":
				$time_array[$x]['dialplan_uuid'] = $dialplan_uuid;
				$x++;
				break;
			case "mon":
				$time_array[$x]['dialplan_uuid'] = $dialplan_uuid;
				$x++;
				break;
			case "yday":
				$time_array[$x]['dialplan_uuid'] = $dialplan_uuid;
				$x++;
				break;
			case "year":
				$time_array[$x]['dialplan_uuid'] = $dialplan_uuid;
				$x++;
				break;
			case "wday":
				$time_array[$x]['dialplan_uuid'] = $dialplan_uuid;
				$x++;
				break;
			case "week":
				$time_array[$x]['dialplan_uuid'] = $dialplan_uuid;
				$x++;
				break;
			}
		}
		unset ($prep_statement);

	echo "<div align='center'>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='2'>\n";
	echo "<tr class='border'>\n";
	echo "<td align=\"center\">\n";
	echo "<br />";

	echo "	<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";
	echo "	<tr>\n";
	echo "	<td align='left'><span class=\"vexpl\"><span class=\"red\"><strong>Time Conditions\n";
	echo "		</strong></span></span>\n";
	echo "	</td>\n";
	echo "	<td align='right'>\n";
	echo "		&nbsp;\n";
	echo "	</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "	<td align='left' colspan='2'>\n";
	echo "		<span class=\"vexpl\">\n";
	echo "			Time conditions route calls based on time conditions. You can use time conditions to \n";
	echo "			send calls to gateways, auto attendants, external numbers, to scripts, or any destination.\n";
	echo "		</span>\n";
	echo "	</td>\n";
	echo "\n";
	echo "	</tr>\n";
	echo "	</table>";

	echo "	<br />";
	echo "	<br />";

	$sql = "";
	$sql .= " select * from v_dialplans ";
	if (count($time_array) == 0) {
		//when there are no time conditions then hide all dialplan entries
		$sql .= " where domain_uuid = '$domain_uuid' ";
		$sql .= " and dialplan_context = 'hide' ";
	}
	else {
		$x = 0;
		foreach ($time_array as &$row) {
			if ($x == 0) {
				$sql .= " where domain_uuid = '$domain_uuid' \n";
				$sql .= " and dialplan_uuid = '".$row['dialplan_uuid']."' \n";
			}
			else {
				$sql .= " or domain_uuid = $domain_uuid \n";
				$sql .= " and dialplan_uuid = '".$row['dialplan_uuid']."' \n";
			}
			$x++;
		}
	}
	if (strlen($order_by)> 0) { $sql .= "order by $order_by $order "; } else { $sql .= "order by dialplan_order, dialplan_name asc "; }
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$result = $prep_statement->fetchAll();
	$num_rows = count($result);
	unset ($prep_statement, $result, $sql);

	$rows_per_page = 20;
	$param = "";
	$page = $_GET['page'];
	if (strlen($page) == 0) { $page = 0; $_GET['page'] = 0; } 
	list($paging_controls, $rows_per_page, $var_3) = paging($num_rows, $param, $rows_per_page); 
	$offset = $rows_per_page * $page;

	$sql = "";
	$sql .= " select * from v_dialplans ";
	if (count($time_array) == 0) {
		//when there are no time conditions then hide all dialplan entries
		$sql .= " where domain_uuid = '$domain_uuid' ";
		$sql .= " and dialplan_context = 'hide' ";
	}
	else {
		$x = 0;
		foreach ($time_array as &$row) {
			if ($x == 0) {
				$sql .= " where domain_uuid = '$domain_uuid' \n";
				$sql .= " and dialplan_uuid = '".$row['dialplan_uuid']."' \n";
			}
			else {
				$sql .= " or domain_uuid = $domain_uuid \n";
				$sql .= " and dialplan_uuid = '".$row['dialplan_uuid']."' \n";
			}
			$x++;
		}
	}
	if (strlen($order_by)> 0) { $sql .= "order by $order_by $order "; } else { $sql .= "order by dialplan_order, dialplan_name asc "; }
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
	echo thorder_by('dialplan_name', 'Extension Name', $order_by, $order);
	echo thorder_by('dialplan_order', 'Order', $order_by, $order);
	echo thorder_by('dialplan_enabled', 'Enabled', $order_by, $order);
	echo thorder_by('dialplan_description', 'Description', $order_by, $order);
	echo "<td align='right' width='42'>\n";
	if (permission_exists('time_conditions_add')) {
		echo "	<a href='v_dialplan_add.php' alt='add'>$v_link_label_add</a>\n";
	}
	echo "</td>\n";
	echo "<tr>\n";

	if ($result_count > 0) {
		foreach($result as $row) {
			echo "<tr >\n";
			echo "   <td valign='top' class='".$row_style[$c]."'>&nbsp;&nbsp;".$row['dialplan_name']."</td>\n";
			echo "   <td valign='top' class='".$row_style[$c]."'>&nbsp;&nbsp;".$row['dialplan_order']."</td>\n";
			echo "   <td valign='top' class='".$row_style[$c]."'>&nbsp;&nbsp;".$row['dialplan_enabled']."</td>\n";
			echo "   <td valign='top' class='row_stylebg' width='30%'>".$row['dialplan_description']."&nbsp;</td>\n";
			echo "   <td valign='top' align='right'>\n";
			if (permission_exists('time_conditions_edit')) {
				echo "		<a href='v_dialplan_edit.php?id=".$row['dialplan_uuid']."' alt='edit'>$v_link_label_edit</a>\n";
			}
			if (permission_exists('time_conditions_delete')) {
				echo "		<a href='v_dialplan_delete.php?id=".$row['dialplan_uuid']."' alt='delete' onclick=\"return confirm('Do you really want to delete this?')\">$v_link_label_delete</a>\n";
			}
			echo "   </td>\n";
			echo "</tr>\n";
			if ($c==0) { $c=1; } else { $c=0; }
		} //end foreach
		unset($sql, $result, $row_count);
	} //end if results

	echo "<tr>\n";
	echo "<td colspan='5'>\n";
	echo "	<table width='100%' cellpadding='0' cellspacing='0'>\n";
	echo "	<tr>\n";
	echo "		<td width='33.3%' nowrap>&nbsp;</td>\n";
	echo "		<td width='33.3%' align='center' nowrap>$paging_controls</td>\n";
	echo "		<td width='33.3%' align='right'>\n";
	if (permission_exists('time_conditions_add')) {
		echo "			<a href='v_dialplan_add.php' alt='add'>$v_link_label_add</a>\n";
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
		echo $_SESSION['switch']['dialplan']['directory'];
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
