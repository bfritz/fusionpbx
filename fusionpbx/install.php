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
require_once "includes/lib_functions.php";

$v_id = '1';

//error reporting
	ini_set('display_errors', '1');
	//error_reporting (E_ALL); // Report everything
	error_reporting (E_ALL ^ E_NOTICE); // Report everything
	//error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING ); //hide notices and warnings

//todolist: install.php save 
	//restore backup if the backup exists (reserved for next version)
	//update button
		//ini_set('default_socket_timeout', 120);
		//$a = file_get_contents("http://fusionpbx.com");
		//http://php.net/manual/en/function.file-get-contents.php
		//extract tgz files with a php class
			//http://www.phpclasses.org/browse/package/945.html

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

//if the config file exists then disable the install page
	if (file_exists($_SERVER["DOCUMENT_ROOT"].PROJECT_PATH."/includes/config.php")) {
		$msg .= "Already installed.<br />\n";
		header("Location: ".PROJECT_PATH."/login.php?msg=".urlencode($msg));
	}

//set the max execution time to 1 hour
	ini_set('max_execution_time',3600);

//set php variables with data from http post
	$db_type = $_POST["db_type"];
	$db_filename = $_POST["db_filename"];
	$db_host = $_POST["db_host"];
	$db_port = $_POST["db_port"];
	$db_name = $_POST["db_name"];
	$db_username = $_POST["db_username"];
	$db_password = $_POST["db_password"];
	$db_create_username = $_POST["db_create_username"];
	$db_create_password = $_POST["db_create_password"];
	$db_filepath = $_POST["db_filepath"];
	$install_step = $_POST["install_step"];
	$install_secure_dir = $_POST["install_secure_dir"];
	$install_php_dir = $_POST["install_php_dir"];
	$install_tmp_dir = $_POST["install_tmp_dir"];
	$install_v_backup_dir = $_POST["install_v_backup_dir"];
	$install_v_dir = $_POST["install_v_dir"];

//clean up the values
	$install_v_dir = realpath($install_v_dir);
	$install_v_dir = str_replace("\\", "/", $install_v_dir);

	$install_php_dir = realpath($_POST["install_php_dir"]);
	$install_php_dir = str_replace("\\", "/", $install_php_dir);

	$install_tmp_dir = realpath($_POST["install_tmp_dir"]);
	$install_tmp_dir = str_replace("\\", "/", $install_tmp_dir);

	$install_v_backup_dir = realpath($_POST["install_v_backup_dir"]);
	$install_v_backup_dir = str_replace("\\", "/", $install_v_backup_dir);

//set the default install_secure_dir
	if (strlen($install_secure_dir) == 0) { //secure dir
		$install_secure_dir = $_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/secure';
	}

//set the default db_filename
	if ($db_type == "sqlite") {
		if (strlen($db_filename) == 0) { $db_filename = "fusionpbx.db"; }
	}

//set the required directories

	//set the php bin directory
		if (file_exists('/usr/local/bin/php') || file_exists('/usr/local/bin/php5')) {
			$install_php_dir = '/usr/local/bin';
		}
		if (file_exists('/usr/bin/php') || file_exists('/usr/bin/php5')) {
			$install_php_dir = '/usr/bin';
		}

	//set the freeswitch bin directory
		if (file_exists('/usr/local/freeswitch/bin')) {
			$install_v_dir = '/usr/local/freeswitch';
			$v_bin_dir = '/usr/local/freeswitch/bin';
			$v_parent_dir = '/usr/local';
		}
		if (file_exists('/opt/freeswitch')) {
			$install_v_dir = '/opt/freeswitch';
			$v_bin_dir = '/opt/freeswitch/bin';
			$v_parent_dir = '/opt';
		}

	//set the default startup script directory
		if (file_exists('/usr/local/etc/rc.d')) {
			$v_startup_script_dir = '/usr/local/etc/rc.d';
		}
		if (file_exists('/etc/init.d')) {
			$v_startup_script_dir = '/etc/init.d';
		}

	//set the default directories
		$v_bin_dir = $install_v_dir.'/bin'; //freeswitch bin directory
		$v_conf_dir = $install_v_dir.'/conf';
		$v_db_dir = $install_v_dir.'/db';
		$v_htdocs_dir = $install_v_dir.'/htdocs';
		$v_log_dir = $install_v_dir.'/log';
		$v_mod_dir = $install_v_dir.'/mod';
		$v_extensions_dir = $v_conf_dir.'/directory/default';
		$v_dialplan_public_dir = $v_conf_dir.'/dialplan/public';
		$v_dialplan_default_dir = $v_conf_dir.'/dialplan/default';
		$v_scripts_dir = $install_v_dir.'/scripts';
		$v_grammar_dir = $install_v_dir.'/grammar';
		$v_storage_dir = $install_v_dir.'/storage';
		$v_voicemail_dir = $install_v_dir.'/storage/voicemail';
		$v_recordings_dir = $install_v_dir.'/recordings';
		$v_sounds_dir = $install_v_dir.'/sounds';
		$install_tmp_dir = realpath(sys_get_temp_dir());
		$install_v_backup_dir = realpath(sys_get_temp_dir());
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
						$v_bin_dir = '/usr/local/bin'; //freeswitch bin directory
						$v_conf_dir = '/usr/local/etc/freeswitch/conf';
						$v_db_dir = '/var/db/freeswitch';
						$v_htdocs_dir = '/usr/local/www/freeswitch/htdocs';
						$v_log_dir = '/var/log/freeswitch';
						$v_mod_dir = '/usr/local/lib/freeswitch/mod';
						$v_extensions_dir = $v_conf_dir.'/directory/default';
						$v_dialplan_public_dir = $v_conf_dir.'/dialplan/public';
						$v_dialplan_default_dir = $v_conf_dir.'/dialplan/default';
						$v_scripts_dir = '/usr/local/etc/freeswitch/scripts';
						$v_grammar_dir = '/usr/local/etc/freeswitch/grammar';
						$v_storage_dir = '/var/freeswitch';
						$v_voicemail_dir = '/var/spool/freeswitch/voicemail';
						$v_recordings_dir = '/var/freeswitch/recordings';
						$v_sounds_dir = '/usr/local/share/freeswitch/sounds';
				}
				else {
					//set the default db_filepath
						if (strlen($db_filepath) == 0) { //secure dir
							$db_filepath = $_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/secure';
						}
				}
			break;
		case "NetBSD":
			$v_startup_script_dir = '';
			$install_php_dir = '/usr/local/bin';

			//set the default db_filepath
				if (strlen($db_filepath) == 0) { //secure dir
					$db_filepath = $_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/secure';
				}
			break;
		case "OpenBSD":
			$v_startup_script_dir = '';

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


