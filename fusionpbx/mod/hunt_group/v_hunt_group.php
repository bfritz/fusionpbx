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
require_once "includes/require.php";
require_once "includes/checkauth.php";

//check permissions
	if (permission_exists('hunt_group_view')) {
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

echo "<div align='center'>";
echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
echo "<tr class='border'>\n";
echo "	<td align=\"center\">\n";
echo "      <br />";

echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
echo "<tr>\n";
echo "    <td align='left'><p><span class='vexpl'><span class='red'><strong>Hunt Group<br />\n";
echo "        </strong></span>\n";
echo "			A Hunt Group is a list of destinations that can be called in sequence or simultaneously.\n";
echo "        </span></p></td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "<br />\n";

//get the number of rows in v_hunt_groups
$sql = "";
$sql .= " select count(*) as num_rows from v_hunt_groups ";
$sql .= "where domain_uuid = '$domain_uuid' ";
$sql .= "and hunt_group_type <> 'dnd' ";
$sql .= "and hunt_group_type <> 'call_forward' ";
$sql .= "and hunt_group_type <> 'follow_me_simultaneous' ";
$sql .= "and hunt_group_type <> 'follow_me_sequence' ";
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

//prepare to page the results
$rows_per_page = 150;
$param = "";
$page = $_GET['page'];
if (strlen($page) == 0) { $page = 0; $_GET['page'] = 0; } 
list($paging_controls, $rows_per_page, $var_3) = paging($num_rows, $param, $rows_per_page); 
$offset = $rows_per_page * $page; 

//get the hunt group list
$sql = "";
$sql .= " select * from v_hunt_groups ";
$sql .= "where domain_uuid = '$domain_uuid' ";
$sql .= "and hunt_group_type <> 'dnd' ";
$sql .= "and hunt_group_type <> 'call_forward' ";
$sql .= "and hunt_group_type <> 'follow_me_simultaneous' ";
$sql .= "and hunt_group_type <> 'follow_me_sequence' ";
if (strlen($order_by)> 0) {
	$sql .= "order by $order_by $order ";
}
else {
	$sql .= "order by hunt_group_extension asc ";
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
echo thorder_by('hunt_group_extension', 'Extension', $order_by, $order);
echo thorder_by('hunt_group_name', 'Hunt Group Name', $order_by, $order);
echo thorder_by('hunt_group_name', 'Enabled', $order_by, $order);
echo thorder_by('hunt_group_descr', 'Description', $order_by, $order);
echo "<td align='right' width='42'>\n";
if (permission_exists('hunt_group_add')) {
	echo "	<a href='v_hunt_group_edit.php' alt='add'>$v_link_label_add</a>\n";
}
echo "</td>\n";
echo "<tr>\n";

if ($result_count == 0) {
	//no results
}
else { //received results
	foreach($result as $row) {
		echo "<tr >\n";
		echo "   <td valign='top' class='".$row_style[$c]."'>".$row['hunt_group_extension']."</td>\n";
		echo "   <td valign='top' class='".$row_style[$c]."'>".$row['hunt_group_name']."</td>\n";
		echo "   <td valign='top' class='".$row_style[$c]."'>".$row['hunt_group_enabled']."</td>\n";
		echo "   <td valign='top' class='row_stylebg' width='40%'>".$row['hunt_group_descr']."&nbsp;</td>\n";
		echo "   <td valign='top' align='right'>\n";
		if (permission_exists('hunt_group_edit')) {
			echo "		<a href='v_hunt_group_edit.php?id=".$row['hunt_group_uuid']."' alt='edit'>$v_link_label_edit</a>\n";
		}
		if (permission_exists('hunt_group_delete')) {
			echo "		<a href='v_hunt_group_delete.php?id=".$row['hunt_group_uuid']."' alt='delete' onclick=\"return confirm('Do you really want to delete this?')\">$v_link_label_delete</a>\n";
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
if (permission_exists('hunt_group_add')) {
	echo "			<a href='v_hunt_group_edit.php' alt='add'>$v_link_label_add</a>\n";
}
echo "		</td>\n";
echo "	</tr>\n";
echo "	</table>\n";
echo "</td>\n";
echo "</tr>\n";

if ($v_path_show) {
	echo "<tr>\n";
	echo "<td align='left' colspan='4'>\n";
	echo "<br />\n";
	echo $_SESSION['switch']['scripts']['dir']."\n";
	echo "</td>\n";
	echo "</tr>\n";
}

echo "</table>";
echo "</div>";
echo "<br><br>";
echo "<br><br>";

echo "</td>";
echo "</tr>";
echo "</table>";
echo "</div>";
echo "<br><br>";


require_once "includes/footer.php";
unset ($result_count);
unset ($result);
unset ($key);
unset ($val);
unset ($c);
?>
