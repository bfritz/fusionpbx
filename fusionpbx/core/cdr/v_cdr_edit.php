<?php
/* $Id$ */
/*
	v_cdr_edit.php
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

if (ifgroup("member") || ifgroup("admin") || ifgroup("superadmin")) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

require_once "v_cdr_import.php";

//action add or update
if (isset($_REQUEST["id"])) {
	$action = "update";
	$cdr_id = checkstr($_REQUEST["id"]);
}
else {
	$action = "add";
}

//POST to PHP variables
if (count($_POST)>0) {
	$caller_id_name = checkstr($_POST["caller_id_name"]);
	$caller_id_number = checkstr($_POST["caller_id_number"]);
	$destination_number = checkstr($_POST["destination_number"]);
	$context = checkstr($_POST["context"]);
	$start_stamp = checkstr($_POST["start_stamp"]);
	$answer_stamp = checkstr($_POST["answer_stamp"]);
	$end_stamp = checkstr($_POST["end_stamp"]);
	$duration = checkstr($_POST["duration"]);
	$billsec = checkstr($_POST["billsec"]);
	$hangup_cause = checkstr($_POST["hangup_cause"]);
	$uuid = checkstr($_POST["uuid"]);
	$bleg_uuid = checkstr($_POST["bleg_uuid"]);
	$accountcode = checkstr($_POST["accountcode"]);
	$read_codec = checkstr($_POST["read_codec"]);
	$write_codec = checkstr($_POST["write_codec"]);
	$remote_media_ip = checkstr($_POST["remote_media_ip"]);
	$network_addr = checkstr($_POST["network_addr"]);
}

/*
if (count($_POST)>0 && strlen($_POST["persistformvar"]) == 0) {

	$msg = '';

	////recommend moving this to the config.php file
	$uploadtempdir = $_ENV["TEMP"]."\\";
	ini_set('upload_tmp_dir', $uploadtempdir);
	////$imagedir = $_ENV["TEMP"]."\\";
	////$filedir = $_ENV["TEMP"]."\\";

	if ($action == "update") {
		$cdr_id = checkstr($_POST["cdr_id"]);
	}

	//check for all required data
		//if (strlen($caller_id_name) == 0) { $msg .= "Please provide: CID Name<br>\n"; }
		//if (strlen($caller_id_number) == 0) { $msg .= "Please provide: CID Number<br>\n"; }
		//if (strlen($destination_number) == 0) { $msg .= "Please provide: Destination<br>\n"; }
		//if (strlen($context) == 0) { $msg .= "Please provide: Context<br>\n"; }
		//if (strlen($start_stamp) == 0) { $msg .= "Please provide: Start<br>\n"; }
		//if (strlen($answer_stamp) == 0) { $msg .= "Please provide: Answer<br>\n"; }
		//if (strlen($end_stamp) == 0) { $msg .= "Please provide: End<br>\n"; }
		//if (strlen($duration) == 0) { $msg .= "Please provide: Duration<br>\n"; }
		//if (strlen($billsec) == 0) { $msg .= "Please provide: Bill Seconds<br>\n"; }
		//if (strlen($hangup_cause) == 0) { $msg .= "Please provide: Hangup Cause<br>\n"; }
		//if (strlen($uuid) == 0) { $msg .= "Please provide: UUID<br>\n"; }
		//if (strlen($bleg_uuid) == 0) { $msg .= "Please provide: Bleg UUID<br>\n"; }
		//if (strlen($accountcode) == 0) { $msg .= "Please provide: Account Code<br>\n"; }
		//if (strlen($read_codec) == 0) { $msg .= "Please provide: Read Codec<br>\n"; }
		//if (strlen($write_codec) == 0) { $msg .= "Please provide: Write Codec<br>\n"; }
		//if (strlen($remote_media_ip) == 0) { $msg .= "Please provide: Remote Media IP<br>\n"; }
		//if (strlen($network_addr) == 0) { $msg .= "Please provide: Network Addr<br>\n"; }
		if (strlen($msg) > 0 && strlen($_POST["persistformvar"]) == 0) {
			require "includes/config.php";
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
	$tmp .= "CID Name: $caller_id_name\n";
	$tmp .= "CID Number: $caller_id_number\n";
	$tmp .= "Destination: $destination_number\n";
	$tmp .= "Context: $context\n";
	$tmp .= "Start: $start_stamp\n";
	$tmp .= "Answer: $answer_stamp\n";
	$tmp .= "End: $end_stamp\n";
	$tmp .= "Duration: $duration\n";
	$tmp .= "Bill Seconds: $billsec\n";
	$tmp .= "Hangup Cause: $hangup_cause\n";
	$tmp .= "UUID: $uuid\n";
	$tmp .= "Bleg UUID: $bleg_uuid\n";
	$tmp .= "Account Code: $accountcode\n";
	$tmp .= "Read Codec: $read_codec\n";
	$tmp .= "Write Codec: $write_codec\n";
	$tmp .= "Remote Media IP: $remote_media_ip\n";
	$tmp .= "Network Addr: $network_addr\n";



	//Add or update the database
	if ($_POST["persistformvar"] != "true") {
		if ($action == "add") {
			$sql = "insert into v_cdr ";
			$sql .= "(";
			$sql .= "v_id, ";
			$sql .= "caller_id_name, ";
			$sql .= "caller_id_number, ";
			$sql .= "destination_number, ";
			$sql .= "context, ";
			$sql .= "start_stamp, ";
			$sql .= "answer_stamp, ";
			$sql .= "end_stamp, ";
			$sql .= "duration, ";
			$sql .= "billsec, ";
			$sql .= "hangup_cause, ";
			$sql .= "uuid, ";
			$sql .= "bleg_uuid, ";
			$sql .= "accountcode, ";
			$sql .= "read_codec, ";
			$sql .= "write_codec, ";
			$sql .= "remote_media_ip, ";
			$sql .= "network_addr ";
			$sql .= ")";
			$sql .= "values ";
			$sql .= "(";
			$sql .= "'$v_id', ";
			$sql .= "'$caller_id_name', ";
			$sql .= "'$caller_id_number', ";
			$sql .= "'$destination_number', ";
			$sql .= "'$context', ";
			$sql .= "'$start_stamp', ";
			$sql .= "'$answer_stamp', ";
			$sql .= "'$end_stamp', ";
			$sql .= "'$duration', ";
			$sql .= "'$billsec', ";
			$sql .= "'$hangup_cause', ";
			$sql .= "'$uuid', ";
			$sql .= "'$bleg_uuid', ";
			$sql .= "'$accountcode', ";
			$sql .= "'$read_codec', ";
			$sql .= "'$write_codec', ";
			$sql .= "'$remote_media_ip', ";
			$sql .= "'$network_addr' ";
			$sql .= ")";
			$db->exec($sql);
			//$lastinsertid = $db->lastInsertId($id);
			unset($sql);

			require "includes/config.php";
			require_once "includes/header.php";
			echo "<meta http-equiv=\"refresh\" content=\"2;url=v_cdr.php\">\n";
			echo "<div align='center'>\n";
			echo "Add Complete\n";
			echo "</div>\n";
			require_once "includes/footer.php";
			return;
		} //if ($action == "add")

		if ($action == "update") {
			$sql = "update v_cdr set ";
			$sql .= "caller_id_name = '$caller_id_name', ";
			$sql .= "caller_id_number = '$caller_id_number', ";
			$sql .= "destination_number = '$destination_number', ";
			$sql .= "context = '$context', ";
			$sql .= "start_stamp = '$start_stamp', ";
			$sql .= "answer_stamp = '$answer_stamp', ";
			$sql .= "end_stamp = '$end_stamp', ";
			$sql .= "duration = '$duration', ";
			$sql .= "billsec = '$billsec', ";
			$sql .= "hangup_cause = '$hangup_cause', ";
			$sql .= "uuid = '$uuid', ";
			$sql .= "bleg_uuid = '$bleg_uuid', ";
			$sql .= "accountcode = '$accountcode', ";
			$sql .= "read_codec = '$read_codec', ";
			$sql .= "write_codec = '$write_codec', ";
			$sql .= "remote_media_ip = '$remote_media_ip', ";
			$sql .= "network_addr = '$network_addr' ";
			$sql .= "where v_id = '$v_id' ";
			$sql .= "and cdr_id = '$cdr_id' ";
			$db->exec($sql);
			unset($sql);

			require "includes/config.php";
			require_once "includes/header.php";
			echo "<meta http-equiv=\"refresh\" content=\"2;url=v_cdr.php\">\n";
			echo "<div align='center'>\n";
			echo "Update Complete\n";
			echo "</div>\n";
			require_once "includes/footer.php";
			return;
		} //if ($action == "update")
	} //if ($_POST["persistformvar"] != "true") { 
} //(count($_POST)>0 && strlen($_POST["persistformvar"]) == 0)
*/


