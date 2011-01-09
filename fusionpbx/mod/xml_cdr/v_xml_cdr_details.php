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

//get the http post,get values and set them to a variable
	if (strlen($_REQUEST["uuid"]) > 0) {
		$uuid = trim($_REQUEST["uuid"]);
	}

//get the xml cdr string from the database
	$sql = "select * from v_xml_cdr ";
	$sql .= "where v_id  = '$v_id' ";
	$sql .= "and uuid  = '$uuid' ";
	$row = $db->query($sql)->fetch();
	$start_stamp = trim($row["start_stamp"]);
	$xml_string = trim($row["xml_cdr"]);
	//print_r($row);

//get cdr from the file system
	if (strlen($xml_string) == 0) {
		$tmp_time = strtotime($start_stamp);
		$tmp_year = date("Y", $tmp_time);
		$tmp_month = date("M", $tmp_time);
		$tmp_day = date("d", $tmp_time);
		$tmp_dir = $v_log_dir.'/xml_cdr/archive/'.$tmp_year.'/'.$tmp_month.'/'.$tmp_day;
		$tmp_file = $uuid.'.xml';
		$xml_string = file_get_contents($tmp_dir.'/'.$tmp_file);
	}

//parse the xml to get the call detail record info
	try {
		$xml = simplexml_load_string($xml_string);
	}
	catch(Exception $e) {
		echo $e->getMessage();
	}

//get the header
	require_once "includes/header.php";

//page title and description
	echo "<br>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
	echo "<tr>\n";
	echo "<td width='30%' align='left' valign='top' nowrap='nowrap'><b>Call Details</b></td>\n";
	echo "<td width='70%' align='right' valign='top'>\n";
	echo "	<input type='button' class='btn' name='' alt='back' onclick=\"window.location='v_xml_cdr.php'\" value='Back'>\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td align='left' colspan='2'>\n";
	echo "A detailed view of the call and all information regarding it. \n";
	echo "The information contains caller id name and number, channel data, \n";
	echo "call variables, call flow, timing information, and other useful call details. \n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "<br />\n";
	echo "<br />\n";

