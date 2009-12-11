<?php
/* $Id$ */
/*
	v_fax.php
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
	$prepstatement = $db->prepare($sql);
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
	$prepstatement = $db->prepare($sql);
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	$resultcount = count($result);
	unset ($prepstatement, $sql);


	$c = 0;
	$rowstyle["0"] = "rowstyle0";
	$rowstyle["1"] = "rowstyle1";

	echo "<div align='center'>\n";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
	//echo "<tr><td colspan='5'><img src='/images/spacer.gif' width='100%' height='1' style='background-color: #BBBBBB;'></td></tr>";

	echo "<tr>\n";
	echo thorderby('faxextension', 'Extension', $orderby, $order);
	echo thorderby('faxname', 'Name', $orderby, $order);
	echo thorderby('faxemail', 'Email', $orderby, $order);
	echo thorderby('faxdomain', 'Domain', $orderby, $order);
	echo thorderby('faxdescription', 'Description', $orderby, $order);
	echo "<td align='right' width='42'>\n";
	echo "	<a href='v_fax_edit.php' alt='add'><img src='".$v_icon_add."' width='17' height='17' border='0' alt='add'></a>\n";
	echo "</td>\n";
	echo "<tr>\n";
	//echo "<tr><td colspan='5'><img src='/images/spacer.gif' width='100%' height='1' style='background-color: #BBBBBB;'></td></tr>\n";

	if ($resultcount == 0) { //no results
	}
	else { //received results

		foreach($result as $row) {
			//print_r( $row );
			echo "<tr >\n";
			echo "   <td valign='top' class='".$rowstyle[$c]."'>".$row[faxextension]."</td>\n";
			echo "   <td valign='top' class='".$rowstyle[$c]."'>".$row[faxname]."</td>\n";
			echo "   <td valign='top' class='".$rowstyle[$c]."'>".$row[faxemail]."&nbsp;</td>\n";
			echo "   <td valign='top' class='".$rowstyle[$c]."'>".$row[faxdomain]."</td>\n";
			echo "   <td valign='top' class='rowstylebg' width='35%'>".$row[faxdescription]."</td>\n";
			echo "   <td valign='top' align='right'>\n";
			echo "		<a href='v_fax_edit.php?id=".$row[fax_id]."' alt='edit'><img src='".$v_icon_edit."' width='17' height='17' border='0' alt='edit'></a>\n";
			echo "		<a href='v_fax_delete.php?id=".$row[fax_id]."' alt='delete' onclick=\"return confirm('Do you really want to delete this?')\"><img src='".$v_icon_delete."' width='17' height='17' border='0' alt='delete'></a>\n";
			echo "   </td>\n";
			echo "</tr>\n";
			//echo "<tr><td colspan='5'><img src='/images/spacer.gif' width='100%'' height='1' style='background-color: #BBBBBB;'></td></tr>\n";
			if ($c==0) { $c=1; } else { $c=0; }
		} //end foreach
		unset($sql, $result, $rowcount);
	} //end if results

	echo "<tr>\n";
	echo "<td colspan='6'>\n";
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
