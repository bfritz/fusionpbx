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
	Copyright (C) 2010
	All Rights Reserved.

	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>
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

//set http compression
	if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
		ob_start("ob_gzhandler");
	}
	else{
		ob_start();
	}

//set variables
	$fifo_name = trim($_REQUEST["c"]);
	$tmp_fifo_name = str_replace('_', ' ', $fifo_name);
	$tmp_fifo_name = str_replace('@', '', $tmp_fifo_name);

//get the event socket information
	if (strlen($_SESSION['event_socket_ip_address']) == 0) {
		$sql = "";
		$sql .= "select * from v_settings ";
		$sql .= "where v_id = '$v_id' ";
		$prepstatement = $db->prepare(check_sql($sql));
		$prepstatement->execute();
		$result = $prepstatement->fetchAll();
		foreach ($result as &$row) {
			$_SESSION['event_socket_ip_address'] = $row["event_socket_ip_address"];
			$_SESSION['event_socket_port'] = $row["event_socket_port"];
			$_SESSION['event_socket_password'] = $row["event_socket_password"];
			break; //limit to 1 row
		}
	}

//prepare and send the command
	$switch_cmd = 'fifo list_verbose '.$fifo_name.'';
	$fp = event_socket_create($_SESSION['event_socket_ip_address'], $_SESSION['event_socket_port'], $_SESSION['event_socket_password']);
	$xml_str = trim(event_socket_request($fp, 'api '.$switch_cmd));
	//echo "<pre>";
	//echo htmlentities($xml_str);
	//echo "</pre>";

//parse the response as xml
	try {
		$xml = new SimpleXMLElement($xml_str);
	}
	catch(Exception $e) {
		//echo $e->getMessage();
	}

//set variables from the xml
	//$name = $xml->conference['name'];
	//$member_count = $xml->conference['member-count'];
	//$locked = $xml->conference['locked'];

//set the alternating row styles
	$c = 0;
	$rowstyle["0"] = "rowstyle0";
	$rowstyle["1"] = "rowstyle1";
?>