//detail summary
	//get the variables from the xml
		$uuid = check_str(urldecode($xml->variables->uuid));
		$direction = check_str(urldecode($xml->channel_data->direction));
		$language = check_str(urldecode($xml->variables->language));
		$xml_string = check_str($xml_string);
		$start_epoch = check_str(urldecode($xml->variables->start_epoch));
		$start_stamp = check_str(urldecode($xml->variables->start_stamp));
		$start_uepoch = check_str(urldecode($xml->variables->start_uepoch));
		$answer_stamp = check_str(urldecode($xml->variables->answer_stamp));
		$answer_epoch = check_str(urldecode($xml->variables->answer_epoch));
		$answer_uepoch = check_str(urldecode($xml->variables->answer_uepoch));
		$end_epoch = check_str(urldecode($xml->variables->end_epoch));
		$end_uepoch = check_str(urldecode($xml->variables->end_uepoch));
		$end_stamp = check_str(urldecode($xml->variables->end_stamp));
		$duration = check_str(urldecode($xml->variables->duration));
		$mduration = check_str(urldecode($xml->variables->mduration));
		$billsec = check_str(urldecode($xml->variables->billsec));
		$billmsec = check_str(urldecode($xml->variables->billmsec));
		$bridge_uuid = check_str(urldecode($xml->variables->bridge_uuid));
		$read_codec = check_str(urldecode($xml->variables->read_codec));
		$write_codec = check_str(urldecode($xml->variables->write_codec));
		$remote_media_ip = check_str(urldecode($xml->variables->remote_media_ip));
		$hangup_cause = check_str(urldecode($xml->variables->hangup_cause));
		$hangup_cause_q850 = check_str(urldecode($xml->variables->hangup_cause_q850));
		$x = 0;
		foreach ($xml->callflow as $row) {
			if ($x == 0) {
				$destination_number = check_str(urldecode($row->caller_profile->destination_number));
				$context = check_str(urldecode($row->caller_profile->context));
				$network_addr = check_str(urldecode($row->caller_profile->network_addr));
			}
			$caller_id_name = check_str(urldecode($row->caller_profile->caller_id_name));
			$caller_id_number = check_str(urldecode($row->caller_profile->caller_id_number));
			$x++;
		}
		unset($x);

	$tmp_year = date("Y", strtotime($start_stamp));
	$tmp_month = date("M", strtotime($start_stamp));
	$tmp_day = date("d", strtotime($start_stamp));

	$c = 0;
	$rowstyle["0"] = "rowstyle0";
	$rowstyle["1"] = "rowstyle1";

	echo "<div align='center'>\n";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
	echo "<tr>\n";
	echo "<td align='left'><b>Summary</b>&nbsp;</td>\n";
	echo "<td></td>\n";
	echo "</tr>\n";
	echo "</table>\n";

	echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
	echo "<tr>\n";
	echo "<th>Direction</th>\n";
	//echo "<th>Language</th>\n";
	//echo "<th>Context</th>\n";
	echo "<th>Name (play)</th>\n";
	echo "<th>Number (download)</th>\n";
	echo "<th>Destination</th>\n";
	echo "<th>Start</th>\n";
	echo "<th>End</th>\n";
	echo "<th>Length</th>\n";
	echo "<th>Status</th>\n";
	echo "</tr>\n";

	echo "<tr >\n";
	echo "	<td valign='top' class='".$rowstyle[$c]."'><a href='v_xml_cdr_details.php?uuid=".$uuid."'>".$direction."</a></td>\n";
	//echo "	<td valign='top' class='".$rowstyle[$c]."'>".$language."</td>\n";
	//echo "	<td valign='top' class='".$rowstyle[$c]."'>".$context."</td>\n";
	echo "	<td valign='top' class='".$rowstyle[$c]."'>";
	if (file_exists($v_recordings_dir.'/archive/'.$tmp_year.'/'.$tmp_month.'/'.$tmp_day.'/'.$uuid.'.wav')) {
		//echo "		<a href=\"../recordings/v_recordings.php?a=download&type=rec&t=bin&filename=".base64_encode('archive/'.$tmp_year.'/'.$tmp_month.'/'.$tmp_day.'/'.$uuid.'.wav')."\">\n";
		//echo "	  </a>";

		echo "	  <a href=\"javascript:void(0);\" onclick=\"window.open('../recordings/v_recordings_play.php?a=download&type=moh&filename=".base64_encode('archive/'.$tmp_year.'/'.$tmp_month.'/'.$tmp_day.'/'.$uuid.'.wav')."', 'play',' width=420,height=40,menubar=no,status=no,toolbar=no')\">\n";
		//$tmp_file_array = explode("\.",$file);
		echo 	$caller_id_name.' ';
		echo "	  </a>";

	}
	else {
		echo 	$caller_id_name.' ';
	}
	echo "	</td>\n";
	echo "	<td valign='top' class='".$rowstyle[$c]."'>";
	if (file_exists($v_recordings_dir.'/archive/'.$tmp_year.'/'.$tmp_month.'/'.$tmp_day.'/'.$uuid.'.wav')) {
		echo "		<a href=\"../recordings/v_recordings.php?a=download&type=rec&t=bin&filename=".base64_encode('archive/'.$tmp_year.'/'.$tmp_month.'/'.$tmp_day.'/'.$uuid.'.wav')."\">\n";
		echo 	$caller_id_number.' ';
		echo "	  </a>";
	}
	else {
		echo 	$caller_id_number.' ';
	}
	echo "	</td>\n";
	echo "	<td valign='top' class='".$rowstyle[$c]."'>".$destination_number."</td>\n";
	echo "	<td valign='top' class='".$rowstyle[$c]."'>".$start_stamp."</td>\n";
	echo "	<td valign='top' class='".$rowstyle[$c]."'>".$end_stamp."</td>\n";
	echo "	<td valign='top' class='".$rowstyle[$c]."'>".$duration."</td>\n";
	echo "	<td valign='top' class='".$rowstyle[$c]."'>".$hangup_cause."</td>\n";
	echo "</table>";
	echo "</div>";

//breaking space
	echo "<br /><br />\n";

//channel data loop
	$c = 0;
	$rowstyle["0"] = "rowstyle0";
	$rowstyle["1"] = "rowstyle1";
	echo "<div align='center'>\n";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
	echo "<tr>\n";
	echo "<td align='left'><b>Channel Data</b>&nbsp;</td>\n";
	echo "<td></td>\n";
	echo "</tr>\n";
	echo "</table>\n";

	echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
	echo "<tr>\n";
	echo "<th>Name</th>\n";
	echo "<th>Value</th>\n";
	echo "</tr>\n";
	foreach($xml->channel_data->children() as $child) {
		$key = $child->getName();
		$value = urldecode($child);
		echo "<tr >\n";
		echo "	<td valign='top' align='left' class='".$rowstyle[$c]."'>".$key."&nbsp;</td>\n";
		echo "	<td valign='top' align='left' class='".$rowstyle[$c]."'>".wordwrap($value,75,"<br />\n", TRUE)."&nbsp;</td>\n";
		echo "</tr>\n";
		if ($c==0) { $c=1; } else { $c=0; }
	}
	echo "</table>";
	echo "</div>";

//breaking space
	echo "<br /><br />\n";

