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
require_once "root.php";
require_once "includes/config.php";

//preferences
	$v_label_show = false;
	$v_menu_tab_show = false;
	$v_fax_show = true;
	$v_path_show = true;

//get user defined variables
	if (strlen($_SESSION['user_defined_variables']) == 0) {
		$sql = "";
		$sql .= "select * from v_vars ";
		$sql .= "where var_cat = 'Defaults' ";
		$prep_statement = $db->prepare(check_sql($sql));
		$prep_statement->execute();
		$result = $prep_statement->fetchAll(PDO::FETCH_ASSOC);
		foreach ($result as &$row) {
			switch ($row["var_name"]) {
				case "username":
					//not allowed to override this value
					break;
				case "groups":
					//not allowed to override this value
					break;
				case "menu":
					//not allowed to override this value
					break;
				case "template_name":
					//not allowed to override this value
					break;
				case "template_content":
					//not allowed to override this value
					break;
				case "extension_array":
					//not allowed to override this value
					break;
				case "user_extension_array":
					//not allowed to override this value
					break;
				case "user_array":
					//not allowed to override this value
					break;
				default:
					$_SESSION[$row["var_name"]] = $row["var_value"];
			}
		}
		//when this value is cleared it will re-read the user defined variables
		$_SESSION["user_defined_variables"] = "set";
	}

function v_settings() {
	global $db, $domain_uuid, $v_secure;

	$program_dir = '';
	$docroot = $_SERVER["DOCUMENT_ROOT"];
	$docroot = str_replace ("\\", "/", $docroot);
	$docrootarray = explode("/", $docroot);
	$docrootarraycount = count($docrootarray);
	$x = 0;
	foreach ($docrootarray as $value) {
		$program_dir = $program_dir.$value."/";
		if (($docrootarraycount-3) == $x) {
		  break;
		}
		$x++;
	}
	$program_dir = rtrim($program_dir, "/");

	$sql = "";
	$sql .= "select * from v_system_settings ";
	$sql .= "where domain_uuid = '$domain_uuid' ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$result = $prep_statement->fetchAll(PDO::FETCH_ASSOC);
	foreach ($result as &$row) {
		//detected automatically with includes/lib_php.php
		$v_settings_array["v_secure"] = $v_secure;

		$v_settings_array["v_domain"] = $row["v_domain"];
		$v_settings_array["v_account_code"] = $row["v_account_code"];

		$php_dir = $row["php_dir"];
		$php_dir = str_replace ("{program_dir}", $program_dir, $php_dir);
		$v_settings_array["php_dir"] = $php_dir;

		if (file_exists($php_dir."/php")) {  $php_exe = "php"; }
		if (file_exists($php_dir."/php.exe")) {  $php_exe = "php.exe"; }
		$v_settings_array["php_exe"] = $php_exe;

		$tmp_dir = $row["tmp_dir"];
		$tmp_dir = str_replace ("{program_dir}", $program_dir, $tmp_dir);
		$v_settings_array["tmp_dir"] = $tmp_dir;

		$bin_dir = $row["bin_dir"];
		$bin_dir = str_replace ("{program_dir}", $program_dir, $bin_dir);
		$v_settings_array["bin_dir"] = $bin_dir;

		$v_startup_script_dir = $row["v_startup_script_dir"];
		$v_startup_script_dir = str_replace ("{program_dir}", $program_dir, $v_startup_script_dir);
		$v_settings_array["v_startup_script_dir"] = $v_startup_script_dir;

		$v_package_version = $row["v_package_version"];
		$v_package_version = str_replace ("{program_dir}", $program_dir, $v_package_version);
		$v_settings_array["v_package_version"] = $v_package_version;

		$v_build_version = $row["v_build_version"];
		$v_build_version = str_replace ("{program_dir}", $program_dir, $v_build_version);
		$v_settings_array["v_build_version"] = $v_build_version;

		$v_build_revision = $row["v_build_revision"];
		$v_build_revision = str_replace ("{program_dir}", $program_dir, $v_build_revision);
		$v_settings_array["v_build_revision"] = $v_build_revision;

		$v_label = $row["v_label"];
		$v_label = str_replace ("{program_dir}", $program_dir, $v_label);
		$v_settings_array["v_label"] = $v_label;

		$v_name = $row["v_name"];
		$v_label = str_replace ("{program_dir}", $program_dir, $v_label);
		$v_settings_array["v_name"] = $v_name;

		$v_dir = $row["v_dir"];
		$v_dir = str_replace ("{program_dir}", $program_dir, $v_dir);
		$v_settings_array["v_dir"] = $v_dir;

		$v_parent_dir = $row["v_parent_dir"];
		$v_parent_dir = str_replace ("{program_dir}", $program_dir, $v_parent_dir);
		$v_settings_array["v_parent_dir"] = $v_parent_dir;

		$v_backup_dir = $row["v_backup_dir"];
		$v_backup_dir = str_replace ("{program_dir}", $program_dir, $v_backup_dir);
		$v_settings_array["v_backup_dir"] = $v_backup_dir;

		$v_web_dir = $row["v_web_dir"];
		$v_web_dir = str_replace ("{program_dir}", $program_dir, $v_web_dir);
		$v_settings_array["v_web_dir"] = $v_web_dir;

		$v_web_root = $row["v_web_root"];
		$v_web_root = str_replace ("{program_dir}", $program_dir, $v_web_root);
		$v_settings_array["v_web_root"] = $v_web_root;

		$v_relative_url = $row["v_relative_url"];
		$v_relative_url = str_replace ("{program_dir}", $program_dir, $v_relative_url);
		$v_settings_array["v_relative_url"] = $v_relative_url;

		$v_conf_dir = $row["v_conf_dir"];
		$v_conf_dir = str_replace ("{program_dir}", $program_dir, $v_conf_dir);
		$v_settings_array["v_conf_dir"] = $v_conf_dir;

		$v_db_dir = $row["v_db_dir"];
		$v_db_dir = str_replace ("{program_dir}", $program_dir, $v_db_dir);
		$v_settings_array["v_db_dir"] = $v_db_dir;

		$v_htdocs_dir = $row["v_htdocs_dir"];
		$v_htdocs_dir = str_replace ("{program_dir}", $program_dir, $v_htdocs_dir);
		$v_settings_array["v_htdocs_dir"] = $v_htdocs_dir;

		$v_log_dir = $row["v_log_dir"];
		$v_log_dir = str_replace ("{program_dir}", $program_dir, $v_log_dir);
		$v_settings_array["v_log_dir"] = $v_log_dir;

		$v_extensions_dir = $row["v_extensions_dir"];
		if (strlen($v_extensions_dir) == 0) { $v_extensions_dir = $v_conf_dir.'/directory/default'; }
		$v_extensions_dir = str_replace ("{program_dir}", $program_dir, $v_extensions_dir);
		$v_settings_array["v_extensions_dir"] = $v_extensions_dir;

		$v_gateways_dir = $row["v_gateways_dir"];
		if (strlen($v_gateways_dir) == 0) { $v_gateways_dir = $v_conf_dir.'/sip_profiles/external'; }
		$v_gateways_dir = str_replace ("{program_dir}", $program_dir, $v_gateways_dir);
		$v_settings_array["v_gateways_dir"] = $v_gateways_dir;

		$v_dialplan_public_dir = $row["v_dialplan_public_dir"];
		if (strlen($v_dialplan_public_dir) == 0) { $v_dialplan_public_dir = $v_conf_dir.'/dialplan/public'; }
		$v_dialplan_public_dir = str_replace ("{program_dir}", $program_dir, $v_dialplan_public_dir);
		$v_settings_array["v_dialplan_public_dir"] = $v_dialplan_public_dir;

		$v_dialplan_default_dir = $row["v_dialplan_default_dir"];
		if (strlen($v_dialplan_default_dir) == 0) { $v_dialplan_default_dir = $v_conf_dir.'/dialplan/default'; }
		$v_dialplan_default_dir = str_replace ("{program_dir}", $program_dir, $v_dialplan_default_dir);
		$v_settings_array["v_dialplan_default_dir"] = $v_dialplan_default_dir;

		$v_mod_dir = $row["v_mod_dir"];
		$v_mod_dir = str_replace ("{program_dir}", $program_dir, $v_mod_dir);
		$v_settings_array["v_mod_dir"] = $v_mod_dir;

		$v_scripts_dir = $row["v_scripts_dir"];
		$v_scripts_dir = str_replace ("{program_dir}", $program_dir, $v_scripts_dir);
		$v_settings_array["v_scripts_dir"] = $v_scripts_dir;

		$v_grammar_dir = $row["v_grammar_dir"];
		$v_grammar_dir = str_replace ("{program_dir}", $program_dir, $v_grammar_dir);
		$v_settings_array["v_grammar_dir"] = $v_grammar_dir;

		$v_storage_dir = $row["v_storage_dir"];
		$v_storage_dir = str_replace ("{program_dir}", $program_dir, $v_storage_dir);
		$v_settings_array["v_storage_dir"] = $v_storage_dir;

		$v_recordings_dir = $row["v_recordings_dir"];
		$v_recordings_dir = str_replace ("{program_dir}", $program_dir, $v_recordings_dir);
		$v_settings_array["v_recordings_dir"] = $v_recordings_dir;

		$v_sounds_dir = $row["v_sounds_dir"];
		$v_sounds_dir = str_replace ("{program_dir}", $program_dir, $v_sounds_dir);
		$v_settings_array["v_sounds_dir"] = $v_sounds_dir;

		$v_download_path = $row["v_download_path"];
		$v_download_path = str_replace ("{program_dir}", $program_dir, $v_download_path);
		$v_settings_array["v_download_path"] = $v_download_path;

		$v_provisioning_tftp_dir = $row["v_provisioning_tftp_dir"];
		$v_provisioning_tftp_dir = str_replace ("{program_dir}", $program_dir, $v_provisioning_tftp_dir);
		$v_settings_array["v_provisioning_tftp_dir"] = $v_provisioning_tftp_dir;

		$v_provisioning_ftp_dir = $row["v_provisioning_ftp_dir"];
		$v_provisioning_ftp_dir = str_replace ("{program_dir}", $program_dir, $v_provisioning_ftp_dir);
		$v_settings_array["v_provisioning_ftp_dir"] = $v_provisioning_ftp_dir;

		$v_provisioning_https_dir = $row["v_provisioning_https_dir"];
		$v_provisioning_https_dir = str_replace ("{program_dir}", $program_dir, $v_provisioning_https_dir);
		$v_settings_array["v_provisioning_https_dir"] = $v_provisioning_https_dir;

		$v_provisioning_http_dir = $row["v_provisioning_http_dir"];
		$v_provisioning_http_dir = str_replace ("{program_dir}", $program_dir, $v_provisioning_http_dir);
		$v_settings_array["v_provisioning_http_dir"] = $v_provisioning_http_dir;

		$v_menu_guid = $row["v_menu_guid"];
		$v_menu_guid = str_replace ("{program_dir}", $program_dir, $v_menu_guid);
		$v_settings_array["v_menu_guid"] = $v_menu_guid;

		break; //limit to 1 row
	}
	unset ($prep_statement);
	return $v_settings_array;
}
//update the settings
$v_settings_array = v_settings();
foreach($v_settings_array as $name => $value) {
	$$name = $value;
}

//create the recordings/archive/year/month/day directory structure
	$v_recording_archive_dir = $v_recordings_dir."/archive/".date("Y")."/".date("M")."/".date("d");
	if(!is_dir($v_recording_archive_dir)) {
		mkdir($v_recording_archive_dir, 0764, true);
		chmod($v_recordings_dir."/archive/".date("Y"), 0764);
		chmod($v_recordings_dir."/archive/".date("Y")."/".date("M"), 0764);
		chmod($v_recording_archive_dir, 0764);
	}

//get the event socket information
	if (strlen($_SESSION['event_socket_ip_address']) == 0) {
			$sql = "";
			$sql .= "select * from v_settings ";
			$sql .= "where domain_uuid = '1' ";
			$prep_statement = $db->prepare(check_sql($sql));
			$prep_statement->execute();
			$result = $prep_statement->fetchAll(PDO::FETCH_ASSOC);
			foreach ($result as &$row) {
				$_SESSION['event_socket_ip_address'] = $row["event_socket_ip_address"];
				$_SESSION['event_socket_port'] = $row["event_socket_port"];
				$_SESSION['event_socket_password'] = $row["event_socket_password"];
				break; //limit to 1 row
			}
	}


//get the extensions that are assigned to this user
	if (strlen($_SESSION["username"]) > 0 && strlen($_SESSION['user_extension_list']) == 0) {
		//get the user extension list
			$_SESSION['user_extension_list'] = '';
			$sql = "";
			$sql .= "select extension, user_context from v_extensions ";
			$sql .= "where domain_uuid = '$domain_uuid' ";
			$sql .= "and enabled = 'true' ";
			$sql .= "and user_list like '%|".$_SESSION["username"]."|%' ";
			$sql .= "order by extension asc ";
			$result = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
			if (count($result) == 0) {
				//no result found
			}
			else {
				$x = 1;
				foreach($result as $row) {
					if (count($result) == $x) {
						$_SESSION['user_extension_list'] .= $row['extension']."";
					}
					else {
						$_SESSION['user_extension_list'] .= $row['extension']."|";
					}
					$_SESSION['user_context'] = $row["user_context"];
					$x++;
				}
			}
			$user_extension_list = $_SESSION['user_extension_list'];
			$ext_array = explode("|",$user_extension_list);
		//if no extension has been assigned then setting user_context will still need to be set
			if (strlen($_SESSION['user_context']) == 0) {
				$_SESSION['user_context'] = '';
				$sql = "";
				$sql .= "select user_context from v_extensions ";
				$sql .= "where domain_uuid = '$domain_uuid' ";
				$sql .= "limit 1 ";
				$prep_statement = $db->prepare(check_sql($sql));
				$prep_statement->execute();
				$result = $prep_statement->fetchAll(PDO::FETCH_ASSOC);
				foreach ($result as &$row) {
					$_SESSION['user_context'] = $row["user_context"];
					break; //limit to 1 row
				}
			}
	}


if ($db_type == "sqlite") {
	//sqlite: check if call detail record (CDR) db file exists if not create it
	if (!file_exists($dbfilepath.'/'.$server_name.'.cdr.db')) {
		//echo "file does not exist: ".$v_db_dir.'/cdr.db';
		if (copy($dbfilepath.'/cdr.clean.db', $dbfilepath.'/'.$server_name.'.cdr.db')) {
			//echo "copy succeeded.\n";
		}
	}
}


function build_menu() {
	global $v_menu_tab_show;

	if ($v_menu_tab_show) {
		global $config;
		//$v_relative_url = $config['installedpackages']['freeswitchsettings']['config'][0]['v_relative_url'];

		//$script_name_array = split ("/", $_SERVER["SCRIPT_NAME"]);
		//$script_name = $script_name_array[count($script_name_array)-1];
		//echo "script_name: ".$script_name."<br />";

		$tab_array = array();
		$menu_selected = false;
		if ($_SERVER["SCRIPT_NAME"] == $v_relative_url."/v_settings.php") { $menu_selected = true; }
		$tab_array[] = array(gettext("Settings"), $menu_selected, $v_relative_url."/v_settings.php");
		unset($menu_selected);

		$menu_selected = false;
		if ($_SERVER["SCRIPT_NAME"] == $v_relative_url."/v_dialplan_includes.php") { $menu_selected = true; }
		if ($_SERVER["SCRIPT_NAME"] == $v_relative_url."/v_dialplan.php") { $menu_selected = true; }
		if ($_SERVER["SCRIPT_NAME"] == $v_relative_url."/v_dialplan_includes_edit.php") { $menu_selected = true; }
		if ($_SERVER["SCRIPT_NAME"] == $v_relative_url."/v_dialplan_includes_details_edit.php") { $menu_selected = true; }
		$tab_array[] = array(gettext("Dialplan"), $menu_selected, $v_relative_url."/v_dialplan_includes.php");
		unset($menu_selected);

		$menu_selected = false;
		if ($_SERVER["SCRIPT_NAME"] == $v_relative_url."/v_extensions.php") { $menu_selected = true; }
		if ($_SERVER["SCRIPT_NAME"] == $v_relative_url."/v_extensions_edit.php") { $menu_selected = true; }
		$tab_array[] = array(gettext("Extensions"), $menu_selected, $v_relative_url."/v_extensions.php");
		unset($menu_selected);

		$menu_selected = false;
		if ($_SERVER["SCRIPT_NAME"] == $v_relative_url."/v_features.php") { $menu_selected = true; }
		if ($_SERVER["SCRIPT_NAME"] == $v_relative_url."/v_fax.php") { $menu_selected = true; }
		if ($_SERVER["SCRIPT_NAME"] == $v_relative_url."/v_fax_edit.php") { $menu_selected = true; }
		if ($_SERVER["SCRIPT_NAME"] == $v_relative_url."/v_hunt_group.php") { $menu_selected = true; }
		if ($_SERVER["SCRIPT_NAME"] == $v_relative_url."/v_hunt_group_edit.php") { $menu_selected = true; }
		if ($_SERVER["SCRIPT_NAME"] == $v_relative_url."/v_hunt_group_destinations.php") { $menu_selected = true; }
		if ($_SERVER["SCRIPT_NAME"] == $v_relative_url."/v_hunt_group_destinations_edit.php") { $menu_selected = true; }
		if ($_SERVER["SCRIPT_NAME"] == $v_relative_url."/v_auto_attendant.php") { $menu_selected = true; }
		if ($_SERVER["SCRIPT_NAME"] == $v_relative_url."/v_auto_attendant_edit.php") { $menu_selected = true; }
		if ($_SERVER["SCRIPT_NAME"] == $v_relative_url."/v_auto_attendant_options_edit.php") { $menu_selected = true; }
		if ($_SERVER["SCRIPT_NAME"] == $v_relative_url."/v_modules.php") { $menu_selected = true; }
		if ($_SERVER["SCRIPT_NAME"] == $v_relative_url."/v_recordings.php") { $menu_selected = true; }
		if ($_SERVER["SCRIPT_NAME"] == $v_relative_url."/v_recordings_edit.php") { $menu_selected = true; }
		$tab_array[] = array(gettext("Features"), $menu_selected, $v_relative_url."/v_features.php");
		unset($menu_selected);

		$menu_selected = false;
		if ($_SERVER["SCRIPT_NAME"] == $v_relative_url."/v_gateways.php") { $menu_selected = true; }
		if ($_SERVER["SCRIPT_NAME"] == $v_relative_url."/v_gateways_edit.php") { $menu_selected = true; }
		$tab_array[] = array(gettext("Gateways"), $menu_selected, $v_relative_url."/v_gateways.php");
		unset($menu_selected);

		$menu_selected = false;
		if ($_SERVER["SCRIPT_NAME"] == $v_relative_url."/v_profiles.php") { $menu_selected = true; }
		if ($_SERVER["SCRIPT_NAME"] == $v_relative_url."/v_profile_edit.php") { $menu_selected = true; }
		$tab_array[] = array(gettext("Profiles"), $menu_selected, $v_relative_url."/v_profiles.php");
		unset($menu_selected);

		$menu_selected = false;
		if ($_SERVER["SCRIPT_NAME"] == $v_relative_url."/v_public.php") { $menu_selected = true; }	
		if ($_SERVER["SCRIPT_NAME"] == $v_relative_url."/v_public_includes.php") { $menu_selected = true; }
		if ($_SERVER["SCRIPT_NAME"] == $v_relative_url."/v_public_includes_edit.php") { $menu_selected = true; }
		if ($_SERVER["SCRIPT_NAME"] == $v_relative_url."/v_public_includes_details_edit.php") { $menu_selected = true; }	
		$tab_array[] = array(gettext("Public"), $menu_selected, $v_relative_url."/v_public_includes.php");
		unset($menu_selected);

		$menu_selected = false;
		if ($_SERVER["SCRIPT_NAME"] == $v_relative_url."/v_status.php") { $menu_selected = true; }
		$tab_array[] = array(gettext("Status"), $menu_selected, $v_relative_url."/v_status.php");
		unset($menu_selected);

		$menu_selected = false;
		if ($_SERVER["SCRIPT_NAME"] == $v_relative_url."/v_vars.php") { $menu_selected = true; }
		$tab_array[] = array(gettext("Vars"), $menu_selected, $v_relative_url."/v_vars.php");
		unset($menu_selected);

		//display_top_tabs($tab_array);
	}
}


function event_socket_create($host, $port, $password){
	$fp = fsockopen($host, $port, $errno, $errdesc, 3);
	socket_set_blocking($fp,false);

	if (!$fp) {
		//error "invalid handle<br />\n";
		//echo "error number: ".$errno."<br />\n";
		//echo "error description: ".$errdesc."<br />\n";
	}
	else {
		//connected to the socket return the handle
		while (!feof($fp)) {
			$buffer = fgets($fp, 1024);
			usleep(100); //allow time for reponse
			if (trim($buffer) == "Content-Type: auth/request") {
				 fputs($fp, "auth $password\n\n");
				 break;
			}
		}
		return $fp;
	}
} //end function


function event_socket_request($fp, $cmd) {
	if ($fp) {
		fputs($fp, $cmd."\n\n");
		usleep(100); //allow time for reponse

		$response = "";
		$i = 0;
		$contentlength = 0;
		while (!feof($fp)) {
			$buffer = fgets($fp, 4096);
			if ($contentlength > 0) {
				$response .= $buffer;
			}

			if ($contentlength == 0) { //if content length is already don't process again
				if (strlen(trim($buffer)) > 0) { //run only if buffer has content
					$temparray = explode(":", trim($buffer));
					if ($temparray[0] == "Content-Length") {
						$contentlength = trim($temparray[1]);
					}
				}
			}

			usleep(20); //allow time for reponse

			//optional because of script timeout //don't let while loop become endless
			if ($i > 1000000) { break; }

			if ($contentlength > 0) { //is contentlength set
				//stop reading if all content has been read.
				if (strlen($response) >= $contentlength) {
					break;
				}
			}
			$i++;
		}

		return $response;
	}
	else {
		echo "no handle";
	}
}


function event_socket_request_cmd($cmd) {
	global $db, $domain_uuid, $host;
  
	$sql = "";
	$sql .= "select * from v_settings ";
	$sql .= "where domain_uuid = '$domain_uuid' ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$result = $prep_statement->fetchAll(PDO::FETCH_ASSOC);
	foreach ($result as &$row) {
		$event_socket_ip_address = $row["event_socket_ip_address"];
		$event_socket_port = $row["event_socket_port"];
		$event_socket_password = $row["event_socket_password"];
		break; //limit to 1 row
	}
	unset ($prep_statement);

	$fp = event_socket_create($event_socket_ip_address, $event_socket_port, $event_socket_password);
	$response = event_socket_request($fp, $cmd);
	fclose($fp);
}

function byte_convert( $bytes ) {
	if ($bytes<=0) {
		return '0 Byte';
	}

	$convention=1000; //[1000->10^x|1024->2^x]
	$s=array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB');
	$e=floor(log($bytes,$convention));
	$e=floor(log($bytes,$convention));
	return round($bytes/pow($convention,$e),2).' '.$s[$e];
}

function lan_sip_profile() {
	global $config;
	$v_settings_array = v_settings();
	foreach($v_settings_array as $name => $value) {
		$$name = $value;
	}
	clearstatcache();

	//if the lan directory does not exist then create it
	if (!is_dir($v_conf_dir.'/sip_profiles/lan/')) {
		exec("mkdir ".$v_conf_dir."/sip_profiles/lan/");
	}

	//create the LAN profile if it doesn't exist
	if (!file_exists($v_conf_dir.'/sip_profiles/lan.xml')) {
		$lan_ip = $config['interfaces']['lan']['ipaddr'];
		if (strlen($lan_ip) > 0) {
			exec("cp ".$v_conf_dir."/sip_profiles/internal.xml ".$v_conf_dir."/sip_profiles/lan.xml");

			$filename = $v_conf_dir."/sip_profiles/lan.xml";
			$handle = fopen($filename,"rb");
			$contents = fread($handle, filesize($filename));
			fclose($handle);

			$handle = fopen($filename,"w");
			$contents = str_replace("<profile name=\"internal\">", "<profile name=\"lan\">", $contents);
			$contents = str_replace("<alias name=\"default\"/>", "", $contents);
			$contents = str_replace("<X-PRE-PROCESS cmd=\"include\" data=\"internal/*.xml\"/>", "<X-PRE-PROCESS cmd=\"include\" data=\"lan/*.xml\"/>", $contents);
			$contents = str_replace("<param name=\"rtp-ip\" value=\"\$\${local_ip_v4}\"/>", "<param name=\"rtp-ip\" value=\"".$lan_ip."\"/>", $contents);
			$contents = str_replace("<param name=\"sip-ip\" value=\"\$\${local_ip_v4}\"/>", "<param name=\"sip-ip\" value=\"".$lan_ip."\"/>", $contents);
			fwrite($handle, $contents);
			unset($contents);
			fclose($handle);
			unset($filename);
		}
	}
}

function ListFiles($dir) {
	if($dh = opendir($dir)) {
		$files = Array();
		$inner_files = Array();

		while($file = readdir($dh)) {
			if($file != "." && $file != ".." && $file[0] != '.') {
				if(is_dir($dir . "/" . $file)) {
					//$inner_files = ListFiles($dir . "/" . $file); //recursive
					if(is_array($inner_files)) $files = array_merge($files, $inner_files); 
			} else {
					array_push($files, $file);
					//array_push($files, $dir . "/" . $file);
				}
			}
		}
		closedir($dh);
		return $files;
	}
}