if ($_POST["install_step"] == "3" && count($_POST)>0 && strlen($_POST["persistformvar"]) == 0) {

	$msg = '';
	//check for all required data
		if (strlen($db_type) == 0) { $msg .= "Please provide the Database Type<br>\n"; }
		if (strlen($install_v_dir) == 0) { $msg .= "Please provide the Switch Directory<br>\n"; }
		if (strlen($install_php_dir) == 0) { $msg .= "Please provide the PHP Directory<br>\n"; }
		if (strlen($install_tmp_dir) == 0) { $msg .= "Please provide the Temp Directory<br>\n"; }
		if (strlen($install_v_backup_dir) == 0) { $msg .= "Please provide the Backup Directory<br>\n"; }
		if (!is_writable($install_v_dir."/conf/vars.xml")) {
			if (stristr(PHP_OS, 'WIN')) { 
				//some windows operating systems report read only but are writable
			}
			else {
				//$msg .= "<b>Write access to ".$install_v_dir." and its sub-directories is required.</b><br />\n";
			}
		}
		if (strlen($msg) > 0 && strlen($_POST["persistformvar"]) == 0) {
			require_once "includes/persistformvar.php";
			echo "<br />\n";
			echo "<br />\n";
			echo "<div align='center'>\n";
			echo "<table><tr><td>\n";
			echo $msg."<br />";
			echo "</td></tr></table>\n";
			persistformvar($_POST);
			echo "</div>\n";
			exit;
		}

	//generate the config.php
		$tmp_config = "<?php\n";
		$tmp_config .= "/* \$Id\$ */\n";
		$tmp_config .= "/*\n";
		$tmp_config .= "	config.php\n";
		$tmp_config .= "	Copyright (C) 2008, 2009 Mark J Crane\n";
		$tmp_config .= "	All rights reserved.\n";
		$tmp_config .= "\n";
		$tmp_config .= "	Redistribution and use in source and binary forms, with or without\n";
		$tmp_config .= "	modification, are permitted provided that the following conditions are met:\n";
		$tmp_config .= "\n";
		$tmp_config .= "	1. Redistributions of source code must retain the above copyright notice,\n";
		$tmp_config .= "	   this list of conditions and the following disclaimer.\n";
		$tmp_config .= "\n";
		$tmp_config .= "	2. Redistributions in binary form must reproduce the above copyright\n";
		$tmp_config .= "	   notice, this list of conditions and the following disclaimer in the\n";
		$tmp_config .= "	   documentation and/or other materials provided with the distribution.\n";
		$tmp_config .= "\n";
		$tmp_config .= "	THIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES,\n";
		$tmp_config .= "	INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY\n";
		$tmp_config .= "	AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE\n";
		$tmp_config .= "	AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,\n";
		$tmp_config .= "	OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF\n";
		$tmp_config .= "	SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS\n";
		$tmp_config .= "	INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN\n";
		$tmp_config .= "	CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)\n";
		$tmp_config .= "	ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE\n";
		$tmp_config .= "	POSSIBILITY OF SUCH DAMAGE.\n";
		$tmp_config .= "*/\n";
		$tmp_config .= "\n";
		$tmp_config .= "//-----------------------------------------------------\n";
		$tmp_config .= "// settings:\n";
		$tmp_config .= "//-----------------------------------------------------\n";
		$tmp_config .= "\n";
		$tmp_config .= "	//set the database type\n";
		$tmp_config .= "		\$dbtype = '".$db_type."'; //sqlite, mysql, pgsql, others with a manually created PDO connection\n";
		$tmp_config .= "\n";
		if ($db_type == "sqlite") {
			$tmp_config .= "	//sqlite: the dbfilename and dbfilepath are automatically assigned however the values can be overidden by setting the values here.\n";
			$tmp_config .= "		\$dbfilename = '".$db_filename."'; //host name/ip address + '.db' is the default database filename\n";
			$tmp_config .= "		\$dbfilepath = '".$db_filepath."'; //the path is determined by a php variable\n";
		}
		$tmp_config .= "\n";
		$tmp_config .= "	//mysql: database connection information\n";
		if ($db_type == "mysql") {
			if ($db_host == "localhost") {
				//if localhost is used it defaults to a Unix Socket which doesn't seem to work.
				//replace localhost with 127.0.0.1 so that it will connect using TCP
				$db_host = "127.0.0.1";
			}
			$tmp_config .= "		\$dbhost = '".$db_host."';\n";
			$tmp_config .= "		\$dbport = '".$db_port."';\n";
			$tmp_config .= "		\$dbname = '".$db_name."';\n";
			$tmp_config .= "		\$dbusername = '".$db_username."';\n";
			$tmp_config .= "		\$dbpassword = '".$db_password."';\n";
		}
		else {
			$tmp_config .= "		//\$dbhost = '';\n";
			$tmp_config .= "		//\$dbport = '';\n";
			$tmp_config .= "		//\$dbname = '';\n";
			$tmp_config .= "		//\$dbusername = '';\n";
			$tmp_config .= "		//\$dbpassword = '';\n";
		}
		$tmp_config .= "\n";
		$tmp_config .= "	//pgsql: database connection information\n";
		if ($db_type == "pgsql") {
			$tmp_config .= "		\$dbhost = '".$db_host."'; //set the host only if the database is not local\n";
			$tmp_config .= "		\$dbport = '".$db_port."';\n";
			$tmp_config .= "		\$dbname = '".$db_name."';\n";
			$tmp_config .= "		\$dbusername = '".$db_username."';\n";
			$tmp_config .= "		\$dbpassword = '".$db_password."';\n";
		}
		else {
			$tmp_config .= "		//\$dbhost = '".$db_host."'; //set the host only if the database is not local\n";
			$tmp_config .= "		//\$dbport = '".$db_port."';\n";
			$tmp_config .= "		//\$dbname = '".$db_name."';\n";
			$tmp_config .= "		//\$dbusername = '".$db_username."';\n";
			$tmp_config .= "		//\$dbpassword = '".$db_password."';\n";
		}
		$tmp_config .= "\n";
		$tmp_config .= "	//show errors\n";
		$tmp_config .= "		ini_set('display_errors', '1');\n";
		$tmp_config .= "		//error_reporting (E_ALL); // Report everything\n";
		$tmp_config .= "		//error_reporting (E_ALL ^ E_NOTICE); // Report everything\n";
		$tmp_config .= "		error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING ); //hide notices and warnings";
		$tmp_config .= "\n";
		$tmp_config .= "//-----------------------------------------------------\n";
		$tmp_config .= "// warning: do not edit below this line\n";
		$tmp_config .= "//-----------------------------------------------------\n";
		$tmp_config .= "\n";
		$tmp_config .= "	require_once \"includes/lib_php.php\";\n";
		$tmp_config .= "	require \"includes/lib_pdo.php\";\n";
		$tmp_config .= "	require_once \"includes/lib_functions.php\";\n";
		$tmp_config .= "	require_once \"includes/lib_switch.php\";\n";
		$tmp_config .= "\n";
		$tmp_config .= "?>";

		$fout = fopen($_SERVER["DOCUMENT_ROOT"].PROJECT_PATH."/includes/config.php","w");
		fwrite($fout, $tmp_config);
		unset($tmp_config);
		fclose($fout);

		//load data into the database
			if ($db_type == "sqlite") {
				//sqlite database will be created when the config.php is loaded and only if the database file does not exist
				$filename = $_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/includes/install/sql/sqlite.sql';
				$file_contents = file_get_contents($filename);
				unset($filename);
				try {
					$db_tmp = new PDO('sqlite:'.$db_filepath.'/'.$db_filename); //sqlite 3
					//$db_tmp = new PDO('sqlite::memory:'); //sqlite 3
					$db_tmp->beginTransaction();
				}
				catch (PDOException $error) {
					print "error: " . $error->getMessage() . "<br/>";
					die();
				}

				//replace \r\n with \n then explode on \n
					$file_contents = str_replace("\r\n", "\n", $file_contents);

				//loop line by line through all the lines of sql code
					$stringarray = explode("\n", $file_contents);
					$x = 0;
					foreach($stringarray as $sql) {
						try {
							$db_tmp->query($sql);
						}
						catch (PDOException $error) {
							echo "error: " . $error->getMessage() . " sql: $sql<br/>";
							//die();
						}
						$x++;
					}
					unset ($file_contents, $sql);
					$db_tmp->commit();
			}

			//--- begin: create the pgsql database -----------------------------------------
			
			if ($db_type == "pgsql") {

				//echo "DB Name: {$db_name}<br>";
				//echo "DB Host: {$db_host}<br>";
				//echo "DB User: {$db_username}<br>";
				//echo "DB Pass: {$db_password}<br>";
				//echo "DB Port: {$db_port}<br>";
				//echo "DB Create User: {$db_create_username}<br>";
				//echo "DB Create Pass: {$db_create_password}<br>";

				$filename = $_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/includes/install/sql/pgsql.sql';
				$file_contents = file_get_contents($filename);
				//echo "<pre>\n";
				//echo $file_contents;
				//echo "</pre>\n";

				//if $db_create_username provided, attempt to create new PG role and database
					if (strlen($db_create_username) > 0) {
						try {
							if (strlen($db_port) == 0) { $db_port = "5432"; }
							if (strlen($db_host) > 0) {
								$db_tmp = new PDO("pgsql:host={$db_host} port={$db_port} user={$db_create_username} password={$db_create_password} dbname=template1");
							} else {
								$db_tmp = new PDO("pgsql:host=localhost port={$db_port} user={$db_create_username} password={$db_create_password} dbname=template1");
							}
						} catch (PDOException $error) {
							print "error: " . $error->getMessage() . "<br/>";
							die();
						}

						//create the database, user, grant perms
						$db_tmp->exec("CREATE DATABASE {$db_name}");
						$db_tmp->exec("CREATE USER {$db_username} WITH PASSWORD '{$db_password}'");
						$db_tmp->exec("GRANT ALL ON {$db_name} TO {$db_username}");

						//close database connection_aborted
						$db_tmp = null;
					}

				//open database connection with $db_name
					try {
						if (strlen($db_port) == 0) { $db_port = "5432"; }
						if (strlen($db_host) > 0) {
							$db_tmp = new PDO("pgsql:host={$db_host} port={$db_port} dbname={$db_name} user={$db_username} password={$db_password}");
						} else {
							$db_tmp = new PDO("pgsql:host=localhost port={$db_port} user={$db_username} password={$db_password} dbname={$db_name}");
						}
					}
					catch (PDOException $error) {
						print "error: " . $error->getMessage() . "<br/>";
						die();
					}

				//replace \r\n with \n then explode on \n
					$file_contents = str_replace("\r\n", "\n", $file_contents);

				//loop line by line through all the lines of sql code
					$stringarray = explode("\n", $file_contents);
					$x = 0;
					foreach($stringarray as $sql) {
						if (strlen($sql) > 3) {
							try {
								$db_tmp->query($sql);
							}
							catch (PDOException $error) {
								echo "error: " . $error->getMessage() . " sql: $sql<br/>";
								die();
							}
						}
						$x++;
					}
					unset ($file_contents, $sql);
			}
			
			//--- end: create the pgsql database -----------------------------------------


			//--- begin: create the mysql database -----------------------------------------
			if ($db_type == "mysql") {
				$filename = $_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/includes/install/sql/mysql.sql';
				$file_contents = file_get_contents($filename);

				//database connection
					try {
						if (strlen($db_host) == 0 && strlen($db_port) == 0) {
							//if both host and port are empty use the unix socket
							if (strlen($db_create_username) == 0) {
								$db_tmp = new PDO("mysql:host=$db_host;unix_socket=/var/run/mysqld/mysqld.sock;", $db_username, $db_password);
							}
							else {
								$db_tmp = new PDO("mysql:host=$db_host;unix_socket=/var/run/mysqld/mysqld.sock;", $db_create_username, $db_create_password);
							}
						}
						else {
							if (strlen($db_port) == 0) {
								//leave out port if it is empty
								if (strlen($db_create_username) == 0) {
									$db_tmp = new PDO("mysql:host=$db_host;", $db_username, $db_password);
								}
								else {
									$db_tmp = new PDO("mysql:host=$db_host;", $db_create_username, $db_create_password);
								}
							}
							else {
								if (strlen($db_create_username) == 0) {
									$db_tmp = new PDO("mysql:host=$db_host;port=$db_port;", $db_username, $db_password);
								}
								else {
									$db_tmp = new PDO("mysql:host=$db_host;port=$db_port;", $db_create_username, $db_create_password);
								}
							}
						}
						$db_tmp->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
						$db_tmp->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
					}
					catch (PDOException $error) {
						print "error: " . $error->getMessage() . "<br/>";
						//die();
					}

				//select the mysql database
					try {
						$db_tmp->query("USE mysql;");
					}
					catch (PDOException $error) {
						print "error: " . $error->getMessage() . "<br/>";
					}

				//create user and set the permissions
					try {
						$tmp_sql = "CREATE USER '".$db_username."'@'%' IDENTIFIED BY '".$db_password."'; ";
						$db_tmp->query($tmp_sql);
					}
					catch (PDOException $error) {
						print "error: " . $error->getMessage() . "<br/>";
					}

				//set account to unlimitted use
					try {
						$tmp_sql = "GRANT USAGE ON * . * TO '".$db_username."'@'localhost' ";
						$tmp_sql .= "IDENTIFIED BY '".$db_password."' ";
						$tmp_sql .= "WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0; ";
						$db_tmp->query($tmp_sql);
					}
					catch (PDOException $error) {
						print "error: " . $error->getMessage() . "<br/>";
					}

				//create the database and set the create user with permissions
					try {
						$tmp_sql = "CREATE DATABASE IF NOT EXISTS ".$db_name."; ";
						$db_tmp->query($tmp_sql);
					}
					catch (PDOException $error) {
						print "error: " . $error->getMessage() . "<br/>";
					}

				//set user permissions
					try {
						$db_tmp->query("GRANT ALL PRIVILEGES ON ".$db_name.".* TO '".$db_username."'@'%'; ");
					}
					catch (PDOException $error) {
						print "error: " . $error->getMessage() . "<br/>";
					}

				//make the changes active
					try {
						$tmp_sql = "FLUSH PRIVILEGES; ";
						$db_tmp->query($tmp_sql);
					}
					catch (PDOException $error) {
						//print "error: " . $error->getMessage() . "<br/>";
					}

				//select the database
					try {
						$db_tmp->query("USE ".$db_name.";");
					}
					catch (PDOException $error) {
						//print "error: " . $error->getMessage() . "<br/>";
					}

				//include sets the database connection
					require "includes/lib_pdo.php";

				//set group permissions for the install
					//$_SESSION["groups"] = '||admin||member||superadmin||';

				//load the default database into memory and compare it with the active database
					//$display_results = false;
					//require_once "includes/lib_schema.php";
					//db_upgrade_schema ($db, $display_results);

				//add the defaults data into the database

				//replace \r\n with \n then explode on \n
					$file_contents = str_replace("\r\n", "\n", $file_contents);

				//loop line by line through all the lines of sql code
					$stringarray = explode("\n", $file_contents);
					$x = 0;
					foreach($stringarray as $sql) {
						if (strlen($sql) > 3) {
							try {
								$db_tmp->query($sql);
							}
							catch (PDOException $error) {
								//echo "error on line $x: " . $error->getMessage() . " sql: $sql<br/>";
								//die();
							}
						}
						$x++;
					}
					unset ($file_contents, $sql);
			}
			//--- end: create the mysql database -----------------------------------------


	//set system settings paths
		$v_package_version = '1.0.8';
		$v_build_version = '1.0.6';
		$v_build_revision = 'Release';
		$v_label = 'FusionPBX';
		$v_name = 'freeswitch';
		$v_web_dir = $_SERVER["DOCUMENT_ROOT"];
		$v_web_root = $_SERVER["DOCUMENT_ROOT"];
		if (is_dir($_SERVER["DOCUMENT_ROOT"].'/fusionpbx')){ $v_relative_url = $_SERVER["DOCUMENT_ROOT"].'/fusionpbx'; } else { $v_relative_url = '/'; }

		$sql = "update v_system_settings set ";
		$sql .= "php_dir = '$install_php_dir', ";
		$sql .= "tmp_dir = '$install_tmp_dir', ";
		$sql .= "bin_dir = '$v_bin_dir', ";
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
		$sql .= "v_grammar_dir = '$v_grammar_dir', ";
		$sql .= "v_storage_dir = '$v_storage_dir', ";
		$sql .= "v_voicemail_dir = '$v_voicemail_dir', ";
		$sql .= "v_recordings_dir = '$v_recordings_dir', ";
		$sql .= "v_sounds_dir = '$v_sounds_dir', ";
		$sql .= "v_download_path = '$v_download_path' ";
		//$sql .= "v_provisioning_tftp_dir = '$v_provisioning_tftp_dir', ";
		//$sql .= "v_provisioning_ftp_dir = '$v_provisioning_ftp_dir', ";
		//$sql .= "v_provisioning_https_dir = '$v_provisioning_https_dir', ";
		//$sql .= "v_provisioning_http_dir = '$v_provisioning_http_dir' ";
		$sql .= "where v_id = '$v_id' ";
		$db_tmp->exec($sql);
		unset($sql);

	//unset the temporary database connection
		unset($db_tmp);

	//include the new config.php file
		require "includes/config.php";

	//remove the default config files that are not needed
		$file = $v_extensions_dir."/brian.xml"; if (file_exists($file)) { unlink($file); }
		$file = $v_extensions_dir."/example.com.xml"; if (file_exists($file)) { unlink($file); }
		$file = $v_dialplan_default_dir."/99999_enum.xml"; if (file_exists($file)) { unlink($file); }
		$file = $v_dialplan_default_dir."/01_example.com.xml"; if (file_exists($file)) { unlink($file); }
		$file = $v_dialplan_public_dir."/00_inbound_did.xml"; if (file_exists($file)) { unlink($file); }
		unset($file);

	//prepare switch.conf.xml for voicemail to email
		$filename = $v_conf_dir."/autoload_configs/switch.conf.xml";
		$handle = fopen($filename,"rb");
		$contents = fread($handle, filesize($filename));
		fclose($handle);

		$handle = fopen($filename,"w");
		if (file_exists($install_php_dir.'/php')) { $install_php_bin = 'php'; }
		if (file_exists($install_php_dir.'/php.exe')) { $install_php_bin = 'php.exe'; }
		$contents = str_replace("<param name=\"mailer-app\" value=\"sendmail\"/>", "<!--<param name=\"mailer-app\" value=\"sendmail\"/>-->\n<param name=\"mailer-app\" value=\"".$install_php_dir."/".$install_php_bin."\"/>", $contents);
		$contents = str_replace("<param name=\"mailer-app-args\" value=\"-t\"/>", "<!--<param name=\"mailer-app-args\" value=\"-t\"/>-->\n<param name=\"mailer-app-args\" value=\"".$v_web_dir."/v_mailto.php\"/>", $contents);
		fwrite($handle, $contents);
		fclose($handle);
		unset($contents);
		unset($filename);

	//create the necessary directories
		if (!is_dir($install_tmp_dir)) { mkdir($install_tmp_dir,0777,true); }
		if (!is_dir($install_v_backup_dir)) { mkdir($install_v_backup_dir,0777,true); }
		if (!is_dir($install_v_dir.'/sounds/en/us/callie/custom/8000')) { mkdir($install_v_dir.'/sounds/en/us/callie/custom/8000',0777,true); }
		if (!is_dir($install_v_dir.'/sounds/en/us/callie/custom/16000')) { mkdir($install_v_dir.'/sounds/en/us/callie/custom/16000',0777,true); }
		if (!is_dir($install_v_dir.'/sounds/en/us/callie/custom/32000')) { mkdir($install_v_dir.'/sounds/en/us/callie/custom/32000',0777,true); }
		if (!is_dir($install_v_dir.'/sounds/en/us/callie/custom/48000')) { mkdir($install_v_dir.'/sounds/en/us/callie/custom/48000',0777,true); }
		if (!is_dir($v_storage_dir.'/fax/')) { mkdir($v_storage_dir.'/fax',0777,true); }
		if (!is_dir($v_log_dir.'')) { mkdir($v_log_dir.'',0777,true); }
		if (!is_dir($v_sounds_dir.'')) { mkdir($v_sounds_dir.'',0777,true); }
		if (!is_dir($v_recordings_dir.'')) { mkdir($v_recordings_dir.'',0777,true); }

	//generate dialplan.xml
		$srcfile = $_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/includes/install/dialplan/default.xml';
		$destfile = $v_conf_dir.'/dialplan/default.xml';
		if (file_exists($destfile)) { unlink($destfile); }
		if (!copy($srcfile, $destfile)) {
			unlink($_SERVER["DOCUMENT_ROOT"].PROJECT_PATH."/includes/config.php");
			echo "failed to copy $srcfile to $destfile\n";
			exit;
		}
		unset($srcfile, $destfile);

	//copy the files and directories from includes/install
		include "includes/lib_install_copy.php";

	//create the switch.conf.xml file
		switch_conf_xml();

	//synchronize the config with the saved settings
		sync_package_freeswitch();

	//redirect to the login page
		$msg = "</strong><br />\n";
		$msg .= "Installation is complete. <br />";
		$msg .= "<br /> ";
		$msg .= "<strong>Getting Started:</strong><br /> ";
		$msg .= "<ul><li>There are two levels of admins 1. superadmin 2. admin.<br />";
		$msg .= "<br />\n";
		$msg .= "username: <strong>superadmin</strong> <br />password: <strong>fusionpbx</strong> <br />\n";
		$msg .= "<br />\n";
		$msg .= "username: <strong>admin</strong> <br />password: <strong>fusionpbx</strong> <br/><br/>\n";
		$msg .= "</li>\n";
		$msg .= "<li>\n";
		$msg .= "After making changes to settings in the interface use the menu to '<strong>apply settings</strong>' or go click on '<strong>reloadxml</strong>' from the status page.<br /><br />\n";
		$msg .= "</li>\n";
		$msg .= "<li>\n";
		$msg .= "The database connection settings have been saved to ".$_SERVER["DOCUMENT_ROOT"].PROJECT_PATH."/includes/config.php.<br />\n";
		$msg .= "</li>\n";
		$msg .= "</ul><strong>\n";
		header("Location: ".PROJECT_PATH."/login.php?msg=".urlencode($msg));

}

