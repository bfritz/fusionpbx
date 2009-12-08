<?php
/* $Id$ */
/*
	rsssubcategorylist.php
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

require_once "includes/header.php";

$orderby = $_GET["orderby"];
$order = $_GET["order"];    



echo "<div align='center'>";
echo "<table border='0' cellpadding='0' cellspacing='2'>\n";

echo "<tr class='border'>\n";
echo "	<td align=\"left\">\n";
echo "      <br>";


$sql = "";
$sql .= "select * from v_rss_sub_category ";
$sql .= "where v_id = '$v_id' ";
if (strlen($orderby)> 0) { $sql .= "order by $orderby $order "; }

$prepstatement = $db->prepare($sql);
$prepstatement->execute();
$result = $prepstatement->fetchAll();
$resultcount = count($result);

$c = 0;
$rowstyle["0"] = "background-color: #F5F5DC;";
$rowstyle["1"] = "background-color: #FFFFFF;";

echo "<div align='left'>\n";
echo "<table border='0' cellpadding='1' cellspacing='1'>\n";
echo "<tr><td colspan='100%'><img src='/images/spacer.gif' width='100%' height='1' style='background-color: #BBBBBB;'></td></tr>";

if ($resultcount == 0) { //no results
	echo "<tr><td>&nbsp;</td></tr>";
}
else { //received results

	echo "<tr>";
	  echo "<th nowrap>&nbsp; &nbsp; ";
	  if (strlen($orderby)==0) {
		echo "<a href='?orderby=Rsssubcategoryid&order=desc' title='ascending'>Rsssubcategoryid</a>";
	  }
	  else {
		if ($order=="asc") {
			echo "<a href='?orderby=Rsssubcategoryid&order=desc' title='ascending'>Rsssubcategoryid</a>";
		}
		else {
			echo "<a href='?orderby=Rsssubcategoryid&order=asc' title='descending'>Rsssubcategoryid</a>";
		}
	  }
	  echo "&nbsp; &nbsp; </th>";

	  echo "<th nowrap>&nbsp; &nbsp; ";
	  if (strlen($orderby)==0) {
		echo "<a href='?orderby=Rsscategory&order=desc' title='ascending'>Rsscategory</a>";
	  }
	  else {
		if ($order=="asc") {
			echo "<a href='?orderby=Rsscategory&order=desc' title='ascending'>Rsscategory</a>";
		}
		else {
			echo "<a href='?orderby=Rsscategory&order=asc' title='descending'>Rsscategory</a>";
		}
	  }
	  echo "&nbsp; &nbsp; </th>";

	  echo "<th nowrap>&nbsp; &nbsp; ";
	  if (strlen($orderby)==0) {
		echo "<a href='?orderby=Rsssubcategory&order=desc' title='ascending'>Rsssubcategory</a>";
	  }
	  else {
		if ($order=="asc") {
			echo "<a href='?orderby=Rsssubcategory&order=desc' title='ascending'>Rsssubcategory</a>";
		}
		else {
			echo "<a href='?orderby=Rsssubcategory&order=asc' title='descending'>Rsssubcategory</a>";
		}
	  }
	  echo "&nbsp; &nbsp; </th>";

	  echo "<th nowrap>&nbsp; &nbsp; ";
	  if (strlen($orderby)==0) {
		echo "<a href='?orderby=Rsssubcategorydesc&order=desc' title='ascending'>Rsssubcategorydesc</a>";
	  }
	  else {
		if ($order=="asc") {
			echo "<a href='?orderby=Rsssubcategorydesc&order=desc' title='ascending'>Rsssubcategorydesc</a>";
		}
		else {
			echo "<a href='?orderby=Rsssubcategorydesc&order=asc' title='descending'>Rsssubcategorydesc</a>";
		}
	  }
	  echo "&nbsp; &nbsp; </th>";

	  echo "<th nowrap>&nbsp; &nbsp; ";
	  if (strlen($orderby)==0) {
		echo "<a href='?orderby=Rssadduser&order=desc' title='ascending'>Rssadduser</a>";
	  }
	  else {
		if ($order=="asc") {
			echo "<a href='?orderby=Rssadduser&order=desc' title='ascending'>Rssadduser</a>";
		}
		else {
			echo "<a href='?orderby=Rssadduser&order=asc' title='descending'>Rssadduser</a>";
		}
	  }
	  echo "&nbsp; &nbsp; </th>";

	  echo "<th nowrap>&nbsp; &nbsp; ";
	  if (strlen($orderby)==0) {
		echo "<a href='?orderby=Rssadddate&order=desc' title='ascending'>Rssadddate</a>";
	  }
	  else {
		if ($order=="asc") {
			echo "<a href='?orderby=Rssadddate&order=desc' title='ascending'>Rssadddate</a>";
		}
		else {
			echo "<a href='?orderby=Rssadddate&order=asc' title='descending'>Rssadddate</a>";
		}
	  }
	  echo "&nbsp; &nbsp; </th>";

	echo "</tr>";
	echo "<tr><td colspan='100%'><img src='/images/spacer.gif' width='100%' height='1' style='background-color: #BBBBBB;'></td></tr>\n";

	foreach($result as $row) {
	//print_r( $row );
		echo "<tr style='".$rowstyle[$c]."'>\n";
			echo "<td valign='top'><a href='rsssubcategoryupdate.php?rsssubcategoryid=".$row[rsssubcategoryid]."'>".$row[rsssubcategoryid]."</a></td>";
			echo "<td valign='top'>".$row[rsscategory]."</td>";
			echo "<td valign='top'>".$row[rsssubcategory]."</td>";
			echo "<td valign='top'>".$row[rsssubcategorydesc]."</td>";
			echo "<td valign='top'>".$row[rssadduser]."</td>";
			echo "<td valign='top'>".$row[rssadddate]."</td>";
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
echo "<input type='button' class='btn' name='' onclick=\"window.location='rsssubcategorysearch.php'\" value='Search'>&nbsp; &nbsp;\n";
echo "<input type='button' class='btn' name='' onclick=\"window.location='rsssubcategoryadd.php'\" value='Add'>&nbsp; &nbsp;\n";
echo "</div>";

echo "<br><br>";
require_once "includes/footer.php";

unset ($resultcount);
unset ($result);
unset ($key);
unset ($val);
unset ($c);

?>
