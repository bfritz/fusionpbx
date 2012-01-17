<?php
/*
	FusionPBX
	Version: MPL 1.1

	The contents of this file are subject to the Mozilla Public License Version
	1.1 (the "License"); you may not use this file except in compliance with
	the License. You may obtain a copy of the License at
	http://www.mozilla.org/MPL/

	Software distributed under the License is distributed on an "AS IS" basis,
	WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
	for the specific language governing rights and limitations under the
	License.

	The Original Code is FusionPBX

	The Initial Developer of the Original Code is
	Mark J Crane <markjcrane@fusionpbx.com>
	Portions created by the Initial Developer are Copyright (C) 2008-2010
	the Initial Developer. All Rights Reserved.

	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>
*/
include "root.php";
require_once "includes/config.php";
require_once "includes/checkauth.php";
require_once "config.php";
if (permission_exists('content_view')) {
	//access granted
}
else {
	echo "access denied";
	exit;
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
$sql .= "where domain_uuid = '$domain_uuid' ";
if (strlen($orderby)> 0) { $sql .= "order by $orderby $order "; }

$prepstatement = $db->prepare(check_sql($sql));
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
		echo "<a href='?orderby=rss_sub_category_id&order=desc' title='ascending'>rss_sub_category_id</a>";
	  }
	  else {
		if ($order=="asc") {
			echo "<a href='?orderby=rss_sub_category_id&order=desc' title='ascending'>rss_sub_category_id</a>";
		}
		else {
			echo "<a href='?orderby=rss_sub_category_id&order=asc' title='descending'>rss_sub_category_id</a>";
		}
	  }
	  echo "&nbsp; &nbsp; </th>";

	  echo "<th nowrap>&nbsp; &nbsp; ";
	  if (strlen($orderby)==0) {
		echo "<a href='?orderby=rss_category&order=desc' title='ascending'>rss_category</a>";
	  }
	  else {
		if ($order=="asc") {
			echo "<a href='?orderby=rss_category&order=desc' title='ascending'>rss_category</a>";
		}
		else {
			echo "<a href='?orderby=rss_category&order=asc' title='descending'>rss_category</a>";
		}
	  }
	  echo "&nbsp; &nbsp; </th>";

	  echo "<th nowrap>&nbsp; &nbsp; ";
	  if (strlen($orderby)==0) {
		echo "<a href='?orderby=rss_sub_category&order=desc' title='ascending'>rss_sub_category</a>";
	  }
	  else {
		if ($order=="asc") {
			echo "<a href='?orderby=rss_sub_category&order=desc' title='ascending'>rss_sub_category</a>";
		}
		else {
			echo "<a href='?orderby=rss_sub_category&order=asc' title='descending'>rss_sub_category</a>";
		}
	  }
	  echo "&nbsp; &nbsp; </th>";

	  echo "<th nowrap>&nbsp; &nbsp; ";
	  if (strlen($orderby)==0) {
		echo "<a href='?orderby=rss_sub_category_desc&order=desc' title='ascending'>rss_sub_category_desc</a>";
	  }
	  else {
		if ($order=="asc") {
			echo "<a href='?orderby=rss_sub_category_desc&order=desc' title='ascending'>rss_sub_category_desc</a>";
		}
		else {
			echo "<a href='?orderby=rss_sub_category_desc&order=asc' title='descending'>rss_sub_category_desc</a>";
		}
	  }
	  echo "&nbsp; &nbsp; </th>";

	  echo "<th nowrap>&nbsp; &nbsp; ";
	  if (strlen($orderby)==0) {
		echo "<a href='?orderby=rss_add_user&order=desc' title='ascending'>rss_add_user</a>";
	  }
	  else {
		if ($order=="asc") {
			echo "<a href='?orderby=rss_add_user&order=desc' title='ascending'>rss_add_user</a>";
		}
		else {
			echo "<a href='?orderby=rss_add_user&order=asc' title='descending'>rss_add_user</a>";
		}
	  }
	  echo "&nbsp; &nbsp; </th>";

	  echo "<th nowrap>&nbsp; &nbsp; ";
	  if (strlen($orderby)==0) {
		echo "<a href='?orderby=rss_add_date&order=desc' title='ascending'>rss_add_date</a>";
	  }
	  else {
		if ($order=="asc") {
			echo "<a href='?orderby=rss_add_date&order=desc' title='ascending'>rss_add_date</a>";
		}
		else {
			echo "<a href='?orderby=rss_add_date&order=asc' title='descending'>rss_add_date</a>";
		}
	  }
	  echo "&nbsp; &nbsp; </th>";

	echo "</tr>";
	echo "<tr><td colspan='100%'><img src='/images/spacer.gif' width='100%' height='1' style='background-color: #BBBBBB;'></td></tr>\n";

	foreach($result as $row) {
	//print_r( $row );
		echo "<tr style='".$rowstyle[$c]."'>\n";
			echo "<td valign='top'><a href='rss_sub_categoryupdate.php?rss_sub_category_id=".$row[rss_sub_category_id]."'>".$row[rss_sub_category_id]."</a></td>";
			echo "<td valign='top'>".$row[rss_category]."</td>";
			echo "<td valign='top'>".$row[rss_sub_category]."</td>";
			echo "<td valign='top'>".$row[rss_sub_category_desc]."</td>";
			echo "<td valign='top'>".$row[rss_add_user]."</td>";
			echo "<td valign='top'>".$row[rss_add_date]."</td>";
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
echo "<input type='button' class='btn' name='' onclick=\"window.location='rss_sub_categorysearch.php'\" value='Search'>&nbsp; &nbsp;\n";
echo "<input type='button' class='btn' name='' onclick=\"window.location='rss_sub_categoryadd.php'\" value='Add'>&nbsp; &nbsp;\n";
echo "</div>";

echo "<br><br>";
require_once "includes/footer.php";

unset ($resultcount);
unset ($result);
unset ($key);
unset ($val);
unset ($c);

?>
