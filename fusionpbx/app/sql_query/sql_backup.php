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
require_once "includes/require.php";
require_once "includes/checkauth.php";
if (permission_exists('sql_query_backup')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

//pdo database connection
	if (strlen($_REQUEST['id']) > 0) {
		require_once "sql_query_pdo.php";
	}

//set the headers
	header('Content-type: application/octet-binary');
	header('Content-Disposition: attachment; filename=database_backup.sql');

//get the list of tables
	if ($db_type == "sqlite") {
		$sql = "SELECT name FROM sqlite_master ";
		$sql .= "WHERE type='table' ";
		$sql .= "order by name;";
	}
	if ($db_type == "pgsql") {
		$sql = "select table_name as name ";
		$sql .= "from information_schema.tables ";
		$sql .= "where table_schema='public' ";
		$sql .= "and table_type='BASE TABLE' ";
		$sql .= "order by table_name ";
	}
	if ($db_type == "mysql") {
		$sql = "show tables";
	}
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
	foreach ($result as &$row) {
		$table_name = $row['name'];

		//get the table data
			$sql = "select * from $table_name";
			if (strlen($sql) > 0) {
				$prep_statement_2 = $db->prepare(check_sql($sql));
				if ($prep_statement_2) { 
					$prep_statement_2->execute();
					$result2 = $prep_statement_2->fetchAll(PDO::FETCH_ASSOC);
				}
				else {
					echo "<b>Error:</b>\n";
					echo "<pre>\n";
					print_r($db->errorInfo());
					echo "</pre>\n";
				}

				$x = 0;
				foreach ($result2[0] as $key => $value) {
					if ($row[$column] != "db") {
						$column_array[$x] = $key;
						$x++;
					}
				}

				$column_array_count = count($column_array);

				foreach ($result2 as &$row) {
					$sql = "INSERT INTO $table_name (";
					$x = 1;
					foreach ($column_array as $column) {
						if ($x < $column_array_count) {
							if (strlen($row[$column]) > 0) {
								$sql .= ''.$column.',';
							}
						}
						else {
							if (strlen($row[$column]) > 0) {
								$sql .= ''.$column.'';
							}
						}
						$x++;
					}
					$sql .= ") ";
					$sql .= "VALUES( ";
					$x = 1;
					foreach ($column_array as $column) {
						if ($x < $column_array_count) {
							if (strlen($row[$column])> 0) {
								$sql .= "'".check_str($row[$column])."',";
							}
						}
						else {
							if (strlen($row[$column])> 0) {
								$sql .= "'".check_str($row[$column])."'";
							}
						}
						$x++;
					}
					$sql .= ");\n";
					echo str_replace(",)", ")", $sql);
				}
			}

		unset($column_array);
	}

?>
