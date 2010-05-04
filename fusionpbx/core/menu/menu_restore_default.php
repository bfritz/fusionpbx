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
	return;
}

//remove the old menu
	$sql  = "delete from v_menu ";
	$sql .= "where v_id = '$v_id' ";
	//echo $sql;
	$db->exec(check_sql($sql));


//load the default database into a sqlite memory database
		$filename = $_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/includes/install/sql/sqlite.sql';
		$file_contents = file_get_contents($filename);
		unset($filename);
		try {
			//$db_default = new PDO('sqlite:'.$dbfilepath.'/'.$dbfilename); //sqlite 3
			$db_default = new PDO('sqlite::memory:'); //sqlite 3
			//$db_default->beginTransaction();
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
					$db_default->query($sql);
				}
				catch (PDOException $error) {
					echo "error: " . $error->getMessage() . " sql: $sql<br/>";
					//die();
				}
				$x++;
			}
			unset ($file_contents, $sql);
			//$db_default->commit();

//load the default menu into an array
	$sql = "";
	$sql .= "select * from v_menu ";
	$sql .= "where v_id = '$v_id' ";
	$prepstatement = $db_default->prepare(check_sql($sql));
	$prepstatement->execute();
	$menu_array = $prepstatement->fetchAll();

//use the menu array to restore the default menu
	foreach ($menu_array as &$row) {
		$menuid = $row["menuid"];
		$menulanguage = $row["menulanguage"];
		$menutitle = $row["menutitle"];
		$menustr = $row["menustr"];
		$menucategory = $row["menucategory"];
		$menudesc = $row["menudesc"];
		$menuparentid = $row["menuparentid"];
		$menuorder = $row["menuorder"];
		$menugroup = $row["menugroup"];
		$menuadduser = $row["menuadduser"];
		$menuadddate = $row["menuadddate"];
		$menumoduser = $row["menumoduser"];
		$menumoddate = $row["menumoddate"];

		//insert the defaul menu into the database
			$sql = "insert into v_menu ";
			$sql .= "(";
			$sql .= "menuid, ";
			$sql .= "v_id, ";
			$sql .= "menulanguage, ";
			$sql .= "menutitle, ";
			$sql .= "menustr, ";
			$sql .= "menucategory, ";
			$sql .= "menugroup, ";
			$sql .= "menudesc, ";
			$sql .= "menuparentid, ";
			$sql .= "menuorder, ";
			$sql .= "menuadduser, ";
			$sql .= "menuadddate, ";
			$sql .= "menumoduser, ";
			$sql .= "menumoddate ";
			$sql .= ")";
			$sql .= "values ";
			$sql .= "(";
			
			$sql .= "'$menuid', ";
			$sql .= "'$v_id', ";
			$sql .= "'$menulanguage', ";
			$sql .= "'$menutitle', ";
			$sql .= "'$menustr', ";
			$sql .= "'$menucategory', ";
			$sql .= "'$menugroup', ";
			$sql .= "'$menudesc', ";
			$sql .= "'$menuparentid', ";
			$sql .= "'$menuorder', ";
			$sql .= "'$menuadduser', ";
			$sql .= "'$menuadddate', ";
			$sql .= "'$menumoduser', ";
			$sql .= "'$menumoddate' ";
			$sql .= ")";
			$db->exec(check_sql($sql));
			//$lastinsertid = $db->lastInsertId($id);
			//echo $sql."<br />\n";
			unset($sql);
	}

	//unset the menu session variable
		$_SESSION["menu"] = "";

	require_once "includes/header.php";
	echo "<meta http-equiv=\"refresh\" content=\"2;url=menu_list.php\">\n";
	echo "<div align='center'>\n";
	echo "Restore Complete\n";
	echo "</div>\n";
	require_once "includes/footer.php";
	return;

?>