function switch_select_destination($select_type, $select_label, $select_name, $select_value, $select_style, $action='') {
	//select_type can be ivr, dialplan, or call_center_contact
	global $config, $db, $domain_uuid;
	$v_settings_array = v_settings();
	foreach($v_settings_array as $name => $value) {
		$$name = $value;
	}

	if (ifgroup("superadmin")) {
		echo "<script>\n";
		echo "var Objs;\n";
		echo "\n";
		echo "function changeToInput".$select_name."(obj){\n";
		echo "	tb=document.createElement('INPUT');\n";
		echo "	tb.type='text';\n";
		echo "	tb.name=obj.name;\n";
		echo "	tb.className='formfld';\n";
		echo "	tb.setAttribute('id', '".$select_name."');\n";
		echo "	tb.setAttribute('style', '".$select_style."');\n";
		echo "	tb.value=obj.options[obj.selectedIndex].value;\n";
		echo "	document.getElementById('btn_select_to_input_".$select_name."').style.visibility = 'hidden';\n";
		echo "	tbb=document.createElement('INPUT');\n";
		echo "	tbb.setAttribute('class', 'btn');\n";
		echo "	tbb.type='button';\n";
		echo "	tbb.value='<';\n";
		echo "	tbb.objs=[obj,tb,tbb];\n";
		echo "	tbb.onclick=function(){ Replace".$select_name."(this.objs); }\n";
		echo "	obj.parentNode.insertBefore(tb,obj);\n";
		echo "	obj.parentNode.insertBefore(tbb,obj);\n";
		echo "	obj.parentNode.removeChild(obj);\n";
		echo "	Replace".$select_name."(this.objs);\n";
		echo "}\n";
		echo "\n";
		echo "function Replace".$select_name."(obj){\n";
		echo "	obj[2].parentNode.insertBefore(obj[0],obj[2]);\n";
		echo "	obj[0].parentNode.removeChild(obj[1]);\n";
		echo "	obj[0].parentNode.removeChild(obj[2]);\n";
		echo "	document.getElementById('btn_select_to_input_".$select_name."').style.visibility = 'visible';\n";
		echo "}\n";
		echo "</script>\n";
		echo "\n";
	}

	//default selection found to false
		$selection_found = false;

	if (ifgroup("superadmin")) {
		echo "		<select name='".$select_name."' id='".$select_name."' class='formfld' style='".$select_style."' onchange='changeToInput".$select_name."(this);'>\n";
		if (strlen($select_value) > 0) {
			if ($select_type == "ivr") {
				echo "		<option value='".$select_value."' selected='selected'>".$select_label."</option>\n";
			}
			if ($select_type == "dialplan") {
				echo "		<option value='".$action.":".$select_value."' selected='selected'>".$select_label."</option>\n";
			}
		}
	}
	else {
		echo "		<select name='".$select_name."' id='".$select_name."' class='formfld' style='".$select_style."'>\n";
	}

	echo "		<option></option>\n";

	//list call center queues
		$sql = "";
		$sql .= "select * from v_call_center_queue ";
		$sql .= "where domain_uuid = '$domain_uuid' ";
		$sql .= "order by queue_name asc ";
		$prep_statement = $db->prepare(check_sql($sql));
		$prep_statement->execute();
		$result = $prep_statement->fetchAll(PDO::FETCH_ASSOC);
		if ($select_type == "dialplan" || $select_type == "ivr") {
			echo "<optgroup label='Call Center'>\n";
		}
		$previous_call_center_name = "";
		foreach ($result as &$row) {
			$queue_name = $row["queue_name"];
			$queue_name = str_replace('_${domain_name}@default', '', $queue_name);
			$queue_extension = $row["queue_extension"];
			if ($previous_call_center_name != $queue_name) {
				if ("menu-exec-app:transfer ".$queue_extension." XML ".$_SESSION["context"] == $select_value || "transfer:".$queue_extension." XML ".$_SESSION["context"] == $select_value) {
					if ($select_type == "ivr") {
						echo "		<option value='menu-exec-app:transfer ".$queue_extension." XML ".$_SESSION["context"]."' selected='selected'>".$queue_extension." ".$queue_name."</option>\n";
					}
					if ($select_type == "dialplan") {
						echo "		<option value='transfer:".$queue_extension." XML ".$_SESSION["context"]."' selected='selected'>".$queue_extension." ".$queue_name."</option>\n";
					}
					$selection_found = true;
				}
				else {
					if ($select_type == "ivr") {
						echo "		<option value='menu-exec-app:transfer ".$queue_extension." XML ".$_SESSION["context"]."'>".$queue_extension." ".$queue_name."</option>\n";
					}
					if ($select_type == "dialplan") {
						echo "		<option value='transfer:".$queue_extension." XML ".$_SESSION["context"]."'>".$queue_extension." ".$queue_name."</option>\n";
					}
				}
				$previous_call_center_name = $queue_name;
			}
		}
		if ($select_type == "dialplan" || $select_type == "ivr") {
			echo "</optgroup>\n";
		}
		unset ($prep_statement);

	//list call groups
		$sql = "";
		$sql .= "select distinct(callgroup) from v_extensions ";
		$sql .= "where domain_uuid = '$domain_uuid' ";
		$sql .= "order by callgroup asc ";
		$prep_statement = $db->prepare(check_sql($sql));
		$prep_statement->execute();
		$x = 0;
		$result = $prep_statement->fetchAll(PDO::FETCH_ASSOC);
		if ($select_type == "dialplan" || $select_type == "ivr") {
			echo "<optgroup label='Call Group'>\n";
		}
		$previous_call_group_name = "";
		foreach ($result as &$row) {
			$call_groups = $row["callgroup"];
			$call_group_array = explode(",", $call_groups);
			foreach ($call_group_array as $call_group) {
				if ($previous_call_group_name != $call_group) {
					if ("menu-exec-app:bridge group/".$call_group."@".$v_domain == $select_value || "bridge:group/".$call_group."@".$v_domain == $select_value) {
						if ($select_type == "ivr") {
							echo "		<option value='menu-exec-app:bridge group/".$call_group."@".$v_domain."' selected='selected'>".$call_group."</option>\n";
						}
						if ($select_type == "dialplan") {
							echo "		<option value='bridge:group/".$call_group."@".$v_domain."' selected='selected'>".$call_group."</option>\n";
						}
						$selection_found = true;
					}
					else {
						if ($select_type == "ivr") {
							echo "		<option value='menu-exec-app:bridge group/".$call_group."@".$v_domain."'>".$call_group."</option>\n";
						}
						if ($select_type == "dialplan") {
							echo "		<option value='bridge:group/".$call_group."@".$v_domain."'>".$call_group."</option>\n";
						}
					}
					$previous_call_group_name = $call_group;
				}
			}
			$x++;
		}
		if ($select_type == "dialplan" || $select_type == "ivr") {
			echo "</optgroup>\n";
		}
		unset ($prep_statement);

	//list conferences
		$sql = "";
		$sql .= "select * from v_dialplan_includes_details ";
		$sql .= "where domain_uuid = '$domain_uuid' ";
		$sql .= "order by field_data asc ";
		$prep_statement = $db->prepare(check_sql($sql));
		$prep_statement->execute();
		$x = 0;
		$result = $prep_statement->fetchAll(PDO::FETCH_ASSOC);
		if ($select_type == "dialplan" || $select_type == "ivr") {
			echo "<optgroup label='Conferences'>\n";
		}
		$previous_conference_name = "";
		foreach ($result as &$row) {
			//$tag = $row["tag"];
			if ($row["field_type"] == "conference") {
				$conference_name = $row["field_data"];
				$conference_name = str_replace('_${domain_name}@default', '', $conference_name);
				if ($previous_conference_name != $conference_name) {
					if ("menu-exec-app:conference ".$row["field_data"] == $select_value || "conference:default ".$row["field_data"] == $select_value) {
						if ($select_type == "ivr") {
							echo "		<option value='menu-exec-app:conference ".$row["field_data"]."' selected='selected'>".$conference_name."</option>\n";
						}
						if ($select_type == "dialplan") {
							echo "		<option value='conference:".$row["field_data"]."' selected='selected'>".$conference_name."</option>\n";
						}
						$selection_found = true;
					}
					else {
						if ($select_type == "ivr") {
							echo "		<option value='menu-exec-app:conference ".$row["field_data"]."'>".$conference_name."</option>\n";
						}
						if ($select_type == "dialplan") {
							echo "		<option value='conference:".$row["field_data"]."'>".$conference_name."</option>\n";
						}
					}
					$previous_conference_name = $conference_name;
				}
				$x++;
			}
		}
		if ($select_type == "dialplan" || $select_type == "ivr") {
			echo "</optgroup>\n";
		}
		unset ($prep_statement);

	//list extensions
		$sql = "";
		$sql .= "select * from v_extensions ";
		$sql .= "where domain_uuid = '$domain_uuid' ";
		$sql .= "and enabled = 'true' ";
		$sql .= "order by extension asc ";
		$prep_statement = $db->prepare(check_sql($sql));
		$prep_statement->execute();
		$result = $prep_statement->fetchAll(PDO::FETCH_ASSOC);

		if ($select_type == "dialplan" || $select_type == "ivr" || $select_type == "call_center_contact") {
			echo "<optgroup label='Extensions'>\n";
		}
		foreach ($result as &$row) {
			$extension = $row["extension"];
			$description = $row["description"];
			if ("transfer ".$extension." XML ".$_SESSION["context"] == $select_value || "transfer:".$extension." XML ".$_SESSION["context"] == $select_value || "user/$extension@".$_SESSION['domains'][$domain_uuid]['domain'] == $select_value) {
				if ($select_type == "ivr") {
					echo "		<option value='menu-exec-app:transfer $extension XML ".$_SESSION["context"]."' selected='selected'>".$extension." ".$description."</option>\n";
				}
				if ($select_type == "dialplan") {
					echo "		<option value='transfer:$extension XML ".$_SESSION["context"]."' selected='selected'>".$extension." ".$description."</option>\n";
				}
				if ($select_type == "call_center_contact") {
					echo "		<option value='user/$extension@".$_SESSION['domains'][$domain_uuid]['domain']."' selected='selected'>".$extension." ".$description."</option>\n";
				}
				$selection_found = true;
			}
			else {
				if ($select_type == "ivr") {
					echo "		<option value='menu-exec-app:transfer $extension XML ".$_SESSION["context"]."'>".$extension." ".$description."</option>\n";
				}
				if ($select_type == "dialplan") {
					echo "		<option value='transfer:$extension XML ".$_SESSION["context"]."'>".$extension." ".$description."</option>\n";
				}
				if ($select_type == "call_center_contact") {
					echo "		<option value='user/$extension@".$_SESSION['domains'][$domain_uuid]['domain']."'>".$extension." ".$description."</option>\n";
				}
			}
		}
		if ($select_type == "dialplan" || $select_type == "ivr" || $select_type == "call_center_contact") {
			echo "</optgroup>\n";
		}
		unset ($prep_statement, $extension);

	//list fax extensions
		if ($select_type == "dialplan" || $select_type == "ivr") {
			$sql = "";
			$sql .= "select * from v_fax ";
			$sql .= "where domain_uuid = '$domain_uuid' ";
			$sql .= "order by fax_extension asc ";
			$prep_statement = $db->prepare(check_sql($sql));
			$prep_statement->execute();
			$result = $prep_statement->fetchAll(PDO::FETCH_ASSOC);
			echo "<optgroup label='FAX'>\n";
			foreach ($result as &$row) {
				$fax_name = $row["fax_name"];
				$extension = $row["fax_extension"];
				if ("transfer $extension XML ".$_SESSION["context"] == $select_value || "transfer:".$extension." XML ".$_SESSION["context"] == $select_value) {
					if ($select_type == "ivr") {
						echo "		<option value='menu-exec-app:transfer $extension XML ".$_SESSION["context"]."' selected='selected'>".$extension." ".$fax_name."</option>\n";
					}
					if ($select_type == "dialplan") {
						echo "		<option value='transfer:$extension XML ".$_SESSION["context"]."' selected='selected'>".$extension." ".$fax_name."</option>\n";
					}
					$selection_found = true;
				}
				else {
					if ($select_type == "ivr") {
						echo "		<option value='menu-exec-app:transfer $extension XML ".$_SESSION["context"]."'>".$extension." ".$fax_name."</option>\n";
					}
					if ($select_type == "dialplan") {
						echo "		<option value='transfer:$extension XML ".$_SESSION["context"]."'>".$extension." ".$fax_name."</option>\n";
					}
				}
			}
			echo "</optgroup>\n";
			unset ($prep_statement, $extension);
		}

	//list fifo queues
		$sql = "";
		$sql .= "select * from v_dialplan_includes_details ";
		$sql .= "where domain_uuid = '$domain_uuid' ";
		$sql .= "order by field_data asc ";
		$prep_statement = $db->prepare(check_sql($sql));
		$prep_statement->execute();
		$x = 0;
		$result = $prep_statement->fetchAll(PDO::FETCH_ASSOC);
		if ($select_type == "dialplan" || $select_type == "ivr") {
			echo "<optgroup label='FIFO'>\n";
		}
		foreach ($result as &$row) {
			//$tag = $row["tag"];
			if ($row["field_type"] == "fifo") {
				if (strpos($row["field_data"], '@${domain_name} in') !== false) {
					$dialplan_include_uuid = $row["dialplan_include_uuid"];
					//get the extension number using the dialplan_include_uuid
						$sql = "select field_data as extension_number ";
						$sql .= "from v_dialplan_includes_details ";
						$sql .= "where domain_uuid = '$domain_uuid' ";
						$sql .= "and dialplan_include_uuid = '$dialplan_include_uuid' ";
						$sql .= "and field_type = 'destination_number' ";
						$tmp = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
						$extension_number = $tmp['extension_number'];
						$extension_number = ltrim($extension_number, "^");
						$extension_number = ltrim($extension_number, "\\");
						$extension_number = rtrim($extension_number, "$");
						unset($tmp);

					//get the extension number using the dialplan_include_uuid
						$sql = "select * ";
						$sql .= "from v_dialplan_includes ";
						$sql .= "where domain_uuid = '$domain_uuid' ";
						$sql .= "and dialplan_include_uuid = '$dialplan_include_uuid' ";
						$tmp = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
						$extension_name = $tmp['extension_name'];
						$extension_name = str_replace("_", " ", $extension_name);
						unset($tmp);

					$fifo_name = $row["field_data"];
					$fifo_name = str_replace('@${domain_name} in', '', $fifo_name);
					$option_label = $extension_number.' '.$extension_name;
					if ($select_type == "ivr") {
						if ("menu-exec-app:transfer ".$row["field_data"] == $select_value) {
							echo "		<option value='menu-exec-app:transfer ".$extension_number." XML ".$_SESSION["context"]."' selected='selected'>".$option_label."</option>\n";
							$selection_found = true;
						}
						else {
							echo "		<option value='menu-exec-app:transfer ".$extension_number." XML ".$_SESSION["context"]."'>".$option_label."</option>\n";
						}
					}
					if ($select_type == "dialplan") {
						if ("transfer:".$row["field_data"] == $select_value) {
							echo "		<option value='transfer:".$extension_number." XML ".$_SESSION["context"]."' selected='selected'>".$option_label."</option>\n";
							$selection_found = true;
						}
						else {
							echo "		<option value='transfer:".$extension_number." XML ".$_SESSION["context"]."'>".$option_label."</option>\n";
						}
					}
				}
			}
		}
		if ($select_type == "dialplan" || $select_type == "ivr") {
			echo "</optgroup>\n";
		}
		unset ($prep_statement);

	//gateways
		if (ifgroup("superadmin")) {
			if ($select_type == "dialplan" || $select_type == "ivr" || $select_type == "call_center_contact") {
				echo "<optgroup label='Gateways'>\n";
			}
			$sql = "";
			$sql .= "select * from v_gateways ";
			$sql .= "where domain_uuid = '$domain_uuid' ";
			$sql .= "and enabled = 'true' ";
			$sql .= "order by gateway asc ";
			$prep_statement = $db->prepare(check_sql($sql));
			$prep_statement->execute();
			$result = $prep_statement->fetchAll(PDO::FETCH_ASSOC);
			$resultcount = count($result);
			unset ($prep_statement, $sql);
			$tmp_selected = '';
			foreach($result as $row) {
				if ($row['gateway'] == $select_value) {
					$tmp_selected = "selected='selected'";
				}
					if ($select_type == "dialplan") {
						echo "		<option value='bridge:sofia/gateway/".$row['gateway']."/xxxxx' $tmp_selected>".$row['gateway']."</option>\n";
					}
					if ($select_type == "ivr") {
						echo "		<option value='menu-exec-app:bridge sofia/gateway/".$row['gateway']."/xxxxx' $tmp_selected>".$row['gateway']."</option>\n";
					}
					if ($select_type == "call_center_contact") {
						echo "		<option value='sofia/gateway/".$row['gateway']."/xxxxx' $tmp_selected>".$row['gateway']."</option>\n";
					}
					$tmp_selected = '';
			}
			unset($sql, $result);
			if ($select_type == "dialplan" || $select_type == "ivr" || $select_type == "call_center_contact") {
				echo "</optgroup>\n";
			}
		}

	//list hunt groups
		$sql = "";
		$sql .= "select * from v_hunt_group ";
		$sql .= "where domain_uuid = '$domain_uuid' ";
		$sql .= "and ( ";
		$sql .= "hunt_group_type = 'simultaneous' ";
		$sql .= "or hunt_group_type = 'sequence' ";
		$sql .= "or hunt_group_type = 'sequentially' ";
		$sql .= ") ";
		$sql .= "order by hunt_group_extension asc ";
		$prep_statement = $db->prepare(check_sql($sql));
		$prep_statement->execute();
		$result = $prep_statement->fetchAll(PDO::FETCH_ASSOC);
		if ($select_type == "dialplan" || $select_type == "ivr") {
			echo "<optgroup label='Hunt Groups'>\n";
		}
		foreach ($result as &$row) {
			$extension = $row["hunt_group_extension"];
			$hunt_group_name = $row["hunt_group_name"];
			if ("transfer $extension XML ".$_SESSION["context"] == $select_value || "transfer:".$extension." XML ".$_SESSION["context"] == $select_value) {
				if ($select_type == "ivr") {
					echo "		<option value='menu-exec-app:transfer $extension XML ".$_SESSION["context"]."' selected='selected'>".$extension." ".$hunt_group_name."</option>\n";
				}
				if ($select_type == "dialplan") {
					echo "		<option value='transfer:$extension XML ".$_SESSION["context"]."' selected='selected'>".$extension." ".$hunt_group_name."</option>\n";
				}
				$selection_found = true;
			}
			else {
				if ($select_type == "ivr") {
					echo "		<option value='menu-exec-app:transfer $extension XML ".$_SESSION["context"]."'>".$extension." ".$hunt_group_name."</option>\n";
				}
				if ($select_type == "dialplan") {
					echo "		<option value='transfer:$extension XML ".$_SESSION["context"]."'>".$extension." ".$hunt_group_name."</option>\n";
				}
			}
		}
		if ($select_type == "dialplan" || $select_type == "ivr") {
			echo "</optgroup>\n";
		}
		unset ($prep_statement, $extension);

	//list ivr menus
		$sql = "";
		$sql .= "select * from v_ivr_menu ";
		$sql .= "where domain_uuid = '$domain_uuid' ";
		$sql .= "and ivr_menu_enabled = 'true' ";
		$sql .= "order by ivr_menu_extension asc ";
		$prep_statement = $db->prepare(check_sql($sql));
		$prep_statement->execute();
		$result = $prep_statement->fetchAll(PDO::FETCH_ASSOC);
		if ($select_type == "dialplan" || $select_type == "ivr") {
			echo "<optgroup label='IVR Menu'>\n";
		}
		foreach ($result as &$row) {
			$extension = $row["ivr_menu_extension"];
			$extension_name = $row["ivr_menu_name"];
			$extension_label = $row["ivr_menu_name"];
			$extension_name = str_replace(" ", "_", $extension_name);
			if (count($_SESSION["domains"]) > 1) {
				$extension_name =  $_SESSION['domains'][$row['domain_uuid']]['domain'].'-'.$extension_name;
			}
			if ("ivr:".$extension_name."" == $select_value || "ivr $extension_name" == $select_value || "transfer:".$extension." XML ".$_SESSION["context"] == $select_value) {
				if ($select_type == "ivr") {
					echo "		<option value='menu-exec-app:ivr $extension_name' selected='selected'>".$extension." ".$extension_label."</option>\n";
				}
				if ($select_type == "dialplan") {
					echo "		<option value='ivr:$extension_name' selected='selected'>".$extension." ".$extension_label."</option>\n";
				}
				$selection_found = true;
			}
			else {
				if ($select_type == "ivr") {
					echo "		<option value='menu-exec-app:ivr $extension_name'>".$extension." ".$extension_label."</option>\n";
				}
				if ($select_type == "dialplan") {
					echo "		<option value='ivr:$extension_name'>".$extension." ".$extension_label."</option>\n";
				}
			}
		}
		if ($select_type == "dialplan" || $select_type == "ivr") {
			echo "</optgroup>\n";
		}
		unset ($prep_statement, $extension);

	//list ivr menus
		if ($select_type == "ivr") {
			//list sub ivr menu
				$sql = "";
				$sql .= "select * from v_ivr_menu ";
				$sql .= "where domain_uuid = '$domain_uuid' ";
				$sql .= "and ivr_menu_enabled = 'true' ";
				$sql .= "order by ivr_menu_name asc ";
				$prep_statement = $db->prepare(check_sql($sql));
				$prep_statement->execute();
				$result = $prep_statement->fetchAll(PDO::FETCH_ASSOC);
				if ($select_type == "dialplan" || $select_type == "ivr") {
					echo "<optgroup label='IVR Sub'>\n";
				}
				foreach ($result as &$row) {
					$extension_name = $row["ivr_menu_name"];
					$extension_label = $row["ivr_menu_name"];
					$extension_name = str_replace(" ", "_", $extension_name);
					if (count($_SESSION["domains"]) > 1) {
						$extension_name = $_SESSION['domains'][$row['domain_uuid']]['domain'].'-'.$extension_name;
					}
					if ($extension_name == $select_value) {
						echo "		<option value='menu-sub:$extension_name' selected='selected'>".$extension_label."</option>\n";
						$selection_found = true;
					}
					else {
						echo "		<option value='menu-sub:$extension_name'>".$extension_label."</option>\n";
					}
				}
				if ($select_type == "dialplan" || $select_type == "ivr") {
					echo "</optgroup>\n";
				}
				unset ($prep_statement, $extension_name);

			//list ivr misc
				if ($select_type == "dialplan" || $select_type == "ivr") {
					echo "<optgroup label='IVR Misc'>\n";
				}
				if ($ivr_menu_options_action == "menu-top") {
					echo "		<option value='menu-top:' selected='selected'>Top</option>\n";
					$selection_found = true;
				}
				else {
					echo "		<option value='menu-top:'>Top</option>\n";
				}
				if ($ivr_menu_options_action == "menu-exit") {
					echo "		<option value='menu-exit:' selected='selected'>Exit</option>\n";
					$selection_found = true;
				}
				else {
					echo "		<option value='menu-exit:'>Exit</option>\n";
				}
				if (strlen($select_value) > 0) {
					if (!$selection_found) {
						echo "		<option value='$select_value' selected='selected'>".$select_value."</option>\n";
					}
				}
				if ($select_type == "dialplan" || $select_type == "ivr") {
					echo "</optgroup>\n";
				}
		}

	//list the languages
		if ($select_type == "dialplan" || $select_type == "ivr") {
			echo "<optgroup label='Language'>\n";
		}
		//dutch
		if ("menu-exec-app:set default_language=nl" == $select_value || "set:default_language=nl" == $select_value) {
			if ($select_type == "ivr") {
				echo "	<option value='menu-exec-app:set default_language=nl' selected='selected'>Dutch</option>\n";
			}
			if ($select_type == "dialplan") {
				echo "	<option value='set:default_language=nl' selected='selected'>Dutch</option>\n";
			}
		}
		else {
			if ($select_type == "ivr") {
				echo "	<option value='menu-exec-app:set default_language=nl'>Dutch</option>\n";
			}
			if ($select_type == "dialplan") {
				echo "	<option value='set:default_language=nl'>Dutch</option>\n";
			}
		}
		//english
		if ("menu-exec-app:set default_language=en" == $select_value || "set:default_language=en" == $select_value) {
			if ($select_type == "ivr") {
				echo "	<option value='menu-exec-app:set default_language=en' selected='selected'>English</option>\n";
			}
			if ($select_type == "dialplan") {
				echo "	<option value='set:default_language=en' selected='selected'>English</option>\n";
			}
		}
		else {
			if ($select_type == "ivr") {
				echo "	<option value='menu-exec-app:set default_language=en'>English</option>\n";
			}
			if ($select_type == "dialplan") {
				echo "	<option value='set:default_language=en'>English</option>\n";
			}
		}
		//french
		if ("menu-exec-app:set default_language=fr" == $select_value || "set:default_language=fr" == $select_value) {
			if ($select_type == "ivr") {
				echo "	<option value='menu-exec-app:set default_language=fr' selected='selected'>French</option>\n";
			}
			if ($select_type == "dialplan") {
				echo "	<option value='set:default_language=fr' selected='selected'>French</option>\n";
			}
		}
		else {
			if ($select_type == "ivr") {
				echo "	<option value='menu-exec-app:set default_language=fr'>French</option>\n";
			}
			if ($select_type == "dialplan") {
				echo "	<option value='set:default_language=fr'>French</option>\n";
			}
		}
		//italian
		if ("menu-exec-app:set default_language=it" == $select_value || "set:default_language=it" == $select_value) {
			if ($select_type == "ivr") {
				echo "	<option value='menu-exec-app:set default_language=it' selected='selected'>Italian</option>\n";
			}
			if ($select_type == "dialplan") {
				echo "	<option value='set:default_language=it' selected='selected'>Italian</option>\n";
			}
		}
		else {
			if ($select_type == "ivr") {
				echo "	<option value='menu-exec-app:set default_language=it'>Italian</option>\n";
			}
			if ($select_type == "dialplan") {
				echo "	<option value='set:default_language=it'>Italian</option>\n";
			}
		}
		//german
		if ("menu-exec-app:set default_language=de" == $select_value || "set:default_language=de" == $select_value) {
			if ($select_type == "ivr") {
				echo "	<option value='menu-exec-app:set default_language=de' selected='selected'>German</option>\n";
			}
			if ($select_type == "dialplan") {
				echo "	<option value='set:default_language=de' selected='selected'>German</option>\n";
			}
		}
		else {
			if ($select_type == "ivr") {
				echo "	<option value='menu-exec-app:set default_language=de'>German</option>\n";
			}
			if ($select_type == "dialplan") {
				echo "	<option value='set:default_language=de'>German</option>\n";
			}
		}
		//spanish
		if ("menu-exec-app:set default_language=es" == $select_value || "set:default_language=es" == $select_value) {
			if ($select_type == "ivr") {
				echo "	<option value='menu-exec-app:set default_language=es' selected='selected'>Spanish</option>\n";
			}
			if ($select_type == "dialplan") {
				echo "	<option value='set:default_language=es' selected='selected'>Spanish</option>\n";
			}
		}
		else {
			if ($select_type == "ivr") {
				echo "	<option value='menu-exec-app:set default_language=es'>Spanish</option>\n";
			}
			if ($select_type == "dialplan") {
				echo "	<option value='set:default_language=es'>Spanish</option>\n";
			}
		}
		if ($select_type == "dialplan" || $select_type == "ivr") {
			echo "</optgroup>\n";
		}

	//recordings
		if ($select_type == "dialplan" || $select_type == "ivr") {
			if($dh = opendir($v_recordings_dir."/")) {
				$tmp_selected = false;
				$files = Array();
				echo "<optgroup label='Recordings'>\n";
				while($file = readdir($dh)) {
					if($file != "." && $file != ".." && $file[0] != '.') {
						if(is_dir($v_recordings_dir . "/" . $file)) {
							//this is a directory
						}
						else {
							if ($ivr_menu_greet_long == $v_recordings_dir."/".$file) {
								$tmp_selected = true;
								if ($select_type == "dialplan") {
									echo "		<option value='playback:".$v_recordings_dir."/".$file."' selected>".$file."</option>\n";
								}
								if ($select_type == "ivr") {
									echo "		<option value='menu-exec-app:playback ".$v_recordings_dir."/".$file."' selected>".$file."</option>\n";
								}
							}
							else {
								if ($select_type == "dialplan") {
									echo "		<option value='playback:".$v_recordings_dir."/".$file."'>".$file."</option>\n";
								}
								if ($select_type == "ivr") {
									echo "		<option value='menu-exec-app:playback ".$v_recordings_dir."/".$file."'>".$file."</option>\n";
								}
							}
						}
					}
				}
				closedir($dh);
				echo "</optgroup>\n";
			}
		}

	//list time conditions
		$sql = "";
		$sql .= "select * from v_dialplan_includes_details ";
		$sql .= "where domain_uuid = '$domain_uuid' ";
		$prep_statement = $db->prepare(check_sql($sql));
		$prep_statement->execute();
		$x = 0;
		$result = $prep_statement->fetchAll(PDO::FETCH_ASSOC);
		foreach ($result as &$row) {
			//$tag = $row["tag"];
			switch ($row['field_type']) {
			case "hour":
				$time_array[$row['dialplan_include_uuid']] = $row['field_type'];
				break;
			case "minute":
				$time_array[$row['dialplan_include_uuid']] = $row['field_type'];
				break;
			case "minute-of-day":
				$time_array[$row['dialplan_include_uuid']] = $row['field_type'];
				break;
			case "mday":
				$time_array[$row['dialplan_include_uuid']] = $row['field_type'];
				break;
			case "mweek":
				$time_array[$row['dialplan_include_uuid']] = $row['field_type'];
				break;
			case "mon":
				$time_array[$row['dialplan_include_uuid']] = $row['field_type'];
				break;
			case "yday":
				$time_array[$row['dialplan_include_uuid']] = $row['field_type'];
				break;
			case "year":
				$time_array[$row['dialplan_include_uuid']] = $row['field_type'];
				break;
			case "wday":
				$time_array[$row['dialplan_include_uuid']] = $row['field_type'];
				break;
			case "week":
				$time_array[$row['dialplan_include_uuid']] = $row['field_type'];
				break;
			default:
				//$time_array[$row['dialplan_include_uuid']] = $row['field_type'];
				break;
			}
		}
		if ($select_type == "dialplan" || $select_type == "ivr") {
			echo "<optgroup label='Time Conditions'>\n";
		}
		foreach($time_array as $key=>$val) {    
			$dialplan_include_uuid = $key;
			//get the extension number using the dialplan_include_uuid
				$sql = "select field_data as extension_number ";
				$sql .= "from v_dialplan_includes_details ";
				$sql .= "where domain_uuid = '$domain_uuid' ";
				$sql .= "and dialplan_include_uuid = '$dialplan_include_uuid' ";
				$sql .= "and field_type = 'destination_number' ";
				$sql .= "order by extension_number asc ";
				$tmp = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
				$extension_number = $tmp['extension_number'];
				$extension_number = ltrim($extension_number, "^");
				$extension_number = ltrim($extension_number, "\\");
				$extension_number = rtrim($extension_number, "$");
				unset($tmp);

			//get the extension number using the dialplan_include_uuid
				$sql = "select * ";
				$sql .= "from v_dialplan_includes ";
				$sql .= "where domain_uuid = '$domain_uuid' ";
				$sql .= "and dialplan_include_uuid = '$dialplan_include_uuid' ";
				$tmp = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
				$extension_name = $tmp['extension_name'];
				$extension_name = str_replace("_", " ", $extension_name);
				unset($tmp);

				$option_label = $extension_number.' '.$extension_name;
				if ($select_type == "ivr") {
					if ("menu-exec-app:transfer ".$row["field_data"]." XML ".$_SESSION["context"] == $select_value) {
						echo "		<option value='menu-exec-app:transfer ".$extension_number." XML ".$_SESSION["context"]."' selected='selected'>".$option_label."</option>\n";
						$selection_found = true;
					}
					else {
						echo "		<option value='menu-exec-app:transfer ".$extension_number." XML ".$_SESSION["context"]."'>".$option_label."</option>\n";
					}
				}
				if ($select_type == "dialplan") {
					if ("transfer:".$row["field_data"] == $select_value) {
						echo "		<option value='transfer:".$extension_number." XML ".$_SESSION["context"]."' selected='selected'>".$option_label."</option>\n";
						$selection_found = true;
					}
					else {
						echo "		<option value='transfer:".$extension_number." XML ".$_SESSION["context"]."'>".$option_label."</option>\n";
					}
				}
		}
		if ($select_type == "dialplan" || $select_type == "ivr") {
			echo "</optgroup>\n";
		}
		unset ($prep_statement);

	//list voicemail
		$sql = "";
		$sql .= "select * from v_extensions ";
		$sql .= "where domain_uuid = '$domain_uuid' ";
		$sql .= "and enabled = 'true' ";
		$sql .= "order by extension asc ";
		$prep_statement = $db->prepare(check_sql($sql));
		$prep_statement->execute();
		$result = $prep_statement->fetchAll(PDO::FETCH_ASSOC);
		if ($select_type == "dialplan" || $select_type == "ivr") {
			echo "<optgroup label='Voicemail'>\n";
		}
		foreach ($result as &$row) {
			$extension = $row["extension"]; //default ${domain_name} 
			$description = $row["description"];
			if ("voicemail default \${domain} ".$extension == $select_value || "voicemail:default \${domain} ".$extension == $select_value) {
				if ($select_type == "ivr") {
					echo "		<option value='menu-exec-app:voicemail default \${domain} $extension' selected='selected'>".$extension." ".$description."</option>\n";
				}
				if ($select_type == "dialplan") {
					echo "		<option value='voicemail:default \${domain} $extension' selected='selected'>".$extension." ".$description."</option>\n";
				}
				$selection_found = true;
			}
			else {
				if ($select_type == "ivr") {
					echo "		<option value='menu-exec-app:voicemail default \${domain} $extension'>".$extension." ".$description."</option>\n";
				}
				if ($select_type == "dialplan") {
					echo "		<option value='voicemail:default \${domain} $extension'>".$extension." ".$description."</option>\n";
				}
			}
		}
		if ($select_type == "dialplan" || $select_type == "ivr") {
			echo "</optgroup>\n";
		}

	//other
		if (ifgroup("superadmin")) {
			if ($select_type == "dialplan" || $select_type == "ivr" || $select_type == "call_center_contact") {
				echo "<optgroup label='Other'>\n";
			}
				if ($select_type == "dialplan" || $select_type == "ivr") {
					//set the default value
						$selected = '';
					//answer
						if ($select_value == "answer") { $selected = "selected='selected'"; }
						if ($select_type == "dialplan") {
							echo "		<option value='answer' $selected>answer</option>\n";
						}
						if ($select_type == "ivr") {
							echo "		<option value='menu-exec-app:answer' $selected>answer</option>\n";
						}
					//bridge
						if ($select_value == "bridge") { $selected = "selected='selected'"; }
						if ($select_type == "dialplan") {
							echo "		<option value='bridge:' $selected>bridge</option>\n";
						}
						if ($select_type == "ivr") {
							echo "		<option value='menu-exec-app:bridge ' $selected>bridge</option>\n";
						}
					//db
						if ($select_value == "db") { $selected = "selected='selected'"; }
						if ($select_type == "dialplan") {
							echo "		<option value='db:' $selected>db</option>\n";
						}
						if ($select_type == "ivr") {
							echo "		<option value='menu-exec-app:db ' $selected>db</option>\n";
						}
					//export
						if ($select_value == "export") { $selected = "selected='selected'"; }
						if ($select_type == "dialplan") {
							echo "		<option value='export:' $selected>export</option>\n";
						}
						if ($select_type == "ivr") {
							echo "		<option value='menu-exec-app:export ' $selected>export</option>\n";
						}
					//global_set
						if ($select_value == "global_set") { $selected = "selected='selected'"; }
						if ($select_type == "dialplan") {
							echo "		<option value='global_set:' $selected>global_set</option>\n";
						}
						if ($select_type == "ivr") {
							echo "		<option value='menu-exec-app:global_set ' $selected>global_set</option>\n";
						}
					//group
						if ($select_value == "group") { $selected = "selected='selected'"; }
						if ($select_type == "dialplan") {
							echo "		<option value='group:' $selected>group</option>\n";
						}
						if ($select_type == "ivr") {
							echo "		<option value='menu-exec-app:group ' $selected>group</option>\n";
						}
					//hangup
						if ($select_value == "hangup") { $selected = "selected='selected'"; }
						if ($select_type == "dialplan") {
							echo "		<option value='hangup' $selected>hangup</option>\n";
						}
						if ($select_type == "ivr") {
							echo "		<option value='menu-exec-app:hangup' $selected>hangup</option>\n";
						}
					//info
						if ($select_value == "info") { $selected = "selected='selected'"; }
						if ($select_type == "dialplan") {
							echo "		<option value='info' $selected>info</option>\n";
						}
						if ($select_type == "ivr") {
							echo "		<option value='menu-exec-app:info' $selected>info</option>\n";
						}
					//javascript
						if ($select_value == "javascript") { $selected = "selected='selected'"; }
						if ($select_type == "dialplan") {
							echo "		<option value='javascript:' $selected>javascript</option>\n";
						}
						if ($select_type == "ivr") {
							echo "		<option value='menu-exec-app:javascript ' $selected>javascript</option>\n";
						}
					//lua
						if ($select_value == "lua") { $selected = "selected='selected'"; }
						if ($select_type == "dialplan") {
							echo "		<option value='lua:' $selected>lua</option>\n";
						}
						if ($select_type == "ivr") {
							echo "		<option value='menu-exec-app:lua ' $selected>lua</option>\n";
						}
					//perl
						if ($select_value == "perl") { $selected = "selected='selected'"; }
						if ($select_type == "dialplan") {
							echo "		<option value='perl:' $selected>perl</option>\n";
						}
						if ($select_type == "ivr") {
							echo "		<option value='menu-exec-app:perl ' $selected>perl</option>\n";
						}
					//reject
						if ($select_value == "reject") { $selected = "selected='selected'"; }
						if ($select_type == "dialplan") {
							echo "		<option value='reject' $selected>reject</option>\n";
						}
						if ($select_type == "ivr") {
							echo "		<option value='menu-exec-app:reject' $selected>reject</option>\n";
						}
					//set
						if ($select_value == "set") { $selected = "selected='selected'"; }
						if ($select_type == "dialplan") {
							echo "		<option value='set:' $selected>set</option>\n";
						}
						if ($select_type == "ivr") {
							echo "		<option value='menu-exec-app:set ' $selected>set</option>\n";
						}
					//sleep	
						if ($select_value == "sleep") { $selected = "selected='selected'"; }
						if ($select_type == "dialplan") {
							echo "		<option value='sleep:' $selected>sleep</option>\n";
						}
						if ($select_type == "ivr") {
							echo "		<option value='menu-exec-app:sleep ' $selected>sleep</option>\n";
						}
					//transfer
						if ($select_value == "transfer") { $selected = "selected='selected'"; }
						if ($select_type == "dialplan") {
							echo "		<option value='transfer:' $selected>transfer</option>\n";
						}
						if ($select_type == "ivr") {
							echo "		<option value='menu-exec-app:transfer ' $selected>transfer</option>\n";
						}
					//other
						if ($select_value == "other") {
							echo "		<option value='' selected='selected'>other</option>\n";
						} else {
							echo "		<option value=''>other</option>\n";
						}
				}
				if (!$selection_found) {
					if (strlen($select_label) > 0) {
						echo "		<option value='".$select_value."' selected='selected'>".$select_label."</option>\n";
					}
					else {
						echo "		<option value='".$select_value."' selected='selected'>".$select_value."</option>\n";
					}
				}
			if ($select_type == "dialplan" || $select_type == "ivr" || $select_type == "call_center_contact") {
				echo "</optgroup>\n";
			}
		}

		/*
		//echo "    <option value='answer'>answer</option>\n";
		//echo "    <option value='bridge'>bridge</option>\n";
		echo "    <option value='cond'>cond</option>\n";
		//echo "    <option value='db'>db</option>\n";
		//echo "    <option value='global_set'>global_set</option>\n";
		//echo "    <option value='group'>group</option>\n";
		echo "    <option value='expr'>expr</option>\n";
		//echo "    <option value='export'>export</option>\n";
		//echo "    <option value='hangup'>hangup</option>\n";
		//echo "    <option value='info'>info</option>\n";
		//echo "    <option value='javascript'>javascript</option>\n";
		//echo "    <option value='lua'>lua</option>\n";
		echo "    <option value='playback'>playback</option>\n";
		echo "    <option value='read'>read</option>\n";
		//echo "    <option value='reject'>reject</option>\n";
		echo "    <option value='respond'>respond</option>\n";
		echo "    <option value='ring_ready'>ring_ready</option>\n";
		//echo "    <option value='set'>set</option>\n";
		echo "    <option value='set_user'>set_user</option>\n";
		//echo "    <option value='sleep'>sleep</option>\n";
		echo "    <option value='sofia_contact'>sofia_contact</option>\n";
		//echo "    <option value='transfer'>transfer</option>\n";
		echo "    <option value='voicemail'>voicemail</option>\n";
		echo "    <option value='conference'>conference</option>\n";
		echo "    <option value='conference_set_auto_outcall'>conference_set_auto_outcall</option>\n";
		*/
		unset ($prep_statement, $extension);

	echo "		</select>\n";
	if (ifgroup("superadmin")) {
		echo "<input type='button' id='btn_select_to_input_".$select_name."' class='btn' name='' alt='back' onclick='changeToInput".$select_name."(document.getElementById(\"".$select_name."\"));this.style.visibility = \"hidden\";' value='<'>";
	}
}

