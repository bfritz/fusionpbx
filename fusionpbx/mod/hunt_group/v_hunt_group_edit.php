<?php
/* $Id$ */
/*
	v_hunt_group_edit.php
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
	$hunt_group_id = checkstr($_REQUEST["id"]);
}
else {
	$action = "add";
}

//POST to PHP variables
if (count($_POST)>0) {
	//$v_id = checkstr($_POST["v_id"]);
	$huntgroupextension = checkstr($_POST["huntgroupextension"]);
	$huntgroupname = checkstr($_POST["huntgroupname"]);
	$huntgrouptype = checkstr($_POST["huntgrouptype"]);
	$huntgroupcontext = checkstr($_POST["huntgroupcontext"]);
	$huntgrouptimeout = checkstr($_POST["huntgrouptimeout"]);
	$huntgrouptimeoutdestination = checkstr($_POST["huntgrouptimeoutdestination"]);
	$huntgrouptimeouttype = checkstr($_POST["huntgrouptimeouttype"]);
	$huntgroupringback = checkstr($_POST["huntgroupringback"]);
	$huntgroupcidnameprefix = checkstr($_POST["huntgroupcidnameprefix"]);
	$huntgrouppin = checkstr($_POST["huntgrouppin"]);
	$huntgroupcallerannounce = checkstr($_POST["huntgroupcallerannounce"]);
	$huntgroupdescr = checkstr($_POST["huntgroupdescr"]);
}

if (count($_POST)>0 && strlen($_POST["persistformvar"]) == 0) {

	$msg = '';

	////recommend moving this to the config.php file
	$uploadtempdir = $_ENV["TEMP"]."\\";
	ini_set('upload_tmp_dir', $uploadtempdir);
	////$imagedir = $_ENV["TEMP"]."\\";
	////$filedir = $_ENV["TEMP"]."\\";

	if ($action == "update") {
		$hunt_group_id = checkstr($_POST["hunt_group_id"]);
	}

	//check for all required data
		if (strlen($v_id) == 0) { $msg .= "Please provide: v_id<br>\n"; }
		if (strlen($huntgroupextension) == 0) { $msg .= "Please provide: Extension<br>\n"; }
		if (strlen($huntgroupname) == 0) { $msg .= "Please provide: Hunt Group Name<br>\n"; }
		if (strlen($huntgrouptype) == 0) { $msg .= "Please provide: Type<br>\n"; }
		if (strlen($huntgroupcontext) == 0) { $msg .= "Please provide: Context<br>\n"; }
		if (strlen($huntgrouptimeout) == 0) { $msg .= "Please provide: Timeout<br>\n"; }
		if (strlen($huntgrouptimeoutdestination) == 0) { $msg .= "Please provide: Timeout Destination<br>\n"; }
		if (strlen($huntgrouptimeouttype) == 0) { $msg .= "Please provide: Timeout Type<br>\n"; }
		if (strlen($huntgroupringback) == 0) { $msg .= "Please provide: Ring Back<br>\n"; }
		//if (strlen($huntgroupcidnameprefix) == 0) { $msg .= "Please provide: CID Prefix<br>\n"; }
		//if (strlen($huntgrouppin) == 0) { $msg .= "Please provide: PIN<br>\n"; }
		if (strlen($huntgroupcallerannounce) == 0) { $msg .= "Please provide: Caller Announce<br>\n"; }
		//if (strlen($huntgroupdescr) == 0) { $msg .= "Please provide: Description<br>\n"; }
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
	$tmp .= "Extension: $huntgroupextension\n";
	$tmp .= "Hunt Group Name: $huntgroupname\n";
	$tmp .= "Type: $huntgrouptype\n";
	$tmp .= "Context: $huntgroupcontext\n";
	$tmp .= "Timeout: $huntgrouptimeout\n";
	$tmp .= "Timeout Destination: $huntgrouptimeoutdestination\n";
	$tmp .= "Timeout Type: $huntgrouptimeouttype\n";
	$tmp .= "Ring Back: $huntgroupringback\n";
	$tmp .= "CID Prefix: $huntgroupcidnameprefix\n";
	$tmp .= "PIN: $huntgrouppin\n";
	$tmp .= "Caller Announce: $huntgroupcallerannounce\n";
	$tmp .= "Description: $huntgroupdescr\n";


//Add or update the database
if ($_POST["persistformvar"] != "true") {
	if ($action == "add") {
		$sql = "insert into v_hunt_group ";
		$sql .= "(";
		$sql .= "v_id, ";
		$sql .= "huntgroupextension, ";
		$sql .= "huntgroupname, ";
		$sql .= "huntgrouptype, ";
		$sql .= "huntgroupcontext, ";
		$sql .= "huntgrouptimeout, ";
		$sql .= "huntgrouptimeoutdestination, ";
		$sql .= "huntgrouptimeouttype, ";
		$sql .= "huntgroupringback, ";
		$sql .= "huntgroupcidnameprefix, ";
		$sql .= "huntgrouppin, ";
		$sql .= "huntgroupcallerannounce, ";
		$sql .= "huntgroupdescr ";
		$sql .= ")";
		$sql .= "values ";
		$sql .= "(";
		$sql .= "'$v_id', ";
		$sql .= "'$huntgroupextension', ";
		$sql .= "'$huntgroupname', ";
		$sql .= "'$huntgrouptype', ";
		$sql .= "'$huntgroupcontext', ";
		$sql .= "'$huntgrouptimeout', ";
		$sql .= "'$huntgrouptimeoutdestination', ";
		$sql .= "'$huntgrouptimeouttype', ";
		$sql .= "'$huntgroupringback', ";
		$sql .= "'$huntgroupcidnameprefix', ";
		$sql .= "'$huntgrouppin', ";
		$sql .= "'$huntgroupcallerannounce', ";
		$sql .= "'$huntgroupdescr' ";
		$sql .= ")";
		$db->exec($sql);
		//$lastinsertid = $db->lastInsertId($id);
		unset($sql);

		//synchronize the xml config
		sync_package_v_hunt_group();
		
		require_once "includes/header.php";
		echo "<meta http-equiv=\"refresh\" content=\"2;url=v_hunt_group.php\">\n";
		echo "<div align='center'>\n";
		echo "Add Complete\n";
		echo "</div>\n";
		require_once "includes/footer.php";
		return;
	} //if ($action == "add")

	if ($action == "update") {
		$sql = "update v_hunt_group set ";
		//$sql .= "v_id = '$v_id', ";
		$sql .= "huntgroupextension = '$huntgroupextension', ";
		$sql .= "huntgroupname = '$huntgroupname', ";
		$sql .= "huntgrouptype = '$huntgrouptype', ";
		$sql .= "huntgroupcontext = '$huntgroupcontext', ";
		$sql .= "huntgrouptimeout = '$huntgrouptimeout', ";
		$sql .= "huntgrouptimeoutdestination = '$huntgrouptimeoutdestination', ";
		$sql .= "huntgrouptimeouttype = '$huntgrouptimeouttype', ";
		$sql .= "huntgroupringback = '$huntgroupringback', ";
		$sql .= "huntgroupcidnameprefix = '$huntgroupcidnameprefix', ";
		$sql .= "huntgrouppin = '$huntgrouppin', ";
		$sql .= "huntgroupcallerannounce = '$huntgroupcallerannounce', ";
		$sql .= "huntgroupdescr = '$huntgroupdescr' ";
		$sql .= "where v_id = '$v_id' ";
		$sql .= "and hunt_group_id = '$hunt_group_id'";
		$db->exec($sql);
		unset($sql);

		//synchronize the xml config
		sync_package_v_hunt_group();

		//synchronize the xml config
		sync_package_v_dialplan_includes();
		
		require_once "includes/header.php";
		echo "<meta http-equiv=\"refresh\" content=\"2;url=v_hunt_group.php\">\n";
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
	$hunt_group_id = $_GET["id"];
	$sql = "";
	$sql .= "select * from v_hunt_group ";
	$sql .= "where hunt_group_id = '$hunt_group_id' ";
	$sql .= "and v_id = '$v_id' ";
	$prepstatement = $db->prepare($sql);
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	foreach ($result as &$row) {
		//$v_id = $row["v_id"];
		$huntgroupextension = $row["huntgroupextension"];
		$huntgroupname = $row["huntgroupname"];
		$huntgrouptype = $row["huntgrouptype"];
		$huntgroupcontext = $row["huntgroupcontext"];
		$huntgrouptimeout = $row["huntgrouptimeout"];
		$huntgrouptimeoutdestination = $row["huntgrouptimeoutdestination"];
		$huntgrouptimeouttype = $row["huntgrouptimeouttype"];
		$huntgroupringback = $row["huntgroupringback"];
		$huntgroupcidnameprefix = $row["huntgroupcidnameprefix"];
		$huntgrouppin = $row["huntgrouppin"];
		$huntgroupcallerannounce = $row["huntgroupcallerannounce"];
		$huntgroupdescr = $row["huntgroupdescr"];
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
	echo "<td align='left' colspan='2'>\n";

	echo "<table width='100%'>\n";
	echo "<tr>\n";
	echo "<td align='left' width='30%' nowrap>\n";
	echo "    <p><span class='vexpl'><span class='red'><strong>Hunt Group:<br>\n";
	echo "        </strong></span>\n";
	echo "</td>\n";
	echo "<td width='70%' align='right'><input type='button' class='btn' name='' alt='back' onclick=\"window.location='v_hunt_group.php'\" value='Back'></td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td align='left' colspan='2'>\n";
		echo "        A Hunt Group is a list of destinations that can be called in sequence or simultaneously. \n";
		echo "        </span></p>\n";
		echo "<br />\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";

	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Extension:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' type='text' name='huntgroupextension' maxlength='255' value=\"$huntgroupextension\">\n";
	echo "<br />\n";
	echo "example: 7002\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Hunt Group Name:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' type='text' name='huntgroupname' maxlength='255' value=\"$huntgroupname\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Type:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <select class='formfld' name='huntgrouptype'>\n";
	echo "    <option value=''></option>\n";
	if ($huntgrouptype == "simultaneous") { 
		echo "    <option value='simultaneous' SELECTED >simultaneous</option>\n";
	}
	else {
		echo "    <option value='simultaneous'>simultaneous</option>\n";
	}
	if ($huntgrouptype == "sequentially") { 
		echo "    <option value='sequentially' SELECTED >sequentially</option>\n";
	}
	else {
		echo "    <option value='sequentially'>sequentially</option>\n";
	}
	echo "    </select>\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Context:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' type='text' name='huntgroupcontext' maxlength='255' value=\"$huntgroupcontext\">\n";
	echo "<br />\n";
	echo "example: default\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Timeout:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' type='text' name='huntgrouptimeout' maxlength='255' value=\"$huntgrouptimeout\">\n";
	echo "<br />\n";
	echo "The timeout sets the time in seconds to continue to call before timing out. \n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Timeout Destination:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' type='text' name='huntgrouptimeoutdestination' maxlength='255' value=\"$huntgrouptimeoutdestination\">\n";
	echo "<br />\n";
	echo "Destination. example: 1001\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Timeout Type:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <select class='formfld' name='huntgrouptimeouttype'>\n";
	echo "    <option value=''></option>\n";
	if ($huntgrouptimeouttype == "extension") { 
		echo "    <option value='extension' SELECTED >extension</option>\n";
	}
	else {
		echo "    <option value='extension'>extension</option>\n";
	}
	if ($huntgrouptimeouttype == "voicemail") { 
		echo "    <option value='voicemail' SELECTED >voicemail</option>\n";
	}
	else {
		echo "    <option value='voicemail'>voicemail</option>\n";
	}
	if ($huntgrouptimeouttype == "sip uri") { 
		echo "    <option value='sip uri' SELECTED >sip uri</option>\n";
	}
	else {
		echo "    <option value='sip uri'>sip uri</option>\n";
	}
	echo "    </select>\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Ring Back:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <select class='formfld' name='huntgroupringback'>\n";
	echo "    <option value=''></option>\n";
	if ($huntgroupringback == "ring") { 
		echo "    <option value='ring' SELECTED >ring</option>\n";
	}
	else {
		echo "    <option value='ring'>ring</option>\n";
	}
	if ($huntgroupringback == "music") { 
		echo "    <option value='music' SELECTED >music</option>\n";
	}
	else {
		echo "    <option value='music'>music</option>\n";
	}
	echo "    </select>\n";
	echo "<br />\n";
	echo "Defines what the caller will hear while destination is being called. The choices are music (music on hold) ring (ring tone.) default: music \n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "    CID Prefix:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' type='text' name='huntgroupcidnameprefix' maxlength='255' value=\"$huntgroupcidnameprefix\">\n";
	echo "<br />\n";
	echo "Set a prefix on the caller ID name. (optional)\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "    PIN:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' type='text' name='huntgrouppin' maxlength='255' value=\"$huntgrouppin\">\n";
	echo "<br />\n";
	echo "If this is provided then the caller will be required to enter the PIN number. (optional) \n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Caller Announce:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <select class='formfld' name='huntgroupcallerannounce'>\n";
	echo "    <option value=''></option>\n";
	if ($huntgroupcallerannounce == "true") { 
		echo "    <option value='true' SELECTED >true</option>\n";
	}
	else {
		echo "    <option value='true'>true</option>\n";
	}
	if ($huntgroupcallerannounce == "false") { 
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
	echo "    <input class='formfld' type='text' name='huntgroupdescr' maxlength='255' value=\"$huntgroupdescr\">\n";
	echo "<br />\n";
	echo "You may enter a description here for your reference (not parsed). \n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "	<tr>\n";
	echo "		<td colspan='2' align='right'>\n";
	if ($action == "update") {
		echo "				<input type='hidden' name='hunt_group_id' value='$hunt_group_id'>\n";
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

//---- begin: v_hunt_group_destinations ---------------------------
if ($action == "update") {


	echo "<div align='center'>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='2'>\n";

	echo "<tr class='border'>\n";
	echo "	<td align=\"center\">\n";
	echo "      <br>";


	echo "<table width='100%' border='0' cellpadding='6' cellspacing='0'>\n";
	echo "  <tr>\n";
	echo "    <td align='left'><p><span class='vexpl'><span class='red'><strong>Destinations<br />\n";
	echo "        </strong></span>\n";
	echo "        The following destinations will be called.\n";
	echo "       </span></p></td>\n";
	echo " </tr>\n";
	echo "</table>\n";
	echo "<br />\n";

	$sql = "";
	$sql .= " select * from v_hunt_group_destinations ";
	$sql .= " where v_id = '$v_id' ";
	$sql .= " and hunt_group_id = '$hunt_group_id' ";
	$sql .= " order by destinationorder asc";
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
	echo "<th align='center'>Destination</th>\n";
	echo "<th align='center'>Type</th>\n";
	echo "<th align='center'>Profile</th>\n";
	echo "<th align='center'>Order</th>\n";
	echo "<th align='center'>Description</th>\n";
	echo "<td align='right' width='42'>\n";
	echo "	<a href='v_hunt_group_destinations_edit.php?id2=".$hunt_group_id."' alt='add'><img src='".$v_icon_add."' width='17' height='17' border='0' alt='add'></a>\n";
	echo "</td>\n";
	echo "<tr>\n";


	if ($resultcount == 0) { //no results
	}
	else { //received results

		foreach($result as $row) {
			//print_r( $row );
			echo "<tr >\n";
			echo "   <td valign='top' class='".$rowstyle[$c]."'>&nbsp;&nbsp;".$row[destinationdata]."</td>\n";
			echo "   <td valign='top' class='".$rowstyle[$c]."'>&nbsp;&nbsp;".$row[destinationtype]."</td>\n";
			echo "   <td valign='top' class='".$rowstyle[$c]."'>&nbsp;&nbsp;".$row[destinationprofile]."</td>\n";
			echo "   <td valign='top' class='".$rowstyle[$c]."'>&nbsp;&nbsp;".$row[destinationorder]."</td>\n";
			echo "   <td valign='top' class='rowstylebg' width='30%'>".$row[destinationdescr]."&nbsp;</td>\n";
			echo "   <td valign='top' align='right'>\n";
			echo "		<a href='v_hunt_group_destinations_edit.php?id=".$row[hunt_group_destination_id]."&id2=".$hunt_group_id."' alt='edit'><img src='".$v_icon_edit."' width='17' height='17' border='0' alt='edit'></a>\n";
			echo "		<a href='v_hunt_group_destinations_delete.php?id=".$row[hunt_group_destination_id]."&id2=".$hunt_group_id."' alt='delete' onclick=\"return confirm('Do you really want to delete this?')\"><img src='".$v_icon_delete."' width='17' height='17' border='0' alt='delete'></a>\n";
			echo "   </td>\n";
			echo "</tr>\n";

			if ($c==0) { $c=1; } else { $c=0; }
		} //end foreach
		unset($sql, $result, $rowcount);
	} //end if results

	echo "<tr>\n";
	echo "<td colspan='6'>\n";
	echo "	<table width='100%' cellpadding='0' cellspacing='0'>\n";
	echo "	<tr>\n";
	echo "		<td width='33.3%' nowrap>&nbsp;</td>\n";
	echo "		<td width='33.3%' align='center' nowrap>$pagingcontrols</td>\n";
	echo "		<td width='33.3%' align='right'>\n";
	echo "			<a href='v_hunt_group_destinations_edit.php?id2=".$hunt_group_id."' alt='add'><img src='".$v_icon_add."' width='17' height='17' border='0' alt='add'></a>\n";
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
//---- end: v_hunt_group_destinations ---------------------------
require_once "includes/footer.php";
?>
