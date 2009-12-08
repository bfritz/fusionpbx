<?php
/*
if ($dbtype == "sqlite") {
	try {
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
		$dbfilepath = str_replace("\\", "/", $dbfilepath);


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

				//replace \r\n with \n then explode on \n
					$file_contents = str_replace("\r\n", "\n", $file_contents);

				//loop line by line through all the lines of sql code
					$stringarray = explode("\n", $file_contents);
					$x = 0;
					foreach($stringarray as $sql) {
						//create the call detail records database
						if (strtolower(substr($sql, 0, 18)) == "create table v_cdr") {
							try {
								$dbcdr = new PDO('sqlite:'.$dbfilepath.'/'.$dbfilenameshort.'.cdr.db'); //sqlite 3
								$dbcdr->query($sql);
								unset($dbcdr);
							}
							catch (PDOException $error) {
								print "error: " . $error->getMessage() . "<br/>";
								die();
							}
						}
						$x++;
					}
					unset ($file_contents, $sql);
			//--- end: create the sqlite db -----------------------------------------

			if (is_writable($dbfilepath.'/'.$dbfilename)) { //is writable
				//use database in current location
			}
			else { //not writable
				echo "The database ".$dbfilepath."/".$dbfilename." is not writeable2.";
				exit;
			}
		}

		unset($db);
		//$db = new PDO('sqlite::memory:'); //sqlite 3
		$db = new PDO('sqlite:'.$dbfilepath.'/'.$dbfilenameshort.'.cdr.db'); //sqlite 3
	}
	catch (PDOException $error) {
		print "error: " . $error->getMessage() . "<br/>";
		die();
	}
}
*/
?>