<div id="cmd_reponse">
</div>
<?php

	echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
	/*
	echo "<tr>\n";
	echo "<td >\n";
	//echo "	<strong>Count: $member_count</strong>\n";
	echo "</td>\n";
	echo "<td colspan='7'>\n";
	echo "	&nbsp;\n";
	echo "</td>\n";
	echo "<td colspan='1' align='right'>\n";
	echo "	<strong>Queues Tools:</strong> \n";
	echo "	<a href='javascript:void(0);' onclick=\"record_count++;send_cmd('v_conference_exec.php?cmd=conference%20".$conference_name." record recordings/conference_".$conference_name."-'+document.getElementById('time_stamp').innerHTML+'_'+record_count+'.wav');\">Start Record</a>&nbsp;\n";
	echo "	<a href='javascript:void(0);' onclick=\"send_cmd('v_conference_exec.php?cmd=conference%20".$conference_name." norecord recordings/conference_".$conference_name."-'+document.getElementById('time_stamp').innerHTML+'_'+record_count+'.wav');\">Stop Record</a>&nbsp;\n";
	if ($locked == "true") {
		echo "	<a href='javascript:void(0);' onclick=\"send_cmd('v_conference_exec.php?cmd=conference%20".$conference_name." unlock');\">Unlock</a>&nbsp;\n";
	}
	else {
		echo "	<a href='javascript:void(0);' onclick=\"send_cmd('v_conference_exec.php?cmd=conference%20".$conference_name." lock');\">Lock</a>&nbsp;\n";
	}
	echo "</td>\n";
	echo "</tr>\n";
	*/

	echo "<tr>\n";
	echo "<th>Username</th>\n";
	//echo "<th>Dialplan</th>\n";
	echo "<th>Caller ID Name</th>\n";
	echo "<th>Caller ID Number</th>\n";
	echo "<th>Language</th>\n";
	//echo "<th>ANI</th>\n";
	//echo "<th>ANIII</th>\n";
	//echo "<th>Network Addr</th>\n";
	echo "<th>Destination Number</th>\n";
	//echo "<th>RDNIS</th>\n";
	//echo "<th>UUID</th>\n";
	//echo "<th>Source</th>\n";
	//echo "<th>Context</th>\n";
	//echo "<th>Chan Name</th>\n";
	echo "<th>Position</th>\n";
	echo "<th>Priority</th>\n";
	echo "<th>Status</th>\n";
	echo "<th>Duration</th>\n";
	echo "</tr>\n";

	foreach ($xml->fifo->callers->caller as $row) {
		//print_r($row);
		$username = $row->caller_profile->username;
		$dialplan = $row->caller_profile->dialplan;
		$caller_id_name = urldecode($row->caller_profile->caller_id_name);
		$caller_id_number = $row->caller_profile->caller_id_number;
		$ani = $row->caller_profile->ani;
		$aniii = $row->caller_profile->aniii;
		$network_addr = $row->caller_profile->network_addr;
		$destination_number = $row->destination_number->rdnis;
		$rdnis = $row->caller_profile->rdnis;
		$uuid = $row->caller_profile->uuid;
		$source = $row->caller_profile->source;
		$context = $row->caller_profile->context;
		$chan_name = $row->caller_profile->chan_name;
		$default_language = $row->variables->default_language;
		$fifo_position = $row->variables->fifo_position;
		$fifo_priority = $row->variables->fifo_priority;
		$fifo_status = $row->variables->fifo_status;
		$fifo_timestamp = urldecode($row->variables->fifo_timestamp);
		$fifo_time = strtotime($fifo_timestamp);
		$fifo_duration = time() - $fifo_time;
		$fifo_duration_formatted = str_pad(intval(intval($fifo_duration/3600)),2,"0",STR_PAD_LEFT).":" . str_pad(intval(($fifo_duration / 60) % 60),2,"0",STR_PAD_LEFT).":" . str_pad(intval($fifo_duration % 60),2,"0",STR_PAD_LEFT) ;

		echo "<tr>\n";
		echo "<td valign='top' class='".$rowstyle[$c]."'>$username &nbsp;</td>\n";
		//echo "<td valign='top' class='".$rowstyle[$c]."'>$dialplan &nbsp;</td>\n";
		echo "<td valign='top' class='".$rowstyle[$c]."'>$caller_id_name &nbsp;</td>\n";
		echo "<td valign='top' class='".$rowstyle[$c]."'>$caller_id_number &nbsp;</td>\n";
		echo "<td valign='top' class='".$rowstyle[$c]."'>$default_language &nbsp;</td>\n";
		//echo "<td valign='top' class='".$rowstyle[$c]."'>$ani &nbsp;</td>\n";
		//echo "<td valign='top' class='".$rowstyle[$c]."'>$aniii &nbsp;</td>\n";
		//echo "<td valign='top' class='".$rowstyle[$c]."'>$network_addr &nbsp;</td>\n";
		echo "<td valign='top' class='".$rowstyle[$c]."'>$destination_number &nbsp;</td>\n";
		//echo "<td valign='top' class='".$rowstyle[$c]."'>$rdnis &nbsp;</td>\n";
		//echo "<td valign='top' class='".$rowstyle[$c]."'>$uuid &nbsp;</td>\n";
		//echo "<td valign='top' class='".$rowstyle[$c]."'>$source &nbsp;</td>\n";
		//echo "<td valign='top' class='".$rowstyle[$c]."'>$context &nbsp;</td>\n";
		//echo "<td valign='top' class='".$rowstyle[$c]."'>$chan_name &nbsp;</td>\n";
		echo "<td valign='top' class='".$rowstyle[$c]."'>$fifo_position &nbsp;</td>\n";
		echo "<td valign='top' class='".$rowstyle[$c]."'>$fifo_priority &nbsp;</td>\n";
		echo "<td valign='top' class='".$rowstyle[$c]."'>$fifo_status &nbsp;</td>\n";
		echo "<td valign='top' class='".$rowstyle[$c]."'>$fifo_duration_formatted &nbsp;</td>\n";
		echo "</tr>\n";
		if ($c==0) { $c=1; } else { $c=0; }
	}
	echo "</table>\n";

?>
