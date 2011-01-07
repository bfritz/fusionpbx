<?php
/* $Id$ */
/*
	call.php
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

echo "<br />";
echo "<div align='center'>\n";

$sql = "";
$sql .= "select * from v_settings ";
$sql .= "where v_id = '$v_id' ";
$prepstatement = $db->prepare($sql);
$prepstatement->execute();
while($row = $prepstatement->fetch()) {
	$event_socket_port = $row["event_socket_port"];
	$event_socket_password = $row["event_socket_password"];
	$event_socket_ip_address = $row["event_socket_ip_address"];
	break; //limit to 1 row
}

if (is_array($_REQUEST) && !empty($_REQUEST['src']) && !empty($_REQUEST['dest'])) {
    //$src = str_replace(array('.', '(', ')', '-', ' '), '', $_REQUEST['src']);
    //$src = ereg_replace('^(1|\+1)?([2-9][0-9]{2}[2-9][0-9]{6})$', '1\2', $src);
	$src = $_REQUEST['src'];
	$dest = $_REQUEST['dest'];
	$cid_name = $_REQUEST['cid_name'];
	$cid_number = $_REQUEST['cid_number'];
	if (strlen($cid_number) == 0) { $cid_number = $src;}

	//$switchcmd = "api originate /user/$dest@${domain} &transfer($src XML default)";
	//$switchcmd = "api originate sofia/gateway/viatalk.com/$src &bridge(sofia/gateway/viatalk.com/$dest)";

	//working
	$switchcmd = "api originate {ignore_early_media=true,effective_caller_id_name=$cid_name,effective_caller_id_number=$cid_number}loopback/$src/default/XML &transfer($dest XML default)";

	//working
	//$switchcmd = "api originate {ignore_early_media=true}sofia/gateway/viatalk.com/$src &transfer($dest XML default)";
    //echo $switchcmd;

	//display the last command
		echo "<div align='center'>\n";
		echo "<table width='40%'>\n";
		echo "<tr>\n";
		echo "<th align='left'>Message</th>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "<td class='rowstyle1'><strong>$src has called $dest</strong></td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		echo "</div>\n";

		//echo "<table>\n";
		//echo "<tr><td>Caller ID Name:</td><td>$cid_name</td></tr>\n";
		//echo "<tr><td>Caller ID Nunber:</td><td>$cid_number</td></tr>\n";
		//echo "<tr><td>Source:</td><td>$src</td></tr>\n";
		//echo "<tr><td>Destination:</td><td>$dest</td></tr>\n";
		//echo "</table>\n";

	$fp = event_socket_create($event_socket_ip_address, $event_socket_port, $event_socket_password);
	$switch_result = event_socket_request($fp, $switchcmd);
	//$switch_result = eval($switchcmd);
	echo "<pre>\n";
	echo $switch_result;
	echo "</pre>\n";
}

echo "	<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";
echo "	<tr>\n";
echo "	<td align='left'><span class=\"vexpl\"><span class=\"red\"><strong>Click to Call\n";
echo "		</strong></span></span>\n";
echo "	</td>\n";
echo "	<td align='right'>\n";
//echo "		<input type='button' class='btn' value='home' onclick=\"document.location.href='/index.php';\">\n";
echo "	</td>\n";
echo "	</tr>\n";
echo "	<tr>\n";
echo "	<td align='left' colspan='2'>\n";
echo "		<span class=\"vexpl\">\n";
echo "			Provide the following information to make a call from the source number to the destination number.\n";
echo "		</span>\n";
echo "	</td>\n";
echo "\n";
echo "	</tr>\n";
echo "	</table>";

echo "	<br />";

echo "<form>\n";
echo "<table border='0' width='100%' cellpadding='6' cellspacing='0'\n";
echo "<tr><td class='vncellreq' width='40%'>Caller ID Name:</td><td class='vtable' align='left'><input name=\"cid_name\" value='$cid_name' class='formfld'></td></tr>\n";
echo "<tr><td class='vncellreq'>Caller ID Number:</td><td class='vtable' align='left'><input name=\"cid_number\" value='$cid_number' class='formfld'></td></tr>\n";
echo "<tr><td class='vncellreq'>Source Number:</td><td class='vtable' align='left'><input name=\"src\" value='$src' class='formfld'></td></tr>\n";
echo "<tr><td class='vncellreq'>Destination Number:</td><td class='vtable' align='left'><input name=\"dest\" value='' class='formfld'></td></tr>\n";
echo "<tr><td colspan='2' align='right'><input type=\"submit\" class='btn' value=\"Call\"></td></tr>";
echo "</table>\n";
echo "</form>";
echo "</div>\n";


require_once "includes/footer.php";
?>