function sync_package_v_settings() {
	global $config;
	$v_settings_array = v_settings();
	foreach($v_settings_array as $name => $value) {
		$$name = $value;
	}

	global $db, $domain_uuid, $host;
 
	$sql = "";
	$sql .= "select * from v_settings ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$result = $prep_statement->fetchAll(PDO::FETCH_ASSOC);
	foreach ($result as &$row) {
		//$numbering_plan = $row["numbering_plan"];
		//$default_gateway = $row["default_gateway"];
		//$default_area_code = $row["default_area_code"];
		//$event_socket_ip_address = $row["event_socket_ip_address"];
		//$event_socket_port = $row["event_socket_port"];
		//$event_socket_password = $row["event_socket_password"];
		//$xml_rpc_http_port = $row["xml_rpc_http_port"];
		//$xml_rpc_auth_realm = $row["xml_rpc_auth_realm"];
		//$xml_rpc_auth_user = $row["xml_rpc_auth_user"];
		//$xml_rpc_auth_pass = $row["xml_rpc_auth_pass"];
		//$admin_pin = $row["admin_pin"];
		//$smtp_host = $row["smtp_host"];
		//$smtp_secure = $row["smtp_secure"];
		//$smtp_auth = $row["smtp_auth"];
		//$smtp_username = $row["smtp_username"];
		//$smtp_password = $row["smtp_password"];
		//$smtp_from = $row["smtp_from"];
		//$smtp_from_name = $row["smtp_from_name"];
		//$mod_shout_decoder = $row["mod_shout_decoder"];
		//$mod_shout_volume = $row["mod_shout_volume"];

		$fout = fopen($v_secure."/v_config_cli.php","w");
		$tmp_xml = "<?php\n";
		$tmp_xml .= "\n";
		$tmp_xml .= "error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING ^ E_DEPRECATED ); //hide notices and warnings\n";
		$tmp_xml .= "\n";
		$tmp_xml .= "//set the email variables\n";
		$tmp_xml .= "	\$v_smtp_host = \"".$row["smtp_host"]."\";\n";
		if ($row["smtp_secure"] == "none") {
			$tmp_xml .= "	\$v_smtp_secure = \"\";\n";
		}
		else {
			$tmp_xml .= "	\$v_smtp_secure = \"".$row["smtp_secure"]."\";\n";
		}
		$tmp_xml .= "	\$v_smtp_auth = \"".$row["smtp_auth"]."\";\n";
		$tmp_xml .= "	\$v_smtp_username = \"".$row["smtp_username"]."\";\n";
		$tmp_xml .= "	\$v_smtp_password = \"".$row["smtp_password"]."\";\n";
		$tmp_xml .= "	\$v_smtp_from = \"".$row["smtp_from"]."\";\n";
		$tmp_xml .= "	\$v_smtp_from_name = \"".$row["smtp_from_name"]."\";\n";
		$tmp_xml .= "\n";
		$tmp_xml .= "//set system dir variables\n";
		$tmp_xml .= "	\$v_storage_dir = \"".$v_storage_dir."\";\n";
		$tmp_xml .= "	\$tmp_dir = \"".$tmp_dir."\";\n";
		$tmp_xml .= "	\$php_dir = \"".$php_dir."\";\n";
		$tmp_xml .= "	\$v_secure = \"".$v_secure."\";\n";
		$tmp_xml .= "\n";
		$tmp_xml .= "?>";
		fwrite($fout, $tmp_xml);
		unset($tmp_xml);
		fclose($fout);

		$fout = fopen($v_conf_dir."/directory/default/default.xml","w");
		$tmp_xml = "<include>\n";
		$tmp_xml .= "  <user id=\"default\"> <!--if id is numeric mailbox param is not necessary-->\n";
		$tmp_xml .= "    <variables>\n";
		$tmp_xml .= "      <!--all variables here will be set on all inbound calls that originate from this user -->\n";
		$tmp_xml .= "      <!-- set these to take advantage of a dialplan localized to this user -->\n";
		$tmp_xml .= "      <variable name=\"numbering_plan\" value=\"" . $row['numbering_plan'] . "\"/>\n";
		$tmp_xml .= "      <variable name=\"default_gateway\" value=\"" . $row['default_gateway'] . "\"/>\n";
		$tmp_xml .= "      <variable name=\"default_area_code\" value=\"" . $row['default_area_code'] . "\"/>\n";
		$tmp_xml .= "    </variables>\n";
		$tmp_xml .= "  </user>\n";
		$tmp_xml .= "</include>\n";
		fwrite($fout, $tmp_xml);
		unset($tmp_xml);
		fclose($fout);

		$event_socket_ip_address = $row['event_socket_ip_address'];
		if (strlen($event_socket_ip_address) == 0) { $event_socket_ip_address = '127.0.0.1'; }

		$fout = fopen($v_conf_dir."/autoload_configs/event_socket.conf.xml","w");
		$tmp_xml = "<configuration name=\"event_socket.conf\" description=\"Socket Client\">\n";
		$tmp_xml .= "  <settings>\n";
		$tmp_xml .= "    <param name=\"listen-ip\" value=\"" . $event_socket_ip_address . "\"/>\n";
		$tmp_xml .= "    <param name=\"listen-port\" value=\"" . $row['event_socket_port'] . "\"/>\n";
		$tmp_xml .= "    <param name=\"password\" value=\"" . $row['event_socket_password'] . "\"/>\n";
		$tmp_xml .= "    <!--<param name=\"apply-inbound-acl\" value=\"lan\"/>-->\n";
		$tmp_xml .= "  </settings>\n";
		$tmp_xml .= "</configuration>";
		fwrite($fout, $tmp_xml);
		unset($tmp_xml, $event_socket_password);
		fclose($fout);

		$fout = fopen($v_conf_dir."/autoload_configs/xml_rpc.conf.xml","w");
		$tmp_xml = "<configuration name=\"xml_rpc.conf\" description=\"XML RPC\">\n";
		$tmp_xml .= "  <settings>\n";
		$tmp_xml .= "    <!-- The port where you want to run the http service (default 8080) -->\n";
		$tmp_xml .= "    <param name=\"http-port\" value=\"" . $row['xml_rpc_http_port'] . "\"/>\n";
		$tmp_xml .= "    <!-- if all 3 of the following params exist all http traffic will require auth -->\n";
		$tmp_xml .= "    <param name=\"auth-realm\" value=\"" . $row['xml_rpc_auth_realm'] . "\"/>\n";
		$tmp_xml .= "    <param name=\"auth-user\" value=\"" . $row['xml_rpc_auth_user'] . "\"/>\n";
		$tmp_xml .= "    <param name=\"auth-pass\" value=\"" . $row['xml_rpc_auth_pass'] . "\"/>\n";
		$tmp_xml .= "  </settings>\n";
		$tmp_xml .= "</configuration>\n";
		fwrite($fout, $tmp_xml);
		unset($tmp_xml);
		fclose($fout);

		//shout.conf.xml
			$fout = fopen($v_conf_dir."/autoload_configs/shout.conf.xml","w");
			$tmp_xml = "<configuration name=\"shout.conf\" description=\"mod shout config\">\n";
			$tmp_xml .= "  <settings>\n";
			$tmp_xml .= "    <!-- Don't change these unless you are insane -->\n";
			$tmp_xml .= "    <param name=\"decoder\" value=\"" . $row['mod_shout_decoder'] . "\"/>\n";
			$tmp_xml .= "    <param name=\"volume\" value=\"" . $row['mod_shout_volume'] . "\"/>\n";
			$tmp_xml .= "    <!--<param name=\"outscale\" value=\"8192\"/>-->\n";
			$tmp_xml .= "  </settings>\n";
			$tmp_xml .= "</configuration>";
			fwrite($fout, $tmp_xml);
			unset($tmp_xml);
			fclose($fout);

		//config.lua
			$fout = fopen($v_scripts_dir."/config.lua","w");
			$tmp = "--lua include\n\n";
			$tmp .= "admin_pin = \"".$row["admin_pin"]."\";\n";
			$tmp .= "sounds_dir = \"".$v_sounds_dir."\";\n";
			$tmp .= "recordings_dir = \"".$v_recordings_dir."\";\n";
			$tmp .= "tmp_dir = \"".$tmp_dir."\";\n";
			fwrite($fout, $tmp);
			unset($tmp);
			fclose($fout);

		//config.js
			$fout = fopen($v_scripts_dir."/config.js","w");
			$tmp = "//javascript include\n\n";
			$tmp .= "var admin_pin = \"".$row["admin_pin"]."\";\n";
			$tmp .= "var sounds_dir = \"".$v_sounds_dir."\";\n";
			$tmp .= "var recordings_dir = \"".$v_recordings_dir."\";\n";
			$tmp .= "var tmp_dir = \"".$tmp_dir."\";\n";
			fwrite($fout, $tmp);
			unset($tmp);
			fclose($fout);
		break; //limit to 1 row
	}
	unset ($prep_statement);

	//apply settings reminder
		$_SESSION["reload_xml"] = true;

	//$cmd = "api reloadxml";
	//event_socket_request_cmd($cmd);
	//unset($cmd);
}


function sync_package_v_dialplan() {
	global $config;
	$v_settings_array = v_settings();
	foreach($v_settings_array as $name => $value) {
		$$name = $value;
	}
}


function sync_package_v_extensions() {
	global $config;
	$v_settings_array = v_settings();
	foreach($v_settings_array as $name => $value) {
		$$name = $value;
	}

	//determine the extensions parent directory
		//$v_extensions_dir = str_replace("\\", "/", $file_contents);
		$v_extensions_dir_array = explode("/", $v_extensions_dir);
		$extension_parent_dir = "";
		$x=1;
		foreach ($v_extensions_dir_array as $tmp_dir) {
			if (count($v_extensions_dir_array) > $x) {
				$extension_parent_dir .= $tmp_dir."/";
			}
			else {
				$extension_dir_name = $tmp_dir; 
			}
			$x++;
		}
		$extension_parent_dir = rtrim($extension_parent_dir, "/");

	// delete all old extensions to prepare for new ones
		if($dh = opendir($v_extensions_dir)) {
			$files = Array();
			while($file = readdir($dh)) {
				if($file != "." && $file != ".." && $file[0] != '.') {
					if(is_dir($dir . "/" . $file)) {
						//this is a directory do nothing
					} else {
						//check if file is an extension; verify the file numeric and the extension is xml
						if (substr($file,0,2) == 'v_' && substr($file,-4) == '.xml') {
							unlink($v_extensions_dir."/".$file);
						}
					}
				}
			}
			closedir($dh);
		}

	global $db, $domain_uuid;
	$sql = "";
	$sql .= "select * from v_extensions ";
	$sql .= "where domain_uuid = '$domain_uuid' ";
	$sql .= "order by callgroup asc ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$i = 0;
	$extension_xml_condensed = false;
	if ($extension_xml_condensed) {
		$fout = fopen($v_extensions_dir."/v_extensions.xml","w");
		$tmp_xml = "<include>\n";
	}
	while($row = $prep_statement->fetch(PDO::FETCH_ASSOC)) {
		$callgroup = $row['callgroup'];
		$callgroup = str_replace(";", ",", $callgroup);
		$tmp_array = explode(",", $callgroup);
		foreach ($tmp_array as &$tmp_callgroup) {
			if (strlen($tmp_callgroup) > 0) {
				if (strlen($callgroups_array[$tmp_callgroup]) == 0) {
					$callgroups_array[$tmp_callgroup] = $row['extension'];
				}
				else {
					$callgroups_array[$tmp_callgroup] = $callgroups_array[$tmp_callgroup].','.$row['extension'];
				}
			}
			$i++;
		}
		$vm_password = $row['vm_password'];
		$vm_password = str_replace("#", "", $vm_password); //preserves leading zeros

		//echo "enabled: ".$row['enabled'];
		if ($row['enabled'] != "false") {
			//remove invalid characters from the file names
			$extension = $row['extension'];
			$extension = str_replace(" ", "_", $extension);
			$extension = preg_replace("/[\*\:\\/\<\>\|\'\"\?]/", "", $extension);

			if (!$extension_xml_condensed) {
				$fout = fopen($v_extensions_dir."/v_".$extension.".xml","w");
				$tmp_xml .= "<include>\n";
			}
			$cidr = '';
			if (strlen($row['cidr']) > 0) {
				$cidr = " cidr=\"" . $row['cidr'] . "\"";
			}
			$number_alias = '';
			if (strlen($row['number_alias']) > 0) {
				$number_alias = " number-alias=\"".$row['number_alias']."\"";
			}
			$tmp_xml .= "  <user id=\"".$row['extension']."\"".$cidr."".$number_alias.">\n";
			$tmp_xml .= "    <params>\n";
			$tmp_xml .= "      <param name=\"password\" value=\"" . $row['password'] . "\"/>\n";
			$tmp_xml .= "      <param name=\"vm-password\" value=\"" . $vm_password . "\"/>\n";
			switch ($row['vm_enabled']) {
			case "true":
				$tmp_xml .= "      <param name=\"vm-enabled\" value=\"true\"/>\n";
				break;
			case "false":
				$tmp_xml .= "      <param name=\"vm-enabled\" value=\"false\"/>\n";
				break;
			default:
				$tmp_xml .= "      <param name=\"vm-enabled\" value=\"true\"/>\n";
			}
			if (strlen($row['vm_mailto']) > 0) {
				$tmp_xml .= "      <param name=\"vm-email-all-messages\" value=\"true\"/>\n";

				switch ($row['vm_attach_file']) {
				case "true":
						$tmp_xml .= "      <param name=\"vm-attach-file\" value=\"true\"/>\n";
						break;
				case "false":
						$tmp_xml .= "      <param name=\"vm-attach-file\" value=\"false\"/>\n";
						break;
				default:
						$tmp_xml .= "      <param name=\"vm-attach-file\" value=\"true\"/>\n";
				}
				switch ($row['vm_keep_local_after_email']) {
				case "true":
						$tmp_xml .= "      <param name=\"vm-keep-local-after-email\" value=\"true\"/>\n";
						break;
				case "false":
						$tmp_xml .= "      <param name=\"vm-keep-local-after-email\" value=\"false\"/>\n";
						break;
				default:
						$tmp_xml .= "      <param name=\"vm-keep-local-after-email\" value=\"true\"/>\n";
				}
				$tmp_xml .= "      <param name=\"vm-mailto\" value=\"" . $row['vm_mailto'] . "\"/>\n";
			}
			if (strlen($row['mwi_account']) > 0) {
				$tmp_xml .= "      <param name=\"MWI-Account\" value=\"" . $row['mwi_account'] . "\"/>\n";
			}
			if (strlen($row['auth-acl']) > 0) {
				$tmp_xml .= "      <param name=\"auth-acl\" value=\"" . $row['auth_acl'] . "\"/>\n";
			}
			$tmp_xml .= "    </params>\n";
			$tmp_xml .= "    <variables>\n";
			if (strlen($row['hold_music']) > 0) {
				$tmp_xml .= "      <variable name=\"hold_music\" value=\"" . $row['hold_music'] . "\"/>\n";
			}
			$tmp_xml .= "      <variable name=\"toll_allow\" value=\"" . $row['toll_allow'] . "\"/>\n";
			if (strlen($v_account_code) > 0) {
				$tmp_xml .= "      <variable name=\"accountcode\" value=\"" . $v_account_code . "\"/>\n";
			}
			else {
				$tmp_xml .= "      <variable name=\"accountcode\" value=\"" . $row['accountcode'] . "\"/>\n";
			}
			$tmp_xml .= "      <variable name=\"user_context\" value=\"" . $row['user_context'] . "\"/>\n";
			if (strlen($row['effective_caller_id_name']) > 0) {
				$tmp_xml .= "      <variable name=\"effective_caller_id_name\" value=\"" . $row['effective_caller_id_name'] . "\"/>\n";
			}
			if (strlen($row['outbound_caller_id_number']) > 0) {
				$tmp_xml .= "      <variable name=\"effective_caller_id_number\" value=\"" . $row['effective_caller_id_number'] . "\"/>\n";
			}
			if (strlen($row['outbound_caller_id_name']) > 0) {
				$tmp_xml .= "      <variable name=\"outbound_caller_id_name\" value=\"" . $row['outbound_caller_id_name'] . "\"/>\n";
			}
			if (strlen($row['outbound_caller_id_number']) > 0) {
				$tmp_xml .= "      <variable name=\"outbound_caller_id_number\" value=\"" . $row['outbound_caller_id_number'] . "\"/>\n";
			}
			if (strlen($row['limit_max']) > 0) {
				$tmp_xml .= "      <variable name=\"limit_max\" value=\"" . $row['limit_max'] . "\"/>\n";
			}
			else {
				$tmp_xml .= "      <variable name=\"limit_max\" value=\"5\"/>\n";
			}
			if (strlen($row['limit_destination']) > 0) {
				$tmp_xml .= "      <variable name=\"limit_destination\" value=\"" . $row['limit_destination'] . "\"/>\n";
			}
			if (strlen($row['sip_force_contact']) > 0) {
				$tmp_xml .= "      <variable name=\"sip-force-contact\" value=\"" . $row['sip_force_contact'] . "\"/>\n";
			}
			if (strlen($row['sip_force_expires']) > 0) {
				$tmp_xml .= "      <variable name=\"sip-force-expires\" value=\"" . $row['sip_force_expires'] . "\"/>\n";
			}
			if (strlen($row['nibble_account']) > 0) {
				$tmp_xml .= "      <variable name=\"nibble_account\" value=\"" . $row['nibble_account'] . "\"/>\n";
			}
			switch ($row['sip_bypass_media']) {
				case "bypass-media":
						$tmp_xml .= "      <variable name=\"bypass_media\" value=\"true\"/>\n";
						break;
				case "bypass-media-after-bridge":
						$tmp_xml .= "      <variable name=\"bypass_media_after_bridge\" value=\"true\"/>\n";
						break;
				case "proxy-media":
						$tmp_xml .= "      <variable name=\"proxy_media\" value=\"true\"/>\n";
						break;
			}

			$tmp_xml .= "    </variables>\n";
			$tmp_xml .= "  </user>\n";

			if (!$extension_xml_condensed) {
				$tmp_xml .= "</include>\n";
				fwrite($fout, $tmp_xml);
				unset($tmp_xml);
				fclose($fout);
			}
		}
	}
	unset ($prep_statement);
	if ($extension_xml_condensed) {
		$tmp_xml .= "</include>\n";
		fwrite($fout, $tmp_xml);
		unset($tmp_xml);
		fclose($fout);
	}

	//define the group members
		$tmp_xml = "<!--\n";
		$tmp_xml .= "	NOTICE NOTICE NOTICE NOTICE NOTICE NOTICE NOTICE NOTICE NOTICE NOTICE\n";
		$tmp_xml .= "\n";
		$tmp_xml .= "	FreeSWITCH works off the concept of users and domains just like email.\n";
		$tmp_xml .= "	You have users that are in domains for example 1000@domain.com.\n";
		$tmp_xml .= "\n";
		$tmp_xml .= "	When freeswitch gets a register packet it looks for the user in the directory\n";
		$tmp_xml .= "	based on the from or to domain in the packet depending on how your sofia profile\n";
		$tmp_xml .= "	is configured.  Out of the box the default domain will be the IP address of the\n";
		$tmp_xml .= "	machine running FreeSWITCH.  This IP can be found by typing \"sofia status\" at the\n";
		$tmp_xml .= "	CLI.  You will register your phones to the IP and not the hostname by default.\n";
		$tmp_xml .= "	If you wish to register using the domain please open vars.xml in the root conf\n";
		$tmp_xml .= "	directory and set the default domain to the hostname you desire.  Then you would\n";
		$tmp_xml .= "	use the domain name in the client instead of the IP address to register\n";
		$tmp_xml .= "	with FreeSWITCH.\n";
		$tmp_xml .= "\n";
		$tmp_xml .= "	NOTICE NOTICE NOTICE NOTICE NOTICE NOTICE NOTICE NOTICE NOTICE NOTICE\n";
		$tmp_xml .= "-->\n";
		$tmp_xml .= "\n";
		$tmp_xml .= "<include>\n";
		$tmp_xml .= "	<!--the domain or ip (the right hand side of the @ in the addr-->\n";
		if ($extension_dir_name == "default") { 
			$tmp_xml .= "	<domain name=\"\$\${domain}\">\n";
		}
		else {
			$tmp_xml .= "	<domain name=\"".$extension_dir_name."\">\n";
		}
		$tmp_xml .= "		<params>\n";
		$tmp_xml .= "			<param name=\"dial-string\" value=\"{sip_invite_domain=\${domain_name},presence_id=\${dialed_user}@\${dialed_domain}}\${sofia_contact(\${dialed_user}@\${dialed_domain})}\"/>\n";
		$tmp_xml .= "		</params>\n";
		$tmp_xml .= "\n";
		$tmp_xml .= "		<variables>\n";
		$tmp_xml .= "			<variable name=\"record_stereo\" value=\"true\"/>\n";
		$tmp_xml .= "			<variable name=\"default_gateway\" value=\"\$\${default_provider}\"/>\n";
		$tmp_xml .= "			<variable name=\"default_areacode\" value=\"\$\${default_areacode}\"/>\n";
		$tmp_xml .= "			<variable name=\"transfer_fallback_extension\" value=\"operator\"/>\n";
		$tmp_xml .= "			<variable name=\"export_vars\" value=\"domain_name\"/>\n";
		$tmp_xml .= "		</variables>\n";
		$tmp_xml .= "\n";
		$tmp_xml .= "		<groups>\n";
		$tmp_xml .= "			<group name=\"".$extension_dir_name."\">\n";
		$tmp_xml .= "			<users>\n";
		$tmp_xml .= "				<X-PRE-PROCESS cmd=\"include\" data=\"".$extension_dir_name."/*.xml\"/>\n";
		$tmp_xml .= "			</users>\n";
		$tmp_xml .= "			</group>\n";
		$tmp_xml .= "\n";
		$previous_callgroup = "";
		foreach ($callgroups_array as $key => $value) {
			$callgroup = $key;
			$extension_list = $value;
			if (strlen($callgroup) > 0) {
				if ($previous_callgroup != $callgroup) {
					$tmp_xml .= "			<group name=\"$callgroup\">\n";
					$tmp_xml .= "				<users>\n";
					$tmp_xml .= "					<!--\n";
					$tmp_xml .= "					type=\"pointer\" is a pointer so you can have the\n";
					$tmp_xml .= "					same user in multiple groups.  It basically means\n";
					$tmp_xml .= "					to keep searching for the user in the directory.\n";
					$tmp_xml .= "					-->\n";
					$extension_array = explode(",", $extension_list);
					foreach ($extension_array as &$tmp_extension) {
						$tmp_xml .= "					<user id=\"$tmp_extension\" type=\"pointer\"/>\n";
					}
					$tmp_xml .= "				</users>\n";
					$tmp_xml .= "			</group>\n";
					$tmp_xml .= "\n";
				}
				$previous_callgroup = $callgroup;
			}
			unset($callgroup);
		}
		$tmp_xml .= "		</groups>\n";
		$tmp_xml .= "\n";
		$tmp_xml .= "	</domain>\n";
		$tmp_xml .= "</include>";

	//remove invalid characters from the file names
		$extension_dir_name = str_replace(" ", "_", $extension_dir_name);
		$extension_dir_name = preg_replace("/[\*\:\\/\<\>\|\'\"\?]/", "", $extension_dir_name);

	//write the xml file
		$fout = fopen($extension_parent_dir."/".$extension_dir_name.".xml","w");
		fwrite($fout, $tmp_xml);
		unset($tmp_xml);
		fclose($fout);

	//syncrhonize the phone directory
		sync_directory();

	//apply settings reminder
		$_SESSION["reload_xml"] = true;

	//$cmd = "api reloadxml";
	//event_socket_request_cmd($cmd);
	//unset($cmd);
}

function filename_safe($filename) {
	// Lower case
	$filename = strtolower($filename);

	// Replace spaces with a '_'
	$filename = str_replace(" ", "_", $filename);

	// Loop through string
	$result = '';
	for ($i=0; $i<strlen($filename); $i++) {
		if (preg_match('([0-9]|[a-z]|_)', $filename[$i])) {
			$result .= $filename[$i];
		}
	}

	// Return filename
	return $result;
}

function sync_package_v_gateways() {

	global $db, $domain_uuid, $config;

	$v_settings_array = v_settings();
	foreach($v_settings_array as $name => $value) {
		$$name = $value;
	}

	// delete all old gateways to prepare for new ones
		if (count($_SESSION["domains"]) > 1) {
			$v_needle = 'v_'.$v_domain.'-';
		}
		else {
			$v_needle = 'v_';
		}
		if($dh = opendir($v_gateways_dir."")) {
			$files = Array();
			while($file = readdir($dh)) {
				if($file != "." && $file != ".." && $file[0] != '.') {
					if(is_dir($dir . "/" . $file)) {
						//this is a directory do nothing
					} else {
						//check if file extension is xml
						if (strpos($file, $v_needle) !== false && substr($file,-4) == '.xml') {
							unlink($v_gateways_dir."/".$file);
						}
					}
				}
			}
			closedir($dh);
		}

	$sql = "";
	$sql .= "select * from v_gateways ";
	$sql .= "where domain_uuid = '$domain_uuid' ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$result = $prep_statement->fetchAll(PDO::FETCH_ASSOC);
	foreach ($result as &$row) {
		if ($row['enabled'] != "false") {
				//remove invalid characters from the file names
					$gateway = $row['gateway'];
					$gateway = str_replace(" ", "_", $gateway);
					$gateway = preg_replace("/[\*\:\\/\<\>\|\'\"\?]/", "", $gateway);
				//set the default profile as external
					$profile = $row['profile'];
					if (strlen($profile) == 0) {
						$profile = "external";
					}
				if (count($_SESSION["domains"]) > 1) {
					$fout = fopen($v_gateways_dir."/".$profile."/v_".$v_domain .'-'.$gateway.".xml","w");
					$tmp_xml .= "<include>\n";
					$tmp_xml .= "    <gateway name=\"" . $v_domain .'-'. $gateway . "\">\n";
				}
				else {
					$fout = fopen($v_gateways_dir."/".$profile."/v_".$gateway.".xml","w");
					$tmp_xml .= "<include>\n";
					$tmp_xml .= "    <gateway name=\"" . $gateway . "\">\n";
				}
				if (strlen($row['username']) > 0) {
					$tmp_xml .= "      <param name=\"username\" value=\"" . $row['username'] . "\"/>\n";
				}
				else {
					$tmp_xml .= "      <param name=\"username\" value=\"register:false\"/>\n";
				}
				if (strlen($row['auth_username']) > 0) {
					$tmp_xml .= "      <param name=\"auth-username\" value=\"" . $row['auth_username'] . "\"/>\n";
				} 
				if (strlen($row['password']) > 0) {
					$tmp_xml .= "      <param name=\"password\" value=\"" . $row['password'] . "\"/>\n";
				}
				else {
					$tmp_xml .= "      <param name=\"password\" value=\"register:false\"/>\n";
				}
				if (strlen($row['realm']) > 0) {
					$tmp_xml .= "      <param name=\"realm\" value=\"" . $row['realm'] . "\"/>\n";
				}
				if (strlen($row['from_user']) > 0) {
					$tmp_xml .= "      <param name=\"from-user\" value=\"" . $row['from_user'] . "\"/>\n";
				}
				if (strlen($row['from_domain']) > 0) {
					$tmp_xml .= "      <param name=\"from-domain\" value=\"" . $row['from_domain'] . "\"/>\n";
				}
				if (strlen($row['proxy']) > 0) {
					$tmp_xml .= "      <param name=\"proxy\" value=\"" . $row['proxy'] . "\"/>\n";
				}
				if (strlen($row['register_proxy']) > 0) {
									$tmp_xml .= "      <param name=\"register-proxy\" value=\"" . $row['register_proxy'] . "\"/>\n";
				}
				if (strlen($row['outbound_proxy']) > 0) {
						$tmp_xml .= "      <param name=\"outbound-proxy\" value=\"" . $row['outbound_proxy'] . "\"/>\n";
				}
				if (strlen($row['expire_seconds']) > 0) {
					$tmp_xml .= "      <param name=\"expire-seconds\" value=\"" . $row['expire_seconds'] . "\"/>\n";
				}
				if (strlen($row['register']) > 0) {
					$tmp_xml .= "      <param name=\"register\" value=\"" . $row['register'] . "\"/>\n";
				}

				if (strlen($row['register_transport']) > 0) {
					switch ($row['register_transport']) {
					case "udp":
						$tmp_xml .= "      <param name=\"register-transport\" value=\"udp\"/>\n";
						break;
					case "tcp":
						$tmp_xml .= "      <param name=\"register-transport\" value=\"tcp\"/>\n";
						break;
					case "tls":
						$tmp_xml .= "      <param name=\"register-transport\" value=\"tls\"/>\n";
						$tmp_xml .= "      <param name=\"contact-params\" value=\"transport=tls\"/>\n";
						break;
					default:
						$tmp_xml .= "      <param name=\"register-transport\" value=\"" . $row['register_transport'] . "\"/>\n";
					}
				  }

				if (strlen($row['retry_seconds']) > 0) {
					$tmp_xml .= "      <param name=\"retry-seconds\" value=\"" . $row['retry_seconds'] . "\"/>\n";
				}
				if (strlen($row['extension']) > 0) {
					$tmp_xml .= "      <param name=\"extension\" value=\"" . $row['extension'] . "\"/>\n";
				}
				if (strlen($row['ping']) > 0) {
					$tmp_xml .= "      <param name=\"ping\" value=\"" . $row['ping'] . "\"/>\n";
				}
				if (strlen($row['context']) > 0) {
					$tmp_xml .= "      <param name=\"context\" value=\"" . $row['context'] . "\"/>\n";
				}
				if (strlen($row['caller_id_in_from']) > 0) {
					$tmp_xml .= "      <param name=\"caller-id-in-from\" value=\"" . $row['caller_id_in_from'] . "\"/>\n";
				}
				if (strlen($row['supress_cng']) > 0) {
					$tmp_xml .= "      <param name=\"supress-cng\" value=\"" . $row['supress_cng'] . "\"/>\n";
				}
				if (strlen($row['sip_cid_type']) > 0) {
					$tmp_xml .= "      <param name=\"sip_cid_type\" value=\"" . $row['sip_cid_type'] . "\"/>\n";
				}
				if (strlen($row['extension_in_contact']) > 0) {
					$tmp_xml .= "      <param name=\"extension-in-contact\" value=\"" . $row['extension_in_contact'] . "\"/>\n";
				}

				$tmp_xml .= "    </gateway>\n";
				$tmp_xml .= "</include>";

				fwrite($fout, $tmp_xml);
				unset($tmp_xml);
				fclose($fout);
		}

	} //end while
	unset($prep_statement);

	//apply settings reminder
		$_SESSION["reload_xml"] = true;

	//$cmd = "api sofia profile external restart reloadxml";
	//event_socket_request_cmd($cmd);
	//unset($cmd);
}


function sync_package_v_modules() {
	global $config, $db, $domain_uuid;
	$v_settings_array = v_settings();
	foreach($v_settings_array as $name => $value) {
		$$name = $value;
	}

	$xml = "";
	$xml .= "<configuration name=\"modules.conf\" description=\"Modules\">\n";
	$xml .= "	<modules>\n";

	$sql = "";
	$sql .= "select * from v_modules ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$prev_module_cat = '';
	$result = $prep_statement->fetchAll(PDO::FETCH_ASSOC);
	foreach ($result as $row) {
		if ($prev_module_cat != $row['module_cat']) {
			$xml .= "\n		<!-- ".$row['module_cat']." -->\n";
		}
		if ($row['module_enabled'] == "true"){
			$xml .= "		<load module=\"".$row['module_name']."\"/>\n"; 
		}
		$prev_module_cat = $row['module_cat'];
	}
	$xml .= "\n";
	$xml .= "	</modules>\n";
	$xml .= "</configuration>";

	$fout = fopen($v_conf_dir."/autoload_configs/modules.conf.xml","w");
	fwrite($fout, $xml);
	unset($xml);
	fclose($fout);

	//apply settings reminder
		$_SESSION["reload_xml"] = true;

	//$cmd = "api reloadxml";
	//event_socket_request_cmd($cmd);
	//unset($cmd);
}

