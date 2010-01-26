<?php
/* $Id$ */
/*
	userlist.php
	Copyright (C) 2008, 2009 Mark J Crane
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
	//access allowed
}
else {
	echo "access denied";
	return;
}

//get a list of all superadmin users
	$superadminlist = superadminlist($db);


if (!function_exists('thorderby')) {
	//html table header order by
	function thorderby($fieldname, $columntitle, $orderby, $order) {

		$html .= "<th nowrap>&nbsp; &nbsp; ";
		if (strlen($orderby)==0) {
			$html .= "<a href='?orderby=$fieldname&order=desc' title='ascending'>$columntitle</a>";
		}
		else {
			if ($order=="asc") {
				$html .= "<a href='?orderby=$fieldname&order=desc' title='ascending'>$columntitle</a>";
			}
			else {
				$html .= "<a href='?orderby=$fieldname&order=asc' title='descending'>$columntitle</a>";
			}
		}
		$html .= "&nbsp; &nbsp; </th>";

		return $html;
	}
}
//example use
//echo thorderby('id', 'Id', $orderby, $order);

$pagelayout = "full";
//require_once "includes/header.php";

$orderby = $_GET["orderby"];
$order = $_GET["order"];    echo "<div align='center'>";

	echo "<table width='100%' border='0' cellpadding='0' cellspacing='2'>\n";

	echo "<tr class='border'>\n";
	echo "	<td align=\"left\">\n";
	echo "      <br>";

	echo "<table width='100%' border='0'><tr>";
	echo "<td width='50%'><b>User List</b></td>";
	echo "<td width='50%' align='right'>";
	//echo "  <input type='button' class='btn' onclick=\"history.go(-1);\" value='back'>";
	//echo "  <input type='button' class='btn' name='' onclick=\"window.location='/users/signup.php'\" value='Add User'>\n";
	echo "</td>\n";
	echo "</tr></table>";

	$sql = "";
	$sql .= "select * from v_users ";
	$sql .= "where v_id = '$v_id' ";
	if (strlen($orderby)> 0) { $sql .= "order by $orderby $order "; }

	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	$resultcount = count($result);

	$c = 0;
	$rowstyle["0"] = "rowstyle0";
	$rowstyle["1"] = "rowstyle1";

	echo "<div align='left'>\n";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
	//echo "<tr><td colspan='100%'><img src='/images/spacer.gif' width='100%' height='1' style='background-color: #BBBBBB;'></td></tr>";

	if ($resultcount == 0) { //no results
		echo "<tr><td>&nbsp;</td></tr>";
	}
	else { //received results

		echo "<tr>";
		echo thorderby('Username', 'Username', $orderby, $order);
		echo thorderby('Userfirstname', 'First Name', $orderby, $order);
		echo thorderby('Userlastname', 'Last Name', $orderby, $order);
		echo thorderby('Usercompanyname', 'Company', $orderby, $order);
		echo thorderby('Userphysicalcity', 'City', $orderby, $order);
		echo thorderby('Userphysicalstateprovince', 'State', $orderby, $order);
		echo thorderby('Userphone1', 'Phone', $orderby, $order);
		echo thorderby('Useremail', 'Email', $orderby, $order);
		echo "<td width='42px' align=\"right\" nowrap>\n";
		echo "	<a href='signup.php' alt='add'><img src='".$v_icon_add."' width='17' height='17' border='0' alt='add'></a>\n";
		echo "</td>\n";
		echo "</tr>";
		//echo "<tr><td colspan='100%'><img src='/images/spacer.gif' width='100%' height='1' style='background-color: #BBBBBB;'></td></tr>\n";

		foreach($result as $row) {
			//print_r( $row );
			if (!ifgroup("superadmin") && ifsuperadmin($superadminlist, $row[username])) { 
				//allow superadmins to view all users
			}
			else {
				echo "<tr>\n";
					//echo "<td valign='top'>".$row[useroptional2]."</td>";
					//echo "<td valign='top'>".$row[useroptional1]."</td>";
					//if (ifgroup("admin")) {
					//    echo "<td valign='top'><a href='usersupdate.php?id=".$row[id]."'>".$row[id]."</a></td>";
					//}
					if (ifgroup("admin")) {
						echo "<td valign='top' class='".$rowstyle[$c]."'><a href=''>".$row[username]."</a></td>";
					}
					//echo "<td valign='top'>".$row[password]."</td>";
					echo "<td valign='top' class='".$rowstyle[$c]."'>".$row[userfirstname]."&nbsp;</td>";
					echo "<td valign='top' class='".$rowstyle[$c]."'>".$row[userlastname]."&nbsp;</td>";
					echo "<td valign='top' class='".$rowstyle[$c]."'>".$row[usercompanyname]."&nbsp;</td>";
					//echo "<td valign='top'>".$row[userphysicaladdress1]."</td>";
					//echo "<td valign='top'>".$row[userphysicaladdress2]."</td>";
					echo "<td valign='top' class='".$rowstyle[$c]."'>".$row[userphysicalcity]."&nbsp;</td>";
					echo "<td valign='top' class='".$rowstyle[$c]."'>".$row[userphysicalstateprovince]."&nbsp;</td>";
					//echo "<td valign='top'>".$row[userphysicalcountry]."</td>";
					//echo "<td valign='top'>".$row[userphysicalpostalcode]."</td>";
					/*
					echo "<td valign='top'>".$row[usermailingaddress1]."</td>";
					echo "<td valign='top'>".$row[usermailingaddress2]."</td>";
					echo "<td valign='top'>".$row[usermailingcity]."</td>";
					echo "<td valign='top'>".$row[usermailingstateprovince]."</td>";
					echo "<td valign='top'>".$row[usermailingcountry]."</td>";
					echo "<td valign='top'>".$row[usermailingpostalcode]."</td>";
					echo "<td valign='top'>".$row[userbillingaddress1]."</td>";
					echo "<td valign='top'>".$row[userbillingaddress2]."</td>";
					echo "<td valign='top'>".$row[userbillingcity]."</td>";
					echo "<td valign='top'>".$row[userbillingstateprovince]."</td>";
					echo "<td valign='top'>".$row[userbillingcountry]."</td>";
					echo "<td valign='top'>".$row[userbillingpostalcode]."</td>";
					echo "<td valign='top'>".$row[usershippingaddress1]."</td>";
					echo "<td valign='top'>".$row[usershippingaddress2]."</td>";
					echo "<td valign='top'>".$row[usershippingcity]."</td>";
					echo "<td valign='top'>".$row[usershippingstateprovince]."</td>";
					echo "<td valign='top'>".$row[usershippingcountry]."</td>";
					echo "<td valign='top'>".$row[usershippingpostalcode]."</td>";
					*/
					//echo "<td valign='top'>".$row[userurl]."</td>";
					echo "<td valign='top' class='".$rowstyle[$c]."'>".$row[userphone1]."&nbsp;</td>";
					//echo "<td valign='top'>".$row[userphone1ext]."</td>";
					//echo "<td valign='top'>".$row[userphone2]."</td>";
					//echo "<td valign='top'>".$row[userphone2ext]."</td>";
					//echo "<td valign='top'>".$row[userphonemobile]."</td>";
					//echo "<td valign='top'>".$row[userphonefax]."</td>";
					echo "<td valign='top' class='".$rowstyle[$c]."'><a href='mailto:".$row[useremail]."'>".$row[useremail]."</a>&nbsp;</td>";
					//echo "<td valign='top'>".$row[useradduser]."</td>";
					//echo "<td valign='top'>".$row[useradddate]."</td>";

					if (ifgroup("admin")) {
						echo "   <td valign='top' align='right' nowrap>\n";
						echo "		<a href='usersupdate.php?id=".$row[id]."' alt='edit'><img src='".$v_icon_edit."' width='17' height='17' border='0' alt='edit'></a>\n";
						echo "		<a href='userdelete.php?id=".$row[id]."' onclick=\"return confirm('Do you really want to delete this?')\" alt='delete'><img src='".$v_icon_delete."' width='17' height='17' border='0' alt='delete'></a>\n";
						echo "   </td>\n";
					}

				echo "</tr>";
			}
			if ($c==0) { $c=1; } else { $c=0; }
		} //end foreach
		unset($sql, $result, $rowcount);


	} //end if results

	echo "<tr>\n";
	echo "<td colspan='9' align='left'>\n";
	echo "	<table border='0' width='100%' cellpadding='0' cellspacing='0'>\n";
	echo "	<tr>\n";
	echo "		<td width='33.3%' nowrap>&nbsp;</td>\n";
	echo "		<td width='33.3%' align='center' nowrap>$pagingcontrols</td>\n";
	echo "		<td width='33.3%' align='right'>\n";
	echo "			<a href='signup.php' alt='add'><img src='".$v_icon_add."' width='17' height='17' border='0' alt='add'></a>\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "	</table>\n";
	echo "</td>\n";
	echo "</tr>\n";
	
	echo "</table>\n";
	echo "</div>\n";

	echo "  <br><br>";
	echo "  </td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</div>";

	echo "<br><br>";
	//require_once "includes/footer.php";

	unset ($resultcount);
	unset ($result);
	unset ($key);
	unset ($val);
	unset ($c);

?>
