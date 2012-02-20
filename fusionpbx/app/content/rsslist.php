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
	Portions created by the Initial Developer are Copyright (C) 2008-2012
	the Initial Developer. All Rights Reserved.

	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>
*/
include "root.php";
require_once "includes/require.php";
require_once "includes/checkauth.php";
require_once "config.php";
if (permission_exists('content_view')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

if (!function_exists('th_order_by')) {
	//html table header order by
	function th_order_by($field_name, $columntitle, $order_by, $order) {

		$html .= "<th nowrap>&nbsp; &nbsp; ";
		if (strlen($order_by)==0) {
			$html .= "<a href='?order_by=$field_name&order=desc' title='ascending'>$columntitle</a>";
		}
		else {
			if ($order=="asc") {
				$html .= "<a href='?order_by=$field_name&order=desc' title='ascending'>$columntitle</a>";
			}
			else {
				$html .= "<a href='?order_by=$field_name&order=asc' title='descending'>$columntitle</a>";
			}
		}
		$html .= "&nbsp; &nbsp; </th>";

		return $html;
	}
}

require_once "includes/header.php";
echo "<link rel=\"alternate\" type=\"application/rss+xml\" title=\"\" href=\"rss.php\" />\n";

$order_by = $_GET["order_by"];
$order = $_GET["order"];


	echo "<div align='center'>";
	echo "<table border='0' width='100%' cellpadding='0' cellspacing='2'>\n";
	echo "<tr class='border'>\n";
	echo "	<td align=\"left\">\n";

	echo "<table width='100%'>";
	echo "<tr>";
	echo "<td align='left'>";
	echo "      <b>$module_title List</b>";
	echo "</td>";
	echo "<td align='right'>";
	//echo "      <input type='button' class='btn' name='' onclick=\"window.location='rssadd.php'\" value='Add $module_title'>&nbsp; &nbsp;\n";
	echo "</td>";
	echo "</tr>";
	echo "</table>";

	$sql = "";
	$sql .= "select * from v_rss ";
	$sql .= "where domain_uuid = '$domain_uuid' ";
	$sql .= "and rss_category = '$rss_category' ";
	$sql .= "and length(rss_del_date) = 0 ";
	$sql .= "or domain_uuid = '$domain_uuid' ";
	$sql .= "and rss_category = '$rss_category' ";
	$sql .= "and rss_del_date is null ";
	if (strlen($order_by)> 0) {
		$sql .= "order by $order_by $order ";
	}
	else {
		$sql .= "order by rss_order asc ";
	}
	//echo $sql;
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$result = $prep_statement->fetchAll();
	$result_count = count($result);

	$c = 0;
	$row_style["0"] = "row_style0";
	$row_style["1"] = "row_style1";

	echo "<div align='left'>\n";
	echo "<table width='100%' border='0' cellpadding='2' cellspacing='0'>\n";
	echo "<tr>";
	echo th_order_by('rss_title', 'Title', $order_by, $order);
	echo th_order_by('rss_link', 'Link', $order_by, $order);
	//echo th_order_by('rss_sub_category', 'Template', $order_by, $order);
	echo th_order_by('rss_group', 'Group', $order_by, $order);
	echo th_order_by('rss_order', 'Order', $order_by, $order);
	if ($result_count == 0) { //no results
		echo "<td align='right' width='21'>\n";
	}
	else {
		echo "<td align='right' width='42'>\n";
	}
	echo "	<a href='rssadd.php' alt='add'>$v_link_label_add</a>\n";
	echo "</td>\n";
	echo "</tr>";

	if ($result_count > 0) {
		foreach($result as $row) {
		//print_r( $row );
			echo "<tr style='".$row_style[$c]."'>\n";
				//echo "<td valign='top'><a href='rssupdate.php?rss_uuid=".$row[rss_uuid]."'>".$row[rss_uuid]."</a></td>";
				//echo "<td valign='top'>".$row[rss_category]."</td>";

				echo "<td valign='top' nowrap class='".$row_style[$c]."'>&nbsp;".$row[rss_title]."&nbsp;</td>";                
				echo "<td valign='top' nowrap class='".$row_style[$c]."'>&nbsp;<a href='/index.php?c=".$row[rss_link]."'>".$row[rss_link]."</a>&nbsp;</td>";
				//echo "<td valign='top' class='".$row_style[$c]."'>".$row[rss_sub_category]."&nbsp;</td>";
				if (strlen($row[rss_group]) > 0) {
					echo "<td valign='top' class='".$row_style[$c]."'>".$row[rss_group]."</td>";
				}
				else {
					echo "<td valign='top' class='".$row_style[$c]."'>public</td>";
				}

				//echo "<td valign='top'>".$row[rss_desc]."</td>";
				//echo "<td valign='top'>".$row[rss_img]."</td>";
				//echo "<td valign='top'>&nbsp;".$row[rss_optional_1]."&nbsp;</td>"; //priority

				//echo "<td valign='top' class='".$row_style[$c]."'>&nbsp;";
				//sif ($row[rss_optional_2]=="100") {
				//    echo "Complete";
				//}
				//else {
				//    echo $row[rss_optional_2]."%";
				//}
				//echo "&nbsp;</td>"; //completion status

				//echo "<td valign='top'>".$row[rss_optional_3]."</td>";
				//echo "<td valign='top'>".$row[rss_optional_4]."</td>";
				//echo "<td valign='top'>".$row[rss_optional_5]."</td>";
				echo "<td valign='top' class='".$row_style[$c]."'>".$row[rss_order]."&nbsp;</td>";

				//echo "<td valign='top' align='center'>";
				//echo "  <input type='button' class='btn' name='' onclick=\"window.location='rssmoveup.php?menuparentid=".$row[menuparentid]."&rss_uuid=".$row[rss_uuid]."&rss_order=".$row[rss_order]."'\" value='<' title='".$row[rss_order].". Move Up'>";
				//echo "  <input type='button' class='btn' name='' onclick=\"window.location='rssmovedown.php?menuparentid=".$row[menuparentid]."&rss_uuid=".$row[rss_uuid]."&rss_order=".$row[rss_order]."'\" value='>' title='".$row[rss_order].". Move Down'>";
				//echo "</td>";

				echo "	<td valign='top' align='right'>\n";
				echo "		<a href='rssupdate.php?rss_uuid=".$row[rss_uuid]."' alt='edit'>$v_link_label_edit</a>\n";
				echo "		<a href='rssdelete.php?rss_uuid=".$row[rss_uuid]."' alt='delete' onclick=\"return confirm('Do you really want to delete this?')\">$v_link_label_delete</a>\n";
				echo "	</td>\n";

				//echo "<td valign='top' align='right' class='".$row_style[$c]."'>";
				//echo "  <input type='button' class='btn' name='' onclick=\"if (confirm('Are you sure you wish to continue?')) { window.location='rssdelete.php?rss_uuid=".$row[rss_uuid]."' }\" value='Delete'>";
				//echo "</td>";

			echo "</tr>";

			if ($c==0) { $c=1; } else { $c=0; }
		} //end foreach
		unset($sql, $result, $row_count);

	} //end if results

	echo "<tr>\n";
	echo "<td colspan='6' align='left'>\n";

	echo "	<table border='0' width='100%' cellpadding='0' cellspacing='0'>\n";
	echo "	<tr>\n";
	echo "		<td width='33.3%' nowrap>&nbsp;</td>\n";
	echo "		<td width='33.3%' align='center' nowrap>$paging_controls</td>\n";
	echo "		<td width='33.3%' align='right'>\n";
	echo "			<a href='rssadd.php' alt='add'>$v_link_label_add</a>\n";
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
	//echo "<input type='button' class='btn' name='' onclick=\"window.location='rssadd.php'\" value='Add $module_title'>&nbsp; &nbsp;\n";
	echo "</div>";

	echo "<br><br>";
	require_once "includes/footer.php";

	unset ($result_count);
	unset ($result);
	unset ($key);
	unset ($val);
	unset ($c);

?>