//variable loop
	$c = 0;
	$rowstyle["0"] = "rowstyle0";
	$rowstyle["1"] = "rowstyle1";
	echo "<div align='center'>\n";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
	echo "<tr>\n";
	echo "	<td align='left'><b>Variables</b>&nbsp;</td>\n";
	echo "<td></td>\n";
	echo "</tr>\n";
	echo "</table>\n";

	echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
	echo "<tr>\n";
	echo "<th>Name</th>\n";
	echo "<th>Value</th>\n";
	echo "</tr>\n";
	foreach($xml->variables->children() as $child) {
		$key = $child->getName();
		$value = urldecode($child);
		echo "<tr >\n";
		echo "	<td valign='top' align='left' class='".$rowstyle[$c]."'>".$key."</td>\n";
		if ($key == "bridge_uuid" || $key == "signal_bond") {
			echo "	<td valign='top' align='left' class='".$rowstyle[$c]."'><a href='v_xml_cdr_details.php?uuid=$value'>".$value."</a>&nbsp;</td>\n";
		}
		else {
			echo "	<td valign='top' align='left' class='".$rowstyle[$c]."'>".wordwrap($value,75,"<br />\n", TRUE)."&nbsp;</td>\n";
		}
		echo "</tr>\n";
		if ($c==0) { $c=1; } else { $c=0; }
	}
	echo "</table>";
	echo "</div>";

//breaking space
	echo "<br /><br />\n";

//app_log
	$c = 0;
	$rowstyle["0"] = "rowstyle0";
	$rowstyle["1"] = "rowstyle1";
	echo "<div align='center'>\n";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
	echo "<tr>\n";
	echo "<td align='left'><b>Application Log</b>&nbsp;</td>\n";
	echo "<td></td>\n";
	echo "</tr>\n";
	echo "</table>\n";

	echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
	echo "<tr>\n";
	echo "<th>Name</th>\n";
	echo "<th>Data</th>\n";
	echo "</tr>\n";

	foreach ($xml->app_log->application as $row) {
		$app_name = $row->attributes()->app_name;
		$app_data = $row->attributes()->app_data;
		echo "<tr >\n";
		echo "	<td valign='top' align='left' class='".$rowstyle[$c]."'>".$app_name."&nbsp;</td>\n";
		echo "	<td valign='top' align='left' class='".$rowstyle[$c]."'>".wordwrap($app_data,75,"<br />\n", TRUE)."&nbsp;</td>\n";
		echo "</tr>\n";
		if ($c==0) { $c=1; } else { $c=0; }
	}
	echo "</table>";
	echo "</div>";

//breaking space
	echo "<br /><br />\n";

