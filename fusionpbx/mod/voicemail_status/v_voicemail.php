<?php
/* $Id$ */
/*
	v_voicemail_settings.php
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
require "includes/config.php";
require_once "includes/checkauth.php";
if (ifgroup("admin") || ifgroup("superadmin") || ifgroup("member")) {
	//access granted
}
else {
	echo "access denied";
	exit;
}
require_once "includes/header.php";
require_once "includes/paging.php";

$orderby = $_GET["orderby"];
$order = $_GET["order"];


	echo "<div align='center'>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='2'>\n";

	echo "<tr class='border'>\n";
	echo "	<td align=\"center\">\n";
	echo "		<br>";

	echo "<table width=\"100%\" border=\"0\" cellpadding=\"6\" cellspacing=\"0\">\n";
	echo "	<tr>\n";
	echo "	<td align='left'><b>Voicemail</b><br>\n";
	//echo "		Use this to configure your SIP extensions.\n";
	echo "	</td>\n";
	echo "	</tr>\n";
	echo "</table>\n";
	echo "<br />";


	$c = 0;
	$rowstyle["0"] = "rowstyle0";
	$rowstyle["1"] = "rowstyle1";

	echo "<div align='center'>\n";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";

	echo "<tr>\n";
	echo thorderby('mailbox', 'Mailbox', $orderby, $order);
	echo thorderby('vm_mailto', 'Voicemail Mail To', $orderby, $order);
	echo "<th>Messages</th>\n";
	echo thorderby('enabled', 'Enabled', $orderby, $order);
	echo thorderby('description', 'Description', $orderby, $order);
	//echo "<td align='right' width='42'>\n";
	//echo "	<a href='v_extensions_edit.php' alt='add'><img src='".$v_icon_add."' width='17' height='17' border='0' alt='add'></a>\n";
	//echo "</td>\n";
	echo "<tr>\n";


	$sql = "";
	$sql .= "select * from v_extensions ";
	$sql .= "where v_id = '$v_id' ";
	$sql .= "and user_list like '%".$_SESSION["username"]."%' ";
	if (strlen($orderby)> 0) { 
		$sql .= "order by $orderby $order ";
	}
	else {
		$sql .= "order by extension asc ";
	}
	$prepstatement = $db->prepare($sql);
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	$numrows = count($result);
	unset ($prepstatement, $result, $sql);

	$rowsperpage = 20;
	$param = "";
	$page = $_GET['page'];
	if (strlen($page) == 0) { $page = 0; $_GET['page'] = 0; }
	list($pagingcontrols, $rowsperpage, $var3) = paging($numrows, $param, $rowsperpage); 
	$offset = $rowsperpage * $page; 

	$sql = "";
	$sql .= "select * from v_extensions ";
	$sql .= "where v_id = '$v_id' ";
	$sql .= "and user_list like '%".$_SESSION["username"]."%' ";
	if (strlen($orderby)> 0) {
		$sql .= "order by $orderby $order ";
	}
	else {
		$sql .= "order by extension asc ";
	}
	$sql .= " limit $rowsperpage offset $offset ";
	$prepstatement = $db->prepare($sql);
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	$resultcount = count($result);
	unset ($prepstatement, $sql);


	if ($resultcount == 0) { //no results
	}
	else { //received results
		foreach($result as $row) {

			try {
				unset($db);
				//$db = new PDO('sqlite::memory:'); //sqlite 3
				$db = new PDO('sqlite:'.$v_db_dir.'/voicemail_default.db'); //sqlite 3
			}
			catch (PDOException $error) {
				print "error: " . $error->getMessage() . "<br/>";
				die();
			}

			$sql = "";
			$sql .= "select count(*) as count from voicemail_msgs ";
			$sql .= "where username = '".$row[mailbox]."' ";
			//echo $sql;
			$prepstatement = $db->prepare($sql);
			$prepstatement->execute();
			$result = $prepstatement->fetchAll();
			foreach ($result as &$row2) {
				$count = $row2["count"];
				break; //limit to 1 row
			}
			unset ($prepstatement);

			//print_r( $row );
			echo "<tr >\n";
			echo "	<td valign='top' class='".$rowstyle[$c]."'>".$row[mailbox]."</td>\n";
			echo "	<td valign='top' class='".$rowstyle[$c]."'>".$row[vm_mailto]."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$rowstyle[$c]."'>".$count."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$rowstyle[$c]."'>".$row[enabled]."</td>\n";
			echo "	<td valign='top' class='rowstylebg' width='30%'>".$row[description]."&nbsp;</td>\n";
			echo "	<td valign='top' align='right'>\n";
			//echo "		<a href='v_extensions_edit.php?id=".$row[extension_id]."' alt='edit'><img src='".$v_icon_edit."' width='17' height='17' alt='edit' border='0'></a>\n";
			//echo "		<a href='v_extensions_delete.php?id=".$row[extension_id]."' alt='delete' onclick=\"return confirm('Do you really want to delete this?')\"><img src='".$v_icon_delete."' width='17' height='17' alt='delete' border='0'></a>\n";
			echo "		<a href='v_voicemail_prefs_delete.php?id=".$row[extension_id]."' alt='restore default preferences' title='restore default preferences' onclick=\"return confirm('Are you sure you want remove the voicemail name and greeting?')\"><img src='".$v_icon_delete."' width='17' height='17' alt='delete' border='0'></a>\n";
			echo "	</td>\n";
			echo "</tr>\n";

			unset($count);
			if ($c==0) { $c=1; } else { $c=0; }
		} //end foreach
		unset($sql, $result, $rowcount);
	} //end if results


	//echo "<tr>\n";
	//echo "<td colspan='5' align='left'>\n";
	//echo "	<table border='0' width='100%' cellpadding='0' cellspacing='0'>\n";
	//echo "	<tr>\n";
	//echo "		<td width='33.3%' nowrap>&nbsp;</td>\n";
	//echo "		<td width='33.3%' align='center' nowrap>$pagingcontrols</td>\n";
	//echo "		<td width='33.3%' align='right'>\n";
	//echo "			<a href='v_extensions_edit.php' alt='add'><img src='".$v_icon_add."' width='17' height='17' border='0' alt='add'></a>\n";
	//echo "		</td>\n";
	//echo "	</tr>\n";
	//echo "	</table>\n";
	//echo "</td>\n";
	//echo "</tr>\n";

	//echo "<tr>\n";
	//echo "<td colspan='5' align='left'>\n";
	//echo "<br />\n";
	//echo "<br />\n";
	//if ($v_path_show) {
	//	echo $v_conf_dir."/directory/default/\n";
	//}
	//echo "</td>\n";
	//echo "</tr>\n";


echo "</table>";
echo "</div>";
echo "<br><br>";
echo "<br><br>";


echo "</td>";
echo "</tr>";
echo "</table>";
echo "</div>";
echo "<br><br>";

require "includes/config.php";
require_once "includes/footer.php";
unset ($resultcount);
unset ($result);
unset ($key);
unset ($val);
unset ($c);
?>
