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
if (!file_exists($_SERVER['DOCUMENT_ROOT'].PROJECT_PATH."/includes/config.php")){
	header("Location: ".PROJECT_PATH."/install.php");
	exit;
}
require_once "includes/config.php";
require_once "includes/header.php";
require_once "includes/checkauth.php";

if (count($_POST)>0) {
	$templateid = check_str($_POST["templateid"]);
	if (ifgroup("admin") || ifgroup("superadmin")) {
		if (strlen($templateid) > 0) {
			//clear the current default
				$sql = "update v_templates set ";
				$sql .= "template_default = '' ";
				$sql .= "where v_id = '$v_id' ";
				//echo "sql:  ".$sql." <br />"; 
				$db->exec(check_sql($sql));
				unset($sql);

			//set the new default
				$sql = "update v_templates set ";
				$sql .= "template_default = 'true' ";
				$sql .= "where v_id = '$v_id' ";
				$sql .= "and templateid = '$templateid' ";
				//echo "sql:  ".$sql." <br />"; 
				$db->exec(check_sql($sql));
				unset($sql);
				//exit;
		}
	}
}

echo "<br />";

//user information
	if (strlen($_SESSION["username"]) > 0) {
		echo "<table width=\"100%\" border=\"0\" cellpadding=\"6\" cellspacing=\"0\">\n";
		echo "<tr>\n";
		echo "	<th class='th' colspan='2' align='left'>User Information</th>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "	<td width='20%' class=\"vncell\" style='text-align: left;'>\n";
		echo "		UserName: \n";
		echo "	</td>\n";
		echo "	<td class=\"vtable\">\n";
		echo "		".$_SESSION["username"]." \n";
		echo "	</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		echo "<br />";
		echo "<br />";
		echo "<br />";
	}

//get the software information
	echo "<table width=\"100%\" border=\"0\" cellpadding=\"6\" cellspacing=\"0\">\n";
	echo "<tr>\n";
	echo "	<th class='th' colspan='2' align='left'>System Information</th>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "	<td width='20%' class=\"vncell\" style='text-align: left;'>\n";
	echo "		Version: \n";
	echo "	</td>\n";
	echo "	<td class=\"vtable\">\n";
	echo "		".software_version()."\n";
	echo "	</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "	<td width='20%' class=\"vncell\" style='text-align: left;'>\n";
	echo "		Date: \n";
	echo "	</td>\n";
	echo "	<td class=\"vtable\">\n";
	echo "		".date('r')." \n";
	echo "	</td>\n";
	echo "</tr>\n";

	if (ifgroup("admin") || ifgroup("superadmin")) {
		echo "<!--\n";
		$tmp_result = system('uname -a');
		echo "-->\n";
		if (strlen($tmp_result) > 0) {
			echo "<tr>\n";
			echo "	<td width='20%' class=\"vncell\" style='text-align: left;'>\n";
			echo "		Operating System: \n";
			echo "	</td>\n";
			echo "	<td class=\"vtable\">\n";
			echo "		".$tmp_result." \n";
			echo "	</td>\n";
			echo "</tr>\n";
		}
		unset($tmp_result);

		echo "<!--\n";
		$tmp_result = system('uptime');
		echo "-->\n";
		if (strlen($tmp_result) > 0) {
			echo "<tr>\n";
			echo "	<td width='20%' class=\"vncell\" style='text-align: left;'>\n";
			echo "		Uptime: \n";
			echo "	</td>\n";
			echo "	<td class=\"vtable\">\n";
			echo "		".$tmp_result." \n";
			echo "	</td>\n";
			echo "</tr>\n";
		}
		unset($tmp_result);
	}

	echo "</table>\n";
	echo "\n";

	echo "<br />";
	echo "<br />";
	echo "<br />";


