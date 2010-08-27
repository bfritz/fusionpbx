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
require_once "includes/v_dialplan_entry_exists.php";

//define variables
	$c = 0;
	$rowstyle["0"] = "rowstyle0";
	$rowstyle["1"] = "rowstyle1";

if ($_GET['a'] == "download") {
	if ($_GET['t'] == "logs") {
		$tmp = $v_log_dir.'/';
		$filename = $v_name.'.log';
	}
	if ($_GET['t'] == "cdrcsv") {
		$tmp = $v_log_dir.'/cdr-csv/';
		$filename = 'Master.csv';
	}
	if ($_GET['t'] == "backup") {
		$tmp = $v_backup_dir.'/';
		$filename = $v_name.'.bak.tgz';
		if (!is_dir($v_backup_dir.'/')) {
			exec("mkdir ".$v_backup_dir."/");
		}
		chdir($v_parent_dir);
		system('tar cvzf '.$v_backup_dir.'/'.$v_name.'.bak.tgz '.$v_name);
	}
	session_cache_limiter('public');
	$fd = fopen($tmp.$filename, "rb");
	header("Content-Type: binary/octet-stream");
	header("Content-Length: " . filesize($tmp.$filename));
	header('Content-Disposition: attachment; filename="'.$filename.'"');
	fpassthru($fd);
	exit;
}

//disabled until further development
//if ($_GET['a'] == "update") {
//	if ($_GET['t'] == "gui_phase_1") {
//		//chdir('/tmp/');
//		chdir($v_parent_dir.'/pkg/');
//		exec("fetch ".$v_download_path."v_config.inc");
//		//exec("cp ".$tmp_dir."/v_config.tmp ".$v_parent_dir."/pkg/v_config.php");
//		//unlink_if_exists($tmp_dir."/v_config.tmp");
//		
//		header( 'Location: v_status.php?a=update&t=gui_phase_2' ) ;
//		exit;
//	}
//}

////disabled until further development
//if ($_GET['a'] == "update") {
//	if ($_GET['t'] == "gui_phase_2") {
//		v_install_phase_2(); //needs to run on the new page so that it uses the new v_config.inc file
//		header( 'Location: v_status.php?savemsg=Update+Completed.' ) ;
//		exit;
//	}
//}
/*
if ($_GET['a'] == "other") {
	if ($_GET['t'] == "restore") {
		$tmp = '/root/backup/';
		$filename = $v_name.'.bak.tgz';

		//extract a specific directory
		if (file_exists($v_backup_dir.'/'.$filename)) {
			//echo "The file $filename exists";

			//Recommended
				//system('cd /usr/local; tar xvpfz '.$v_backup_dir.'/'.$filename.' '.$v_name.'/db/');
				//system('cd /usr/local; tar xvpfz '.$v_backup_dir.'/'.$filename.' '.$v_name.'/log/');
				//system('cd /usr/local; tar xvpfz '.$v_backup_dir.'/'.$filename.' '.$v_name.'/recordings/');
				//system('cd /usr/local; tar xvpfz '.$v_backup_dir.'/'.$filename.' '.$v_name.'/scripts/');
				//system('cd /usr/local; tar xvpfz '.$v_backup_dir.'/'.$filename.' '.$v_name.'/storage/');
				//system('cd /usr/local; tar xvpfz '.$v_backup_dir.'/'.$filename.' '.$v_name.'/sounds/custom/8000/');
				//system('cd /usr/local; tar xvpfz '.$v_backup_dir.'/'.$filename.' '.$v_name.'/sounds/music/8000/');
				//system('cd /usr/local; tar xvpfz '.$v_backup_dir.'/'.$filename.' '.$v_name.'/conf/ssl');
				//system('cd /usr/local; tar xvpfz '.$v_backup_dir.'/'.$filename.' '.$v_name.'/conf/sip_profiles/');
				//system('cd /usr/local; tar xvpfz '.$v_backup_dir.'/'.$filename.' '.$v_name.'/conf/vars.xml');
				//system('cd /usr/local; tar xvpfz '.$v_backup_dir.'/'.$filename.' '.$v_name.'/conf/dialplan/default.xml');
				//system('cd /usr/local; tar xvpfz '.$v_backup_dir.'/'.$filename.' '.$v_name.'/conf/dialplan/public.xml');

			//Optional
				//system('cd /usr/local; tar xvpfz '.$v_backup_dir.'/'.$filename.' '.$v_name.'/conf/');
				//system('cd /usr/local; tar xvpfz '.$v_backup_dir.'/'.$filename.' '.$v_name.'/grammar/');
				//system('cd /usr/local; tar xvpfz '.$v_backup_dir.'/'.$filename.' '.$v_name.'/htdocs/');

			//Synchronize Package
				sync_package_freeswitch();

			header( 'Location: v_status.php?savemsg=Backup+has+been+restored.' ) ;
		}
		else {
			header( 'Location: v_status.php?savemsg=Restore+failed.+Backup+file+not+found.' ) ;
		}

		exit;
	}
}
*/
require_once "includes/header.php";