//get a list of assigned extensions for this user
	$sql = "";
	$sql .= " select * from v_extensions ";
	$sql .= "where v_id = '$v_id' ";
	$sql .= "and user_list like '%|".$_SESSION["username"]."|%' ";
	$prepstatement = $db->prepare($sql);
	$prepstatement->execute();
	//$v_mailboxes = '';
	$x = 0;
	while($row = $prepstatement->fetch()) {
		//$v_mailboxes = $v_mailboxes.$row["mailbox"].'|';
		//$extension_id = $row["extension_id"];
		//$mailbox = $row["mailbox"];
		$extension_array[$x]['extension_id'] = $row["extension_id"];
		$extension_array[$x]['extension'] = $row["extension"];
		$x++;
	}
	unset ($prepstatement, $x);


//Pre-populate the form
if (count($_GET)>0 && $_POST["persistformvar"] != "true") {
	$cdr_id = $_GET["id"];
	$sql = "";
	$sql .= "select * from v_cdr ";	
	if (!ifgroup("admin") || !ifgroup("superadmin")) {
		if (trim($sqlwhere) == "where") { $sqlwhere = ""; }
		if (count($extension_array) > 0) {
			foreach($extension_array as $value) {
				if ($value['extension'] > 0) { $sqlwhere .= "where v_id = '$v_id' and caller_id_number = '".$value['extension']."' and cdr_id = '$cdr_id' "; } //source
				if ($value['extension'] > 0) { $sqlwhere .= "or v_id = '$v_id' and destination_number = '".$value['extension']."' and cdr_id = '$cdr_id' "; } //destination
				if ($value['extension'] > 0) { $sqlwhere .= "or v_id = '$v_id'  and destination_number = '*99".$value['extension']."' and cdr_id = '$cdr_id' "; } //destination
			}
		} //count($extension_array)
	}
	else {
		$sql .= "where v_id = '$v_id' ";
		$sql .= "and cdr_id = '$cdr_id' ";
	}
	$sqlwhere = str_replace ("where or", "where", $sqlwhere);
	$sqlwhere = str_replace ("where and", "where", $sqlwhere);
	$sql .= $sqlwhere;
	$prepstatement = $db->prepare($sql);
	$prepstatement->execute();
	while($row = $prepstatement->fetch()) {
		$caller_id_name = $row["caller_id_name"];
		$caller_id_number = $row["caller_id_number"];
		$destination_number = $row["destination_number"];
		$context = $row["context"];
		$start_stamp = $row["start_stamp"];
		$answer_stamp = $row["answer_stamp"];
		$end_stamp = $row["end_stamp"];
		$duration = $row["duration"];
		$billsec = $row["billsec"];
		$hangup_cause = $row["hangup_cause"];
		$uuid = $row["uuid"];
		$bleg_uuid = $row["bleg_uuid"];
		$accountcode = $row["accountcode"];
		$read_codec = $row["read_codec"];
		$write_codec = $row["write_codec"];
		$remote_media_ip = $row["remote_media_ip"];
		$network_addr = $row["network_addr"];
		break; //limit to 1 row
	}
	unset ($prepstatement);
}


