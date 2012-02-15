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
	Copyright (C) 2010
	All Rights Reserved.

	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>
*/
include "root.php";

//define the database class
	if (!class_exists('database')) {
		class database {
			private $db;
			public $result;
			public $type;
			public $table;
			public $where; //array
			public $order_by; //array
			public $order_type;
			public $limit;
			public $offset;
			public $fields;
			public $count;

			public function connect() {
				//include config.php
					include "root.php";
					include "includes/config.php";

				//set defaults
					if (strlen($dbtype) > 0) { 
						$db_type = $dbtype; 
					}
					if (strlen($dbhost) > 0) { 
						$db_host = $dbhost; 
					}
					if (strlen($dbport) > 0) { 
						$db_port = $dbport; 
					}
					if (strlen($dbname) > 0) { 
						$db_name = $dbname; 
					}
					if (strlen($dbusername) > 0) { 
						$db_username = $dbusername; 
					}
					if (strlen($dbpassword) > 0) { 
						$db_password = $dbpassword; 
					}
					if (strlen($dbfilepath) > 0) { 
						$db_path = $dbfilepath; 
					}
					if (strlen($dbfilename) > 0) { 
						$db_name = $dbfilename; 
					}

				if ($db_type == "sqlite") {
					if (strlen($db_name) == 0) {
						$server_name = $_SERVER["SERVER_NAME"];
						$server_name = str_replace ("www.", "", $server_name);
						$db_name_short = $server_name;
						$db_name = $server_name.'.db';
					}
					else {
						$db_name_short = $db_name;
					}
					$db_path = $_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/secure';
					$db_path = realpath($db_path);
					if (file_exists($db_path.'/'.$db_name)) {
						//echo "main file exists<br>";
					}
					else {
						$file_name = $_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/includes/install/sql/sqlite.sql';
						$file_contents = file_get_contents($file_name);
						try {
							//$db = new PDO('sqlite2:example.db'); //sqlite 2
							//$db = new PDO('sqlite::memory:'); //sqlite 3
							$db = new PDO('sqlite:'.$db_path.'/'.$db_name); //sqlite 3
							$db->beginTransaction();
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
								$db->query($sql);
							}
							catch (PDOException $error) {
								echo "error: " . $error->getMessage() . " sql: $sql<br/>";
							}
							$x++;
						}
						unset ($file_contents, $sql);
						$db->commit();

						if (is_writable($db_path.'/'.$db_name)) {
							//is writable - use database in current location
						}
						else { 
							//not writable
							echo "The database ".$db_path."/".$db_name." is not writeable.";
							exit;
						}
					}
					try {
						//$db = new PDO('sqlite2:example.db'); //sqlite 2
						//$db = new PDO('sqlite::memory:'); //sqlite 3
						$this->db = new PDO('sqlite:'.$db_path.'/'.$db_name); //sqlite 3

						//add additional functions to SQLite so that they are accessible inside SQL
						//bool PDO::sqliteCreateFunction ( string function_name, callback callback [, int num_args] )
						$this->db->sqliteCreateFunction('md5', 'php_md5', 1);
						$this->db->sqliteCreateFunction('unix_timestamp', 'php_unix_time_stamp', 1);
						$this->db->sqliteCreateFunction('now', 'php_now', 0);
						$this->db->sqliteCreateFunction('str_left', 'php_left', 2);
						$this->db->sqliteCreateFunction('str_right', 'php_right', 2);
					}
					catch (PDOException $error) {
						print "error: " . $error->getMessage() . "<br/>";
						die();
					}
				}

				if ($db_type == "mysql") {
					try {
						//required for mysql_real_escape_string
							if (function_exists(mysql_connect)) {
								$mysql_connection = mysql_connect($this->db_host, $this->db_username, $this->db_password);
							}
						//mysql pdo connection
							if (strlen($this->db_host) == 0 && strlen($this->db_port) == 0) {
								//if both host and port are empty use the unix socket
								$db = new PDO("mysql:host=$this->db_host;unix_socket=/var/run/mysqld/mysqld.sock;dbname=$this->db_name", $db_username, $db_password);
							}
							else {
								if (strlen($this->db_port) == 0) {
									//leave out port if it is empty
									$db = new PDO("mysql:host=$this->db_host;dbname=$this->db_name;", $this->db_username, $this->db_password, array(
									PDO::ATTR_ERRMODE,
									PDO::ERRMODE_EXCEPTION
									));
								}
								else {
									$db = new PDO("mysql:host=$this->db_host;port=$this->db_port;dbname=$this->db_name;", $this->db_username, $this->db_password, array(
									PDO::ATTR_ERRMODE,
									PDO::ERRMODE_EXCEPTION
									));
								}
							}
							$this->db = $db;
					}
					catch (PDOException $error) {
						print "error: " . $error->getMessage() . "<br/>";
						die();
					}
				}

				if ($db_type == "pgsql") {
					//database connection
					try {
						if (strlen($this->db_host) > 0) {
							if (strlen($this->db_port) == 0) { $this->db_port = "5432"; }
							$db = new PDO("pgsql:host=$this->db_host port=$this->db_port dbname=$this->db_name user=$this->db_username password=$this->db_password");
						}
						else {
							$db = new PDO("pgsql:dbname=$this->db_name user=$this->db_username password=$this->db_password");
						}
					}
					catch (PDOException $error) {
						print "error: " . $error->getMessage() . "<br/>";
						die();
					}
					$this->db = $db;
				}
			}

			//public function disconnect() {
			//	return null;
			//}

			public function find() {
				//connect;
				//table;
				//where;
				//order_by;
				//limit;
				//offset;

				//connect to the database if needed
					if (!$this->db) {
						$this->connect();
					}
				//get data from the database
					$sql = "";
					$sql .= " select * from ".$this->table." ";
					if ($this->where) {
						$i = 0;
						foreach($this->where as $row) {
							if ($i == 0) {
								$sql .= 'where '.$row['name']." ".$row['operator']." '".$row['value']."' ";
							}
							else {
								$sql .= "and ".$row['name']." ".$row['operator']." '".$row['value']."' ";
							}
							$i++;
						}
					}
					if ($this->order_by) {
						$sql .= "order by ";
						$i = 1;
						foreach($this->order_by as $row) {
							if (count($this->order_by) == $i) {
								$sql .= $row['name']." ";
							}
							else {
								$sql .= $row['name'].", ";
							}
							$i++;
						}
						if ($this->order_type) {
							$sql .= $this->order_type." ";
						}
					}
					if ($this->limit) {
						$sql .= " limit ".$this->limit." offset ".$this->offset." ";
					}
					$prep_statement = $this->db->prepare($sql);
					if ($prep_statement) {
						$prep_statement->execute();
						return $prep_statement->fetchAll(PDO::FETCH_ASSOC);
					}
					else {
						return false;
					}
			}

			public function add(){
				//connect to the database if needed
					if (!$this->db) {
						$this->connect();
					}
				//add data to the database
					$sql = "insert into ".$this->table;
					$sql .= "(";
					$i = 1;
					foreach($this->fields as $name => $value) {
						if (count($this->fields) == $i) {
							$sql .= $name." ";
						}
						else {
							$sql .= $name.", ";
						}
						$i++;
					}
					$sql .= ") ";
					$sql .= "values ";
					$sql .= "(";
					$i = 1;
					foreach($this->fields as $name => $value) {
						if (count($this->fields) == $i) {
							$sql .= "'".$value."' ";
						}
						else {
							$sql .= "'".$value."', ";
						}
						$i++;
					}
					$sql .= ")";
					$this->db->exec($sql);
					unset($this->fields);
					unset($sql);
			}

			public function update() {
				//connect to the database if needed
					if (!$this->db) {
						$this->connect();
					}
				//udate the database
					$sql = "update ".$this->table." set ";
					$i = 1;
					foreach($this->fields as $name => $value) {
						if (count($this->fields) == $i) {
							$sql .= $name." = '".$value."' ";
						}
						else {
							$sql .= $name." = '".$value."', ";
						}
						$i++;
					}
					$i = 0;
					foreach($this->where as $row) {
						if ($i == 0) {
							$sql .= 'where '.$row['name']." ".$row['operator']." '".$row['value']."' ";
						}
						else {
							$sql .= "and ".$row['name']." ".$row['operator']." '".$row['value']."' ";
						}
						$i++;
					}
					$this->db->exec(check_sql($sql));
					unset($this->fields);
					unset($this->where);
					unset($sql);
			}

			public function delete(){
				//connect to the database if needed
					if (!$this->db) {
						$this->connect();
					}
				//delete from the database
					$sql = "";
					$sql .= "delete from ".$this->table." ";
					if ($this->where) {
						$i = 0;
						foreach($this->where as $row) {
							if ($i == 0) {
								$sql .= 'where '.$row['name']." ".$row['operator']." '".$row['value']."' ";
							}
							else {
								$sql .= "and ".$row['name']." ".$row['operator']." '".$row['value']."' ";
							}
							$i++;
						}
					}
					//echo $sql."<br>\n";
					$prep_statement = $this->db->prepare($sql);
					$prep_statement->execute();
					unset($sql);
					unset($this->where);
			}

			public function count() {
				//connect to the database if needed
					if (!$this->db) {
						$this->connect();
					}
				//get the number of rows
					$sql = "";
					$sql .= " select count(*) as num_rows from ".$this->table;
					if ($this->where) {
						$i = 0;
						foreach($this->where as $row) {
							if ($i == 0) {
								$sql .= 'where '.$row['name']." ".$row['operator']." '".$row['value']."' ";
							}
							else {
								$sql .= "and ".$row['name']." ".$row['operator']." '".$row['value']."' ";
							}
							$i++;
						}
					}
					unset($this->where);
					$prep_statement = $this->db->prepare(check_sql($sql));
					if ($prep_statement) {
						$prep_statement->execute();
						$row = $prep_statement->fetch(PDO::FETCH_ASSOC);
						if ($row['num_rows'] > 0) {
							$this->result = $row['num_rows'];
						}
						else {
							$this->result = 0;
						}
					}
					unset($prep_statement);
			}

			private function php_md5($string) {
				return md5($string);
			}

			private function php_unix_time_stamp($string) {
				return strtotime($string);
			}

			private function php_now() {
				//return date('r');
				return date("Y-m-d H:i:s");
			}

			private function php_left($string, $num) {
				return substr($string, 0, $num);
			}

			private function php_right($string, $num) {
				return substr($string, (strlen($string)-$num), strlen($string));
			}
		}
	}

//example usage
/*
//find
	require_once "includes/classes/database.php";
	$database = new database;
	$database->domain_uuid = $_SESSION["domain_uuid"];
	$database->type = $db_type;
	$database->table = "v_extensions";
	$where[0]['name'] = 'domain_uuid';
	$where[0]['value'] = $_SESSION["domain_uuid"];
	$where[0]['operator'] = '=';
	$database->where = $where;
	$order_by[0]['name'] = 'extension';
	$database->order_by = $order_by;
	$database->order_type = 'desc';
	$database->limit = '2';
	$database->offset = '0';
	$database->find();
	print_r($database->result);
//insert
	require_once "includes/classes/database.php";
	$database = new database;
	$database->domain_uuid = $_SESSION["domain_uuid"];
	$database->type = $db_type;
	$database->table = "v_ivr_menus";
	$fields[0]['name'] = 'domain_uuid';
	$fields[0]['value'] = $_SESSION["domain_uuid"];
	$database->add();
	print_r($database->result);
*/
?>