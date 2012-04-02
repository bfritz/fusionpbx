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

//import xml_cdr files
	require_once "v_xml_cdr_import.php";

//additional includes
	require_once "includes/header.php";
	require_once "includes/paging.php";

//set 24hr or 12hr clock
	define('TIME_24HR', 1);

//get post or get variables from http
	if (count($_REQUEST)>0) {
		$order_by = $_REQUEST["order_by"];
		$order = $_REQUEST["order"];
		$cdr_id = $_REQUEST["cdr_id"];
		$direction = $_REQUEST["direction"];
		$caller_id_name = $_REQUEST["caller_id_name"];
		$caller_id_number = $_REQUEST["caller_id_number"];
		$destination_number = $_REQUEST["destination_number"];
		$context = $_REQUEST["context"];
		$start_stamp = $_REQUEST["start_stamp"];
		$answer_stamp = $_REQUEST["answer_stamp"];
		$end_stamp = $_REQUEST["end_stamp"];
		$duration = $_REQUEST["duration"];
		$billsec = $_REQUEST["billsec"];
		$hangup_cause = $_REQUEST["hangup_cause"];
		$uuid = $_REQUEST["uuid"];
		$bleg_uuid = $_REQUEST["bleg_uuid"];
		$accountcode = $_REQUEST["accountcode"];
		$read_codec = $_REQUEST["read_codec"];
		$write_codec = $_REQUEST["write_codec"];
		$remote_media_ip = $_REQUEST["remote_media_ip"];
		$network_addr = $_REQUEST["network_addr"];
	}

