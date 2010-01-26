<?php
/* $Id$ */
/*
	v_vars.php
	Copyright (C) 2008 Mark J Crane
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
require_once "includes/header.php";
require_once "includes/paging.php";
if (ifgroup("admin") || ifgroup("superadmin")) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

$orderby = $_GET["orderby"];
$order = $_GET["order"];
if (!function_exists('thorderby')) {
	//html table header order by
	function thorderby($fieldname, $columntitle, $orderby, $order) {

		$html .= "<th class='' nowrap>&nbsp; &nbsp; ";
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

	echo "<div align='center'>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='2'>\n";

	echo "<tr class='border'>\n";
	echo "	<td align=\"center\">\n";
	echo "		<br>";

	echo "<table width=\"100%\" border=\"0\" cellpadding=\"6\" cellspacing=\"0\">\n";
	echo "  <tr>\n";
	echo "	<td align='left'><b>Variables</b><br>\n";
	echo "		Define preprocessor variables here. \n";
	echo "	</td>\n";
	echo "  </tr>\n";
	echo "</table>\n";
	//echo "<br />";

	//$sql = "";
	//$sql .= "select * from v_vars ";
	//$sql .= "where v_id = '$v_id' ";
	//if (strlen($orderby)> 0) { $sql .= "order by $orderby $order "; }
	//$prepstatement = $db->prepare(check_sql($sql));
	//$prepstatement->execute();
	//$result = $prepstatement->fetchAll();
	//$numrows = count($result);
	//unset ($prepstatement, $result, $sql);

	//$rowsperpage = 100;
	//$param = "";
	//$page = $_GET['page'];
	//if (strlen($page) == 0) { $page = 0; $_GET['page'] = 0; } 
	//list($pagingcontrols, $rowsperpage, $var3) = paging($numrows, $param, $rowsperpage); 
	//$offset = $rowsperpage * $page; 

	$sql = "";
	$sql .= "select * from v_vars ";
	$sql .= "where v_id = '$v_id' ";
	if (strlen($orderby)> 0) {
		$sql .= "order by $orderby $order ";
	}
	else {
		$sql .= "order by var_cat, var_order asc ";
	}
	//$sql .= " limit $rowsperpage offset $offset ";

	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	$resultcount = count($result);
	unset ($prepstatement, $sql);


	$c = 0;
	$rowstyle["0"] = "rowstyle0";
	$rowstyle["1"] = "rowstyle1";

	echo "<div align='center'>\n";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";

	$tmp_var_header = '';
	$tmp_var_header .= "<tr>\n";
	$tmp_var_header .= thorderby('var_name', 'Name', $orderby, $order);
	$tmp_var_header .= thorderby('var_value', 'Value', $orderby, $order);
	//$tmp_var_header .= thorderby('var_cat', 'Category', $orderby, $order);
	//$tmp_var_header .= thorderby('var_order', 'Order', $orderby, $order);
	$tmp_var_header .= thorderby('var_enabled', 'Enabled', $orderby, $order);
	$tmp_var_header .= "<th>Description</th>\n";
	$tmp_var_header .= "<td align='right' width='42'>\n";
	$tmp_var_header .= "	<a href='v_vars_edit.php' alt='add'><img src='".$v_icon_add."' width='17' height='17' border='0' alt='add'></a>\n";
	//$tmp_var_header .= "	<input type='button' class='btn' name='' alt='add' onclick=\"window.location='v_vars_edit.php'\" value='+'>\n";
	$tmp_var_header .= "</td>\n";
	$tmp_var_header .= "<tr>\n";

	if ($resultcount == 0) { //no results
	}
	else { //received results
		$prev_var_cat = '';
		foreach($result as $row) {
			if ($prev_var_cat != $row[var_cat]) {
				$c=0;
				if (strlen($prev_var_cat) > 0) {
					echo "<tr>\n";
					echo "<td colspan='5'>\n";
					echo "	<table width='100%' cellpadding='0' cellspacing='0'>\n";
					echo "	<tr>\n";
					echo "		<td width='33.3%' nowrap>&nbsp;</td>\n";
					echo "		<td width='33.3%' align='center' nowrap>&nbsp;</td>\n";
					echo "		<td width='33.3%' align='right'>\n";
					echo "			<a href='v_vars_edit.php' alt='add'><img src='".$v_icon_add."' width='17' height='17' border='0' alt='add'></a>\n";
					echo "		</td>\n";
					echo "	</tr>\n";
					echo "	</table>\n";
					echo "</td>\n";
					echo "</tr>\n";
				}

				echo "<tr><td colspan='4' align='left'>\n";
				echo "	<br />\n";
				echo "	<br />\n";
				echo "	<b>".$row[var_cat]."</b>&nbsp;</td></tr>\n";
				echo $tmp_var_header;
			}

			//print_r( $row );
			echo "<tr >\n";
			echo "	<td valign='top' align='left' class='".$rowstyle[$c]."'>".$row[var_name]."</td>\n";
			echo "	<td valign='top' align='left' class='".$rowstyle[$c]."'>".$row[var_value]."</td>\n";
			//echo "	<td valign='top' align='left' class='".$rowstyle[$c]."'>".$row[var_cat]."</td>\n";
			//echo "	<td valign='top' align='left' class='".$rowstyle[$c]."'>".$row[var_order]."</td>\n";
			echo "	<td valign='top' align='left' class='".$rowstyle[$c]."'>".$row[var_enabled]."</td>\n";

			$var_desc = str_replace("\n", "<br />", trim(base64_decode($row[var_desc])));
			$var_desc = str_replace("   ", "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $var_desc);
			echo "	<td valign='top' align='left' class='".$rowstyle[$c]."'>".$var_desc."&nbsp;</td>\n";
			echo "	<td valign='top' align='right'>\n";
			echo "		<a href='v_vars_edit.php?id=".$row[var_id]."' alt='edit'><img src='".$v_icon_edit."' width='17' height='17' alt='edit' border='0'></a>\n";
			echo "		<a href='v_vars_delete.php?id=".$row[var_id]."' alt='delete' onclick=\"return confirm('Do you really want to delete this?')\"><img src='".$v_icon_delete."' width='17' height='17' alt='delete' border='0'></a>\n";
			//echo "		<input type='button' class='btn' name='' alt='edit' onclick=\"window.location='v_vars_edit.php?id=".$row[var_id]."'\" value='e'>\n";
			//echo "		<input type='button' class='btn' name='' alt='delete' onclick=\"if (confirm('Are you sure you want to delete this?')) { window.location='v_vars_delete.php?id=".$row[var_id]."' }\" value='x'>\n";
			echo "	</td>\n";
			echo "</tr>\n";

			$prev_var_cat = $row[var_cat];
			if ($c==0) { $c=1; } else { $c=0; }
		} //end foreach
		unset($sql, $result, $rowcount);
	} //end if results


	echo "<tr>\n";
	echo "<td colspan='6' align='left'>\n";
	echo "	<table width='100%' cellpadding='0' cellspacing='0'>\n";
	echo "	<tr>\n";
	echo "		<td width='33.3%' nowrap>&nbsp;</td>\n";
	echo "		<td width='33.3%' align='center' nowrap>$pagingcontrols</td>\n";
	echo "		<td width='33.3%' align='right'>\n";
	echo "			<a href='v_vars_edit.php' alt='add'><img src='".$v_icon_add."' width='17' height='17' border='0' alt='add'></a>\n";
	//echo "			<input type='button' class='btn' name='' alt='add' onclick=\"window.location='v_vars_edit.php'\" value='+'>\n";
	echo "		</td>\n";
	echo "	</tr>\n";
 	echo "	</table>\n";
	echo "</td>\n";
	echo "</tr>\n";


	echo "</table>";
	echo "</div>";
	echo "<br><br>";
	echo "<br><br>";


	echo "</td>";
	echo "</tr>";
	echo "</table>";
	echo "</div>";
	echo "<br><br>";


require_once "includes/footer.php";
unset ($resultcount);
unset ($result);
unset ($key);
unset ($val);
unset ($c);
?>
