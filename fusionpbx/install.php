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
	$install_tmp_dir = '';
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
			$v_startup_script_dir = '/usr/local/share/fusionpbx_secure';
			$install_php_dir = '/usr/local/bin';
			break;
		case "NetBSD":
			$v_startup_script_dir = '/usr/local/share/fusionpbx_secure';
			$install_php_dir = '/usr/local/bin';
			break;
		case "OpenBSD":
			$v_startup_script_dir = '/usr/local/share/fusionpbx_secure';
			$install_php_dir = '/usr/local/bin';
			break;
		default:
			$v_startup_script_dir = '';
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


if (count($_POST)>0 && strlen($_POST["persistformvar"]) == 0) {

	$msg = '';
	$dbtype = $_POST["dbtype"];
	$dbfilename = $_POST["dbfilename"];
	$dbfilepath = $_POST["dbfilepath"];
	$dbhost = $_POST["dbhost"];
	$dbport = $_POST["dbport"];
	$dbname = $_POST["dbname"];
	$dbusername = $_POST["dbusername"];
	$dbpassword = $_POST["dbpassword"];
	$install_v_dir = realpath($_POST["install_v_dir"]);
	$install_v_dir = str_replace("\\", "/", $install_v_dir);

	$install_php_dir = realpath($_POST["install_php_dir"]);
	$install_php_dir = str_replace("\\", "/", $install_php_dir);

	$install_tmp_dir = realpath($_POST["install_tmp_dir"]);
	$install_tmp_dir = str_replace("\\", "/", $install_tmp_dir);

	$install_v_backup_dir = realpath($_POST["install_v_backup_dir"]);
	$install_v_backup_dir = str_replace("\\", "/", $install_v_backup_dir);

	//check for all required data
		if (strlen($dbtype) == 0) { $msg .= "Please provide the Database Type<br>\n"; }
		if (strlen($install_v_dir) == 0) { $msg .= "Please provide the Switch Directory<br>\n"; }
		if (strlen($install_php_dir) == 0) { $msg .= "Please provide the PHP Directory<br>\n"; }
		if (strlen($install_tmp_dir) == 0) { $msg .= "Please provide the Temp Directory<br>\n"; }
		if (strlen($install_v_backup_dir) == 0) { $msg .= "Please provide the Backup Directory<br>\n"; }
		if (!is_writable($install_v_dir."/conf/vars.xml")) {
			if (stristr(PHP_OS, 'WIN')) { 
				//some windows operating systems report read only but are writable
			}
			else {
				$msg .= "<b>Write access to ".$install_v_dir." and its sub-directories is required.</b><br />\n";
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
		$tmp_config .= "		\$dbtype = '".$dbtype."'; //sqlite, mysql, pgsql, others with a manually created PDO connection\n";
		$tmp_config .= "\n";
		if ($dbtype == "sqlite") {
			$tmp_config .= "	//sqlite: the dbfilename and dbfilepath are automatically assigned however the values can be overidden by setting the values here.\n";
			$tmp_config .= "		\$dbfilename = '".$dbfilename."'; //host name/ip address + '.db' is the default database filename\n";
			$tmp_config .= "		\$dbfilepath = '".$dbfilepath."'; //the path is determined by a php variable\n";
		}
		$tmp_config .= "\n";
		$tmp_config .= "	//mysql: database connection information\n";
		if ($dbtype == "mysql") {
			if ($dbhost == "localhost") {
				//if localhost is used it defaults to a Unix Socket which doesn't seem to work.
				//replace localhost with 127.0.0.1 so that it will connect using TCP
				$dbhost = "127.0.0.1";
			}
			$tmp_config .= "		\$dbhost = '".$dbhost."';\n";
			$tmp_config .= "		\$dbport = '".$dbport."';\n";
			$tmp_config .= "		\$dbname = '".$dbname."';\n";
			$tmp_config .= "		\$dbusername = '".$dbusername."';\n";
			$tmp_config .= "		\$dbpassword = '".$dbpassword."';\n";
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
		if ($dbtype == "pgsql") {
			$tmp_config .= "		\$dbhost = '".$dbhost."'; //set the host only if the database is not local\n";
			$tmp_config .= "		\$dbport = '".$dbport."';\n";
			$tmp_config .= "		\$dbname = '".$dbname."';\n";
			$tmp_config .= "		\$dbusername = '".$dbusername."';\n";
			$tmp_config .= "		\$dbpassword = '".$dbpassword."';\n";
		}
		else {
			$tmp_config .= "		//\$dbhost = '".$dbhost."'; //set the host only if the database is not local\n";
			$tmp_config .= "		//\$dbport = '".$dbport."';\n";
			$tmp_config .= "		//\$dbname = '".$dbname."';\n";
			$tmp_config .= "		//\$dbusername = '".$dbusername."';\n";
			$tmp_config .= "		//\$dbpassword = '".$dbpassword."';\n";
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

		//copy the secure directory
			$srcdir = $_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/secure';
			//if the directory already exists do not copy over it.
			if (!is_dir($dbfilepath)) {
				//only copy if the src and dest are different
				if ($srcdir != $dbfilepath) {
					if (!is_dir($dbfilepath)) { mkdir($dbfilepath,0777,true); }
					recursive_copy($srcdir, $dbfilepath);
				}
			}
			unset($srcdir);

		$fout = fopen($_SERVER["DOCUMENT_ROOT"].PROJECT_PATH."/includes/config.php","w");
		fwrite($fout, $tmp_config);
		unset($tmp_config);
		fclose($fout);

		//load data into the database
			if ($dbtype == "sqlite") {
				//sqlite database will be created when the config.php is loaded and only if the database file does not exist
			}

			//--- begin: create the pgsql database -----------------------------------------
			/*
			if ($dbtype == "pgsql") {

				$filename = $_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/includes/install/sql/pgsql.sql';
				$file_contents = file_get_contents($filename);
				//echo "<pre>\n";
				//echo $file_contents;
				//echo "</pre>\n";

				//database connection
					try {
						if (strlen($dbhost) > 0) {
							if (strlen($dbport) == 0) { $dbport = "5432"; }
							$dbsql = new PDO("pgsql:host=localhost port=5432 user=$dbusername password=$dbpassword");
						}
						else {
							$dbsql = new PDO("pgsql:user=$dbusername password=$dbpassword");
						}
					}
					catch (PDOException $error) {
						print "error: " . $error->getMessage() . "<br/>";
						die();
					}

					//create the database
						$sql = "";
						$sql .= "CREATE DATABASE $dbname; ";
						//echo $sql;
						$dbsql->query($sql);
						unset($sql);

					//close database connection_aborted
						$dbsql = null;

					//open database connection with $dbname
						try {
							if (strlen($dbhost) > 0) {
								if (strlen($dbport) == 0) { $dbport = "5432"; }
								$dbsql = new PDO("pgsql:host=localhost port=5432 dbname=$dbname user=$dbusername password=$dbpassword");
							}
							else {
								$dbsql = new PDO("pgsql:dbname=$dbname user=$dbusername password=$dbpassword");
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
								$dbsql->query($sql);
							}
							catch (PDOException $error) {
								echo "error: " . $error->getMessage() . " sql: $sql<br/>";
								die();
							}
						}
						$x++;
					}
					unset ($dbsql, $file_contents, $sql);
			}
			*/
			//--- end: create the pgsql database -----------------------------------------


			//--- begin: create the mysql database -----------------------------------------
			if ($dbtype == "mysql") {
				$filename = $_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/includes/install/sql/mysql.sql';
				$file_contents = file_get_contents($filename);
				//echo "<pre>\n";
				//echo $file_contents;
				//echo "</pre>\n";

				//database connection
					try {
						if (strlen($dbhost) == 0 && strlen($dbport) == 0) {
						//if both host and port are empty use the unix socket
						$dbsql = new PDO("mysql:host=$dbhost;unix_socket=/var/run/mysqld/mysqld.sock;", $dbusername, $dbpassword);
					}
					else {
						if (strlen($dbport) == 0) {
							//leave out port if it is empty
							$dbsql = new PDO("mysql:host=$dbhost;", $dbusername, $dbpassword);
						}
						else {
							$dbsql = new PDO("mysql:host=$dbhost;port=$dbport;", $dbusername, $dbpassword);
						}
					}
					$dbsql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				}
				catch (PDOException $error) {
					print "error: " . $error->getMessage() . "<br/>";
					//die();
				}

				//create the database
					try {
						$dbsql->query("CREATE DATABASE $dbname;");
					}
					catch (PDOException $error) {
						//database exists so upgrade the schema
						$display_results = false;
						require_once "core/upgrade/upgrade_schema.php";
						header("Location: ".PROJECT_PATH."/login.php");
						exit;
					}

				//select the database
					$dbsql->query("USE $dbname;");

				//replace \r\n with \n then explode on \n
					$file_contents = str_replace("\r\n", "\n", $file_contents);

				//loop line by line through all the lines of sql code
					$stringarray = explode("\n", $file_contents);
					$x = 0;
					foreach($stringarray as $sql) {
						if (strlen($sql) > 3) {
							try {
								$dbsql->query($sql);
							}
							catch (PDOException $error) {
								//echo "error on line $x: " . $error->getMessage() . " sql: $sql<br/>";
								//die();
							}
						}
						$x++;
					}
					unset ($dbsql, $file_contents, $sql);

			}
			//--- end: create the mysql database -----------------------------------------


		//include the new config.php file
			require_once "includes/config.php";

		//set system settings paths
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
			$v_download_path = 'http://fusionpbx.com/downloads/fusionpbx.tgz';

			$sql = "update v_system_settings set ";
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
			$sql .= "v_sounds_dir = '$v_sounds_dir', ";
			$sql .= "v_download_path = '$v_download_path' ";
			//$sql .= "v_provisioning_tftp_dir = '$v_provisioning_tftp_dir', ";
			//$sql .= "v_provisioning_ftp_dir = '$v_provisioning_ftp_dir', ";
			//$sql .= "v_provisioning_https_dir = '$v_provisioning_https_dir', ";
			//$sql .= "v_provisioning_http_dir = '$v_provisioning_http_dir' ";
			$sql .= "where v_id = '$v_id'";
			$db->exec(check_sql($sql));
			unset($sql);

	//remove the default config files that are not needed
		require_once "includes/config.php";
		$file = $v_conf_dir."/directory/default/brian.xml"; if (file_exists($file)) { unlink($file); }
		$file = $v_conf_dir."/directory/default/example.com.xml"; if (file_exists($file)) { unlink($file); }
		$file = $v_conf_dir."/dialplan/default/99999_enum.xml"; if (file_exists($file)) { unlink($file); }
		$file = $v_conf_dir."/dialplan/default/01_example.com.xml"; if (file_exists($file)) { unlink($file); }
		$file = $v_conf_dir."/dialplan/public/00_inbound_did.xml"; if (file_exists($file)) { unlink($file); }
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

	//prepare shout.conf.xml for mod_shout
		$fout = fopen($v_conf_dir."/autoload_configs/shout.conf.xml","w");
		$tmpxml = "<configuration name=\"shout.conf\" description=\"mod shout config\">\n";
		$tmpxml .= "  <settings>\n";
		$tmpxml .= "    <!-- Don't change these unless you are insane -->\n";
		$tmpxml .= "    <param name=\"decoder\" value=\"i586\"/>\n";
		$tmpxml .= "    <!--<param name=\"volume\" value=\".1\"/>-->\n";
		$tmpxml .= "    <!--<param name=\"outscale\" value=\"8192\"/>-->\n";
		$tmpxml .= "  </settings>\n";
		$tmpxml .= "</configuration>";
		fwrite($fout, $tmpxml);
		unset($tmpxml);
		fclose($fout);

	//create the necessary directories
		if (!is_dir($install_tmp_dir)) { mkdir($install_tmp_dir,0777,true); }
		if (!is_dir($install_v_backup_dir)) { mkdir($install_v_backup_dir,0777,true); }
		if (!is_dir($install_v_dir.'/sounds/custom/8000')) { mkdir($install_v_dir.'/sounds/custom/8000',0777,true); }
		if (!is_dir($v_storage_dir.'/fax/')) { mkdir($v_storage_dir.'/fax',0777,true); }
		if (!is_dir($v_log_dir.'')) { mkdir($v_log_dir.'',0777,true); }
		if (!is_dir($v_log_dir.'/cdr-csv/')) { mkdir($v_log_dir.'/cdr-csv',0777,true); }
		if (!is_dir($v_sounds_dir.'')) { mkdir($v_sounds_dir.'',0777,true); }
		if (!is_dir($v_recordings_dir.'')) { mkdir($v_recordings_dir.'',0777,true); }
		if (!file_exists($v_log_dir.'/cdr-csv/Master.csv')) { 
			touch($v_log_dir.'/cdr-csv/Master.csv'); 
		}
		else {
			//make a backup copy of the old cdr file that is likely incompatible with the sql cdr.
			copy($v_log_dir.'/cdr-csv/Master.csv', $v_log_dir.'/cdr-csv/Master.csv.old');

			//truncate the file now that it has been backed up
			$fh = fopen($v_log_dir.'/cdr-csv/Master.csv', 'w');
			fclose($fh);
		}

	//generate dialplan.xml
		$srcfile = $_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/includes/install/dialplan/default.xml';
		$destfile = $v_conf_dir.'/dialplan/default.xml';
		if (file_exists($destfile)) { unlink($destfile); }
		if (!copy($srcfile, $destfile)) {
			unlink($_SERVER["DOCUMENT_ROOT"].PROJECT_PATH."/includes/config.php");
			echo "failed to copy $srcfile to $destfile...\n";
			exit;
		}
		unset($srcfile, $destfile);

	//copy files from autoload_configs
		recursive_copy($_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/includes/install/autoload_configs', $v_conf_dir.'/autoload_configs');

	//make a backup copy of the default config used with the 'Restore Default' buttons on the text areas.
		if (!is_dir($v_conf_dir.".orig")) { mkdir($v_conf_dir.".orig".'',0777,true); }
		recursive_copy($v_conf_dir, $v_conf_dir.".orig");

	//copy the dialplan default.xml to the conf.orig dir
		$srcfile = $_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/includes/install/dialplan/default.xml';
		$destfile = $v_conf_dir.'.orig/dialplan/default.xml';
		if (file_exists($destfile)) { unlink($destfile); }
		if (!copy($srcfile, $destfile)) {
			unlink($_SERVER["DOCUMENT_ROOT"].PROJECT_PATH."/includes/config.php");
			echo "failed to copy $srcfile to $destfile...\n";
			exit;
		}
		unset($srcfile, $destfile);

	//copy audio files
		if (!is_dir($v_sounds_dir.'/custom/8000')) { mkdir($v_sounds_dir.'/custom/8000',0777,true); }
		recursive_copy($_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/includes/install/sounds', $v_sounds_dir);

	//copy recordings files
		recursive_copy($_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/includes/install/recordings', $v_recordings_dir.'');

	//get the javascript files
		if (!is_dir($v_scripts_dir.'')) { mkdir($v_scripts_dir.'',0777,true); }
		if (!is_dir($v_scripts_dir.'/javascript')) { mkdir($v_scripts_dir.'/javascript',0777,true); }
		if (!is_dir($v_scripts_dir.'/lua')) { mkdir($v_scripts_dir.'/lua',0777,true); }
		if (!is_dir($v_scripts_dir.'/perl')) { mkdir($v_scripts_dir.'/perl',0777,true); }
		recursive_copy($_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/includes/install/scripts', $v_scripts_dir);

	//copy additional the flash mp3 player
		$srcfile = $_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/includes/install/htdocs/slim.swf';
		$destfile = $_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/mod/recordings/slim.swf';
		if (!copy($srcfile, $destfile)) {
			//echo "failed to copy $srcfile to $destfile...\n";
			//exit;
		}
		unset($srcfile, $destfile);

	//activate the .htaccess file
		$srcfile = $dbfilepath.'/htaccess.tmp';
		$destfile = $dbfilepath.'/.htaccess';
		if (!copy($srcfile, $destfile)) {
			//echo "failed to copy $srcfile to $destfile...\n";
			//exit;
		}
		unset($srcfile, $destfile);

	//create the switch.conf.xml file
		switch_conf_xml();

	//synchronize the config with the saved settings
		sync_package_freeswitch();

	//redirect to the login page
		$msg = "</strong><br />\n";
		$msg .= "Congratulations, the installation has been completed. <br />";
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


//temp sqlite db
	$dbfilepath = $_SERVER["DOCUMENT_ROOT"].PROJECT_PATH;
	//if (file_exists($dbfilepath.'/'.$dbfilename)) {
	//	unlink($dbfilepath.'/'.$dbfilename);
	//}
	//--- begin: create the sqlite db file -----------------------------------------
		$filename = $_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/includes/install/sql/sqlite.sql';
		$file_contents = file_get_contents($filename);
		unset($filename);
		//echo "<pre>\n";
		//echo $file_contents;
		//echo "</pre>\n";
		//exit;
		try {
			//$db_temp = new PDO('sqlite:'.$dbfilepath.'/'.$dbfilename); //sqlite 3
			$db_temp = new PDO('sqlite::memory:'); //sqlite 3
			$db_temp->beginTransaction();
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
				//create the call detail records database
				//if (strtolower(substr($sql, 0, 18)) == "create table v_cdr") {
				//	//add the CDR database from lib_cdr.php
				//}
				//else { //create the tables and fill in the basic settings
					try {
						$db_temp->query($sql);
					}
					catch (PDOException $error) {
						echo "error: " . $error->getMessage() . " sql: $sql<br/>";
						//die();
					}
				//}
				$x++;
			}
			unset ($file_contents, $sql);
			//$db_temp->commit();
	//--- end: create the sqlite db -----------------------------------------

//get the template information
	$sql = "";
	$sql .= "select * from v_templates ";
	if (strlen($template_rsssubcategory) > 0) {
		$sql .= "where v_id = '$v_id' ";
		//$sql .= "and templatename = '$template_rsssubcategory' ";
	}
	else {
		$sql .= "where v_id = '$v_id' ";
		$sql .= "and template_default = 'true' ";
	}
	$prepstatement = $db_temp->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	foreach ($result as &$row) {
		$template = base64_decode($row["template"]);
		$templatemenutype = $row["templatemenutype"];
		$templatemenucss = base64_decode($row["templatemenucss"]);
		//$adduser = $row["adduser"];
		//$adddate = $row["adddate"];
		break; //limit to 1 row
	}

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

	echo "<form method='post' name='frm' action=''>\n";
	echo "<table width='100%'  border='0' cellpadding='6' cellspacing='0'>\n";

	echo "<tr>\n";
	echo "<td align='left' width='30%' nowrap><b>Installation</b></td>\n";
	echo "<td width='70%' align='right'>&nbsp;</td>\n";
	//echo "<td width='70%' align='right'><input type='button' class='btn' name='' alt='back' onclick=\"window.location='v_dialplan_includes_edit.php?id=".$dialplan_include_id."'\" value='Back'></td>\n";
	echo "</tr>\n";
?>
<script type="text/javascript">
function dbtype_onchange() {
	var dbtype = document.getElementById("dbtype").value;

	if (dbtype == "mysql") {

		document.getElementById("desc_dbtype").innerHTML = "If you use the root database account the database can be created automatically. However if preferred the database can be created manually with the <a href='<?php echo PROJECT_PATH; ?>/includes/install/sql/mysql.sql' target='_blank'>mysql.sql</a> script. ";

		document.getElementById("desc_dbfilename").innerHTML = "Not applicable"; document.frm.dbfilename.value = ''; document.frm.dbfilename.disabled = true;
		document.getElementById("desc_dbfilepath").innerHTML = "Choose the database where the settings will be stored. <br />Path to the secure folder that contains PHP command line scripts.";
		document.getElementById("desc_dbhost").innerHTML = "Recommended for MySQL."; document.frm.dbhost.disabled = false;
		document.getElementById("desc_dbport").innerHTML = "Optional if the database is using the default port."; document.frm.dbport.disabled = false;
		document.getElementById("desc_dbname").innerHTML = "Required for MySQL."; document.frm.dbname.value = 'fusionpbx'; document.frm.dbname.disabled = false;
		document.getElementById("desc_dbusername").innerHTML = "Required for MySQL. "; document.frm.dbusername.disabled = false;
		document.getElementById("desc_dbpassword").innerHTML = "Required for MySQL."; document.frm.dbpassword.disabled = false;
	}
	else if (dbtype == "pgsql") {
		document.getElementById("desc_dbtype").innerHTML = "Choose the database where the settings will be stored. <br /><b>Note: Before proceeding use the <a href='<?php echo PROJECT_PATH; ?>/includes/install/sql/pgsql.sql' target='_blank'>pgsql.sql</a> script to setup the database.</b>";

		document.getElementById("desc_dbfilename").innerHTML = "Not applicable"; document.frm.dbfilename.value = ''; document.frm.dbfilename.disabled = true;
		document.getElementById("desc_dbfilepath").innerHTML = "Path to the secure folder that contains PHP command line scripts.";
		document.getElementById("desc_dbhost").innerHTML = "Optional for PostgreSQL when the database is local."; document.frm.dbhost.disabled = false;
		document.getElementById("desc_dbport").innerHTML = "Optional if the database is using the default port."; document.frm.dbport.disabled = false; 
		document.getElementById("desc_dbname").innerHTML = "Required for PostgreSQL."; document.frm.dbname.value = 'fusionpbx'; document.frm.dbname.disabled = false;
		document.getElementById("desc_dbusername").innerHTML = "Required for PostgreSQL."; document.frm.dbusername.disabled = false;
		document.getElementById("desc_dbpassword").innerHTML = "Required for PostgreSQL."; document.frm.dbpassword.disabled = false;
	}
	else if (dbtype == "sqlite") {
		document.getElementById("desc_dbtype").innerHTML = "Choose the database where the settings will be stored. <br />The sqlite database will be created automatically.";
		document.frm.dbfilename.value = 'fusionpbx.db';
		document.getElementById("desc_dbfilename").innerHTML = "Default: fusiopbx.db. If the field is left empty then the file name is determined by the host or IP address."; document.frm.dbfilename.disabled = false;
		document.getElementById("desc_dbfilepath").innerHTML = "Path to the secure folder that contains PHP command line scripts and the SQLite database.";
		document.getElementById("desc_dbhost").innerHTML = "Not applicable"; document.frm.dbhost.value = ''; document.frm.dbhost.disabled = true;
		document.getElementById("desc_dbport").innerHTML = "Not applicable"; document.frm.dbport.value = ''; document.frm.dbport.disabled = true;
		document.getElementById("desc_dbname").innerHTML = "Not applicable"; document.frm.dbname.value = ''; document.frm.dbname.disabled = true;
		document.getElementById("desc_dbusername").innerHTML = "Not applicable"; document.frm.dbusername.value = ''; document.frm.dbusername.disabled = true;
		document.getElementById("desc_dbpassword").innerHTML = "Not applicable"; document.frm.dbpassword.value = ''; document.frm.dbpassword.disabled = true;
	}
	if (dbtype == "") {
		document.getElementById("desc_dbfilename").innerHTML = "";
		document.getElementById("desc_dbfilepath").innerHTML = "";
		document.getElementById("desc_dbhost").innerHTML = "";
		document.getElementById("desc_dbport").innerHTML = "";
		document.getElementById("desc_dbname").innerHTML = "";
		document.getElementById("desc_dbusername").innerHTML = "";
		document.getElementById("desc_dbpassword").innerHTML = "";
	}
}
</script>
<?php

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Database Type:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<select name='dbtype' id='dbtype' class='formfld' id='form_tag' onchange='dbtype_onchange();'>\n";
	if (extension_loaded('pdo_pgsql')) {	echo "	<option value='pgsql'>postgresql</option>\n"; }
	if (extension_loaded('pdo_mysql')) {	echo "	<option value='mysql'>mysql</option>\n"; }
	if (extension_loaded('pdo_sqlite')) {	echo "	<option  value='sqlite' selected>sqlite</option>\n"; } //set sqlite as the default
	echo "	</select><br />\n";
	echo "	<span id='desc_dbtype'></span><br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' 'valign='top' align='left' nowrap>\n";
	echo "		Database Filename:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "		<input class='formfld' type='text' name='dbfilename' maxlength='255' value=\"$dbfilename\"><br />\n";
	echo "		<span id='desc_dbfilename'></span><br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "		Secure Directory:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "		<input class='formfld' type='text' name='dbfilepath' maxlength='255' value=\"$dbfilepath\"><br />\n";
	echo "		<span id='desc_dbfilepath'></span><br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "		Database Host:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "		<input class='formfld' type='text' name='dbhost' maxlength='255' value=\"$dbhost\"><br />\n";
	echo "		<span id='desc_dbhost'></span><br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "		Database Port:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "		<input class='formfld' type='text' name='dbport' maxlength='255' value=\"$dbport\"><br />\n";
	echo "		<span id='desc_dbport'></span><br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "		Database Name:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "		<input class='formfld' type='text' name='dbname' maxlength='255' value=\"$dbname\"><br />\n";
	echo "		<span id='desc_dbname'></span><br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "		Database Username:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "		<input class='formfld' type='text' name='dbusername' maxlength='255' value=\"$dbusername\"><br />\n";
	echo "		<span id='desc_dbusername'></span><br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "		Database Password:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "		<input class='formfld' type='text' name='dbpassword' maxlength='255' value=\"$dbpassword\"></span><br />\n";
	echo "		<span id='desc_dbpassword'></span><br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    FreeSWITCH Directory:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' type='text' name='install_v_dir' maxlength='255' value=\"$install_v_dir\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    PHP Directory:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' type='text' name='install_php_dir' maxlength='255' value=\"$install_php_dir\"><br />\n";
	echo "Path to PHP's bin or executable directory.<br />\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Temp Directory:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' type='text' name='install_tmp_dir' maxlength='255' value=\"".realpath(sys_get_temp_dir())."\"><br />\n";
	echo "Set this to the temporary directory.<br />\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Backup Directory:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' type='text' name='install_v_backup_dir' maxlength='255' value=\"".realpath(sys_get_temp_dir())."\"><br />\n";
	echo "Set a backup directory.<br />\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "	<tr>\n";
	echo "		<td colspan='2' align='right'>\n";
	echo "			<input type='submit' name='submit' class='btn' value='Install'>\n";
	echo "		</td>\n";
	echo "	</tr>";
	echo "</table>";
	echo "</form>";
	echo "</div>";
	echo "<script type=\"text/javascript\">dbtype_onchange();</script>\n";


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