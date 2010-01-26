<?php
/* $Id$ */
/*
	groupadd.php
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



$path = check_str($_GET["path"]);
$msg = check_str($_GET["msg"]);

$groupid = check_str($_POST["groupid"]);
$groupdesc = check_str($_POST["groupdesc"]);


if (strlen($groupid) > 0) {

	$sqlinsert = "insert into v_groups ";
	$sqlinsert .= "(";
	$sqlinsert .= "v_id, ";
	$sqlinsert .= "groupid, ";
	$sqlinsert .= "groupdesc ";
	$sqlinsert .= ")";
	$sqlinsert .= "values ";
	$sqlinsert .= "(";
	$sqlinsert .= "'$v_id', ";
	$sqlinsert .= "'$groupid', ";
	$sqlinsert .= "'$groupdesc' ";
	$sqlinsert .= ")";
	//echo $sqlinsert;
	if (!$db->exec($sqlinsert)) {
		//echo $db->errorCode() . "<br>";
		$info = $db->errorInfo();
		print_r($info);
		// $info[0] == $db->errorCode() unified error code
		// $info[1] is the driver specific error code
		// $info[2] is the driver specific error string
	}

	require_once "includes/header.php";
	echo "<meta http-equiv=\"refresh\" content=\"2;url=index.php\">\n";
	echo "<div align='center'>\n";
	echo "Group Added\n";
	echo "</div>\n";
	require_once "includes/footer.php";
	return;

}


include "includes/header.php";
echo "<br><br>";
echo "<div align='center'>";

echo "<table width='100%' cellpadding='6' cellspacing='0'>\n";
echo "<tr>\n";
echo "<td align='left'>\n";
echo "Please choose a group name. ";
echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "<br>";

echo "<form name='login' METHOD=\"POST\" action=\"groupadd.php\">\n";

echo "<table width='100%' cellpadding='6' cellspacing='0'>\n";
echo "<tr>\n";
echo "<td width='30%' class='vncellreq'>\n";
echo "Group Name:\n";
echo "</td>\n";
echo "<td width='70%' align='left' class='vtable'>\n";
echo "  <input type=\"text\" class='formfld' name=\"groupid\">\n";
echo "</td>\n";
echo "</tr>\n";

echo "<tr>\n";
echo "<td class='vncellreq'>\n";
echo "Description:\n";
echo "</td>\n";
echo "<td align='left' class='vtable'>\n";
echo "<textarea name='groupdesc' class='formfld'></textarea>\n";
echo "</td>\n";
echo "</tr>\n";

echo "<tr>\n";
echo "<td>\n";
echo "</td>\n";
echo "<td align=\"right\">\n";
echo "  <input type=\"hidden\" name=\"path\" value=\"$path\">\n";
echo "  <input type=\"submit\" class='btn' value=\"Save\">\n";
echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "</form>";

echo "</div>";


echo "<br><br>";
echo "<br><br>";


include "includes/footer.php";
?>