function sync_package_v_vars() {
	global $config, $db, $domain_uuid;
	$v_settings_array = v_settings();
	foreach($v_settings_array as $name => $value) {
		$$name = $value;
	}

	$fout = fopen($v_conf_dir."/vars.xml","w");
	$xml = '';

	$sql = "";
	$sql .= "select * from v_vars ";
	$sql .= "order by var_cat, var_order asc ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$prev_var_cat = '';
	$result = $prep_statement->fetchAll(PDO::FETCH_ASSOC);
	foreach ($result as &$row) {
		//$var_name = $row["var_name"];
		//$var_value = $row["var_value"];
		//$var_cat = $row["var_cat"];
		//$var_order = $row["var_order"];
		//$var_enabled = $row["var_enabled"];
		//$var_desc = $row["var_desc"];

		if ($row['var_cat'] != 'Provision') {
			if ($prev_var_cat != $row['var_cat']) {
				$xml .= "\n<!-- ".$row['var_cat']." -->\n";
				if (strlen($row["var_desc"]) > 0) {
					$xml .= "<!-- ".base64_decode($row['var_desc'])." -->\n";
				}
			}
			if ($row['var_enabled'] == "true"){	$xml .= "<X-PRE-PROCESS cmd=\"set\" data=\"".$row['var_name']."=".$row['var_value']."\"/>\n"; }
		}

		$prev_var_cat = $row['var_cat'];
	}
	$xml .= "\n"; 

	fwrite($fout, $xml);
	unset($xml);
	fclose($fout);

	//apply settings reminder
		$_SESSION["reload_xml"] = true;

	//$cmd = "api reloadxml";
	//event_socket_request_cmd($cmd);
	//unset($cmd);
}

function sync_package_v_public() {
	//global $config;
	//$v_settings_array = v_settings();
	//foreach($v_settings_array as $name => $value) {
	//	$$name = $value;
	//}
}

function outbound_route_to_bridge ($destination_number) {
	global $domain_uuid, $db;

	$destination_number = trim($destination_number);
	if (is_numeric($destination_number)) {
			//not found, continue to process the function
	}
	else {
			//not a number, brige_array and exit the function
			$bridge_array[0] = $destination_number;
			return $bridge_array;
	}

	//get the outbound routes and set as the dialplan array
		$sql = "";
		$sql .= "select * from v_dialplan_includes_details ";
		$sql .= "where domain_uuid = '$domain_uuid' ";
		$sql .= "and (";
		$sql .= "field_data like '%sofia/gateway/%' ";
		$sql .= "or field_data like '%freetdm%' ";
		$sql .= "or field_data like '%openzap%' ";
		$sql .= "or field_data like '%dingaling%' ";
		$sql .= "or field_data like '%enum_auto_route%' ";
		$sql .= ") ";
		$prep_statement = $db->prepare(check_sql($sql));
		$prep_statement->execute();
		$x = 0;
		$result = $prep_statement->fetchAll();
		foreach ($result as &$row) {
			$dialplan_include_uuid = $row["dialplan_include_uuid"];
			//$tag = $row["tag"];
			//$field_order = $row["field_order"];
			//$field_type = $row["field_type"];
			//$field_data = $row["field_data"];
			$dialplan_array[$x]['dialplan_include_uuid'] = $dialplan_include_uuid;
			$x++;
		}
		unset ($prep_statement);

	$sql = "";
	$sql .= "select * from v_dialplan_includes ";
	if (count($dialplan_array) == 0) {
		//when there are no outbound routes do this to hide all remaining entries
		$sql .= " where domain_uuid = '$domain_uuid' ";
		$sql .= " and context = 'hide' ";
	}
	else {
		$x = 0;
		foreach ($dialplan_array as &$row) {
			if ($x == 0) {
				$sql .= " where domain_uuid = '$domain_uuid' \n";
				$sql .= " and dialplan_include_uuid = '".$row['dialplan_include_uuid']."' \n";
				$sql .= "and enabled = 'true' ";
			}
			else {
				$sql .= " or domain_uuid = $domain_uuid \n";
				$sql .= " and dialplan_include_uuid = '".$row['dialplan_include_uuid']."' \n";
				$sql .= "and enabled = 'true' ";
			}
			$x++;
		}
	}
	$sql .= "order by dialplan_order asc ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$result = $prep_statement->fetchAll(PDO::FETCH_ASSOC);
	$x = 0;
	foreach ($result as &$row) {
			$dialplan_include_uuid = $row['dialplan_include_uuid'];
			$tag = $row["tag"];
			$field_type = $row['field_type'];
			$extension_continue = $row['extension_continue'];

			//get the extension number using the dialplan_include_uuid
					$sql = "select * ";
					$sql .= "from v_dialplan_includes_details ";
					$sql .= "where domain_uuid = '$domain_uuid' ";
					$sql .= "and dialplan_include_uuid = '$dialplan_include_uuid' ";
					$sql .= "order by field_order asc ";
					$sub_result = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
					$regex_match = false;
					foreach ($sub_result as &$sub_row) {
							if ($sub_row['tag'] == "condition") {
									if ($sub_row['field_type'] == "destination_number") {
											$field_data = $sub_row['field_data'];
											$pattern = '/'.$field_data.'/';
											preg_match($pattern, $destination_number, $matches, PREG_OFFSET_CAPTURE);
											if (count($matches) == 0) {
													$regex_match = false;
											}
											else {
													$regex_match = true;
													$regex_match_1 = $matches[1][0];
													$regex_match_2 = $matches[2][0];
													$regex_match_3 = $matches[3][0];
													$regex_match_4 = $matches[4][0];
													$regex_match_5 = $matches[5][0];
											}
									}
							}
					}
					if ($regex_match) {
							foreach ($sub_result as &$sub_row) {
									$field_data = $sub_row['field_data'];
									if ($sub_row['tag'] == "action" && $sub_row['field_type'] == "bridge" && $field_data != "\${enum_auto_route}") {
											$field_data = str_replace("\$1", $regex_match_1, $field_data);
											$field_data = str_replace("\$2", $regex_match_2, $field_data);
											$field_data = str_replace("\$3", $regex_match_3, $field_data);
											$field_data = str_replace("\$4", $regex_match_4, $field_data);
											$field_data = str_replace("\$5", $regex_match_5, $field_data);
											//echo "field_data: $field_data";
											$bridge_array[$x] = $field_data;
											$x++;
											if ($extension_continue == "false") {
												break 2;
											}
									}
							}
					}
	}
	return $bridge_array;
	unset ($prep_statement);
}
//$destination_number = '1231234';
//$bridge_array = outbound_route_to_bridge ($destination_number);
//foreach ($bridge_array as &$bridge) {
//	echo "bridge: ".$bridge."<br />";
//}

function extension_exists($extension) {
	global $db, $domain_uuid;
	$sql = "";
	$sql .= "select * from v_extensions ";
	$sql .= "where domain_uuid = '$domain_uuid' ";
	$sql .= "and extension = '$extension' ";
	$sql .= "and enabled = 'true' ";
	$result = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
	if (count($result) > 0) {
		return true;
	}
	else {
		return false;
	}
}

function sync_package_v_hunt_group() {

	//Hunt Group Lua Notes:
		//get the domain
		//loop through all Hunt Groups
			//get the Hunt Group information such as the name and description
			//add each Hunt Group to the dialplan
			//get the list of destinations then build the Hunt Group lua

	global $config;
	$v_settings_array = v_settings();
	foreach($v_settings_array as $name => $value) {
		$$name = $value;
	}

	//get the domain
		global $db, $domain_uuid, $host;
		$v_settings_array = v_settings();
		foreach($v_settings_array as $name => $value) {
			$$name = $value;
		}

		$tmp = "";
		$tmp .= "\n";
		$tmp .= " domain_name = \"".$domain."\"; //by default this is the ipv4 address of FreeSWITCH used for transfer to voicemail\n";
		$tmp .= "\n";
		$tmp .= "\n";

	//prepare for hunt group .lua files to be written. delete all hunt groups that are prefixed with huntgroup_ and have a file extension of .lua
		$v_prefix = 'v_huntgroup_';
		if($dh = opendir($v_scripts_dir)) {
			$files = Array();
			while($file = readdir($dh)) {
				if($file != "." && $file != ".." && $file[0] != '.') {
					if(is_dir($dir . "/" . $file)) {
						//this is a directory
					} else {
						if (substr($file,0, strlen($v_prefix)) == $v_prefix && substr($file,-4) == '.lua') {
							if ($file != "huntgroup_originate.lua") {
								unlink($v_scripts_dir.'/'.$file);
							}
						}
					}
				}
			}
			closedir($dh);
		}

	//loop through all Hunt Groups
		$x = 0;

		$sql = "";
		$sql .= "select * from v_hunt_group ";
		//$sql .= "where domain_uuid = '$domain_uuid' ";
		$prep_statement = $db->prepare(check_sql($sql));
		$prep_statement->execute();
		$result = $prep_statement->fetchAll(PDO::FETCH_ASSOC);
		foreach ($result as &$row) {
				//get the Hunt Group information such as the name and description
					//$row['hunt_group_uuid']
					//$row['hunt_group_extension']
					//$row['hunt_group_name']
					//$row['hunt_group_type']
					//$row['hunt_group_timeout']
					//$row['hunt_group_context']
					//$row['hunt_group_ringback']
					//$row['hunt_group_cid_name_prefix']
					//$row['hunt_group_pin']
					//$row['hunt_group_caller_announce']
					//$row['hunt_group_enabled']
					//$row['hunt_group_descr']
					$domain_uuid = $row['domain_uuid'];

				//add each Hunt Group to the dialplan
					if (strlen($row['hunt_group_uuid']) > 0) {
						$action = 'add'; //set default action to add
						$i = 0;

						$sql = "";
						$sql .= "select * from v_dialplan_includes ";
						$sql .= "where domain_uuid = '$domain_uuid' ";
						$sql .= "and opt_1_name = 'hunt_group_uuid' ";
						$sql .= "and opt_1_value = '".$row['hunt_group_uuid']."' ";
						$prep_statement_2 = $db->prepare($sql);
						$prep_statement_2->execute();
						while($row2 = $prep_statement_2->fetch(PDO::FETCH_ASSOC)) {
							$action = 'update';
							$dialplan_include_uuid = $row2['dialplan_include_uuid'];
							break; //limit to 1 row
						}
						unset ($sql, $prep_statement_2);

						if ($action == 'add') {
							//create huntgroup extension in the dialplan
								$extension_name = check_str($row['hunt_group_name']);
								$dialplan_order ='999';
								$context = $row['hunt_group_context'];
								if ($row['hunt_group_enabled'] == "false") {
									$enabled = 'false';
								}
								else {
									$enabled = 'true';
								}
								$descr = 'huntgroup';
								$opt_1_name = 'hunt_group_uuid';
								$opt_1_value = $row['hunt_group_uuid'];
								$dialplan_include_uuid = v_dialplan_includes_add($domain_uuid, $extension_name, $dialplan_order, $context, $enabled, $descr, $opt_1_name, $opt_1_value);

								$tag = 'condition'; //condition, action, antiaction
								$field_type = 'destination_number';
								$field_data = '^'.$row['hunt_group_extension'].'$';
								$field_order = '000';
								v_dialplan_includes_details_add($domain_uuid, $dialplan_include_uuid, $tag, $field_order, $field_type, $field_data);

								$tag = 'action'; //condition, action, antiaction
								$field_type = 'lua';
								$field_data = 'v_huntgroup_'.$_SESSION['domains'][$domain_uuid]['domain'].'_'.$row['hunt_group_extension'].'.lua';
								$field_order = '001';
								v_dialplan_includes_details_add($domain_uuid, $dialplan_include_uuid, $tag, $field_order, $field_type, $field_data);
						}
						if ($action == 'update') {
							//update the huntgroup
								$extension_name = check_str($row['hunt_group_name']);
								$dialplan_order = '999';
								$context = $row['hunt_group_context'];
								if ($row['hunt_group_enabled'] == "false") {
									$enabled = 'false';
								}
								else {
									$enabled = 'true';
								}
								$descr = 'huntgroup';
								$hunt_group_uuid = $row['hunt_group_uuid'];

								$sql = "";
								$sql = "update v_dialplan_includes set ";
								$sql .= "extension_name = '$extension_name', ";
								$sql .= "dialplan_order = '$dialplan_order', ";
								$sql .= "context = '$context', ";
								$sql .= "enabled = '$enabled', ";
								$sql .= "descr = '$descr' ";
								$sql .= "where domain_uuid = '$domain_uuid' ";
								$sql .= "and opt_1_name = 'hunt_group_uuid' ";
								$sql .= "and opt_1_value = '$hunt_group_uuid' ";
								$db->query($sql);
								unset($sql);

								//update the condition
								$sql = "";
								$sql = "update v_dialplan_includes_details set ";
								$sql .= "field_data = '^".$row['hunt_group_extension']."$' ";
								$sql .= "where domain_uuid = '$domain_uuid' ";
								$sql .= "and tag = 'condition' ";
								$sql .= "and field_type = 'destination_number' ";
								$sql .= "and dialplan_include_uuid = '$dialplan_include_uuid' ";
								$db->query($sql);
								unset($sql);

								//update the action
								$sql = "";
								$sql = "update v_dialplan_includes_details set ";
								$sql .= "field_data = 'v_huntgroup_".$_SESSION['domains'][$domain_uuid]['domain']."_".$row['hunt_group_extension'].".lua', ";
								$sql .= "field_type = 'lua' ";
								$sql .= "where domain_uuid = '$domain_uuid' ";
								$sql .= "and tag = 'action' ";
								$sql .= "and dialplan_include_uuid = '$dialplan_include_uuid' ";
								$db->query($sql);

								unset($extension_name);
								unset($order);
								unset($context);
								unset($enabled);
								unset($descr);
								unset($opt_1_name);
								unset($opt_1_value);
						}
						unset($action);

						//check whether the fifo queue exists already
							$action = 'add'; //set default action to add
							$i = 0;

							$sql = "";
							$sql .= "select * from v_dialplan_includes ";
							$sql .= "where domain_uuid = '$domain_uuid' ";
							$sql .= "and opt_1_name = 'hunt_group_uuid_fifo' ";
							$sql .= "and opt_1_value = '".$row['hunt_group_uuid']."' ";
							$prep_statement_2 = $db->prepare($sql);
							$prep_statement_2->execute();
							while($row2 = $prep_statement_2->fetch(PDO::FETCH_ASSOC)) {
								$dialplan_include_uuid = $row2['dialplan_include_uuid'];
								$action = 'update';
								break; //limit to 1 row
							}
							unset ($sql, $prep_statement_2);

						if ($action == 'add') {
							//create a fifo queue for each huntgroup
							$extension_name = check_str($row['hunt_group_name']).'.park';
							$dialplan_order ='999';
							$context = $row['hunt_group_context'];
							if ($row['hunt_group_enabled'] == "false") {
								$enabled = 'false';
							}
							else {
								$enabled = 'true';
							}
							$descr = 'fifo '.$row['hunt_group_extension'];
							$opt_1_name = 'hunt_group_uuid_fifo';
							$opt_1_value = $row['hunt_group_uuid'];
							$dialplan_include_uuid = v_dialplan_includes_add($domain_uuid, $extension_name, $dialplan_order, $context, $enabled, $descr, $opt_1_name, $opt_1_value);

							$tag = 'condition'; //condition, action, antiaction
							$field_type = 'destination_number';
							$field_data = '^\*'.$row['hunt_group_extension'].'$';
							$field_order = '000';
							v_dialplan_includes_details_add($domain_uuid, $dialplan_include_uuid, $tag, $field_order, $field_type, $field_data);

							$tag = 'action'; //condition, action, antiaction
							$field_type = 'set';
							$field_data = 'fifo_music=$${hold_music}';
							$field_order = '001';
							v_dialplan_includes_details_add($domain_uuid, $dialplan_include_uuid, $tag, $field_order, $field_type, $field_data);

							$hunt_group_timeout_type = $row['hunt_group_timeout_type'];
							$hunt_group_timeout_destination = $row['hunt_group_timeout_destination'];
							if ($hunt_group_timeout_type == "voicemail") { $hunt_group_timeout_destination = '*99'.$hunt_group_timeout_destination; }

							$tag = 'action'; //condition, action, antiaction
							$field_type = 'set';
							$field_data = 'fifo_orbit_exten='.$hunt_group_timeout_destination.':'.$row['hunt_group_timeout'];
							$field_order = '002';
							v_dialplan_includes_details_add($domain_uuid, $dialplan_include_uuid, $tag, $field_order, $field_type, $field_data);

							$tag = 'action'; //condition, action, antiaction
							$field_type = 'fifo';
							$field_data = $row['hunt_group_extension'].'@${domain_name} in';
							$field_order = '003';
							v_dialplan_includes_details_add($domain_uuid, $dialplan_include_uuid, $tag, $field_order, $field_type, $field_data);
						}
						if ($action == 'update') {
							//update the huntgroup fifo
								$extension_name = $row['hunt_group_name'].'.park';
								$dialplan_order = '999';
								$context = $row['hunt_group_context'];
								if ($row['hunt_group_enabled'] == "false") {
									$enabled = 'false';
								}
								else {
									$enabled = 'true';
								}
								$descr = 'fifo '.$row['hunt_group_extension'];
								$hunt_group_uuid = $row['hunt_group_uuid'];

								$sql = "";
								$sql = "update v_dialplan_includes set ";
								$sql .= "extension_name = '$extension_name', ";
								$sql .= "dialplan_order = '$dialplan_order', ";
								$sql .= "context = '$context', ";
								$sql .= "enabled = '$enabled', ";
								$sql .= "descr = '$descr' ";
								$sql .= "where domain_uuid = '$domain_uuid' ";
								$sql .= "and opt_1_name = 'hunt_group_uuid_fifo' ";
								$sql .= "and opt_1_value = '$hunt_group_uuid' ";
								$db->query($sql);
								unset($sql);

								$sql = "";
								$sql = "delete from v_dialplan_includes_details ";
								$sql .= "where domain_uuid = '$domain_uuid' ";
								$sql .= "and dialplan_include_uuid = '$dialplan_include_uuid' ";
								$db->query($sql);
								unset($sql);

								$tag = 'condition'; //condition, action, antiaction
								$field_type = 'destination_number';
								$field_data = '^\*'.$row['hunt_group_extension'].'$';
								$field_order = '000';
								v_dialplan_includes_details_add($domain_uuid, $dialplan_include_uuid, $tag, $field_order, $field_type, $field_data);

								$tag = 'action'; //condition, action, antiaction
								$field_type = 'set';
								$field_data = 'fifo_music=$${hold_music}';
								$field_order = '001';
								v_dialplan_includes_details_add($domain_uuid, $dialplan_include_uuid, $tag, $field_order, $field_type, $field_data);

								$hunt_group_timeout_type = $row['hunt_group_timeout_type'];
								$hunt_group_timeout_destination = $row['hunt_group_timeout_destination'];
								if ($hunt_group_timeout_type == "voicemail") { $hunt_group_timeout_destination = '*99'.$hunt_group_timeout_destination; }

								$tag = 'action'; //condition, action, antiaction
								$field_type = 'set';
								$field_data = 'fifo_orbit_exten='.$hunt_group_timeout_destination.':'.$row['hunt_group_timeout'];
								$field_order = '002';
								v_dialplan_includes_details_add($domain_uuid, $dialplan_include_uuid, $tag, $field_order, $field_type, $field_data);

								$tag = 'action'; //condition, action, antiaction
								$field_type = 'fifo';
								$field_data = $row['hunt_group_extension'].'@${domain_name} in';
								$field_order = '003';
								v_dialplan_includes_details_add($domain_uuid, $dialplan_include_uuid, $tag, $field_order, $field_type, $field_data);
						}

						sync_package_v_dialplan_includes();
						unset($dialplanincludeid);
					} //end if strlen hunt_group_uuid; add the Hunt Group to the dialplan

				//get the list of destinations then build the Hunt Group Lua
					$tmp = "";
					$tmp .= "\n";
					$tmp .= "session:preAnswer();\n";
					$tmp .= "extension = '".$row['hunt_group_extension']."';\n";
					$tmp .= "result = '';\n";
					$tmp .= "timeoutpin = 7500;\n";
					$tmp .= "sip_profile = 'internal';\n";
					$tmp .= "\n";

					$tmp .=	"function extension_registered(domain_name, sip_profile, extension)\n";
					$tmp .=	"	api = freeswitch.API();\n";
					$tmp .=	"	result = api:execute(\"sofia_contact\", sip_profile..\"/\"..extension..\"@\"..domain_name);\n";
					$tmp .=	"	if (result == \"error/user_not_registered\") then\n";
					$tmp .=	"		return false;\n";
					$tmp .=	"	else\n";
					$tmp .=	"		return true;\n";
					$tmp .=	"	end\n";
					$tmp .=	"end\n";
					$tmp .=	"\n";

					$tmp .= "\n";
					$tmp .= "sounds_dir = session:getVariable(\"sounds_dir\");\n";
					$tmp .= "uuid = session:getVariable(\"uuid\");\n";
					$tmp .= "dialed_extension = session:getVariable(\"dialed_extension\");\n";
					$tmp .= "domain_name = session:getVariable(\"domain_name\");\n";
					$tmp .= "caller_id_name = session:getVariable(\"caller_id_name\");\n";
					$tmp .= "caller_id_number = session:getVariable(\"caller_id_number\");\n";
					$tmp .= "effective_caller_id_name = session:getVariable(\"effective_caller_id_name\");\n";
					$tmp .= "effective_caller_id_number = session:getVariable(\"effective_caller_id_number\");\n";
					$tmp .= "outbound_caller_id_name = session:getVariable(\"effective_caller_id_name\");\n";
					$tmp .= "outbound_caller_id_number = session:getVariable(\"effective_caller_id_number\");\n";
					$tmp .= "\n";

					$tmp .= "--set the sounds path for the language, dialect and voice\n";
					$tmp .= "	default_language = session:getVariable(\"default_language\");\n";
					$tmp .= "	default_dialect = session:getVariable(\"default_dialect\");\n";
					$tmp .= "	default_voice = session:getVariable(\"default_voice\");\n";
					$tmp .= "	if (not default_language) then default_language = 'en'; end\n";
					$tmp .= "	if (not default_dialect) then default_dialect = 'us'; end\n";
					$tmp .= "	if (not default_voice) then default_voice = 'callie'; end\n";
					$tmp .= "\n";

					//pin number requested from caller if provided
						if (strlen($row['hunt_group_pin']) > 0) {
							$tmp .= "pin = '".$row['hunt_group_pin']."';\n";
							$tmp .= "digits = session:playAndGetDigits(".strlen($row['hunt_group_pin']).", ".strlen($row['hunt_group_pin']).", 3, 3000, \"#\", sounds_dir..\"/\"..default_language..\"/\"..default_dialect..\"/\"..default_voice..\"/custom/please_enter_the_pin_number.wav\", \"\", \"\\\\d+\");\n";
							$tmp .= "\n";
							$tmp .= "\n";
							$tmp .= "if (digits == pin) then\n";
							$tmp .= "	--continue\n";
							$tmp .= "\n";
						}

					//caller announce requested from caller if provided
						if ($row['hunt_group_caller_announce'] == "true" || $row['hunt_group_call_prompt'] == "true") {
							if ($row['hunt_group_caller_announce'] == "true") {
								$tmp .=	"function originate(domain_name, session, sipuri, extension, caller_id_name, caller_id_number, caller_announce) \n";
							}
							else {
								$tmp .=	"function originate(domain_name, session, sipuri, extension, caller_id_name, caller_id_number) \n";
							}
							$tmp .=	"	--caller_id_name = caller_id_name.replace(\" \", \"..\");\n";
							$tmp .=	"	caller_id_name = string.gsub(caller_id_name, \" \", \"..\");\n";
							//$tmp .=	"	--session:execute(\"luarun\", \"huntgroup_originate.lua \"..domain_name..\" \"..uuid..\" \"..sipuri..\" \"..extension..\" \"..caller_id_name..\" \"..caller_id_number..\" \"..caller_announce);\n";
							$tmp .=	"	api = freeswitch.API();\n";
							if ($row['hunt_group_caller_announce'] == "true") {
								$tmp .=	"	result = api:execute(\"luarun\", \"huntgroup_originate.lua \"..domain_name..\" \"..uuid..\" \"..sipuri..\" \"..extension..\" \"..caller_id_name..\" \"..caller_id_number..\" \"..caller_announce);\n";
							}
							else {
								$tmp .=	"	result = api:execute(\"luarun\", \"huntgroup_originate.lua \"..domain_name..\" \"..uuid..\" \"..sipuri..\" \"..extension..\" \"..caller_id_name..\" \"..caller_id_number..\"\");\n";
							}
							$tmp .=	"end";
							$tmp .=	"\n";

							if ($row['hunt_group_caller_announce'] == "true") {
								$tmp .=	"caller_announce = \"".$tmp_dir."/\"..extension..\"_\"..uuid..\".wav\";\n";
								$tmp .=	"session:streamFile(sounds_dir..\"/\"..default_language..\"/\"..default_dialect..\"/\"..default_voice..\"/custom/please_say_your_name_and_reason_for_calling.wav\");\n";
								$tmp .=	"session:execute(\"gentones\", \"%(1000, 0, 640)\");\n";
								$tmp .=	"session:execute(\"set\", \"playback_terminators=#\");\n";
								$tmp .=	"session:execute(\"record\", caller_announce..\" 180 200\");\n";
							}
							$tmp .=	"\n";
							$tmp .=	"session:setAutoHangup(false);\n";
							$tmp .=	"session:execute(\"transfer\", \"*\"..extension..\" XML ".$_SESSION["context"]."\");\n";
							$tmp .=	"\n";
						}

					//set caller id prefix
						if (strlen($row['hunt_group_cid_name_prefix'])> 0) {
							$tmp .= "if caller_id_name then\n";
							$tmp .= "	session:setVariable(\"caller_id_name\", \"".$row['hunt_group_cid_name_prefix']."\"..caller_id_name);\n";
							$tmp .= "end\n";
							$tmp .= "if effective_caller_id_name then\n";
							$tmp .= "	session:setVariable(\"effective_caller_id_name\", \"".$row['hunt_group_cid_name_prefix']."\"..effective_caller_id_name);\n";
							$tmp .= "elseif caller_id_name then\n";
							$tmp .= "	--effective_caller_id_name missing, set to caller_id_name\n";
							$tmp .= "	session:setVariable(\"effective_caller_id_name\", \"".$row['hunt_group_cid_name_prefix']."\"..caller_id_name);\n";
							$tmp .= "end\n";
							$tmp .= "if outbound_caller_id_name then\n";
							$tmp .= "	session:setVariable(\"outbound_caller_id_name\", \"".$row['hunt_group_cid_name_prefix']."\"..outbound_caller_id_name);\n";
							$tmp .= "end\n";
						}

					//set ring back
						if (isset($row['hunt_group_ringback'])){
							if ($row['hunt_group_ringback'] == "music"){
								$tmp .= "session:execute(\"set\", \"ringback=\${hold_music}\");          --set to music\n";
								$tmp .= "session:execute(\"set\", \"transfer_ringback=\${hold_music}\"); --set to music\n";
							}
							else {
								$tmp .= "session:execute(\"set\", \"ringback=\$\${".$row['hunt_group_ringback']."}\"); --set to ringtone\n";
								$tmp .= "session:execute(\"set\", \"transfer_ringback=\$\${".$row['hunt_group_ringback']."}\"); --set to ringtone\n";
							}
							if ($row['hunt_group_ringback'] == "ring"){
								$tmp .= "session:execute(\"set\", \"ringback=\$\${us-ring}\"); --set to ringtone\n";
								$tmp .= "session:execute(\"set\", \"transfer_ringback=\$\${us-ring}\"); --set to ringtone\n";
							}
						}
						else {
							$tmp .= "session:execute(\"set\", \"ringback=\${hold_music}\");          --set to ringtone\n";
							$tmp .= "session:execute(\"set\", \"transfer_ringback=\${hold_music}\"); --set to ringtone\n";
						}

					if ($row['hunt_group_timeout'] > 0) {
						//$tmp .= "session:setVariable(\"call_timeout\", \"".$row['hunt_group_timeout']."\");\n";
						$tmp .= "session:setVariable(\"continue_on_fail\", \"true\");\n";
						$tmp .= "session:setVariable(\"ignore_early_media\", \"true\");\n";
					}
					$tmp .= "session:setVariable(\"hangup_after_bridge\", \"true\");\n";
					$tmp .= "\n";
					$tmp .= "--freeswitch.consoleLog( \"info\", \"dialed extension:\"..dialed_extension..\"\\n\" );\n";
					$tmp .= "--freeswitch.consoleLog( \"info\", \"domain: \"..domain..\"\\n\" );\n";
					$tmp .= "--freeswitch.consoleLog( \"info\", \"us_ring: \"..us_ring..\"\\n\" );\n";
					$tmp .= "--freeswitch.consoleLog( \"info\", \"domain_name: \"..domain_name..\"\\n\" );\n";
					$tmp .= "\n";

					$tmp .= "--freeswitch.consoleLog( \"info\", \"action call now don't wait for dtmf\\n\" );\n";
					if ($row['hunt_group_caller_announce'] == "true" || $row['hunt_group_call_prompt'] == "true") {
						//do nothing
					}
					else {
						$tmp .= "if session:ready() then\n";
						//$tmp .= "	session.answer();\n";
					}
					$tmp .= "\n";

					$i = 0;
					$sql = "";
					$sql .= "select * from v_hunt_group_destinations ";
					$sql .= "where hunt_group_uuid = '".$row['hunt_group_uuid']."' ";
					$sql .= "and domain_uuid = '$domain_uuid' ";
					//$sql .= "and destination_enabled = 'true' ";
					$sql .= "order by destination_order asc ";
					$prep_statement_2 = $db->prepare($sql);
					$prep_statement_2->execute();
					while($ent = $prep_statement_2->fetch(PDO::FETCH_ASSOC)) {
						//$ent['hunt_group_uuid']
						//$ent['destination_data']
						//$ent['destination_type']
						//$ent['destination_profile']
						//$ent['destination_order']
						//$ent['destination_enabled']
						//$ent['destination_descr']

						$destination_timeout = $ent['destination_timeout'];
						if (strlen($destination_timeout) == 0) {
							if (strlen($row['hunt_group_timeout']) == 0) {
								$destination_timeout = '30';
							}
							else {
								$destination_timeout = $row['hunt_group_timeout'];
							}
						}

						//set the default profile
						if (strlen($ent['destination_data']) == 0) { $ent['destination_data'] = "internal"; }

						if ($ent['destination_type'] == "extension") {
							//$tmp .= "	sofia_contact_".$ent['destination_data']." = \"\${sofia_contact(".$ent['destination_profile']."/".$ent['destination_data']."@\"..domain_name..\")}\";\n";
							$tmp_sub_array["application"] = "bridge";
							$tmp_sub_array["type"] = "extension";
							$tmp_sub_array["extension"] = $ent['destination_data'];
							//$tmp_sub_array["data"] = "\"[leg_timeout=$destination_timeout]\"..sofia_contact_".$ent['destination_data'];
							$tmp_sub_array["data"] = "\"[leg_timeout=$destination_timeout]user/".$ent['destination_data']."@\"..domain_name";
							$tmp_array[$i] = $tmp_sub_array;
							unset($tmp_sub_array);
						}
						if ($ent['destination_type'] == "voicemail") {
							$tmp_sub_array["application"] = "voicemail";
							$tmp_sub_array["type"] = "voicemail";
							$tmp .= "	session:execute(\"voicemail\", \"default \${domain_name} ".$ent['destination_data']."\");\n";
							//$tmp_sub_array["application"] = "voicemail";
							//$tmp_sub_array["data"] = "default \${domain_name} ".$ent['destination_data'];
							//$tmp_array[$i] = $tmp_sub_array;
							unset($tmp_sub_array);
						}
						if ($ent['destination_type'] == "sip uri") {
							$tmp_sub_array["application"] = "bridge";
							$tmp_sub_array["type"] = "sip uri";
							//$destination_data = "{user=foo}loopback/".$ent['destination_data']."/default/XML";
							$bridge_array = outbound_route_to_bridge ($ent['destination_data']);
							$destination_data = $bridge_array[0];
							$tmp_sub_array["application"] = "bridge";
							$tmp_sub_array["data"] = "\"[leg_timeout=$destination_timeout]".$destination_data."\"";
							$tmp_array[$i] = $tmp_sub_array;
							unset($tmp_sub_array);
							unset($destination_data);
						}
						$i++;
					} //end while
					unset ($sql, $prep_statement_2);
					unset($i, $ent);

					$i = 0;
					if(count($tmp_array) > 0) {
						foreach ($tmp_array as $ent) {
							$tmpdata = $ent["data"];
							if ($ent["application"] == "voicemail") { $tmpdata = "*99".$tmpdata; }
							if ($i < 1) {
								$tmp_buffer = $tmpdata;
							}
							else {
								$tmp_buffer .= "..\",\"..".$tmpdata;
							}
							$i++;
						}
					}
					unset($i);
					$tmp_application = $tmp_array[0]["application"];

					if ($row['hunt_group_type'] == "simultaneous" || $row['hunt_group_type'] == "follow_me_simultaneous" || $row['hunt_group_type'] ==  "call_forward") {
						$tmp_switch = "simultaneous";
					}
					if ($row['hunt_group_type'] == "sequence" || $row['hunt_group_type'] == "follow_me_sequence" || $row['hunt_group_type'] ==  "sequentially") {
						$tmp_switch = "sequence";
					}
					switch ($tmp_switch) {
					case "simultaneous":
						if ($row['hunt_group_caller_announce'] == "true" || $row['hunt_group_call_prompt'] == "true") {
							$i = 0;
							if (count($tmp_array) > 0) {
								foreach ($tmp_array as $tmp_row) {
									$tmpdata = $tmp_row["data"];
									if ($tmp_row["application"] == "voicemail") { 
										$tmpdata = "*99".$tmpdata;
									}
									else {
										if ($tmp_row["type"] == "extension") {
											$tmp .= "if (extension_registered(domain_name, sip_profile, '".$tmp_row["extension"]."')) then\n";
											$tmp .= "	";
										}
										if ($row['hunt_group_caller_announce'] == "true") {
											$tmp .= "result = originate (domain_name, session, ".$tmpdata.", extension, caller_id_name, caller_id_number, caller_announce);\n";
										}
										else {
											$tmp .= "result = originate (domain_name, session, ".$tmpdata.", extension, caller_id_name, caller_id_number);\n";
										}
										if ($tmp_row["type"] == "extension") {
											$tmp .= "end\n";
										}
									}
								}
							}
						}
						else {
							$tmp .= "\n";
							if (strlen($tmp_buffer) > 0) {
								$tmp .= "	session:execute(\"".$tmp_application."\", $tmp_buffer);\n";
							}
						}
						break;
					case "sequence":
						$tmp .= "\n";
						$i = 0;
						if (count($tmp_array) > 0) {
							if ($row['hunt_group_caller_announce'] == "true" || $row['hunt_group_call_prompt'] == "true") {
								$i = 0;
								if (count($tmp_array) > 0) {
									foreach ($tmp_array as $tmp_row) {
										$tmpdata = $tmp_row["data"];
										if ($tmp_row["application"] == "voicemail") { 
											$tmpdata = "*99".$tmpdata;
										}
										else {
											if ($tmp_row["type"] == "extension") {
												$tmp .= "if (extension_registered(domain_name, sip_profile, '".$tmp_row["extension"]."')) then\n";
												$tmp .= "	";
											}
											if ($row['hunt_group_caller_announce'] == "true") {
												$tmp .= "result = originate (domain_name, session, ".$tmpdata.", extension, caller_id_name, caller_id_number, caller_announce);\n";
											}
											else {
												$tmp .= "result = originate (domain_name, session, ".$tmpdata.", extension, caller_id_name, caller_id_number);\n";
											}
											if ($tmp_row["type"] == "extension") {
												$tmp .= "end\n";
											}
										}
									}
								}
							}
							else {
								foreach ($tmp_array as $tmp_row) {
									if (strlen($tmp_row["data"]) > 0) {
										$tmp .= "	session:execute(\"".$tmp_application."\", ".$tmp_row["data"].");\n";
									}
								}
							}
							unset($tmp_row);
						}
						break;
					}
					unset($tmp_switch, $tmp_buffer, $tmp_array);

					//set the timeout destination
						$hunt_group_timeout_destination = $row['hunt_group_timeout_destination'];
						if ($row['hunt_group_timeout_type'] == "extension") { $hunt_group_timeout_type = "transfer"; }
						if ($row['hunt_group_timeout_type'] == "voicemail") { $hunt_group_timeout_type = "voicemail"; $hunt_group_timeout_destination = "default \${domain_name} ".$hunt_group_timeout_destination; }
						if ($row['hunt_group_timeout_type'] == "sip uri") { $hunt_group_timeout_type = "bridge"; }
						$tmp .= "\n";
						if ($row['hunt_group_caller_announce'] == "true" || $row['hunt_group_call_prompt'] == "true") {
							//do nothing
						}
						else {
							$tmp .= "	--timeout\n";
							if ($row['hunt_group_type'] != 'dnd') {
								$tmp .= "	originate_disposition = session:getVariable(\"originate_disposition\");\n";
								$tmp .= "	if originate_disposition ~= \"SUCCESS\" then\n";
							}
							$tmp .= "			session:execute(\"".$hunt_group_timeout_type."\", \"".$hunt_group_timeout_destination."\");\n";
							if ($row['hunt_group_type'] != 'dnd') {
								$tmp .= "	end\n";
							}
						}

						if ($row['hunt_group_caller_announce'] == "true" || $row['hunt_group_call_prompt'] == "true") {
							//do nothing
						}
						else {
							$tmp .= "end --end if session:ready\n";
						}
						$tmp .= "\n";
						//pin number requested from caller if provided
						if (strlen($row['hunt_group_pin']) > 0) {
							$tmp .= "else \n";
							$tmp .= "	session:streamFile(sounds_dir..\"/\"..default_language..\"/\"..default_dialect..\"/\"..default_voice..\"/custom/your_pin_number_is_incorect_goodbye.wav\");\n";
							$tmp .= "	session:hangup();\n";
							$tmp .= "end\n";
							$tmp .= "\n";
						}

					//unset variables
						$tmp .= "\n";
						$tmp .= "--clear variables\n";
						$tmp .= "dialed_extension = \"\";\n";
						$tmp .= "new_extension = \"\";\n";
						$tmp .= "domain_name = \"\";\n";
						$tmp .= "\n";

					//remove invalid characters from the file names
						$huntgroup_extension = $row['hunt_group_extension'];
						$huntgroup_extension = str_replace(" ", "_", $huntgroup_extension);
						$huntgroup_extension = preg_replace("/[\*\:\\/\<\>\|\'\"\?]/", "", $huntgroup_extension);

					//write the hungroup lua script
						if (strlen($row['hunt_group_extension']) > 0) {
							if ($row['hunt_group_enabled'] != "false") {
								$hunt_group_filename = "v_huntgroup_".$_SESSION['domains'][$domain_uuid]['domain']."_".$huntgroup_extension.".lua";
								//echo "location".$v_scripts_dir."/".$hunt_group_filename;
								$fout = fopen($v_scripts_dir."/".$hunt_group_filename,"w");
								fwrite($fout, $tmp);
								unset($hunt_group_filename);
								fclose($fout);
							}
						}
		} //end while

	//apply settings reminder
		$_SESSION["reload_xml"] = true;
} //end huntgroup function lua


