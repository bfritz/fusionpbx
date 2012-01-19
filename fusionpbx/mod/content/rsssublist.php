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

$rss_uuid = $_GET["rss_uuid"];
$orderby = $_GET["orderby"];
$order = $_GET["order"];

require_once "includes/header.php";


	echo "<div align='center'>";
	echo "<table width='500' border='0' cellpadding='0' cellspacing='2'>\n";
	echo "<tr class='border'>\n";
	echo "	<td align=\"left\">\n";

	echo "      <br>";
	echo "      <b>$moduletitle Details</b>";
	$sql = "";
	$sql .= "select * from v_rss ";
	$sql .= "where domain_uuid = '$domain_uuid'  ";
	$sql .= "and rss_uuid = '$rss_uuid'  ";
	$sql .= "and rss_category = '$rss_category' ";
	$sql .= "and length(rss_del_date) = 0 ";	
	$sql .= "or domain_uuid = '$domain_uuid'  ";
	$sql .= "and rss_uuid = '$rss_uuid'  ";
	$sql .= "and rss_category = '$rss_category' ";
	$sql .= "and rss_del_date is null  ";
	$sql .= "order by rss_uuid asc ";

	//echo $sql;
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	$resultcount = count($result);
	echo "<table border='0' width='100%'>";
	if ($resultcount == 0) { //no results
		echo "<tr><td>&nbsp;</td></tr>";
	}
	else { //received results
		foreach($result as $row) {
		  //print_r( $row );
			  //echo "<tr style='".$rowstyle[$c]."'>\n";
			  //echo "<tr>";
			  //echo "    <td valign='top'>Title</td>";
			  //echo "    <td valign='top'><a href='rssupdate.php?rss_uuid=".$row[rss_uuid]."'>".$row[rss_uuid]."</a></td>";
			  //echo "</tr>";
			  //echo "<td valign='top'>".$row[rss_category]."</td>";
			  
			  echo "<tr>";
			  echo "    <td valign='top'>Title: &nbsp;</td>";
			  echo "    <td valign='top'><b>".$row[rss_title]."</b></td>";
			  echo "    <td valign='top' align='right'>";
			  echo "        <input type='button' class='btn' name='' onclick=\"window.location='rssupdate.php?rss_uuid=".$row[rss_uuid]."'\" value='Update'>";
			  echo "    </td>";
			  $rss_desc = $row[rss_desc];
			  //$rss_desc = str_replace ("\r\n", "<br>", $rss_desc);
			  //$rss_desc = str_replace ("\n", "<br>", $rss_desc);
			  echo "</tr>";              
			  
			  
			  echo "<tr>";
			  echo "    <td valign='top'>Template: &nbsp;</td>";
			  echo "     <td valign='top'>".$row[rss_sub_category]."</td>";
			  echo "</tr>";

			  echo "<tr>";
			  echo "    <td valign='top'>Group: &nbsp;</td>";
			  echo "     <td valign='top'>".$row[rss_group]."</td>";
			  echo "</tr>";
			  
			  if (strlen($row[rss_order]) > 0) {
				  echo "<tr>";
				  echo "    <td valign='top'>Order: &nbsp;</td>";
				  echo "     <td valign='top'>".$row[rss_order]."</td>";
				  echo "</tr>";
			  }

			  //echo "<td valign='top'>".$row[rss_link]."</td>";
			  echo "    <td valign='top'>Description: &nbsp;</td>";
			  echo "    <td valign='top' colspan='2'>".$rss_desc."</td>";
			  //echo "<td valign='top'>".$row[rss_img]."</td>";

			  //echo "<tr>";
			  //echo "    <td valign='top'>Priority: &nbsp;</td>";
			  //echo "    <td valign='top' colspan='2'>".$row[rss_optional_1]."</td>"; //priority
			  //echo "</tr>";

			  //echo "<tr>";
			  //echo "    <td valign='top'>Status: &nbsp;</td>"; //completion status
			  //echo "    <td valign='top' colspan='2'>";
			  //echo      $row[rss_optional_2];
			  //if ($row[rss_optional_2]=="100") {
			  //    echo "Complete";
			  //}
			  //else {
			  //    echo $row[rss_optional_2]."%";
			  //}
			  //echo      "</td>"; //completion status
			  //echo "<td valign='top'>".$row[rss_optional_3]."</td>";
			  //echo "<td valign='top'>".$row[rss_optional_4]."</td>";
			  //echo "<td valign='top'>".$row[rss_optional_5]."</td>";
			  //echo "<td valign='top'>".$row[rss_add_date]."</td>";
			  //echo "<td valign='top'>".$row[rss_add_user]."</td>";
			  //echo "<tr>";
			  //echo "    <td valign='top'>";
			  //echo "      <a href='rsssublist.php?rss_uuid=".$row[rss_uuid]."'>Details</a>";
			  //echo "        <input type='button' class='btn' name='' onclick=\"window.location='rsssublist.php?rss_uuid=".$row[rss_uuid]."'\" value='Details'>";
			  //echo "    </td>";
			  //echo "</tr>";

			  echo "</tr>";

			  //echo "<tr><td colspan='100%'><img src='/images/spacer.gif' width='100%' height='1' style='background-color: #BBBBBB;'></td></tr>\n";
			  if ($c==0) { $c=1; } else { $c=0; }
		} //end foreach
	}
	echo "</table>";
	unset($sql, $prepstatement, $result);


	if ($rsssubshow == 1) {

		echo "<br><br><br>";
		echo "<b>$rss_sub_title</b><br>";

		$sql = "";
		$sql .= "select * from v_rss_sub ";
		$sql .= "where domain_uuid = '$domain_uuid' ";
		$sql .= "and rss_uuid = '$rss_uuid' ";
		$sql .= "and length(rss_sub_del_date) = 0 ";
		$sql .= "or domain_uuid = '$domain_uuid' ";
		$sql .= "and rss_uuid = '$rss_uuid' ";
		$sql .= "and rss_sub_del_date is null ";
		if (strlen($orderby)> 0) { $sql .= "order by $orderby $order "; }
		//echo $sql;

		$prepstatement = $db->prepare(check_sql($sql));
		$prepstatement->execute();
		$result = $prepstatement->fetchAll();
		$resultcount = count($result);

		$c = 0;
		$rowstyle["0"] = "background-color: #F5F5DC;";
		$rowstyle["1"] = "background-color: #FFFFFF;";

		echo "<div align='left'>\n";
		echo "<table width='100%' border='0' cellpadding='1' cellspacing='1'>\n";
		//echo "<tr><td colspan='100%'><img src='/images/spacer.gif' width='100%' height='1' style='background-color: #BBBBBB;'></td></tr>";

		if ($resultcount == 0) { //no results
			echo "<tr><td>&nbsp;</td></tr>";
		}
		else { //received results

			echo "<tr>";
			/*
			  echo "<th nowrap>&nbsp; &nbsp; ";
			  if (strlen($orderby)==0) {
				echo "<a href='?orderby=rss_sub_uuid&order=desc' title='ascending'>rss_sub_uuid</a>";
			  }
			  else {
				if ($order=="asc") {
					echo "<a href='?orderby=rss_sub_uuid&order=desc' title='ascending'>rss_sub_uuid</a>";
				}
				else {
					echo "<a href='?orderby=rss_sub_uuid&order=asc' title='descending'>rss_sub_uuid</a>";
				}
			  }
			  echo "&nbsp; &nbsp; </th>";

			  echo "<th nowrap>&nbsp; &nbsp; ";
			  if (strlen($orderby)==0) {
				echo "<a href='?orderby=rss_uuid&order=desc' title='ascending'>rss_uuid</a>";
			  }
			  else {
				if ($order=="asc") {
					echo "<a href='?orderby=rss_uuid&order=desc' title='ascending'>rss_uuid</a>";
				}
				else {
					echo "<a href='?orderby=rss_uuid&order=asc' title='descending'>rss_uuid</a>";
				}
			  }
			  echo "&nbsp; &nbsp; </th>";

			  echo "<th nowrap>&nbsp; &nbsp; ";
			  if (strlen($orderby)==0) {
				echo "<a href='?orderby=rss_sub_title&order=desc' title='ascending'>rss_sub_title</a>";
			  }
			  else {
				if ($order=="asc") {
					echo "<a href='?orderby=rss_sub_title&order=desc' title='ascending'>rss_sub_title</a>";
				}
				else {
					echo "<a href='?orderby=rss_sub_title&order=asc' title='descending'>rss_sub_title</a>";
				}
			  }
			  echo "&nbsp; &nbsp; </th>";

			  echo "<th nowrap>&nbsp; &nbsp; ";
			  if (strlen($orderby)==0) {
				echo "<a href='?orderby=rss_sub_link&order=desc' title='ascending'>rss_sub_link</a>";
			  }
			  else {
				if ($order=="asc") {
					echo "<a href='?orderby=rss_sub_link&order=desc' title='ascending'>rss_sub_link</a>";
				}
				else {
					echo "<a href='?orderby=rss_sub_link&order=asc' title='descending'>rss_sub_link</a>";
				}
			  }
			  echo "&nbsp; &nbsp; </th>";

			  echo "<th nowrap>&nbsp; &nbsp; ";
			  if (strlen($orderby)==0) {
				echo "<a href='?orderby=rss_sub_desc&order=desc' title='ascending'>rss_sub_desc</a>";
			  }
			  else {
				if ($order=="asc") {
					echo "<a href='?orderby=rss_sub_desc&order=desc' title='ascending'>rss_sub_desc</a>";
				}
				else {
					echo "<a href='?orderby=rss_sub_desc&order=asc' title='descending'>rss_sub_desc</a>";
				}
			  }
			  echo "&nbsp; &nbsp; </th>";

			  echo "<th nowrap>&nbsp; &nbsp; ";
			  if (strlen($orderby)==0) {
				echo "<a href='?orderby=rss_sub_optional_1&order=desc' title='ascending'>rss_sub_optional_1</a>";
			  }
			  else {
				if ($order=="asc") {
					echo "<a href='?orderby=rss_sub_optional_1&order=desc' title='ascending'>rss_sub_optional_1</a>";
				}
				else {
					echo "<a href='?orderby=rss_sub_optional_1&order=asc' title='descending'>rss_sub_optional_1</a>";
				}
			  }
			  echo "&nbsp; &nbsp; </th>";

			  echo "<th nowrap>&nbsp; &nbsp; ";
			  if (strlen($orderby)==0) {
				echo "<a href='?orderby=rss_sub_optional_2&order=desc' title='ascending'>rss_sub_optional_2</a>";
			  }
			  else {
				if ($order=="asc") {
					echo "<a href='?orderby=rss_sub_optional_2&order=desc' title='ascending'>rss_sub_optional_2</a>";
				}
				else {
					echo "<a href='?orderby=rss_sub_optional_2&order=asc' title='descending'>rss_sub_optional_2</a>";
				}
			  }
			  echo "&nbsp; &nbsp; </th>";

			  echo "<th nowrap>&nbsp; &nbsp; ";
			  if (strlen($orderby)==0) {
				echo "<a href='?orderby=rss_sub_optional_3&order=desc' title='ascending'>rss_sub_optional_3</a>";
			  }
			  else {
				if ($order=="asc") {
					echo "<a href='?orderby=rss_sub_optional_3&order=desc' title='ascending'>rss_sub_optional_3</a>";
				}
				else {
					echo "<a href='?orderby=rss_sub_optional_3&order=asc' title='descending'>rss_sub_optional_3</a>";
				}
			  }
			  echo "&nbsp; &nbsp; </th>";

			  echo "<th nowrap>&nbsp; &nbsp; ";
			  if (strlen($orderby)==0) {
				echo "<a href='?orderby=rss_sub_optional_4&order=desc' title='ascending'>rss_sub_optional_4</a>";
			  }
			  else {
				if ($order=="asc") {
					echo "<a href='?orderby=rss_sub_optional_4&order=desc' title='ascending'>rss_sub_optional_4</a>";
				}
				else {
					echo "<a href='?orderby=rss_sub_optional_4&order=asc' title='descending'>rss_sub_optional_4</a>";
				}
			  }
			  echo "&nbsp; &nbsp; </th>";

			  echo "<th nowrap>&nbsp; &nbsp; ";
			  if (strlen($orderby)==0) {
				echo "<a href='?orderby=rss_sub_optional_5&order=desc' title='ascending'>rss_sub_optional_5</a>";
			  }
			  else {
				if ($order=="asc") {
					echo "<a href='?orderby=rss_sub_optional_5&order=desc' title='ascending'>rss_sub_optional_5</a>";
				}
				else {
					echo "<a href='?orderby=rss_sub_optional_5&order=asc' title='descending'>rss_sub_optional_5</a>";
				}
			  }
			  echo "&nbsp; &nbsp; </th>";

			  echo "<th nowrap>&nbsp; &nbsp; ";
			  if (strlen($orderby)==0) {
				echo "<a href='?orderby=rss_sub_add_date&order=desc' title='ascending'>rss_sub_add_date</a>";
			  }
			  else {
				if ($order=="asc") {
					echo "<a href='?orderby=rss_sub_add_date&order=desc' title='ascending'>rss_sub_add_date</a>";
				}
				else {
					echo "<a href='?orderby=rss_sub_add_date&order=asc' title='descending'>rss_sub_add_date</a>";
				}
			  }
			  echo "&nbsp; &nbsp; </th>";

			  echo "<th nowrap>&nbsp; &nbsp; ";
			  if (strlen($orderby)==0) {
				echo "<a href='?orderby=rss_sub_add_user&order=desc' title='ascending'>rss_sub_add_user</a>";
			  }
			  else {
				if ($order=="asc") {
					echo "<a href='?orderby=rss_sub_add_user&order=desc' title='ascending'>rss_sub_add_user</a>";
				}
				else {
					echo "<a href='?orderby=rss_sub_add_user&order=asc' title='descending'>rss_sub_add_user</a>";
				}
			  }
			  echo "&nbsp; &nbsp; </th>";
			  */

			echo "</tr>";
			echo "<tr><td colspan='100%'><img src='/images/spacer.gif' width='100%' height='1' style='background-color: #BBBBBB;'></td></tr>\n";

			foreach($result as $row) {
			//print_r( $row );
				echo "<tr style='".$rowstyle[$c]."'>\n";
					//echo "<td valign='top'>".$rss_uuid."</td>";
					//echo "<td valign='top'>&nbsp;<b>".$row[rss_sub_title]."</b>&nbsp;</td>";
					//echo "<td valign='top'>&nbsp;".$row[rss_sub_link]."&nbsp;</td>";
					echo "<td valign='top' width='200'>";
					echo "  <b>".$row[rss_sub_title]."</b>";
					echo "</td>";

					echo "<td valign='top'>".$row[rss_sub_add_date]."</td>";

					//echo "<td valign='top'>".$row[rss_sub_optional_1]."</td>";
					//echo "<td valign='top'>".$row[rss_sub_optional_2]."</td>";
					//echo "<td valign='top'>".$row[rss_sub_optional_3]."</td>";
					//echo "<td valign='top'>".$row[rss_sub_optional_4]."</td>";
					//echo "<td valign='top'>".$row[rss_sub_optional_5]."</td>";
					//echo "<td valign='top'>".$row[rss_sub_add_user]."</td>";

					echo "<td valign='top'>";
					echo "  <input type='button' class='btn' name='' onclick=\"if (confirm('Are you sure you wish to continue?')) { window.location='rsssubdelete.php?rss_uuid=".$row[rss_uuid]."&rss_sub_uuid=".$row[rss_sub_uuid]."' }\" value='Delete'>";
					echo "</td>";

					echo "<td valign='top' align='right'>";
					echo "  &nbsp;";
					echo "  <input type='button' class='btn' name='' onclick=\"window.location='rsssubupdate.php?rss_uuid=".$rss_uuid."&rss_sub_uuid=".$row[rss_sub_uuid]."'\" value='Update'>";
					echo "  &nbsp; \n";
					//echo "  <a href='rsssubupdate.php?rss_uuid=".$rss_uuid."&rss_sub_uuid=".$row[rss_sub_uuid]."'>Update</a>&nbsp;";
					echo "</td>";


					$rss_sub_desc = $row[rss_sub_desc];
					$rss_sub_desc = str_replace ("\r\n", "<br>", $rss_sub_desc);
					$rss_sub_desc = str_replace ("\n", "<br>", $rss_sub_desc);

					echo "</tr>";
					echo "<tr style='".$rowstyle[$c]."'>\n";
					echo "<td valign='top' width='300' colspan='4'>";
					echo "".$rss_sub_desc."&nbsp;";
					echo "</td>";

					echo "</tr>";



				echo "</tr>";

				echo "<tr><td colspan='100%'><img src='/images/spacer.gif' width='100%' height='1' style='background-color: #BBBBBB;'></td></tr>\n";
				if ($c==0) { $c=1; } else { $c=0; }
			} //end foreach        unset($sql, $result, $rowcount);



		} //end if results

		echo "</table>\n";
		echo "</div>\n";


	} //if ($showrsssub == 1) {

	echo "  <br><br>";
	echo "  </td>\n";
	echo "</tr>\n";
	echo "</table>\n";

	//echo "<input type='button' class='btn' name='' onclick=\"window.location='rsssubsearch.php'\" value='Search'>&nbsp; &nbsp;\n";
	if ($rsssubshow == 1) {
		echo "<input type='button' class='btn' name='' onclick=\"window.location='rsssubadd.php?rss_uuid=".$rss_uuid."'\" value='Add $rss_sub_title'>&nbsp; &nbsp;\n";
	}
	echo "</div>";

	echo "<br><br>";
	require_once "includes/footer.php";

	unset ($resultcount);
	unset ($result);
	unset ($key);
	unset ($val);
	unset ($c);

?>