$sql = "";
$sql .= "select * from v_settings ";
$sql .= "where v_id = '$v_id' ";
$prepstatement = $db->prepare(check_sql($sql));
$prepstatement->execute();
$result = $prepstatement->fetchAll();
foreach ($result as &$row) {
	//$v_id = $row["v_id"];
	$event_socket_ip_address = $row["event_socket_ip_address"];
	$event_socket_port = $row["event_socket_port"];
	$event_socket_password = $row["event_socket_password"];
	break; //limit to 1 row
}

?>

<script language="Javascript" type="text/javascript" src="<?php echo PROJECT_PATH ?>/includes/edit_area/edit_area_full.js"></script>
<script language="Javascript" type="text/javascript">
	// initialisation
	editAreaLoader.init({
		id: "log"	// id of the textarea to transform
		,start_highlight: false
		,allow_toggle: true
		,display: "later"
		,language: "en"
		,syntax: "html"
		,toolbar: "search, go_to_line,|, fullscreen, |, undo, redo, |, select_font, |, syntax_selection, |, change_smooth_selection, highlight, reset_highlight, |, help"
		,syntax_selection_allow: "css,html,js,php,xml,c,cpp,sql"
		,show_line_colors: true
	});
</script>


<?php


$savemsg = $_GET["savemsg"];
if ($savemsg) {
	echo "<div align='center'>\n";
	echo "<table width='40%'>\n";
	echo "<tr>\n";
	echo "<th align='left'>Message</th>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td class='rowstyle1'><strong>$savemsg</strong></td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</div>\n";
  //print_info_box($savemsg);
}

echo "<div align='center'>\n";
echo "<table width='100%'><tr><td align='left'>\n";
echo "<br /><br />\n\n";

//sofia status
	$fp = event_socket_create($event_socket_ip_address, $event_socket_port, $event_socket_password);
	$cmd = "api sofia xmlstatus";
	$xml_response = trim(event_socket_request($fp, $cmd));
	//echo "--".$xml_response."--";
	try {
		$xml = new SimpleXMLElement($xml_response);
	}
	catch(Exception $e) {
		echo $e->getMessage();
	}
	//print_r($xml);
	echo "<table width='100%' cellpadding='0' cellspacing='0' border='0'>\n";
	echo "<tr>\n";
	echo "<td width='50%'>\n";
	echo "  <b>sofia status</b> \n";
	echo "</td>\n";
	echo "<td width='50%' align='right'>\n";
	echo "  <input type='button' class='btn' value='reloadxml' onclick=\"document.location.href='v_cmd.php?cmd=api+reloadxml';\" />\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";

	echo "<table width='100%' cellspacing='0'>\n";
	echo "<tr>\n";
	echo "<th>Name</th>\n";
	echo "<th>Type</th>\n";
	echo "<th>Data</th>\n";
	echo "<th>State</th>\n";
	echo "</tr>\n";

	foreach ($xml->profile as $row) {
		//print_r($row);
		echo "<tr>\n";
		echo "	<td class='".$rowstyle[$c]."'>".$row->name."</td>\n";
		echo "	<td class='".$rowstyle[$c]."'>".$row->type."</td>\n";
		echo "	<td class='".$rowstyle[$c]."'>".$row->data."</td>\n";
		echo "	<td class='".$rowstyle[$c]."'>".$row->state."</td>\n";
		echo "</tr>\n";
		if ($c==0) { $c=1; } else { $c=0; }
	}
	foreach ($xml->gateway as $row) {
		//print_r($row);
		echo "<tr>\n";
		echo "	<td class='".$rowstyle[$c]."'>".$row->name."</td>\n";
		echo "	<td class='".$rowstyle[$c]."'>".$row->type."</td>\n";
		echo "	<td class='".$rowstyle[$c]."'>".$row->data."</td>\n";
		echo "	<td class='".$rowstyle[$c]."'>".$row->state."</td>\n";
		echo "</tr>\n";
		if ($c==0) { $c=1; } else { $c=0; }
	}
	foreach ($xml->alias as $row) {
		//print_r($row);
		echo "<tr>\n";
		echo "	<td class='".$rowstyle[$c]."'>".$row->name."</td>\n";
		echo "	<td class='".$rowstyle[$c]."'>".$row->type."</td>\n";
		echo "	<td class='".$rowstyle[$c]."'>".$row->data."</td>\n";
		echo "	<td class='".$rowstyle[$c]."'>".$row->state."</td>\n";
		echo "</tr>\n";
		if ($c==0) { $c=1; } else { $c=0; }
	}
	echo "</table>\n";
	fclose($fp);
	unset($xml);

	echo "<br /><br />\n\n";

