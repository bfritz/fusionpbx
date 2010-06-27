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
if (ifgroup("superadmin")) {
	//access granted
}
else {
	echo "access denied";
	exit;
}


//Action add or update
if (isset($_REQUEST["id"])) {
	$action = "update";
	$v_id = check_str($_REQUEST["id"]);
}
else {
	$action = "add";
}

//make sure the sys_get_temp_dir exists 
	if ( !function_exists('sys_get_temp_dir')) {
		function sys_get_temp_dir() {
			if( $temp=getenv('TMP') ) { return $temp; }
			if( $temp=getenv('TEMP') ) { return $temp; }
			if( $temp=getenv('TMPDIR') ) { return $temp; }
			$temp=tempnam(__FILE__,'');
			if (file_exists($temp)) {
				unlink($temp);
				return dirname($temp);
			}
			return null;
		}
	}


//define the variables
	$dbtype = '';
	$dbfilename = '';
	$dbfilepath = '';
	$dbhost = '';
	$dbport = '';
	$dbname = '';
	$dbusername = '';
	$dbpassword = '';

//install_ prefix was used to so these variables aren't overwritten by config.php
	$install_v_dir = '';
	$install_php_dir = '';
	$install_tmp_dir = '/tmp';
	$install_v_backup_dir = '';

	//set the default dbfilepath
	if (strlen($dbfilepath) == 0) { //secure dir
		$dbfilepath = $_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/secure';
	}

