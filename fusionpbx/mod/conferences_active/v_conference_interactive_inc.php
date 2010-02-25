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
	Portions created by the Initial Developer are Copyright (C) 2008-2010
	the Initial Developer. All Rights Reserved.

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

$conference_name = trim($_REQUEST["c"]);
$tmp_conference_name = str_replace("_", " ", $conference_name);

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

$switch_cmd = 'conference '.$conference_name.' xml_list';
$fp = event_socket_create($_SESSION['event_socket_ip_address'], $_SESSION['event_socket_port'], $_SESSION['event_socket_password']);
$xml_str = trim(event_socket_request($fp, 'api '.$switch_cmd));
//echo $xml_str;

try {
	$xml = new SimpleXMLElement($xml_str);
}
catch(Exception $e) {
	//echo $e->getMessage();
}
//$name = $xml->conference['name'];
$member_count = $xml->conference['member-count'];
$locked = $xml->conference['locked'];

$c = 0;
$rowstyle["0"] = "rowstyle0";
$rowstyle["1"] = "rowstyle1";
?>

<div id="cmd_reponse">
</div>
<?php

	echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";

	echo "<tr>\n";
	echo "<td >\n";
	echo "	<strong>Count: $member_count</strong>\n";
	echo "</td>\n";
	echo "<td colspan='7'>\n";
	echo "	&nbsp;\n";
	echo "</td>\n";
	echo "<td colspan='1' align='right'>\n";
	echo "	<strong>Conference Tools:</strong> \n";
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

	echo "<tr>\n";
	echo "<th>ID</th>\n";
	//echo "<th>UUID</th>\n";
	echo "<th>Caller ID Name</th>\n";
	echo "<th>Caller ID Number</th>\n";
	echo "<th>Hear</th>\n";
	echo "<th>Speak</th>\n";
	echo "<th>Talking</th>\n";
	echo "<th>Video</th>\n";
	echo "<th>Has Floor</th>\n";
	echo "<th>Tools</th>\n";
	echo "</tr>\n";

	foreach ($xml->conference->members->member as $row) {
		//print_r($row);

		$id = $row->id;
		$flag_can_hear = $row->flags->can_hear;
		$flag_can_speak = $row->flags->can_speak;
		$flag_talking = $row->flags->talking;
		$flag_has_video = $row->flags->has_video;
		$flag_has_floor = $row->flags->has_floor;
		$uuid = $row->uuid;
		$caller_id_name = $row->caller_id_name;
		$caller_id_name = str_replace("%20", " ", $caller_id_name);
		$caller_id_number = $row->caller_id_number;

		echo "<tr>\n";
		echo "<td valign='top' class='".$rowstyle[$c]."'>$id</td>\n";
		//echo "<td valign='top' class='".$rowstyle[$c]."'>$uuid</td>\n";
		echo "<td valign='top' class='".$rowstyle[$c]."'>$caller_id_name</td>\n";
		echo "<td valign='top' class='".$rowstyle[$c]."'>$caller_id_number</td>\n";
		if ($flag_can_hear == "true") {
			echo "<td valign='top' class='".$rowstyle[$c]."'>yes</td>\n";
		}
		else {
			echo "<td valign='top' class='".$rowstyle[$c]."'>no</td>\n";
		}
		if ($flag_can_speak == "true") {
			echo "<td valign='top' class='".$rowstyle[$c]."'>yes</td>\n";
		}
		else {
			echo "<td valign='top' class='".$rowstyle[$c]."'>no</td>\n";
		}
		if ($flag_talking == "true") {
			echo "<td valign='top' class='".$rowstyle[$c]."'>yes</td>\n";
		}
		else {
			echo "<td valign='top' class='".$rowstyle[$c]."'>no</td>\n";
		}
		if ($flag_has_video == "true") {
			echo "<td valign='top' class='".$rowstyle[$c]."'>yes</td>\n";
		}
		else {
			echo "<td valign='top' class='".$rowstyle[$c]."'>no</td>\n";
		}
		if ($flag_has_floor == "true") {
			echo "<td valign='top' class='".$rowstyle[$c]."'>yes</td>\n";
		}
		else {
			echo "<td valign='top' class='".$rowstyle[$c]."'>no</td>\n";
		}
		echo "<td valign='top' class='".$rowstyle[$c]."' style='text-align:right;'>\n";
		//energy
			echo "	<a href='javascript:void(0);' onclick=\"send_cmd('v_conference_exec.php?action=energy&direction=up&cmd=conference%20".$conference_name." energy ".$id."');\">+energy</a>&nbsp;\n";
			echo "	<a href='javascript:void(0);' onclick=\"send_cmd('v_conference_exec.php?action=energy&direction=down&cmd=conference%20".$conference_name." energy ".$id."');\">-energy</a>&nbsp;\n";
		//volume
			echo "	<a href='javascript:void(0);' onclick=\"send_cmd('v_conference_exec.php?action=volume_in&direction=up&cmd=conference%20".$conference_name." volume_in ".$id."');\">+vol</a>&nbsp;\n";
			echo "	<a href='javascript:void(0);' onclick=\"send_cmd('v_conference_exec.php?action=volume_in&direction=down&cmd=conference%20".$conference_name." volume_in ".$id."');\">-vol</a>&nbsp;\n";
			echo "	<a href='javascript:void(0);' onclick=\"send_cmd('v_conference_exec.php?action=volume_out&direction=up&cmd=conference%20".$conference_name." volume_out ".$id."');\">+gain</a>&nbsp;\n";
			echo "	<a href='javascript:void(0);' onclick=\"send_cmd('v_conference_exec.php?action=volume_out&direction=down&cmd=conference%20".$conference_name." volume_out ".$id."');\">-gain</a>&nbsp;\n";
		//mute and unmute
			if ($flag_can_speak == "true"){
				echo "	<a href='javascript:void(0);' onclick=\"send_cmd('v_conference_exec.php?cmd=conference%20".$conference_name." mute ".$id."');\">mute</a>&nbsp;\n";
			}
			else {
				echo "	<a href='javascript:void(0);' onclick=\"send_cmd('v_conference_exec.php?cmd=conference%20".$conference_name." unmute ".$id."');\">unmute</a>&nbsp;\n";
			}
		//deaf and undeaf
			if ($flag_can_hear == "true"){
				echo "	<a href='javascript:void(0);' onclick=\"send_cmd('v_conference_exec.php?cmd=conference%20".$conference_name." deaf ".$id."');\">deaf</a>&nbsp;\n";
			}
			else {
				echo "	<a href='javascript:void(0);' onclick=\"send_cmd('v_conference_exec.php?cmd=conference%20".$conference_name." undeaf ".$id."');\">undeaf</a>&nbsp;\n";
			}
		//kick someone from the conference
			echo "	<a href='javascript:void(0);' onclick=\"send_cmd('v_conference_exec.php?cmd=conference%20".$conference_name." kick ".$id."');\">kick</a>&nbsp;\n";
		echo "	&nbsp;";
		echo "</td>\n";
		echo "</tr>\n";
		if ($c==0) { $c=1; } else { $c=0; }
	}
	echo "</table>\n";

?>