//sofia status profile
	foreach (ListFiles($v_conf_dir.'/sip_profiles') as $key=>$sip_profile_file){

		if (substr($sip_profile_file, -4) == ".xml") {
	
			$sip_profile_name = str_replace(".xml", "", $sip_profile_file);
			$fp = event_socket_create($event_socket_ip_address, $event_socket_port, $event_socket_password);
			$cmd = "api sofia xmlstatus profile ".$sip_profile_name."";
			$xml_response = trim(event_socket_request($fp, $cmd));
			if ($xml_response == "Invalid Profile!") { $xml_response = "<error_msg>Invalid Profile!</error_msg>"; }
			$xml_response = str_replace("<profile-info>", "<profile_info>", $xml_response);
			$xml_response = str_replace("</profile-info>", "</profile_info>", $xml_response);
			try {
				$xml = new SimpleXMLElement($xml_response);
			}
			catch(Exception $e) {
				echo $e->getMessage();
				exit;
			}
	
			echo "<br />\n";
			echo "<br />\n";
	
			echo "<table width='100%' cellpadding='0' cellspacing='0' border='0'>\n";
			echo "<tr>\n";
			echo "<td width='50%'>\n";
			echo "  <b>sofia status profile $sip_profile_name</b> \n";
			echo "</td>\n";
			echo "<td width='50%' align='right'>\n";
			echo "  <input type='button' class='btn' value='registrations' onclick=\"document.location.href='v_status_registrations.php?show_reg=1&profile=".$sip_profile_name."';\" />\n";
			echo "  <input type='button' class='btn' value='start' onclick=\"document.location.href='v_cmd.php?cmd=api+sofia+profile+".$sip_profile_name."+start';\" />\n";
			echo "  <input type='button' class='btn' value='stop' onclick=\"document.location.href='v_cmd.php?cmd=api+sofia+profile+".$sip_profile_name."+stop';\" />\n";
			echo "  <input type='button' class='btn' value='restart' onclick=\"document.location.href='v_cmd.php?cmd=api+sofia+profile+".$sip_profile_name."+restart';\" />\n";
			echo "  <input type='button' class='btn' value='rescan' onclick=\"document.location.href='v_cmd.php?cmd=api+sofia+profile+".$sip_profile_name."+rescan';\" />\n";
			if ($sip_profile_name != "external") {
				echo "  <input type='button' class='btn' value='flush_inbound_reg' onclick=\"document.location.href='v_cmd.php?cmd=api+sofia+profile+".$sip_profile_name."+flush_inbound_reg';\" />\n";
			}
			echo "</td>\n";
			echo "</tr>\n";
			echo "</table>\n";
	
			echo "<table width='100%' cellspacing='0' cellpadding='5'>\n";
			echo "<tr>\n";
			echo "<th width='20%'>&nbsp;</th>\n";
			echo "<th>&nbsp;</th>\n";
			echo "</tr>\n";
	
			foreach ($xml->profile_info as $row) {
				//print_r($row);
				echo "	<tr><td class='vncell'>name</td><td class='vtable'>&nbsp; &nbsp;".$row->name."&nbsp;</td></tr>\n";
				echo "	<tr><td class='vncell'>domain-name</td><td class='vtable'>&nbsp; &nbsp;".$row->{'domain-name'}."&nbsp;</td></tr>\n";
				echo "	<tr><td class='vncell'>auto-nat</td><td class='vtable'>&nbsp;".$row->{'auto-nat'}."&nbsp;</td></tr>\n";
				echo "	<tr><td class='vncell'>db-name</td><td class='vtable'>&nbsp;".$row->{'db-name'}."&nbsp;</td></tr>\n";
				echo "	<tr><td class='vncell'>pres-hosts</td><td class='vtable'>&nbsp;".$row->{'pres-hosts'}."&nbsp;</td></tr>\n";
				echo "	<tr><td class='vncell'>dialplan</td><td class='vtable'>&nbsp;".$row->dialplan."&nbsp;</td></tr>\n";
				echo "	<tr><td class='vncell'>context</td><td class='vtable'>&nbsp;".$row->context."&nbsp;</td></tr>\n";
				echo "	<tr><td class='vncell'>challenge-realm</td><td class='vtable'>&nbsp;".$row->{'challenge-realm'}."&nbsp;</td></tr>\n";
				echo "	<tr><td class='vncell'>rtp-ip</td><td class='vtable'>&nbsp;".$row->{'rtp-ip'}."&nbsp;</td></tr>\n";
				echo "	<tr><td class='vncell'>ext-rtp-ip</td><td class='vtable'>&nbsp;".$row->{'ext-rtp-ip'}."&nbsp;</td></tr>\n";
				echo "	<tr><td class='vncell'>sip-ip</td><td class='vtable'>&nbsp;".$row->{'sip-ip'}."&nbsp;</td></tr>\n";
				echo "	<tr><td class='vncell'>ext-sip-ip</td><td class='vtable'>&nbsp;".$row->{'ext-sip-ip'}."&nbsp;</td></tr>\n";
				echo "	<tr><td class='vncell'>url</td><td class='vtable'>&nbsp;".$row->url."&nbsp;</td></tr>\n";
				echo "	<tr><td class='vncell'>bind-url</td><td class='vtable'>&nbsp;".$row->{'bind-url'}."&nbsp;</td></tr>\n";
				echo "	<tr><td class='vncell'>tls-url</td><td class='vtable'>&nbsp;".$row->{'tls-url'}."&nbsp;</td></tr>\n";
				echo "	<tr><td class='vncell'>tls-bind-url</td><td class='vtable'>&nbsp;".$row->{'tls-bind-url'}."&nbsp;</td></tr>\n";
				echo "	<tr><td class='vncell'>hold-music</td><td class='vtable'>&nbsp;".$row->{'hold-music'}."&nbsp;</td></tr>\n";
				echo "	<tr><td class='vncell'>outbound-proxy</td><td class='vtable'>&nbsp;".$row->{'outbound-proxy'}."&nbsp;</td></tr>\n";
				echo "	<tr><td class='vncell'>inbound-codecs</td><td class='vtable'>&nbsp;".$row->{'inbound-codecs'}."&nbsp;</td></tr>\n";
				echo "	<tr><td class='vncell'>outbound-codecs</td><td class='vtable'>&nbsp;".$row->{'outbound-codecs'}."&nbsp;</td></tr>\n";
				echo "	<tr><td class='vncell'>tel-event</td><td class='vtable'>&nbsp;".$row->{'tel-event'}."&nbsp;</td></tr>\n";
				echo "	<tr><td class='vncell'>dtmf-mode</td><td class='vtable'>&nbsp;".$row->{'dtmf-mode'}."&nbsp;</td></tr>\n";
				echo "	<tr><td class='vncell'>cng</td><td class='vtable'>&nbsp;".$row->cng."&nbsp;</td></tr>\n";
				echo "	<tr><td class='vncell'>session-to</td><td class='vtable'>&nbsp;".$row->{'session-to'}."&nbsp;</td></tr>\n";
				echo "	<tr><td class='vncell'>max-dialog</td><td class='vtable'>&nbsp;".$row->{'max-dialog'}."&nbsp;</td></tr>\n";
				echo "	<tr><td class='vncell'>nomedia</td><td class='vtable'>&nbsp;".$row->nomedia."&nbsp;</td></tr>\n";
				echo "	<tr><td class='vncell'>late-neg</td><td class='vtable'>&nbsp;".$row->{'late-neg'}."&nbsp;</td></tr>\n";
				echo "	<tr><td class='vncell'>proxy-media</td><td class='vtable'>&nbsp;".$row->{'proxy-media'}."&nbsp;</td></tr>\n";
				echo "	<tr><td class='vncell'>aggressive-nat</td><td class='vtable'>&nbsp;".$row->{'aggressive-nat'}."&nbsp;</td></tr>\n";
				echo "	<tr><td class='vncell'>stun-enabled</td><td class='vtable'>&nbsp;".$row->{'stun-enabled'}."&nbsp;</td></tr>\n";
				echo "	<tr><td class='vncell'>stun-auto-disable</td><td class='vtable'>&nbsp;".$row->{'stun-auto-disable'}."&nbsp;</td></tr>\n";
				echo "	<tr><td class='vncell'>user-agent-filter</td><td class='vtable'>&nbsp;".$row->{'user-agent-filter'}."&nbsp;</td></tr>\n";
				echo "	<tr><td class='vncell'>max-registrations-per-extension</td><td class='vtable'>&nbsp;".$row->{'max-registrations-per-extension'}."&nbsp;</td></tr>\n";
				echo "	<tr><td class='vncell'>calls-in</td><td class='vtable'>&nbsp;".$row->{'calls-in'}."&nbsp;</td></tr>\n";
				echo "	<tr><td class='vncell'>calls-out</td><td class='vtable'>&nbsp;".$row->{'calls-out'}."&nbsp;</td></tr>\n";
				echo "	<tr><td class='vncell'>failed-calls-in</td><td class='vtable'>&nbsp;".$row->{'failed-calls-in'}."&nbsp;</td></tr>\n";
				echo "	<tr><td class='vncell'>failed-calls-out</td><td class='vtable'>&nbsp;".$row->{'failed-calls-out'}."&nbsp;</td></tr>\n";
			}
			echo "</table>\n";
	
			fclose($fp);
			unset($xml);
			echo "<br /><br />\n\n";
		}
	}