//memory information
	if (ifgroup("admin") || ifgroup("superadmin")) {
		//linux
			echo "<!--\n";
			$shellcmd='free';
			$shell_result = system($shellcmd);
			echo "-->\n";
			if (strlen($shell_result) > 0) {
				echo "<table width=\"100%\" border=\"0\" cellpadding=\"6\" cellspacing=\"0\">\n";
				echo "<tr>\n";
				echo "	<th colspan='2' align='left' valign='top'>Memory Information</th>\n";
				echo "</tr>\n";
				echo "<tr>\n";
				echo "	<td width='20%' class=\"vncell\" style='text-align: left;'>\n";
				echo "	Memory Status:\n";
				echo "	</td>\n";
				echo "	<td class=\"vtable\">\n";
				echo "	<pre>\n";
				$shell_result = system($shellcmd);
				foreach ($shell_result as $value) {
						echo "$value<br>";
				}
				echo "</pre>\n";
				unset($shell_result);
				echo "	</td>\n";
				echo "</tr>\n";
				echo "</table>\n";
				echo "<br />";
				echo "<br />";
				echo "<br />";
			}

		//freebsd
			echo "<!--\n";
			$shellcmd='sysctl vm.vmtotal';
			$shell_result = system($shellcmd);
			echo "-->\n";
			if (strlen($shell_result) > 0) {
				echo "<table width=\"100%\" border=\"0\" cellpadding=\"6\" cellspacing=\"0\">\n";
				echo "<tr>\n";
				echo "	<th colspan='2' align='left' valign='top'>Memory Information</th>\n";
				echo "</tr>\n";
				echo "<tr>\n";
				echo "	<td width='20%' class=\"vncell\" style='text-align: left;'>\n";
				echo "	Memory Status:\n";
				echo "	</td>\n";
				echo "	<td class=\"vtable\">\n";
				echo "	<pre>\n";
				$shell_result = system($shellcmd);
				foreach ($shell_result as $value) {
						echo "$value<br>";
				}
				echo "</pre>\n";
				unset($shell_result);
				echo "	</td>\n";
				echo "</tr>\n";
				echo "</table>\n";
				echo "<br />";
				echo "<br />";
				echo "<br />";
			}
	}

//memory information
	if (ifgroup("admin") || ifgroup("superadmin")) {
		//linux
			echo "<!--\n";
			if(stristr(system('uname -r'), 'astlinux') === FALSE) {
				$shellcmd="ps -e -o pcpu,cpu,nice,state,cputime,args --sort pcpu | sed '/^ 0.0 /d'";
				$shell_result = system($shellcmd);
				echo "-->\n";
				if (strlen($shell_result) > 0) {
					echo "<table width=\"100%\" border=\"0\" cellpadding=\"6\" cellspacing=\"0\">\n";
					echo "<tr>\n";
					echo "	<th class='th' colspan='2' align='left' valign='top'>CPU Information</th>\n";
					echo "</tr>\n";
					echo "<tr>\n";
					echo "	<td width='20%' class=\"vncell\" style='text-align: left;'>\n";
					echo "	CPU Status:\n";
					echo "	</td>\n";
					echo "	<td class=\"vtable\">\n";
					echo "	<pre>\n";
					$last_line = exec($shellcmd, $shell_result);
					foreach ($shell_result as $value) {
						echo substr($value, 0, 100);
						echo "<br />";
					}
					echo "</pre>\n";
					unset($shell_result);
					echo "	</td>\n";
					echo "</tr>\n";
					echo "</table>\n";
					echo "<br />";
					echo "<br />";
					echo "<br />";
				}
			}

		//freebsd
			echo "<!--\n";
			$shellcmd='top';
			$shell_result = system($shellcmd);
			echo "-->\n";
			if (strlen($shell_result) > 0) {
				echo "<table width=\"100%\" border=\"0\" cellpadding=\"6\" cellspacing=\"0\">\n";
				echo "<tr>\n";
				echo "	<th class='th' colspan='2' align='left' valign='top'>CPU Information</th>\n";
				echo "</tr>\n";
				echo "<tr>\n";
				echo "	<td width='20%' class=\"vncell\" style='text-align: left;'>\n";
				echo "	CPU Status:\n";
				echo "	</td>\n";
				echo "	<td class=\"vtable\">\n";
				echo "	<pre>\n";
				$last_line = exec($shellcmd, $shell_result);
				foreach ($shell_result as $value) {
					echo substr($value, 0, 100);
					echo "<br />";
				}
				echo "</pre>\n";
				unset($shell_result);
				echo "	</td>\n";
				echo "</tr>\n";
				echo "</table>\n";
				echo "<br />";
				echo "<br />";
				echo "<br />";
			}
	}