//page title and description
	echo "<div align='center'>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
	echo "<tr>\n";
	echo "<td align='left' width='50%' nowrap='nowrap'><b>Call Detail Records</b></td>\n";
	echo "<td align='right' width='100%'>\n";
	echo "<table>\n";
	echo "<tr>\n";
	echo "<td>\n";
	echo "	<input type='button' class='btn' value='Statistics' onclick=\"document.location.href='v_xml_cdr_statistics.php';\">\n";
	echo "</td>\n";
	echo "<form method='post' action='v_xml_cdr_csv.php'>";
	echo "<td>\n";
	echo "	<input type='hidden' name='caller_id_name' value='$caller_id_name'>\n";
	echo "	<input type='hidden' name='start_stamp' value='$start_stamp'>\n";
	echo "	<input type='hidden' name='hangup_cause' value='$hangup_cause'>\n";
	echo "	<input type='hidden' name='caller_id_number' value='$caller_id_number'>\n";
	echo "	<input type='hidden' name='destination_number' value='$destination_number'>\n";
	//echo "	<input type='hidden' name='context' value='$context'>\n";
	echo "	<input type='hidden' name='answer_stamp' value='$answer_stamp'>\n";
	echo "	<input type='hidden' name='end_stamp' value='$end_stamp'>\n";
	echo "	<input type='hidden' name='duration' value='$duration'>\n";
	echo "	<input type='hidden' name='billsec' value='$billsec'>\n";
	echo "	<input type='hidden' name='uuid' value='$uuid'>\n";
	echo "	<input type='hidden' name='bleg_uuid' value='$bleg_uuid'>\n";
	echo "	<input type='hidden' name='accountcode' value='$accountcode'>\n";
	echo "	<input type='hidden' name='read_codec' value='$read_codec'>\n";
	echo "	<input type='hidden' name='write_codec' value='$write_codec'>\n";
	echo "	<input type='hidden' name='remote_media_ip' value='$remote_media_ip'>\n";
	echo "	<input type='hidden' name='network_addr' value='$network_addr'>\n";
	echo "	<input type='submit' class='btn' name='submit' value=' csv '>\n";
	echo "</td>\n";
	echo "	</form>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td align='left' colspan='2'>\n";

	echo "Call Detail Records (CDRs) are detailed information on the calls. \n";
	echo "The information contains source, destination, duration, and other useful call details. \n";
	echo "Use the fields to filter the information for the specific call records that are desired. \n";
	echo "Then view the calls in the list or download them as comma seperated file by using the 'csv' button. \n";
	//To do an advanced search of the call detail records click on the following advanced button.

	echo "<br />\n";
	echo "<br />\n";

	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";

	//search the call detail records
		if (if_group("admin") || if_group("superadmin")) {
			echo "<form method='post' action=''>\n";
			echo "<table width='95%' cellpadding='3' border='0'>\n";
			echo "<tr>\n";
			echo "<td width='33.3%'>\n";
				echo "<table width='100%' border='0'>\n";
				echo "	<tr>\n";
				echo "		<td align='left' width='25%'>Direction:</td>\n";
				echo "		<td align='left' width='75%'>\n";
				echo "			<select name='direction' style='width:100%' class='formfld'>\n";
				echo "			<option value=''>                                </option>\n";
				if ($direction == "inbound") {
					echo "			<option value='inbound' selected='selected'>inbound</option>\n";
				}
				else {
					echo "			<option value='inbound'>inbound</option>\n";
				}
				if ($direction == "outbound") {
					echo "			<option value='outbound' selected='selected'>outbound</option>\n";
				}
				else {
					echo "			<option value='outbound'>outbound</option>\n";
				}
				if ($direction == "local") {
					echo "			<option value='local' selected='selected'>local</option>\n";
				}
				else {
					echo "			<option value='local'>local</option>\n";
				}
				echo "			</select>\n";
				echo "		</td>\n";
				echo "	</tr>\n";

				echo "	<tr>\n";
				echo "		<td align=\"left\">CID Name:</td>\n";
				echo "		<td align=\"left\"><input type='text' class='formfld' name='caller_id_name' style='width:100%' value='$caller_id_name'></td>\n";
				echo "	</tr>\n";

				echo "</table>\n";

			echo "</td>\n";
			echo "<td width='33.3%'>\n";

				echo "<table width='100%'>\n";
				echo "	<tr>\n";
				echo "		<td align='left' width='25%'>Source:</td>\n";
				echo "		<td align='left' width='75%'><input type='text' class='formfld' name='caller_id_number' style='width:100%' value='$caller_id_number'></td>\n";
				echo "	</tr>\n";
				echo "	<tr>\n";
				echo "		<td align='left' width='25%'>Destination:</td>\n";
				echo "		<td align='left' width='75%'><input type='text' class='formfld' name='destination_number' style='width:100%' value='$destination_number'></td>\n";
				echo "	</tr>\n";
				echo "</table>\n";

			echo "</td>\n";
			echo "<td width='33.3%'>\n";

				echo "<table width='100%'>\n";
				//echo "	<tr>";
				//echo "		<td>Context:</td>";
				//echo "		<td><input type='text' class='formfld' name='context' style='width:100%' value='$context'></td>";
				//echo "	</tr>";
				//echo "	<tr>";
				//echo "		<td>Answer:</td>";
				//echo "		<td><input type='text' class='formfld' name='answer_stamp' style='width:100%' value='$answer_stamp'></td>";
				//echo "	</tr>";
				//echo "	<tr>";
				//echo "		<td>End:</td>";
				//echo "		<td><input type='text' class='formfld' name='end_stamp' style='width:100%' value='$end_stamp'></td>";
				//echo "	</tr>";

				echo "	<tr>\n";
				echo "		<td align='left' width='25%'>Start:</td>\n";
				echo "		<td align='left' width='75%'><input type='text' class='formfld' name='start_stamp' style='width:100%' value='$start_stamp'></td>\n";
				echo "	</tr>\n";

				echo "	<tr>\n";
				echo "		<td align='left' width='25%'>Status:</td>\n";
				echo "		<td align='left' width='75%'>\n";
				echo "			<select name=\"hangup_cause\" style='width:100%' class='formfld'>\n";
				echo "			<option value='".$hangup_cause."' selected='selected'>".$hangup_cause."</option>\n";
				if (strlen($hangup_cause) > 0) {
					echo "			<option value=''></option>\n";
				}
				echo "			<option value='NORMAL_CLEARING'>NORMAL_CLEARING</option>\n";
				echo "			<option value='ORIGINATOR_CANCEL'>ORIGINATOR_CANCEL</option>\n";
				echo "			<option value='BLIND_TRANSFER'>BLIND_TRANSFER</option>\n";
				echo "			<option value='LOSE_RACE'>LOSE_RACE</option>\n";
				echo "			<option value='NO_ANSWER'>NO_ANSWER</option>\n";
				echo "			<option value='NORMAL_UNSPECIFIED'>NORMAL_UNSPECIFIED</option>\n";
				echo "			<option value='NO_USER_RESPONSE'>NO_USER_RESPONSE</option>\n";
				echo "			<option value='NO_ROUTE_DESTINATION'>NO_ROUTE_DESTINATION</option>\n";
				echo "			<option value='SUBSCRIBER_ABSENT'>SUBSCRIBER_ABSENT</option>\n";
				echo "			<option value='NORMAL_TEMPORARY_FAILURE'>NORMAL_TEMPORARY_FAILURE</option>\n";
				echo "			<option value='ATTENDED_TRANSFER'>ATTENDED_TRANSFER</option>\n";
				echo "			<option value='PICKED_OFF'>PICKED_OFF</option>\n";
				echo "			<option value='USER_BUSY'>USER_BUSY</option>\n";
				echo "			<option value='CALL_REJECTED'>CALL_REJECTED</option>\n";
				echo "			<option value='INVALID_NUMBER_FORMAT'>INVALID_NUMBER_FORMAT</option>\n";
				echo "			<option value='NETWORK_OUT_OF_ORDER'>NETWORK_OUT_OF_ORDER</option>\n";
				echo "			<option value='DESTINATION_OUT_OF_ORDER'>DESTINATION_OUT_OF_ORDER</option>\n";
				echo "			<option value='RECOVERY_ON_TIMER_EXPIRE'>RECOVERY_ON_TIMER_EXPIRE</option>\n";
				echo "			<option value='MANAGER_REQUEST'>MANAGER_REQUEST</option>\n";
				echo "			<option value='MEDIA_TIMEOUT'>MEDIA_TIMEOUT</option>\n";
				echo "			<option value='UNALLOCATED_NUMBER'>UNALLOCATED_NUMBER</option>\n";
				echo "			<option value='NONE'>NONE</option>\n";
				echo "			<option value='EXCHANGE_ROUTING_ERROR'>EXCHANGE_ROUTING_ERROR</option>\n";
				echo "			<option value='ALLOTTED_TIMEOUT'>ALLOTTED_TIMEOUT</option>\n";
				echo "			<option value='CHAN_NOT_IMPLEMENTED'>CHAN_NOT_IMPLEMENTED</option>\n";
				echo "			<option value='INCOMPATIBLE_DESTINATION'>INCOMPATIBLE_DESTINATION</option>\n";
				echo "			<option value='USER_NOT_REGISTERED'>USER_NOT_REGISTERED</option>\n";
				echo "			<option value='SYSTEM_SHUTDOWN'>SYSTEM_SHUTDOWN</option>\n";
				echo "			<option value='MANDATORY_IE_MISSING'>MANDATORY_IE_MISSING</option>\n";
				/*
				$sql = "";
				$sql .= "select distinct(hangup_cause) from v_xml_cdr ";
				$sql .= "where domain_uuid = '$domain_uuid' ";
				$prep_statement = $db->prepare(check_sql($sql));
				$prep_statement->execute();
				$result = $prep_statement->fetchAll(PDO::FETCH_ASSOC);
				foreach ($result as &$row) {
					if ($row["hangup_cause"] == $hangup_cause) {
						echo "			<option value='".$row["hangup_cause"]."' selected='selected'>".$row["hangup_cause"]."</option>\n";
					}
					else {
						echo "			<option value='".$row["hangup_cause"]."'>".$row["hangup_cause"]."</option>\n";
					}
				}
				unset ($prep_statement);
				*/
				echo "			</select>\n";
				echo "		</td>\n";
				echo "	</tr>\n";

				//echo "	<tr>";
				//echo "		<td align='left' width='25%'>Duration:</td>";
				//echo "		<td align='left' width='75%'><input type='text' class='txt' name='duration' value='$duration'></td>";
				//echo "	</tr>";
				//echo "	<tr>";
				//echo "		<td align='left' width='25%'>Bill:</td>";
				//echo "		<td align='left' width='75%'><input type='text' class='txt' name='billsec' value='$billsec'></td>";
				//echo "	</tr>";
				//echo "	<tr>";
				//echo "		<td>UUID:</td>";
				//echo "		<td><input type='text' class='txt' name='uuid' value='$uuid'></td>";
				//echo "	</tr>";
				//echo "	<tr>";
				//echo "		<td>Bridge UUID:</td>";
				//echo "		<td><input type='text' class='txt' name='bridge_uuid' value='$bridge_uuid'></td>";
				//echo "	</tr>";
				//echo "	<tr>";
				//echo "		<td>Account Code:</td>";
				//echo "		<td><input type='text' class='txt' name='accountcode' value='$accountcode'></td>";
				//echo "	</tr>";
				//echo "	<tr>";
				//echo "		<td>Read Codec:</td>";
				//echo "		<td><input type='text' class='txt' name='read_codec' value='$read_codec'></td>";
				//echo "	</tr>";
				//echo "	<tr>";
				//echo "		<td>Write Codec:</td>";
				//echo "		<td><input type='text' class='txt' name='write_codec' value='$write_codec'></td>";
				//echo "	</tr>";
				//echo "	<tr>";
				//echo "		<td>Remote Media IP:</td>";
				//echo "		<td><input type='text' class='txt' name='remote_media_ip' value='$remote_media_ip'></td>";
				//echo "	</tr>";
				//echo "	<tr>";
				//echo "		<td>Network Address:</td>";
				//echo "		<td><input type='text' class='txt' name='network_addr' value='$network_addr'></td>";
				//echo "	</tr>";
				//echo "	<tr>";

				echo "	</tr>";
				echo "</table>";

			echo "</td>";
			echo "</tr>";
			echo "<tr>\n";
			echo "<td colspan='2' align='right'>\n";
			//echo "	<input type='button' class='btn' name='' alt='view' onclick=\"window.location='v_cdr_search.php'\" value='advanced'>\n";
			echo "</td>\n";
			echo "<td colspan='1' align='right'>\n";
			echo "	<input type='button' class='btn' name='' alt='view' onclick=\"window.location='v_xml_cdr_search.php'\" value='advanced'>&nbsp;\n";
			echo "	<input type='submit' class='btn' name='submit' value='filter'>\n";
			echo "</td>\n";
			echo "</tr>";
			echo "</table>";
			echo "</form>";
		}