//status
	$fp = event_socket_create($event_socket_ip_address, $event_socket_port, $event_socket_password);
	$cmd = "api status";
	$response = event_socket_request($fp, $cmd);
	echo "<b>status</b><br />\n";
	echo "<pre style=\"font-size: 9pt;\">\n";
	echo $response;
	echo "</pre>\n";
	fclose($fp);
	echo "<br /><br />\n\n";

/*
$fp = event_socket_create($event_socket_ip_address, $event_socket_port, $event_socket_password);
$cmd = "api show channels";
$response = event_socket_request($fp, $cmd);
echo "<b>show channels</b><br />\n";
if (strlen($response) > 40) {
  echo "<textarea cols='85' rows='10' wrap='off'>\n";
  echo $response;
  echo "</textarea>\n";
}
else {
  echo "<pre style=\"font-size: 9pt;\">\n";
  echo $response;
  echo "</pre>\n";
}
fclose($fp);
echo "<br /><br />\n\n";
echo "<br /><br />\n\n";


$fp = event_socket_create($event_socket_ip_address, $event_socket_port, $event_socket_password);
$cmd = "api show calls";
$response = event_socket_request($fp, $cmd);
echo "<b>show calls</b><br />\n";
if (strlen($response) > 40) {
  echo "<textarea cols='85' rows='10' wrap='off'>\n";
  echo $response;
  echo "</textarea>\n";
}
else {
  echo "<pre style=\"font-size: 9pt;\">\n";
  echo $response;
  echo "</pre>\n";
}
fclose($fp);
echo "<br /><br />\n\n";
echo "<br /><br />\n\n";
*/

