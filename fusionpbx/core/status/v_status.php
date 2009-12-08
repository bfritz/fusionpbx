<?php
/* $Id$ */
/*
	v_status.php
	Copyright (C) 2008 Mark J Crane
	All rights reserved.

	Redistribution and use in source and binary forms, with or without
	modification, are permitted provided that the following conditions are met:

	1. Redistributions of source code must retain the above copyright notice,
	   this list of conditions and the following disclaimer.

	2. Redistributions in binary form must reproduce the above copyright
	   notice, this list of conditions and the following disclaimer in the
	   documentation and/or other materials provided with the distribution.

	THIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES,
	INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY
	AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
	AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
	OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
	SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
	INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
	CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
	ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
	POSSIBILITY OF SUCH DAMAGE.
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

require_once "includes/header.php";

//$event_socket_password = $config['installedpackages']['freeswitchsettings']['config'][0]['event_socket_password'];
//$event_socket_port = $config['installedpackages']['freeswitchsettings']['config'][0]['event_socket_port'];
//$host = $config['interfaces']['lan']['ipaddr'];




$host = '127.0.0.1';

$sql = "";
$sql .= "select * from v_settings ";
$sql .= "where v_id = '$v_id' ";
$prepstatement = $db->prepare($sql);
$prepstatement->execute();
while($row = $prepstatement->fetch()) {
	//$v_id = $row["v_id"];
	$numbering_plan = $row["numbering_plan"];
	$default_gateway = $row["default_gateway"];
	$default_area_code = $row["default_area_code"];
	$event_socket_port = $row["event_socket_port"];
	$event_socket_password = $row["event_socket_password"];
	$xml_rpc_http_port = $row["xml_rpc_http_port"];
	$xml_rpc_auth_realm = $row["xml_rpc_auth_realm"];
	$xml_rpc_auth_user = $row["xml_rpc_auth_user"];
	$xml_rpc_auth_pass = $row["xml_rpc_auth_pass"];
	$admin_pin = $row["admin_pin"];
	$smtphost = $row["smtphost"];
	$smtpsecure = $row["smtpsecure"];
	$smtpauth = $row["smtpauth"];
	$smtpusername = $row["smtpusername"];
	$smtppassword = $row["smtppassword"];
	$smtpfrom = $row["smtpfrom"];
	$smtpfromname = $row["smtpfromname"];
	$mod_shout_decoder = $row["mod_shout_decoder"];
	$mod_shout_volume = $row["mod_shout_volume"];
	break; //limit to 1 row
}

$host = "127.0.0.1";




//if service is not running then start it
if (!pkg_is_service_running($v_name)) {
//	$handle = popen($v_startup_script_dir."/".$v_name.".sh start", "r");
//	pclose($handle);
	//give time for the service to load
//	sleep(7);
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
//include("fbegin.inc");
//if ($v_label_show) {
//	echo "<p class=\"pgtitle\">$v_label: Status</p>\n";
//}

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
?>



<?php

echo "<div align='center'>\n";
echo "<table width='100%'><tr><td align='left'>\n";

echo "<br /><br />\n\n";


$fp = event_socket_create($host, $event_socket_port, $event_socket_password);
$cmd = "api sofia status";
$response = event_socket_request($fp, $cmd);
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
echo "<pre style=\"font-size: 9pt;\">\n";
echo $response;
echo "</pre>\n";
fclose($fp);
echo "<br /><br />\n\n";

foreach (ListFiles($v_conf_dir.'/sip_profiles') as $key=>$sip_profile_file){
	
	$sip_profile_name = str_replace(".xml", "", $sip_profile_file);
	$fp = event_socket_create($host, $event_socket_port, $event_socket_password);
	$cmd = "api sofia status profile ".$sip_profile_name;
	$response = event_socket_request($fp, $cmd);
	echo "<table width='100%' cellpadding='0' cellspacing='0' border='0'>\n";
	echo "<tr>\n";
	echo "<td width='50%'>\n";
	echo "  <b>sofia status profile $sip_profile_name</b> \n";
	echo "</td>\n";
	echo "<td width='50%' align='right'>\n";
	echo "  <input type='button' class='btn' value='start' onclick=\"document.location.href='v_cmd.php?cmd=api+sofia+profile+".$sip_profile_name."+start';\" />\n";
	echo "  <input type='button' class='btn' value='stop' onclick=\"document.location.href='v_cmd.php?cmd=api+sofia+profile+".$sip_profile_name."+stop';\" />\n";
	echo "  <input type='button' class='btn' value='restart' onclick=\"document.location.href='v_cmd.php?cmd=api+sofia+profile+".$sip_profile_name."+restart';\" />\n";
	if ($sip_profile_name == "external") {
		echo "  <input type='button' class='btn' value='rescan' onclick=\"document.location.href='v_cmd.php?cmd=api+sofia+profile+".$sip_profile_name."+rescan';\" />\n";
	}
	else {
		echo "  <input type='button' class='btn' value='flush_inbound_reg' onclick=\"document.location.href='v_cmd.php?cmd=api+sofia+profile+".$sip_profile_name."+flush_inbound_reg';\" />\n";
	}
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "<pre style=\"font-size: 9pt;\">\n";
	echo $response;
	echo "</pre>\n";
	fclose($fp);
	echo "<br /><br />\n\n";

}


$fp = event_socket_create($host, $event_socket_port, $event_socket_password);
$cmd = "api status";
$response = event_socket_request($fp, $cmd);
echo "<b>status</b><br />\n";
echo "<pre style=\"font-size: 9pt;\">\n";
echo $response;
echo "</pre>\n";
fclose($fp);
echo "<br /><br />\n\n";


$fp = event_socket_create($host, $event_socket_port, $event_socket_password);
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


$fp = event_socket_create($host, $event_socket_port, $event_socket_password);
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


//disabled until further development
//echo "<table width='100%' cellpadding='0' cellspacing='0' border='0'>\n";
//echo "<tr>\n";
//echo "<td width='50%'>\n";
//echo "<b>Web Interface</b><br />\n";
//echo "Use the following button to update the web interface.<br /><br />\n";
//echo "</td>\n";
//echo "<td width='50%' align='right'>\n";
//echo "  <input type='button' class='btn' value='update' onclick=\"document.location.href='v_status.php?a=update&t=gui_phase_1';\" />\n";
//echo "</td>\n";
//echo "</tr>\n";
//echo "</table>\n";
//echo "<br /><br />\n\n";

echo "<table width='100%' cellpadding='0' cellspacing='0' border='0'>\n";
echo "<tr>\n";
echo "<td width='50%'>\n";
echo "<b>Logs</b><br />\n";
echo $v_log_dir.'/'.$v_name.".log<br /><br />\n";
echo "</td>\n";
echo "<td width='50%' align='right'>\n";
echo "  <input type='button' class='btn' value='download logs' onclick=\"document.location.href='v_status.php?a=download&t=logs';\" />\n";
echo "</tr>\n";
echo "</table>\n";
echo "<br /><br />\n\n";
if (stristr(PHP_OS, 'WIN')) { 
	//windows detected
	echo "<b>tail -n 500 ".$v_log_dir."/".$v_name.".log</b><br />\n";
	echo "<textarea id='log' name='log' style='width: 100%' rows='30' wrap='off'>\n";
	echo tail($v_log_dir."/".$v_name.".log", 500);
	echo "</textarea>\n";
}
else {
	//windows not detected
	echo "<b>tail -n 500 ".$v_log_dir."/".$v_name.".log</b><br />\n";
	echo "<textarea id='log' name='log' style='width: 100%' rows='30' style='' wrap='off'>\n";
	echo system("tail -n 500 ".$v_log_dir."/".$v_name.".log");
	echo "</textarea>\n";
}
echo "<br /><br />\n\n";


//$fp = event_socket_create($host, $event_socket_port, $event_socket_password);
//$cmd = "api sofia ";
//$response = event_socket_request($fp, $cmd);
//echo "<b>api sofia</b><br />\n";
//echo "<pre style=\"font-size: 9pt;\">\n";
//echo $response;
//echo "</pre>\n";
//fclose($fp);
//echo "<br /><br />\n\n";
echo "</td></tr></table>\n";
echo "</div>\n";

require_once "includes/footer.php";

?>