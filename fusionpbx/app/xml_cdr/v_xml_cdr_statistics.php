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
	function get_call_volume_between($start, $end) {
		global $db, $sql_where;
		$sql = " select count(*) as count from v_xml_cdr ";
		$sql .= $sql_where;
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
//$call_volume_1st_hour

//get the call time in seconds between the start and end time in seconds
	function get_call_seconds_between($start, $end) {
		global $db, $sql_where;
		$sql = " select sum(billsec) as seconds from v_xml_cdr ";
		$sql .= $sql_where;
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
	$call_seconds_1st_hour = get_call_seconds_between(3600, 0);
//if (strlen($call_seconds_1st_hour) == 0) { $call_seconds_1st_hour = 0; }

//get the call volume in a day
	$sql = "";
	$sql .= " select count(*) as count from v_xml_cdr ";
	$sql .= $sql_where;
	$sql .= "and start_epoch BETWEEN ".(time()-$seconds_day)." AND ".time()." ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$result = $prep_statement->fetchAll(PDO::FETCH_ASSOC);
	$result_count = count($result);
	unset ($prep_statement, $sql);
	if ($result_count > 0) {
		foreach($result as $row) {
			$call_volume_day .= $row['count'];
		}
	}
	unset($prep_statement, $result, $result_count, $sql);

//get the call time in a day
	$sql = "";
	$sql .= " select sum(billsec) as seconds from v_xml_cdr ";
	$sql .= $sql_where;
	$sql .= "and start_epoch BETWEEN ".(time()-$seconds_day)." AND ".time()." ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$result = $prep_statement->fetchAll(PDO::FETCH_ASSOC);
	$result_count = count($result);
	unset ($prep_statement, $sql);
	if ($result_count > 0) {
		foreach($result as $row) {
			$call_seconds_day .= $row['seconds'];
		}
	}
	unset($prep_statement, $result, $result_count, $sql);
	if (strlen($call_seconds_day) == 0) { $call_seconds_day = 0; }

//get the call volume in a week
	$sql = "";
	$sql .= " select count(*) as count from v_xml_cdr ";
	$sql .= $sql_where;
	$sql .= "and start_epoch BETWEEN ".(time()-$seconds_week)." AND ".time()." ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$result = $prep_statement->fetchAll(PDO::FETCH_ASSOC);
	$result_count = count($result);
	unset ($prep_statement, $sql);
	if ($result_count > 0) {
		foreach($result as $row) {
			$call_volume_week .= $row['count'];
		}
	}
	unset($prep_statement, $result, $result_count, $sql);

//get the call time in a week
	$sql = "";
	$sql .= " select sum(billsec) as seconds from v_xml_cdr ";
	$sql .= $sql_where;
	$sql .= "and start_epoch BETWEEN ".(time()-$seconds_week)." AND ".time()." ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$result = $prep_statement->fetchAll(PDO::FETCH_ASSOC);
	$result_count = count($result);
	unset ($prep_statement, $sql);
	if ($result_count > 0) {
		foreach($result as $row) {
			$call_seconds_week .= $row['seconds'];
		}
	}
	unset($prep_statement, $result, $result_count, $sql);
	if (strlen($call_seconds_week) == 0) { $call_seconds_week = 0; }

//get the call volume in a month
	$sql = "";
	$sql .= " select count(*) as count from v_xml_cdr ";
	$sql .= $sql_where;
	$sql .= "and start_epoch BETWEEN ".(time()-$seconds_month)." AND ".time()." ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$result = $prep_statement->fetchAll(PDO::FETCH_ASSOC);
	$result_count = count($result);
	unset ($prep_statement, $sql);
	if ($result_count > 0) { 
		foreach($result as $row) {
			$call_volume_month .= $row['count'];
		}
	}
	unset($prep_statement, $result, $result_count, $sql);	

//get the call time in a month
	$sql = "";
	$sql .= " select sum(billsec) as seconds from v_xml_cdr ";
	$sql .= $sql_where;
	$sql .= "and start_epoch BETWEEN ".(time()-$seconds_month)." AND ".time()." ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$result = $prep_statement->fetchAll(PDO::FETCH_ASSOC);
	$result_count = count($result);
	unset ($prep_statement, $sql);
	if ($result_count > 0) {
		foreach($result as $row) {
			$call_seconds_month .= $row['seconds'];
		}
	}
	unset($prep_statement, $result, $result_count, $sql);
	if (strlen($call_seconds_month) == 0) { $call_seconds_month = 0; }

//set the style
	$c = 0;
	$row_style["0"] = "row_style0";
	$row_style["1"] = "row_style1";

//show the results
	echo "<table width='100%' cellpadding='0' cellspacing='0'>\n";
	echo "<tr>\n";
	echo "	<th>Hour</th>\n";
	echo "	<th>Volume</th>\n";
	echo "	<th>Minutes</th>\n";
	echo "</tr>\n";

	echo "<tr >\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>1st</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>".get_call_volume_between(3600, 0)."</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>".get_call_seconds_between(3600, 0)."</td>\n";
	echo "</tr >\n";
	if ($c==0) { $c=1; } else { $c=0; }
	echo "<tr >\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>2nd</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>".get_call_volume_between((3600*2), (3600*1))."</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>".get_call_seconds_between((3600*2), (3600*1))."</td>\n";
	echo "</tr >\n";
	if ($c==0) { $c=1; } else { $c=0; }
	echo "<tr >\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>3rd</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>".get_call_volume_between((3600*3), (3600*2))."</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>".get_call_seconds_between((3600*3), (3600*2))."</td>\n";
	echo "</tr >\n";
	if ($c==0) { $c=1; } else { $c=0; }
	echo "<tr >\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>4th</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>".get_call_volume_between((3600*4), (3600*3))."</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>".get_call_seconds_between((3600*4), (3600*3))."</td>\n";
	echo "</tr >\n";
	if ($c==0) { $c=1; } else { $c=0; }
	echo "<tr >\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>5th</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>".get_call_volume_between((3600*5), (3600*4))."</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>".get_call_seconds_between((3600*5), (3600*4))."</td>\n";
	echo "</tr >\n";
	if ($c==0) { $c=1; } else { $c=0; }
	echo "<tr >\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>6th</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>".get_call_volume_between((3600*6), (3600*5))."</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>".get_call_seconds_between((3600*6), (3600*5))."</td>\n";
	echo "</tr >\n";
	if ($c==0) { $c=1; } else { $c=0; }
	echo "<tr >\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>7th</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>".get_call_volume_between((3600*7), (3600*6))."</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>".get_call_seconds_between((3600*7), (3600*6))."</td>\n";
	echo "</tr >\n";
	if ($c==0) { $c=1; } else { $c=0; }
	echo "<tr >\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>8th</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>".get_call_volume_between((3600*8), (3600*7))."</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>".get_call_seconds_between((3600*8), (3600*7))."</td>\n";
	echo "</tr >\n";
	if ($c==0) { $c=1; } else { $c=0; }
	echo "<tr >\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>9th</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>".get_call_volume_between((3600*9), (3600*8))."</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>".get_call_seconds_between((3600*9), (3600*8))."</td>\n";
	echo "</tr >\n";
	if ($c==0) { $c=1; } else { $c=0; }
	echo "<tr >\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>10th</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>".get_call_volume_between((3600*10), (3600*9))."</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>".get_call_seconds_between((3600*10), (3600*9))."</td>\n";
	echo "</tr >\n";
	if ($c==0) { $c=1; } else { $c=0; }
	echo "<tr >\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>11th</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>".get_call_volume_between((3600*11), (3600*10))."</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>".get_call_seconds_between((3600*11), (3600*10))."</td>\n";
	echo "</tr >\n";
	if ($c==0) { $c=1; } else { $c=0; }
	echo "<tr >\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>12th</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>".get_call_volume_between((3600*12), (3600*11))."</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>".get_call_seconds_between((3600*12), (3600*11))."</td>\n";
	echo "</tr >\n";
	if ($c==0) { $c=1; } else { $c=0; }
	echo "<tr >\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>13th</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>".get_call_volume_between((3600*13), (3600*12))."</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>".get_call_seconds_between((3600*13), (3600*12))."</td>\n";
	echo "</tr >\n";
	if ($c==0) { $c=1; } else { $c=0; }
	echo "<tr >\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>14th</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>".get_call_volume_between((3600*14), (3600*13))."</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>".get_call_seconds_between((3600*14), (3600*13))."</td>\n";
	echo "</tr >\n";
	if ($c==0) { $c=1; } else { $c=0; }
	echo "<tr >\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>15th</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>".get_call_volume_between((3600*15), (3600*14))."</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>".get_call_seconds_between((3600*15), (3600*14))."</td>\n";
	echo "</tr >\n";
	if ($c==0) { $c=1; } else { $c=0; }
	echo "<tr >\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>16th</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>".get_call_volume_between((3600*16), (3600*15))."</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>".get_call_seconds_between((3600*16), (3600*15))."</td>\n";
	echo "</tr >\n";
	if ($c==0) { $c=1; } else { $c=0; }
	echo "<tr >\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>17th</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>".get_call_volume_between((3600*17), (3600*16))."</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>".get_call_seconds_between((3600*17), (3600*16))."</td>\n";
	echo "</tr >\n";
	if ($c==0) { $c=1; } else { $c=0; }
	echo "<tr >\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>18th</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>".get_call_volume_between((3600*18), (3600*17))."</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>".get_call_seconds_between((3600*18), (3600*17))."</td>\n";
	echo "</tr >\n";
	if ($c==0) { $c=1; } else { $c=0; }
	echo "<tr >\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>19th</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>".get_call_volume_between((3600*19), (3600*18))."</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>".get_call_seconds_between((3600*19), (3600*18))."</td>\n";
	echo "</tr >\n";
	if ($c==0) { $c=1; } else { $c=0; }
	echo "<tr >\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>20th</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>".get_call_volume_between((3600*20), (3600*19))."</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>".get_call_seconds_between((3600*20), (3600*19))."</td>\n";
	echo "</tr >\n";
	if ($c==0) { $c=1; } else { $c=0; }
	echo "<tr >\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>21st</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>".get_call_volume_between((3600*21), (3600*20))."</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>".get_call_seconds_between((3600*21), (3600*20))."</td>\n";
	echo "</tr >\n";
	if ($c==0) { $c=1; } else { $c=0; }
	echo "<tr >\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>22nd</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>".get_call_volume_between((3600*22), (3600*21))."</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>".get_call_seconds_between((3600*22), (3600*21))."</td>\n";
	echo "</tr >\n";
	if ($c==0) { $c=1; } else { $c=0; }
	echo "<tr >\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>23rd</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>".get_call_volume_between((3600*23), (3600*22))."</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>".get_call_seconds_between((3600*23), (3600*22))."</td>\n";
	echo "</tr >\n";
	if ($c==0) { $c=1; } else { $c=0; }
	echo "<tr >\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>24th</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>".get_call_volume_between((3600*24), (3600*23))."</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>".get_call_seconds_between((3600*24), (3600*23))."</td>\n";
	echo "</tr >\n";
	if ($c==0) { $c=1; } else { $c=0; }

	echo "<tr>\n";
	echo "	<td colspan='3'><br /><br /></td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "	<th>Time</th>\n";
	echo "	<th>Volume</th>\n";
	echo "	<th>Minutes</th>\n";
	echo "</tr>\n";
	echo "<tr >\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>Day</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>".$call_volume_day."</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>".$call_seconds_day."</td>\n";
	echo "</tr >\n";
	if ($c==0) { $c=1; } else { $c=0; }

	echo "<tr >\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>Week</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>".$call_volume_week."</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>".$call_seconds_week."</td>\n";
	echo "</tr >\n";
	if ($c==0) { $c=1; } else { $c=0; }

	echo "<tr >\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>Month</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>".$call_volume_month."</td>\n";
	echo "	<td valign='top' class='".$row_style[$c]."'>".$call_seconds_month."</td>\n";
	echo "</tr >\n";
	if ($c==0) { $c=1; } else { $c=0; }

	echo "</table>";
	echo "</div>";
	echo "<br><br>";
	echo "<br><br>";

//show the footer
	require_once "includes/footer.php";
?>