require "includes/config.php";
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
	echo "<td align='left' width='30%' nowrap><b>Call Detail Records Add</b></td>\n";
}
if ($action == "update") {
	echo "<td align='left' width='30%' nowrap><b>Call Detail Records: Details</b></td>\n";
}
echo "<td width='70%' align='right'><input type='button' class='btn' name='' alt='back' onclick=\"window.location='v_cdr.php'\" value='Back'></td>\n";
echo "</tr>\n";

echo "<tr>\n";
echo "<td class='vncell' valign='top' align='left' nowrap>\n";
echo "    CID Name:\n";
echo "</td>\n";
echo "<td class='vtable' align='left'>\n";
echo "    <input class='formfld' type='text' name='caller_id_name' maxlength='255' value=\"$caller_id_name\">\n";
echo "<br />\n";
echo "\n";
echo "</td>\n";
echo "</tr>\n";

echo "<tr>\n";
echo "<td class='vncell' valign='top' align='left' nowrap>\n";
echo "    CID Number:\n";
echo "</td>\n";
echo "<td class='vtable' align='left'>\n";
echo "    <input class='formfld' type='text' name='caller_id_number' maxlength='255' value=\"$caller_id_number\">\n";
echo "<br />\n";
echo "\n";
echo "</td>\n";
echo "</tr>\n";