if (stripos($_SERVER["HTTP_USER_AGENT"], 'windows') !== false) {
	//windows detected
}
else {
	echo "<table width='100%' cellpadding='0' cellspacing='0' border='0'>\n";
	echo "<tr>\n";
	echo "<td width='80%'>\n";
	echo "<b>Backup / Restore</b><br />\n";
	echo "The 'backup' button will tar gzip ".$v_dir." to ".$v_backup_dir."/".$v_name.".bak.tgz it then presents a file to download. \n";
	echo "If the backup file does not exist in ".$v_backup_dir."/".$v_name.".bak.tgz then the 'restore' button will be hidden. \n";
	echo "Use Diagnostics->Command->File to upload: to browse to the file and then click on upload it now ready to be restored. \n";
	echo "<br /><br />\n";
	echo "</td>\n";
	echo "<td width='20%' valign='middle' align='right'>\n";
	echo "  <input type='button' class='btn' value='backup' onclick=\"document.location.href='v_status.php?a=download&t=backup';\" />\n";
	if (file_exists($v_backup_dir.'/'.$v_name.'.bak.tgz')) {
	  echo "  <input type='button' class='btn' value='restore' onclick=\"document.location.href='v_status.php?a=other&t=restore';\" />\n";
	}
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "<br /><br />\n\n";
}


