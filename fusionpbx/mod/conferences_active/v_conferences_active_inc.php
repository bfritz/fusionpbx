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

$tmp_conference_name = str_replace("_", " ", $conference_name);

$switch_cmd = 'conference xml_list';
$fp = event_socket_create($_SESSION['event_socket_ip_address'], $_SESSION['event_socket_port'], $_SESSION['event_socket_password']);
$xml_str = trim(event_socket_request($fp, 'api '.$switch_cmd));
try {
	$xml = new SimpleXMLElement($xml_str);
}
catch(Exception $e) {
	//echo $e->getMessage();
}

$c = 0;
$rowstyle["0"] = "rowstyle0";
$rowstyle["1"] = "rowstyle1";

echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
echo "<tr>\n";
echo "<th>Name</th>\n";
echo "<th>Member Count</th>\n";
echo "<th>&nbsp;</th>\n";
echo "</tr>\n";

foreach ($xml->conference as $row) {
	//print_r($row);

	$name = $row['name'];
	$member_count = $row['member-count'];

	//$id = $row->members->member->id;
	//$flag_can_hear = $row->members->member->flags->can_hear;
	//$flag_can_speak = $row->members->member->flags->can_speak;
	//$flag_talking = $row->members->member->flags->talking;
	//$flag_has_video = $row->members->member->flags->has_video;
	//$flag_has_floor = $row->members->member->flags->has_floor;
	//$uuid = $row->members->member->uuid;
	//$caller_id_name = $row->members->member->caller_id_name;
	//$caller_id_name = str_replace("%20", " ", $caller_id_name);
	//$caller_id_number = $row->members->member->caller_id_number;

	echo "<tr>\n";
	echo "<td valign='top' class='".$rowstyle[$c]."'>".$name."</td>\n";
	echo "<td valign='top' class='".$rowstyle[$c]."'>".$member_count."</td>\n";
	echo "<td valign='top' class='".$rowstyle[$c]."'><a href='v_conference_interactive.php?c=".$name."'>view</a></td>\n";
	echo "</tr>\n";

	if ($c==0) { $c=1; } else { $c=0; }
}
echo "</table>\n";

?>