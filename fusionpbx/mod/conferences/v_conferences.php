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

//find the conference extensions from the dialplan include details

	//define the conference array
		$conference_array = array ();

	$sql = "";
	$sql .= "select * from v_dialplan_includes_details ";
	$sql .= "where v_id = $v_id ";
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$x = 0;
	$result = $prepstatement->fetchAll();
	foreach ($result as &$row) {
		$dialplan_include_id = $row["dialplan_include_id"];
		//$tag = $row["tag"];
		//$fieldorder = $row["fieldorder"];
		$fieldtype = $row["fieldtype"];
		//$fielddata = $row["fielddata"];
		if ($fieldtype == "conference") {
			//echo "dialplan_include_id: $dialplan_include_id<br />";
			//echo "fielddata: $fielddata<br />";
			$conference_array[$x]['dialplan_include_id'] = $dialplan_include_id;
			$x++;
		}
	}
	unset ($prepstatement);
	//print_r($conference_array);
	//foreach ($conference_array as &$row) {
	//	echo "--".$row['dialplan_include_id']."--<br />\n";
	//}


//begin the form
	echo "<div align='center'>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='2'>\n";
	echo "<tr class='border'>\n";
	echo "<td align=\"center\">\n";
	echo "<br />";

	echo "	<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";
	echo "	<tr>\n";
	echo "	<td align='left'><span class=\"vexpl\"><span class=\"red\"><strong>Conferences\n";
	echo "		</strong></span></span>\n";
	echo "	</td>\n";
	echo "	<td align='right'>\n";
	//echo "		<input type='button' class='btn' value='advanced' onclick=\"document.location.href='v_conferences.php';\">\n";
	echo "	</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "	<td align='left' colspan='2'>\n";
	echo "		<span class=\"vexpl\">\n";
	echo "			Conferences is used to setup conference rooms with a name, description, and optional pin number.\n";
	echo "			Show <a href='/mod/conferences_active/v_conferences_active.php'>Active Conferences</a> and then select a conference to monitor and interact with it.\n";
	echo "		</span>\n";
	echo "	</td>\n";
	echo "\n";
	echo "	</tr>\n";
	echo "	</table>";

	echo "	<br />";
	echo "	<br />";

	$sql = "";
	$sql .= " select * from v_dialplan_includes ";
	if (count($conference_array) == 0) {
		//when there are no conferences do this to hide all remaining entries
		$sql .= " where v_id = $v_id ";
		$sql .= " and context = 'hide' ";
	}
	else {
		$x = 0;
		foreach ($conference_array as &$row) {
			if ($x == 0) {
				$sql .= " where v_id = $v_id \n";
				$sql .= " and dialplan_include_id = '".$row['dialplan_include_id']."' \n";
			}
			else {
				$sql .= " or v_id = $v_id \n";
				$sql .= " and dialplan_include_id = '".$row['dialplan_include_id']."' \n";
			}
			$x++;
		}
	}
	//echo $sql; //exit;
	if (strlen($orderby)> 0) { $sql .= "order by $orderby $order "; } else { $sql .= "order by dialplanorder, extensionname asc "; }
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	$numrows = count($result);
	unset ($prepstatement, $result, $sql);

	$rowsperpage = 20;
	$param = "";
	$page = $_GET['page'];
	if (strlen($page) == 0) { $page = 0; $_GET['page'] = 0; } 
	list($pagingcontrols, $rowsperpage, $var3) = paging($numrows, $param, $rowsperpage); 
	$offset = $rowsperpage * $page;

	$sql = "";
	$sql .= " select * from v_dialplan_includes ";
	if (count($conference_array) == 0) {
		//when there are no conferences do this to hide all remaining entries
		$sql .= " where v_id = $v_id ";
		$sql .= " and context = 'hide' ";
	}
	else {
		$x = 0;
		foreach ($conference_array as &$row) {
			if ($x == 0) {
				$sql .= " where v_id = $v_id \n";
				$sql .= " and dialplan_include_id = '".$row['dialplan_include_id']."' \n";
			}
			else {
				$sql .= " or v_id = $v_id \n";
				$sql .= " and dialplan_include_id = '".$row['dialplan_include_id']."' \n";
			}
			$x++;
		}
	}
	if (strlen($orderby)> 0) { $sql .= "order by $orderby $order "; } else { $sql .= "order by dialplanorder, extensionname asc "; }
	$sql .= " limit $rowsperpage offset $offset ";
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	$resultcount = count($result);
	unset ($prepstatement, $sql);


	$c = 0;
	$rowstyle["0"] = "rowstyle0";
	$rowstyle["1"] = "rowstyle1";

	echo "<div align='center'>\n";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";

	echo "<tr>\n";
	echo thorderby('extensionname', 'Extension Name', $orderby, $order);
	echo thorderby('dialplanorder', 'Order', $orderby, $order);
	echo thorderby('enabled', 'Enabled', $orderby, $order);
	echo thorderby('descr', 'Description', $orderby, $order);
	echo "<td align='right' width='42'>\n";
	echo "	<a href='v_conferences_add.php' alt='add'><img src='".$v_icon_add."' width='17' height='17' border='0' alt='add'></a>\n";
	echo "</td>\n";
	echo "<tr>\n";

	if ($resultcount == 0) { //no results
	}
	else { //received results

		foreach($result as $row) {
			//print_r( $row );
			echo "<tr >\n";
			echo "   <td valign='top' class='".$rowstyle[$c]."'>&nbsp;&nbsp;".$row[extensionname]."</td>\n";
			echo "   <td valign='top' class='".$rowstyle[$c]."'>&nbsp;&nbsp;".$row[dialplanorder]."</td>\n";
			echo "   <td valign='top' class='".$rowstyle[$c]."'>&nbsp;&nbsp;".$row[enabled]."</td>\n";
			echo "   <td valign='top' class='rowstylebg' width='30%'>".$row[descr]."&nbsp;</td>\n";
			echo "   <td valign='top' align='right'>\n";
			echo "		<a href='v_conferences_edit.php?id=".$row[dialplan_include_id]."' alt='edit'><img src='".$v_icon_edit."' width='17' height='17' border='0' alt='edit'></a>\n";
			echo "		<a href='v_conferences_delete.php?id=".$row[dialplan_include_id]."' alt='delete' onclick=\"return confirm('Do you really want to delete this?')\"><img src='".$v_icon_delete."' width='17' height='17' border='0' alt='delete'></a>\n";
			echo "   </td>\n";
			echo "</tr>\n";
			if ($c==0) { $c=1; } else { $c=0; }
		} //end foreach
		unset($sql, $result, $rowcount);
	} //end if results


	echo "<tr>\n";
	echo "<td colspan='5'>\n";
	echo "	<table width='100%' cellpadding='0' cellspacing='0'>\n";
	echo "	<tr>\n";
	echo "		<td width='33.3%' nowrap>&nbsp;</td>\n";
	echo "		<td width='33.3%' align='center' nowrap>$pagingcontrols</td>\n";
	echo "		<td width='33.3%' align='right'>\n";
	echo "			<a href='v_conferences_add.php' alt='add'><img src='".$v_icon_add."' width='17' height='17' border='0' alt='add'></a>\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "	</table>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td colspan='5' align='left'>\n";
	echo "<br />\n";
	//echo "<br />\n";
	if ($v_path_show) {
		echo $v_conf_dir."/dialplan/default/";
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


require_once "includes/footer.php";
unset ($resultcount);
unset ($result);
unset ($key);
unset ($val);
unset ($c);
?>
