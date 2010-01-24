<?php
/* $Id$ */
/*
	index.php
	Copyright (C) 2008, 2009 Mark J Crane
	All rights reserved.

	Redistribution and use in source and binary forms, with or without
	modification, are permitted provided that the following conditions are met:

	1. Redistributions of source code must retain the above copyright notice,
	   this list of conditions and the following disclaimer.

	2. Redistributions in binary form must reproduce the above copyright
	   notice, this list of conditions and the following disclaimer in the
	   documentation and/or other materials provided with the distribution.

	THIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES,
	INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY
	AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
	AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
	OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
	SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
	INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
	CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
	ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
	POSSIBILITY OF SUCH DAMAGE.
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
	$templateid = checkstr($_POST["templateid"]);
	if (ifgroup("admin") || ifgroup("superadmin")) {
		if (strlen($templateid) > 0) {
			//clear the current default
				$sql = "update v_templates set ";
				$sql .= "template_default = '' ";
				$sql .= "where v_id = '$v_id' ";
				//echo "sql:  ".$sql." <br />"; 
				$db->exec($sql);
				unset($sql);

			//set the new default
				$sql = "update v_templates set ";
				$sql .= "template_default = 'true' ";
				$sql .= "where v_id = '$v_id' ";
				$sql .= "and templateid = '$templateid' ";
				//echo "sql:  ".$sql." <br />"; 
				$db->exec($sql);
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
		echo "	<th colspan='2' align='left'>User Information</th>\n";
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
	$sql = "";
	$sql .= "select * from v_software ";
	$sql .= "where v_id = '$v_id' ";
	$prepstatement = $db->prepare($sql);
	$prepstatement->execute();
	while($row = $prepstatement->fetch()) {
		$softwarename = $row["softwarename"];
		$softwareversion = $row["softwareversion"];
		$softwareurl = $row["softwareurl"];
		break; //limit to 1 row
	}
	unset ($prepstatement);

	echo "<table width=\"100%\" border=\"0\" cellpadding=\"6\" cellspacing=\"0\">\n";
	echo "<tr>\n";
	echo "	<th colspan='2' align='left'>System Information</th>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "	<td width='20%' class=\"vncell\" style='text-align: left;'>\n";
	echo "		Version: \n";
	echo "	</td>\n";
	echo "	<td class=\"vtable\">\n";
	echo "		$softwareversion \n";
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
				echo $shell_result;
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
				echo $shell_result;
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
					echo "	<th colspan='2' align='left' valign='top'>CPU Information</th>\n";
					echo "</tr>\n";
					echo "<tr>\n";
					echo "	<td width='20%' class=\"vncell\" style='text-align: left;'>\n";
					echo "	CPU Status:\n";
					echo "	</td>\n";
					echo "	<td class=\"vtable\">\n";
					echo "	<pre>\n";
					$shell_result = system($shellcmd);
					echo $shell_result;
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
				echo "	<th colspan='2' align='left' valign='top'>CPU Information</th>\n";
				echo "</tr>\n";
				echo "<tr>\n";
				echo "	<td width='20%' class=\"vncell\" style='text-align: left;'>\n";
				echo "	CPU Status:\n";
				echo "	</td>\n";
				echo "	<td class=\"vtable\">\n";
				echo "	<pre>\n";
				$shell_result = system($shellcmd);
				echo $shell_result;
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
	}
	else {
		$driveletter = "/";
	}
	$disksize = round(disk_total_space($driveletter)/1024/1024, 2);
	$disksizefree = round(disk_free_space($driveletter)/1024/1024, 2);
	$diskpercentavailable = round(($disksizefree/$disksize) * 100, 2);

	echo "<table width=\"100%\" border=\"0\" cellpadding=\"6\" cellspacing=\"0\">\n";
	echo "<tr>\n";
	echo "	<th colspan='2' align='left'>Drive Space</th>\n";
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

	echo "<br />";
	echo "<br />";
	echo "<br />";
}


//templates selection
if (ifgroup("admin") || ifgroup("superadmin")) {
	$sql = "";
	$sql .= "select * from v_templates ";
	$sql .= "where v_id = '$v_id' ";
	$sql .= "and template_default = 'true'; ";
	$prepstatement = $db->prepare($sql);
	$prepstatement->execute();
	while($row = $prepstatement->fetch()) {
		$templatename = $row["templatename"];
		break; //limit to 1 row
	}
	unset ($prepstatement);
	echo "<form method='post' name='frm' action=''>\n";
	echo "<table width=\"100%\" border=\"0\" cellpadding=\"6\" cellspacing=\"0\">\n";
	echo "<tr>\n";
	echo "	<th colspan='2' align='left'>Theme</th>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "	<td width='20%' class=\"vncell\" style='text-align: left;'>\n";
	echo "		Template: \n";
	echo "	</td>\n";
	echo "	<td class=\"vtable\">\n";
	$tablename = 'v_templates'; $fieldname = 'templatename'; $sqlwhereoptional = "where v_id = '$v_id' "; $fieldcurrentvalue = $templatename; $fieldvalue = 'templateid';
	echo htmlselect($db, $tablename, $fieldname, $sqlwhereoptional, $fieldcurrentvalue, $fieldvalue);
	echo "<input type='submit' name='submit' class='btn' value='Save'>\n";
	echo "	<br />\n";
	echo "	Select a template to set as the default and then press save.\n";

	echo "	</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</form>\n";

	echo "<br />";
	echo "<br />";
	echo "<br />";
}

//backup
if (ifgroup("admin") || ifgroup("superadmin")) {
	if ($dbtype == 'sqlite') {
		require_once "core/backup/backupandrestore.php";
	}
}

require_once "includes/footer.php";
?>