function sync_package_v_fax() {
	global $domain_uuid, $db;
	$v_settings_array = v_settings();
	foreach($v_settings_array as $name => $value) {
		$$name = $value;
	}

	$sql = "";
	$sql .= "select * from v_fax ";
	$sql .= "where domain_uuid = '$domain_uuid' ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$result = $prep_statement->fetchAll(PDO::FETCH_ASSOC);
	foreach ($result as &$row) {
		//get the fax information such as the name and description
			//$row['fax_uuid']
			//$row['fax_extension']
			//$row['fax_name']
			//$row['fax_email']
			//$row['fax_pin_number']
			//$row['fax_caller_id_name']
			//$row['fax_caller_id_number']
			//$row['fax_description']

		//determine if the entry should be an add, or update to the dialplan 
		if (strlen($row['fax_uuid']) > 0) {
			$action = 'add'; //set default action to add

			$sql = "";
			$sql .= "select * from v_dialplan_includes ";
			$sql .= "where domain_uuid = '$domain_uuid' ";
			$sql .= "and opt_1_name = 'faxid' ";
			$sql .= "and opt_1_value = '".$row['fax_uuid']."' ";
			$prep_statement_2 = $db->prepare($sql);
			$prep_statement_2->execute();
			while($row2 = $prep_statement_2->fetch(PDO::FETCH_ASSOC)) {
				$action = 'update';

				$dialplan_include_uuid = $row2['dialplan_include_uuid'];
				$extension_name = check_str($row2['extension_name']);
				$order = $row2['order'];
				$context = $row2['context'];
				$enabled = $row2['enabled'];
				$descr = check_str($row2['descr']);
				$opt_1_name = $row2['opt_1_name'];
				$opt_1_value = $row2['opt_1_value'];
				$id = $i;

				if (file_exists($v_dialplan_default_dir."/".$order."_".$extension_name.".xml")){
					unlink($v_dialplan_default_dir."/".$order."_".$extension_name.".xml");
				}

				break; //limit to 1 row
			}
			unset ($sql, $prep_statement_2);


			if ($action == 'add') {
				//$faxid = $row['fax_uuid'];
				if (strlen($row['fax_name']) > 0) {

					//create auto attendant extension in the dialplan
					$extension_name = $row['fax_name'];
					$dialplan_order ='999';
					$context = "default";
					$enabled = 'true';
					$descr = $row['fax_description'];
					$opt_1_name = 'faxid';
					$opt_1_value = $row['fax_uuid'];
					$dialplan_include_uuid = v_dialplan_includes_add($domain_uuid, $extension_name, $dialplan_order, $context, $enabled, $descr, $opt_1_name, $opt_1_value);

					//<!-- default ${domain_name} -->
					//<condition field="destination_number" expression="^\*9978$">
					$tag = 'condition'; //condition, action, antiaction
					$field_type = 'destination_number';
					$field_data = '^'.$row['fax_extension'].'$';
					$field_order = '000';
					v_dialplan_includes_details_add($domain_uuid, $dialplan_include_uuid, $tag, $field_order, $field_type, $field_data);

					//<action application="system" data="$v_scripts_dir/emailfax.sh USER DOMAIN $v_storage_dir/fax/inbox/9872/${last_fax}.tif"/>
					$tag = 'action'; //condition, action, antiaction
					$field_type = 'set';
					$field_data = "api_hangup_hook=system ".$php_dir."/".$php_exe." ".$v_secure."/fax_to_email.php ";
					$field_data .= "email=".$row['fax_email']." ";
					$field_data .= "extension=".$row['fax_extension']." ";
					$field_data .= "name=\\\\\\\${last_fax} ";
					$field_data .= "messages='result: \\\\\\\${fax_result_text} sender:\\\\\\\${fax_remote_station_id} pages:\\\\\\\${fax_document_total_pages}' ";
					$field_data .= "domain=".$v_domain." ";
					$field_data .= "caller_id_name='\\\\\\\${caller_id_name}' ";
					$field_data .= "caller_id_number=\\\\\\\${caller_id_number} ";

					$field_order = '005';
					v_dialplan_includes_details_add($domain_uuid, $dialplan_include_uuid, $tag, $field_order, $field_type, $field_data);

					//<action application="answer" />
					$tag = 'action'; //condition, action, antiaction
					$field_type = 'answer';
					$field_data = '';
					$field_order = '010';
					v_dialplan_includes_details_add($domain_uuid, $dialplan_include_uuid, $tag, $field_order, $field_type, $field_data);

					////<action application="set" data="fax_enable_t38=true"/>
					$tag = 'action'; //condition, action, antiaction
					$field_type = 'set';
					$field_data = 'fax_enable_t38=true';
					$field_order = '015';
					v_dialplan_includes_details_add($domain_uuid, $dialplan_include_uuid, $tag, $field_order, $field_type, $field_data);

					////<action application="set" data="fax_enable_t38_request=true"/>
					$tag = 'action'; //condition, action, antiaction
					$field_type = 'set';
					$field_data = 'fax_enable_t38_request=true';
					$field_order = '020';
					v_dialplan_includes_details_add($domain_uuid, $dialplan_include_uuid, $tag, $field_order, $field_type, $field_data);

					//<action application="playback" data="silence_stream://2000"/>
					$tag = 'action'; //condition, action, antiaction
					$field_type = 'playback';
					$field_data = 'silence_stream://2000';
					$field_order = '025';
					v_dialplan_includes_details_add($domain_uuid, $dialplan_include_uuid, $tag, $field_order, $field_type, $field_data);

					//<action application="set" data="last_fax=${caller_id_number}-${strftime(%Y-%m-%d-%H-%M-%S)}"/>
					$tag = 'action'; //condition, action, antiaction
					$field_type = 'set';
					$field_data = 'last_fax=${caller_id_number}-${strftime(%Y-%m-%d-%H-%M-%S)}';
					$field_order = '030';
					v_dialplan_includes_details_add($domain_uuid, $dialplan_include_uuid, $tag, $field_order, $field_type, $field_data);

					//<action application="rxfax" data="$v_storage_dir/fax/inbox/${last_fax}.tif"/>
					$tag = 'action'; //condition, action, antiaction
					$field_type = 'rxfax';
					if (count($_SESSION["domains"]) > 1) {
						$field_data = $v_storage_dir.'/fax/'.$_SESSION['domains'][$row['domain_uuid']]['domain'].'/'.$row['fax_extension'].'/inbox/${last_fax}.tif';
					}
					else {
						$field_data = $v_storage_dir.'/fax/'.$row['fax_extension'].'/inbox/${last_fax}.tif';
					}
					$field_order = '035';
					v_dialplan_includes_details_add($domain_uuid, $dialplan_include_uuid, $tag, $field_order, $field_type, $field_data);

					//<action application="hangup"/>
					$tag = 'action'; //condition, action, antiaction
					$field_type = 'hangup';
					$field_data = '';
					$field_order = '040';
					v_dialplan_includes_details_add($domain_uuid, $dialplan_include_uuid, $tag, $field_order, $field_type, $field_data);
				}
				//unset($fax_uuid);
			}
			if ($action == 'update') {
				$extension_name = $row['fax_name'];
				$dialplan_order = $order;
				$context = $context;
				$enabled = $enabled;
				$descr = $row['fax_description'];

				$sql = "";
				$sql = "update v_dialplan_includes set ";
				$sql .= "extension_name = '$extension_name', ";
				if (strlen($dialplan_order) > 0) {
					$sql .= "dialplan_order = '$dialplan_order', ";
				}
				$sql .= "context = '$context', ";
				$sql .= "enabled = '$enabled', ";
				$sql .= "descr = '$descr' ";
				$sql .= "where domain_uuid = '$domain_uuid' ";
				$sql .= "and dialplan_include_uuid = '$dialplan_include_uuid' ";

				$db->query($sql);
				unset($sql);

				//update the condition
				$sql = "";
				$sql = "update v_dialplan_includes_details set ";
				$sql .= "field_data = '^".$row['fax_extension']."$' ";
				$sql .= "where domain_uuid = '$domain_uuid' ";
				$sql .= "and tag = 'condition' ";
				$sql .= "and field_type = 'destination_number' ";
				$sql .= "and dialplan_include_uuid = '$dialplan_include_uuid' ";
				$db->query($sql);
				unset($sql);

				//update the action
				if (count($_SESSION["domains"]) > 1) {
					$field_data = $v_storage_dir.'/fax/'.$_SESSION['domains'][$row['domain_uuid']]['domain'].'/'.$row['fax_extension'].'/inbox/${last_fax}.tif';
				}
				else {
					$field_data = $v_storage_dir.'/fax/'.$row['fax_extension'].'/inbox/${last_fax}.tif';
				}
				$sql = "";
				$sql = "update v_dialplan_includes_details set ";
				$sql .= "field_data = '".$field_data."' ";
				$sql .= "where domain_uuid = '$domain_uuid' ";
				$sql .= "and tag = 'action' ";
				$sql .= "and field_type = 'rxfax' ";
				$sql .= "and dialplan_include_uuid = '$dialplan_include_uuid' ";
				$db->query($sql);

				//update the action
				$tag = 'action'; //condition, action, antiaction
				$field_type = 'set';
				$field_data = "api_hangup_hook=system ".$php_dir."/".$php_exe." ".$v_secure."/fax_to_email.php ";
				$field_data .= "email=".$row['fax_email']." ";
				$field_data .= "extension=".$row['fax_extension']." ";
				$field_data .= "name=\\\\\\\${last_fax} ";
				$field_data .= "messages='result: \\\\\\\${fax_result_text} sender:\\\\\\\${fax_remote_station_id} pages:\\\\\\\${fax_document_total_pages}' ";
				$field_data .= "domain=".$v_domain." ";
				$field_data .= "caller_id_name='\\\\\\\${caller_id_name}' ";
				$field_data .= "caller_id_number=\\\\\\\${caller_id_number} ";
				$sql = "";
				$sql = "update v_dialplan_includes_details set ";
				$sql .= "field_data = '".check_str($field_data)."' ";
				$sql .= "where domain_uuid = '$domain_uuid' ";
				$sql .= "and tag = 'action' ";
				$sql .= "and field_type = 'set' ";
				$sql .= "and dialplan_include_uuid = '$dialplan_include_uuid' ";
				$sql .= "and field_data like 'api_hangup_hook=%' ";
				$db->query(check_sql($sql));

				unset($extension_name);
				unset($order);
				unset($context);
				unset($enabled);
				unset($descr);
				unset($opt_1_name);
				unset($opt_1_value);
				unset($id);
			}

			sync_package_v_dialplan_includes();
			unset($dialplanincludeid);
		} //end if strlen fax_uuid; add the fax to the dialplan
	} //end if result

	//apply settings reminder
		$_SESSION["reload_xml"] = true;
} //end fax function


function get_recording_filename($id) {
	global $domain_uuid, $db;
	$sql = "";
	$sql .= "select * from v_recordings ";
	$sql .= "where recording_uuid = '$id' ";
	$sql .= "and domain_uuid = '$domain_uuid' ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$result = $prep_statement->fetchAll(PDO::FETCH_ASSOC);
	foreach ($result as &$row) {
		//$filename = $row["filename"];
		//$recording_name = $row["recording_name"];
		//$recording_uuid = $row["recording_uuid"];
		//$descr = $row["descr"];
		return $row["filename"];
		break; //limit to 1 row
	}
	unset ($prep_statement);
}


