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


	echo "<div align='center'>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='2'>\n";

	echo "<tr class='border'>\n";
	echo "	<td align=\"center\">\n";
	echo "      <br>";


	echo "  	<table width=\"100%\" border=\"0\" cellpadding=\"6\" cellspacing=\"0\">\n";
	echo "      <tr>\n";
	echo "        <td align='left'><p><span class=\"vexpl\"><span class=\"red\"><strong>FAX<br>\n";
	echo "            </strong></span>\n";
	echo "			To receive a FAX setup a fax extension and then direct the incoming FAX with a dedicated number or you can detect the FAX tone by using\n";
	echo "";
	if ($v_path_show) {
		echo "<a href='http://wiki.freeswitch.org/wiki/Misc._Dialplan_Tools_tone_detect' target='_blank'>tone detection</a>\n";
	}
	else {
		echo "tone detection\n";
	}
	echo "			on the Public tab.\n";
	echo "            </p></td>\n";
	echo "      </tr>\n";
	echo "    </table>\n";
	echo "    <br />";


	$sql = "";
	$sql .= " select * from v_fax ";
	$sql .= "where v_id = '$v_id' ";
	if (strlen($orderby)> 0) { $sql .= "order by $orderby $order "; }
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	$numrows = count($result);
	unset ($prepstatement, $result, $sql);

	$rowsperpage = 10;
	$param = "";
	$page = $_GET['page'];
	if (strlen($page) == 0) { $page = 0; $_GET['page'] = 0; } 
	list($pagingcontrols, $rowsperpage, $var3) = paging($numrows, $param, $rowsperpage); 
	$offset = $rowsperpage * $page;

	$sql = "";
	$sql .= " select * from v_fax ";
	$sql .= "where v_id = '$v_id' ";
	if (strlen($orderby)> 0) { $sql .= "order by $orderby $order "; }
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
	echo thorderby('faxextension', 'Extension', $orderby, $order);
	echo thorderby('faxname', 'Name', $orderby, $order);
	echo thorderby('faxemail', 'Email', $orderby, $order);
	echo thorderby('faxdescription', 'Description', $orderby, $order);
	echo "<td align='right' width='42'>\n";
	echo "	<a href='v_fax_edit.php' alt='add'><img src='".$v_icon_add."' width='17' height='17' border='0' alt='add'></a>\n";
	echo "</td>\n";
	echo "<tr>\n";

	if ($resultcount == 0) { //no results
	}
	else { //received results

		foreach($result as $row) {
			//print_r( $row );
			echo "<tr >\n";
			echo "   <td valign='top' class='".$rowstyle[$c]."'>".$row[faxextension]."</td>\n";
			echo "   <td valign='top' class='".$rowstyle[$c]."'>".$row[faxname]."</td>\n";
			echo "   <td valign='top' class='".$rowstyle[$c]."'>".$row[faxemail]."&nbsp;</td>\n";
			echo "   <td valign='top' class='rowstylebg' width='35%'>".$row[faxdescription]."</td>\n";
			echo "   <td valign='top' align='right'>\n";
			echo "		<a href='v_fax_edit.php?id=".$row[fax_id]."' alt='edit'><img src='".$v_icon_edit."' width='17' height='17' border='0' alt='edit'></a>\n";
			echo "		<a href='v_fax_delete.php?id=".$row[fax_id]."' alt='delete' onclick=\"return confirm('Do you really want to delete this?')\"><img src='".$v_icon_delete."' width='17' height='17' border='0' alt='delete'></a>\n";
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
	echo "			<a href='v_fax_edit.php' alt='add'><img src='".$v_icon_add."' width='17' height='17' border='0' alt='add'></a>\n";
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


require_once "includes/footer.php";
unset ($resultcount);
unset ($result);
unset ($key);
unset ($val);
unset ($c);
?>