//build the sql where string
	if (strlen($cdr_id) > 0) { $sql_where .= "and cdr_id like '%$cdr_id%' "; }
	if (strlen($direction) > 0) { $sql_where .= "and direction like '%$direction%' "; }
	if (strlen($caller_id_name) > 0) { $sql_where .= "and caller_id_name like '%$caller_id_name%' "; }
	if (strlen($caller_id_number) > 0 && strlen($destination_number) > 0) {
			$sql_where .= "and (";
			$sql_where .= "caller_id_number = '$caller_id_number' ";
			$sql_where .= "or destination_number = '$destination_number'";
			$sql_where .= ") ";
	}
	else {
		if (strlen($caller_id_number) > 0) { $sql_where .= "and caller_id_number like '%$caller_id_number%' "; }
		if (strlen($destination_number) > 0) { $sql_where .= "and destination_number like '%$destination_number%' "; }
	}
	if (strlen($context) > 0) { $sql_where .= "and context like '%$context%' "; }
	if (strlen($start_stamp) > 0) { $sql_where .= "and start_stamp like '%$start_stamp%' "; }
	if (strlen($answer_stamp) > 0) { $sql_where .= "and answer_stamp like '%$answer_stamp%' "; }
	if (strlen($end_stamp) > 0) { $sql_where .= "and end_stamp like '%$end_stamp%' "; }
	if (strlen($duration) > 0) { $sql_where .= "and duration like '%$duration%' "; }
	if (strlen($billsec) > 0) { $sql_where .= "and billsec like '%$billsec%' "; }
	if (strlen($hangup_cause) > 0) { $sql_where .= "and hangup_cause like '%$hangup_cause%' "; }
	if (strlen($uuid) > 0) { $sql_where .= "and uuid like '%$uuid%' "; }
	if (strlen($bleg_uuid) > 0) { $sql_where .= "and bleg_uuid like '%$bleg_uuid%' "; }
	if (strlen($accountcode) > 0) { $sql_where .= "and accountcode like '%$accountcode%' "; }
	if (strlen($read_codec) > 0) { $sql_where .= "and read_codec like '%$read_codec%' "; }
	if (strlen($write_codec) > 0) { $sql_where .= "and write_codec like '%$write_codec%' "; }
	if (strlen($remote_media_ip) > 0) { $sql_where .= "and remote_media_ip like '%$remote_media_ip%' "; }
	if (strlen($network_addr) > 0) { $sql_where .= "and network_addr like '%$network_addr%' "; }

	//example sql
		// select caller_id_number, destination_number from v_xml_cdr where domain_uuid = '' 
		// and (caller_id_number = '1001' or destination_number = '1001' or destination_number = '*991001')
	if (!if_group("admin") && !if_group("superadmin")) {
		$sql_where = "where domain_uuid = '$domain_uuid' ";
		$sql_where .= "and ( ";
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
		else {
			$sql_where .= "destination_number = 'no extension assigned' \n"; //destination
		}
		$sql_where .= ") ";
	}
	else {
		//superadmin or admin
		$sql_where = "where domain_uuid = '$domain_uuid' ".$sql_where;
	}
	//$sql_where = str_replace ("where or", "where", $sql_where);
	//$sql_where = str_replace ("where and", " and", $sql_where);