function sync_package_v_auto_attendant() {
	global $db, $domain_uuid, $host;
	$v_settings_array = v_settings();
	foreach($v_settings_array as $name => $value) {
		$$name = $value;
	}

	$db->beginTransaction();

	//prepare for auto attendant .js files to be written. delete all auto attendants that are prefixed with autoattendant_ and have a file extension of .js
		$v_prefix = 'autoattendant_';
		if($dh = opendir($v_scripts_dir)) {
			$files = Array();
			while($file = readdir($dh)) {
				if($file != "." && $file != ".." && $file[0] != '.') {
					if(is_dir($dir . "/" . $file)) {
						//this is a directory
					} else {
						if (substr($file,0, strlen($v_prefix)) == $v_prefix && substr($file,-3) == '.js') {
							//echo "file: $file<br />\n";
							//echo "extension: ".substr($file,-3)."<br />";
							unlink($v_scripts_dir.'/'.$file);
						}
					}
				}
			}
			closedir($dh);
		}

	//loop through all auto attendants

	$sql = "";
	$sql .= "select * from v_auto_attendant ";
	$sql .= "where domain_uuid = '$domain_uuid' ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$result = $prep_statement->fetchAll(PDO::FETCH_ASSOC);
	foreach ($result as &$row) {
		//add the auto attendant to the dialplan
			if (strlen($row['auto_attendant_id']) > 0) {
					$action = 'add'; //set default action to add

					$sql = "";
					$sql .= "select * from v_dialplan_includes ";
					$sql .= "where domain_uuid = '$domain_uuid' ";
					$sql .= "and opt_1_name = 'auto_attendant_id' ";
					$sql .= "and opt_1_value = '".$row['auto_attendant_id']."' ";
					$prep_statement_2 = $db->prepare($sql);
					$prep_statement_2->execute();
					while($row2 = $prep_statement_2->fetch(PDO::FETCH_ASSOC)) {
						$action = 'update';
						$dialplan_include_uuid = $row2['dialplan_include_uuid'];
						break; //limit to 1 row
					}
					unset ($sql, $prep_statement_2);
			}

		if ($action == 'add') {

			//create auto attendant extension in the dialplan
				$extension_name = $row['aaextension'];
				$dialplan_order ='999';
				$context = $row['aacontext'];
				$enabled = 'true';
				$descr = 'auto attendant';
				$opt_1_name = 'auto_attendant_id';
				$opt_1_value = $row['auto_attendant_id'];
				$dialplan_include_uuid = v_dialplan_includes_add($domain_uuid, $extension_name, $dialplan_order, $context, $enabled, $descr, $opt_1_name, $opt_1_value);

				$tag = 'condition'; //condition, action, antiaction
				$field_type = 'destination_number';
				$field_data = '^'.$row['aaextension'].'$';
				$field_order = '000';
				v_dialplan_includes_details_add($domain_uuid, $dialplan_include_uuid, $tag, $field_order, $field_type, $field_data);

				$tag = 'action'; //condition, action, antiaction
				$field_type = 'javascript';
				$field_data = 'autoattendant_'.$row['aaextension'].'.js';
				$field_order = '001';
				v_dialplan_includes_details_add($domain_uuid, $dialplan_include_uuid, $tag, $field_order, $field_type, $field_data);

		}
		if ($action == 'update') {

				$extension_name = $row['aaextension'];
				$dialplan_order = '999';
				$context = $row['aacontext'];
				$enabled = 'true';
				$descr = 'auto attendant';
				$auto_attendant_id = $row['auto_attendant_id'];

				//update the main dialplan entry
				$sql = "";
				$sql = "update v_dialplan_includes set ";
				$sql .= "extension_name = '$extension_name', ";
				$sql .= "dialplan_order = '$dialplan_order', ";
				$sql .= "context = '$context', ";
				$sql .= "enabled = '$enabled', ";
				$sql .= "descr = '$descr' ";
				$sql .= "where domain_uuid = '$domain_uuid' ";
				$sql .= "and opt_1_name = 'auto_attendant_id' ";
				$sql .= "and opt_1_value = '$auto_attendant_id' ";
				//echo "sql: ".$sql."<br />";
				//exit;
				$db->query($sql);
				unset($sql);

				//update the condition
				$sql = "";
				$sql = "update v_dialplan_includes_details set ";
				$sql .= "field_data = '^".$row['aaextension']."$' ";
				$sql .= "where domain_uuid = '$domain_uuid' ";
				$sql .= "and tag = 'condition' ";
				$sql .= "and field_type = 'destination_number' ";
				$sql .= "and dialplan_include_uuid = '$dialplan_include_uuid' ";
				//echo $sql."<br />";
				$db->query($sql);
				unset($sql);

				//update the action
				$sql = "";
				$sql = "update v_dialplan_includes_details set ";
				$sql .= "field_data = 'autoattendant_".$row['aaextension'].".js' ";
				$sql .= "where domain_uuid = '$domain_uuid' ";
				$sql .= "and tag = 'action' ";
				$sql .= "and field_type = 'javascript' ";
				$sql .= "and dialplan_include_uuid = '$dialplan_include_uuid' ";
				//echo $sql."<br />";
				$db->query($sql);

				unset($sql);
				unset($ent);
				unset($extension_name);
				unset($dialplan_order);
				unset($context);
				unset($enabled);
				unset($descr);
				unset($opt_1_name);
				unset($opt_1_value);
		}

		sync_package_v_dialplan_includes();
		unset($dialplanincludeid);

		// Build the auto attendant javascript
		$recording_action_filename = get_recording_filename($row['recordingidaction']);
		$recording_antiaction_filename = get_recording_filename($row['recordingidantiaction']);

		$sql = "";
		$sql .= "select * from v_settings ";
		$sql .= "where domain_uuid = '$domain_uuid' ";
		$prep_statement_2 = $db->prepare($sql);
		$prep_statement_2->execute();
		while($row2 = $prep_statement_2->fetch(PDO::FETCH_ASSOC)) {
			$event_socket_ip_address = $row2["event_socket_ip_address"];
			$event_socket_port = $row2["event_socket_port"];
			$event_socket_password = $row2["event_socket_password"];
		}
		unset ($prep_statement_2);

		$fp = event_socket_create($event_socket_ip_address, $event_socket_port, $event_socket_password);
		$cmd = "api global_getvar domain";
		$domain = trim(event_socket_request($fp, $cmd));

		$tmp = ""; //make sure the variable starts with no value
		$tmp .= "\n";
		$tmp .= " var condition = true;\n";
		$tmp .= "\n";
		$tmp .= " var domain = \"".$domain."\"; //by default this is the ipv4 address of FreeSWITCH used for transfer to voicemail\n";
		$tmp .= " var digitmaxlength = 0;\n";
		$tmp .= " var objdate = new Date();\n";
		$tmp .= "\n";
		$tmp .= " var adjusthours = 0; //Adjust Server time that is set to GMT 7 hours\n";
		$tmp .= " var adjustoperator = \"-\"; //+ or -\n";
		$tmp .= "\n";
		$tmp .= " if (adjustoperator == \"-\") {\n";
		$tmp .= "   var objdate2 = new Date(objdate.getFullYear(),objdate.getMonth(),objdate.getDate(),(objdate.getHours() - adjusthours),objdate.getMinutes(),objdate.getSeconds());\n";
		$tmp .= " }\n";
		$tmp .= " if (adjustoperator == \"+\") {\n";
		$tmp .= "   var objdate2 = new Date(objdate.getFullYear(),objdate.getMonth(),objdate.getDate(),(objdate.getHours() + adjusthours),objdate.getMinutes(),objdate.getSeconds());\n";
		$tmp .= " }\n";
		$tmp .= "\n";
		$tmp .= " var Hours = objdate2.getHours();\n";
		$tmp .= " var Mins = objdate2.getMinutes();\n";
		$tmp .= " var Seconds = objdate2.getSeconds();\n";
		$tmp .= " var Month = objdate2.getMonth() + 1;\n";
		$tmp .= " var Date = objdate2.getDate();\n";
		$tmp .= " var Year = objdate2.getYear()\n";
		$tmp .= " var Day = objdate2.getDay()+1;\n";
		$tmp .= " var exit = false;\n";
		$tmp .= "\n";
		$tmp .= " dialed_extension = session.getVariable(\"dialed_extension\");\n";
		$tmp .= " domain_name = session.getVariable(\"domain_name\");\n";
		$tmp .= " domain = session.getVariable(\"domain\");\n";
		$tmp .= " us_ring = session.getVariable(\"us-ring\");\n";
		$tmp .= " caller_id_name = session.getVariable(\"caller_id_name\");\n";
		$tmp .= " caller_id_number = session.getVariable(\"caller_id_number\");\n";
		$tmp .= " effective_caller_id_name = session.getVariable(\"effective_caller_id_name\");\n";
		$tmp .= " effective_caller_id_number = session.getVariable(\"effective_caller_id_number\");\n";
		$tmp .= " outbound_caller_id_name = session.getVariable(\"outbound_caller_id_name\");\n";
		$tmp .= " outbound_caller_id_number = session.getVariable(\"outbound_caller_id_number\");\n";
		$tmp .= "\n";

		//set caller id prefix
		if (strlen($row['aacidnameprefix'])> 0) {
			$tmp .= "session.execute(\"set\", \"caller_id_name=".$row['aacidnameprefix']."\"+caller_id_name);\n";
			$tmp .= "session.execute(\"set\", \"effective_caller_id_name=".$row['aacidnameprefix']."\"+effective_caller_id_name);\n";
			$tmp .= "session.execute(\"set\", \"outbound_caller_id_name=".$row['aacidnameprefix']."\"+outbound_caller_id_name);\n";
		}
		$tmp .= "\n";

		$tmp .= "session.execute(\"set\", \"ignore_early_media=true\");\n";
		$tmp .= " session.execute(\"set\", \"hangup_after_bridge=true\");\n";
		$tmp .= " session.execute(\"set\", \"continue_on_fail=true\");\n";
		if (strlen($row['aacalltimeout']) == 0){
			$tmp .= " session.execute(\"set\", \"call_timeout=30\");\n"; //aacalltimeout
			$tmp .= " session.execute(\"export\", \"call_timeout=30\");\n"; //aacalltimeout
		}
		else {
			$tmp .= " session.execute(\"set\", \"call_timeout=".$row['aacalltimeout']."\");\n"; //aacalltimeout
			$tmp .= " session.execute(\"export\", \"call_timeout=".$row['aacalltimeout']."\");\n"; //aacalltimeout
		}

		if (isset($row['aaringback'])){
			if ($row['aaringback'] == "ring"){
				$tmp .= " session.execute(\"set\", \"ringback=\"+us_ring);          //set to ringtone\n";
				$tmp .= " session.execute(\"set\", \"transfer_ringback=\"+us_ring); //set to ringtone\n";
			}
			if ($row['aaringback'] == "music"){
				$tmp .= " session.execute(\"set\", \"ringback=\${hold_music}\");          //set to ringtone\n";
				$tmp .= " session.execute(\"set\", \"transfer_ringback=\${hold_music}\"); //set to ringtone\n";
			}
		}
		else {
			$tmp .= " session.execute(\"set\", \"ringback=\${hold_music}\");          //set to ringtone\n";
			$tmp .= " session.execute(\"set\", \"transfer_ringback=\${hold_music}\"); //set to ringtone\n";
		}
		$tmp .= "\n";
		$tmp .= "//console_log( \"info\", \"Auto Attendant Server Time is: \"+Hours+\":\"+Mins+\" \\n\" );\n";
		$tmp .= "\n";

		$tmp .= " function get_sofia_contact(extension,domain_name, profile){\n";
		$tmp .= "	if (profile == \"auto\") {\n";
		$i = 0;
		foreach (ListFiles($v_conf_dir.'/sip_profiles') as $key=>$sip_profile_file){
			$sip_profile_name = str_replace(".xml", "", $sip_profile_file);
			if ($i == 0) {
			  $tmp .= "			profile = \"".$sip_profile_name."\";\n";
			  $tmp .= "			session.execute(\"set\", \"sofia_contact_\"+extension+\"=\${sofia_contact(\"+profile+\"/\"+extension+\"@\"+domain_name+\")}\");\n";
			  $tmp .= "			sofia_contact = session.getVariable(\"sofia_contact_\"+extension);\n";
			}
			else {
			  $tmp .= "\n";
			  $tmp .= "			if (sofia_contact == \"error/user_not_registered\") {\n";
			  $tmp .= "				profile = \"".$sip_profile_name."\";\n";
			  $tmp .= "				session.execute(\"set\", \"sofia_contact_\"+extension+\"=\${sofia_contact(\"+profile+\"/\"+extension+\"@\"+domain_name+\")}\");\n";
			  $tmp .= "				sofia_contact = session.getVariable(\"sofia_contact_\"+extension);\n";
			  $tmp .= "			}\n";
			}
			$i++;
		}
		unset ($i);
		$tmp .= "	}\n";
		$tmp .= "	else {\n";
		$tmp .= "		session.execute(\"set\", \"sofia_contact_\"+extension+\"=\${sofia_contact(\"+profile+\"/\"+extension+\"@\"+domain_name+\")}\");\n";
		$tmp .= "		sofia_contact = session.getVariable(\"sofia_contact_\"+extension);\n";
		$tmp .= "	}\n";
		$tmp .= "	console_log( \"info\", \"sofia_contact \"+profile+\": \"+sofia_contact+\".\\n\" );\n";
		$tmp .= "	return sofia_contact;\n";
		$tmp .= " }\n";
		$tmp .= "\n";

		$tmp .= " function mycb( session, type, obj, arg ) {\n";
		$tmp .= "    try {\n";
		$tmp .= "        if ( type == \"dtmf\" ) {\n";
		$tmp .= "          console_log( \"info\", \"digit: \"+obj.digit+\"\\n\" );\n";
		$tmp .= "          if ( obj.digit == \"#\" ) {\n";
		$tmp .= "            //console_log( \"info\", \"detected pound sign.\\n\" );\n";
		$tmp .= "            exit = true;\n";
		$tmp .= "            return( false );\n";
		$tmp .= "          }\n";
		$tmp .= "\n";
		$tmp .= "          dtmf.digits += obj.digit;\n";
		$tmp .= "\n";
		$tmp .= "          if ( dtmf.digits.length >= digitmaxlength ) {\n";
		$tmp .= "            exit = true;\n";
		$tmp .= "            return( false );\n";
		$tmp .= "          }\n";
		$tmp .= "        }\n";
		$tmp .= "    } catch (e) {\n";
		$tmp .= "        console_log( \"err\", e+\"\\n\" );\n";
		$tmp .= "    }\n";
		$tmp .= "    return( true );\n";
		$tmp .= " } //end function mycb\n";

		$tmp .= "\n";
		//condition
		$tmp .= $row['aaconditionjs'];
		$tmp .= "\n";
		$tmp .= "\n";

		//$tmp .= " //condition = true; //debugging\n";

		$actiondirect = false;
		$actiondefault = false;
		$actioncount = 0;

		$sql = "";
		$sql .= "select * from v_auto_attendant_options ";
		$sql .= "where domain_uuid = '$domain_uuid' ";
		$sql .= "and auto_attendant_id = '".$row['auto_attendant_id']."' ";
		//echo $sql;
		$prep_statement_2 = $db->prepare($sql);
		$prep_statement_2->execute();
		while($row2 = $prep_statement_2->fetch(PDO::FETCH_ASSOC)) {
			//$auto_attendant_id = $row2["auto_attendant_id"];
			//$optionaction = $row2["optionaction"];
			//$optionnumber = $row2["optionnumber"];
			//$optiontype = $row2["optiontype"];
			//$optionprofile = $row2["optionprofile"];
			//$optiondata = $row2["optiondata"];
			//$optiondescr = $row2["optiondescr"];

			if ($row2['optionaction'] == "action") {
				$actioncount++;
				if (strtolower($row2['optionnumber']) == "n") { //direct the call now don't wait for dtmf
					//echo "now found\n";
					$actiondirect = true;
					$actiondirecttype = $row2['optiontype'];
					$actiondirectprofile = $row2['optionprofile'];
					$actiondirectdest = $row2['optiondata'];
					$actiondirectdesc = $row2['optiondesc'];
				}
				if (strtolower($row2['optionnumber']) == "d") { //default option used when dtmf doesn't match any other option
					//echo "default found\n";
					$actiondefault = true;
					$actiondefaulttype = $row2['optiontype'];
					$actiondefaultprofile = $row2['optionprofile'];
					$actiondefaultdest = $row2['optiondata'];
					$actiondefaultdesc = $row2['optiondesc'];
					$actiondefaultrecording = $row2['optionrecording'];
				}
			}
		} //end while
		unset ($prep_statement_2);

		//$tmp .= "action count: ".$actioncount."<br />\n";
		if ($actioncount > 0) {
			if ($actiondirect) {
				$tmp .= " if (condition) {\n";
				$tmp .= "    //direct\n";
				$tmp .= "    //console_log( \"info\", \"action direct\\n\" );\n";

				//play the option recording if it exists
				if (strlen($row2['optionrecording']) > 0) {
					$option_recording_filename = get_recording_filename($row2['optionrecording']);
					$tmp .= "    session.streamFile( \"".$v_recordings_dir."/".$option_recording_filename."\" );\n";
				}

				$tmp .= "    session.execute(\"".$actiondirecttype."\", \"".$actiondirectdest."\"); //".$actiondirectdesc."\n";

				//if ($actiondirecttype == "extension") {
				//	$tmp .= "    sofia_contact_".$actiondirectdest." = get_sofia_contact(\"".$actiondirectdest."\",domain_name, \"".$actiondirectprofile."\");\n";
				//	$tmp .= "    session.execute(\"bridge\", sofia_contact_".$actiondirectdest."); //".$actiondirectdest."\n";
				//	if ($actiondirectprofile == "auto") {
				//		$tmp .= "    session.execute(\"voicemail\", \"default \${domain} ".$actiondirectdest."\");\n";
				//	}
				//	else {
				//		$tmp .= "    session.execute(\"voicemail\", \"default \${domain} ".$actiondirectdest."\");\n";
				//	}
				//}
				//if ($actiondirecttype == "voicemail") {
				//	if ($actiondirectprofile == "auto") {
				//		$tmp .= "    session.execute(\"voicemail\", \"default \${domain} ".$actiondirectdest."\");\n";
				//	}
				//	else {
				//		$tmp .= "    session.execute(\"voicemail\", \"default \${domain} ".$actiondirectdest."\");\n";
				//	}
				//}
				//if ($actiondirecttype == "sip uri") {
				//	$tmp .= "    session.execute(\"bridge\", \"".$actiondirectdest."\"); //".$actiondirectdest."\n";
				//}
			$tmp .= "}\n";

		}
		else {
			$tmp .= " if (condition) {\n";
			$tmp .= "    //action\n";
			$tmp .= "\n";
			$tmp .= "     //console_log( \"info\", \"action call now don't wait for dtmf\\n\" );\n";
			$tmp .= "      var dtmf = new Object( );\n";
			$tmp .= "     dtmf.digits = \"\";\n";
			$tmp .= "     if ( session.ready( ) ) {\n";
			$tmp .= "         session.answer( );\n";
			$tmp .= "\n";
			$tmp .= "         digitmaxlength = 1;\n";
			$tmp .= "         while (session.ready() && ! exit ) {\n";
			$tmp .= "           session.streamFile( \"".$v_recordings_dir."/".$recording_action_filename."\", mycb, \"dtmf ".$row['aatimeout']."\" );\n";
			$tmp .= "           if (session.ready()) {\n";
			$tmp .= "           	if (dtmf.digits.length == 0) {\n";
			$tmp .= "           		dtmf.digits +=  session.getDigits(1, \"#\", ".($row['aatimeout']*1000)."); // ".$row['aatimeout']." seconds\n";
			$tmp .= "           		if (dtmf.digits.length == 0) {\n";

			//$tmp .= "           			console_log( "info", "time out option: " + dtmf.digits + "\n" );\n";

					//find the timeout auto attendant options with the correct action
					$sql = "";
					$sql .= "select * from v_auto_attendant_options ";
					$sql .= "where auto_attendant_id = '".$row['auto_attendant_id']."' ";
					$sql .= "and domain_uuid = '$domain_uuid' ";
					$prep_statement_2 = $db->prepare($sql);
					$prep_statement_2->execute();
					while($row2 = $prep_statement_2->fetch(PDO::FETCH_ASSOC)) {
						//$auto_attendant_id = $row2["auto_attendant_id"];
						//$optionaction = $row2["optionaction"];
						//$optionnumber = $row2["optionnumber"];
						//$optiontype = $row2["optiontype"];
						//$optiondata = $row2["optiondata"];
						//$optionprofile = $row2["optionprofile"];
						//$optiondescr = $row2["optiondescr"];

						if ($row2['optionaction'] == "action") {
							if (strtolower($row2['optionnumber']) == "t") {

								//play the option recording if it exists
								if (strlen($row2['optionrecording']) > 0) {
									$option_recording_filename = get_recording_filename($row2['optionrecording']);
									$tmp .= "                 	session.streamFile( \"".$v_recordings_dir."/".$option_recording_filename."\" );\n";
								}

								$tmp .= "                 	session.execute(\"".$row2['optiontype']."\", \"".$row2['optiondata']."\"); //".$row2['optiondescr']."\n";

								//if ($row2['optiontype'] == "extension") {
								//	$tmp .= "                 	sofia_contact_".$row2['optiondata']." = get_sofia_contact(\"".$row2['optiondata']."\",domain_name, \"".$row2['optionprofile']."\");\n";
								//	$tmp .= "                 	session.execute(\"bridge\", sofia_contact_".$row2['optiondata']."); //".$row2['optiondescr']."\n";
								//	if ($row2['optionprofile'] == "auto") {
								//		$tmp .= "                 	session.execute(\"voicemail\", \"default \${domain} ".$row2['optiondata']."\");\n";
								//	}
								//	else {
								//		$tmp .= "                 	session.execute(\"voicemail\", \"default \${domain} ".$row2['optiondata']."\");\n";
								//	}
								//}
								//if ($row2['optiontype'] == "voicemail") {
								//	if ($row2['optionprofile'] == "auto") {
								//		$tmp .= "                 	session.execute(\"voicemail\", \"default \${domain} ".$row2['optiondata']."\"); //".$row2['optiondescr']."\n";
								//	}
								//	else {
								//		$tmp .= "                 	session.execute(\"voicemail\", \"default \${domain} ".$row2['optiondata']."\"); //".$row2['optiondescr']."\n";
								//	}
								//}
								//if ($row2['optiontype'] == "sip uri") {
								//	$tmp .= "                 	session.execute(\"bridge\", \"".$row2['optiondata']."\"); //".$row2['optiondescr']."\n";
								//}
							}
						} //end anti-action

					} //end while
					unset ($prep_statement_2);


			$tmp .= "           		}\n";
			$tmp .= "           		else {\n";
			$tmp .= "           			break; //dtmf found end the while loop\n";
			$tmp .= "           		}\n";
			$tmp .= "           	}\n";
			$tmp .= "           }\n";
			$tmp .= "         }\n";
			$tmp .= "\n";
			$tmp .= "         //pickup the remaining digits\n";
			//$tmp .= "         //http://wiki.freeswitch.org/wiki/Session_getDigits\n";
			//$tmp .= "         //getDigits(length, terminators, timeout, digit_timeout, abs_timeout)\n";
			//$tmp .= "       //dtmf.digits +=  session.getDigits(2, \"#\", 3000); //allow up to 3 digits\n";
			$tmp .= "         dtmf.digits +=  session.getDigits(4, \"#\", 3000); //allow up to 5 digits\n";
			$tmp .= "\n";
			$tmp .= "\n";
			//$tmp .= "         console_log( \"info\", \"Auto Attendant Digit Pressed: \" + dtmf.digits + \"\\n\" );\n";


			//action
			$tmpaction = "";

			$tmp .= "         if ( dtmf.digits.length > \"0\" ) {\n\n";
			$x = 0;

			$sql = "";
			$sql .= "select * from v_auto_attendant_options ";
			$sql .= "where auto_attendant_id = '".$row['auto_attendant_id']."' ";
			$sql .= "and domain_uuid = '$domain_uuid' ";
			$prep_statement_2 = $db->prepare($sql);
			$prep_statement_2->execute();
			while($row2 = $prep_statement_2->fetch(PDO::FETCH_ASSOC)) {
				//$auto_attendant_id = $row2["auto_attendant_id"];
				//$optionaction = $row2["optionaction"];
				//$optionnumber = $row2["optionnumber"];
				//$optiontype = $row2["optiontype"];
				//$optiondata = $row2["optiondata"];
				//$optionprofile = $row2["optionprofile"];
				//$optiondescr = $row2["optiondescr"];
				$tmpactiondefault = "";

				if ($row2['optionaction'] == "action") {
					//$tmpaction .= "\n";

					switch ($row2['optionnumber']) {
					//case "t":
					//		break;
					//case "d":
					//		break;
					default:
							//$tmpaction .= "             //console_log( \"info\", \"Auto Attendant Detected 1 digit \\n\" );\n";
							if ($x == 0) {
								$tmpaction .= "             if ( dtmf.digits == \"".$row2['optionnumber']."\" ) { //".$row2['optiondescr']."\n";
							}
							else {
								$tmpaction .= "             else if ( dtmf.digits == \"".$row2['optionnumber']."\" ) { //".$row2['optiondescr']."\n";
							}

							//play the option recording if it was provided 
							if (strlen($row2['optionrecording']) > 0) {
								$option_recording_filename = get_recording_filename($row2['optionrecording']);
								$tmpaction .= "                 session.streamFile( \"".$v_recordings_dir."/".$option_recording_filename."\" );\n";
							}

							$tmpaction .= "                 session.execute(\"".$row2['optiontype']."\", \"".$row2['optiondata']."\"); //".$row2['optiondescr']."\n";

							//if ($row2['optiontype'] == "extension") {
							//	$tmpaction .= "                 sofia_contact_".$row2['optiondata']." = get_sofia_contact(\"".$row2['optiondata']."\",domain_name, \"".$row2['optionprofile']."\");\n";
							//	$tmpaction .= "                 session.execute(\"bridge\", sofia_contact_".$row2['optiondata']."); //".$row2['optiondescr']."\n";
							//	if ($row2['optionprofile'] == "auto") {
							//		$tmpaction .= "                 session.execute(\"voicemail\", \"default \${domain} ".$row2['optiondata']."\");\n";
							//	}
							//	else {
							//		$tmpaction .= "                 session.execute(\"voicemail\", \"default \${domain} ".$row2['optiondata']."\"); //".$row2['optiondescr']."\n";
							//	}
							//}
							//if ($row2['optiontype'] == "voicemail") {
							//	if ($row2['optionprofile'] == "auto") {
							//		$tmpaction .= "                 session.execute(\"voicemail\", \"default \${domain} ".$row2['optiondata']."\"); //".$row2['optiondescr']."\n";
							//	}
							//	else {
							//		$tmpaction .= "                 session.execute(\"voicemail\", \"default \${domain} ".$row2['optiondata']."\"); //".$row2['optiondescr']."\n";
							//	}
							//}
							//if ($row2['optiontype'] == "sip uri") {
							//	$tmpaction .= "                 session.execute(\"bridge\", \"".$row2['optiondata']."\"); //".$row2['optiondescr']."\n";
							//}

							$tmpaction .= "             }\n";
					}

					$x++;
				} //end auto_attendant_id

			} //end while
			unset ($prep_statement_2);

			$tmp .= $tmpaction;
			if ($row['aadirectdial'] == "true") {
				$tmp .= "             else {\n";
				$tmp .= "	                  session.execute(\"transfer\", dtmf.digits+\" XML ".$_SESSION["context"]."\");\n";
				//$tmp .= $tmpactiondefault;
				$tmp .= "             }\n";
			}
			else {
				if ($actiondefault) {
					$tmp .= "             else {\n";
					$tmp .= "	                  //console_log( \"info\", \"default option when there is no matching dtmf found\\n\" );\n";

					//play the option recording if it exists
					if (strlen($actiondefaultrecording) > 0) {
						$option_recording_filename = get_recording_filename($actiondefaultrecording);
						$tmp .= "	                  session.streamFile( \"".$v_recordings_dir."/".$option_recording_filename."\" );\n";
					}

					$tmp .= "	                  session.execute(\"".$actiondefaulttype."\", \"".$actiondefaultdest."\"); //".$actiondefaultdesc."\n";

					//if ($actiondefaulttype == "extension") {
					//	$tmp .= "	                  sofia_contact_".$actiondefaultdest." = get_sofia_contact(\"".$actiondefaultdest."\",domain_name, \"".$actiondefaultprofile."\");\n";
					//	$tmp .= "	                  session.execute(\"bridge\", sofia_contact_".$actiondefaultdest."); //".$actiondefaultdest."\n";
					//	if ($actiondirectprofile == "auto") {
					//		$tmp .= "	                  session.execute(\"voicemail\", \"default \${domain} ".$actiondefaultdest."\");\n";
					//	}
					//	else {
					//		$tmp .= "	                  session.execute(\"voicemail\", \"default \${domain} ".$actiondefaultdest."\");\n";
					//	}
					//}
					//if ($actiondefaulttype == "voicemail") {
					//	if ($actiondirectprofile == "auto") {
					//		$tmp .= "	                  session.execute(\"voicemail\", \"default \${domain} ".$actiondefaultdest."\");\n";
					//	}
					//	else {
					//		$tmp .= "	                  session.execute(\"voicemail\", \"default \${domain} ".$actiondefaultdest."\");\n";
					//	}
					//}
					//if ($actiondefaulttype == "sip uri") {
					//	$tmp .= "	                  session.execute(\"bridge\", \"".$actiondefaultdest."\"); //".$actiondefaultdest."\n";
					//}

					$tmp .= "             }\n";

				}
			}

			$tmp .= "\n";
			unset($tmpaction);


			$tmp .= "          } \n";
			//$tmp .= "          else if ( dtmf.digits.length == \"4\" ) {\n";
			//$tmp .= "	                  //Transfer to the extension the caller\n";
			//$tmp .= "	                  session.execute(\"transfer\", dtmf.digits+\" XML ".$_SESSION["context"]."\");\n";
			//$tmp .= "          } else {\n";
			//$tmp .= $tmpactiondefault;
			//$tmp .= "          }\n";
			$tmp .= "\n";
			$tmp .= "     } //end if session.ready\n";
			$tmp .= "\n";
			$tmp .= " }\n"; //end if condition

		   }	//if ($actiondirect)
		} //actioncount

		$antiactiondirect = false;
		$antiactiondefault = false;
		$antiactioncount = 0;

		$sql = "";
		$sql .= "select * from v_auto_attendant_options ";
		$sql .= "where auto_attendant_id = '".$row['auto_attendant_id']."' ";
		$sql .= "and domain_uuid = '$domain_uuid' ";
		$prep_statement_2 = $db->prepare($sql);
		$prep_statement_2->execute();
		while($row2 = $prep_statement_2->fetch(PDO::FETCH_ASSOC)) {
			//$auto_attendant_id = $row2["auto_attendant_id"];
			//$optionaction = $row2["optionaction"];
			//$optionnumber = $row2["optionnumber"];
			//$optiontype = $row2["optiontype"];
			//$optionprofile = $row2["optionprofile"];
			//$optiondata = $row2["optiondata"];
			//$optiondescr = $row2["optiondescr"];

			if ($row2['optionaction'] == "anti-action") {
				$antiactioncount++;
				if (strtolower($row2['optionnumber']) == "n") { //direct the call now don't wait for dtmf
					$antiactiondirect = true;
					$antiactiondirecttype = $row2['optiontype'];
					$antiactiondirectdest = $row2['optiondata'];
					$antiactiondirectdesc = $row2['optiondesc'];
					$antiactiondirectprofile = $row2['optionprofile'];
				}
				if (strtolower($row2['optionnumber']) == "d") { //default option used when an dtmf doesn't match any option
					$antiactiondefault = true;
					$antiactiondefaulttype = $row2['optiontype'];
					$antiactiondefaultdest = $row2['optiondata'];
					$antiactiondefaultdesc = $row2['optiondesc'];
					$antiactiondefaultrecording = $row2['optionrecording'];
					$antiactiondefaultprofile = $row2['optionprofile'];
				}
			}
		} //end while
		unset ($prep_statement_2);
		//$tmp .= "anti-action count: ".$antiactioncount."<br />\n";

		if ($antiactioncount > 0) {
		  if ($antiactiondirect) {
			$tmp .= " else {\n";
			$tmp .= "     //console_log( \"info\", \"anti-action call now don't wait for dtmf\\n\" );\n";

			$tmp .= "     session.execute(\"".$antiactiondirecttype."\", \"".$antiactiondirectdest."\"); //".$antiactiondefaultdesc."\n";

			//if ($antiactiondirecttype == "extension") {
			//	$tmp .= "    sofia_contact_".$antiactiondirectdest." = get_sofia_contact(\"".$antiactiondirectdest."\",domain_name, \"".$antiactiondirectprofile."\");\n";
			//	$tmp .= "    session.execute(\"bridge\", sofia_contact_".$antiactiondirectdest."); //".$antiactiondirectdest."\n";
			//	if ($antiactiondirectprofile == "auto") {
			//		$tmp .= "    session.execute(\"voicemail\", \"default \${domain} ".$antiactiondirectdest."\");\n";
			//	}
			//	else {
			//		$tmp .= "    session.execute(\"voicemail\", \"default \${domain} ".$antiactiondirectdest."\");\n";
			//	}
			//}
			//if ($antiactiondirecttype == "voicemail") {
			//	if ($antiactiondirectprofile == "auto") {
			//		$tmp .= "    session.execute(\"voicemail\", \"default \${domain} ".$antiactiondirectdest."\");\n";
			//	}
			//	else {
			//		$tmp .= "    session.execute(\"voicemail\", \"default \${domain} ".$antiactiondirectdest."\");\n";
			//	}
			//}
			//if ($antiactiondirecttype == "sip uri") {
			//	$tmp .= "    session.execute(\"bridge\", \"".$antiactiondirectdest."\"); //".$antiactiondirectdest."\n";
			//}
			$tmp .= "}\n";
		}
		else {
			$tmp .= " else {\n";
			$tmp .= "     //anti-action\n";
			$tmp .= "     //console_log( \"info\", \"anti-action options\\n\" );\n";
			$tmp .= "\n";
			$tmp .= "     var dtmf = new Object( );\n";
			$tmp .= "     dtmf.digits = \"\";\n";
			$tmp .= "     if ( session.ready( ) ) {\n";
			$tmp .= "         session.answer( );\n";
			$tmp .= "\n";
			$tmp .= "         digitmaxlength = 1;\n";
			$tmp .= "         while (session.ready() && ! exit ) {\n";
			$tmp .= "           session.streamFile( \"".$v_recordings_dir."/".$recording_antiaction_filename."\", mycb, \"dtmf ".$row['aatimeout']."\" );\n";
			$tmp .= "           if (session.ready()) {\n";
			$tmp .= "           	if (dtmf.digits.length == 0) {\n";
			$tmp .= "           		dtmf.digits +=  session.getDigits(1, \"#\", ".($row['aatimeout']*1000)."); // ".$row['aatimeout']." seconds\n";
			$tmp .= "           		if (dtmf.digits.length == 0) {\n";
			//$tmp .= "           			console_log( "info", "time out option: " + dtmf.digits + "\n" );\n";

			//find the timeout auto attendant options with the correct action
				$sql = "";
				$sql .= "select * from v_auto_attendant_options ";
				$sql .= "where auto_attendant_id = '".$row['auto_attendant_id']."' ";
				$sql .= "and domain_uuid = '$domain_uuid' ";
				$prep_statement_2 = $db->prepare($sql);
				$prep_statement_2->execute();
				while($row2 = $prep_statement_2->fetch(PDO::FETCH_ASSOC)) {
					$auto_attendant_id = $row2["auto_attendant_id"];
					$optionaction = $row2["optionaction"];
					$optionnumber = $row2["optionnumber"];
					$optiontype = $row2["optiontype"];
					$optionprofile = $row2["optionprofile"];
					$optiondata = $row2["optiondata"];
					$optiondescr = $row2["optiondescr"];

					if ($row2['optionaction'] == "anti-action") {
						 if (strtolower($row2['optionnumber']) == "t") {

							//play the option recording if it exists
							if (strlen($row2['optionrecording']) > 0) {
								$option_recording_filename = get_recording_filename($row2['optionrecording']);
								$tmp .= "                 	session.streamFile( \"".$v_recordings_dir."/".$option_recording_filename."\" );\n";
							}

							$tmp .= "                 	session.execute(\"".$row2['optiontype']."\", \"".$row2['optiondata']."\"); //".$row2['optiondescr']."\n";

							//if ($row2['optiontype'] == "extension") {
							//	$tmp .= "                 	sofia_contact_".$row2['optiondata']." = get_sofia_contact(\"".$row2['optiondata']."\",domain_name, \"".$row2['optionprofile']."\");\n";
							//	$tmp .= "                 	session.execute(\"bridge\", sofia_contact_".$row2['optiondata']."); //".$row2['optiondescr']."\n";
							//	if ($row2['optionprofile'] == "auto") {
							//		$tmp .= "                 	session.execute(\"voicemail\", \"default \${domain} ".$row2['optiondata']."\");\n";
							//	}
							//	else {
							//		$tmp .= "                 	session.execute(\"voicemail\", \"default \${domain} ".$row2['optiondata']."\");\n";
							//	}
							//}
							//if ($row2['optiontype'] == "voicemail") {
							//	if ($row2['optionprofile'] == "auto") {
							//		$tmp .= "                 	session.execute(\"voicemail\", \"default \${domain} ".$row2['optiondata']."\"); //".$row2['optiondescr']."\n";
							//	}
							//	else {
							//		$tmp .= "                 	session.execute(\"voicemail\", \"default \${domain} ".$row2['optiondata']."\"); //".$row2['optiondescr']."\n";
							//	}
							//}
							//if ($row2['optiontype'] == "sip uri") {
							//	$tmp .= "                 	session.execute(\"bridge\", \"".$row2['optiondata']."\"); //".$row2['optiondescr']."\n";
							//}
						 }

					} //end anti-action

				} //end while
				unset ($prep_statement_2);

			$tmp .= "           		}\n";
			$tmp .= "           		else {\n";
			$tmp .= "           			break; //dtmf found end the while loop\n";
			$tmp .= "           		}\n";
			$tmp .= "           	}\n";
			$tmp .= "           }\n";
			$tmp .= "         }\n";
			$tmp .= "\n";
			$tmp .= "         //pickup the remaining digits\n";
			$tmp .= "         //http://wiki.freeswitch.org/wiki/Session_getDigits\n";
			$tmp .= "         //getDigits(length, terminators, timeout, digit_timeout, abs_timeout)\n";
			$tmp .= "         dtmf.digits +=  session.getDigits(4, \"#\", 3000);\n";
			$tmp .= "\n";
			$tmp .= "         console_log( \"info\", \"Auto Attendant Digit Pressed: \" + dtmf.digits + \"\\n\" );\n";
			$tmp .= "\n";

			$tmpantiaction = "";
			$tmp .= "         if ( dtmf.digits.length > \"0\" ) {\n\n";

			$x = 0;
			$sql = "";
			$sql .= "select * from v_auto_attendant_options ";
			$sql .= "where auto_attendant_id = '".$row['auto_attendant_id']."' ";
			$sql .= "and domain_uuid = '$domain_uuid' ";
			$prep_statement_2 = $db->prepare($sql);
			$prep_statement_2->execute();
			while($row2 = $prep_statement_2->fetch(PDO::FETCH_ASSOC)) {
				$auto_attendant_id = $row2["auto_attendant_id"];
				$optionaction = $row2["optionaction"];
				$optionnumber = $row2["optionnumber"];
				$optiontype = $row2["optiontype"];
				$optionprofile = $row2["optionprofile"];
				$optiondata = $row2["optiondata"];
				$optiondescr = $row2["optiondescr"];

				//find the correct auto attendant options with the correct action
					if ($row2['optionaction'] == "anti-action") {
						switch ($row2['optionnumber']) {
						//case "t":
						//		//break;
						//case "d":
						//		//break;
						default:
								//$tmpantiaction .= "             //console_log( \"info\", \"Auto Attendant Detected 1 digit \\n\" );\n";

								if ($x == 0) {
									$tmpantiaction .= "             if ( dtmf.digits == \"".$row2['optionnumber']."\" ) { //".$row2['optiondescr']."\n";
								}
								else {
									$tmpantiaction .= "             else if ( dtmf.digits == \"".$row2['optionnumber']."\" ) { //".$row2['optiondescr']."\n";
								}

								//play the option recording if it was provided 
								if (strlen($row2['optionrecording']) > 0) {
									$option_recording_filename = get_recording_filename($row2['optionrecording']);
									$tmpantiaction .= "             session.streamFile( \"".$v_recordings_dir."/".$option_recording_filename."\" );\n\n";
								}

								$tmpantiaction .= "                 session.execute(\"".$row2['optiontype']."\", \"".$row2['optiondata']."\"); //".$row2['optiondescr']."\n";

								//if ($row2['optiontype'] == "extension") {
								//	$tmpantiaction .= "                 sofia_contact_".$row2['optiondata']." = get_sofia_contact(\"".$row2['optiondata']."\",domain_name, \"".$row2['optionprofile']."\");\n";
								//	$tmpantiaction .= "                 session.execute(\"bridge\", sofia_contact_".$row2['optiondata']."); //".$row2['optiondescr']."\n";
								//	if ($row2['optionprofile'] == "auto") {
								//		$tmpantiaction .= "                 session.execute(\"voicemail\", \"default \${domain} ".$row2['optiondata']."\");\n";
								//	}
								//	else {
								//		$tmpantiaction .= "                 session.execute(\"voicemail\", \"default \${domain} ".$row2['optiondata']."\");\n";
								//	}
								//}
								//if ($row2['optiontype'] == "voicemail") {
								//	if ($row2['optionprofile'] == "auto") {
								//		$tmpantiaction .= "                 session.execute(\"voicemail\", \"default \${domain} ".$row2['optiondata']."\"); //".$row2['optiondescr']."\n";
								//	}
								//	else {
								//		$tmpantiaction .= "                 session.execute(\"voicemail\", \"default \${domain} ".$row2['optiondata']."\"); //".$row2['optiondescr']."\n";
								//	}
								//}
								//if ($row2['optiontype'] == "sip uri") {
								//	$tmpantiaction .= "                 session.execute(\"bridge\", \"".$row2['optiondata']."\"); //".$row2['optiondescr']."\n";
								//}

								$tmpantiaction .= "             }\n";

						} //end switch

						  $x++;
					} //end anti-action

			} //end while
			unset ($prep_statement_2);

			$tmp .= $tmpantiaction;
			if ($row['aadirectdial'] == "true") {
				$tmp .= "             else {\n";
				$tmp .= "	                  session.execute(\"transfer\", dtmf.digits+\" XML ".$_SESSION["context"]."\");\n";	
				//$tmp .= $tmpantiactiondefault;
				$tmp .= "             }\n";
			}
			else {
				if ($antiactiondefault) {
					$tmp .= "             else {\n";
					$tmp .= "	                  //console_log( \"info\", \"default option used when dtmf doesn't match any other option\\n\" );\n";

					//play the option recording if it exists
					if (strlen($antiactiondefaultrecording) > 0) {
						$option_recording_filename = get_recording_filename($antiactiondefaultrecording);
						$tmp .= "	                  session.streamFile( \"".$v_recordings_dir."/".$option_recording_filename."\" );\n";
					}

					$tmp .= "	                  session.execute(\"".$antiactiondefaulttype."\", \"".$antiactiondefaultdest."\"); //".$antiactiondefaultdesc."\n";

					//if ($antiactiondefaulttype == "extension") {
					//	$tmp .= "	                  sofia_contact_".$antiactiondefaultdest." = get_sofia_contact(\"".$antiactiondefaultdest."\",domain_name, \"".$actiondirectprofile."\");\n";
					//	$tmp .= "	                  session.execute(\"bridge\", sofia_contact_".$antiactiondefaultdest."); //".$antiactiondefaultdest."\n";
					//	if ($actiondirectprofile == "auto") {
					//		$tmp .= "	                  session.execute(\"voicemail\", \"default \${domain} ".$antiactiondefaultdest."\");\n";
					//	}
					//	else {
					//		$tmp .= "	                  session.execute(\"voicemail\", \"default \${domain} ".$antiactiondefaultdest."\");\n";
					//	}
					//}
					//if ($antiactiondefaulttype == "voicemail") {
					//	if ($actiondirectprofile == "auto") {
					//		$tmp .= "	                  session.execute(\"voicemail\", \"default \${domain} ".$antiactiondefaultdest."\");\n";
					//	}
					//	else {
					//		$tmp .= "	                  session.execute(\"voicemail\", \"default \${domain} ".$antiactiondefaultdest."\");\n";
					//	}
					//}
					//if ($antiactiondefaulttype == "sip uri") {
					//	$tmp .= "	                  session.execute(\"bridge\", \"".$antiactiondefaultdest."\"); //".$antiactiondefaultdest."\n";
					//}

					$tmp .= "             }\n";
				}
			}
			$tmp .= "\n";
			unset($tmpantiaction);

			$tmp .= "          } \n";
			//$tmp .= "          else if ( dtmf.digits.length == \"3\" ) {\n";
			//$tmp .= "                //Transfer to the extension the caller chose\n";
			//$tmp .= "                session.execute(\"transfer\", dtmf.digits+\" XML ".$_SESSION["context"]."\"); \n";
			//$tmp .= "          }\n";
			//$tmp .= "          else {\n";
			//$tmp .= $tmpantiactiondefault;
			//$tmp .= "          }\n";
			$tmp .= "\n";
			$tmp .= "     } //end if session.ready\n";
			$tmp .= "\n";
			$tmp .=  " } //end if condition";
		
		   }	//if ($antiactiondirect)
		} //antiactioncount
		unset($tmpactiondefault);
		unset($tmpantiactiondefault);

		if (strlen($row['aaextension']) > 0) {
			$aafilename = "autoattendant_".$row['aaextension'].".js";
			$fout = fopen($v_scripts_dir."/".$aafilename,"w");
			fwrite($fout, $tmp);
			unset($aafilename);
			fclose($fout);
		}

	} //end while
	$db->commit();

	//apply settings reminder
		$_SESSION["reload_xml"] = true;
} //end auto attendant function


function v_dialplan_includes_add($domain_uuid, $extension_name, $dialplan_order, $context, $enabled, $descr, $opt_1_name, $opt_1_value) {
	global $db, $db_type;
	$sql = "insert into v_dialplan_includes ";
	$sql .= "(";
	$sql .= "domain_uuid, ";
	$sql .= "extension_name, ";
	$sql .= "dialplan_order, ";
	$sql .= "context, ";
	$sql .= "enabled, ";
	$sql .= "descr, ";
	$sql .= "opt_1_name, ";
	$sql .= "opt_1_value ";
	$sql .= ")";
	$sql .= "values ";
	$sql .= "(";
	$sql .= "'$domain_uuid', ";
	$sql .= "'$extension_name', ";
	$sql .= "'$dialplan_order', ";
	$sql .= "'$context', ";
	$sql .= "'$enabled', ";
	$sql .= "'$descr', ";
	$sql .= "'$opt_1_name', ";
	$sql .= "'$opt_1_value' ";
	$sql .= ")";
	if ($db_type == "sqlite" || $db_type == "mysql" ) {
		$db->exec(check_sql($sql));
		$dialplan_include_uuid = $db->lastInsertId($id);
	}
	if ($db_type == "pgsql") {
		$sql .= " RETURNING dialplan_include_uuid ";
		$prep_statement = $db->prepare(check_sql($sql));
		$prep_statement->execute();
		$result = $prep_statement->fetchAll(PDO::FETCH_ASSOC);
		foreach ($result as &$row) {
			$dialplan_include_uuid = $row["dialplan_include_uuid"];
		}
		unset($prep_statement, $result);
	}
	unset($sql);
	return $dialplan_include_uuid;
}

function v_dialplan_includes_details_add($domain_uuid, $dialplan_include_uuid, $tag, $field_order, $field_type, $field_data) {
	global $db;
	$sql = "insert into v_dialplan_includes_details ";
	$sql .= "(";
	$sql .= "domain_uuid, ";
	$sql .= "dialplan_include_uuid, ";
	$sql .= "tag, ";
	$sql .= "field_order, ";
	$sql .= "field_type, ";
	$sql .= "field_data ";
	$sql .= ")";
	$sql .= "values ";
	$sql .= "(";
	$sql .= "'$domain_uuid', ";
	$sql .= "'".check_str($dialplan_include_uuid)."', ";
	$sql .= "'".check_str($tag)."', ";
	$sql .= "'".check_str($field_order)."', ";
	$sql .= "'".check_str($field_type)."', ";
	$sql .= "'".check_str($field_data)."' ";
	$sql .= ")";
	$db->exec(check_sql($sql));
	unset($sql);
}