//set a default template
	if (strlen($_SESSION["template_name"]) == 0) { $_SESSION["template_name"] = 'default'; }

//get the contents of the template and save it to the template variable
	$template = file_get_contents($_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/themes/'.$_SESSION["template_name"].'/template.php');

//buffer the content
	ob_end_clean(); //clean the buffer
	ob_start();

//--- begin: content ---------------------------------------------
	if (!is_writable($_SERVER["DOCUMENT_ROOT"].PROJECT_PATH."/includes/header.php")) {
		$installmsg .= "<li>Write access to ".$_SERVER["DOCUMENT_ROOT"].PROJECT_PATH."/includes/ is required during the install.</li>\n";
	}
	if (!is_writable($install_v_dir)) {
		$installmsg .= "<li>Write access to the 'FreeSWITCH Directory' and most of its sub directories is required.</li>\n";
	}
	if (!extension_loaded('PDO')) {
		$installmsg .= "<li>PHP PDO was not detected. Please install it before proceeding.</li>";
	}

	if ($installmsg) {
		echo "<br />\n";
		echo "<div align='center'>\n";
		echo "<table width='75%'>\n";
		echo "<tr>\n";
		echo "<th align='left'>Message</th>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "<td class='rowstyle1'><strong><ul>$installmsg</ul></strong></td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		echo "</div>\n";
	  //print_info_box($installmsg);
	}

	echo "<div align='center'>\n";
	$msg = '';
	//make sure the includes directory is writable so the config.php file can be written.
		if (!is_writable($_SERVER["DOCUMENT_ROOT"].PROJECT_PATH."/includes/lib_pdo.php")) {
			$msg .= "<b>Write access to ".$_SERVER["DOCUMENT_ROOT"].PROJECT_PATH."</b><br />";
			$msg .= "and its sub-directories are required during the install.<br /><br />\n";
		}

	//display the message
		if (strlen($msg) > 0) {
			//echo "not writable";
			echo $msg;
			echo "<br />\n";
			echo "<br />\n";
			unset($msg);
			//exit;
		}


/*
pgsql
		document.getElementById("desc_db_type").innerHTML = "Choose the database where the settings will be stored. <br /><b>Note: Before proceeding use the <a href='<?php echo PROJECT_PATH; ?>/includes/install/sql/pgsql.sql' target='_blank'>pgsql.sql</a> script to setup the database.</b>";

		document.getElementById("desc_db_filename").innerHTML = "Not applicable"; document.frm.db_filename.value = ''; document.frm.db_filename.disabled = true;
		document.getElementById("desc_install_secure_dir").innerHTML = "Path to the secure folder that contains PHP command line scripts.";
		document.getElementById("desc_db_host").innerHTML = "Optional for PostgreSQL when the database is local."; document.frm.db_host.disabled = false;
		document.getElementById("desc_db_port").innerHTML = "Optional if the database is using the default port."; document.frm.db_port.disabled = false; 
		document.getElementById("desc_db_name").innerHTML = "Required for PostgreSQL."; document.frm.db_name.value = 'fusionpbx'; document.frm.db_name.disabled = false;
		document.getElementById("desc_db_username").innerHTML = "Required for PostgreSQL."; document.frm.db_username.disabled = false;
		document.getElementById("desc_db_password").innerHTML = "Required for PostgreSQL."; document.frm.db_password.disabled = false;
*/

// begin step 1 --------------------------------------
	if ($_POST["install_step"] == "") {
		echo "<form method='post' name='frm' action=''>\n";
		echo "<table width='100%'  border='0' cellpadding='6' cellspacing='0'>\n";

		//echo "<tr>\n";
		//echo "<td colspan='2' align='left' width='30%' nowrap><b>Installation</b></td>\n";
		//echo "</tr>\n";
		echo "<tr>\n";
		echo "<td colspan='2' width='100%' align='left'>\n";
		echo "	<strong>The installation is a simple two step process.</strong> \n";
		echo "	<ul>\n";
		echo "	<li>Step 1 is used for selecting the database engine to use. After making that section then ensure the paths are correct and then press next. </li> ";
		echo "	<li>Step 2 requests the database specific settings. When finished press save. The installation will then complete the tasks required to do the install. </li></td>\n";
		echo "	</ul>\n";
		//echo "<td width='70%' align='right'><input type='button' class='btn' name='' alt='back' onclick=\"window.location='v_dialplan_includes_edit.php?id=".$dialplan_include_id."'\" value='Back'></td>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<td align='left' width='30%' nowrap><b>Step 1</b></td>\n";
		echo "<td width='70%' align='right'>&nbsp;</td>\n";
		//echo "<td width='70%' align='right'><input type='button' class='btn' name='' alt='back' onclick=\"window.location='v_dialplan_includes_edit.php?id=".$dialplan_include_id."'\" value='Back'></td>\n";
		echo "</tr>\n";

		$db_type = $_POST["db_type"];
		$install_step = $_POST["install_step"];

		echo "<tr>\n";
		echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
		echo "	Database Type:\n";
		echo "</td>\n";
		echo "<td class='vtable' align='left'>\n";
		echo "	<select name='db_type' id='db_type' class='formfld' id='form_tag' onchange='db_type_onchange();'>\n";
		if (extension_loaded('pdo_pgsql')) {	echo "	<option value='pgsql'>postgresql</option>\n"; }
		if (extension_loaded('pdo_mysql')) {	echo "	<option value='mysql'>mysql</option>\n"; }
		if (extension_loaded('pdo_sqlite')) {	echo "	<option  value='sqlite' selected>sqlite</option>\n"; } //set sqlite as the default
		echo "	</select><br />\n";
		echo "		Select the database type.\n";
		echo "\n";
		echo "</td>\n";
		echo "</tr>\n";

		//echo "<tr>\n";
		//echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
		//echo "		Secure Directory:\n";
		//echo "</td>\n";
		//echo "<td class='vtable' align='left'>\n";
		//echo "		<input class='formfld' type='text' name='install_secure_dir' maxlength='255' value=\"$install_secure_dir\"><br />\n";
		//echo "		Path to the secure directory that contains PHP command line scripts.\n";
		//echo "\n";
		//echo "</td>\n";
		//echo "</tr>\n";

		echo "<tr>\n";
		echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
		echo "	FreeSWITCH Directory:\n";
		echo "</td>\n";
		echo "<td class='vtable' align='left'>\n";
		echo "	<input class='formfld' type='text' name='install_v_dir' maxlength='255' value=\"$install_v_dir\">\n";
		echo "<br />\n";
		echo "Enter the FreeSWITCH directory path.\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
		echo "	PHP Directory:\n";
		echo "</td>\n";
		echo "<td class='vtable' align='left'>\n";
		echo "	<input class='formfld' type='text' name='install_php_dir' maxlength='255' value=\"$install_php_dir\"><br />\n";
		echo "	Enter the path to PHP's bin or executable directory.<br />\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "	<tr>\n";
		echo "		<td colspan='2' align='right'>\n";
		echo "			<input type='hidden' name='install_tmp_dir' value='$install_tmp_dir'>\n";
		echo "			<input type='hidden' name='install_v_backup_dir' value='$install_v_backup_dir'>\n";
		echo "			<input type='hidden' name='install_step' value='2'>\n";
		echo "			<input type='submit' name='submit' class='btn' value='Next'>\n";
		echo "		</td>\n";
		echo "	</tr>";

		echo "</table>";
		echo "</form>";
		echo "</div>";
	}
// end step 1 --------------------------------------


// begin step 2, sqlite --------------------------------------
	if ($_POST["install_step"] == "2" && $_POST["db_type"] == "sqlite") {

		echo "<form method='post' name='frm' action=''>\n";
		echo "<table width='100%'  border='0' cellpadding='6' cellspacing='0'>\n";

		echo "<tr>\n";
		echo "<td align='left' width='30%' nowrap><b>Installation: Step 2 - SQLite</b></td>\n";
		echo "<td width='70%' align='right'>&nbsp;</td>\n";
		//echo "<td width='70%' align='right'><input type='button' class='btn' name='' alt='back' onclick=\"window.location='v_dialplan_includes_edit.php?id=".$dialplan_include_id."'\" value='Back'></td>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<td class='vncell' 'valign='top' align='left' nowrap>\n";
		echo "	Database Filename:\n";
		echo "</td>\n";
		echo "<td class='vtable' align='left'>\n";
		echo "	<input class='formfld' type='text' name='db_filename' maxlength='255' value=\"$db_filename\"><br />\n";
		echo "	Default: fusiopbx.db. If the field is left empty then the file name is determined by the host or IP address.\n";
		echo "\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<td class='vncell' 'valign='top' align='left' nowrap>\n";
		echo "	Database Directory:\n";
		echo "</td>\n";
		echo "<td class='vtable' align='left'>\n";
		echo "	<input class='formfld' type='text' name='db_filepath' maxlength='255' value=\"$db_filepath\"><br />\n";
		echo "	Set the path to the database directory.\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "	<tr>\n";
		echo "		<td colspan='2' align='right'>\n";
		echo "			<input type='hidden' name='db_type' value='$db_type'>\n";
		echo "			<input type='hidden' name='install_secure_dir' value='$install_secure_dir'>\n";
		echo "			<input type='hidden' name='install_v_dir' value='$install_v_dir'>\n";
		echo "			<input type='hidden' name='install_php_dir' value='$install_php_dir'>\n";
		echo "			<input type='hidden' name='install_tmp_dir' value='$install_tmp_dir'>\n";
		echo "			<input type='hidden' name='install_v_backup_dir' value='$install_v_backup_dir'>\n";
		echo "			<input type='hidden' name='install_step' value='3'>\n";
		echo "			<input type='submit' name='submit' class='btn' value='Next'>\n";
		echo "		</td>\n";
		echo "	</tr>";

		echo "</table>";
		echo "</form>";
		echo "</div>";
	}


// begin step 2, mysql --------------------------------------
	if ($_POST["install_step"] == "2" && $_POST["db_type"] == "mysql") {

		//set defaults
			if (strlen($db_host) == 0) { $db_host = 'localhost'; }
			if (strlen($db_port) == 0) { $db_port = '3306'; }
			//if (strlen($db_name) == 0) { $db_name = 'fusionpbx'; }

		//echo "However if preferred the database can be created manually with the <a href='". echo PROJECT_PATH; ."/includes/install/sql/mysql.sql' target='_blank'>mysql.sql</a> script. ";

		echo "<form method='post' name='frm' action=''>\n";
		echo "<table width='100%'  border='0' cellpadding='6' cellspacing='0'>\n";

		echo "<tr>\n";
		echo "<td align='left' width='30%' nowrap><b>Installation: Step 2 - MySQL</b></td>\n";
		echo "<td width='70%' align='right'>&nbsp;</td>\n";
		//echo "<td width='70%' align='right'><input type='button' class='btn' name='' alt='back' onclick=\"window.location='v_dialplan_includes_edit.php?id=".$dialplan_include_id."'\" value='Back'></td>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
		echo "		Database Host:\n";
		echo "</td>\n";
		echo "<td class='vtable' align='left'>\n";
		echo "		<input class='formfld' type='text' name='db_host' maxlength='255' value=\"$db_host\"><br />\n";
		echo "		Enter the host address for the database server.\n";
		echo "\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<td class='vncell' valign='top' align='left' nowrap>\n";
		echo "		Database Port:\n";
		echo "</td>\n";
		echo "<td class='vtable' align='left'>\n";
		echo "		<input class='formfld' type='text' name='db_port' maxlength='255' value=\"$db_port\"><br />\n";
		echo "		Enter the port number. It is optional if the database is using the default port.\n";
		echo "\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
		echo "		Database Name:\n";
		echo "</td>\n";
		echo "<td class='vtable' align='left'>\n";
		echo "		<input class='formfld' type='text' name='db_name' maxlength='255' value=\"$db_name\"><br />\n";
		echo "		Enter the name of the database.\n";
		echo "\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
		echo "		Database Username:\n";
		echo "</td>\n";
		echo "<td class='vtable' align='left'>\n";
		echo "		<input class='formfld' type='text' name='db_username' maxlength='255' value=\"$db_username\"><br />\n";
		echo "		Enter the database username. \n";
		echo "\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
		echo "		Database Password:\n";
		echo "</td>\n";
		echo "<td class='vtable' align='left'>\n";
		echo "		<input class='formfld' type='text' name='db_password' maxlength='255' value=\"$db_password\"><br />\n";
		echo "		Enter the database password.\n";
		echo "\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<td class='vncell' valign='top' align='left' nowrap>\n";
		echo "		Create Database Username:\n";
		echo "</td>\n";
		echo "<td class='vtable' align='left'>\n";
		echo "		<input class='formfld' type='text' name='db_create_username' maxlength='255' value=\"$db_create_username\"><br />\n";
		echo "		Optional, this username is used to create the database, a database user and set the permissions. \n";
		echo "		By default this username is 'root' however it can be any account with permission to add a database, user, and grant permissions. \n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<td class='vncell' valign='top' align='left' nowrap>\n";
		echo "		Create Database Password:\n";
		echo "</td>\n";
		echo "<td class='vtable' align='left'>\n";
		echo "		<input class='formfld' type='text' name='db_create_password' maxlength='255' value=\"$db_create_password\"><br />\n";
		echo "		Enter the create database password.\n";
		echo "\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "	<tr>\n";
		echo "		<td colspan='2' align='right'>\n";
		echo "			<input type='hidden' name='db_type' value='$db_type'>\n";
		echo "			<input type='hidden' name='install_secure_dir' value='$install_secure_dir'>\n";
		echo "			<input type='hidden' name='install_v_dir' value='$install_v_dir'>\n";
		echo "			<input type='hidden' name='install_php_dir' value='$install_php_dir'>\n";
		echo "			<input type='hidden' name='install_tmp_dir' value='$install_tmp_dir'>\n";
		echo "			<input type='hidden' name='install_v_backup_dir' value='$install_v_backup_dir'>\n";
		echo "			<input type='hidden' name='install_step' value='3'>\n";
		echo "			<input type='submit' name='submit' class='btn' value='Next'>\n";
		echo "		</td>\n";
		echo "	</tr>";

		echo "</table>";
		echo "</form>";
		echo "</div>";
	}

	// begin step 2, pgsql --------------------------------------
	if ($_POST["install_step"] == "2" && $_POST["db_type"] == "pgsql") {
		if (strlen($db_host) == 0) { $db_host = 'localhost'; }
		if (strlen($db_port) == 0) { $db_port = '5432'; }
		if (strlen($db_create_username) == 0) { $db_create_username = 'pgsql'; }
		//if (strlen($db_name) == 0) { $db_name = 'fusionpbx'; }

		echo "<form method='post' name='frm' action=''>\n";
		echo "<table width='100%'  border='0' cellpadding='6' cellspacing='0'>\n";

		echo "<tr>\n";
		echo "<td align='left' width='30%' nowrap><b>Installation: Step 2 - Postgres</b></td>\n";
		echo "<td width='70%' align='right'>&nbsp;</td>\n";
		//echo "<td width='70%' align='right'><input type='button' class='btn' name='' alt='back' onclick=\"window.location='v_dialplan_includes_edit.php?id=".$dialplan_include_id."'\" value='Back'></td>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
		echo "		Database Host:\n";
		echo "</td>\n";
		echo "<td class='vtable' align='left'>\n";
		echo "		<input class='formfld' type='text' name='db_host' maxlength='255' value=\"$db_host\"><br />\n";
		echo "		Enter the host address for the database server.\n";
		echo "\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<td class='vncell' valign='top' align='left' nowrap>\n";
		echo "		Database Port:\n";
		echo "</td>\n";
		echo "<td class='vtable' align='left'>\n";
		echo "		<input class='formfld' type='text' name='db_port' maxlength='255' value=\"$db_port\"><br />\n";
		echo "		Enter the port number. It is optional if the database is using the default port.\n";
		echo "\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
		echo "		Database Name:\n";
		echo "</td>\n";
		echo "<td class='vtable' align='left'>\n";
		echo "		<input class='formfld' type='text' name='db_name' maxlength='255' value=\"$db_name\"><br />\n";
		echo "		Enter the name of the database.\n";
		echo "\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
		echo "		Database Username:\n";
		echo "</td>\n";
		echo "<td class='vtable' align='left'>\n";
		echo "		<input class='formfld' type='text' name='db_username' maxlength='255' value=\"$db_username\"><br />\n";
		echo "		Enter the database username.\n";
		echo "\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
		echo "		Database Password:\n";
		echo "</td>\n";
		echo "<td class='vtable' align='left'>\n";
		echo "		<input class='formfld' type='text' name='db_password' maxlength='255' value=\"$db_password\"><br />\n";
		echo "		Enter the database password.\n";
		echo "\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<td class='vncell' valign='top' align='left' nowrap>\n";
		echo "		Create Database Username:\n";
		echo "</td>\n";
		echo "<td class='vtable' align='left'>\n";
		echo "		<input class='formfld' type='text' name='db_create_username' maxlength='255' value=\"$db_create_username\"><br />\n";
		echo "		Optional, this username is used to create the database, a database user and set the permissions. \n";
		echo "		By default this username is 'pgsql' however it can be any account with permission to add a database, user, and grant permissions. \n";
		echo "		Leave blank if the user and empty database already exist and you do not want them created. \n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<td class='vncell' valign='top' align='left' nowrap>\n";
		echo "		Create Database Password:\n";
		echo "</td>\n";
		echo "<td class='vtable' align='left'>\n";
		echo "		<input class='formfld' type='text' name='db_create_password' maxlength='255' value=\"$db_create_password\"><br />\n";
		echo "		Enter the create database password.\n";
		echo "\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "	<tr>\n";
		echo "		<td colspan='2' align='right'>\n";
		echo "			<input type='hidden' name='db_type' value='$db_type'>\n";
		echo "			<input type='hidden' name='install_secure_dir' value='$install_secure_dir'>\n";
		echo "			<input type='hidden' name='install_v_dir' value='$install_v_dir'>\n";
		echo "			<input type='hidden' name='install_php_dir' value='$install_php_dir'>\n";
		echo "			<input type='hidden' name='install_tmp_dir' value='$install_tmp_dir'>\n";
		echo "			<input type='hidden' name='install_v_backup_dir' value='$install_v_backup_dir'>\n";
		echo "			<input type='hidden' name='install_step' value='3'>\n";
		echo "			<input type='submit' name='submit' class='btn' value='Install'>\n";
		echo "		</td>\n";
		echo "	</tr>";

		echo "</table>";
		echo "</form>";
		echo "</div>";
	}

	echo "<br />\n";
	echo "<br />\n";
	echo "<br />\n";
	echo "<br />\n";
	echo "<br />\n";
	echo "<br />\n";
	echo "<br />\n";
	echo "<br />\n";

	echo "<br />\n";
	echo "<br />\n";
	echo "<br />\n";
	echo "<br />\n";
	echo "<br />\n";
	echo "<br />\n";
	echo "<br />\n";
	echo "<br />\n";

// add the content to the template and then send output -----------------------
	$body = $content_from_db.ob_get_contents(); //get the output from the buffer
	ob_end_clean(); //clean the buffer
	ob_start();

	$template = $strheadertop.$template;
	eval('?>' . $template . '<?php ');
	$template = ob_get_contents(); //get the output from the buffer
	ob_end_clean(); //clean the buffer
	$customhead = $customhead.$templatemenucss;
	//$customhead ='test';
	//$output = str_replace ("\r\n", "<br>", $output);
	$output = str_replace ("<!--{title}-->", $customtitle, $template); //<!--{title}--> defined in each individual page
	$output = str_replace ("<!--{head}-->", $customhead, $output); //<!--{head}--> defined in each individual page
	$output = str_replace ("<!--{menu}-->", $_SESSION["menu"], $output); //defined in /includes/menu.php
	$output = str_replace ("<!--{project_path}-->", PROJECT_PATH, $output); //defined in /includes/menu.php

	$pos = strrpos($output, "<!--{body}-->");
	if ($pos === false) {
		$output = $body; //if tag not found just show the body
	}
	else {
		//replace the body
		$output = str_replace ("<!--{body}-->", $body, $output);
	}

	echo $output;
	unset($output);
?>