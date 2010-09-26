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

$switch_cmd = 'fifo list';
$fp = event_socket_create($_SESSION['event_socket_ip_address'], $_SESSION['event_socket_port'], $_SESSION['event_socket_password']);
$xml_str = trim(event_socket_request($fp, 'api '.$switch_cmd));
try {
	$xml = new SimpleXMLElement($xml_str);
}
catch(Exception $e) {
	//echo $e->getMessage();
}

/*
<fifo_report>
  <fifo name="5900@voip.fusionpbx.com" consumer_count="0" caller_count="1" waiting_count="1" importance="0">
    <callers>
      <caller uuid="73a9324f-2a87-df11-bedf-0019dbe93b1f" status="WAITING" timestamp="2010-07-04 05:09:23">
        <caller_profile></caller_profile>
      </caller>
    </callers>
    <consumers></consumers>
  </fifo>
  <fifo name="cool_fifo@voip.fusionpbx.com" consumer_count="0" caller_count="0" waiting_count="0" importance="0">
    <callers></callers>
    <consumers></consumers>
  </fifo>
</fifo_report>
*/

echo "<pre>\n";
//print_r($xml);
echo "</pre>\n";

$c = 0;
$rowstyle["0"] = "rowstyle0";
$rowstyle["1"] = "rowstyle1";

echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
echo "<tr>\n";
echo "<th>Name</th>\n";
echo "<th>Consumer Count</th>\n";
echo "<th>Caller Count</th>\n";
echo "<th>Waiting Count</th>\n";
echo "<th>Importance</th>\n";
echo "<th>&nbsp;</th>\n";
echo "</tr>\n";

foreach ($xml->fifo as $row) {

	foreach($row->attributes() as $tmp_name => $tmp_value) {
		$$tmp_name = $tmp_value;
	}
	unset($tmp_name, $tmp_value);
	//echo "<pre>\n";
	//print_r($row);
	//echo "</pre>\n";

	echo "<tr>\n";
	echo "<td valign='top' class='".$rowstyle[$c]."'>".$name."</td>\n";
	echo "<td valign='top' class='".$rowstyle[$c]."'>".$consumer_count."</td>\n";
	echo "<td valign='top' class='".$rowstyle[$c]."'>".$caller_count."</td>\n";
	echo "<td valign='top' class='".$rowstyle[$c]."'>".$waiting_count."</td>\n";
	echo "<td valign='top' class='".$rowstyle[$c]."'>".$importance."</td>\n";
	echo "<td valign='top' class='".$rowstyle[$c]."'><a href='v_fifo_interactive.php?c=".$name."'>view</a></td>\n";
	echo "</tr>\n";

	if ($c==0) { $c=1; } else { $c=0; }
}
echo "</table>\n";

?>