echo "<tr>\n";
echo "<td class='vncell' valign='top' align='left' nowrap>\n";
echo "    Destination:\n";
echo "</td>\n";
echo "<td class='vtable' align='left'>\n";
echo "    <input class='formfld' type='text' name='destination_number' maxlength='255' value=\"$destination_number\">\n";
echo "<br />\n";
echo "\n";
echo "</td>\n";
echo "</tr>\n";

echo "<tr>\n";
echo "<td class='vncell' valign='top' align='left' nowrap>\n";
echo "    Context:\n";
echo "</td>\n";
echo "<td class='vtable' align='left'>\n";
echo "    <input class='formfld' type='text' name='context' maxlength='255' value=\"$context\">\n";
echo "<br />\n";
echo "\n";
echo "</td>\n";
echo "</tr>\n";

echo "<tr>\n";
echo "<td class='vncell' valign='top' align='left' nowrap>\n";
echo "    Start:\n";
echo "</td>\n";
echo "<td class='vtable' align='left'>\n";
echo "    <input class='formfld' type='text' name='start_stamp' maxlength='255' value=\"$start_stamp\">\n";
echo "<br />\n";
echo "\n";
echo "</td>\n";
echo "</tr>\n";

echo "<tr>\n";
echo "<td class='vncell' valign='top' align='left' nowrap>\n";
echo "    Answer:\n";
echo "</td>\n";
echo "<td class='vtable' align='left'>\n";
echo "    <input class='formfld' type='text' name='answer_stamp' maxlength='255' value=\"$answer_stamp\">\n";
echo "<br />\n";
echo "\n";
echo "</td>\n";
echo "</tr>\n";

echo "<tr>\n";
echo "<td class='vncell' valign='top' align='left' nowrap>\n";
echo "    End:\n";
echo "</td>\n";
echo "<td class='vtable' align='left'>\n";
echo "    <input class='formfld' type='text' name='end_stamp' maxlength='255' value=\"$end_stamp\">\n";
echo "<br />\n";
echo "\n";
echo "</td>\n";
echo "</tr>\n";

echo "<tr>\n";
echo "<td class='vncell' valign='top' align='left' nowrap>\n";
echo "    Duration:\n";
echo "</td>\n";
echo "<td class='vtable' align='left'>\n";
echo "    <input class='formfld' type='text' name='duration' maxlength='255' value=\"$duration\">\n";
echo "<br />\n";
echo "\n";
echo "</td>\n";
echo "</tr>\n";

echo "<tr>\n";
echo "<td class='vncell' valign='top' align='left' nowrap>\n";
echo "    Bill Seconds:\n";
echo "</td>\n";
echo "<td class='vtable' align='left'>\n";
echo "    <input class='formfld' type='text' name='billsec' maxlength='255' value=\"$billsec\">\n";
echo "<br />\n";
echo "\n";
echo "</td>\n";
echo "</tr>\n";

