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

if (count($_GET)>0) {
	$switch_cmd = trim($_GET["cmd"]);
	$action = trim($_GET["action"]);
	$direction = trim($_GET["direction"]);
}


//GET to PHP variables
if (count($_GET)>0) {

	//fs cmd
	if (strlen($switch_cmd) > 0) {

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

		$fp = event_socket_create($_SESSION['event_socket_ip_address'], $_SESSION['event_socket_port'], $_SESSION['event_socket_password']);
		/*
		if ($action == "energy") {
			//conference 3001-markjcrane.dyndns.org energy 103
			$switch_result = event_socket_request($fp, 'api '.$switch_cmd);
			$result_array = explode("=",$switch_result);
			$tmp_value = $result_array[1];
			if ($direction == "up") { $tmp_value = $tmp_value + 100; }
			if ($direction == "down") { $tmp_value = $tmp_value - 100; }
			//echo "energy $tmp_value<br />\n";
			$switch_result = event_socket_request($fp, 'api '.$switch_cmd.' '.$tmp_value);
		}
		if ($action == "volume_in") {
			$switch_result = event_socket_request($fp, 'api '.$switch_cmd);
			$result_array = explode("=",$switch_result);
			$tmp_value = $result_array[1];
			if ($direction == "up") { $tmp_value = $tmp_value + 1; }
			if ($direction == "down") { $tmp_value = $tmp_value - 1; }
			//echo "volume $tmp_value<br />\n";
			$switch_result = event_socket_request($fp, 'api '.$switch_cmd.' '.$tmp_value);
		}
		if ($action == "volume_out") {
			$switch_result = event_socket_request($fp, 'api '.$switch_cmd);
			$result_array = explode("=",$switch_result);
			$tmp_value = $result_array[1];
			if ($direction == "up") { $tmp_value = $tmp_value + 1; }
			if ($direction == "down") { $tmp_value = $tmp_value - 1; }
			//echo "volume $tmp_value<br />\n";
			$switch_result = event_socket_request($fp, 'api '.$switch_cmd.' '.$tmp_value);
		}
		*/
		
		
		//$switch_cmd = "";
		//echo "<b>switch command:</b>\n";
		//echo "<pre>\n";
		$switch_result = event_socket_request($fp, 'api '.$switch_cmd);
		//$switch_result = eval($switch_cmd);
		//echo htmlentities($switch_result);
		//echo "</pre>\n";
	}

}

?>
