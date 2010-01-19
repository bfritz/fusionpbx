<?php
/* $Id$ */
/*
	grouplist.php
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

//if (ifpermission("add")) {
	echo "<div class='' style='padding:0px;'>\n";
	echo "<table width='100%'>";
	echo "<td>";

	echo "<table width='100%' border='0'><tr>";
	echo "<td width='50%'><b>Group List</b></td>";
	echo "<td width='50%' align='right'>";
	//echo "  <input type='button' class='btn' onclick=\"history.go(-1);\" value='back'>";
	//echo "  <input type='button' class='btn' name='' onclick=\"window.location='groupadd.php'\" value='Add Group'>\n";
	echo "</td>\n";
	echo "</tr></table>";


	$sql = "SELECT * FROM v_groups ";
	$sql .= "where v_id = '$v_id' ";
	$prepstatement = $db->prepare($sql);
	$prepstatement->execute();

	$c = 0;
	$rowstyle["0"] = "rowstyle0";
	$rowstyle["1"] = "rowstyle1";

	$strlist = "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
	//$strlist .= "<tr><td colspan='7'><img src='/images/spacer.gif' width='100%' height='1' style='background-color: #BBBBBB;'></td></tr>";

	$strlist .= "<tr class='border'>\n";
	$strlist .= "	<th align=\"left\" nowrap> &nbsp; Group ID &nbsp; </th>\n";
	$strlist .= "	<th align=\"left\" nowrap> &nbsp; Group Description &nbsp; </th>\n";
	$strlist .= "	<th align=\"center\" nowrap>&nbsp;</th>\n";

	$strlist .= "	<td width='22px' align=\"right\" nowrap>\n";
	$strlist .= "	<a href='groupadd.php' alt='add'><img src='".$v_icon_add."' width='17' height='17' border='0' alt='add'></a>\n";
	$strlist .= "	</td>\n";

	$strlist .= "</tr>\n";
	//$strlist .= "<tr><td colspan='7'><img src='/images/spacer.gif' width='100%' height='1' style='background-color: #BBBBBB;'></td></tr>";

	$count = 0;
	$result = $prepstatement->fetchAll();
	foreach ($result as &$row) {
		$id = $row["id"];
		$groupid = $row["groupid"];
		$groupdesc = $row["groupdesc"];
		if (strlen($groupid) == 0) { $groupid = "&nbsp;"; }
		if (strlen($groupdesc) == 0) { $groupdesc = "&nbsp;"; }
		$groupdesc = wordwrap($groupdesc, 50, "<br />\n");

		if (!ifgroup("superadmin") && $groupid == "superadmin") {
			//hide the superadmin group from non superadmin's
		}
		else {
			$strlist .= "<tr>";
			$strlist .= "<td class='".$rowstyle[$c]."' align=\"left\" class='' nowrap> &nbsp; $groupid &nbsp; </td>\n";
			$strlist .= "<td class='".$rowstyle[$c]."' align=\"left\" class='' nowrap> &nbsp;  $groupdesc &nbsp; </td>\n";

			//if (ifpermission("add")) {
				$strlist .= "<td class='".$rowstyle[$c]."' align=\"center\" nowrap>\n";
				$strlist .= "&nbsp;<a class='' href='groupmembers.php?groupid=$groupid' title='Group Members'>Members</a>&nbsp;";
				$strlist .= "</td>\n";

				//$strlist .= "<td align=\"center\" nowrap>\n";
				//$strlist .= "&nbsp;<a class='' href='grouppermissions.php?groupid=$groupid' title='Group Permissions'>P</a>&nbsp;";
				//$strlist .= "</td>\n";
			//}

			$strlist .= "<td align=\"right\" nowrap>\n";
			//echo "		<a href='v_gateways_edit.php?id=".$id."' alt='edit'><img src='".$v_icon_edit."' width='17' height='17' border='0' alt='edit'></a>\n";
			$strlist .= "<a href='groupdelete.php?id=$id' onclick=\"return confirm('Do you really want to delete this?')\" alt='delete'><img src='".$v_icon_delete."' width='17' height='17' border='0' alt='delete'></a>\n";

			$strlist .= "</td>\n";
			$strlist .= "</tr>\n";
		}

		if ($c==0) { $c=1; } else { $c=0; }
		$count++;
	}

	$strlist .= "<tr>\n";
	$strlist .= "<td colspan='4' align='right' height='20'>\n";
	$strlist .= "	<a href='groupadd.php' alt='add'><img src='".$v_icon_add."' width='17' height='17' border='0' alt='add'></a>\n";
	$strlist .= "</td>\n";
	$strlist .= "</tr>\n";

	$strlist .= "</table>\n";
	if ($count > 0) {
		echo $strlist;
	}


	echo "</td>";
	echo "</tr>";
	echo "</table>";
	echo "<br>";
	echo "</div>";


//} //end if add permission