echo "<tr>\n";
echo "<td class='vncell' valign='top' align='left' nowrap>\n";
echo "    Hangup Cause:\n";
echo "</td>\n";
echo "<td class='vtable' align='left'>\n";
echo "    <input class='formfld' type='text' name='hangup_cause' maxlength='255' value=\"$hangup_cause\">\n";
echo "<br />\n";
echo "\n";
echo "</td>\n";
echo "</tr>\n";

echo "<tr>\n";
echo "<td class='vncell' valign='top' align='left' nowrap>\n";
echo "    UUID:\n";
echo "</td>\n";
echo "<td class='vtable' align='left'>\n";
echo "    <input class='formfld' type='text' name='uuid' maxlength='255' value=\"$uuid\">\n";
echo "<br />\n";
echo "\n";
echo "</td>\n";
echo "</tr>\n";

echo "<tr>\n";
echo "<td class='vncell' valign='top' align='left' nowrap>\n";
echo "    Bleg UUID:\n";
echo "</td>\n";
echo "<td class='vtable' align='left'>\n";
echo "    <input class='formfld' type='text' name='bleg_uuid' maxlength='255' value=\"$bleg_uuid\">\n";
echo "<br />\n";
echo "\n";
echo "</td>\n";
echo "</tr>\n";

echo "<tr>\n";
echo "<td class='vncell' valign='top' align='left' nowrap>\n";
echo "    Account Code:\n";
echo "</td>\n";
echo "<td class='vtable' align='left'>\n";
echo "    <input class='formfld' type='text' name='accountcode' maxlength='255' value=\"$accountcode\">\n";
echo "<br />\n";
echo "\n";
echo "</td>\n";
echo "</tr>\n";

echo "<tr>\n";
echo "<td class='vncell' valign='top' align='left' nowrap>\n";
echo "    Read Codec:\n";
echo "</td>\n";
echo "<td class='vtable' align='left'>\n";
echo "    <input class='formfld' type='text' name='read_codec' maxlength='255' value=\"$read_codec\">\n";
echo "<br />\n";
echo "\n";
echo "</td>\n";
echo "</tr>\n";

echo "<tr>\n";
echo "<td class='vncell' valign='top' align='left' nowrap>\n";
echo "    Write Codec:\n";
echo "</td>\n";
echo "<td class='vtable' align='left'>\n";
echo "    <input class='formfld' type='text' name='write_codec' maxlength='255' value=\"$write_codec\">\n";
echo "<br />\n";
echo "\n";
echo "</td>\n";
echo "</tr>\n";

echo "<tr>\n";
echo "<td class='vncell' valign='top' align='left' nowrap>\n";
echo "    Remote Media IP:\n";
echo "</td>\n";
echo "<td class='vtable' align='left'>\n";
echo "    <input class='formfld' type='text' name='remote_media_ip' maxlength='255' value=\"$remote_media_ip\">\n";
echo "<br />\n";
echo "\n";
echo "</td>\n";
echo "</tr>\n";

echo "<tr>\n";
echo "<td class='vncell' valign='top' align='left' nowrap>\n";
echo "    Network Addr:\n";
echo "</td>\n";
echo "<td class='vtable' align='left'>\n";
echo "    <input class='formfld' type='text' name='network_addr' maxlength='255' value=\"$network_addr\">\n";
echo "<br />\n";
echo "\n";
echo "</td>\n";
echo "</tr>\n";
echo "	<tr>\n";
echo "		<td colspan='2' align='right'>\n";
if ($action == "update") {
	echo "				<input type='hidden' name='cdr_id' value='$cdr_id'>\n";
}
//echo "				<input type='submit' name='submit' class='btn' value='Save'>\n";
echo "		</td>\n";
echo "	</tr>";
echo "</table>";
echo "</form>";


echo "	</td>";
echo "	</tr>";
echo "</table>";
echo "</div>";

require "includes/config.php";
require_once "includes/footer.php";
?>
