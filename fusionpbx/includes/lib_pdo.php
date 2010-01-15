<?php
	if ($dbtype == "sqlite") {

		if (strlen($dbfilename) == 0) {
			//if (strlen($_SERVER["SERVER_NAME"]) == 0) { $_SERVER["SERVER_NAME"] = "http://localhost"; }
			$server_name = $_SERVER["SERVER_NAME"];
			$server_name = str_replace ("www.", "", $server_name);
			$server_name = str_replace ("example.net", "example.com", $server_name);
			//$server_name = str_replace (".", "_", $server_name);
			$dbfilenameshort = $server_name;
			$dbfilename = $server_name.'.db';
		}
		else {
			$dbfilenameshort = $dbfilename;
		}

		$filepath = $v_secure;
		$dbfilepath = $v_secure;
		$dbfilepath = realpath($dbfilepath);
		//echo "dbfilepath and name: ".$dbfilepath."/".$dbfilename."\n";
		if (file_exists($dbfilepath.'/'.$dbfilename)) {
			//echo "main file exists<br>";
		}
		else { //file doese not exist

			//--- begin: create the sqlite db file -----------------------------------------
				$filename = $_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/includes/install/sql/sqlite.sql';
				$file_contents = file_get_contents($filename);
				//echo "<pre>\n";
				//echo $file_contents;
				//echo "</pre>\n";
				//exit;
				try {
					//$db = new PDO('sqlite2:example.db'); //sqlite 2
					//$dbimg = new PDO('sqlite::memory:'); //sqlite 3
					$dbsql = new PDO('sqlite:'.$dbfilepath.'/'.$dbfilename); //sqlite 3
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
								$dbsql->query($sql);
							}
							catch (PDOException $error) {
								echo "error: " . $error->getMessage() . " sql: $sql<br/>";
								//die();
							}
						//}
						$x++;
					}
					unset ($file_contents, $sql);
			//--- end: create the sqlite db -----------------------------------------

			if (is_writable($dbfilepath.'/'.$dbfilename)) { //is writable
				//use database in current location
			}
			else { //not writable
				echo "The database ".$dbfilepath."/".$dbfilename." is not writeable.";
				exit;
			}

		}

		if (!function_exists('phpmd5')) {
			function phpmd5($string) {
				return md5($string);
			}
		}
		if (!function_exists('phpunix_timestamp')) {
			function phpunix_timestamp($string) {
				return strtotime($string);
			}
		}
		if (!function_exists('phpnow')) {
			function phpnow() {
				//return date('r');
				return date("Y-m-d H:i:s");
			}
		}

		if (!function_exists('phpleft')) {
			function phpleft($string, $num) {
				return substr($string, 0, $num);
			}
		}

		if (!function_exists('phpright')) {
			function phpright($string, $num) {
				return substr($string, (strlen($string)-$num), strlen($string));
			}
		}

		if (!function_exists('phpsqlitedatatype')) {
			function phpsqlitedatatype($string, $field) {

				//--- Begin: Get String Between start and end characters -----
				$start = '(';
				$end = ')';
				$ini = stripos($string,$start);
				if ($ini == 0) return "";
				$ini += strlen($start);
				$len = stripos($string,$end,$ini) - $ini;
				$string = substr($string,$ini,$len);
				//--- End: Get String Between start and end characters -----

				$strdatatype = '';
				$stringarray = explode(',', $string);
				foreach($stringarray as $lnvalue) {

					//$strdatatype .= "-- ".$lnvalue ." ".strlen($lnvalue)." delim ".strrchr($lnvalue, " ")."---<br>";
					//$delimpos = stripos($lnvalue, " ");
					//$strdatatype .= substr($value,$delimpos,strlen($value))." --<br>";

					$fieldlistarray = explode (" ", $value);
					//$strdatatype .= $value ."<br>";
					//$strdatatype .= $fieldlistarray[0] ."<br>";
					//echo $fieldarray[0]."<br>\n";
					if ($fieldarray[0] == $field) {
						//$strdatatype = $fieldarray[1]." ".$fieldarray[2]." ".$fieldarray[3]." ".$fieldarray[4]; //strdatatype
					}
					unset($fieldarray, $string, $field);
				}

				//$strdatatype = $string;
				return $strdatatype;
			}
		} //end function


		//database connection
		try {
			//$db = new PDO('sqlite2:example.db'); //sqlite 2
			//$db = new PDO('sqlite::memory:'); //sqlite 3
			$db = new PDO('sqlite:'.$dbfilepath.'/'.$dbfilename); //sqlite 3

			//Add additional functions to SQLite so that they are accessible inside SQL
			//bool PDO::sqliteCreateFunction ( string function_name, callback callback [, int num_args] )
			$db->sqliteCreateFunction('md5', 'phpmd5', 1);
			$db->sqliteCreateFunction('unix_timestamp', 'phpunix_timestamp', 1);
			$db->sqliteCreateFunction('now', 'phpnow', 0);
			$db->sqliteCreateFunction('sqlitedatatype', 'phpsqlitedatatype', 2);
			$db->sqliteCreateFunction('strleft', 'phpleft', 2);
			$db->sqliteCreateFunction('strright', 'phpright', 2);
		}
		catch (PDOException $error) {
			print "error: " . $error->getMessage() . "<br/>";
			die();
		}
	} //end if dbtype sqlite


	if ($dbtype == "mysql") {
		//database connection
		try {
			if (strlen($dbhost) == 0 && strlen($dbport) == 0) {
				//if both host and port are empty use the unix socket
				$db = new PDO("mysql:host=$dbhost;unix_socket=/var/run/mysqld/mysqld.sock;dbname=$dbname", $dbusername, $dbpassword);
			}
			else {
				if (strlen($dbport) == 0) {
					//leave out port if it is empty
					$db = new PDO("mysql:host=$dbhost;dbname=$dbname;", $dbusername, $dbpassword, array(
						PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
						PDO::ATTR_ERRMODE, 
						PDO::ERRMODE_EXCEPTION
					));
				}
				else {
					$db = new PDO("mysql:host=$dbhost;port=$dbport;dbname=$dbname;", $dbusername, $dbpassword, array(
						PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
						PDO::ATTR_ERRMODE, 
						PDO::ERRMODE_EXCEPTION
					));
				}
			}
		}
		catch (PDOException $error) {
			print "error: " . $error->getMessage() . "<br/>";
			die();
		}
	} //end if dbtype mysql


	if ($dbtype == "pgsql") {
		//database connection
		try {
			if (strlen($dbhost) > 0) {
				if (strlen($dbport) == 0) { $dbport = "5432"; }
				$db = new PDO("pgsql:host=localhost port=5432 dbname=$dbname user=$dbusername password=$dbpassword");
			}
			else {
				$db = new PDO("pgsql:dbname=$dbname user=$dbusername password=$dbpassword");
			}
		}
		catch (PDOException $error) {
			print "error: " . $error->getMessage() . "<br/>";
			die();
		}
	} //end if dbtype pgsql
?>