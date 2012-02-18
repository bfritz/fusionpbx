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
include "root.php";
require "includes/require.php";
require_once "includes/checkauth.php";
if (permission_exists('cdr_csv_view')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}
require_once "includes/header.php";
require_once "includes/paging.php";

require_once "v_cdr_import.php";
require "includes/lib_cdr.php";
	
$order_by = $_GET["order_by"];
$order = $_GET["order"];

if (count($_REQUEST)>0) {
	$cdr_id = $_REQUEST["cdr_id"];
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

//get a list of assigned extensions for this user
	$sql = "";
	$sql .= " select * from v_extensions ";
	$sql .= "where domain_uuid = '$domain_uuid' ";
	$sql .= "and user_list like '%|".$_SESSION["username"]."|%' ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	//$v_mailboxes = '';
	$x = 0;
	$result = $prep_statement->fetchAll();
	foreach ($result as &$row) {
		//$v_mailboxes = $v_mailboxes.$row["mailbox"].'|';
		//$extension_uuid = $row["extension_uuid"];
		//$mailbox = $row["mailbox"];
		$extension_array[$x]['extension_uuid'] = $row["extension_uuid"];
		$extension_array[$x]['extension'] = $row["extension"];
		$x++;
	}
	unset ($prep_statement, $x);


//call detail record list
	echo "<div align='center'>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='2'>\n";

	echo "<tr class='border'>\n";
	echo "	<td align=\"center\">\n";
	echo "      <br>";


	echo "<table width='100%' border='0'><tr>\n";
	echo "<td align='left' width='50%' nowrap><b>Call Detail Records</b></td>\n";
	echo "<td align='left' width='50%' align='right'>&nbsp;</td>\n";
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
	echo "</tr></table>\n";

	if (strlen($cdr_id) > 0) { $sqlwhere .= "and cdr_id like '%$cdr_id%' "; }
	if (strlen($caller_id_name) > 0) { $sqlwhere .= "and caller_id_name like '%$caller_id_name%' "; }
	if (strlen($caller_id_number) > 0) { $sqlwhere .= "and caller_id_number like '%$caller_id_number%' "; }
	if (strlen($destination_number) > 0) { $sqlwhere .= "and destination_number like '%$destination_number%' "; }
	if (strlen($context) > 0) { $sqlwhere .= "and context like '%$context%' "; }
	if (strlen($start_stamp) > 0) { $sqlwhere .= "and start_stamp like '%$start_stamp%' "; }
	if (strlen($answer_stamp) > 0) { $sqlwhere .= "and answer_stamp like '%$answer_stamp%' "; }
	if (strlen($end_stamp) > 0) { $sqlwhere .= "and end_stamp like '%$end_stamp%' "; }
	if (strlen($duration) > 0) { $sqlwhere .= "and duration like '%$duration%' "; }
	if (strlen($billsec) > 0) { $sqlwhere .= "and billsec like '%$billsec%' "; }
	if (strlen($hangup_cause) > 0) { $sqlwhere .= "and hangup_cause like '%$hangup_cause%' "; }
	if (strlen($uuid) > 0) { $sqlwhere .= "and uuid like '%$uuid%' "; }
	if (strlen($bleg_uuid) > 0) { $sqlwhere .= "and bleg_uuid like '%$bleg_uuid%' "; }
	if (strlen($accountcode) > 0) { $sqlwhere .= "and accountcode like '%$accountcode%' "; }
	if (strlen($read_codec) > 0) { $sqlwhere .= "and read_codec like '%$read_codec%' "; }
	if (strlen($write_codec) > 0) { $sqlwhere .= "and write_codec like '%$write_codec%' "; }
	if (strlen($remote_media_ip) > 0) { $sqlwhere .= "and remote_media_ip like '%$remote_media_ip%' "; }
	if (strlen($network_addr) > 0) { $sqlwhere .= "and network_addr like '%$network_addr%' "; }
	if (!if_group("admin") && !if_group("superadmin")) {
		//disable member search
		//$sqlwhereorig = $sqlwhere;
		$sqlwhere = "where ";
		if (count($extension_array) > 0) {
			foreach($extension_array as $value) {
				if ($value['extension'] > 0) { $sqlwhere .= "or domain_uuid = '$domain_uuid' and caller_id_number = '".$value['extension']."' ". $sqlwhereorig." \n"; } //source
				if ($value['extension'] > 0) { $sqlwhere .= "or domain_uuid = '$domain_uuid' and destination_number = '".$value['extension']."' ".$sqlwhereorig." \n"; } //destination
				if ($value['extension'] > 0) { $sqlwhere .= "or domain_uuid = '$domain_uuid' and destination_number = '*99".$value['extension']."' ".$sqlwhereorig." \n"; } //destination
			}
		} //count($extension_array)
	}
	else {
		//superadmin or admin
		$sqlwhere = "where domain_uuid = '$domain_uuid' ".$sqlwhere;
	}
	$sqlwhere = str_replace ("where or", "where", $sqlwhere);
	$sqlwhere = str_replace ("where and", " and", $sqlwhere);

	$sql = "";
	$sql .= " select * from v_cdr ";
	$sql .= $sqlwhere;
	if (strlen($order_by) == 0) {
		$sql .= "order by cdr_id desc "; 
	}
	else {
		$sql .= "order by $order_by $order "; 
	}
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$result = $prep_statement->fetchAll();
	$num_rows = count($result);
	unset ($prep_statement, $result, $sql);

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
	$param .= "&bleg_uuid=$bleg_uuid";
	$param .= "&accountcode=$accountcode";
	$param .= "&read_codec=$read_codec";
	$param .= "&write_codec=$write_codec";
	$param .= "&remote_media_ip=$remote_media_ip";
	$param .= "&network_addr=$network_addr";

	$rows_per_page = 200;

	$page = $_GET['page'];
	if (strlen($page) == 0) { $page = 0; $_GET['page'] = 0; } 
	list($paging_controls, $rows_per_page, $var_3) = paging($num_rows, $param, $rows_per_page); 
	$offset = $rows_per_page * $page; 

	$sql = "";
	$sql .= " select * from v_cdr ";
	$sql .= $sqlwhere;
	if (strlen($order_by) == 0) {
		$sql .= "order by cdr_id desc "; 
	}
	else {
		$sql .= "order by $order_by $order "; 
	}
	$sql .= " limit $rows_per_page offset $offset ";
	//echo $sql;
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$result = $prep_statement->fetchAll();
	$result_count = count($result);
	unset ($prep_statement, $sql);


	$c = 0;
	$row_style["0"] = "row_style0";
	$row_style["1"] = "row_style1";

	//search the call detail records
	if (if_group("admin") || if_group("superadmin")) {
		echo "<div align='center'>\n";

		echo "<form method='post' action=''>";

		echo "<table width='95%' cellpadding='3' border='0'>";
		echo "<tr>";
		echo "<td width='33.3%'>\n";
			echo "<table width='100%'>";
			//echo "	<tr>";
			//echo "		<td>Source Name:</td>";
			//echo "		<td><input type='text' class='txt' name='caller_id_name' value='$caller_id_name'></td>";
			//echo "	</tr>";
			echo "	<tr>";
			echo "		<td align='left' width='25%'>Start:</td>";
			echo "		<td align='left' width='75%'><input type='text' class='txt' name='start_stamp' value='$start_stamp'></td>";
			echo "	</tr>";
			echo "	<tr>";
			echo "		<td align='left' width='25%'>Status:</td>";
			echo "		<td align='left' width='75%'><input type='text' class='txt' name='hangup_cause' value='$hangup_cause'></td>";
			echo "	</tr>";
			echo "</table>\n";

		echo "</td>\n";
		echo "<td width='33.3%'>\n";

			echo "<table width='100%'>";
			echo "	<tr>";
			echo "		<td align='left' width='25%'>Source:</td>";
			echo "		<td align='left' width='75%'><input type='text' class='txt' name='caller_id_number' value='$caller_id_number'></td>";
			echo "	</tr>";
			echo "	<tr>";
			echo "		<td align='left' width='25%'>Destination:</td>";
			echo "		<td align='left' width='75%'><input type='text' class='txt' name='destination_number' value='$destination_number'></td>";
			echo "	</tr>";	
			echo "</table>\n";

		echo "</td>\n";
		echo "<td width='33.3%'>\n";

			echo "<table width='100%'>\n";
			//echo "	<tr>";
			//echo "		<td>Context:</td>";
			//echo "		<td><input type='text' class='txt' name='context' value='$context'></td>";
			//echo "	</tr>";

			//echo "	<tr>";
			//echo "		<td>Answer:</td>";
			//echo "		<td><input type='text' class='txt' name='answer_stamp' value='$answer_stamp'></td>";
			//echo "	</tr>";
			//echo "	<tr>";
			//echo "		<td>End:</td>";
			//echo "		<td><input type='text' class='txt' name='end_stamp' value='$end_stamp'></td>";
			//echo "	</tr>";
			echo "	<tr>";
			echo "		<td align='left' width='25%'>Duration:</td>";
			echo "		<td align='left' width='75%'><input type='text' class='txt' name='duration' value='$duration'></td>";
			echo "	</tr>";
			echo "	<tr>";
			echo "		<td align='left' width='25%'>Bill:</td>";
			echo "		<td align='left' width='75%'><input type='text' class='txt' name='billsec' value='$billsec'></td>";
			echo "	</tr>";

			//echo "	<tr>";
			//echo "		<td>UUID:</td>";
			//echo "		<td><input type='text' class='txt' name='uuid' value='$uuid'></td>";
			//echo "	</tr>";
			//echo "	<tr>";
			//echo "		<td>Bleg UUID:</td>";
			//echo "		<td><input type='text' class='txt' name='bleg_uuid' value='$bleg_uuid'></td>";
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
		echo "	<input type='button' class='btn' name='' alt='view' onclick=\"window.location='v_cdr_search.php'\" value='advanced'>&nbsp;\n";
		echo "	<input type='submit' class='btn' name='submit' value='filter'>\n";
		echo "</td>\n";
		echo "</tr>";
		echo "</table>";
		echo "</form>";
	}


	echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";

	echo "<tr>\n";
	echo "<th>Start</th>\n";
	//echo th_order_by('start_stamp', 'Start', $order_by, $order);
	echo th_order_by('caller_id_name', 'CID Name', $order_by, $order);
	echo "<th>Source</th>\n";
	//echo th_order_by('caller_id_number', 'Source', $order_by, $order);
	echo "<th>Destination</th>\n";
	//echo th_order_by('destination_number', 'Destination', $order_by, $order);
	//echo th_order_by('context', 'Context', $order_by, $order);
	//echo th_order_by('answer_stamp', 'Answer', $order_by, $order);
	//echo th_order_by('end_stamp', 'End', $order_by, $order);
	echo "<th>Duration</th>\n";
	//echo th_order_by('duration', 'Duration', $order_by, $order);
	echo "<th>Bill</th>\n";
	//echo th_order_by('billsec', 'Bill', $order_by, $order);
	echo "<th>Status</th>\n";
	//echo th_order_by('hangup_cause', 'Status', $order_by, $order);


	echo "<form method='post' action='v_cdr_csv.php'>";
	echo "<td align='left' width='22'>\n";
	echo "<input type='hidden' name='caller_id_name' value='$caller_id_name'>\n";
	echo "<input type='hidden' name='start_stamp' value='$start_stamp'>\n";
	echo "<input type='hidden' name='hangup_cause' value='$hangup_cause'>\n";
	echo "<input type='hidden' name='caller_id_number' value='$caller_id_number'>\n";
	echo "<input type='hidden' name='destination_number' value='$destination_number'>\n";
	echo "<input type='hidden' name='context' value='$context'>\n";
	echo "<input type='hidden' name='answer_stamp' value='$answer_stamp'>\n";
	echo "<input type='hidden' name='end_stamp' value='$end_stamp'>\n";
	echo "<input type='hidden' name='duration' value='$duration'>\n";
	echo "<input type='hidden' name='billsec' value='$billsec'>\n";
	echo "<input type='hidden' name='uuid' value='$uuid'>\n";
	echo "<input type='hidden' name='bleg_uuid' value='$bleg_uuid'>\n";
	echo "<input type='hidden' name='accountcode' value='$accountcode'>\n";
	echo "<input type='hidden' name='read_codec' value='$read_codec'>\n";
	echo "<input type='hidden' name='write_codec' value='$write_codec'>\n";
	echo "<input type='hidden' name='remote_media_ip' value='$remote_media_ip'>\n";
	echo "<input type='hidden' name='network_addr' value='$network_addr'>\n";
	echo "<input type='submit' class='btn' name='submit' value=' csv '>\n";
	//echo "    <input type='button' class='btn' name='' alt='view' onclick=\"window.location='v_cdr_csv.php?id=".$row[cdr_id]."'\" value='csv'>\n";
	//echo "  <input type='button' class='btn' name='' alt='add' onclick=\"window.location='v_cdr_edit.php'\" value='+'>\n";
	echo "</td>\n";
	echo "</form>\n";
	echo "<tr>\n";

	if ($result_count == 0) { //no results
	}
	else { //received results
		foreach($result as $row) {
			//print_r( $row );
			echo "<tr >\n";
			echo "   <td valign='top' class='".$row_style[$c]."' nowrap>&nbsp;".$row[start_stamp]."&nbsp;</td>\n";
			echo "   <td valign='top' class='".$row_style[$c]."'>&nbsp;".$row[caller_id_name]."&nbsp;</td>\n";
			echo "   <td valign='top' class='".$row_style[$c]."'>&nbsp;".$row[caller_id_number]."&nbsp;</td>\n";
			echo "   <td valign='top' class='".$row_style[$c]."'>&nbsp;".$row[destination_number]."&nbsp;</td>\n";
			//echo "   <td valign='top' class='".$row_style[$c]."'>&nbsp;".$row[context]."&nbsp;</td>\n";
			//echo "   <td valign='top' class='".$row_style[$c]."' nowrap>&nbsp;".$row[answer_stamp]."&nbsp;</td>\n";
			//echo "   <td valign='top' class='".$row_style[$c]."' nowrap>&nbsp;".$row[end_stamp]."&nbsp;</td>\n";
			$duration = $row[duration];
			//if ($duration < 60) { $duration = $duration. " sec"; }
			//if ($duration > 60) { $duration = round(($duration/60), 2). " min"; }
			echo "   <td valign='top' class='".$row_style[$c]."'>&nbsp;".$duration."&nbsp;</td>\n";
			echo "   <td valign='top' class='".$row_style[$c]."' nowrap>&nbsp;".$row[billsec]."&nbsp;</td>\n";
			echo "   <td valign='top' class='".$row_style[$c]."' nowrap>&nbsp;".strtolower($row[hangup_cause])."&nbsp;</td>\n";
			echo "   <td valign='top' align='right'>\n";
			//echo "	<a href='v_cdr_edit.php?id=".$row[cdr_id]."' alt='add'>$v_link_label_view</a>\n";
			echo "       <input type='button' class='btn' name='' alt='view' onclick=\"window.location='v_cdr_edit.php?id=".$row[cdr_id]."'\" value='  >  '>\n";
			//echo "       <input type='button' class='btn' name='' alt='delete' onclick=\"if (confirm('Are you sure you want to delete this?')) { window.location='v_cdr_delete.php?id=".$row[cdr_id]."' }\" value='x'>\n";
			echo "   </td>\n";
			echo "</tr>\n";
			if ($c==0) { $c=1; } else { $c=0; }
		} //end foreach
		unset($sql, $result, $row_count);
	} //end if results


	echo "<tr>\n";
	echo "<td colspan='7'>\n";
	echo "   <table width='100%' cellpadding='0' cellspacing='0'>\n";
	echo "   <tr>\n";
	echo "       <td width='33.3%' nowrap>&nbsp;</td>\n";
	echo "       <td width='33.3%' align='center' nowrap>$paging_controls</td>\n";
	echo "       <td width='33.3%' align='right'>&nbsp;</td>\n";
	//echo "       <td width='33.3%' align='right'><input type='button' class='btn' name='' alt='add' onclick=\"window.location='v_cdr_edit.php'\" value='+'></td>\n";
	echo "   </tr>\n";
	echo "   </table>\n";
	echo "</td>\n";
	echo "</tr>\n";


	echo "</table>";
	echo "</div>";
	echo "<br><br>";
	echo "<br><br>";


	echo "</td>";
	echo "</tr>";
	echo "</table>";
	echo "</div>";
	echo "<br><br>";

require "includes/require.php";
require_once "includes/footer.php";
?>
