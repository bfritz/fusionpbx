<?php
/* $Id$ */
/*
	rsssearch.php
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
return; //disabled

include "root.php";
require_once "includes/config.php";
require_once "includes/checkauth.php";
require_once "config.php";
if (!ifgroup("admin")) {
	echo "access denied";
	return;
}

if (count($_POST)>0) {
	$rssid = checkstr($_POST["rssid"]);
	//$rsscategory = checkstr($_POST["rsscategory"]); //defined in local config.php
	$rsssubcategory = checkstr($_POST["rsssubcategory"]);
	$rsstitle = checkstr($_POST["rsstitle"]);
	$rsslink = checkstr($_POST["rsslink"]);
	$rssdesc = checkstr($_POST["rssdesc"]);
	$rssimg = checkstr($_POST["rssimg"]);
	$rssoptional1 = checkstr($_POST["rssoptional1"]);
	$rssoptional2 = checkstr($_POST["rssoptional2"]);
	$rssoptional3 = checkstr($_POST["rssoptional3"]);
	$rssoptional4 = checkstr($_POST["rssoptional4"]);
	$rssoptional5 = checkstr($_POST["rssoptional5"]);
	$rssadddate = checkstr($_POST["rssadddate"]);
	$rssadduser = checkstr($_POST["rssadduser"]);

	require_once "includes/header.php";



	echo "<div align='center'>";
	echo "<table border='0' cellpadding='0' cellspacing='2'>\n";

	echo "<tr class='border'>\n";
	echo "	<td align=\"left\">\n";
	echo "      <br>";


	$sql = "";
	$sql .= "select * from v_rss ";
	$sql .= "where ";
	if (strlen($rssid) > 0) { $sql .= "and rssid like '%$rssid%' "; }
	if (strlen($rsscategory) > 0) { $sql .= "and rsscategory like '%$rsscategory%' "; }
	if (strlen($rsssubcategory) > 0) { $sql .= "and rsssubcategory like '%$rsssubcategory%' "; }
	if (strlen($rsstitle) > 0) { $sql .= "and rsstitle like '%$rsstitle%' "; }
	if (strlen($rsslink) > 0) { $sql .= "and rsslink like '%$rsslink%' "; }
	if (strlen($rssdesc) > 0) { $sql .= "and rssdesc like '%$rssdesc%' "; }
	if (strlen($rssimg) > 0) { $sql .= "and rssimg like '%$rssimg%' "; }
	if (strlen($rssoptional1) > 0) { $sql .= "and rssoptional1 like '%$rssoptional1%' "; }
	if (strlen($rssoptional2) > 0) { $sql .= "and rssoptional2 like '%$rssoptional2%' "; }
	if (strlen($rssoptional3) > 0) { $sql .= "and rssoptional3 like '%$rssoptional3%' "; }
	if (strlen($rssoptional4) > 0) { $sql .= "and rssoptional4 like '%$rssoptional4%' "; }
	if (strlen($rssoptional5) > 0) { $sql .= "and rssoptional5 like '%$rssoptional5%' "; }
	if (strlen($rssadddate) > 0) { $sql .= "and rssadddate like '%$rssadddate%' "; }
	if (strlen($rssadduser) > 0) { $sql .= "and rssadduser like '%$rssadduser%' "; }
	$sql .= "and length(rssdeldate) = 0 ";
	$sql .= "or ";
	if (strlen($rssid) > 0) { $sql .= "and rssid like '%$rssid%' "; }
	if (strlen($rsscategory) > 0) { $sql .= "and rsscategory like '%$rsscategory%' "; }
	if (strlen($rsssubcategory) > 0) { $sql .= "and rsssubcategory like '%$rsssubcategory%' "; }
	if (strlen($rsstitle) > 0) { $sql .= "and rsstitle like '%$rsstitle%' "; }
	if (strlen($rsslink) > 0) { $sql .= "and rsslink like '%$rsslink%' "; }
	if (strlen($rssdesc) > 0) { $sql .= "and rssdesc like '%$rssdesc%' "; }
	if (strlen($rssimg) > 0) { $sql .= "and rssimg like '%$rssimg%' "; }
	if (strlen($rssoptional1) > 0) { $sql .= "and rssoptional1 like '%$rssoptional1%' "; }
	if (strlen($rssoptional2) > 0) { $sql .= "and rssoptional2 like '%$rssoptional2%' "; }
	if (strlen($rssoptional3) > 0) { $sql .= "and rssoptional3 like '%$rssoptional3%' "; }
	if (strlen($rssoptional4) > 0) { $sql .= "and rssoptional4 like '%$rssoptional4%' "; }
	if (strlen($rssoptional5) > 0) { $sql .= "and rssoptional5 like '%$rssoptional5%' "; }
	if (strlen($rssadddate) > 0) { $sql .= "and rssadddate like '%$rssadddate%' "; }
	if (strlen($rssadduser) > 0) { $sql .= "and rssadduser like '%$rssadduser%' "; }
	$sql .= "and rssdeldate is null ";

	$sql = trim($sql);
	if (substr($sql, -5) == "where"){ $sql = substr($sql, 0, (strlen($sql)-5)); }
	if (substr($sql, -3) == " or"){ $sql = substr($sql, 0, (strlen($sql)-5)); }
	$sql = str_replace ("where and", "where", $sql);
	$sql = str_replace ("or and", "or", $sql);
	//echo $sql;
	$prepstatement = $db->prepare($sql);
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	$resultcount = count($result);

	$c = 0;
	$rowstyle["0"] = "background-color: #F5F5DC;";
	$rowstyle["1"] = "background-color: #FFFFFF;";

	echo "<b>Search Results</b><br>";
	echo "<div align='left'>\n";
	echo "<table border='0' cellpadding='1' cellspacing='1'>\n";
	echo "<tr><td colspan='100%'><img src='/images/spacer.gif' width='100%' height='1' style='background-color: #BBBBBB;'></td></tr>";

	if ($resultcount == 0) { //no results
		echo "<tr><td>&nbsp;</td></tr>";
	}
	else { //received results

		echo "<tr>";
		  echo "<th nowrap>&nbsp; &nbsp; ID&nbsp; &nbsp; </th>";
		  echo "<th nowrap>&nbsp; &nbsp; Category&nbsp; &nbsp; </th>";
		  echo "<th nowrap>&nbsp; &nbsp; Sub Category&nbsp; &nbsp; </th>";
		  echo "<th nowrap>&nbsp; &nbsp; Title&nbsp; &nbsp; </th>";
		  //echo "<th nowrap>&nbsp; &nbsp; Rsslink&nbsp; &nbsp; </th>";
		  //echo "<th nowrap>&nbsp; &nbsp; Rssdesc&nbsp; &nbsp; </th>";
		  //echo "<th nowrap>&nbsp; &nbsp; Rssimg&nbsp; &nbsp; </th>";
		  //echo "<th nowrap>&nbsp; &nbsp; Rssoptional1&nbsp; &nbsp; </th>";
		  //echo "<th nowrap>&nbsp; &nbsp; Rssoptional2&nbsp; &nbsp; </th>";
		  //echo "<th nowrap>&nbsp; &nbsp; Rssoptional3&nbsp; &nbsp; </th>";
		  //echo "<th nowrap>&nbsp; &nbsp; Rssoptional4&nbsp; &nbsp; </th>";
		  //echo "<th nowrap>&nbsp; &nbsp; Rssoptional5&nbsp; &nbsp; </th>";
		  //echo "<th nowrap>&nbsp; &nbsp; Rssadddate&nbsp; &nbsp; </th>";
		  //echo "<th nowrap>&nbsp; &nbsp; Rssadduser&nbsp; &nbsp; </th>";
		echo "</tr>";
		echo "<tr><td colspan='100%'><img src='/images/spacer.gif' width='100%' height='1' style='background-color: #BBBBBB;'></td></tr>\n";

		foreach($result as $row) {
		//print_r( $row );
			echo "<tr style='".$rowstyle[$c]."'>\n";
				echo "<td valign='top'><a href='rssupdate.php?rssid=".$row[rssid]."'>".$row[rssid]."</a></td>";
				echo "<td valign='top'>".$row[rsscategory]."</td>";
				echo "<td valign='top'>".$row[rsssubcategory]."</td>";
				echo "<td valign='top'>".$row[rsstitle]."</td>";
				//echo "<td valign='top'>".$row[rsslink]."</td>";
				//echo "<td valign='top'>".$row[rssdesc]."</td>";
				//echo "<td valign='top'>".$row[rssimg]."</td>";
				//echo "<td valign='top'>".$row[rssoptional1]."</td>";
				//echo "<td valign='top'>".$row[rssoptional2]."</td>";
				//echo "<td valign='top'>".$row[rssoptional3]."</td>";
				//echo "<td valign='top'>".$row[rssoptional4]."</td>";
				//echo "<td valign='top'>".$row[rssoptional5]."</td>";
				//echo "<td valign='top'>".$row[rssadddate]."</td>";
				//echo "<td valign='top'>".$row[rssadduser]."</td>";
			echo "</tr>";

			echo "<tr><td colspan='100%'><img src='/images/spacer.gif' width='100%' height='1' style='background-color: #BBBBBB;'></td></tr>\n";
			if ($c==0) { $c=1; } else { $c=0; }
		} //end foreach        unset($sql, $result, $rowcount);

		echo "</table>\n";
		echo "</div>\n";


		echo "  <br><br>";
		echo "  </td>\n";
		echo "</tr>\n";

	} //end if results

	echo "</table>\n";
	echo "</div>";

	echo "<br><br>";
	require_once "includes/footer.php";

	unset ($resultcount);
	unset ($result);
	unset ($key);
	unset ($val);
	unset ($c);

	}
	else {

		echo "\n";    require_once "includes/header.php";
	echo "<div align='center'>";
	echo "<table border='0' cellpadding='0' cellspacing='2'>\n";

	echo "<tr class='border'>\n";
	echo "	<td align=\"left\">\n";
	echo "      <br>";


	echo "<form method='post' action=''>";
	echo "<table>";
	echo "	<tr>";
	echo "		<td>Id:</td>";
	echo "		<td><input type='text' class='txt' name='rssid'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td>Category:</td>";
	echo "		<td><input type='text' class='txt' name='rsscategory'></td>";
	echo "	</tr>";
	//echo "	<tr>";
	//echo "		<td>Rsssubcategory:</td>";
	//echo "		<td><input type='text' class='txt' name='rsssubcategory'></td>";
	//echo "	</tr>";
	echo "	<tr>";
	echo "		<td>Title:</td>";
	echo "		<td><input type='text' class='txt' name='rsstitle'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td>Link:</td>";
	echo "		<td><input type='text' class='txt' name='rsslink'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td>Desc:</td>";
	echo "		<td><input type='text' class='txt' name='rssdesc'></td>";
	echo "	</tr>";
	//echo "	<tr>";
	//echo "		<td>Image:</td>";
	//echo "		<td><input type='text' class='txt' name='rssimg'></td>";
	//echo "	</tr>";
	//echo "	<tr>";
	//echo "		<td>Rssoptional1:</td>";
	//echo "		<td><input type='text' class='txt' name='rssoptional1'></td>";
	//echo "	</tr>";
	//echo "	<tr>";
	//echo "		<td>Rssoptional2:</td>";
	//echo "		<td><input type='text' class='txt' name='rssoptional2'></td>";
	//echo "	</tr>";
	//echo "	<tr>";
	//echo "		<td>Rssoptional3:</td>";
	//echo "		<td><input type='text' class='txt' name='rssoptional3'></td>";
	//echo "	</tr>";
	//echo "	<tr>";
	//echo "		<td>Rssoptional4:</td>";
	//echo "		<td><input type='text' class='txt' name='rssoptional4'></td>";
	//echo "	</tr>";
	//echo "	<tr>";
	//echo "		<td>Rssoptional5:</td>";
	//echo "		<td><input type='text' class='txt' name='rssoptional5'></td>";
	//echo "	</tr>";
	//echo "	<tr>";
	//echo "		<td>Rssadddate:</td>";
	//echo "		<td><input type='text' class='txt' name='rssadddate'></td>";
	//echo "	</tr>";
	//echo "	<tr>";
	//echo "		<td>Rssadduser:</td>";
	//echo "		<td><input type='text' class='txt' name='rssadduser'></td>";
	//echo "	</tr>";
	echo "	<tr>";
	echo "		<td colspan='2' align='right'><input type='submit' name='submit' class='btn' value='Search'></td>";
	echo "	</tr>";
	echo "</table>";
	echo "</form>";


	echo "	</td>";
	echo "	</tr>";
	echo "</table>";
	echo "</div>";


require_once "includes/footer.php";

} //end if not post
?>
