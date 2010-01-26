<?php
/* $Id$ */
/*
	root.php
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
require_once "includes/header.php";

if (ifgroup("admin") || ifgroup("superadmin")) {
	//access allowed
}
else {
	echo "access denied";
	return;
}

//requires a superadmin to view members of the superadmin group
	if (!ifgroup("superadmin") && $_GET["groupid"] == "superadmin") {
		echo "access denied";
		return;
	}

//HTTP GET set to a variable
	$groupid = $_GET["groupid"];

function ifgroupmembers($db, $groupid, $username) {
	$sql = "select * from v_group_members ";
	$sql .= "where v_id = '$v_id' ";
	$sql .= "and groupid = '$groupid' ";
	$sql .= "and username = '$username' ";
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	if (count($prepstatement->fetchAll()) == 0) { return true; } else { return false; }
	unset ($sql, $prepstatement);
}
//$exampledatareturned = example("apples", 1);


$c = 0;
$rowstyle["0"] = "rowstyle0";
$rowstyle["1"] = "rowstyle1";

echo "<div align='center'>\n";
echo "<table width='90%' border='0'><tr><td>\n";
echo "<span  class=\"\" height='50'>Member list for <b>$groupid</b></span><br /><br />\n";

$sql = "SELECT * FROM v_group_members ";
$sql .= "where v_id = '$v_id' ";
$sql .= "and groupid = '$groupid' ";
$prepstatement = $db->prepare(check_sql($sql));
$prepstatement->execute();

$strlist = "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";

$strlist .= "<tr>\n";
$strlist .= "	<th align=\"left\" nowrap> &nbsp; Username &nbsp; </th>\n";
$strlist .= "	<th align=\"left\" nowrap> &nbsp; &nbsp; </th>\n";
$strlist .= "	<td width='22' align=\"right\" nowrap>\n";
$strlist .= "		&nbsp;\n";
$strlist .= "	</td>\n";
$strlist .= "</tr>\n";

$count = 0;
$result = $prepstatement->fetchAll();
foreach ($result as &$row) {
	$id = $row["id"];
	$username = $row["username"];
	$strlist .= "<tr'>";
	$strlist .= "<td align=\"left\"  class='".$rowstyle[$c]."' nowrap> &nbsp; $username &nbsp; </td>\n";
	$strlist .= "<td align=\"left\"  class='".$rowstyle[$c]."' nowrap> &nbsp; </td>\n";
	$strlist .= "<td align=\"right\" nowrap>\n";
	$strlist .= "	<a href='groupmemberdelete.php?username=$username&groupid=$groupid' onclick=\"return confirm('Do you really want to delete this?')\" alt='delete'><img src='".$v_icon_delete."' width='17' height='17' border='0' alt='delete'></a>\n";
	$strlist .= "</td>\n";
	$strlist .= "</tr>\n";

	if ($c==0) { $c=1; } else { $c=0; }
	$count++;
}


$strlist .= "</table>\n";
echo $strlist;


echo "</td>";
echo "</tr>";
echo "</table>";
echo "<br>";


echo "  <div align='center'>";
echo "  <form method='post' action='groupmemberadd.php'>";
echo "  <table width='250'>";
echo "	<tr>";
echo "		<td width='60%' align='right'>";

//---- Begin Select List --------------------
$sql = "SELECT * FROM v_users ";
$sql .= "where v_id = '$v_id' ";
$prepstatement = $db->prepare(check_sql($sql));
$prepstatement->execute();

echo "<select name=\"username\" style='width: 200px;' class='formfld'>\n";
echo "<option value=\"\"></option>\n";
$result = $prepstatement->fetchAll();
//$catcount = count($result);
foreach($result as $field) {
	$username = $field[username];
	if (ifgroupmembers($db, $groupid, $username)) {
		echo "<option value='".$field[username]."'>".$field[username]."</option>\n";
	}
}
echo "</select>";
unset($sql, $result);
//---- End Select List --------------------

echo "		</td>";
echo "		<td align='right'>";
echo "          <input type='hidden' name='groupid' value='$groupid'>";
echo "          <input type='submit' class='btn' value='Add Member'>";
echo "      </td>";
echo "	</tr>";
echo "  </table>";
echo "  </form>";
echo "  </div>";

echo "<br><br>";



require_once "includes/footer.php";
?>

