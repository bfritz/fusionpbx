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
	$seconds_month = $seconds_day * 30;

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

//round down to the nearest hour
	$time = time() - time() % 3600;

//call info hour by hour
	for ($i = 1; $i <= 24; $i++) {
		$stats[$i]['volume'] = get_call_volume_between(3600*$i, 3600*($i-1), '');
		$stats[$i]['start_epoch'] = $time - 3600*$i;
		$stats[$i]['stop_epoch'] = $time - 3600*($i-1);
		$stats[$i]['seconds'] = get_call_seconds_between(3600*$i, 3600*($i-1), '');
		$stats[$i]['minutes'] = $stats[$i]['seconds'] / 60;
		$stats[$i]['avg_sec'] = $stats[$i]['seconds'] / $stats[$i]['volume'];
		$stats[$i]['avg_min'] = ($stats[$i]['volume'] - $stats[$i]['missed']) / 60;
		
		//answer / seizure ratio
		$where = "where domain_uuid = '".$_SESSION['domain_uuid']."' ";
		$where .= "and billsec = '0' ";
		$stats[$i]['missed'] = get_call_volume_between(3600*$i, 3600*($i-1), $where);
		$stats[$i]['asr'] = (($stats[$i]['volume'] - $stats[$i]['missed']) / ($stats[$i]['volume']) * 100);

		//average length of call
		$stats[$i]['aloc'] = $stats[$i]['minutes'] / ($stats[$i]['volume'] - $stats[$i]['missed']);
	}

//call info for a day
	$stats[$i]['volume'] = get_call_volume_between($seconds_day, 0, '');
	$stats[$i]['seconds'] = get_call_seconds_between($seconds_day, 0, '');
	$stats[$i]['start_epoch'] = time() - $seconds_day;
	$stats[$i]['stop_epoch'] = time();
	$stats[$i]['minutes'] = $stats[$i]['seconds'] / 60;
	$stats[$i]['avg_sec'] = $stats[$i]['seconds'] / $stats[$i]['volume'];
	$stats[$i]['avg_min'] = ($stats[$i]['volume'] - $stats[$i]['missed']) / (60*24);
	$where = "where domain_uuid = '".$_SESSION['domain_uuid']."' ";
	$where .= "and billsec = '0' ";
	$stats[$i]['missed'] = get_call_volume_between($seconds_day, 0, $where);
	$stats[$i]['asr'] = (($stats[$i]['volume'] - $stats[$i]['missed']) / ($stats[$i]['volume']) * 100);
	$stats[$i]['aloc'] = $stats[$i]['minutes'] / ($stats[$i]['volume'] - $stats[$i]['missed']);
	$i++;

//call info for a week
	$stats[$i]['volume'] = get_call_volume_between($seconds_week, 0, '');
	$stats[$i]['seconds'] = get_call_seconds_between($seconds_week, 0, '');
	$stats[$i]['start_epoch'] = time() - $seconds_week;
	$stats[$i]['stop_epoch'] = time();
	$stats[$i]['minutes'] = $stats[$i]['seconds'] / 60;
	$stats[$i]['avg_sec'] = $stats[$i]['seconds'] / $stats[$i]['volume'];
	$stats[$i]['avg_min'] = ($stats[$i]['volume'] - $stats[$i]['missed']) / (60*24*7);
	$where = "where domain_uuid = '".$_SESSION['domain_uuid']."' ";
	$where .= "and billsec = '0' ";
	$stats[$i]['missed'] = get_call_volume_between($seconds_week, 0, $where);
	$stats[$i]['asr'] = (($stats[$i]['volume'] - $stats[$i]['missed']) / ($stats[$i]['volume']) * 100);
	$stats[$i]['aloc'] = $stats[$i]['minutes'] / ($stats[$i]['volume'] - $stats[$i]['missed']);
	$i++;