//find the freeswitch directory
	if (stristr(PHP_OS, 'WIN')) { 
		//echo "windows: ".PHP_OS;
		if (is_dir('C:/program files/FreeSWITCH')) {
			$install_v_dir = 'C:/program files/FreeSWITCH';
			$v_parent_dir = 'C:/program files';
			$v_startup_script_dir = '';
		}
		if (is_dir('D:/program files/FreeSWITCH')) {
			$install_v_dir = 'D:/program files/FreeSWITCH';
			$v_parent_dir = 'D:/program files';
			$v_startup_script_dir = '';
		}
		if (is_dir('E:/program files/FreeSWITCH')) {
			$install_v_dir = 'E:/program files/FreeSWITCH';
			$v_parent_dir = 'E:/program files';
			$v_startup_script_dir = '';
		}
		if (is_dir('F:/program files/FreeSWITCH')) {
			$install_v_dir = 'F:/program files/FreeSWITCH';
			$v_parent_dir = 'F:/program files';
			$v_startup_script_dir = '';
		}
		if (is_dir('C:/FreeSWITCH')) {
			$install_v_dir = 'C:/FreeSWITCH';
			$v_parent_dir = 'C:';
			$v_startup_script_dir = '';
		}
		if (is_dir('D:/FreeSWITCH')) {
			$install_v_dir = 'D:/FreeSWITCH';
			$v_parent_dir = 'D:';
			$v_startup_script_dir = '';
		}
		if (is_dir('E:/FreeSWITCH')) {
			$install_v_dir = 'E:/FreeSWITCH';
			$v_parent_dir = 'E:';
			$v_startup_script_dir = '';
		}
		if (is_dir('F:/FreeSWITCH')) {
			$install_v_dir = 'F:/FreeSWITCH';
			$v_parent_dir = 'F:';
			$v_startup_script_dir = '';
		}
		if (is_dir('C:/PHP')) { $install_php_dir = 'C:/PHP'; }
		if (is_dir('D:/PHP')) { $install_php_dir = 'D:/PHP'; }
		if (is_dir('E:/PHP')) { $install_php_dir = 'E:/PHP'; }
		if (is_dir('F:/PHP')) { $install_php_dir = 'F:/PHP'; }
		if (is_dir('C:/FreeSWITCH/wamp/bin/php/php5.3.0')) { $install_php_dir = 'C:/FreeSWITCH/wamp/bin/php/php5.3.0'; }
		if (is_dir('D:/FreeSWITCH/wamp/bin/php/php5.3.0')) { $install_php_dir = 'D:/FreeSWITCH/wamp/bin/php/php5.3.0'; }
		if (is_dir('E:/FreeSWITCH/wamp/bin/php/php5.3.0')) { $install_php_dir = 'E:/FreeSWITCH/wamp/bin/php/php5.3.0'; }
		if (is_dir('F:/FreeSWITCH/wamp/bin/php/php5.3.0')) { $install_php_dir = 'F:/FreeSWITCH/wamp/bin/php/php5.3.0'; }
		if (is_dir('C:/fusionpbx/Program/php')) { $install_php_dir = 'C:/fusionpbx/Program/php'; }
		if (is_dir('D:/fusionpbx/Program/php')) { $install_php_dir = 'D:/fusionpbx/Program/php'; }
		if (is_dir('E:/fusionpbx/Program/php')) { $install_php_dir = 'E:/fusionpbx/Program/php'; }
		if (is_dir('F:/fusionpbx/Program/php')) { $install_php_dir = 'F:/fusionpbx/Program/php'; }
	}
	else { 
		//echo "other: ".PHP_OS;
		if (is_dir('/usr/local/freeswitch')) {
			$install_v_dir = '/usr/local/freeswitch';
			$v_parent_dir = '/usr/local';
		}
		if (is_dir('/opt/freeswitch')) {
			$install_v_dir = '/opt/freeswitch';
			$v_parent_dir = '/opt';
		}
		switch (PHP_OS) {
		case "FreeBSD":
			$v_startup_script_dir = '/usr/local/etc/rc.d';
			$install_php_dir = '/usr/local/bin';
			break;
		case "NetBSD":
			$v_startup_script_dir = '/usr/local/etc/rc.d';
			$install_php_dir = '/usr/local/bin';
			break;
		case "OpenBSD":
			$v_startup_script_dir = '/usr/local/etc/rc.d';
			$install_php_dir = '/usr/local/bin';
			break;
		default:
			$v_startup_script_dir = '/etc/init.d';
			$install_php_dir = '/usr/bin';
		}

		/*
		* CYGWIN_NT-5.1
		* Darwin
		* FreeBSD
		* HP-UX
		* IRIX64
		* Linux
		* NetBSD
		* OpenBSD
		* SunOS
		* Unix
		* WIN32
		* WINNT
		* Windows
		* CYGWIN_NT-5.1
		* IRIX64
		* SunOS
		* HP-UX
		* OpenBSD (not in Wikipedia)
		*/
	}


	//set system settings paths
		$v_domain = $_SERVER["HTTP_HOST"]; 
		//$install_v_dir = ''; //freeswitch directory
		//$install_php_dir = '';
		//$install_tmp_dir = '';
		$bin_dir = '/usr/local/freeswitch/bin'; //freeswitch bin directory
		//$v_startup_script_dir = '';
		$v_package_version = '1.0.8';
		$v_build_version = '1.0.6';
		$v_build_revision = 'Release';
		$v_label = 'FusionPBX';
		$v_name = 'freeswitch';
		//$v_parent_dir = '/usr/local';
		$install_v_backup_dir = '/backup';
		$v_web_dir = $_SERVER["DOCUMENT_ROOT"];
		$v_web_root = $_SERVER["DOCUMENT_ROOT"];
		if (is_dir($_SERVER["DOCUMENT_ROOT"].'/fusionpbx')){ $v_relative_url = $_SERVER["DOCUMENT_ROOT"].'/fusionpbx'; } else { $v_relative_url = '/'; }
		$v_conf_dir = $install_v_dir.'/conf';
		$v_db_dir = $install_v_dir.'/db';
		$v_htdocs_dir = $install_v_dir.'/htdocs';
		$v_log_dir = $install_v_dir.'/log';
		$v_mod_dir = $install_v_dir.'/modules';
		$v_extensions_dir = $install_v_dir.'/conf/directory/default';
		$v_dialplan_public_dir = $install_v_dir.'/conf/dialplan/public';
		$v_dialplan_default_dir = $install_v_dir.'/conf/dialplan/default';
		$v_scripts_dir = $install_v_dir.'/scripts';
		$v_storage_dir = $install_v_dir.'/storage';
		$v_recordings_dir = $install_v_dir.'/recordings';
		$v_sounds_dir = $install_v_dir.'/sounds';
		$v_download_path = '';

	//add to v_system settings
		$action = "add";
		if ($action == "add") {
			$sql = "insert into v_system_settings ";
			$sql .= "(";
			//$sql .= "v_id, ";
			$sql .= "v_domain, ";
			$sql .= "php_dir, ";
			$sql .= "tmp_dir, ";
			$sql .= "bin_dir, ";
			$sql .= "v_startup_script_dir, ";
			//$sql .= "v_package_version, ";
			$sql .= "v_build_version, ";
			$sql .= "v_build_revision, ";
			$sql .= "v_label, ";
			$sql .= "v_name, ";
			$sql .= "v_dir, ";
			$sql .= "v_parent_dir, ";
			$sql .= "v_backup_dir, ";
			$sql .= "v_web_dir, ";
			$sql .= "v_web_root, ";
			$sql .= "v_relative_url, ";
			$sql .= "v_conf_dir, ";
			$sql .= "v_db_dir, ";
			$sql .= "v_htdocs_dir, ";
			$sql .= "v_log_dir, ";
			$sql .= "v_extensions_dir, ";
			$sql .= "v_dialplan_public_dir, ";
			$sql .= "v_dialplan_default_dir, ";
			$sql .= "v_mod_dir, ";
			$sql .= "v_scripts_dir, ";
			$sql .= "v_storage_dir, ";
			$sql .= "v_recordings_dir, ";
			$sql .= "v_sounds_dir, ";
			//$sql .= "v_download_path, ";
			$sql .= "v_provisioning_tftp_dir, ";
			$sql .= "v_provisioning_ftp_dir, ";
			$sql .= "v_provisioning_https_dir, ";
			$sql .= "v_provisioning_http_dir ";
			$sql .= ")";
			$sql .= "values ";
			$sql .= "(";
			//$sql .= "'$v_id', ";
			$sql .= "'$v_domain', ";
			$sql .= "'$php_dir', ";
			$sql .= "'$tmp_dir', ";
			$sql .= "'$bin_dir', ";
			$sql .= "'$v_startup_script_dir', ";
			//$sql .= "'$v_package_version', ";
			$sql .= "'$v_build_version', ";
			$sql .= "'$v_build_revision', ";
			$sql .= "'$v_label', ";
			$sql .= "'$v_name', ";
			$sql .= "'$v_dir', ";
			$sql .= "'$v_parent_dir', ";
			$sql .= "'$v_backup_dir', ";
			$sql .= "'$v_web_dir', ";
			$sql .= "'$v_web_root', ";
			$sql .= "'$v_relative_url', ";
			$sql .= "'$v_conf_dir', ";
			$sql .= "'$v_db_dir', ";
			$sql .= "'$v_htdocs_dir', ";
			$sql .= "'$v_log_dir', ";
			$sql .= "'$v_extensions_dir', ";
			$sql .= "'$v_dialplan_public_dir', ";
			$sql .= "'$v_dialplan_default_dir', ";
			$sql .= "'$v_mod_dir', ";
			$sql .= "'$v_scripts_dir', ";
			$sql .= "'$v_storage_dir', ";
			$sql .= "'$v_recordings_dir', ";
			$sql .= "'$v_sounds_dir', ";
			//$sql .= "'$v_download_path', ";
			$sql .= "'$v_provisioning_tftp_dir', ";
			$sql .= "'$v_provisioning_ftp_dir', ";
			$sql .= "'$v_provisioning_https_dir', ";
			$sql .= "'$v_provisioning_http_dir' ";
			$sql .= ")";
			$db->exec(check_sql($sql));
			unset($sql);
		}

	//restore the defaults in the database
		$sql = "update v_system_settings set ";
		$sql .= "v_domain = '$v_domain', ";
		$sql .= "php_dir = '$install_php_dir', ";
		$sql .= "tmp_dir = '$install_tmp_dir', ";
		$sql .= "bin_dir = '$bin_dir', ";
		$sql .= "v_startup_script_dir = '$v_startup_script_dir', ";
		$sql .= "v_package_version = '$v_package_version', ";
		$sql .= "v_build_version = '$v_build_version', ";
		$sql .= "v_build_revision = '$v_build_revision', ";
		$sql .= "v_label = '$v_label', ";
		$sql .= "v_name = '$v_name', ";
		$sql .= "v_dir = '$install_v_dir', ";
		$sql .= "v_parent_dir = '$v_parent_dir', ";
		$sql .= "v_backup_dir = '$install_v_backup_dir', ";
		$sql .= "v_web_dir = '$v_web_dir', ";
		$sql .= "v_web_root = '$v_web_root', ";
		$sql .= "v_relative_url = '$v_relative_url', ";
		$sql .= "v_conf_dir = '$v_conf_dir', ";
		$sql .= "v_db_dir = '$v_db_dir', ";
		$sql .= "v_htdocs_dir = '$v_htdocs_dir', ";
		$sql .= "v_log_dir = '$v_log_dir', ";
		$sql .= "v_mod_dir = '$v_mod_dir', ";
		$sql .= "v_extensions_dir = '$v_extensions_dir', ";
		$sql .= "v_dialplan_public_dir = '$v_dialplan_public_dir', ";
		$sql .= "v_dialplan_default_dir = '$v_dialplan_default_dir', ";
		$sql .= "v_scripts_dir = '$v_scripts_dir', ";
		$sql .= "v_storage_dir = '$v_storage_dir', ";
		$sql .= "v_recordings_dir = '$v_recordings_dir', ";
		$sql .= "v_sounds_dir = '$v_sounds_dir' ";
		//$sql .= "v_download_path = '$v_download_path' ";
		//$sql .= "v_provisioning_tftp_dir = '$v_provisioning_tftp_dir', ";
		//$sql .= "v_provisioning_ftp_dir = '$v_provisioning_ftp_dir', ";
		//$sql .= "v_provisioning_https_dir = '$v_provisioning_https_dir', ";
		//$sql .= "v_provisioning_http_dir = '$v_provisioning_http_dir' ";
		$sql .= "where v_id = '$v_id'";
		$db->exec(check_sql($sql));
		//unset($sql);

		require_once "includes/header.php";
		echo "<meta http-equiv=\"refresh\" content=\"2;url=v_system_settings.php\">\n";
		echo "<div align='center'>\n";
		echo "Restore Complete\n";
		echo "</div>\n";
		require_once "includes/footer.php";
		return;

?>