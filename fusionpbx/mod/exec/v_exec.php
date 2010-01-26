<?php
/* $Id$ */
/*
	v_exec.php
	Copyright (C) 2008 Mark J Crane
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
	$shellcmd = trim($_POST["shellcmd"]);
	$phpcmd = trim($_POST["phpcmd"]);
	$switchcmd = trim($_POST["switchcmd"]);
}


	require_once "includes/header.php";


	//--- Begin: Edit Area -----------------------------------------------------
		echo "    <script language=\"javascript\" type=\"text/javascript\" src=\"".PROJECT_PATH."/includes/edit_area/edit_area_full.js\"></script>\n";
		echo "    <!-- -->\n";

		echo "	<script language=\"Javascript\" type=\"text/javascript\">\n";
		echo "		// initialisation //load,\n";
		echo "		editAreaLoader.init({\n";
		echo "			id: \"shellcmd\"	// id of the textarea to transform //, |, help\n";
		echo "			,start_highlight: false\n";
		echo "			,display: \"later\"\n";
		echo "			,font_size: \"8\"\n";
		echo "			,allow_toggle: true\n";
		echo "			,language: \"en\"\n";
		echo "			,syntax: \"html\"\n";
		echo "			,toolbar: \"search, go_to_line,|, fullscreen, |, undo, redo, |, select_font, |, syntax_selection, |, change_smooth_selection, highlight, reset_highlight, |, help\" //new_document,\n";
		echo "			,plugins: \"charmap\"\n";
		echo "			,charmap_default: \"arrows\"\n";
		echo "\n";
		echo "    });\n";
		echo "\n";
		echo "\n";
		echo "		editAreaLoader.init({\n";
		echo "			id: \"phpcmd\"	// id of the textarea to transform //, |, help\n";
		echo "			,start_highlight: false\n";
		echo "			,display: \"later\"\n";
		echo "			,font_size: \"8\"\n";
		echo "			,allow_toggle: true\n";
		echo "			,language: \"en\"\n";
		echo "			,syntax: \"php\"\n";
		echo "			,toolbar: \"search, go_to_line,|, fullscreen, |, undo, redo, |, select_font, |, syntax_selection, |, change_smooth_selection, highlight, reset_highlight, |, help\" //new_document,\n";
		echo "			,plugins: \"charmap\"\n";
		echo "			,charmap_default: \"arrows\"\n";
		echo "\n";
		echo "    });\n";
		echo "\n";
		echo "		editAreaLoader.init({\n";
		echo "			id: \"switchcmd\"	// id of the textarea to transform //, |, help\n";
		echo "			,start_highlight: false\n";
		echo "			,display: \"later\"\n";
		echo "			,font_size: \"8\"\n";
		echo "			,allow_toggle: true\n";
		echo "			,language: \"en\"\n";
		echo "			,syntax: \"php\"\n";
		echo "			,toolbar: \"search, go_to_line,|, fullscreen, |, undo, redo, |, select_font, |, syntax_selection, |, change_smooth_selection, highlight, reset_highlight, |, help\" //new_document,\n";
		echo "			,plugins: \"charmap\"\n";
		echo "			,charmap_default: \"arrows\"\n";
		echo "\n";
		echo "    });\n";
		echo "    </script>";
	//--- End: Edit Area -------------------------------------------------------

	echo "<div align='center'>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='2'>\n";

	echo "<tr class='border'>\n";
	echo "	<td align=\"left\">\n";
	echo "      <br>";



	echo "<form method='post' name='frm' action=''>\n";

	echo "<div align='center'>\n";
	echo "<table width='100%'  border='0' cellpadding='6' cellspacing='0'>\n";

	echo "<tr>\n";
	echo "<td align='left' width='30%' nowrap><b>Execute Command</b></td>\n";
	echo "<td width='70%' align='right'>\n";
	//echo "	<input type='button' class='btn' name='' alt='back' onclick=\"window.location='index.php'\" value='Back'>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "    Shell command:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <textarea name='shellcmd' id='shellcmd' rows='7' class='txt' wrap='off'>$shellcmd</textarea\n";
	echo "<br />\n";
	//echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "    PHP command:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <textarea name='phpcmd' id='phpcmd' rows='10' class='txt' wrap='off'>$phpcmd</textarea\n";
	echo "<br />\n";
	echo "Use the following link as a reference for PHP: <a href='http://php.net/manual/en/index.php' target='_blank'>PHP Manual</a>\n";
	echo "</td>\n";
	echo "</tr>\n";


	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	if ($v_name == "freeswitch") {
		echo "    FreeSWITCH Command:\n";
	}
	else {
		echo "    ".ucfirst($v_name)." Command:\n";
	}
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <textarea name='switchcmd' id='switchcmd' rows='7' class='txt' wrap='off'>$switchcmd</textarea\n";
	echo "<br />\n";
	echo "For a list of the valid commands use: help\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "	<tr>\n";
	echo "		<td colspan='2' align='right'>\n";
	echo "			<input type='submit' name='submit' class='btn' value='Execute'>\n";
	echo "		</td>\n";
	echo "	</tr>";

	echo "</table>";

//POST to PHP variables
if (count($_POST)>0) {

	echo "	<tr>\n";
	echo "		<td colspan='2' align='left'>\n";

	//shellcmd
	if (strlen($shellcmd) > 0) {
		echo "<b>shell command:</b>\n";
		echo "<!--\n";
		$shell_result = system($shellcmd);
		echo "-->\n";
		echo "<pre>\n";
		echo htmlentities($shell_result);
		echo "</pre>\n";
	}

	//phpcmd
	if (strlen($phpcmd) > 0) {
		echo "<b>php command:</b>\n";
		echo "<pre>\n";
		$php_result = eval($phpcmd);
		echo htmlentities($php_result);
		echo "</pre>\n";
	}

	//fs cmd
	if (strlen($switchcmd) > 0) {

		$sql = "";
		$sql .= "select * from v_settings ";
		$sql .= "where v_id = '$v_id' ";
		$prepstatement = $db->prepare(check_sql($sql));
		$prepstatement->execute();
		$result = $prepstatement->fetchAll();
		foreach ($result as &$row) {
			//$v_id = $row["v_id"];
			$numbering_plan = $row["numbering_plan"];
			$default_gateway = $row["default_gateway"];
			$default_area_code = $row["default_area_code"];
			$event_socket_ip_address = $row["event_socket_ip_address"];
			$event_socket_port = $row["event_socket_port"];
			$event_socket_password = $row["event_socket_password"];
			$xml_rpc_http_port = $row["xml_rpc_http_port"];
			$xml_rpc_auth_realm = $row["xml_rpc_auth_realm"];
			$xml_rpc_auth_user = $row["xml_rpc_auth_user"];
			$xml_rpc_auth_pass = $row["xml_rpc_auth_pass"];
			$admin_pin = $row["admin_pin"];
			$smtphost = $row["smtphost"];
			$smtpsecure = $row["smtpsecure"];
			$smtpauth = $row["smtpauth"];
			$smtpusername = $row["smtpusername"];
			$smtppassword = $row["smtppassword"];
			$smtpfrom = $row["smtpfrom"];
			$smtpfromname = $row["smtpfromname"];
			$mod_shout_decoder = $row["mod_shout_decoder"];
			$mod_shout_volume = $row["mod_shout_volume"];
			break; //limit to 1 row
		}

		echo "<b>switch command:</b>\n";
		echo "<pre>\n";
		$fp = event_socket_create($event_socket_ip_address, $event_socket_port, $event_socket_password);
		$switch_result = event_socket_request($fp, 'api '.$switchcmd);
		//$switch_result = eval($switchcmd);
		echo htmlentities($switch_result);
		echo "</pre>\n";
	}

	echo "		</td>\n";
	echo "	</tr>";
}

	echo "	</td>";
	echo "	</tr>";
	echo "</table>";
	echo "</div>";

	echo "</form>";


require_once "includes/footer.php";
?>