function sync_package_v_dialplan_includes() {
	global $db, $domain_uuid;

	$v_settings_array = v_settings();
	foreach($v_settings_array as $name => $value) {
		$$name = $value;
	}

	//prepare for dialplan .xml files to be written. delete all dialplan files that are prefixed with dialplan_ and have a file extension of .xml
		$v_needle = 'v_dialplan_';
		if($dh = opendir($v_dialplan_default_dir."/")) {
			$files = Array();
			while($file = readdir($dh)) {
				if($file != "." && $file != ".." && $file[0] != '.') {
					if(is_dir($dir . "/" . $file)) {
						//this is a directory
					} else {
						if (strpos($file, $v_needle) !== false && substr($file,-4) == '.xml') {
							unlink($v_dialplan_default_dir."/".$file); //remove before final release
						}
					}
				}
			}
			closedir($dh);
		}

	$sql = "";
	$sql .= "select * from v_dialplan_includes ";
	$sql .= "where domain_uuid = '$domain_uuid' ";
	$sql .= "and enabled = 'true' ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$result = $prep_statement->fetchAll(PDO::FETCH_ASSOC);
	foreach ($result as &$row) {
		$tmp = "";
		$tmp .= "\n";

		$extension_continue = '';
		if ($row['extension_continue'] == "true") {
			$extension_continue = "continue=\"true\"";
		}

		$tmp = "<extension name=\"".$row['extension_name']."\" $extension_continue>\n";

		$sql = "";
		$sql .= " select * from v_dialplan_includes_details ";
		$sql .= " where dialplan_include_uuid = '".$row['dialplan_include_uuid']."' ";
		$sql .= " and domain_uuid = $domain_uuid ";
		$sql .= " order by field_group asc, field_order asc ";
		$prep_statement_2 = $db->prepare($sql);
		$prep_statement_2->execute();
		$result2 = $prep_statement_2->fetchAll(PDO::FETCH_NAMED);
		$resultcount2 = count($result2);
		unset ($prep_statement_2, $sql);

		//create a new array that is sorted into groups and put the tags in order conditions, actions, anti-actions
			$details = '';
			$previous_tag = '';
			$details[$group]['condition_count'] = '';
			//conditions
				$x = 0;
				$y = 0;
				foreach($result2 as $row2) {
					if ($row2['tag'] == "condition") {
						//get the group
							$group = $row2['field_group'];
						//get the generic type
							switch ($row2['field_type']) {
							case "hour":
								$type = 'time';
								break;
							case "minute":
								$type = 'time';
								break;
							case "minute-of-day":
								$type = 'time';
								break;
							case "mday":
								$type = 'time';
								break;
							case "mweek":
								$type = 'time';
								break;
							case "mon":
								$type = 'time';
								break;
							case "yday":
								$type = 'time';
								break;
							case "year":
								$type = 'time';
								break;
							case "wday":
								$type = 'time';
								break;
							case "week":
								$type = 'time';
								break;
							default:
								$type = 'default';
							}

						//add the conditions to the details array
							$details[$group]['condition-'.$x]['tag'] = $row2['tag'];
							$details[$group]['condition-'.$x]['field_type'] = $row2['field_type'];
							$details[$group]['condition-'.$x]['dialplan_include_uuid'] = $row2['dialplan_include_uuid'];
							$details[$group]['condition-'.$x]['field_order'] = $row2['field_order'];
							$details[$group]['condition-'.$x]['field'][$y]['type'] = $row2['field_type'];
							$details[$group]['condition-'.$x]['field'][$y]['data'] = $row2['field_data'];
							$details[$group]['condition-'.$x]['field_break'] = $row2['field_break'];
							$details[$group]['condition-'.$x]['field_group'] = $row2['field_group'];
							$details[$group]['condition-'.$x]['field_inline'] = $row2['field_inline'];
							if ($type == "time") {
								$y++;
							}
					}
					if ($type == "default") {
						$x++;
						$y = 0;
					}
				}

			//actions
				$x = 0;
				foreach($result2 as $row2) {
					if ($row2['tag'] == "action") {
						$group = $row2['field_group'];
						foreach ($row2 as $key => $val) {
							$details[$group]['action-'.$x][$key] = $val;
						}
					}
					$x++;
				}
			//anti-actions
				$x = 0;
				foreach($result2 as $row2) {
					if ($row2['tag'] == "anti-action") {
						$group = $row2['field_group'];
						foreach ($row2 as $key => $val) {
							$details[$group]['anti-action-'.$x][$key] = $val;
						}
					}
					$x++;
				}
			unset($result2);

		$i=1;
		if ($resultcount2 > 0) { 
			foreach($details as $group) {
				$current_count = 0;
				$x = 0;
				foreach($group as $ent) {
					$current_tag = $ent['tag'];
					$c = 0;
					if ($ent['tag'] == "condition") {
						//get the generic type
							switch ($ent['field_type']) {
							case "hour":
								$type = 'time';
								break;
							case "minute":
								$type = 'time';
								break;
							case "minute-of-day":
								$type = 'time';
								break;
							case "mday":
								$type = 'time';
								break;
							case "mweek":
								$type = 'time';
								break;
							case "mon":
								$type = 'time';
								break;
							case "yday":
								$type = 'time';
								break;
							case "year":
								$type = 'time';
								break;
							case "wday":
								$type = 'time';
								break;
							case "week":
								$type = 'time';
								break;
							default:
								$type = 'default';
							}

						//set the attribute and expression
							$condition_attribute = '';
							foreach($ent['field'] as $field) {
								if ($type == "time") {
									$condition_attribute .= $field['type'].'="'.$field['data'].'" ';
									$condition_expression = '';
								}
								if ($type == "default") {
									$condition_attribute = 'field="'.$field['type'].'" ';
									$condition_expression = 'expression="'.$field['data'].'" ';
								}
							}

						//get the condition break attribute
							$condition_break = '';
							if (strlen($ent['field_break']) > 0) {
								$condition_break = "break=\"".$ent['field_break']."\" ";
							}

						//get the count
							$count = 0;
							foreach($details as $group2) {
								foreach($group2 as $ent2) {
									if ($ent2['field_group'] == $ent['field_group'] && $ent2['tag'] == "condition") {
										$count++;
									}
								}
							}

						//use the correct type of tag open or self closed
							if ($count == 1) { //single condition
								//start tag
								$tmp .= "   <condition ".$condition_attribute."".$condition_expression."".$condition_break.">\n";
							}
							else { //more than one condition
								$current_count++;
								if ($current_count < $count) {
									//all tags should be self-closing except the last one
									$tmp .= "   <condition ".$condition_attribute."".$condition_expression."".$condition_break."/>\n";
								}
								else {
									//for the last tag use the start tag
									$tmp .= "   <condition ".$condition_attribute."".$condition_expression."".$condition_break.">\n";
								}
							}
					}
					//actions
						if ($ent['tag'] == "action") {
							//get the action inline attribute
							$action_inline = '';
							if (strlen($ent['field_inline']) > 0) {
								$action_inline = "inline=\"".$ent['field_inline']."\"";
							}
							if (strlen($ent['field_data']) > 0) {
								$tmp .= "       <action application=\"".$ent['field_type']."\" data=\"".$ent['field_data']."\" $action_inline/>\n";
							}
							else {
								$tmp .= "       <action application=\"".$ent['field_type']."\" $action_inline/>\n";
							}
						}
					//anti-actions
						if ($ent['tag'] == "anti-action") {
							if (strlen($ent['field_data']) > 0) {
								$tmp .= "       <anti-action application=\"".$ent['field_type']."\" data=\"".$ent['field_data']."\"/>\n";
							}
							else {
								$tmp .= "       <anti-action application=\"".$ent['field_type']."\"/>\n";
							}
						}
					//set the previous tag
						$previous_tag = $ent['tag'];
					$i++;
				} //end foreach
				$tmp .= "   </condition>\n";
				$x++;
			}
			if ($condition_count > 0) {
				$condition_count = $resultcount2;
			}
			unset($sql, $resultcount2, $result2, $rowcount2);
		} //end if results
		$tmp .= "</extension>\n";

		$dialplan_order = $row['dialplan_order'];
		if (strlen($dialplan_order) == 0) { $dialplan_order = "000".$dialplan_order; }
		if (strlen($dialplan_order) == 1) { $dialplan_order = "00".$dialplan_order; }
		if (strlen($dialplan_order) == 2) { $dialplan_order = "0".$dialplan_order; }
		if (strlen($dialplan_order) == 4) { $dialplan_order = "999"; }
		if (strlen($dialplan_order) == 5) { $dialplan_order = "999"; }

		//remove invalid characters from the file names
		$extension_name = $row['extension_name'];
		$extension_name = str_replace(" ", "_", $extension_name);
		$extension_name = preg_replace("/[\*\:\\/\<\>\|\'\"\?]/", "", $extension_name);

		$dialplan_include_filename = $dialplan_order."_v_dialplan_".$extension_name.".xml";
		$fout = fopen($v_dialplan_default_dir."/".$dialplan_include_filename,"w");
		fwrite($fout, $tmp);
		fclose($fout);

		unset($dialplan_include_filename);
		unset($tmp);
	} //end while

	//apply settings reminder
		$_SESSION["reload_xml"] = true;
}


function sync_package_v_public_includes() {
	global $config;
	$v_settings_array = v_settings();
	foreach($v_settings_array as $name => $value) {
		$$name = $value;
	}

	global $db, $domain_uuid;

	//prepare for dialplan .xml files to be written. delete all dialplan files that are prefixed with dialplan_ and have a file extension of .xml
		$v_needle = '_v_';
		if($dh = opendir($v_dialplan_public_dir."/")) {
			$files = Array();
			while($file = readdir($dh)) {
				if($file != "." && $file != ".." && $file[0] != '.') {
					if(is_dir($dir . "/" . $file)) {
						//this is a directory
					} else {
						if (strpos($file, $v_needle) !== false && substr($file,-4) == '.xml') {
							unlink($v_dialplan_public_dir."/".$file);
						}
					}
				}
			}
			closedir($dh);
		}

	//loop through all the public includes aka inbound routes
		$sql = "";
		$sql .= "select * from v_public_includes ";
		$sql .= "where domain_uuid = $domain_uuid ";
		$sql .= "and enabled = 'true' ";
		$prep_statement = $db->prepare(check_sql($sql));
		$prep_statement->execute();
		$result = $prep_statement->fetchAll(PDO::FETCH_ASSOC);
		foreach ($result as &$row) {
			$extension_continue = '';
			if ($row['extension_continue'] == "true") {
				$extension_continue = "continue=\"true\"";
			}

			$tmp = "";
			$tmp .= "\n";
			$tmp = "<extension name=\"".$row['extension_name']."\" $extension_continue>\n";

			$sql = "";
			$sql .= " select * from v_public_includes_details ";
			$sql .= " where public_include_uuid = '".$row['public_include_uuid']."' ";
			$sql .= " and tag = 'condition' ";
			$sql .= " order by field_order asc";
			$prep_statement_2 = $db->prepare($sql);
			$prep_statement_2->execute();
			$result2 = $prep_statement_2->fetchAll(PDO::FETCH_ASSOC);
			$resultcount2 = count($result2);
			unset ($prep_statement_2, $sql);
			$i=1;
			if ($resultcount2 == 0) {
				//no results
			}
			else { //received results
				foreach($result2 as $ent) {
					if ($resultcount2 == 1) { //single condition
						//start tag
						$tmp .= "   <condition field=\"".$ent['field_type']."\" expression=\"".$ent['field_data']."\">\n";
					}
					else { //more than one condition
						if ($i < $resultcount2) {
							  //all tags should be self-closing except the last one
							  $tmp .= "   <condition field=\"".$ent['field_type']."\" expression=\"".$ent['field_data']."\"/>\n";
						}
						else {
							//for the last tag use the start tag
							  $tmp .= "   <condition field=\"".$ent['field_type']."\" expression=\"".$ent['field_data']."\">\n";
						}
					}
					$i++;
				} //end foreach
				$conditioncount = $resultcount2;
				unset($sql, $resultcount2, $result2);
			} //end if results

			$sql = "";
			$sql .= " select * from v_public_includes_details ";
			$sql .= " where public_include_uuid = '".$row['public_include_uuid']."' ";
			$sql .= " and tag = 'action' ";
			$sql .= " order by field_order asc";
			$prep_statement_2 = $db->prepare($sql);
			$prep_statement_2->execute();
			$result2 = $prep_statement_2->fetchAll(PDO::FETCH_ASSOC);
			$resultcount2 = count($result2);
			unset ($prep_statement_2, $sql);
			if ($resultcount2 == 0) { //no results
			}
			else { //received results
				$i = 0;
				foreach($result2 as $ent) {
					if ($ent['tag'] == "action" && $row['public_include_uuid'] == $ent['public_include_uuid']) {
						if (strlen($ent['field_data']) > 0) {
							$tmp .= "       <action application=\"".$ent['field_type']."\" data=\"".$ent['field_data']."\"/>\n";
						}
						else {
							$tmp .= "       <action application=\"".$ent['field_type']."\"/>\n";
						}
					}
					$i++;
				} //end foreach
				unset($sql, $resultcount2, $result2);
			} //end if results

			$sql = "";
			$sql .= " select * from v_public_includes_details ";
			$sql .= " where public_include_uuid = '".$row['public_include_uuid']."' ";
			$sql .= " and tag = 'anti-action' ";
			$sql .= " order by field_order asc";
			$prep_statement_2 = $db->prepare($sql);
			$prep_statement_2->execute();
			$result2 = $prep_statement_2->fetchAll(PDO::FETCH_ASSOC);
			$resultcount2 = count($result2);
			unset ($prep_statement_2, $sql);
			if ($resultcount2 == 0) { //no results
			}
			else { //received results
				$i = 0;
				foreach($result2 as $ent) {
					if ($ent['tag'] == "anti-action" && $row['public_include_uuid'] == $ent['public_include_uuid']) {
						if (strlen($ent['field_data']) > 0) {
							$tmp .= "       <anti-action application=\"".$ent['field_type']."\" data=\"".$ent['field_data']."\"/>\n";
						}
						else {
							$tmp .= "       <anti-action application=\"".$ent['field_type']."\"/>\n";
						}
					}
					$i++;
				} //end foreach
				unset($sql, $resultcount2, $result2, $rowcount2);
			} //end if results

			if ($conditioncount > 0) {
				$tmp .= "   </condition>\n";
			}
			unset ($conditioncount);
			$tmp .= "</extension>\n";

			$public_order = $row['public_order'];
			if (strlen($public_order) == 0) { $public_order = "000".$public_order; }
			if (strlen($public_order) == 1) { $public_order = "00".$public_order; }
			if (strlen($public_order) == 2) { $public_order = "0".$public_order; }
			if (strlen($public_order) == 4) { $public_order = "999"; }
			if (strlen($public_order) == 5) { $public_order = "999"; }

			//remove invalid characters from the file names
			$extension_name = $row['extension_name'];
			$extension_name = str_replace(" ", "_", $extension_name);
			$extension_name = preg_replace("/[\*\:\\/\<\>\|\'\"\?]/", "", $extension_name);

			$public_include_filename = $public_order."_v_".$extension_name.".xml";
			$fout = fopen($v_dialplan_public_dir."/".$public_include_filename,"w");
			fwrite($fout, $tmp);
			fclose($fout);

			unset($public_include_filename);
			unset($tmp);
	} //end while
	unset ($prep_statement);

	//apply settings reminder
		$_SESSION["reload_xml"] = true;
}


if (!function_exists('phone_letter_to_number')) {
	function phone_letter_to_number($tmp) {
		$tmp = strtolower($tmp);
		if ($tmp == "a" | $tmp == "b" | $tmp == "c") { return 2; }
		if ($tmp == "d" | $tmp == "e" | $tmp == "f") { return 3; }
		if ($tmp == "g" | $tmp == "h" | $tmp == "i") { return 4; }
		if ($tmp == "j" | $tmp == "k" | $tmp == "l") { return 5; }
		if ($tmp == "m" | $tmp == "n" | $tmp == "o") { return 6; }
		if ($tmp == "p" | $tmp == "q" | $tmp == "r" | $tmp == "s") { return 7; }
		if ($tmp == "t" | $tmp == "u" | $tmp == "v") { return 8; }
		if ($tmp == "w" | $tmp == "x" | $tmp == "y" | $tmp == "z") { return 9; }
	}
}


if (!function_exists('sync_directory')) {
	function sync_directory() {

		global $domain_uuid, $db;
		$v_settings_array = v_settings();
		foreach($v_settings_array as $name => $value) {
			$$name = $value;
		}

		$tmp = "include(\"config.js\");\n";
		$tmp .= "//var sounds_dir\n";
		$tmp .= "var admin_pin = \"\";\n";
		$tmp .= "var search_type = \"\";\n";
		$tmp .= "//var tmp_dir\n";
		$tmp .= "var digitmaxlength = 0;\n";
		$tmp .= "var timeoutpin = 5000;\n";
		$tmp .= "var timeouttransfer = 5000;\n";
		$tmp .= "\n";
		$tmp .= "var dtmf = new Object( );\n";
		$tmp .= "dtmf.digits = \"\";\n";
		$tmp .= "\n";
		$tmp .= "function mycb( session, type, obj, arg ) {\n";
		$tmp .= "	try {\n";
		$tmp .= "		if ( type == \"dtmf\" ) {\n";
		$tmp .= "			console_log( \"info\", \"digit: \"+obj.digit+\"\\n\" );\n";
		$tmp .= "			if ( obj.digit == \"#\" ) {\n";
		$tmp .= "				//console_log( \"info\", \"detected pound sign.\\n\" );\n";
		$tmp .= "				exit = true;\n";
		$tmp .= "				return( false );\n";
		$tmp .= "			}\n";
		$tmp .= "			if ( obj.digit == \"*\" ) {\n";
		$tmp .= "				//console_log( \"info\", \"detected pound sign.\\n\" );\n";
		$tmp .= "				exit = true;\n";
		$tmp .= "				return( false );\n";
		$tmp .= "			}\n";
		$tmp .= "			dtmf.digits += obj.digit;\n";
		$tmp .= "			if ( dtmf.digits.length >= digitmaxlength ) {\n";
		$tmp .= "				exit = true;\n";
		$tmp .= "				return( false );\n";
		$tmp .= "			}\n";
		$tmp .= "		}\n";
		$tmp .= "	} catch (e) {\n";
		$tmp .= "		console_log( \"err\", e+\"\\n\" );\n";
		$tmp .= "	}\n";
		$tmp .= "	return( true );\n";
		$tmp .= "} //end function mycb\n";
		$tmp .= "\n";
		$tmp .= "function directory_search(search_type) {\n";
		$tmp .= "\n";
		$tmp .= "	digitmaxlength = 3;\n";
		$tmp .= "	session.streamFile( sounds_dir+\"/en/us/callie/directory/48000/dir-enter_person.wav\");\n";
		$tmp .= "	if (search_type == \"last_name\") {\n";
		$tmp .= "		session.streamFile( sounds_dir+\"/en/us/callie/directory/48000/dir-last_name.wav\", mycb, \"dtmf\");\n";
		$tmp .= "		session.streamFile( sounds_dir+\"/en/us/callie/directory/48000/dir-to_search_by.wav\", mycb, \"dtmf\");\n";
		$tmp .= "		session.streamFile( sounds_dir+\"/en/us/callie/directory/48000/dir-first_name.wav\", mycb, \"dtmf\");\n";
		$tmp .= "	}\n";
		$tmp .= "	if (search_type == \"first_name\") {\n";
		$tmp .= "		session.streamFile( sounds_dir+\"/en/us/callie/directory/48000/dir-first_name.wav\", mycb, \"dtmf\");\n";
		$tmp .= "		session.streamFile( sounds_dir+\"/en/us/callie/directory/48000/dir-to_search_by.wav\", mycb, \"dtmf\");\n";
		$tmp .= "		session.streamFile( sounds_dir+\"/en/us/callie/directory/48000/dir-last_name.wav\", mycb, \"dtmf\");\n";
		$tmp .= "	}\n";
		$tmp .= "	session.streamFile( sounds_dir+\"/en/us/callie/directory/48000/dir-press.wav\", mycb, \"dtmf\");\n";
		$tmp .= "	session.execute(\"say\", \"en name_spelled iterated 1\");\n";
		$tmp .= "	session.collectInput( mycb, dtmf, timeoutpin );\n";
		$tmp .= "	var dtmf_search = dtmf.digits;\n";
		$tmp .= "	//console_log( \"info\", \"--\" + dtmf.digits + \"--\\n\" );\n";
		$tmp .= "	if (dtmf_search == \"1\") {\n";
		$tmp .= "		//console_log( \"info\", \"press 1 detected: \" + dtmf.digits + \"\\n\" );\n";
		$tmp .= "		//console_log( \"info\", \"press 1 detected: \" + search_type + \"\\n\" );\n";
		$tmp .= "		if (search_type == \"last_name\") {\n";
		$tmp .= "			//console_log( \"info\", \"press 1 detected last_name: \" + search_type + \"\\n\" );\n";
		$tmp .= "			search_type = \"first_name\";\n";
		$tmp .= "		}\n";
		$tmp .= "		else {\n";
		$tmp .= "			//console_log( \"info\", \"press 1 detected first_name: \" + search_type + \"\\n\" );\n";
		$tmp .= "			search_type = \"last_name\";\n";
		$tmp .= "		}\n";
		$tmp .= "		dtmf_search = \"\";\n";
		$tmp .= "		dtmf.digits = \"\";\n";
		$tmp .= "		directory_search(search_type);\n";
		$tmp .= "		return;\n";
		$tmp .= "	}\n";
		$tmp .= "	console_log( \"info\", \"first 3 letters of first or last name: \" + dtmf.digits + \"\\n\" );\n";
		$tmp .= "\n";
		$tmp .= "	//session.execute(\"say\", \"en name_spelled pronounced mark\");\n";
		$tmp .= "	//<action application=\"say\" data=\"en name_spelled iterated \${destination_number}\"/>\n";
		$tmp .= "	//session.execute(\"say\", \"en number iterated 12345\");\n";
		$tmp .= "	//session.execute(\"say\", \"en number pronounced 1001\");\n";
		$tmp .= "	//session.execute(\"say\", \"en short_date_time pronounced [timestamp]\");\n";
		$tmp .= "	//session.execute(\"say\", \"en CURRENT_TIME pronounced CURRENT_TIME\");\n";
		$tmp .= "	//session.execute(\"say\", \"en CURRENT_DATE pronounced CURRENT_DATE\");\n";
		$tmp .= "	//session.execute(\"say\", \"en CURRENT_DATE_TIME pronounced CURRENT_DATE_TIME\");\n";
		$tmp .= "\n";
		$tmp .= "\n";
		$tmp .= "	//take each name and convert it to the equivalent number in php when this file is generated\n";
		$tmp .= "	//then test each number see if it matches the user dtmf search keys\n";
		$tmp .= "\n";
		$tmp .= "	var result_array = new Array();\n";
		$tmp .= "	var x = 0;\n";

		//get a list of extensions and the users assigned to them
			$sql = "";
			$sql .= " select * from v_extensions ";
			$sql .= "where domain_uuid = '$domain_uuid' ";
			$prep_statement = $db->prepare(check_sql($sql));
			$prep_statement->execute();
			$x = 0;
			$result = $prep_statement->fetchAll(PDO::FETCH_ASSOC);
			foreach ($result as &$row) {
				$extension = $row["extension"];
				$effective_caller_id_name = $row["effective_caller_id_name"];
				$user_list = $row["user_list"];
				$user_list = trim($user_list, "|");
				$username_array = explode ("|", $user_list);
				foreach ($username_array as &$username) {
					if (strlen($username) > 0) {
						$sql = "";
						$sql .= "select * from v_users ";
						$sql .= "where domain_uuid = '$domain_uuid' ";
						$sql .= "and username = '$username' ";
						$prep_statement = $db->prepare(check_sql($sql));
						$prep_statement->execute();
						$tmp_result = $prep_statement->fetchAll(PDO::FETCH_ASSOC);
						foreach ($tmp_result as &$row_tmp) {
							$user_first_name = $row_tmp["user_first_name"];
							$user_last_name = $row_tmp["user_last_name"];
							if ($user_first_name == "na") { $user_first_name = ""; }
							if ($user_last_name == "na") { $user_last_name = ""; }
							if ($user_first_name == "admin") { $user_first_name = ""; }
							if ($user_last_name == "admin") { $user_last_name = ""; }
							if ($user_first_name == "superadmin") { $user_first_name = ""; }
							if ($user_last_name == "superadmin") { $user_last_name = ""; }
							if (strlen($user_first_name.$user_last_name) == 0) {
								$name_array = explode (" ", $effective_caller_id_name);
								$user_first_name = $name_array[0];
								if (count($name_array) > 1) {
									$user_last_name = $name_array[1];
								}
							}
							
							break; //limit to 1 row
						}
						$f1 = phone_letter_to_number(substr($user_first_name, 0,1)); 
						$f2 = phone_letter_to_number(substr($user_first_name, 1,1));
						$f3 = phone_letter_to_number(substr($user_first_name, 2,1));

						$l1 = phone_letter_to_number(substr($user_last_name, 0,1)); 
						$l2 = phone_letter_to_number(substr($user_last_name, 1,1));
						$l3 = phone_letter_to_number(substr($user_last_name, 2,1));

						//echo $sql." extension: $extension  first_name $user_first_name last_name $user_last_name $tmp<br />";

						$tmp .= "	if (search_type == \"first_name\" && dtmf_search == \"".$f1.$f2.$f3."\" || search_type == \"last_name\" && dtmf_search == \"".$l1.$l2.$l3."\") {\n";
						$tmp .= "		result_array[x]=new Array()\n";
						$tmp .= "		result_array[x]['first_name'] =\"".$user_first_name."\";\n";
						$tmp .= "		result_array[x]['last_name'] =\"".$user_last_name."\";\n";
						$tmp .= "		result_array[x]['extension'] = \"".$extension."\";\n";
						$tmp .= "		//console_log( \"info\", \"found: ".$user_first_name." ".$user_last_name."\\n\" );\n";
						$tmp .= "		x++;\n";
						$tmp .= "	}\n";
					}
				}
			}
			unset ($prep_statement);

		$tmp .= "\n";
		$tmp .= "\n";
		$tmp .= "	//say the number of results that matched\n";
		$tmp .= "	\$result_count = result_array.length;\n";
		$tmp .= "	session.execute(\"say\", \"en number iterated \"+\$result_count);\n";
		$tmp .= "	session.streamFile( sounds_dir+\"/en/us/callie/directory/48000/dir-result_match.wav\", mycb, \"dtmf\");\n";
		$tmp .= "\n";
		$tmp .= "	//clear values\n";
		$tmp .= "	dtmf_search = 0;\n";
		$tmp .= "	dtmf.digits = '';\n";
		$tmp .= "\n";
		$tmp .= "	if (\$result_count == 0) {\n";
		$tmp .= "		//session.execute(\"transfer\", \"*347 XML ".$_SESSION["context"]."\");\n";
		$tmp .= "		directory_search(search_type);\n";
		$tmp .= "		return;\n";
		$tmp .= "	}\n";
		$tmp .= "\n";
		$tmp .= "	session.execute(\"set\", \"tts_engine=flite\");\n";
		$tmp .= "	session.execute(\"set\", \"tts_voice=rms\");  //rms //kal //awb //slt\n";
		$tmp .= "	session.execute(\"set\", \"playback_terminators=#\");\n";
		$tmp .= "	//session.speak(\"flite\",\"kal\",\"Thanks for.. calling\");\n";
		$tmp .= "\n";
		$tmp .= "	i=1;\n";
		$tmp .= "	for ( i in result_array ) {\n";
		$tmp .= "\n";
		$tmp .= "		//say first name and last name is at extension 1001\n";
		$tmp .= "		//session.execute(\"speak\", result_array[i]['first_name']);\n";
		$tmp .= "		//session.execute(\"speak\", result_array[i]['last_name']);\n";
		$tmp .= "		session.execute(\"say\", \"en name_spelled pronounced \"+result_array[i]['first_name']);\n";
		$tmp .= "		session.execute(\"sleep\", \"500\");\n";
		$tmp .= "		session.execute(\"say\", \"en name_spelled pronounced \"+result_array[i]['last_name']);\n";
		$tmp .= "		session.streamFile( sounds_dir+\"/en/us/callie/directory/48000/dir-at_extension.wav\", mycb, \"dtmf\");\n";
		$tmp .= "		session.execute(\"say\", \"en number pronounced \"+result_array[i]['extension']);\n";
		$tmp .= "\n";
		$tmp .= "		//to select this entry press 1\n";
		$tmp .= "		session.streamFile( sounds_dir+\"/en/us/callie/directory/48000/dir-to_select_entry.wav\", mycb, \"dtmf\");\n";
		$tmp .= "		session.streamFile( sounds_dir+\"/en/us/callie/directory/48000/dir-press.wav\", mycb, \"dtmf\");\n";
		$tmp .= "		session.execute(\"say\", \"en number iterated 1\");\n";
		$tmp .= "\n";
		$tmp .= "		//console_log( \"info\", \"first name: \" + result_array[i]['first_name'] + \"\\n\" );\n";
		$tmp .= "		//console_log( \"info\", \"last name: \" + result_array[i]['last_name'] + \"\\n\" );\n";
		$tmp .= "		//console_log( \"info\", \"extension: \" + result_array[i]['extension'] + \"\\n\" );\n";
		$tmp .= "\n";
		$tmp .= "		//if 1 is pressed then transfer the call\n";
		$tmp .= "		dtmf.digits = session.getDigits(1, \"#\", 3000);\n";
		$tmp .= "		if (dtmf.digits == \"1\") {\n";
		$tmp .= "			console_log( \"info\", \"directory: call transfered to: \" + result_array[i]['extension'] + \"\\n\" );\n";
		$tmp .= "			session.execute(\"transfer\", result_array[i]['extension']+\" XML ".$_SESSION["context"]."\");\n";
		$tmp .= "		}\n";
		$tmp .= "\n";
		$tmp .= "	}\n";
		$tmp .= "}\n";
		$tmp .= "\n";
		$tmp .= "\n";
		$tmp .= "if ( session.ready() ) {\n";
		$tmp .= "	session.answer();\n";
		$tmp .= "	search_type = \"last_name\";\n";
		$tmp .= "	directory_search(search_type);\n";
		$tmp .= "	session.hangup(\"NORMAL_CLEARING\");\n";
		$tmp .= "}\n";
		$tmp .= "";

		//write the file
			$fout = fopen($v_scripts_dir."/directory.js","w");
			fwrite($fout, $tmp);
			fclose($fout);

		//apply settings reminder
			$_SESSION["reload_xml"] = true;
	} //end sync_directory
} //end if function exists

