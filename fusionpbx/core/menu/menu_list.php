<?php
/* $Id$ */
/*
	menu_list.php
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

if (!ifgroup("superadmin")) {
	echo "access denied";
	return;
}

$tmp_menuorder = 0;

function builddbchildmenulist ($db, $menulevel, $menuid, $c) {
	global $v_id, $tmp_menuorder, $v_icon_edit, $v_icon_delete;
	//--- Begin check for children -----------------------------------------

		$menulevel = $menulevel+1;

		$sql = "select * from v_menu ";
		$sql .= "where v_id = '".$v_id."' ";
		$sql .= "and menuparentid = '".$menuid."' ";
		$sql .= "order by menuorder asc ";
		//echo $sql."<br><br>\n";

		$prepstatement2 = $db->prepare($sql);
		$prepstatement2->execute();
		$result2 = $prepstatement2->fetchAll();
		//echo "count: ". count($result2)."<br><br>\n\n";

		//$c = 0;
		$rowstyle["0"] = "rowstyle1";
		$rowstyle["1"] = "rowstyle1";

		if (count($result2) > 0) {

			if ($c==0) { $c2=1; } else { $c2=0; }
			foreach($result2 as $row2) {

				//print_r( $row );
				$menuid = $row2[menuid];
				$menuparentid = $row2[menuparentid];                    
				$menucategory = $row2[menucategory];
				$menugroup = $row2[menugroup];
				if (strlen($menugroup)==0) {
					$menugroup = 'public';
				}
				$menutitle = $row2[menutitle];
				$menustr = $row2[menustr];
				switch ($menucategory) {
					case "internal":
						$menutitle = "<a href='".PROJECT_PATH."$menustr'>$menutitle</a>";
						break;
					case "external":
						$menutitle = "<a href='$menustr' target='_blank'>$menutitle</a>";
						break;
					case "email":
						$menutitle = "<a href='mailto:$menustr'>$menutitle</a>";
						break;
				}

				echo "<tr'>\n";
					//echo "<td valign='top' class='".$rowstyle[$c]."'>&nbsp;<a href='menuupdate.php?menuid=".$row2[menuid]."'>".$row2[menuid]."&nbsp;</a></td>";
					echo "<td valign='top' class='".$rowstyle[$c]."'>";
					echo "  <table cellpadding='0' cellspacing='0' border='0'>";
					echo "  <tr>";
					echo "      <td nowrap>";
					$i=0;
					while($i < $menulevel){
						echo "&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;";
						$i++;
					}
					//echo "      </td>";
					//echo "      <td>";
					//echo "       ".$menulevel." ".$menutitle."&nbsp;";
					echo "       ".$menutitle."&nbsp;";

					echo "      </td>";
					echo "  </tr>";
					echo "  </table>";
					echo "</td>";
					//echo "<td valign='top'>&nbsp;".$menustr."&nbsp;</td>";
					echo "<td valign='top' class='".$rowstyle[$c]."'>&nbsp;".$menucategory."&nbsp;</td>";
					echo "<td valign='top' class='".$rowstyle[$c]."'>&nbsp;".$menugroup."&nbsp;</td>";
					//echo "<td valign='top'>".$row[menudesc]."</td>";
					//echo "<td valign='top'>&nbsp;".$row[menuparentid]."&nbsp;</td>";
					//echo "<td valign='top'>&nbsp;".$row[menuorder]."&nbsp;</td>";

					echo "<td valign='top' align='center' nowrap class='".$rowstyle[$c]."'>";
					echo "  ".$row2[menuorder]."&nbsp;";
					echo "</td>";

					echo "<td valign='top' align='center' class='".$rowstyle[$c]."'>";
					echo "  <input type='button' class='btn' name='' onclick=\"window.location='menu_move_up.php?menuparentid=".$row2[menuparentid]."&menuid=".$row2[menuid]."&menuorder=".$row2[menuorder]."'\" value='<' title='".$row2[menuorder].". Move Up'>";
					echo "  <input type='button' class='btn' name='' onclick=\"window.location='menu_move_down.php?menuparentid=".$row2[menuparentid]."&menuid=".$row2[menuid]."&menuorder=".$row2[menuorder]."'\" value='>' title='".$row2[menuorder].". Move Down'>";
					echo "</td>";

					echo "   <td valign='top' align='right' nowrap>\n";
					echo "		<a href='menu_edit.php?menuid=".$row2[menuid]."&menuparentid=".$row2[menuparentid]."' alt='edit'><img src='".$v_icon_edit."' width='17' height='17' border='0' alt='edit'></a>\n";
					echo "		<a href='menu_delete.php?menuid=".$row2[menuid]."' onclick=\"return confirm('Do you really want to delete this?')\" alt='delete'><img src='".$v_icon_delete."' width='17' height='17' border='0' alt='delete'></a>\n";
					echo "   </td>\n";

					//echo "<td valign='top'>".$row[menuadduser]."</td>";
					//echo "<td valign='top'>".$row[menuadddate]."</td>";
					//echo "<td valign='top'>".$row[menudeluser]."</td>";
					//echo "<td valign='top'>".$row[menudeldate]."</td>";
					//echo "<td valign='top'>".$row[menumoduser]."</td>";
					//echo "<td valign='top'>".$row[menumoddate]."</td>";
				echo "</tr>";

				if ($row2[menuorder] != $tmp_menuorder) {
					$sql  = "update v_menu set ";
					$sql .= "menutitle = '".$row2[menutitle]."', ";
					$sql .= "menuorder = '".$tmp_menuorder."' ";
					$sql .= "where v_id = '".$v_id."' ";
					$sql .= "and menuid = '".$row2[menuid]."' ";
					//echo $sql."<br />\n";
					$count = $db->exec(check_sql($sql));
				}
				$tmp_menuorder++;

				//echo "menuid ".$row2[menuid]."<br>\n";
				if (strlen($menuid)> 0) {                  
				  $c = builddbchildmenulist($db, $menulevel, $menuid, $c);
				}

				if ($c==0) { $c=1; } else { $c=0; }
			} //end foreach
			unset($sql, $result2, $row2);
		}
		return $c;

	//--- End check for children -----------------------------------------
}

require_once "includes/header.php";
$orderby = $_GET["orderby"];
$order = $_GET["order"];

	echo "<div align='center'>";
	echo "<table width='90%' border='0' cellpadding='0' cellspacing='0'>\n";

	echo "<tr class='border'>\n";
	echo "	<td align=\"left\">\n";

	echo "<table width='100%' border='0'><tr>";
	echo "<td width='50%'><b>Menu Manager</b></td>";
	echo "</tr></table>";


	$sql = "";
	$sql .= "select * from v_menu ";
	$sql .= "where v_id = '".$v_id."' ";
	$sql .= "and menuparentid = '' ";
	$sql .= "or v_id = '".$v_id."' ";
	$sql .= "and menuparentid = '' ";
	if (strlen($orderby)> 0) {
		$sql .= "order by $orderby $order ";
	}
	else {
		$sql .= "order by menuorder asc ";
	}
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	$resultcount = count($result);

	$c = 0;
	$rowstyle["0"] = "rowstyle0";
	$rowstyle["1"] = "rowstyle0";

	echo "<div align='left'>\n";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";

	if ($resultcount == 0) { //no results
		echo "<tr><td>&nbsp;</td></tr>";
	}
	else { //received results

		echo "<tr>";
		echo "<th align='left' nowrap>&nbsp; Title &nbsp; &nbsp; </th>";
		echo "<th align='left'nowrap>&nbsp; Category &nbsp; &nbsp; </th>";
		echo "<th align='left' nowrap>&nbsp; Group &nbsp; &nbsp; </th>";
		//echo "<th nowrap>&nbsp; Parent &nbsp; &nbsp; </th>";
		echo "<th align='left'  width='55' nowrap>&nbsp; Order &nbsp; &nbsp; </th>";
		echo "<th nowrap width='70'>&nbsp; </th>";
		echo "<td align='right' width='42'>\n";
		echo "	<a href='menu_edit.php' alt='add'><img src='".$v_icon_add."' width='17' height='17' border='0' alt='add'></a>\n";
		echo "</td>\n";
		echo "</tr>";

		foreach($result as $row) {

			//print_r( $row );
			$menucategory = $row[menucategory];
			$menugroup = $row[menugroup];
			if (strlen($menugroup)==0) {
				$menugroup = 'public';
			}
			$menutitle = $row[menutitle];
			$menustr = $row[menustr];
			switch ($menucategory) {
				case "internal":
					$menutitle = "<a href='".PROJECT_PATH."$menustr'>$menutitle</a>";
					break;
				case "external":
					$menutitle = "<a href='$menustr' target='_blank'>$menutitle</a>";
					break;
				case "email":
					$menutitle = "<a href='mailto:$menustr'>$menutitle</a>";
					break;
			}

			echo "<tr style='".$rowstyle[$c]."'>\n";
				echo "<td valign='top' class='".$rowstyle[$c]."'>&nbsp; ".$menutitle."&nbsp;</td>";
				//echo "<td valign='top' class='".$rowstyle[$c]."'>&nbsp;".$menustr."&nbsp;</td>";
				echo "<td valign='top' class='".$rowstyle[$c]."'>&nbsp;".$menucategory."&nbsp;</td>";
				echo "<td valign='top' class='".$rowstyle[$c]."'>&nbsp;".$menugroup."&nbsp;</td>";
				//echo "<td valign='top' class='".$rowstyle[$c]."'>".$row[menudesc]."</td>";
				//echo "<td valign='top' class='".$rowstyle[$c]."'>&nbsp;".$row[menuparentid]."&nbsp;</td>";
				//echo "<td valign='top' class='".$rowstyle[$c]."'>&nbsp;".$row[menuorder]."&nbsp;</td>";

				echo "<td valign='top' align='center' nowrap class='".$rowstyle[$c]."'>";
				echo "  ".$row[menuorder]."&nbsp;";
				echo "</td>";

				echo "<td valign='top' align='center' nowrap class='".$rowstyle[$c]."'>";
				echo "  <input type='button' class='btn' name='' onclick=\"window.location='menu_move_up.php?menuparentid=".$row[menuparentid]."&menuid=".$row[menuid]."&menuorder=".$row[menuorder]."'\" value='<' title='".$row[menuorder].". Move Up'>";
				echo "  <input type='button' class='btn' name='' onclick=\"window.location='menu_move_down.php?menuparentid=".$row[menuparentid]."&menuid=".$row[menuid]."&menuorder=".$row[menuorder]."'\" value='>' title='".$row[menuorder].". Move Down'>";
				echo "</td>";

				echo "   <td valign='top' align='right' nowrap>\n";
				echo "		<a href='menu_edit.php?menuid=".$row[menuid]."' alt='edit'><img src='".$v_icon_edit."' width='17' height='17' border='0' alt='edit'></a>\n";
				echo "		<a href='menu_delete.php?menuid=".$row[menuid]."' onclick=\"return confirm('Do you really want to delete this?')\" alt='delete'><img src='".$v_icon_delete."' width='17' height='17' border='0' alt='delete'></a>\n";
				echo "   </td>\n";

				//echo "<td valign='top'>".$row[menuadduser]."</td>";
				//echo "<td valign='top'>".$row[menuadddate]."</td>";
				//echo "<td valign='top'>".$row[menudeluser]."</td>";
				//echo "<td valign='top'>".$row[menudeldate]."</td>";
				//echo "<td valign='top'>".$row[menumoduser]."</td>";
				//echo "<td valign='top'>".$row[menumoddate]."</td>";
			echo "</tr>";

			if ($row[menuorder] != $tmp_menuorder) {
				$sql  = "update v_menu set ";
				$sql .= "menutitle = '".$row[menutitle]."', ";
				$sql .= "menuorder = '".$tmp_menuorder."' ";
				$sql .= "where v_id = '".$v_id."' ";
				$sql .= "and menuid = '".$row[menuid]."' ";
				//echo $sql."<br />\n";
				$count = $db->exec(check_sql($sql));
			}
			$tmp_menuorder++;
			$menulevel = 0;
			$c = builddbchildmenulist($db, $menulevel, $row[menuid], $c);

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
	echo "			<a href='menu_edit.php' alt='add'><img src='".$v_icon_add."' width='17' height='17' border='0' alt='add'></a>\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "	</table>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "</table>\n";
	echo "</div>\n";

	echo "  <br><br>";

	echo "  </td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	//echo "<input type='button' class='btn' name='' onclick=\"window.location='menusearch.php'\" value='Search'>&nbsp; &nbsp;\n";
	//echo "<input type='button' class='btn' name='' onclick=\"window.location='menuadd.php'\" value='Add'>&nbsp; &nbsp;\n";
	echo "</div>";

	echo "<br><br>";
	require_once "includes/footer.php";

	unset ($resultcount);
	unset ($result);
	unset ($key);
	unset ($val);
	unset ($c);

?>
