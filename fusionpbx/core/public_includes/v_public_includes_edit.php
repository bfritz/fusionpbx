<?php
/* $Id$ */
/*
	v_public_includes_edit.php
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
require_once "includes/paging.php";

if (ifgroup("admin") || ifgroup("superadmin")) {
	//access granted
}
else {
	echo "access denied";
	exit;
}


//Action add or update
if (isset($_REQUEST["id"])) {
	$action = "update";
	$public_include_id = checkstr($_REQUEST["id"]);
}
else {
	$action = "add";
}

//POST to PHP variables
if (count($_POST)>0) {
	//$v_id = checkstr($_POST["v_id"]);
	$extensionname = checkstr($_POST["extensionname"]);
	$publicorder = checkstr($_POST["publicorder"]);
	$context = checkstr($_POST["context"]);
	$enabled = checkstr($_POST["enabled"]);
	$descr = checkstr($_POST["descr"]);
}

if (count($_POST)>0 && strlen($_POST["persistformvar"]) == 0) {

	$msg = '';

	////recommend moving this to the config.php file
	$uploadtempdir = $_ENV["TEMP"]."\\";
	ini_set('upload_tmp_dir', $uploadtempdir);
	////$imagedir = $_ENV["TEMP"]."\\";
	////$filedir = $_ENV["TEMP"]."\\";

	if ($action == "update") {
		$public_include_id = checkstr($_POST["public_include_id"]);
	}

	//check for all required data
		if (strlen($v_id) == 0) { $msg .= "Please provide: v_id<br>\n"; }
		if (strlen($extensionname) == 0) { $msg .= "Please provide: Extension Name<br>\n"; }
		if (strlen($publicorder) == 0) { $msg .= "Please provide: Order<br>\n"; }
		//if (strlen($context) == 0) { $msg .= "Please provide: Context<br>\n"; }
		if (strlen($enabled) == 0) { $msg .= "Please provide: Enabled<br>\n"; }
		//if (strlen($descr) == 0) { $msg .= "Please provide: Description<br>\n"; }
		if (strlen($msg) > 0 && strlen($_POST["persistformvar"]) == 0) {
			require_once "includes/header.php";
			require_once "includes/persistformvar.php";
			echo "<div align='center'>\n";
			echo "<table><tr><td>\n";
			echo $msg."<br />";
			echo "</td></tr></table>\n";
			persistformvar($_POST);
			echo "</div>\n";
			require_once "includes/footer.php";
			return;
		}

	$tmp = "\n";
	//$tmp .= "v_id: $v_id\n";
	$tmp .= "Extension Name: $extensionname\n";
	$tmp .= "Order: $publicorder\n";
	$tmp .= "Context: $context\n";
	$tmp .= "Enabled: $enabled\n";
	$tmp .= "Description: $descr\n";

	//Add or update the database
	if ($_POST["persistformvar"] != "true") {
		if ($action == "add") {
			$sql = "insert into v_public_includes ";
			$sql .= "(";
			$sql .= "v_id, ";
			$sql .= "extensionname, ";
			$sql .= "publicorder, ";
			$sql .= "context, ";
			$sql .= "enabled, ";
			$sql .= "descr ";
			$sql .= ")";
			$sql .= "values ";
			$sql .= "(";
			$sql .= "'$v_id', ";
			$sql .= "'$extensionname', ";
			$sql .= "'$publicorder', ";
			$sql .= "'default', ";
			$sql .= "'$enabled', ";
			$sql .= "'$descr' ";
			$sql .= ")";
			$db->exec($sql);
			//$lastinsertid = $db->lastInsertId($id);
			unset($sql);

			//synchronize the xml config
			sync_package_v_public_includes();

			require_once "includes/header.php";
			echo "<meta http-equiv=\"refresh\" content=\"2;url=v_public_includes.php\">\n";
			echo "<div align='center'>\n";
			echo "Add Complete\n";
			echo "</div>\n";
			require_once "includes/footer.php";
			return;
		} //if ($action == "add")

		if ($action == "update") {

			$sql = "";
			$sql .= "select * from v_public_includes ";
			$sql .= "where v_id = '$v_id' ";
			$sql .= "and public_include_id = '$public_include_id' ";
			$prepstatement = $db->prepare($sql);
			$prepstatement->execute();
			$result = $prepstatement->fetchAll();
			foreach ($result as &$row) {
				$orig_extensionname = $row["extensionname"];
				$orig_publicorder = $row["publicorder"];
				//$context = $row["context"];
				//$enabled = $row["enabled"];
				//$descr = $row["descr"];
				break; //limit to 1 row
			}
			unset ($prepstatement, $sql);

			$publicincludefilename = $orig_publicorder."_".$orig_extensionname.".xml";
			if (file_exists($v_conf_dir."/dialplan/public/".$publicincludefilename)) {
				unlink($v_conf_dir."/dialplan/public/".$publicincludefilename);
			}
			unset($publicincludefilename, $orig_publicorder, $orig_extensionname);

			$sql = "update v_public_includes set ";
			//$sql .= "v_id = '$v_id', ";
			$sql .= "extensionname = '$extensionname', ";
			$sql .= "publicorder = '$publicorder', ";
			$sql .= "context = '$context', ";
			$sql .= "enabled = '$enabled', ";
			$sql .= "descr = '$descr' ";
			$sql .= "where public_include_id = '$public_include_id'";
			$db->exec($sql);
			unset($sql);



			//synchronize the xml config
			sync_package_v_public_includes();

			require_once "includes/header.php";
			echo "<meta http-equiv=\"refresh\" content=\"2;url=v_public_includes.php\">\n";
			echo "<div align='center'>\n";
			echo "Update Complete\n";
			echo "</div>\n";
			require_once "includes/footer.php";
			return;
		} //if ($action == "update")
	} //if ($_POST["persistformvar"] != "true") { 

} //(count($_POST)>0 && strlen($_POST["persistformvar"]) == 0)

	//Pre-populate the form
	if (count($_GET)>0 && $_POST["persistformvar"] != "true") {
		$public_include_id = $_GET["id"];
		$sql = "";
		$sql .= "select * from v_public_includes ";
		$sql .= "where v_id = '$v_id' ";
		$sql .= "and public_include_id = '$public_include_id' ";
		$prepstatement = $db->prepare($sql);
		$prepstatement->execute();
		$result = $prepstatement->fetchAll();
		foreach ($result as &$row) {
			//$v_id = $row["v_id"];
			$extensionname = $row["extensionname"];
			$publicorder = $row["publicorder"];
			$context = $row["context"];
			$enabled = $row["enabled"];
			$descr = $row["descr"];
			break; //limit to 1 row
		}
		unset ($prepstatement);
	}


	require_once "includes/header.php";

	echo "<script language='javascript' src='/includes/calendar_popcalendar.js'></script>\n";
	echo "<script language='javascript' src='/includes/calendar_lw_layers.js'></script>\n";
	echo "<script language='javascript' src='/includes/calendar_lw_menu.js'></script>\n";

	echo "<div align='center'>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='2'>\n";

	echo "<tr class='border'>\n";
	echo "	<td align='left'>\n";
	echo "      <br>";




	//Write the HTML form
	echo "<form method='post' name='frm' action=''>\n";

	echo "<div align='center'>\n";
	echo "<table width='100%'  border='0' cellpadding='6' cellspacing='0'>\n";

	echo "<tr>\n";
	if ($action == "add") {
		echo "<td width='30%' align='left'nowrap><b>Public Include Add</b></td>\n";
	}
	if ($action == "update") {
		echo "<td width='30%' align='left' nowrap><b>Public Include Update</b></td>\n";
	}
	echo "<td width='70%' align='right'><input type='button' class='btn' name='' alt='back' onclick=\"window.location='v_public_includes.php'\" value='Back'></td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Name:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' type='text' name='extensionname' maxlength='255' value=\"$extensionname\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Order:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "              <select name='publicorder' class='formfld'>\n";
	//echo "              <option></option>\n";
	if (strlen(htmlspecialchars($publicorder))> 0) {
		echo "              <option selected='yes' value='".htmlspecialchars($publicorder)."'>".htmlspecialchars($publicorder)."</option>\n";
	}
	$i=0;
	while($i<=999) {
	  if (strlen($i) == 1) {
		echo "              <option value='00$i'>00$i</option>\n";
	  }
	  if (strlen($i) == 2) {
		echo "              <option value='0$i'>0$i</option>\n";
	  }
	  if (strlen($i) == 3) {
		echo "              <option value='$i'>$i</option>\n";
	  }

	  $i++;
	}
	echo "              </select>\n";
	//echo "  <input class='formfld' type='text' name='publicorder' maxlength='255' value='$publicorder'>\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	//echo "<tr>\n";
	//echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	//echo "    Context:\n";
	//echo "</td>\n";
	//echo "<td class='vtable' align='left'>\n";
	//echo "    <input class='formfld' type='text' name='context' maxlength='255' value=\"$context\">\n";
	//echo "<br />\n";
	//echo "\n";
	//echo "</td>\n";
	//echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Enabled:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <select class='formfld' name='enabled'>\n";
	echo "    <option value=''></option>\n";
	if ($enabled == "true") { 
		echo "    <option value='true' SELECTED >true</option>\n";
	}
	else {
		echo "    <option value='true'>true</option>\n";
	}
	if ($enabled == "false") { 
		echo "    <option value='false' SELECTED >false</option>\n";
	}
	else {
		echo "    <option value='false'>false</option>\n";
	}
	echo "    </select>\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "    Description:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <textarea class='formfld' name='descr' rows='4'>$descr</textarea>\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "	<tr>\n";
	echo "		<td colspan='2' align='right'>\n";
	if ($action == "update") {
		echo "				<input type='hidden' name='public_include_id' value='$public_include_id'>\n";
	}
	echo "				<input type='submit' name='submit' class='btn' value='Save'>\n";
	echo "		</td>\n";
	echo "	</tr>";
	echo "</table>";
	echo "</form>";


	echo "	</td>";
	echo "	</tr>";
	echo "</table>";
	echo "</div>";

//---- begin: v_public_details ---------------------------
if ($action == "update") {
	echo "<div align='center'>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='2'>\n";

	echo "<tr class='border'>\n";
	echo "	<td align=\"center\">\n";
	echo "      <br>";


	//echo "<table width='100%' border='0'><tr>\n";
	//echo "<td width='50%' nowrap><b>Conditions and Actions</b></td>\n";
	//echo "<td width='50%' align='right'>&nbsp;</td>\n";
	//echo "</tr></table>\n";
	echo "<table width=\"100%\" border=\"0\" cellpadding=\"6\" cellspacing=\"0\">\n";
	echo "  <tr>\n";
	echo "    <td align='left'><p><span class=\"vexpl\"><span class=\"red\"><strong>Conditions and Actions<br />\n";
	echo "        </strong></span>\n";
	echo "        The following conditions, actions and anti-actions are used in the public to direct call flow. Each is processed in order until you reach the action tag which tells the system what action to perform. You are not limited to only one condition or action tag for a given extension.\n";
	echo "        </span></p></td>\n";
	echo "  </tr>\n";
	echo "</table>";
	echo "<br />\n";


	$sql = "";
	$sql .= " select * from v_public_includes_details ";
	$sql .= " where public_include_id = '$public_include_id' ";
	$sql .= " and v_id = $v_id ";
	$sql .= " and tag = 'condition' ";
	$sql .= " order by fieldorder asc";
	//if (strlen($orderby)> 0) { $sql .= "order by $orderby $order "; }
	//$sql .= " limit $rowsperpage offset $offset ";
	//echo $sql;
	$prepstatement = $db->prepare($sql);
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	$resultcount = count($result);
	unset ($prepstatement, $sql);

	$c = 0;
	$rowstyle["0"] = "rowstyle0";
	$rowstyle["1"] = "rowstyle1";

	echo "<div align='center'>\n";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";

	echo "<tr>\n";
	echo "<th align='center'>Tag</th>\n";
	echo "<th align='center'>Type</th>\n";
	echo "<th align='center'>Data</th>\n";
	echo "<th align='center'>Order</th>\n";
	echo "<td align='right' width='42'>\n";
	echo "	<a href='v_public_includes_details_edit.php?id2=".$public_include_id."' alt='add'><img src='".$v_icon_add."' width='17' height='17' border='0' alt='add'></a>\n";
	echo "</td>\n";
	echo "<tr>\n";

	if ($resultcount == 0) { //no results
	}
	else { //received results
		foreach($result as $row) {
			//print_r( $row );
			echo "<tr >\n";
			echo "   <td valign='top' class='".$rowstyle[$c]."'>&nbsp;&nbsp;".$row[tag]."</td>\n";
			echo "   <td valign='top' class='".$rowstyle[$c]."'>&nbsp;&nbsp;".$row[fieldtype]."</td>\n";
			echo "   <td valign='top' class='".$rowstyle[$c]."'>&nbsp;&nbsp;".$row[fielddata]."</td>\n";
			echo "   <td valign='top' class='".$rowstyle[$c]."'>&nbsp;&nbsp;".$row[fieldorder]."</td>\n";
			echo "   <td valign='top' align='right'>\n";
			echo "		<a href='v_public_includes_details_edit.php?id=".$row[public_includes_detail_id]."&id2=".$public_include_id."' alt='edit'><img src='".$v_icon_edit."' width='17' height='17' border='0' alt='edit'></a>\n";
			echo "		<a href='v_public_includes_details_delete.php?id=".$row[public_includes_detail_id]."&id2=".$public_include_id."' alt='delete' onclick=\"return confirm('Do you really want to delete this?')\"><img src='".$v_icon_delete."' width='17' height='17' border='0' alt='delete'></a>\n";
			echo "   </td>\n";
			echo "</tr>\n";
			if ($c==0) { $c=1; } else { $c=0; }
		} //end foreach
		unset($sql, $result, $rowcount);
	} //end if results

	//--------------------------------------------------------------------------

	$sql = "";
	$sql .= " select * from v_public_includes_details ";
	$sql .= " where public_include_id = '$public_include_id' ";
	$sql .= " and v_id = $v_id ";
	$sql .= " and tag = 'action' ";
	$sql .= " order by fieldorder asc";
	//if (strlen($orderby)> 0) { $sql .= "order by $orderby $order "; }
	//$sql .= " limit $rowsperpage offset $offset ";
	//echo $sql;
	$prepstatement = $db->prepare($sql);
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	$resultcount = count($result);
	unset ($prepstatement, $sql);

	//$c = 0;
	//$rowstyle["0"] = "rowstyle0";
	//$rowstyle["1"] = "rowstyle1";

	if ($resultcount == 0) { //no results
	}
	else { //received results
		foreach($result as $row) {
			//print_r( $row );
			echo "<tr >\n";
			echo "   <td valign='top' class='".$rowstyle[$c]."'>&nbsp;&nbsp;".$row[tag]."</td>\n";
			echo "   <td valign='top' class='".$rowstyle[$c]."'>&nbsp;&nbsp;".$row[fieldtype]."</td>\n";
			echo "   <td valign='top' class='".$rowstyle[$c]."'>&nbsp;&nbsp;".$row[fielddata]."</td>\n";
			echo "   <td valign='top' class='".$rowstyle[$c]."'>&nbsp;&nbsp;".$row[fieldorder]."</td>\n";
			echo "   <td valign='top' align='right'>\n";
			echo "		<a href='v_public_includes_details_edit.php?id=".$row[public_includes_detail_id]."&id2=".$public_include_id."' alt='edit'><img src='".$v_icon_edit."' width='17' height='17' border='0' alt='edit'></a>\n";
			echo "		<a href='v_public_includes_details_delete.php?id=".$row[public_includes_detail_id]."&id2=".$public_include_id."' alt='delete' onclick=\"return confirm('Do you really want to delete this?')\"><img src='".$v_icon_delete."' width='17' height='17' border='0' alt='delete'></a>\n";
			echo "   </td>\n";
			echo "</tr>\n";
			if ($c==0) { $c=1; } else { $c=0; }
		} //end foreach
		unset($sql, $result, $rowcount);
	} //end if results

	//--------------------------------------------------------------------------

	$sql = "";
	$sql .= " select * from v_public_includes_details ";
	$sql .= " where public_include_id = '$public_include_id' ";
	$sql .= " and v_id = $v_id ";
	$sql .= " and tag = 'anti-action' ";
	$sql .= " order by fieldorder asc";
	//if (strlen($orderby)> 0) { $sql .= "order by $orderby $order "; }
	//$sql .= " limit $rowsperpage offset $offset ";
	//echo $sql;
	$prepstatement = $db->prepare($sql);
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	$resultcount = count($result);
	unset ($prepstatement, $sql);

	//$c = 0;
	//$rowstyle["0"] = "rowstyle0";
	//$rowstyle["1"] = "rowstyle1";

	if ($resultcount == 0) { //no results
	}
	else { //received results
		foreach($result as $row) {
			//print_r( $row );
			echo "<tr >\n";
			echo "	<td valign='top' class='".$rowstyle[$c]."'>&nbsp;&nbsp;".$row[tag]."</td>\n";
			echo "	<td valign='top' class='".$rowstyle[$c]."'>&nbsp;&nbsp;".$row[fieldtype]."</td>\n";
			echo "	<td valign='top' class='".$rowstyle[$c]."'>&nbsp;&nbsp;".$row[fielddata]."</td>\n";
			echo "	<td valign='top' class='".$rowstyle[$c]."'>&nbsp;&nbsp;".$row[fieldorder]."</td>\n";
			echo "	<td valign='top' align='right'>\n";
			echo "		<a href='v_public_includes_details_edit.php?id=".$row[public_includes_detail_id]."&id2=".$public_include_id."' alt='edit'><img src='".$v_icon_edit."' width='17' height='17' border='0' alt='edit'></a>\n";
			echo "		<a href='v_public_includes_details_delete.php?id=".$row[public_includes_detail_id]."&id2=".$public_include_id."' alt='delete' onclick=\"return confirm('Do you really want to delete this?')\"><img src='".$v_icon_delete."' width='17' height='17' border='0' alt='delete'></a>\n";
			echo "	</td>\n";
			echo "</tr>\n";
			if ($c==0) { $c=1; } else { $c=0; }
		} //end foreach
		unset($sql, $result, $rowcount);
	} //end if results

	echo "<tr>\n";
	echo "<td colspan='5'>\n";
	echo "	<table width='100%' cellpadding='0' cellspacing='0'>\n";
	echo "	<tr>\n";
	echo "		<td width='33.3%' nowrap>&nbsp;</td>\n";
	echo "		<td width='33.3%' align='center' nowrap>$pagingcontrols</td>\n";
	echo "		<td width='33.3%' align='right'>\n";
	echo "			<a href='v_public_includes_details_edit.php?id2=".$public_include_id."' alt='add'><img src='".$v_icon_add."' width='17' height='17' border='0' alt='add'></a>\n";
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
} //end if update
//---- end: v_public_details ---------------------------

require_once "includes/footer.php";
?>