//callflow
	$c = 0;
	$rowstyle["0"] = "rowstyle0";
	$rowstyle["1"] = "rowstyle1";

	foreach ($xml->callflow as $row) {

		echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
		echo "<tr>\n";
		echo "	<td align='left'>\n";

		//attributes
			echo "	<table width='95%' border='0' cellpadding='0' cellspacing='0'>\n";
			echo "		<tr>\n";
			echo "			<td><b>Call Flow: Attributes</b>&nbsp;</td>\n";
			echo "			<td></td>\n";
			echo "		</tr>\n";
			echo "	</table>\n";

			echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
			echo "		<tr>\n";
			echo "			<th>Name</th>\n";
			echo "			<th>Value</th>\n";
			echo "		</tr>\n";
			foreach($row->attributes() as $key => $value) {
				echo "		<tr>\n";
				echo "				<td valign='top' align='left' class='".$rowstyle[$c]."'>".$key."&nbsp;</td>\n";
				echo "				<td valign='top' align='left' class='".$rowstyle[$c]."'>".wordwrap($value,75,"<br />\n", TRUE)."&nbsp;</td>\n";
				echo "		</tr>\n";
				if ($c==0) { $c=1; } else { $c=0; }
			}
			echo "		<tr>\n";
			echo "			<td colspan='2'><br /><br /></td>\n";
			echo "		</tr>\n";
			echo "</table>\n";

		//extension->attributes
			echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
			echo "		<tr>\n";
			echo "			<td><b>Call Flow: Extension: Attributes</b>&nbsp;</td>\n";
			echo "			<td></td>\n";
			echo "		</tr>\n";
			echo "</table>\n";

			echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
			echo "		<tr>\n";
			echo "			<th>Name</th>\n";
			echo "			<th>Value</th>\n";
			echo "		</tr>\n";
			foreach($row->extension->attributes() as $key => $value) {
				echo "		<tr >\n";
				echo "			<td valign='top' align='left' class='".$rowstyle[$c]."'>".$key."&nbsp;</td>\n";
				echo "			<td valign='top' align='left' class='".$rowstyle[$c]."'>".wordwrap($value,75,"<br />\n", TRUE)."&nbsp;</td>\n";
				echo "		</tr>\n";
				if ($c==0) { $c=1; } else { $c=0; }
			}
			echo "		<tr>\n";
			echo "			<td colspan='2'><br /><br /></td>\n";
			echo "		</tr>\n";
			echo "</table>\n";

		//extension->application
			echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
			echo "		<tr>\n";
			echo "			<td><b>Call Flow: Extension: Application</b>&nbsp;</td>\n";
			echo "			<td></td>\n";
			echo "		</tr>\n";
			echo "</table>\n";

			echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
			echo "		<tr>\n";
			echo "			<th>Name</th>\n";
			echo "			<th>Data</th>\n";
			echo "		</tr>\n";
			foreach ($row->extension->application as $tmp_row) {
				$app_name = $tmp_row->attributes()->app_name;
				$app_data = $tmp_row->attributes()->app_data;
				echo "		<tr >\n";
				echo "			<td valign='top' align='left' class='".$rowstyle[$c]."'>".$app_name."&nbsp;</td>\n";
				echo "			<td valign='top' align='left' class='".$rowstyle[$c]."'>".wordwrap($app_data,75,"<br />\n", TRUE)."&nbsp;</td>\n";
				echo "		</tr>\n";
				if ($c==0) { $c=1; } else { $c=0; }
			}
			echo "		<tr>\n";
			echo "			<td colspan='2'><br /><br /></td>\n";
			echo "		</tr>\n";
			echo "</table>\n";

		//caller_profile
			echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
			echo "		<tr>\n";
			echo "			<td><b>Call Flow: Caller Profile</b>&nbsp;</td>\n";
			echo "			<td></td>\n";
			echo "		</tr>\n";
			echo "</table>\n";

			echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
			echo "		<tr>\n";
			echo "			<th>Name</th>\n";
			echo "			<th>Value</th>\n";
			echo "		</tr>\n";
			foreach($row->caller_profile->children() as $child) {
				$key = $child->getName();
				echo "		<tr >\n";
				if ($key != "originatee") {
					$value = urldecode($child);
					echo "			<td valign='top' align='left' class='".$rowstyle[$c]."'>".$key."&nbsp;</td>\n";
					echo "			<td valign='top' align='left' class='".$rowstyle[$c]."'>".wordwrap($value,75,"<br />\n", TRUE)."&nbsp;</td>\n";
				}
				else {
					echo "			<td valign='top' align='left' class='".$rowstyle[$c]."'>".$key."</td>\n";
					echo "			<td>\n";
					echo "				<table width='100%'>\n";
					foreach($child->originatee_caller_profile->children() as $tmp_child) {
						//print_r($tmp_child);
						$key = $tmp_child->getName();
						$value = urldecode($tmp_child);
						echo "				<tr >\n";
						echo "					<td valign='top' align='left' width='20%' class='".$rowstyle[$c]."'>".$key."&nbsp;</td>\n";
						if ($key != "uuid") {
							echo "					<td valign='top' align='left' class='".$rowstyle[$c]."'>".wordwrap($value,75,"<br />\n", TRUE)."&nbsp;</td>\n";
						}
						else {
							echo "					<td valign='top' align='left' class='".$rowstyle[$c]."'><a href='v_xml_cdr_details.php?uuid=$value'>".$value."</a>&nbsp;</td>\n";
						}
						echo "				</tr>\n";
					}
					echo "				</table>\n";
					echo "			</td>\n";
				}
				echo "</tr>\n";
				if ($c==0) { $c=1; } else { $c=0; }
			}
			echo "		<tr>\n";
			echo "			<td colspan='2'><br /><br /></td>\n";
			echo "		</tr>\n";
			echo "</table>\n";

		//times
			echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
			echo "		<tr>\n";
			echo "			<td><b>Call Flow: Times</b>&nbsp;</td>\n";
			echo "			<td></td>\n";
			echo "		</tr>\n";

			echo "		<tr>\n";
			echo "			<th>Name</th>\n";
			echo "			<th>Value</th>\n";
			echo "		</tr>\n";
			foreach($row->times->children() as $child) {
				$key = $child->getName();
				$value = urldecode($child);
				echo "		<tr >\n";
				echo "			<td valign='top' align='left' class='".$rowstyle[$c]."'>".$key."&nbsp;</td>\n";
				echo "			<td valign='top' align='left' class='".$rowstyle[$c]."'>".wordwrap($value,75,"<br />\n", TRUE)."&nbsp;</td>\n";
				echo "		</tr>\n";
				if ($c==0) { $c=1; } else { $c=0; }
			}

			echo "		<tr>\n";
			echo "			<td colspan='2'><br /><br /></td>\n";
			echo "		</tr>\n";

			echo "	</table>";
			echo "	<br /><br />\n";
	}

	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>";


//testing
	//echo "<pre>\n";
	//echo htmlentities($xml_string);
	//print_r($xml);
	//echo "</pre>\n";


//get the footer
	require_once "includes/footer.php";
?>