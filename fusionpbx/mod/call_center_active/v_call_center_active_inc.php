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

//send the event socket command and get the response
	$switch_cmd = 'callcenter_config queue list support@default';
	$fp = event_socket_create($_SESSION['event_socket_ip_address'], $_SESSION['event_socket_port'], $_SESSION['event_socket_password']);
	$event_socket_str = trim(event_socket_request($fp, 'api '.$switch_cmd));

//convert the string to a named array
	$event_socket_array = explode ("\n", $event_socket_str);
	if (trim(strtoupper($event_socket_array[0])) != "+OK") {
		$event_socket_field_name_array = explode ("|", $event_socket_array[0]);
		$x = 0;
		foreach ($event_socket_array as $row) {
			if ($x > 0) {
				$event_socket_field_value_array = explode ("|", $event_socket_array[$x]);
				$y = 0;
				foreach ($event_socket_field_value_array as $tmp_value) {
					$tmp_name = $event_socket_field_name_array[$y];
					if (trim(strtoupper($tmp_value)) != "+OK") {
						$result[$x][$tmp_name] = $tmp_value;
					}
					$y++;
				}
			}
			$x++;
		}
		unset($row);
	}

$c = 0;
$rowstyle["0"] = "rowstyle0";
$rowstyle["1"] = "rowstyle1";

echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
echo "<tr>\n";
echo "<th>Name</th>\n";
echo "<th>Number</th>\n";
echo "<th>Status</th>\n";
echo "</tr>\n";

foreach ($result as $row) {
	//print_r($row);

	$queue = $row['queue'];
	$system = $row['system'];
	$uuid = $row['uuid'];
	$caller_number = $row['caller_number'];
	$caller_name = $row['caller_name'];
	$system_epoch = $row['system_epoch'];
	$joined_epoch = $row['joined_epoch'];
	$rejoined_epoch = $row['rejoined_epoch'];
	$bridge_epoch = $row['bridge_epoch'];
	$abandoned_epoch = $row['abandoned_epoch'];
	$base_score = $row['base_score'];
	$skill_score = $row['skill_score'];
	$serving_agent = $row['serving_agent'];
	$serving_system = $row['serving_system'];
	$state = $row['state'];

	echo "<tr>\n";
	echo "<td valign='top' class='".$rowstyle[$c]."'>".$caller_name."</td>\n";
	echo "<td valign='top' class='".$rowstyle[$c]."'>".$caller_number."</td>\n";
	echo "<td valign='top' class='".$rowstyle[$c]."'>".$state."</td>\n";
	echo "</tr>\n";

	if ($c==0) { $c=1; } else { $c=0; }
}
echo "</table>\n";

?>