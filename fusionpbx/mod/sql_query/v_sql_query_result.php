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
	exit;
}

if (count($_POST)>0) {
	$sql_cmd = trim($_POST["sql_cmd"]);
	$table_name = trim($_POST["table_name"]);
	if (strlen($sql_cmd) == 0) { $sql_cmd = "select * from ".$table_name; }
}



//POST to PHP variables
if (count($_POST)>0) {
	echo "<html>\n";
	echo "<head>\n";
	echo "<style type='text/css'>\n";
	echo "\n";
	echo "body {\n";
	echo "	font-size: 13px;\n";
	echo "	color: #444444;\n";
	echo "}\n";
	echo "\n";
	echo "th {\n";
	echo "	border-top: 1px solid #444444;\n";
	echo "	border-bottom: 1px solid #444444;\n";
	echo "	color: #FFFFFF;\n";
	echo "	font-size: 12px;\n";
	echo "	font-family: arial;\n";
	echo "	font-weight: bold;\n";
	echo "	/*background-color: #506eab;*/\n";
	echo "	background-image: url(".PROJECT_PATH."'/themes/horizontal/background_th.png');\n";
	echo "	padding-top: 4px;\n";
	echo "	padding-bottom: 4px;\n";
	echo "	padding-right: 7px;\n";
	echo "	padding-left: 7px;\n";
	echo "}\n";
	echo "\n";
	echo ".rowstyle0 {\n";
	echo "	background-image: url(".PROJECT_PATH."'/themes/horizontal/background_cell.gif');\n";
	echo "	border-bottom: 1px solid #999999;\n";
	echo "	color: #444444;\n";
	echo "	text-align: left;\n";
	echo "	padding-top: 4px;\n";
	echo "	padding-bottom: 4px;\n";
	echo "	padding-right: 7px;\n";
	echo "	padding-left: 7px;\n";
	echo "}\n";
	echo "\n";
	echo ".rowstyle0 a:link{ color:#444444; }\n";
	echo ".rowstyle0 a:visited{ color:#444444; }\n";
	echo ".rowstyle0 a:hover{ color:#444444; }\n";
	echo ".rowstyle0 a:active{ color:#444444; }\n";
	echo "\n";
	echo ".rowstyle1 {\n";
	echo "	border-bottom: 1px solid #999999;\n";
	echo "	background-color: #FFFFFF;\n";
	echo "	color: #444444;\n";
	echo "	text-align: left;\n";
	echo "	padding-top: 4px;\n";
	echo "	padding-bottom: 4px;\n";
	echo "	padding-right: 7px;\n";
	echo "	padding-left: 7px;\n";
	echo "}\n";
	echo "\n";
	echo "</style>";
	echo "</head>\n";
	echo "<body>\n";
	$c = 0;
	$rowstyle["0"] = "rowstyle0";
	$rowstyle["1"] = "rowstyle1";

	echo "<b>SQL Query:</b><br>\n";
	echo "".$sql_cmd."<br /><br />";

	//sql cmd
	if (strlen($sql_cmd) > 0) {
		try {
			$prepstatement = $db->prepare(check_sql($sql_cmd));
			if ($prepstatement) { 
				$prepstatement->execute();
				$result = $prepstatement->fetchAll(PDO::FETCH_ASSOC);
			}

			echo "<b>Results: ".count($result)."</b><br />";

			echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
			$x = 0;
			foreach ($result[0] as $key => $value) {
				echo "<th>".$key."</th>";
				$column_array[$x] = $key;
				$x++;
			}

			foreach ($result as &$row) {
				echo "<tr>\n";
				foreach ($column_array as $column) {
					echo "<td class='".$rowstyle[$c]."'>&nbsp;".$row[$column]."&nbsp;</td>";
				}
				echo "</tr>\n";
				if ($c==0) { $c=1; } else { $c=0; }
			}
			echo "</table>\n";

		}
		catch (PDOException $error) {
			print "error: " . $error->getMessage() . "<br/>";
			//die();
		}
	}

	echo "<body>\n";
	echo "<html>\n";

}

?>