//drive space
if (ifgroup("admin") || ifgroup("superadmin")) {
	//disk_free_space returns the number of bytes available on the drive;
	//1 kilobyte = 1024 byte
	//1 megabyte = 1024 kilobyte
	if (stristr(PHP_OS, 'WIN')) { 
		$driveletter = substr($_SERVER["DOCUMENT_ROOT"], 0, 2);
		$disksize = round(disk_total_space($driveletter)/1024/1024, 2);
		$disksizefree = round(disk_free_space($driveletter)/1024/1024, 2);
		$diskpercentavailable = round(($disksizefree/$disksize) * 100, 2);

		echo "<table width=\"100%\" border=\"0\" cellpadding=\"6\" cellspacing=\"0\">\n";
		echo "<tr>\n";
		echo "	<th class='th' colspan='2' align='left'>Drive Space</th>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "	<td width='20%' class=\"vncell\" style='text-align: left;'>\n";
		echo "		Capacity: \n";
		echo "	</td>\n";
		echo "	<td class=\"vtable\">\n";
		echo "		$disksize mb\n";
		echo "	</td>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "	<td width='20%' class=\"vncell\" style='text-align: left;'>\n";
		echo "		Free Space: \n";
		echo "	</td>\n";
		echo "	<td class=\"vtable\">\n";
		echo "		$disksizefree mb\n";
		echo "	</td>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "	<td width='20%' class=\"vncell\" style='text-align: left;'>\n";
		echo "		Percent Free: \n";
		echo "	</td>\n";
		echo "	<td class=\"vtable\">\n";
		echo "		$diskpercentavailable% \n";
		echo "	</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		echo "\n";
	}
	else {
		echo "<table width=\"100%\" border=\"0\" cellpadding=\"6\" cellspacing=\"0\">\n";
		echo "<tr>\n";
		echo "	<th class='th' colspan='2' align='left'>Drive Information</th>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "	<td width='20%' class=\"vncell\" style='text-align: left;'>\n";
		echo "		Drive Space: \n";
		echo "	</td>\n";
		echo "	<td class=\"vtable\">\n";
		echo "<pre>\n";
		system("df -h");
		echo "</pre>\n";
		echo "	</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
	}

	echo "<br />";
	echo "<br />";
	echo "<br />";
}

/*
if (ifgroup("member")) {
	//find the conference extensions from the dialplan include details

		//define the conference array
			$conference_array = array ();

		$sql = "";
		$sql .= "select * from v_dialplan_includes_details ";
		$sql .= "where v_id = $v_id ";
		$prepstatement = $db->prepare(check_sql($sql));
		$prepstatement->execute();
		$x = 0;
		$result = $prepstatement->fetchAll();
		foreach ($result as &$row) {
			$dialplan_include_id = $row["dialplan_include_id"];
			//$tag = $row["tag"];
			//$fieldorder = $row["fieldorder"];
			$fieldtype = $row["fieldtype"];
			//$fielddata = $row["fielddata"];
			if ($fieldtype == "conference") {
				//echo "dialplan_include_id: $dialplan_include_id<br />";
				//echo "fielddata: $fielddata<br />";
				$conference_array[$x]['dialplan_include_id'] = $dialplan_include_id;
				$x++;
			}
		}
		unset ($prepstatement);
		//print_r($conference_array);
		foreach ($conference_array as &$row) {
			echo "--".$row['dialplan_include_id']."--<br />\n";
		}
}
*/

//backup
if (ifgroup("superadmin")) {
	if ($dbtype == 'sqlite') {
		require_once "core/backup/backupandrestore.php";
	}
}

require_once "includes/footer.php";
?>
