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
			public $db;
			public $driver;
			public $type;
			public $host;
			public $port;
			public $name; //database name
			public $username;
			public $password;
			public $path;
			public $table;
			public $where; //array
			public $order_by; //array
			public $order_type;
			public $limit;
			public $offset;
			public $fields;
			public $count;
			public $sql;
			public $result;

			public function connect() {

				if (strlen($this->type) == 0 && strlen($this->name) == 0) {
					//include config.php
						include "root.php";
						include "includes/config.php";

					//backwards compatibility
						if (strlen($dbtype) > 0) { $db_type = $dbtype; }
						if (strlen($dbhost) > 0) { $db_host = $dbhost; }
						if (strlen($dbport) > 0) { $db_port = $dbport; }
						if (strlen($dbname) > 0) { $db_name = $dbname; }
						if (strlen($dbusername) > 0) { $db_username = $dbusername; }
						if (strlen($dbpassword) > 0) { $db_password = $dbpassword; }
						if (strlen($dbfilepath) > 0) { $db_path = $db_file_path; }
						if (strlen($dbfilename) > 0) { $db_name = $dbfilename; }

					//set defaults
						if (strlen($db_type) > 0) { $this->driver = $db_type; }
						if (strlen($db_type) > 0) { $this->type = $db_type; }
						if (strlen($db_host) > 0) { $this->host = $db_host; }
						if (strlen($db_port) > 0) { $this->port = $db_port; }
						if (strlen($db_name) > 0) { $this->name = $db_name; }
						if (strlen($db_username) > 0) { $this->username = $db_username; }
						if (strlen($db_password) > 0) { $this->password = $db_password; }
						if (strlen($db_path) > 0) { $this->path = $db_path; }
				}
				if (strlen($this->driver) == 0) {
					$this->driver = $this->type;
				}

				if ($this->driver == "sqlite") {
					if (strlen($this->name) == 0) {
						$server_name = $_SERVER["SERVER_NAME"];
						$server_name = str_replace ("www.", "", $server_name);
						$db_name_short = $server_name;
						$this->name = $server_name.'.db';
					}
					else {
						$db_name_short = $this->name;
					}
					$this->path = $_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/secure';
					$this->path = realpath($this->path);
					if (file_exists($this->path.'/'.$this->name)) {
						//echo "main file exists<br>";
					}
					else {
						$file_name = $_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/includes/install/sql/sqlite.sql';
						$file_contents = file_get_contents($file_name);
						try {
							//$db = new PDO('sqlite2:example.db'); //sqlite 2
							//$db = new PDO('sqlite::memory:'); //sqlite 3
							$db = new PDO('sqlite:'.$this->path.'/'.$this->name); //sqlite 3
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

						if (is_writable($this->path.'/'.$this->name)) {
							//is writable - use database in current location
						}
						else { 
							//not writable
							echo "The database ".$this->path."/".$this->name." is not writeable.";
							exit;
						}
					}
					try {
						//$db = new PDO('sqlite2:example.db'); //sqlite 2
						//$db = new PDO('sqlite::memory:'); //sqlite 3
						$this->db = new PDO('sqlite:'.$this->path.'/'.$this->name); //sqlite 3

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

				if ($this->driver == "mysql") {
					try {
						//required for mysql_real_escape_string
							if (function_exists(mysql_connect)) {
								$mysql_connection = mysql_connect($this->host, $this->username, $this->password);
							}
						//mysql pdo connection
							if (strlen($this->host) == 0 && strlen($this->port) == 0) {
								//if both host and port are empty use the unix socket
								$this->db = new PDO("mysql:host=$this->host;unix_socket=/var/run/mysqld/mysqld.sock;dbname=$this->name", $this->username, $this->password);
							}
							else {
								if (strlen($this->port) == 0) {
									//leave out port if it is empty
									$this->db = new PDO("mysql:host=$this->host;dbname=$this->name;", $this->username, $this->password, array(
									PDO::ATTR_ERRMODE,
									PDO::ERRMODE_EXCEPTION
									));
								}
								else {
									$this->db = new PDO("mysql:host=$this->host;port=$this->port;dbname=$this->name;", $this->username, $this->password, array(
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
				}

				if ($this->driver == "pgsql") {
					//database connection
					try {
						if (strlen($this->host) > 0) {
							if (strlen($this->port) == 0) { $this->port = "5432"; }
							$this->db = new PDO("pgsql:host=$this->host port=$this->port dbname=$this->name user=$this->username password=$this->password");
						}
						else {
							$this->db = new PDO("pgsql:dbname=$this->name user=$this->username password=$this->password");
						}
					}
					catch (PDOException $error) {
						print "error: " . $error->getMessage() . "<br/>";
						die();
					}
				}

				if ($this->driver == "odbc") {
					//database connection
						try {
							$this->db = new PDO("odbc:".$this->name, $this->username, $this->password);
						}
						catch (PDOException $e) {
							echo 'Connection failed: ' . $e->getMessage();
						}
				}
			}

			public function tables() {
				//connect to the database if needed
					if (!$this->db) {
						$this->connect();
					}
					if ($this->type == "sqlite") {
						$sql = "SELECT name FROM sqlite_master ";
						$sql .= "WHERE type='table' ";
						$sql .= "order by name;";
					}
					if ($this->type == "pgsql") {
						$sql = "select table_name as name ";
						$sql .= "from information_schema.tables ";
						$sql .= "where table_schema='public' ";
						$sql .= "and table_type='BASE TABLE' ";
						$sql .= "order by table_name ";
					}
					if ($this->type == "mysql") {
						$sql = "show tables";
					}
					if ($this->type == "mssql") {
						$sql = "SELECT * FROM sys.Tables order by name asc";
					}
					$prep_statement = $this->db->prepare(check_sql($sql));
					$prep_statement->execute();
					$tmp = $prep_statement->fetchAll(PDO::FETCH_NAMED);
					if ($this->type == "pgsql" || $this->type == "sqlite" || $this->type == "mssql") {
						foreach ($tmp as &$row) {
							$result[]['name'] = $row['name'];
						}
					}
					if ($this->type == "mysql") {
						foreach ($tmp as &$row) {
							$table_array = array_values($row);
							$result[]['name'] = $table_array[0];
						}
					}
					return $result;
			}

			public function table_info() {
				//public $db;
				//public $type;
				//public $table;
				//public $name;

				//connect to the database if needed
					if (!$this->db) {
						$this->connect();
					}
				//get the table info
					if (strlen($this->table) == 0) { return false; }
					if ($this->type == "sqlite") {
						$sql = "PRAGMA table_info(".$this->table.");";
					}
					if ($this->type == "pgsql") {
						$sql = "SELECT ordinal_position, ";
						$sql .= "column_name, ";
						$sql .= "data_type, ";
						$sql .= "column_default, ";
						$sql .= "is_nullable, ";
						$sql .= "character_maximum_length, ";
						$sql .= "numeric_precision ";
						$sql .= "FROM information_schema.columns ";
						$sql .= "WHERE table_name = '".$this->table."' ";
						$sql .= "and table_catalog = '".$this->name."' ";
						$sql .= "ORDER BY ordinal_position; ";
					}
					if ($this->type == "mysql") {
						$sql = "DESCRIBE ".$this->table.";";
					}
					if ($this->type == "mssql") {
						$sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '".$this->table."'";
					}
					$prep_statement = $this->db->prepare($sql);
					$prep_statement->execute();
				//set the result array
					return $prep_statement->fetchAll(PDO::FETCH_ASSOC);
			}

			public function fields() {
				//public $db;
				//public $type;
				//public $table;
				//public $name;

				//get the table info
					$table_info = $this->table_info();

				//set the list of fields
					if ($this->type == "sqlite") {
						foreach($table_info as $row) {
							$result[]['name'] = $row['name'];
						}
					}
					if ($this->type == "pgsql") {
						foreach($table_info as $row) {
							$result[]['name'] = $row['column_name'];
						}
					}
					if ($this->type == "mysql") {
						foreach($table_info as $row) {
							$result[]['name'] = $row['Field'];
						}
					}
					if ($this->type == "mssql") {
						foreach($table_info as $row) {
							$result[]['name'] = $row['COLUMN_NAME'];
						}
					}

				//return the result array
					return $result;
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
					$sql = "select * from ".$this->table." ";
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
					if (count($this->order_by) > 0) {
						$sql .= "order by ";
						$i = 1;
						foreach($this->order_by as $row) {
							if (count($this->order_by) == $i) {
								$sql .= $row['name']." ".$row['order']." ";
							}
							else {
								$sql .= $row['name']." ".$row['order'].", ";
							}
							$i++;
						}
					}
					if ($this->limit) {
						$sql .= " limit ".$this->limit." offset ".$this->offset." ";
					}
					//echo $sql;
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
					$sql .= " (";
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
							if (strlen($value) > 0) {
								$sql .= "'".$value."' ";
							}
							else {
								$sql .= "'".$value."' ";
							}
						}
						else {
							if (strlen($value) > 0) {
								$sql .= "'".$value."', ";
							}
							else {
								$sql .= "null, ";
							}
						}
						$i++;
					}
					$sql .= ")";
					$this->sql = $sql;
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
							if (strlen($name) > 0 && $value == null) {
								$sql .= $name." = null ";
							}
							else {
								$sql .= $name." = '".$value."' ";
							}
						}
						else {
							if (strlen($name) > 0 && $value == null) {
								$sql .= $name." = null, ";
							}
							else {
								$sql .= $name." = '".$value."', ";
							}
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
					$sql = "delete from ".$this->table." ";
					if ($this->where) {
						$i = 0;
						foreach($this->where as $row) {
							if ($i == 0) {
								$sql .= "where ".$row['name']." ".$row['operator']." '".$row['value']."' ";
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
					$sql = "select count(*) as num_rows from ".$this->table." ";
					if ($this->where) {
						$i = 0;
						foreach($this->where as $row) {
							if ($i == 0) {
								$sql .= "where ".$row['name']." ".$row['operator']." '".$row['value']."' ";
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
		}
	}

if (!function_exists('php_md5')) {
	function php_md5($string) {
		return md5($string);
	}
}

if (!function_exists('php_unix_time_stamp')) {
	function php_unix_time_stamp($string) {
		return strtotime($string);
	}
}

if (!function_exists('php_now')) {
	function php_now() {
		return date("Y-m-d H:i:s");
	}
}

if (!function_exists('php_left')) {
	function php_left($string, $num) {
		return substr($string, 0, $num);
	}
}

if (!function_exists('php_right')) {
	function php_right($string, $num) {
		return substr($string, (strlen($string)-$num), strlen($string));
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