//set the param variable which is used with paging
	$param = "";
	$param .= "&caller_id_name=$caller_id_name";
	$param .= "&start_stamp=$start_stamp";
	$param .= "&hangup_cause=$hangup_cause";
	$param .= "&caller_id_number=$caller_id_number";
	$param .= "&destination_number=$destination_number";
	$param .= "&context=$context";
	$param .= "&answer_stamp=$answer_stamp";
	$param .= "&end_stamp=$end_stamp";
	$param .= "&duration=$duration";
	$param .= "&billsec=$billsec";
	$param .= "&uuid=$uuid";
	$param .= "&bridge_uuid=$bridge_uuid";
	$param .= "&accountcode=$accountcode";
	$param .= "&read_codec=$read_codec";
	$param .= "&write_codec=$write_codec";
	$param .= "&remote_media_ip=$remote_media_ip";
	$param .= "&network_addr=$network_addr";

//create the sql query to get the xml cdr records
	if (strlen($order_by) == 0)  { $order_by  = "start_epoch"; }
	if (strlen($order) == 0)  { $order  = "desc"; }

//set the default
	$num_rows = '0';

//get the number of rows in the v_xml_cdr 
	$sql = "";
	$sql .= " select count(*) as num_rows from v_xml_cdr ";
	$sql .= $sql_where;
	$prep_statement = $db->prepare(check_sql($sql));
	if ($prep_statement) {
		$prep_statement->execute();
		$row = $prep_statement->fetch(PDO::FETCH_ASSOC);
		if ($row['num_rows'] > 0) {
			$num_rows = $row['num_rows'];
		}
		else {
			$num_rows = '0';
		}
	}
	unset($prep_statement, $result);