//echo "<table width='100%' cellpadding='0' cellspacing='0' border='0'>\n";
//echo "<tr>\n";
//echo "<td width='50%'>\n";
//echo "<b>Call Detail Records</b><br />\n";
//echo $v_log_dir."/cdr-csv/Master.csv<br /><br />\n";
//echo "</td>\n";
//echo "<td width='50%' align='right'>\n";
//echo "  <input type='button' class='btn' value='download cdr csv' onclick=\"document.location.href='v_status.php?a=download&t=cdrcsv';\" />\n";
//echo "</td>\n";
//echo "</tr>\n";
//echo "</table>\n";
//echo "<br /><br />\n\n";


echo "<table width='100%' cellpadding='0' cellspacing='0' border='0'>\n";
echo "<tr>\n";
echo "<td width='50%'>\n";
echo "<b>Upgrade</b><br />\n";
echo "Use this button to upgrade the graphical interface to the latest version. The first time this is run it will take a while please be patient. In most cases future upgrades will take less time.<br /><br />\n";
echo "</td>\n";
echo "<td width='50%' align='right'>\n";
echo "  <input type='button' class='btn' value='upgrade' onclick=\"document.location.href='".PROJECT_PATH."/core/upgrade/upgrade.php';\" />\n";
echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "<br /><br />\n\n";

echo "<table width='100%' cellpadding='0' cellspacing='0' border='0'>\n";
echo "<tr>\n";
echo "<td width='50%'>\n";
echo "<b>Logs</b><br />\n";
//echo $v_log_dir.'/'.$v_name.".log<br /><br />\n";
echo "</td>\n";
echo "<td width='50%' align='right'>\n";
echo "  <input type='button' class='btn' value='download logs' onclick=\"document.location.href='v_status.php?a=download&t=logs';\" />\n";
echo "</tr>\n";
echo "</table>\n";
echo "<br />\n\n";
if (stristr(PHP_OS, 'WIN')) { 
	//windows detected
	//echo "<b>tail -n 1500 ".$v_log_dir."/".$v_name.".log</b><br />\n";
	echo "<textarea id='log' name='log' style='width: 100%' rows='30' wrap='off'>\n";
	echo tail($v_log_dir."/".$v_name.".log", 1500);
	echo "</textarea>\n";
}
else {
	//windows not detected
	//echo "<b>tail -n 1500 ".$v_log_dir."/".$v_name.".log</b><br />\n";
	echo "<textarea id='log' name='log' style='width: 100%' rows='30' style='' wrap='off'>\n";
	echo system("tail -n 1500 ".$v_log_dir."/".$v_name.".log");
	echo "</textarea>\n";
}
echo "<br /><br />\n\n";


echo "</td></tr></table>\n";
echo "</div>\n";

require_once "includes/footer.php";

?>