<?php
/* $Id$ */
/*
	rsslist.php
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

if (!function_exists('thorderby')) {
    //html table header order by
    function thorderby($fieldname, $columntitle, $orderby, $order) {

        $html .= "<th nowrap>&nbsp; &nbsp; ";
        if (strlen($orderby)==0) {
          $html .= "<a href='?orderby=$fieldname&order=desc' title='ascending'>$columntitle</a>";
        }
        else {
          if ($order=="asc") {
              $html .= "<a href='?orderby=$fieldname&order=desc' title='ascending'>$columntitle</a>";
          }
          else {
              $html .= "<a href='?orderby=$fieldname&order=asc' title='descending'>$columntitle</a>";
          }
        }
        $html .= "&nbsp; &nbsp; </th>";

        return $html;
    }
}

require_once "includes/header.php";
echo "<link rel=\"alternate\" type=\"application/rss+xml\" title=\"\" href=\"rss.php\" />\n";

$orderby = $_GET["orderby"];
$order = $_GET["order"];


	echo "<div align='center'>";
	echo "<table border='0' width='95%' cellpadding='0' cellspacing='2'>\n";

	echo "<tr class='border'>\n";
	echo "	<td align=\"left\">\n";
	//echo "      <br>";

	echo "<table width='100%'>";
	echo "<tr>";
	echo "<td align='left'>";
	echo "      <b>$moduletitle List</b>";
	echo "</td>";
	echo "<td align='right'>";
	//echo "      <input type='button' class='btn' name='' onclick=\"window.location='rssadd.php'\" value='Add $moduletitle'>&nbsp; &nbsp;\n";
	echo "</td>";
	echo "</tr>";
	echo "</table>";

	$sql = "";
	$sql .= "select * from v_rss ";
	$sql .= "where v_id = '$v_id' ";
	$sql .= "and rsscategory = '$rsscategory' ";
	$sql .= "and length(rssdeldate) = 0 ";
	$sql .= "or v_id = '$v_id' ";
	$sql .= "and rsscategory = '$rsscategory' ";
	$sql .= "and rssdeldate is null ";
	if (strlen($orderby)> 0) {
		$sql .= "order by $orderby $order ";
	}
	else {
		$sql .= "order by rssorder asc ";
	}
	//echo $sql;
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	$resultcount = count($result);

	$c = 0;
	$rowstyle["0"] = "rowstyle0";
	$rowstyle["1"] = "rowstyle1";

	echo "<div align='left'>\n";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
	//echo "<tr><td colspan='100%'><img src='/images/spacer.gif' width='100%' height='1' style='background-color: #BBBBBB;'></td></tr>";

	if ($resultcount == 0) { //no results
		echo "<tr><td>&nbsp;</td></tr>";
	}
	else { //received results

		echo "<tr>";
		echo thorderby('rsstitle', 'Title', $orderby, $order);        
		echo thorderby('rsslink', 'Link', $orderby, $order);
		echo thorderby('rsssubcategory', 'Template', $orderby, $order);
		echo thorderby('rssgroup', 'Group', $orderby, $order);     
		echo thorderby('rssorder', 'Order', $orderby, $order);   
		echo "<td align='right' width='42'>\n";
		echo "	<a href='rssadd.php' alt='add'><img src='".$v_icon_add."' width='17' height='17' border='0' alt='add'></a>\n";
		echo "</td>\n";                  
		echo "</tr>";

		foreach($result as $row) {
		//print_r( $row );
			echo "<tr style='".$rowstyle[$c]."'>\n";
				//echo "<td valign='top'><a href='rssupdate.php?rssid=".$row[rssid]."'>".$row[rssid]."</a></td>";
				//echo "<td valign='top'>".$row[rsscategory]."</td>";

				echo "<td valign='top' nowrap class='".$rowstyle[$c]."'>&nbsp;".$row[rsstitle]."&nbsp;</td>";                
				echo "<td valign='top' nowrap class='".$rowstyle[$c]."'>&nbsp;<a href='/index.php?c=".$row[rsslink]."'>".$row[rsslink]."</a>&nbsp;</td>";
				echo "<td valign='top' class='".$rowstyle[$c]."'>".$row[rsssubcategory]."</td>";
				if (strlen($row[rssgroup]) > 0) {
					echo "<td valign='top' class='".$rowstyle[$c]."'>".$row[rssgroup]."</td>";
				}
				else {
					echo "<td valign='top' class='".$rowstyle[$c]."'>public</td>";
				}

				//echo "<td valign='top'>".$row[rssdesc]."</td>";
				//echo "<td valign='top'>".$row[rssimg]."</td>";
				//echo "<td valign='top'>&nbsp;".$row[rssoptional1]."&nbsp;</td>"; //priority

				//echo "<td valign='top' class='".$rowstyle[$c]."'>&nbsp;";
				//sif ($row[rssoptional2]=="100") {
				//    echo "Complete";
				//}
				//else {
				//    echo $row[rssoptional2]."%";
				//}
				//echo "&nbsp;</td>"; //completion status

				//echo "<td valign='top'>".$row[rssoptional3]."</td>";
				//echo "<td valign='top'>".$row[rssoptional4]."</td>";
				//echo "<td valign='top'>".$row[rssoptional5]."</td>";
				echo "<td valign='top' class='".$rowstyle[$c]."'>".$row[rssorder]."&nbsp;</td>";

				//echo "<td valign='top' align='center'>";
				//echo "  <input type='button' class='btn' name='' onclick=\"window.location='rssmoveup.php?menuparentid=".$row[menuparentid]."&rssid=".$row[rssid]."&rssorder=".$row[rssorder]."'\" value='<' title='".$row[rssorder].". Move Up'>";
				//echo "  <input type='button' class='btn' name='' onclick=\"window.location='rssmovedown.php?menuparentid=".$row[menuparentid]."&rssid=".$row[rssid]."&rssorder=".$row[rssorder]."'\" value='>' title='".$row[rssorder].". Move Down'>";
				//echo "</td>";

				echo "	<td valign='top' align='right'>\n";
				echo "		<a href='rssupdate.php?rssid=".$row[rssid]."' alt='edit'><img src='".$v_icon_edit."' width='17' height='17' alt='edit' border='0'></a>\n";
				echo "		<a href='rssdelete.php?rssid=".$row[rssid]."' alt='delete' onclick=\"return confirm('Do you really want to delete this?')\"><img src='".$v_icon_delete."' width='17' height='17' alt='delete' border='0'></a>\n";
				echo "	</td>\n";

				//echo "<td valign='top' align='right' class='".$rowstyle[$c]."'>";
				//echo "  <input type='button' class='btn' name='' onclick=\"if (confirm('Are you sure you wish to continue?')) { window.location='rssdelete.php?rssid=".$row[rssid]."' }\" value='Delete'>";
				//echo "</td>";

				//echo "<td valign='top' align='right' class='".$rowstyle[$c]."'>";
				//echo "  <input type='button' class='btn' name='' onclick=\"window.location='rsssublist.php?rssid=".$row[rssid]."'\" value='Details'>";
				//echo "</td>";

			echo "</tr>";

			if ($c==0) { $c=1; } else { $c=0; }
		} //end foreach
		unset($sql, $result, $rowcount);

	} //end if results

	echo "<tr>\n";
	echo "<td colspan='6' align='left'>\n";
	echo "	<table border='0' width='100%' cellpadding='0' cellspacing='0'>\n";
	echo "	<tr>\n";
	echo "		<td width='33.3%' nowrap>&nbsp;</td>\n";
	echo "		<td width='33.3%' align='center' nowrap>$pagingcontrols</td>\n";
	echo "		<td width='33.3%' align='right'>\n";
	echo "			<a href='rssadd.php' alt='add'><img src='".$v_icon_add."' width='17' height='17' border='0' alt='add'></a>\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "	</table>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "</table>\n";
	echo "</div>\n";

	echo "  <br>";
	echo "  </td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	//echo "<input type='button' class='btn' name='' onclick=\"window.location='rsssearch.php'\" value='Search'>&nbsp; &nbsp;\n";
	//echo "<input type='button' class='btn' name='' onclick=\"window.location='rssadd.php'\" value='Add $moduletitle'>&nbsp; &nbsp;\n";
	echo "</div>";

	echo "<br><br>";
	require_once "includes/footer.php";

	unset ($resultcount);
	unset ($result);
	unset ($key);
	unset ($val);
	unset ($c);

?>