//prepare to page the results
	$rows_per_page = 150;
	$page = $_GET['page'];
	if (strlen($page) == 0) { $page = 0; $_GET['page'] = 0; } 
	list($paging_controls, $rows_per_page, $var_3) = paging($num_rows, $param, $rows_per_page); 
	$offset = $rows_per_page * $page; 

//get the results from the db
	$sql = "";
	$sql .= " select * from v_xml_cdr ";
	$sql .= $sql_where;
	if (strlen($order_by)> 0) { $sql .= "order by $order_by $order "; }
	$sql .= " limit $rows_per_page offset $offset ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$result = $prep_statement->fetchAll(PDO::FETCH_ASSOC);
	$result_count = count($result);
	unset ($prep_statement, $sql);

	$c = 0;
	$row_style["0"] = "row_style0";
	$row_style["1"] = "row_style1";

//show the results
	echo "<table width='100%' cellpadding='0' cellspacing='0'>\n";
	echo "<tr>\n";
	//echo th_order_by('direction', 'Direction', $order_by, $order);
	//echo th_order_by('default_language', 'Language', $order_by, $order);
	//echo th_order_by('context', 'Context', $order_by, $order);
	//echo th_order_by('leg', 'Leg', $order_by, $order);
	echo th_order_by('caller_id_name', 'Name', $order_by, $order);
	echo th_order_by('caller_id_number', 'Number', $order_by, $order);
	echo th_order_by('destination_number', 'Destination', $order_by, $order);
	echo th_order_by('start_stamp', 'Start', $order_by, $order);
	//echo th_order_by('end_stamp', 'End', $order_by, $order);
	echo th_order_by('duration', 'Length', $order_by, $order);
	if (if_group("admin") || if_group("superadmin")) { 
		echo th_order_by('pdd_ms', 'PDD', $order_by, $order); 
	}
	echo th_order_by('hangup_cause', 'Status', $order_by, $order);
	echo "</tr>\n";

	if ($result_count > 0) {
		foreach($result as $row) {
			$tmp_year = date("Y", strtotime($row['start_stamp']));
			$tmp_month = date("M", strtotime($row['start_stamp']));
			$tmp_day = date("d", strtotime($row['start_stamp']));

			if (defined('TIME_24HR') && TIME_24HR == 1) {
				$tmp_start_epoch = date("j M Y H:i:s", $row['start_epoch']);
			} else {
				$tmp_start_epoch = date("j M Y h:i:sa", $row['start_epoch']);
			}

			$hangup_cause = $row['hangup_cause'];
			$hangup_cause = str_replace("_", " ", $hangup_cause);
			$hangup_cause = strtolower($hangup_cause);
			$hangup_cause = ucwords($hangup_cause);

			echo "<tr >\n";
			//echo "	<td valign='top' class='".$row_style[$c]."'>".$row['direction']."</td>\n";
			//echo "	<td valign='top' class='".$row_style[$c]."'>".$row['default_language']."</td>\n";
			//echo "	<td valign='top' class='".$row_style[$c]."'>".$row['context']."</td>\n";
			//echo "	<td valign='top' class='".$row_style[$c]."'>".$row['leg']."</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>";

			$tmp_dir = $_SESSION['switch']['recordings']['dir'].'/archive/'.$tmp_year.'/'.$tmp_month.'/'.$tmp_day;
			$tmp_name = '';
			if(!empty($row['recording_file']) && file_exists($row['recording_file'])){
				$tmp_name=$row['recording_file'];
			}
			elseif (file_exists($tmp_dir.'/'.$row['uuid'].'.wav')) {
				$tmp_name = $row['uuid'].".wav";
			}
			elseif (file_exists($tmp_dir.'/'.$row['uuid'].'_1.wav')) {
				$tmp_name = $row['uuid']."_1.wav";
			}
			elseif (file_exists($tmp_dir.'/'.$row['uuid'].'.mp3')) {
				$tmp_name = $row['uuid'].".mp3";
			}
			elseif (file_exists($tmp_dir.'/'.$row['uuid'].'_1.mp3')) {
				$tmp_name = $row['uuid']."_1.mp3";
			}
			if (strlen($tmp_name) > 0 && file_exists($_SESSION['switch']['recordings']['dir'].'/archive/'.$tmp_year.'/'.$tmp_month.'/'.$tmp_day.'/'.$tmp_name)) {
				echo "	  <a href=\"javascript:void(0);\" onclick=\"window.open('../recordings/v_recordings_play.php?a=download&type=moh&filename=".base64_encode('archive/'.$tmp_year.'/'.$tmp_month.'/'.$tmp_day.'/'.$tmp_name)."', 'play',' width=420,height=150,menubar=no,status=no,toolbar=no')\">\n";
				echo 	$row['caller_id_name'].' ';
				echo "	  </a>";
			}
			else {
				echo 	$row['caller_id_name'].' ';
			}
			echo "	</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>";
			if (strlen($tmp_name) > 0 && file_exists($_SESSION['switch']['recordings']['dir'].'/archive/'.$tmp_year.'/'.$tmp_month.'/'.$tmp_day.'/'.$tmp_name)) {
				echo "		<a href=\"../recordings/v_recordings.php?a=download&type=rec&t=bin&filename=".base64_encode("archive/".$tmp_year."/".$tmp_month."/".$tmp_day."/".$tmp_name)."\">\n";
				if (is_numeric($row['caller_id_number'])) {
					echo 	format_phone($row['caller_id_number']).' ';
				}
				else {
					echo 	$row['caller_id_number'].' ';
				}
				echo "	  </a>";
			}
			else {
				if (is_numeric($row['caller_id_number'])) {
					echo 	format_phone($row['caller_id_number']).' ';
				}
				else {
					echo 	$row['caller_id_number'].' ';
				}
			}
			echo "	</td>\n";
			if (is_numeric($row['destination_number'])) {
				echo "	<td valign='top' class='".$row_style[$c]."'>".format_phone($row['destination_number'])."</td>\n";
			}
			else {
				echo "	<td valign='top' class='".$row_style[$c]."'>".$row['destination_number']."</td>\n";
			}
			echo "	<td valign='top' class='".$row_style[$c]."'>".$tmp_start_epoch."</td>\n";
			//echo "	<td valign='top' class='".$row_style[$c]."'>".$row['end_stamp']."</td>\n";
			
			//If they cancelled, show the ring time, not the bill time.
			$seconds = ($row['hangup_cause']=="ORIGINATOR_CANCEL") ? $row['duration'] : $row['billsec'];

			echo "	<td valign='top' class='".$row_style[$c]."'>".gmdate("G:i:s", $seconds)."</td>\n";
			if (if_group("admin") || if_group("superadmin")) {
				echo "	<td valign='top' class='".$row_style[$c]."'>".number_format($row['pdd_ms']/1000,2)."s</td>\n";
				echo "	<td valign='top' class='".$row_style[$c]."'><a href='v_xml_cdr_details.php?uuid=".$row['uuid']."'>".$hangup_cause."</a></td>\n";
			}
			else {
				echo "	<td valign='top' class='".$row_style[$c]."'>".$hangup_cause."</td>\n";
			}
			echo "</tr>\n";
			if ($c==0) { $c=1; } else { $c=0; }
		} //end foreach
		unset($sql, $result, $row_count);
	} //end if results

	echo "<tr>\n";
	echo "<td colspan='11' align='left'>\n";
	echo "	<table width='100%' cellpadding='0' cellspacing='0'>\n";
	echo "	<tr>\n";
	echo "		<td width='33.3%' nowrap>&nbsp;</td>\n";
	echo "		<td width='33.3%' align='center' nowrap>$paging_controls</td>\n";
	echo "		<td width='33.3%' nowrap='nowrap'>&nbsp;</td>\n";
	echo "		</td>\n";
	echo "	</tr>\n";
 	echo "	</table>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "</table>";
	echo "</div>";
	echo "<br><br>";
	echo "<br><br>";

//show the footer
	require_once "includes/footer.php";
?>
