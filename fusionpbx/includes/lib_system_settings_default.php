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
require_once "includes/config.php";
require_once "includes/checkauth.php";
if (ifgroup("superadmin")) {
	//access granted
}
else {
	echo "access denied";
	exit;
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

//set the default db_filename
	if ($db_type == "sqlite") {
		if (strlen($db_filename) == 0) { $db_filename = "fusionpbx.db"; }
	}

//set the default install_secure_dir
	if (strlen($install_secure_dir) == 0) { //secure dir
		$install_secure_dir = $_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/secure';
	}

//set the php bin directory
	if (file_exists('/usr/local/bin/php') || file_exists('/usr/local/bin/php5')) {
		$install_php_dir = '/usr/local/bin';
	}
	if (file_exists('/usr/bin/php') || file_exists('/usr/bin/php5')) {
		$install_php_dir = '/usr/bin';
	}

//set the freeswitch bin directory
	if (file_exists('/usr/local/freeswitch/bin')) {
		$install_switch_base_dir = '/usr/local/freeswitch';
		$switch_bin_dir = '/usr/local/freeswitch/bin';
		$parent_dir = '/usr/local';
	}
	if (file_exists('/opt/freeswitch')) {
		$install_switch_base_dir = '/opt/freeswitch';
		$switch_bin_dir = '/opt/freeswitch/bin';
		$parent_dir = '/opt';
	}

//set the default startup script directory
	if (file_exists('/usr/local/etc/rc.d')) {
		$startup_script_dir = '/usr/local/etc/rc.d';
	}
	if (file_exists('/etc/init.d')) {
		$startup_script_dir = '/etc/init.d';
	}

//set the default directories
	$switch_bin_dir = $install_switch_base_dir.'/bin'; //freeswitch bin directory
	$switch_conf_dir = $install_switch_base_dir.'/conf';
	$switch_db_dir = $install_switch_base_dir.'/db';
	$switch_htdocs_dir = $install_switch_base_dir.'/htdocs';
	$switch_log_dir = $install_switch_base_dir.'/log';
	$switch_mod_dir = $install_switch_base_dir.'/mod';
	$switch_extensions_dir = $switch_conf_dir.'/directory/default';
	$switch_gateways_dir = $switch_conf_dir.'/sip_profiles/external';
	$v_dialplan_public_dir = $switch_conf_dir.'/dialplan/public';
	$v_dialplan_default_dir = $switch_conf_dir.'/dialplan/default';
	$switch_scripts_dir = $install_switch_base_dir.'/scripts';
	$switch_grammar_dir = $install_switch_base_dir.'/grammar';
	$switch_storage_dir = $install_switch_base_dir.'/storage';
	$switch_voicemail_dir = $install_switch_base_dir.'/storage/voicemail';
	$switch_recordings_dir = $install_switch_base_dir.'/recordings';
	$switch_sounds_dir = $install_switch_base_dir.'/sounds';
	$install_tmp_dir = realpath(sys_get_temp_dir());
	$install_backup_dir = realpath(sys_get_temp_dir());
	$v_download_path = '';

//set specific alternative directories as required
	switch (PHP_OS) {
	case "FreeBSD":
		//if the freebsd port is installed use the following paths by default.
			if (file_exists('/usr/local/etc/freeswitch/conf')) {

				//set the default db_filepath
					if (strlen($db_filepath) == 0) { //secure dir
						$db_filepath = '/var/db/fusionpbx';
						if (!is_dir($db_filepath)) { mkdir($db_filepath,0777,true); }
					}

				//set the other default directories
					$switch_bin_dir = '/usr/local/bin'; //freeswitch bin directory
					$switch_conf_dir = '/usr/local/etc/freeswitch/conf';
					$switch_db_dir = '/var/db/freeswitch';
					$switch_htdocs_dir = '/usr/local/www/freeswitch/htdocs';
					$switch_log_dir = '/var/log/freeswitch';
					$switch_mod_dir = '/usr/local/lib/freeswitch/mod';
					$switch_extensions_dir = $switch_conf_dir.'/directory/default';
					$switch_gateways_dir = $switch_conf_dir.'/sip_profiles/external';
					$v_dialplan_public_dir = $switch_conf_dir.'/dialplan/public';
					$v_dialplan_default_dir = $switch_conf_dir.'/dialplan/default';
					$switch_scripts_dir = '/usr/local/etc/freeswitch/scripts';
					$switch_grammar_dir = '/usr/local/etc/freeswitch/grammar';
					$switch_storage_dir = '/var/freeswitch';
					$switch_voicemail_dir = '/var/spool/freeswitch/voicemail';
					$switch_recordings_dir = '/var/freeswitch/recordings';
					$switch_sounds_dir = '/usr/local/share/freeswitch/sounds';
			}
			else {
				//set the default db_filepath
					if (strlen($db_filepath) == 0) { //secure dir
						$db_filepath = $_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/secure';
					}
			}
		break;
	case "NetBSD":
		$startup_script_dir = '';
		$install_php_dir = '/usr/local/bin';

		//set the default db_filepath
			if (strlen($db_filepath) == 0) { //secure dir
				$db_filepath = $_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/secure';
			}
		break;
	case "OpenBSD":
		$startup_script_dir = '';

		//set the default db_filepath
			if (strlen($db_filepath) == 0) { //secure dir
				$db_filepath = $_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/secure';
			}
		break;
	default:
		//set the default db_filepath
			if (strlen($db_filepath) == 0) { //secure dir
				$db_filepath = $_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/secure';
			}
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

//set the dir defaults for windows
	if (stristr(PHP_OS, 'WIN')) { 
		//echo "windows: ".PHP_OS;
		if (is_dir('C:/program files/FreeSWITCH')) {
			$install_switch_base_dir = 'C:/program files/FreeSWITCH';
			$parent_dir = 'C:/program files';
			$startup_script_dir = '';
		}
		if (is_dir('D:/program files/FreeSWITCH')) {
			$install_switch_base_dir = 'D:/program files/FreeSWITCH';
			$parent_dir = 'D:/program files';
			$startup_script_dir = '';
		}
		if (is_dir('E:/program files/FreeSWITCH')) {
			$install_switch_base_dir = 'E:/program files/FreeSWITCH';
			$parent_dir = 'E:/program files';
			$startup_script_dir = '';
		}
		if (is_dir('F:/program files/FreeSWITCH')) {
			$install_switch_base_dir = 'F:/program files/FreeSWITCH';
			$parent_dir = 'F:/program files';
			$startup_script_dir = '';
		}
		if (is_dir('C:/FreeSWITCH')) {
			$install_switch_base_dir = 'C:/FreeSWITCH';
			$parent_dir = 'C:';
			$startup_script_dir = '';
		}
		if (is_dir('D:/FreeSWITCH')) {
			$install_switch_base_dir = 'D:/FreeSWITCH';
			$parent_dir = 'D:';
			$startup_script_dir = '';
		}
		if (is_dir('E:/FreeSWITCH')) {
			$install_switch_base_dir = 'E:/FreeSWITCH';
			$parent_dir = 'E:';
			$startup_script_dir = '';
		}
		if (is_dir('F:/FreeSWITCH')) {
			$install_switch_base_dir = 'F:/FreeSWITCH';
			$parent_dir = 'F:';
			$startup_script_dir = '';
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

//set additional information
	if (is_dir($_SERVER["DOCUMENT_ROOT"].'/fusionpbx')){ $relative_url = $_SERVER["DOCUMENT_ROOT"].'/fusionpbx'; } else { $relative_url = '/'; }

?>