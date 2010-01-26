<?php
/* $Id$ */
/*
	rsssubcategoryadd.php
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
require_once "config.php";

if (!ifgroup("admin")) {
	echo "access denied";
	return;
}

if (count($_POST)>0) {
	$rsscategory = check_str($_POST["rsscategory"]);
	$rsssubcategory = check_str($_POST["rsssubcategory"]);
	$rsssubcategorydesc = check_str($_POST["rsssubcategorydesc"]);
	$rssadduser = check_str($_POST["rssadduser"]);
	$rssadddate = check_str($_POST["rssadddate"]);

	$sql = "insert into v_rss_sub_category ";
	$sql .= "(";
	$sql .= "v_id, ";
	$sql .= "rsscategory, ";
	$sql .= "rsssubcategory, ";
	$sql .= "rsssubcategorydesc, ";
	$sql .= "rssadduser, ";
	$sql .= "rssadddate ";
	$sql .= ")";
	$sql .= "values ";
	$sql .= "(";
	$sql .= "'$v_id', ";
	$sql .= "'$rsscategory', ";
	$sql .= "'$rsssubcategory', ";
	$sql .= "'$rsssubcategorydesc', ";
	$sql .= "'$rssadduser', ";
	$sql .= "'$rssadddate' ";
	$sql .= ")";
	$db->exec(check_sql($sql));
	$lastinsertid = $db->lastInsertId($id);
	unset($sql);

	require_once "includes/header.php";
	echo "<meta http-equiv=\"refresh\" content=\"5;url=rsssubcategorylist.php\">\n";
	echo "Add Complete";
	require_once "includes/footer.php";
	return;
}

require_once "includes/header.php";
echo "<div align='center'>";
echo "<table border='0' cellpadding='0' cellspacing='2'>\n";

echo "<tr class='border'>\n";
echo "	<td align=\"left\">\n";
echo "      <br>";

echo "<form method='post' action=''>";
echo "<table>";
echo "	<tr>";
echo "		<td>Rsscategory:</td>";
echo "		<td><input type='text' name='rsscategory'></td>";
echo "	</tr>";
echo "	<tr>";
echo "		<td>Rsssubcategory:</td>";
echo "		<td><input type='text' name='rsssubcategory'></td>";
echo "	</tr>";
echo "	<tr>";
echo "		<td>Rsssubcategorydesc:</td>";
echo "		<td><input type='text' name='rsssubcategorydesc'></td>";
echo "	</tr>";
echo "	<tr>";
echo "		<td>Rssadduser:</td>";
echo "		<td><input type='text' name='rssadduser'></td>";
echo "	</tr>";
echo "	<tr>";
echo "		<td>Rssadddate:</td>";
echo "		<td><input type='text' name='rssadddate'></td>";
echo "	</tr>";
//echo "	<tr>";
//echo "	<td>example:</td>";
//echo "	<td><textarea name='example'></textarea></td>";
//echo "	</tr>";    echo "	<tr>";
echo "		<td colspan='2' align='right'><input type='submit' name='submit' value='Add'></td>";
echo "	</tr>";
echo "</table>";
echo "</form>";


echo "	</td>";
echo "	</tr>";
echo "</table>";
echo "</div>";


require_once "includes/footer.php";
?>
