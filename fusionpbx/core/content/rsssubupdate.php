<?php
/* $Id$ */
/*
	rsssubupdate.php
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

$rssid = $_GET["rssid"];
if (!ifgroup("admin")) {
	echo "access denied";
	return;
}


if (count($_POST)>0 && $_POST["persistform"] == "0") {

	$rsssubid = checkstr($_POST["rsssubid"]);
	$rssid = checkstr($_POST["rssid"]);
	$rsssubtitle = checkstr($_POST["rsssubtitle"]);
	$rsssublink = checkstr($_POST["rsssublink"]);
	$rsssubdesc = checkstr($_POST["rsssubdesc"]);
	$rsssuboptional1 = checkstr($_POST["rsssuboptional1"]);
	$rsssuboptional2 = checkstr($_POST["rsssuboptional2"]);
	$rsssuboptional3 = checkstr($_POST["rsssuboptional3"]);
	$rsssuboptional4 = checkstr($_POST["rsssuboptional4"]);
	$rsssuboptional5 = checkstr($_POST["rsssuboptional5"]);
	$rsssubadddate = checkstr($_POST["rsssubadddate"]);
	$rsssubadduser = checkstr($_POST["rsssubadduser"]);

	$msg = '';
	if (strlen($rssid) == 0) { $msg .= "Error missing rssid.<br>\n"; }
	if (strlen($rsssubid) == 0) { $msg .= "Error missing rsssubid.<br>\n"; }
	//if (strlen($rsssubtitle) == 0) { $msg .= "Please provide a title.<br>\n"; }
	if (strlen($rsssubdesc) == 0) { $msg .= "Please provide a description.<br>\n"; }

	if (strlen($msg) > 0) {
		require_once "includes/persistform.php";
		require_once "includes/header.php";
		echo "<div align='center' style='' >";
		echo "<table>";
		echo "<tr>";
		echo "<td>";
		echo "  <div class='borderlight' align='left' style='padding:10px;'>";
		echo "      $msg";
		echo "      <br>";
		echo "      <div align='center'>".persistform($_POST)."</div>";
		echo "  </div>";
		echo "</td>";
		echo "</tr>";
		echo "</table>";
		echo "</div>";

		require_once "includes/footer.php";
		return;
	}


	//sql update
	$sql  = "update v_rss_sub set ";
	//$sql .= "rssid = '$rssid', ";
	$sql .= "rsssubtitle = '$rsssubtitle', ";
	$sql .= "rsssublink = '$rsssublink', ";
	$sql .= "rsssubdesc = '$rsssubdesc', ";
	$sql .= "rsssuboptional1 = '$rsssuboptional1', ";
	$sql .= "rsssuboptional2 = '$rsssuboptional2', ";
	$sql .= "rsssuboptional3 = '$rsssuboptional3', ";
	$sql .= "rsssuboptional4 = '$rsssuboptional4', ";
	$sql .= "rsssuboptional5 = '$rsssuboptional5' ";
	//$sql .= "rsssubadddate = now(), ";
	//$sql .= "rsssubadduser = '".$_SESSION["username"]."' ";
	$sql .= "where v_id = '$v_id' ";
	$sql .= "and rsssubid = '$rsssubid' ";
	//$sql .= "and rssid = '$rssid' ";
	$count = $db->exec($sql);
	//echo "Affected Rows: ".$count;

	//edit: make sure the meta redirect url is correct 
	require_once "includes/header.php";
	echo "<meta http-equiv=\"refresh\" content=\"2;url=rsssublist.php?rssid=$rssid&rsssubid=$rsssubid\">\n";
	echo "<div align='center'>";
	echo "Update Complete";
	echo "</div>";
	require_once "includes/footer.php";
	return;
}
else {
	//get data from the db
	$rsssubid = $_GET["rsssubid"];

	$sql = "";
	$sql .= "select * from v_rss_sub ";
	$sql .= "where v_id = '$v_id' ";
	$sql .= "and rsssubid = '$rsssubid' ";
	$prepstatement = $db->prepare($sql);
	$prepstatement->execute();
	while($row = $prepstatement->fetch()) {
		//$rssid = $row["rssid"];
		$rsssubtitle = $row["rsssubtitle"];
		$rsssublink = $row["rsssublink"];
		$rsssubdesc = $row["rsssubdesc"];
		$rsssuboptional1 = $row["rsssuboptional1"];
		$rsssuboptional2 = $row["rsssuboptional2"];
		$rsssuboptional3 = $row["rsssuboptional3"];
		$rsssuboptional4 = $row["rsssuboptional4"];
		$rsssuboptional5 = $row["rsssuboptional5"];
		$rsssubadddate = $row["rsssubadddate"];
		$rsssubadduser = $row["rsssubadduser"];
		break; //limit to 1 row
	}
}

	require_once "includes/header.php";
	require_once "includes/wysiwyg.php";
	echo "<div align='center'>";
	echo "<table border='0' cellpadding='0' cellspacing='2'>\n";

	echo "<tr class='border'>\n";
	echo "	<td align=\"left\">\n";
	echo "      <br>";


	echo "<form method='post' action=''>";
	echo "<table width='100%'>";
	//echo "	<tr>";
	//echo "		<td>Rssid:</td>";
	//echo "		<td><input type='text' name='rssid' class='txt' value='$rssid'></td>";
	//echo "	</tr>";
	echo "	<tr>";
	echo "		<td nowrap>Sub Title:</td>";
	echo "		<td width='100%'><input type='text' name='rsssubtitle' class='txt' value='$rsssubtitle'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td>Sub Link:</td>";
	echo "		<td><input type='text' name='rsssublink' class='txt' value='$rsssublink'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td valign='top'>Description:</td>";
	echo "		<td>";
	echo "            <textarea name='rsssubdesc' rows='12' class='txt'>$rsssubdesc</textarea>";
	echo "        </td>";
	echo "	</tr>";
	//echo "	<tr>";
	//echo "		<td>Rsssuboptional1:</td>";
	//echo "		<td><input type='text' name='rsssuboptional1' value='$rsssuboptional1'></td>";
	//echo "	</tr>";
	//echo "	<tr>";
	//echo "		<td>Rsssuboptional2:</td>";
	//echo "		<td><input type='text' name='rsssuboptional2' value='$rsssuboptional2'></td>";
	//echo "	</tr>";
	//echo "	<tr>";
	//echo "		<td>Rsssuboptional3:</td>";
	//echo "		<td><input type='text' name='rsssuboptional3' value='$rsssuboptional3'></td>";
	//echo "	</tr>";
	//echo "	<tr>";
	//echo "		<td>Rsssuboptional4:</td>";
	//echo "		<td><input type='text' name='rsssuboptional4' value='$rsssuboptional4'></td>";
	//echo "	</tr>";
	//echo "	<tr>";
	//echo "		<td>Rsssuboptional5:</td>";
	//echo "		<td><input type='text' name='rsssuboptional5' value='$rsssuboptional5'></td>";
	//echo "	</tr>";

	echo "	<tr>";
	echo "		<td colspan='2' align='right'>";
	echo "		    <input type='hidden' name='rssid' value='$rssid'>";
	echo "		    <input type='hidden' name='persistform' value='0'>";
	echo "          <input type='hidden' name='rsssubid' value='$rsssubid'>";
	echo "          <input type='submit' name='submit' class='btn' value='Update'>";
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