//call info for a month
	$stats[$i]['volume'] = get_call_volume_between($seconds_month, 0, '');
	$stats[$i]['seconds'] = get_call_seconds_between($seconds_month, 0, '');
	$stats[$i]['start_epoch'] = time() - $seconds_month;
	$stats[$i]['stop_epoch'] = time();
	$stats[$i]['minutes'] = $stats[$i]['seconds'] / 60;
	$stats[$i]['avg_sec'] = $stats[$i]['seconds'] / $stats[$i]['volume'];
	$stats[$i]['avg_min'] = ($stats[$i]['volume'] - $stats[$i]['missed']) / (60*24*30);
	$where = "where domain_uuid = '".$_SESSION['domain_uuid']."' ";
	$where .= "and billsec = '0' ";
	$stats[$i]['missed'] = get_call_volume_between($seconds_month, 0, $where);
	$stats[$i]['asr'] = (($stats[$i]['volume'] - $stats[$i]['missed']) / ($stats[$i]['volume']) * 100);
	$stats[$i]['aloc'] = $stats[$i]['minutes'] / ($stats[$i]['volume'] - $stats[$i]['missed']);
	$i++;

//show the graph
	$x = 0;
	foreach ($stats as $row) {
		$graph['volume'][$x][] = $x+1;
		$graph['volume'][$x][] = $row['volume']/1;
		if ($x == 23) { break; }
		$x++;
	}
	$x = 0;
	foreach ($stats as $row) {
		$graph['minutes'][$x][] = $x+1;
		$graph['minutes'][$x][] = round($row['minutes'],2);
		if ($x == 23) { break; }
		$x++;
	}
	$x = 0;
	foreach ($stats as $row) {
		$graph['call_per_min'][$x][] = $x+1;
		$graph['call_per_min'][$x][] = round($row['avg_min'],2);
		if ($x == 23) { break; }
		$x++;
	}
	$x = 0;
	foreach ($stats as $row) {
		$graph['missed'][$x][] = $x+1;
		$graph['missed'][$x][] = $row['missed']/1;
		if ($x == 23) { break; }
		$x++;
	}
	$x = 0;
	foreach ($stats as $row) {
		$graph['asr'][$x][] = $x+1;
		$graph['asr'][$x][] = round($row['asr'],2)/100;
		if ($x == 23) { break; }
		$x++;
	}
	$x = 0;
	foreach ($stats as $row) {
		$graph['aloc'][$x][] = $x+1;
		$graph['aloc'][$x][] = round($row['aloc'],2);
		if ($x == 23) { break; }
		$x++;
	}
	?>
	<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="/includes/jquery/flot/excanvas.min.js"></script><![endif]-->
    <script language="javascript" type="text/javascript" src="/includes/jquery/jquery-1.7.2.min.js"></script>
    <script language="javascript" type="text/javascript" src="/includes/jquery/flot/jquery.flot.js"></script>
	<table>
		<tr>
			<td align='left'>
				<div id="placeholder" style="width:700px;height:180px;"></div>
			</td>
			<td align='left' valign='top'>
				<p id="choices"></p>
			</td>
		</tr>
	</table>
	<script type="text/javascript">
	$(function () {
		var datasets = {
			"volume": {
				label: "Volume",
				data: <?php echo json_encode($graph['volume']); ?>
			},
			"minutes": {
				label: "Minutes",
				data: <?php echo json_encode($graph['minutes']); ?>
			},
			"call_per_min": {
				label: "Calls Per Min",
				data: <?php echo json_encode($graph['call_per_min']); ?>
			},
			"missed": {
				label: "Missed",
				data: <?php echo json_encode($graph['missed']); ?>
			},
			"asr": {
				label: "ASR",
				data: <?php echo json_encode($graph['asr']); ?>
			},
			"aloc": {
				label: "ALOC",
				data: <?php echo json_encode($graph['aloc']); ?>
			},		
		};

		// hard-code color indices to prevent them from shifting as
		// countries are turned on/off
		var i = 0;
		$.each(datasets, function(key, val) {
			val.color = i;
			++i;
		});
		
		// insert checkboxes 
		var choiceContainer = $("#choices");
		$.each(datasets, function(key, val) {
			choiceContainer.append('<br /><input type="checkbox" name="' + key +
								   '" checked="checked" id="id' + key + '">' +
								   '<label for="id' + key + '">'
									+ val.label + '</label>');
		});
		choiceContainer.find("input").click(plotAccordingToChoices);

		
		function plotAccordingToChoices() {
			var data = [];

			choiceContainer.find("input:checked").each(function () {
				var key = $(this).attr("name");
				if (key && datasets[key])
					data.push(datasets[key]);
			});

			if (data.length > 0)
				$.plot($("#placeholder"), data, {
					yaxis: { min: 0 },
					xaxis: { tickDecimals: 0 }
				});
		}

		plotAccordingToChoices();
	});
	</script>
	<?php