if (!function_exists('sync_package_v_ivr_menu')) {
	function sync_package_v_ivr_menu() {
		global $db, $domain_uuid;

		$v_settings_array = v_settings();
		foreach($v_settings_array as $name => $value) {
			$$name = $value;
		}

		//prepare for dialplan .xml files to be written. delete all dialplan files that are prefixed with dialplan_ and have a file extension of .xml
			if (count($_SESSION["domains"]) > 1) {
				$v_needle = 'v_'.$v_domain.'_';
			}
			else {
				$v_needle = 'v_';
			}
			if($dh = opendir($v_conf_dir."/ivr_menus/")) {
				$files = Array();
				while($file = readdir($dh)) {
					if($file != "." && $file != ".." && $file[0] != '.') {
						if(is_dir($dir . "/" . $file)) {
							//this is a directory
						} else {
							if (strpos($file, $v_needle) !== false && substr($file,-4) == '.xml') {
								//echo "file: $file<br />\n";
								unlink($v_conf_dir."/ivr_menus/".$file);
							}
						}
					}
				}
				closedir($dh);
			}

		$sql = "";
		$sql .= " select * from v_ivr_menu ";
		$sql .= " where domain_uuid = '$domain_uuid' ";
		$prep_statement = $db->prepare(check_sql($sql));
		$prep_statement->execute();
		$result = $prep_statement->fetchAll(PDO::FETCH_ASSOC);
		$resultcount = count($result);
		unset ($prep_statement, $sql);
		if ($resultcount > 0) {
			foreach($result as $row) {
				$ivr_menu_uuid = $row["ivr_menu_uuid"];
				$ivr_menu_name = check_str($row["ivr_menu_name"]);
				$ivr_menu_extension = $row["ivr_menu_extension"];
				$ivr_menu_greet_long = $row["ivr_menu_greet_long"];
				$ivr_menu_greet_short = $row["ivr_menu_greet_short"];
				$ivr_menu_invalid_sound = $row["ivr_menu_invalid_sound"];
				$ivr_menu_exit_sound = $row["ivr_menu_exit_sound"];
				$ivr_menu_confirm_macro = $row["ivr_menu_confirm_macro"];
				$ivr_menu_confirm_key = $row["ivr_menu_confirm_key"];
				$ivr_menu_tts_engine = $row["ivr_menu_tts_engine"];
				$ivr_menu_tts_voice = $row["ivr_menu_tts_voice"];
				$ivr_menu_confirm_attempts = $row["ivr_menu_confirm_attempts"];
				$ivr_menu_timeout = $row["ivr_menu_timeout"];
				$ivr_menu_exit_app = $row["ivr_menu_exit_app"];
				$ivr_menu_exit_data = $row["ivr_menu_exit_data"];
				$ivr_menu_inter_digit_timeout = $row["ivr_menu_inter_digit_timeout"];
				$ivr_menu_max_failures = $row["ivr_menu_max_failures"];
				$ivr_menu_max_timeouts = $row["ivr_menu_max_timeouts"];
				$ivr_menu_digit_len = $row["ivr_menu_digit_len"];
				$ivr_menu_direct_dial = $row["ivr_menu_direct_dial"];
				$ivr_menu_enabled = $row["ivr_menu_enabled"];
				$ivr_menu_desc = check_str($row["ivr_menu_desc"]);

				//replace space with an underscore
					$ivr_menu_name = str_replace(" ", "_", $ivr_menu_name);

				//add each IVR Menu to the dialplan
					if (strlen($row['ivr_menu_uuid']) > 0) {
						$action = 'add'; //set default action to add
						$i = 0;

						//get the dialplan include id
							$sql = "";
							$sql .= "select * from v_dialplan_includes ";
							$sql .= "where domain_uuid = '$domain_uuid' ";
							$sql .= "and opt_1_name = 'ivr_menu_uuid' ";
							$sql .= "and opt_1_value = '".$row['ivr_menu_uuid']."' ";
							$prep_statement_2 = $db->prepare($sql);
							$prep_statement_2->execute();
							while($row2 = $prep_statement_2->fetch(PDO::FETCH_ASSOC)) {
								$action = 'update';
								$dialplan_include_uuid = $row2['dialplan_include_uuid'];
								break; //limit to 1 row
							}
							unset ($sql, $prep_statement_2);

						//delete the dialplan details
							$sql = "";
							$sql .= "delete from v_dialplan_includes_details ";
							$sql .= "where domain_uuid = '$domain_uuid' ";
							$sql .= "and dialplan_include_uuid = '$dialplan_include_uuid' ";
							$prep_statement_2 = $db->prepare(check_sql($sql));
							$prep_statement_2->execute();
							unset ($sql, $prep_statement_2);

						//create the ivr menu dialplan extension
							$extension_name = $ivr_menu_name;
							$dialplan_order ='999';
							$context = $row['ivr_menu_context'];
							$context = 'default';
							$enabled = 'true';
							$descr = $ivr_menu_desc;

							if ($action  == "add") {
								$opt_1_name = 'ivr_menu_uuid';
								$opt_1_value = $row['ivr_menu_uuid'];
								$dialplan_include_uuid = v_dialplan_includes_add($domain_uuid, $extension_name, $dialplan_order, $context, $enabled, $descr, $opt_1_name, $opt_1_value);
							}
							if ($action  == "update") {
								$ivr_menu_uuid = $row['ivr_menu_uuid'];

								$sql = "";
								$sql = "update v_dialplan_includes set ";
								$sql .= "extension_name = '$extension_name', ";
								$sql .= "dialplan_order = '$dialplan_order', ";
								$sql .= "context = '$context', ";
								$sql .= "enabled = '$enabled', ";
								$sql .= "descr = '$descr' ";
								$sql .= "where domain_uuid = '$domain_uuid' ";
								$sql .= "and opt_1_name = 'ivr_menu_uuid' ";
								$sql .= "and opt_1_value = '$ivr_menu_uuid' ";
								$db->query($sql);
								unset($sql);
							}

							$tag = 'condition'; //condition, action, antiaction
							$field_type = 'destination_number';
							$field_data = '^'.$row['ivr_menu_extension'].'$';
							$field_order = '005';
							v_dialplan_includes_details_add($domain_uuid, $dialplan_include_uuid, $tag, $field_order, $field_type, $field_data);

							$tag = 'action'; //condition, action, antiaction
							$field_type = 'answer';
							$field_data = '';
							$field_order = '010';
							v_dialplan_includes_details_add($domain_uuid, $dialplan_include_uuid, $tag, $field_order, $field_type, $field_data);

							$tag = 'action'; //condition, action, antiaction
							$field_type = 'sleep';
							$field_data = '1000';
							$field_order = '015';
							v_dialplan_includes_details_add($domain_uuid, $dialplan_include_uuid, $tag, $field_order, $field_type, $field_data);

							$tag = 'action'; //condition, action, antiaction
							$field_type = 'set';
							$field_data = 'hangup_after_bridge=true';
							$field_order = '020';
							v_dialplan_includes_details_add($domain_uuid, $dialplan_include_uuid, $tag, $field_order, $field_type, $field_data);

							$tag = 'action'; //condition, action, antiaction
							$field_type = 'ivr';
							if (count($_SESSION["domains"]) > 1) {
								$field_data = $_SESSION['domains'][$domain_uuid]['domain'].'-'.$ivr_menu_name;
							}
							else {
								$field_data = $ivr_menu_name;
							}
							$field_order = '025';
							v_dialplan_includes_details_add($domain_uuid, $dialplan_include_uuid, $tag, $field_order, $field_type, $field_data);
							
							if (strlen($ivr_menu_exit_app) > 0) {
								$tag = 'action'; //condition, action, antiaction
								$field_type = $ivr_menu_exit_app;
								$field_data = $ivr_menu_exit_data;
								$field_order = '030';
								v_dialplan_includes_details_add($domain_uuid, $dialplan_include_uuid, $tag, $field_order, $field_type, $field_data);
							}

						unset($action);
						unset($dialplan_include_uuid);
					} //end if strlen ivr_menu_uuid; add the IVR Menu to the dialplan

				//add each IVR menu to the XML config
					$tmp = "<include>\n";
					if (strlen($ivr_menu_desc) > 0) {
						$tmp .= "	<!-- $ivr_menu_desc -->\n";
					}
					if (count($_SESSION["domains"]) > 1) {
						$tmp .= "	<menu name=\"".$_SESSION['domains'][$domain_uuid]['domain']."-".$ivr_menu_name."\"\n";
					}
					else {
						$tmp .= "	<menu name=\"$ivr_menu_name\"\n";
					}
					if (stripos($ivr_menu_greet_long, 'mp3') !== false || stripos($ivr_menu_greet_long, 'wav') !== false) {
						//found wav or mp3
						$tmp .= "		greet-long=\"".$ivr_menu_greet_long."\"\n";
					}
					else {
						//not found
						$tmp .= "		greet-long=\"".$ivr_menu_greet_long."\"\n";
					}
					if (stripos($ivr_menu_greet_short, 'mp3') !== false || stripos($ivr_menu_greet_short, 'wav') !== false) {
						if (strlen($ivr_menu_greet_short) > 0) {
							$tmp .= "		greet-short=\"".$ivr_menu_greet_short."\"\n";
						}
					}
					else {
						//not found
						if (strlen($ivr_menu_greet_short) > 0) {
							$tmp .= "		greet-short=\"".$ivr_menu_greet_short."\"\n";
						}
					}
					$tmp .= "		invalid-sound=\"$ivr_menu_invalid_sound\"\n";
					$tmp .= "		exit-sound=\"$ivr_menu_exit_sound\"\n";
					$tmp .= "		confirm-macro=\"$ivr_menu_confirm_macro\"\n";
					$tmp .= "		confirm-key=\"$ivr_menu_confirm_key\"\n";
					$tmp .= "		tts-engine=\"$ivr_menu_tts_engine\"\n";
					$tmp .= "		tts-voice=\"$ivr_menu_tts_voice\"\n";
					$tmp .= "		confirm-attempts=\"$ivr_menu_confirm_attempts\"\n";
					$tmp .= "		timeout=\"$ivr_menu_timeout\"\n";
					$tmp .= "		inter-digit-timeout=\"$ivr_menu_inter_digit_timeout\"\n";
					$tmp .= "		max-failures=\"$ivr_menu_max_failures\"\n";
					$tmp .= "		max-timeouts=\"$ivr_menu_max_timeouts\"\n";
					$tmp .= "		digit-len=\"$ivr_menu_digit_len\">\n";

					$sub_sql = "";
					$sub_sql .= "select * from v_ivr_menu_options ";
					$sub_sql .= "where ivr_menu_uuid = '$ivr_menu_uuid' ";
					$sub_sql .= "and domain_uuid = '$domain_uuid' ";
					$sub_sql .= "order by ivr_menu_options_order asc "; 
					$sub_prepstatement = $db->prepare(check_sql($sub_sql));
					$sub_prepstatement->execute();
					$sub_result = $sub_prepstatement->fetchAll(PDO::FETCH_ASSOC);
					foreach ($sub_result as &$sub_row) {
						//$ivr_menu_uuid = $sub_row["ivr_menu_uuid"];
						$ivr_menu_options_digits = $sub_row["ivr_menu_options_digits"];
						$ivr_menu_options_action = $sub_row["ivr_menu_options_action"];
						$ivr_menu_options_param = $sub_row["ivr_menu_options_param"];
						$ivr_menu_options_desc = $sub_row["ivr_menu_options_desc"];

						$tmp .= "		<entry action=\"$ivr_menu_options_action\" digits=\"$ivr_menu_options_digits\" param=\"$ivr_menu_options_param\"/>";
						if (strlen($ivr_menu_options_desc) == 0) {
							$tmp .= "\n";
						}
						else {
							$tmp .= "	<!-- $ivr_menu_options_desc -->\n";
						}
					}
					unset ($sub_prepstatement, $sub_row);

					if ($ivr_menu_direct_dial == "true") {
						$tmp .= "		<entry action=\"menu-exec-app\" digits=\"/(^\d{3,6}$)/\" param=\"transfer $1 XML ".$_SESSION["context"]."\"/>\n";
					}
					$tmp .= "	</menu>\n";
					$tmp .= "</include>\n";

					//remove invalid characters from the file names
						$ivr_menu_name = str_replace(" ", "_", $ivr_menu_name);
						$ivr_menu_name = preg_replace("/[\*\:\\/\<\>\|\'\"\?]/", "", $ivr_menu_name);

					//write the file
						if (count($_SESSION["domains"]) > 1) {
							$fout = fopen($v_conf_dir."/ivr_menus/v_".$_SESSION['domains'][$row['domain_uuid']]['domain']."_".$ivr_menu_name.".xml","w");
						}
						else {
							$fout = fopen($v_conf_dir."/ivr_menus/v_".$ivr_menu_name.".xml","w");
						}
						fwrite($fout, $tmp);
						fclose($fout);
			}
		}
		sync_package_v_dialplan_includes();

		//apply settings reminder
			$_SESSION["reload_xml"] = true;
	}
}

if (!function_exists('sync_package_v_call_center')) {
	function sync_package_v_call_center() {
		global $db, $domain_uuid;

		$v_settings_array = v_settings();
		foreach($v_settings_array as $name => $value) {
			$$name = $value;
		}

		//include the classes
		include "includes/classes/dialplan.php";

		$sql = "";
		$sql .= "select * from v_call_center_queue ";
		$prep_statement = $db->prepare(check_sql($sql));
		$prep_statement->execute();
		$result = $prep_statement->fetchAll(PDO::FETCH_ASSOC);
		$result_count = count($result);
		unset ($prep_statement, $sql);
		if ($result_count > 0) { //found results
			foreach($result as $row) {
				$call_center_queue_uuid = $row["call_center_queue_uuid"];
				$queue_name = check_str($row["queue_name"]);
				$queue_extension = $row["queue_extension"];
				$queue_strategy = $row["queue_strategy"];
				$queue_moh_sound = $row["queue_moh_sound"];
				$queue_record_template = $row["queue_record_template"];
				$queue_time_base_score = $row["queue_time_base_score"];
				$queue_max_wait_time = $row["queue_max_wait_time"];
				$queue_max_wait_time_with_no_agent = $row["queue_max_wait_time_with_no_agent"];
				$queue_tier_rules_apply = $row["queue_tier_rules_apply"];
				$queue_tier_rule_wait_second = $row["queue_tier_rule_wait_second"];
				$queue_tier_rule_wait_multiply_level = $row["queue_tier_rule_wait_multiply_level"];
				$queue_tier_rule_no_agent_no_wait = $row["queue_tier_rule_no_agent_no_wait"];
				$queue_timeout_action = $row["queue_timeout_action"];
				$queue_discard_abandoned_after = $row["queue_discard_abandoned_after"];
				$queue_abandoned_resume_allowed = $row["queue_abandoned_resume_allowed"];
				$queue_cid_prefix = $row["queue_cid_prefix"];
				$queue_description = check_str($row["queue_description"]);

				//replace space with an underscore
					$queue_name = str_replace(" ", "_", $queue_name);

				//add each Queue to the dialplan
					if (strlen($row['call_center_queue_uuid']) > 0) {
						$action = 'add'; //set default action to add
						$i = 0;

						$sql = "";
						$sql .= "select * from v_dialplan_includes ";
						$sql .= "where opt_1_name = 'call_center_queue_uuid' ";
						$sql .= "and opt_1_value = '".$row['call_center_queue_uuid']."' ";
						$prep_statement_2 = $db->prepare($sql);
						$prep_statement_2->execute();
						while($row2 = $prep_statement_2->fetch(PDO::FETCH_ASSOC)) {
							$action = 'update';
							$dialplan_include_uuid = $row2['dialplan_include_uuid'];
							break; //limit to 1 row
						}
						unset ($sql, $prep_statement_2);

						if ($action == 'add') {

							//create queue entry in the dialplan
								$extension_name = $queue_name;
								$dialplan_order ='9';
								//$context = $row['queue_context'];
								$context = 'default';
								$enabled = 'true';
								$descr = $queue_description;
								$opt_1_name = 'call_center_queue_uuid';
								$opt_1_value = $row['call_center_queue_uuid'];
								$dialplan_include_uuid = v_dialplan_includes_add($domain_uuid, $extension_name, $dialplan_order, $context, $enabled, $descr, $opt_1_name, $opt_1_value);


								//group 1
									$dialplan = new dialplan;
									$dialplan->domain_uuid = $domain_uuid;
									$dialplan->dialplan_include_uuid = $dialplan_include_uuid;
									$dialplan->tag = 'condition'; //condition, action, antiaction
									$dialplan->field_type = '${caller_id_name}';
									$dialplan->field_data = '^([^#]+#)(.*)$';
									$dialplan->field_break = 'never';
									$dialplan->field_inline = '';
									$dialplan->field_group = '1';
									$dialplan->field_order = '000';
									$dialplan->dialplan_detail_add();
									unset($dialplan);

									$dialplan = new dialplan;
									$dialplan->domain_uuid = $domain_uuid;
									$dialplan->dialplan_include_uuid = $dialplan_include_uuid;
									$dialplan->tag = 'action'; //condition, action, antiaction
									$dialplan->field_type = 'set';
									$dialplan->field_data = 'caller_id_name=$2';
									$dialplan->field_break = '';
									$dialplan->field_inline = '';
									$dialplan->field_group = '1';
									$dialplan->field_order = '001';
									$dialplan->dialplan_detail_add();
									unset($dialplan);

								//group 2
									$dialplan = new dialplan;
									$dialplan->domain_uuid = $domain_uuid;
									$dialplan->dialplan_include_uuid = $dialplan_include_uuid;
									$dialplan->tag = 'condition'; //condition, action, antiaction
									$dialplan->field_type = 'destination_number';
									$dialplan->field_data = '^'.$row['queue_extension'].'$';
									$dialplan->field_break = '';
									$dialplan->field_inline = '';
									$dialplan->field_group = '2';
									$dialplan->field_order = '000';
									$dialplan->dialplan_detail_add();
									unset($dialplan);

									$dialplan = new dialplan;
									$dialplan->domain_uuid = $domain_uuid;
									$dialplan->dialplan_include_uuid = $dialplan_include_uuid;
									$dialplan->tag = 'action'; //condition, action, antiaction
									$dialplan->field_type = 'answer';
									$dialplan->field_data = '';
									$dialplan->field_break = '';
									$dialplan->field_inline = '';
									$dialplan->field_group = '2';
									$dialplan->field_order = '001';
									$dialplan->dialplan_detail_add();
									unset($dialplan);

									$dialplan = new dialplan;
									$dialplan->domain_uuid = $domain_uuid;
									$dialplan->dialplan_include_uuid = $dialplan_include_uuid;
									$dialplan->tag = 'action'; //condition, action, antiaction
									$dialplan->field_type = 'set';
									$dialplan->field_data = 'hangup_after_bridge=true';
									$dialplan->field_break = '';
									$dialplan->field_inline = '';
									$dialplan->field_group = '2';
									$dialplan->field_order = '002';
									$dialplan->dialplan_detail_add();
									unset($dialplan);

									$dialplan = new dialplan;
									$dialplan->domain_uuid = $domain_uuid;
									$dialplan->dialplan_include_uuid = $dialplan_include_uuid;
									$dialplan->tag = 'action'; //condition, action, antiaction
									$dialplan->field_type = 'set';
									$dialplan->field_data = "caller_id_name=".$queue_cid_prefix."#\${caller_id_name}";
									$dialplan->field_break = '';
									$dialplan->field_inline = '';
									$dialplan->field_group = '2';
									$dialplan->field_order = '003';
									$dialplan->dialplan_detail_add();
									unset($dialplan);

									$dialplan = new dialplan;
									$dialplan->domain_uuid = $domain_uuid;
									$dialplan->dialplan_include_uuid = $dialplan_include_uuid;
									$dialplan->tag = 'action'; //condition, action, antiaction
									$dialplan->field_type = 'system';
									$dialplan->field_data = 'mkdir -p $${base_dir}/recordings/archive/${strftime(%Y)}/${strftime(%b)}/${strftime(%d)}/';
									$dialplan->field_break = '';
									$dialplan->field_inline = '';
									$dialplan->field_group = '2';
									$dialplan->field_order = '004';
									$dialplan->dialplan_detail_add();
									unset($dialplan);

									$dialplan = new dialplan;
									$dialplan->domain_uuid = $domain_uuid;
									$dialplan->dialplan_include_uuid = $dialplan_include_uuid;
									$dialplan->tag = 'action'; //condition, action, antiaction
									$dialplan->field_type = 'callcenter';
									$dialplan->field_data = $queue_name."@".$_SESSION['domains'][$domain_uuid]['domain'];
									$dialplan->field_break = '';
									$dialplan->field_inline = '';
									$dialplan->field_group = '2';
									$dialplan->field_order = '005';
									$dialplan->dialplan_detail_add();
									unset($dialplan);

									if (strlen($queue_timeout_action) > 0) {
										$action_array = explode(":",$queue_timeout_action);
										$dialplan = new dialplan;
										$dialplan->domain_uuid = $domain_uuid;
										$dialplan->dialplan_include_uuid = $dialplan_include_uuid;
										$dialplan->tag = 'action'; //condition, action, antiaction
										$dialplan->field_type = $action_array[0];
										$dialplan->field_data = substr($queue_timeout_action, strlen($action_array[0])+1, strlen($queue_timeout_action));
										$dialplan->field_break = '';
										$dialplan->field_inline = '';
										$dialplan->field_group = '2';
										$dialplan->field_order = '006';
										$dialplan->dialplan_detail_add();
										unset($dialplan);
									}

									$dialplan = new dialplan;
									$dialplan->domain_uuid = $domain_uuid;
									$dialplan->dialplan_include_uuid = $dialplan_include_uuid;
									$dialplan->tag = 'action'; //condition, action, antiaction
									$dialplan->field_type = 'hangup';
									$dialplan->field_data = '';
									$dialplan->field_break = '';
									$dialplan->field_inline = '';
									$dialplan->field_group = '2';
									$dialplan->field_order = '007';
									$dialplan->dialplan_detail_add();
									unset($dialplan);
						}
						if ($action == 'update') {
							//update the queue entry in the dialplan

								$extension_name = $queue_name;
								$dialplan_order = '9';
								//$context = $row['queue_context'];
								$context = 'default';
								$enabled = 'true';
								$descr = $queue_description;
								$call_center_queue_uuid = $row['call_center_queue_uuid'];

								$sql = "";
								$sql = "update v_dialplan_includes set ";
								$sql .= "extension_name = '$extension_name', ";
								$sql .= "dialplan_order = '$dialplan_order', ";
								$sql .= "context = '$context', ";
								$sql .= "enabled = '$enabled', ";
								$sql .= "descr = '$descr' ";
								$sql .= "where domain_uuid = '$domain_uuid' ";
								$sql .= "and opt_1_name = 'call_center_queue_uuid' ";
								$sql .= "and opt_1_value = '$call_center_queue_uuid' ";
								//echo "sql: ".$sql."<br />";
								$db->query($sql);
								unset($sql);

								//update the condition
								$sql = "";
								$sql = "update v_dialplan_includes_details set ";
								$sql .= "field_data = '^".$row['queue_extension']."$' ";
								$sql .= "where domain_uuid = '$domain_uuid' ";
								$sql .= "and tag = 'condition' ";
								$sql .= "and field_type = 'destination_number' ";
								$sql .= "and dialplan_include_uuid = '$dialplan_include_uuid' ";
								//echo $sql."<br />";
								$db->query($sql);
								unset($sql);

								//update the action
								$sql = "";
								$sql = "update v_dialplan_includes_details set ";
								$sql .= "field_data = 'caller_id_name=".$queue_cid_prefix."\${caller_id_name}' ";
								$sql .= "where domain_uuid = '$domain_uuid' ";
								$sql .= "and tag = 'action' ";
								$sql .= "and field_type = 'set' ";
								$sql .= "and dialplan_include_uuid = '$dialplan_include_uuid' ";
								$sql .= "and field_data like '%{caller_id_name}%' ";
								//echo $sql."<br />";
								$db->query($sql);

								//update the action
								$sql = "";
								$sql = "update v_dialplan_includes_details set ";
								$sql .= "field_data = '".$queue_name."@".$_SESSION['domains'][$domain_uuid]['domain']."' ";
								$sql .= "where domain_uuid = '$domain_uuid' ";
								$sql .= "and tag = 'action' ";
								$sql .= "and field_type = 'callcenter' ";
								$sql .= "and dialplan_include_uuid = '$dialplan_include_uuid' ";
								//echo $sql."<br />";
								$db->query($sql);

								unset($extension_name);
								unset($order);
								unset($context);
								unset($enabled);
								unset($descr);
								unset($opt_1_name);
								unset($opt_1_value);
						}
						unset($action);
						unset($dialplanincludeid);
					} //end if strlen call_center_queue_uuid; add the call center queue to the dialplan
			}

			//prepare Queue XML string
				$v_queues = '';
				$sql = "";
				$sql .= "select * from v_call_center_queue ";
				$prep_statement = $db->prepare(check_sql($sql));
				$prep_statement->execute();
				$result = $prep_statement->fetchAll(PDO::FETCH_ASSOC);
				$x=0;
				foreach ($result as &$row) {
					$queue_name = $row["queue_name"];
					$queue_extension = $row["queue_extension"];
					$queue_strategy = $row["queue_strategy"];
					$queue_moh_sound = $row["queue_moh_sound"];
					$queue_record_template = $row["queue_record_template"];
					$queue_time_base_score = $row["queue_time_base_score"];
					$queue_max_wait_time = $row["queue_max_wait_time"];
					$queue_max_wait_time_with_no_agent = $row["queue_max_wait_time_with_no_agent"];
					$queue_tier_rules_apply = $row["queue_tier_rules_apply"];
					$queue_tier_rule_wait_second = $row["queue_tier_rule_wait_second"];
					$queue_tier_rule_wait_multiply_level = $row["queue_tier_rule_wait_multiply_level"];
					$queue_tier_rule_no_agent_no_wait = $row["queue_tier_rule_no_agent_no_wait"];
					$queue_discard_abandoned_after = $row["queue_discard_abandoned_after"];
					$queue_abandoned_resume_allowed = $row["queue_abandoned_resume_allowed"];
					$queue_description = $row["queue_description"];
					if ($x > 0) {
						$v_queues .= "\n";
						$v_queues .= "		";
					}
					$v_queues .= "<queue name=\"$queue_name@".$_SESSION['domains'][$row["domain_uuid"]]['domain']."\">\n";
					$v_queues .= "			<param name=\"strategy\" value=\"$queue_strategy\"/>\n";
					$v_queues .= "			<param name=\"moh-sound\" value=\"$queue_moh_sound\"/>\n";
					if (strlen($queue_record_template) > 0) {
						$v_queues .= "			<param name=\"record-template\" value=\"$queue_record_template\"/>\n";
					}
					$v_queues .= "			<param name=\"time-base-score\" value=\"$queue_time_base_score\"/>\n";
					$v_queues .= "			<param name=\"max-wait-time\" value=\"$queue_max_wait_time\"/>\n";
					$v_queues .= "			<param name=\"max-wait-time-with-no-agent\" value=\"$queue_max_wait_time_with_no_agent\"/>\n";
					$v_queues .= "			<param name=\"tier-rules-apply\" value=\"$queue_tier_rules_apply\"/>\n";
					$v_queues .= "			<param name=\"tier-rule-wait-second\" value=\"$queue_tier_rule_wait_second\"/>\n";
					$v_queues .= "			<param name=\"tier-rule-wait-multiply-level\" value=\"$queue_tier_rule_wait_multiply_level\"/>\n";
					$v_queues .= "			<param name=\"tier-rule-no-agent-no-wait\" value=\"$queue_tier_rule_no_agent_no_wait\"/>\n";
					$v_queues .= "			<param name=\"discard-abandoned-after\" value=\"$queue_discard_abandoned_after\"/>\n";
					$v_queues .= "			<param name=\"abandoned-resume-allowed\" value=\"$queue_abandoned_resume_allowed\"/>\n";
					$v_queues .= "		</queue>";
					$x++;
				}
				unset ($prep_statement);

			//prepare Agent XML string
				$v_agents = '';
				$sql = "";
				$sql .= "select * from v_call_center_agent ";
				$prep_statement = $db->prepare(check_sql($sql));
				$prep_statement->execute();
				$result = $prep_statement->fetchAll(PDO::FETCH_ASSOC);
				$x=0;
				foreach ($result as &$row) {
					//get the values from the db and set as php variables
						$agent_name = $row["agent_name"];
						$agent_type = $row["agent_type"];
						$agent_call_timeout = $row["agent_call_timeout"];
						$agent_contact = $row["agent_contact"];
						$agent_status = $row["agent_status"];
						$agent_no_answer_delay_time = $row["agent_no_answer_delay_time"];
						$agent_max_no_answer = $row["agent_max_no_answer"];
						$agent_wrap_up_time = $row["agent_wrap_up_time"];
						$agent_reject_delay_time = $row["agent_reject_delay_time"];
						$agent_busy_delay_time = $row["agent_busy_delay_time"];
						if ($x > 0) {
							$v_agents .= "\n";
							$v_agents .= "		";

						}

					//get and then set the complete agent_contact with the call_timeout and when necessary confirm
						$tmp_confirm = "group_confirm_file=custom/press_1_to_accept_this_call.wav,group_confirm_key=1";
						if(strstr($agent_contact, '}') === FALSE) {
							//not found
							if(stristr($agent_contact, 'sofia/gateway') === FALSE) {
								//add the call_timeout
								$tmp_agent_contact = "{call_timeout=".$agent_call_timeout."}".$agent_contact;
							}
							else {
								//add the call_timeout and confirm
								$tmp_agent_contact = $tmp_first.',call_timeout='.$agent_call_timeout.$tmp_last;
								$tmp_agent_contact = "{".$tmp_confirm.",call_timeout=".$agent_call_timeout."}".$agent_contact;
							}
						}
						else {
							//found
							if(stristr($agent_contact, 'sofia/gateway') === FALSE) {
								//not found
								if(stristr($agent_contact, 'call_timeout') === FALSE) {
									//add the call_timeout
									$tmp_pos = strrpos($agent_contact, "}");
									$tmp_first = substr($agent_contact, 0, $tmp_pos);
									$tmp_last = substr($agent_contact, $tmp_pos); 
									$tmp_agent_contact = $tmp_first.',call_timeout='.$agent_call_timeout.$tmp_last;
								}
								else {
									//the string has the call timeout
									$tmp_agent_contact = $agent_contact;
								}
							}
							else {
								//found
								$tmp_pos = strrpos($agent_contact, "}");
								$tmp_first = substr($agent_contact, 0, $tmp_pos);
								$tmp_last = substr($agent_contact, $tmp_pos);
								if(stristr($agent_contact, 'call_timeout') === FALSE) {
									//add the call_timeout and confirm
									$tmp_agent_contact = $tmp_first.','.$tmp_confirm.',call_timeout='.$agent_call_timeout.$tmp_last;
								}
								else {
									//add confirm
									$tmp_agent_contact = $tmp_first.','.$tmp_confirm.$tmp_last;
								}
							}
						}

					$v_agents .= "<agent ";
					$v_agents .= "name=\"$agent_name@".$_SESSION['domains'][$row["domain_uuid"]]['domain']."\" ";
					$v_agents .= "type=\"$agent_type\" ";
					$v_agents .= "contact=\"$tmp_agent_contact\" ";
					$v_agents .= "status=\"$agent_status\" ";
					$v_agents .= "no-answer-delay-time=\"$agent_no_answer_delay_time\" ";
					$v_agents .= "max-no-answer=\"$agent_max_no_answer\" ";
					$v_agents .= "wrap-up-time=\"$agent_wrap_up_time\" ";
					$v_agents .= "reject-delay-time=\"$agent_reject_delay_time\" ";
					$v_agents .= "busy-delay-time=\"$agent_busy_delay_time\" ";
					$v_agents .= "/>";
					$x++;
				}
				unset ($prep_statement);

			//prepare Tier XML string
				$v_tiers = '';
				$sql = "";
				$sql .= "select * from v_call_center_tier ";
				$prep_statement = $db->prepare(check_sql($sql));
				$prep_statement->execute();
				$result = $prep_statement->fetchAll(PDO::FETCH_ASSOC);
				$x=0;
				foreach ($result as &$row) {
					$agent_name = $row["agent_name"];
					$queue_name = $row["queue_name"];
					$tier_level = $row["tier_level"];
					$tier_position = $row["tier_position"];
					if ($x > 0) {
						$v_tiers .= "\n";
						$v_tiers .= "		";
					}
					$v_tiers .= "<tier agent=\"$agent_name@".$_SESSION['domains'][$row["domain_uuid"]]['domain']."\" queue=\"$queue_name@".$_SESSION['domains'][$row["domain_uuid"]]['domain']."\" level=\"$tier_level\" position=\"$tier_position\"/>";
					$x++;
				}

			//get the contents of the template
				$file_contents = file_get_contents($_SERVER["DOCUMENT_ROOT"].PROJECT_PATH."/includes/templates/conf/autoload_configs/callcenter.conf.xml");

			//add the Call Center Queues, Agents and Tiers to the XML config
				$file_contents = str_replace("{v_queues}", $v_queues, $file_contents);
				unset ($v_queues);

				$file_contents = str_replace("{v_agents}", $v_agents, $file_contents);
				unset ($v_agents);

				$file_contents = str_replace("{v_tiers}", $v_tiers, $file_contents);
				unset ($v_tiers);

			//write the XML config file
				$fout = fopen($v_conf_dir."/autoload_configs/callcenter.conf.xml","w");
				fwrite($fout, $file_contents);
				fclose($fout);

			//syncrhonize the configuration
				sync_package_v_dialplan_includes();

			//apply settings reminder
				$_SESSION["reload_xml"] = true;
		}
	}
}

if (!function_exists('switch_conf_xml')) {
	function switch_conf_xml() {
		//get the global variables
			global $db, $domain_uuid;

		//get settings as array and convert them to a php variable
			$v_settings_array = v_settings();
			foreach($v_settings_array as $name => $value) {
				$$name = $value;
			}

		//get the contents of the template
			$file_contents = file_get_contents($_SERVER["DOCUMENT_ROOT"].PROJECT_PATH."/includes/templates/conf/autoload_configs/switch.conf.xml");

		//prepare the php variables
			if (file_exists($php_dir.'/php')) { $php_bin = 'php'; }
			if (file_exists($php_dir.'/php.exe')) { $php_bin = 'php.exe'; }
			if (stristr(PHP_OS, 'WIN')) {
				$v_mailer_app = $php_dir."/".$php_bin."";
				$v_mailer_app_args = "".$v_secure."/v_mailto.php -t";
			}
			else {
				$v_mailer_app = $php_dir."/".$php_bin." ".$v_secure."/v_mailto.php";
				$v_mailer_app_args = "-t";
			}

		//replace the values in the template
			$file_contents = str_replace("{v_mailer_app}", $v_mailer_app, $file_contents);
			unset ($v_mailer_app);

		//replace the values in the template
			$file_contents = str_replace("{v_mailer_app_args}", $v_mailer_app_args, $file_contents);
			unset ($v_mailer_app_args);

		//write the XML config file
			$fout = fopen($v_conf_dir."/autoload_configs/switch.conf.xml","w");
			fwrite($fout, $file_contents);
			fclose($fout);

		//apply settings reminder
			$_SESSION["reload_xml"] = true;
	}
}

if (!function_exists('xml_cdr_conf_xml')) {
	function xml_cdr_conf_xml() {

		//get the global variables
			global $db, $domain_uuid;

		//get settings as array and convert them to a php variable
			$v_settings_array = v_settings();
			foreach($v_settings_array as $name => $value) {
				$$name = $value;
			}

		//get the contents of the template
			$file_contents = file_get_contents($_SERVER["DOCUMENT_ROOT"].PROJECT_PATH."/includes/templates/conf/autoload_configs/xml_cdr.conf.xml");

		//replace the values in the template
			$file_contents = str_replace("{v_http_protocol}", "http", $file_contents);
			$file_contents = str_replace("{v_domain}", "127.0.0.1", $file_contents);
			$file_contents = str_replace("{v_project_path}", PROJECT_PATH, $file_contents);

			$v_user = generate_password();
			$file_contents = str_replace("{v_user}", $v_user, $file_contents);
			unset ($v_user);

			$v_pass = generate_password();
			$file_contents = str_replace("{v_pass}", $v_pass, $file_contents);
			unset ($v_pass);

		//write the XML config file
			$fout = fopen($v_conf_dir."/autoload_configs/xml_cdr.conf.xml","w");
			fwrite($fout, $file_contents);
			fclose($fout);

		//apply settings reminder
			$_SESSION["reload_xml"] = true;
	}
}

if (!function_exists('sync_package_freeswitch')) {
	function sync_package_freeswitch() {
		global $config;
		sync_package_v_settings();
		sync_package_v_dialplan();
		sync_package_v_dialplan_includes();
		sync_package_v_extensions();
		sync_package_v_gateways();
		sync_package_v_modules();
		sync_package_v_public();
		sync_package_v_public_includes();
		sync_package_v_vars();
		//sync_package_v_recordings();
		sync_package_v_hunt_group();
		sync_package_v_ivr_menu();
		sync_package_v_call_center();
		sync_package_v_fax();
	}
}

//include all the .php files in the /includes/mod/includes directory
	//foreach (glob($v_web_dir."/includes/mod/includes/*.php") as $filename) {
	//	require_once $filename;
	//}
?>
