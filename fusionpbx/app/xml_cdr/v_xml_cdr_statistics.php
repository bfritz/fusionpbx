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
	Portions created by the Initial Developer are Copyright (C) 2008-2012
	the Initial Developer. All Rights Reserved.

	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>
*/
require_once "root.php";
require_once "includes/require.php";
require_once "includes/checkauth.php";
if (permission_exists('xml_cdr_view')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

//additional includes
	require_once "includes/header.php";

//page title and description
	echo "<div align='center'>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
	echo "<tr>\n";
	echo "	<td width='30%' align='left' valign='top' nowrap='nowrap'><b>Call Detail Record Statistics</b></td>\n";
	echo "	<td width='70%' align='right' valign='top'>\n";
	echo "		<input type='button' class='btn' name='' alt='back' onclick=\"window.location='v_xml_cdr.php'\" value='Back'>\n";
	echo "	</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "		<td align='left' colspan='2'>\n";
	echo "			Call Detail Records Statics summarize the call information. \n";
	echo "			<br />\n";
	echo "			<br />\n";
	echo "		</td>\n";
	echo "</tr>\n";
	echo "</table>\n";

//show all call detail records to admin and superadmin. for everyone else show only the call details for extensions assigned to them
	if (!if_group("admin") && !if_group("superadmin")) {
		// select caller_id_number, destination_number from v_xml_cdr where domain_uuid = '' 
		// and (caller_id_number = '1001' or destination_number = '1001' or destination_number = '*991001')
		$sql_where = "where domain_uuid = '$domain_uuid' and ( ";
		if (count($_SESSION['user']['extension']) > 0) {
			$x = 0;
			foreach($_SESSION['user']['extension'] as $row) {
				if ($x==0) {
					if ($row['user'] > 0) { $sql_where .= "caller_id_number = '".$row['user']."' \n"; } //source
				}
				else {
					if ($row['user'] > 0) { $sql_where .= "or caller_id_number = '".$row['user']."' \n"; } //source
				}
				if ($row['user'] > 0) { $sql_where .= "or destination_number = '".$row['user']."' \n"; } //destination
				if ($row['user'] > 0) { $sql_where .= "or destination_number = '*99".$row['user']."' \n"; } //destination
				$x++;
			}
		}
		$sql_where .= ") ";
	}
	else {
		//superadmin or admin
		$sql_where = "where domain_uuid = '$domain_uuid' ";
	}

//create the sql query to get the xml cdr records
	if (strlen($order_by) == 0)  { $order_by  = "start_epoch"; }
	if (strlen($order) == 0)  { $order  = "desc"; }

//calculate the seconds in different time frames
	$seconds_hour = 3600;
	$seconds_day = $seconds_hour * 24;
	$seconds_week = $seconds_day * 7;
	$seconds_month = $seconds_week * 4;

//get the call volume between a start end end time in seconds
	function get_call_volume_between($start, $end, $where) {
		global $db;
		if (strlen($where) == 0) {
			$where = "where domain_uuid = '".$_SESSION['domain_uuid']."' ";
		}
		$sql = " select count(*) as count from v_xml_cdr ";
		$sql .= $where;
		$sql .= "and start_epoch BETWEEN ".(time()-$start)." AND ".(time()-$end)." ";
		$prep_statement = $db->prepare(check_sql($sql));
		$prep_statement->execute();
		$result = $prep_statement->fetchAll(PDO::FETCH_ASSOC);
		unset ($prep_statement, $sql);
		if (count($result) > 0) {
			foreach($result as $row) {
				return $row['count'];
			}
		}
		else {
			return false;
		}
		unset($prep_statement, $result, $sql);
	}
	$call_volume_1st_hour = get_call_volume_between(3600, 0);

//get the call time in seconds between the start and end time in seconds
	function get_call_seconds_between($start, $end, $where) {
		global $db;
		if (strlen($where) == 0) {
			$where = "where domain_uuid = '".$_SESSION['domain_uuid']."' ";
		}
		$sql = " select sum(billsec) as seconds from v_xml_cdr ";
		$sql .= $where;
		$sql .= "and start_epoch BETWEEN ".(time()-$start)." AND ".(time()-$end)." ";
		$prep_statement = $db->prepare(check_sql($sql));
		$prep_statement->execute();
		$result = $prep_statement->fetchAll(PDO::FETCH_ASSOC);
		unset ($prep_statement, $sql);
		if (count($result) > 0) {
			foreach($result as $row) {
				$result = $row['seconds'];
				if (strlen($result) == 0) {
					return 0;
				}
				else {
					return $row['seconds'];
				}
			}
		}
		else {
			return false;
		}
		unset($prep_statement, $result, $sql);
	}
	//$call_seconds_1st_hour = get_call_seconds_between(3600, 0);
	//if (strlen($call_seconds_1st_hour) == 0) { $call_seconds_1st_hour = 0; }


//set the style
	$c = 0;
	$row_style["0"] = "row_style0";
	$row_style["1"] = "row_style1";

//call info hour by hour
	for ($i = 1; $i <= 24; $i++) {
		$stats[$i]['volume'] = get_call_volume_between(3600*$i, 3600*($i-1), '');
		$stats[$i]['seconds'] = get_call_seconds_between(3600*$i, 3600*($i-1), '');
		$stats[$i]['minutes'] = $stats[$i]['seconds'] / 60;
		$stats[$i]['avg_sec'] = $stats[$i]['seconds'] / $stats[$i]['volume'];
		$stats[$i]['avg_min'] = $stats[$i]['minutes'] / $stats[$i]['volume'];
		
		$where = "where domain_uuid = '".$_SESSION['domain_uuid']."' ";
		$where .= "and billsec = '0' ";
		$stats[$i]['missed'] = get_call_volume_between(3600*$i, 3600*($i-1), $where);
		$stats[$i]['asr'] = ($stats[$i]['volume'] - $stats[$i]['missed']) / $stats[$i]['volume'];
	}

//call info for a day
	$stats[$i]['volume'] = get_call_volume_between($seconds_day, 0, '');
	$stats[$i]['seconds'] = get_call_seconds_between($seconds_day, 0, '');
	$stats[$i]['minutes'] = $stats[$i]['seconds'] / 60;
	$stats[$i]['avg_sec'] = $stats[$i]['seconds'] / $stats[$i]['volume'];
	$stats[$i]['avg_min'] = $stats[$i]['minutes'] / $stats[$i]['volume'];
	$where = "where domain_uuid = '".$_SESSION['domain_uuid']."' ";
	$where .= "and billsec = '0' ";
	$stats[$i]['missed'] = get_call_volume_between($seconds_day, 0, $where);
	$stats[$i]['asr'] = ($stats[$i]['volume'] - $stats[$i]['missed']) / $stats[$i]['volume'];
	$i++;

//call info for a week
	$stats[$i]['volume'] = get_call_volume_between($seconds_week, 0, '');
	$stats[$i]['seconds'] = get_call_seconds_between($seconds_week, 0, '');
	$stats[$i]['minutes'] = $stats[$i]['seconds'] / 60;
	$stats[$i]['avg_sec'] = $stats[$i]['seconds'] / $stats[$i]['volume'];
	$stats[$i]['avg_min'] = $stats[$i]['minutes'] / $stats[$i]['volume'];
	$where = "where domain_uuid = '".$_SESSION['domain_uuid']."' ";
	$where .= "and billsec = '0' ";
	$stats[$i]['missed'] = get_call_volume_between($seconds_week, 0, $where);
	$stats[$i]['asr'] = ($stats[$i]['volume'] - $stats[$i]['missed']) / $stats[$i]['volume'];
	$i++;

//call info for a month
	$stats[$i]['volume'] = get_call_volume_between($seconds_month, 0, '');
	$stats[$i]['seconds'] = get_call_seconds_between($seconds_month, 0, '');
	$stats[$i]['minutes'] = $stats[$i]['seconds'] / 60;
	$stats[$i]['avg_sec'] = $stats[$i]['seconds'] / $stats[$i]['volume'];
	$stats[$i]['avg_min'] = $stats[$i]['minutes'] / $stats[$i]['volume'];
	$where = "where domain_uuid = '".$_SESSION['domain_uuid']."' ";
	$where .= "and billsec = '0' ";
	$stats[$i]['missed'] = get_call_volume_between($seconds_month, 0, $where);
	$stats[$i]['asr'] = ($stats[$i]['volume'] - $stats[$i]['missed']) / $stats[$i]['volume'];
	$i++;

//show the results
	echo "<table width='100%' cellpadding='0' cellspacing='0'>\n";
	echo "<tr>\n";
	echo "	<th>Hour</th>\n";
	echo "	<th>Volume</th>\n";
	echo "	<th>Minutes</th>\n";
	echo "	<th>Calls Per Min</th>\n";
	echo "	<th>Missed</th>\n";
	echo "	<th>ASR</th>\n";
	echo "</tr>\n";

	$i = 0;
	foreach ($stats as $row) {
		echo "<tr >\n";
		if ($i < 24) {
			echo "	<td valign='top' class='".$row_style[$c]."'>".$i."</td>\n";
		}
		elseif ($i == 24) {
			echo "	<td valign='top' class='".$row_style[$c]."'>Day</td>\n";
		}
		elseif ($i == 25) {
			echo "	<td valign='top' class='".$row_style[$c]."'>Week</td>\n";
		}
		elseif ($i == 26) {
			echo "	<td valign='top' class='".$row_style[$c]."'>Month</td>\n";
		}
		echo "	<td valign='top' class='".$row_style[$c]."'>".$row['volume']."&nbsp;</td>\n";
		echo "	<td valign='top' class='".$row_style[$c]."'>".(round($row['minutes'],2))."&nbsp;</td>\n";
		echo "	<td valign='top' class='".$row_style[$c]."'>".(round($row['avg_min'],2))."&nbsp;</td>\n";
		echo "	<td valign='top' class='".$row_style[$c]."'>".$row['missed']."&nbsp;</td>\n";
		echo "	<td valign='top' class='".$row_style[$c]."'>".(round($row['asr'],2))."&nbsp;</td>\n";
		echo "</tr >\n";
		if ($c==0) { $c=1; } else { $c=0; }
		$i++;
	}
	echo "</table>\n";

//show the footer
	require_once "includes/footer.php";
?>