//show the results
	echo "<table width='100%' cellpadding='0' cellspacing='0'>\n";
	echo "<tr>\n";
	echo "	<th>Hours</th>\n";
	echo "	<th>Date</th>\n";
	echo "	<th nowrap='nowrap'>Time</th>\n";
	echo "	<th>Volume</th>\n";
	echo "	<th>Minutes</th>\n";
	echo "	<th>Calls Per Min</th>\n";
	echo "	<th>Missed</th>\n";
	echo "	<th>ASR</th>\n";
	echo "	<th>ALOC</th>\n";
	echo "</tr>\n";

	$i = 0;
	foreach ($stats as $row) {
		echo "<tr >\n";
		if ($i < 24) {
			echo "	<td valign='top' class='".$row_style[$c]."'>".($i+1)."</td>\n";
		}
		elseif ($i == 24) {
			echo "	<br /><br />\n";
			echo "</tr>\n";
			echo "<tr>\n";
			echo "	<td>\n";
			echo "		<br /><br />\n";
			echo "	</td>\n";
			echo "</tr>\n";
			echo "<tr>\n";
			echo "	<th nowrap='nowrap'>Days</th>\n";
			echo "	<th nowrap='nowrap'>Date</th>\n";
			echo "	<th nowrap='nowrap'>Time</th>\n";
			echo "	<th>Volume</th>\n";
			echo "	<th>Minutes</th>\n";
			echo "	<th nowrap='nowrap'>Calls Per Min</th>\n";
			echo "	<th>Missed</th>\n";
			echo "	<th>ASR</th>\n";
			echo "	<th>ALOC</th>\n";
			echo "</tr>\n";
			echo "<tr>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>1</td>\n";
		}
		elseif ($i == 25) {
			echo "<tr>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>7</td>\n";
		}
		elseif ($i == 26) {
			echo "	<td valign='top' class='".$row_style[$c]."'>30</td>\n";
		}
		if ($i < 24) {
			echo "	<td valign='top' class='".$row_style[$c]."'>".date('j M', $row['start_epoch'])."</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".date('H:i', $row['start_epoch'])." - ".date('H:i', $row['stop_epoch'])."&nbsp;</td>\n";
		}
		else {
			echo "	<td valign='top' class='".$row_style[$c]."'>".date('j M', $row['start_epoch'])."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".date('H:i', $row['start_epoch'])." - ".date('j M H:i', $row['stop_epoch'])."&nbsp;</td>\n";
		}
		echo "	<td valign='top' class='".$row_style[$c]."'>".$row['volume']."&nbsp;</td>\n";
		echo "	<td valign='top' class='".$row_style[$c]."'>".(round($row['minutes'],2))."&nbsp;</td>\n";
		echo "	<td valign='top' class='".$row_style[$c]."'>".(round($row['avg_min'],2))."&nbsp;</td>\n";
		echo "	<td valign='top' class='".$row_style[$c]."'>".$row['missed']."&nbsp;</td>\n";
		echo "	<td valign='top' class='".$row_style[$c]."'>".(round($row['asr'],2))."&nbsp;</td>\n";
		echo "	<td valign='top' class='".$row_style[$c]."'>".(round($row['aloc'],2))."&nbsp;</td>\n";
		echo "</tr >\n";
		if ($c==0) { $c=1; } else { $c=0; }
		$i++;
	}
	echo "</table>\n";

//show the footer
	require_once "includes/footer.php";
?>