<?php
/* $Id$ */
/*
	rsssubcategoryupdate.php
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
	$rsssubcategoryid = checkstr($_POST["rsssubcategoryid"]);
	$rsscategory = checkstr($_POST["rsscategory"]);
	$rsssubcategory = checkstr($_POST["rsssubcategory"]);
	$rsssubcategorydesc = checkstr($_POST["rsssubcategorydesc"]);
	$rssadduser = checkstr($_POST["rssadduser"]);
	$rssadddate = checkstr($_POST["rssadddate"]);

	//sql update
	$sql  = "update v_rss_sub_category set ";
	$sql .= "rsscategory = '$rsscategory', ";
	$sql .= "rsssubcategory = '$rsssubcategory', ";
	$sql .= "rsssubcategorydesc = '$rsssubcategorydesc', ";
	$sql .= "rssadduser = '$rssadduser', ";
	$sql .= "rssadddate = '$rssadddate' ";
	$sql .= "where v_id = '$v_id' ";
	$sql .= "and rsssubcategoryid = '$rsssubcategoryid' ";
	$count = $db->exec($sql);
	//echo "Affected Rows: ".$count;

	//edit: make sure the meta redirect url is correct 
	require_once "includes/header.php";
	echo "<meta http-equiv=\"refresh\" content=\"5;url=rsssubcategorylist.php\">\n";
	echo "Update Complete";
	require_once "includes/footer.php";
	return;
}
else {
	//get data from the db
	$rsssubcategoryid = $_GET["rsssubcategoryid"];

	$sql = "";
	$sql .= "select * from v_rss_sub_category ";
	$sql .= "where v_id = '$v_id' ";
	$sql .= "and rsssubcategoryid = '$rsssubcategoryid' ";
	$prepstatement = $db->prepare($sql);
	$prepstatement->execute();

	while($row = $prepstatement->fetch()) {
		$rsscategory = $row["rsscategory"];
		$rsssubcategory = $row["rsssubcategory"];
		$rsssubcategorydesc = $row["rsssubcategorydesc"];
		$rssadduser = $row["rssadduser"];
		$rssadddate = $row["rssadddate"];
		break; //limit to 1 row
	}
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
echo "		<td><input type='text' name='rsscategory' value='$rsscategory'></td>";
echo "	</tr>";
echo "	<tr>";
echo "		<td>Rsssubcategory:</td>";
echo "		<td><input type='text' name='rsssubcategory' value='$rsssubcategory'></td>";
echo "	</tr>";
echo "	<tr>";
echo "		<td>Rsssubcategorydesc:</td>";
echo "		<td><input type='text' name='rsssubcategorydesc' value='$rsssubcategorydesc'></td>";
echo "	</tr>";
echo "	<tr>";
echo "		<td>Rssadduser:</td>";
echo "		<td><input type='text' name='rssadduser' value='$rssadduser'></td>";
echo "	</tr>";
echo "	<tr>";
echo "		<td>Rssadddate:</td>";
echo "		<td><input type='text' name='rssadddate' value='$rssadddate'></td>";
echo "	</tr>";
echo "	<tr>";
echo "		<td colspan='2' align='right'>";
echo "     <input type='hidden' name='rsssubcategoryid' value='$rsssubcategoryid'>";
echo "     <input type='submit' name='submit' value='Update'>";
echo "		</td>";
echo "	</tr>";
echo "</table>";
echo "</form>";


echo "	</td>";
echo "	</tr>";
echo "</table>";
echo "</div>";


  require_once "includes/footer.php";
?>
