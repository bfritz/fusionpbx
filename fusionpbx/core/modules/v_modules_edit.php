<?php
/* $Id$ */
/*
	v_modules_edit.php
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
	$module_id = checkstr($_REQUEST["id"]);
}
else {
	$action = "add";
}

//POST to PHP variables
if (count($_POST)>0) {
	//$v_id = checkstr($_POST["v_id"]);
	$modulelabel = checkstr($_POST["modulelabel"]);
	$modulename = checkstr($_POST["modulename"]);
	$moduledesc = checkstr($_POST["moduledesc"]);
	$modulecat = checkstr($_POST["modulecat"]);
	$moduleenabled = checkstr($_POST["moduleenabled"]);
	$moduledefaultenabled = checkstr($_POST["moduledefaultenabled"]);
}

if (count($_POST)>0 && strlen($_POST["persistformvar"]) == 0) {

	$msg = '';

	////recommend moving this to the config.php file
	$uploadtempdir = $_ENV["TEMP"]."\\";
	ini_set('upload_tmp_dir', $uploadtempdir);
	////$imagedir = $_ENV["TEMP"]."\\";
	////$filedir = $_ENV["TEMP"]."\\";

	if ($action == "update") {
		$module_id = checkstr($_POST["module_id"]);
	}

	//check for all required data
		if (strlen($v_id) == 0) { $msg .= "Please provide: v_id<br>\n"; }
		if (strlen($modulelabel) == 0) { $msg .= "Please provide: Label<br>\n"; }
		if (strlen($modulename) == 0) { $msg .= "Please provide: Module Name<br>\n"; }
		//if (strlen($moduledesc) == 0) { $msg .= "Please provide: Description<br>\n"; }
		if (strlen($modulecat) == 0) { $msg .= "Please provide: Module Category<br>\n"; }
		if (strlen($moduleenabled) == 0) { $msg .= "Please provide: Enabled<br>\n"; }
		if (strlen($moduledefaultenabled) == 0) { $msg .= "Please provide: Default Enabled<br>\n"; }
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
	$tmp .= "v_id: $v_id\n";
	$tmp .= "Label: $modulelabel\n";
	$tmp .= "Module Name: $modulename\n";
	$tmp .= "Description: $moduledesc\n";
	$tmp .= "Module Category: $modulecat\n";
	$tmp .= "Enabled: $moduleenabled\n";
	$tmp .= "Default Enabled: $moduledefaultenabled\n";



//Add or update the database
if ($_POST["persistformvar"] != "true") {
	if ($action == "add") {
		$sql = "insert into v_modules ";
		$sql .= "(";
		$sql .= "v_id, ";
		$sql .= "modulelabel, ";
		$sql .= "modulename, ";
		$sql .= "moduledesc, ";
		$sql .= "modulecat, ";
		$sql .= "moduleenabled, ";
		$sql .= "moduledefaultenabled ";
		$sql .= ")";
		$sql .= "values ";
		$sql .= "(";
		$sql .= "'$v_id', ";
		$sql .= "'$modulelabel', ";
		$sql .= "'$modulename', ";
		$sql .= "'$moduledesc', ";
		$sql .= "'$modulecat', ";
		$sql .= "'$moduleenabled', ";
		$sql .= "'$moduledefaultenabled' ";
		$sql .= ")";
		$db->exec($sql);
		//$lastinsertid = $db->lastInsertId($id);
		unset($sql);

		sync_package_v_modules();

		require_once "includes/header.php";
		echo "<meta http-equiv=\"refresh\" content=\"2;url=v_modules.php\">\n";
		echo "<div align='center'>\n";
		echo "Add Complete\n";
		echo "</div>\n";
		require_once "includes/footer.php";
		return;
	} //if ($action == "add")

	if ($action == "update") {
		$sql = "update v_modules set ";
		$sql .= "modulelabel = '$modulelabel', ";
		$sql .= "modulename = '$modulename', ";
		$sql .= "moduledesc = '$moduledesc', ";
		$sql .= "modulecat = '$modulecat', ";
		$sql .= "moduleenabled = '$moduleenabled', ";
		$sql .= "moduledefaultenabled = '$moduledefaultenabled' ";
		$sql .= "where v_id = '$v_id'";
		$sql .= "and module_id = '$module_id'";
		$db->exec($sql);
		unset($sql);

		sync_package_v_modules();

		require_once "includes/header.php";
		echo "<meta http-equiv=\"refresh\" content=\"2;url=v_modules.php\">\n";
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
	$module_id = $_GET["id"];
	$sql = "";
	$sql .= "select * from v_modules ";
	$sql .= "where v_id = '$v_id' ";
	$sql .= "and module_id = '$module_id' ";
	$prepstatement = $db->prepare($sql);
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	foreach ($result as &$row) {
		$v_id = $row["v_id"];
		$modulelabel = $row["modulelabel"];
		$modulename = $row["modulename"];
		$moduledesc = $row["moduledesc"];
		$modulecat = $row["modulecat"];
		$moduleenabled = $row["moduleenabled"];
		$moduledefaultenabled = $row["moduledefaultenabled"];
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
	echo "	<td align=\"left\">\n";
	echo "      <br>";


	echo "<form method='post' name='frm' action=''>\n";

	echo "<div align='center'>\n";
	echo "<table width='100%'  border='0' cellpadding='6' cellspacing='0'>\n";

	echo "<tr>\n";
	if ($action == "add") {
		echo "<td align='left' width='30%' nowrap><b>Module Add</b></td>\n";
	}
	if ($action == "update") {
		echo "<td align='left' width='30%' nowrap><b>Module Update</b></td>\n";
	}
	echo "<td width='70%' align='right'><input type='button' class='btn' name='' alt='back' onclick=\"window.location='v_modules.php'\" value='Back'></td>\n";
	echo "</tr>\n";

	//echo "<tr>\n";
	//echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	//echo "    v_id:\n";
	//echo "</td>\n";
	//echo "<td class='vtable' align='left'>\n";
	//echo "  <input class='formfld' type='text' name='v_id' maxlength='255' value='$v_id'>\n";
	//echo "<br />\n";
	//echo "\n";
	//echo "</td>\n";
	//echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Label:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' type='text' name='modulelabel' maxlength='255' value=\"$modulelabel\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Module Name:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' type='text' name='modulename' maxlength='255' value=\"$modulename\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "    Description:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' type='text' name='moduledesc' maxlength='255' value=\"$moduledesc\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Module Category:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' type='text' name='modulecat' maxlength='255' value=\"$modulecat\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Enabled:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <select class='formfld' name='moduleenabled'>\n";
	echo "    <option value=''></option>\n";
	if ($moduleenabled == "true") { 
		echo "    <option value='true' SELECTED >true</option>\n";
	}
	else {
		echo "    <option value='true'>true</option>\n";
	}
	if ($moduleenabled == "false") { 
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
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Default Enabled:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <select class='formfld' name='moduledefaultenabled'>\n";
	echo "    <option value=''></option>\n";
	if ($moduledefaultenabled == "true") { 
		echo "    <option value='true' SELECTED >true</option>\n";
	}
	else {
		echo "    <option value='true'>true</option>\n";
	}
	if ($moduledefaultenabled == "false") { 
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
	echo "	<tr>\n";
	echo "		<td colspan='2' align='right'>\n";
	if ($action == "update") {
		echo "				<input type='hidden' name='module_id' value='$module_id'>\n";
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


require_once "includes/footer.php";
?>
