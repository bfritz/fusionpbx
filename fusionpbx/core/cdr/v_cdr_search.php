<?php
/* $Id$ */
/*
	v_cdr_search.php
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
if (ifgroup("admin") || ifgroup("superadmin")) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

if (count($_POST)>0) {
	$cdr_id = $_POST["cdr_id"];
	$caller_id_name = $_POST["caller_id_name"];
	$caller_id_number = $_POST["caller_id_number"];
	$destination_number = $_POST["destination_number"];
	$context = $_POST["context"];
	$start_stamp = $_POST["start_stamp"];
	$answer_stamp = $_POST["answer_stamp"];
	$end_stamp = $_POST["end_stamp"];
	$duration = $_POST["duration"];
	$billsec = $_POST["billsec"];
	$hangup_cause = $_POST["hangup_cause"];
	$uuid = $_POST["uuid"];
	$bleg_uuid = $_POST["bleg_uuid"];
	$accountcode = $_POST["accountcode"];
	$read_codec = $_POST["read_codec"];
	$write_codec = $_POST["write_codec"];
	$remote_media_ip = $_POST["remote_media_ip"];
	$network_addr = $_POST["network_addr"];
}
else {

	echo "\n";    
	require_once "includes/header.php";
	echo "<div align='center'>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='2'>\n";

	echo "<tr class='border'>\n";
	echo "	<td align=\"left\">\n";
	echo "      <br>";

	echo "<form method='post' action='v_cdr.php'>";
	echo "<table width='100%' cellpadding='6' cellspacing='0'>";

	echo "<tr>\n";
	echo "<td width='30%' nowrap valign='top'><b>Advanced Search</b></td>\n";
	echo "<td width='70%' align='right' valign='top'><input type='button' class='btn' name='' alt='back' onclick=\"window.location='v_cdr.php'\" value='Back'><br /><br /></td>\n";
	echo "</tr>\n";

	echo "	<tr>";
	echo "		<td class='vncell'>Source Name:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='caller_id_name' value='$caller_id_name'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Source Number:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='caller_id_number' value='$caller_id_number'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Destination Number:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='destination_number' value='$destination_number'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Context:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='context' value='$context'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Start:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='start_stamp' value='$start_stamp'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Answer:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='answer_stamp' value='$answer_stamp'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>End:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='end_stamp' value='$end_stamp'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Duration:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='duration' value='$duration'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Bill Sec:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='billsec' value='$billsec'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Status:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='hangup_cause' value='$hangup_cause'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>UUID:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='uuid' value='$uuid'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Bleg UUID:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='bleg_uuid' value='$bleg_uuid'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Account Code:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='accountcode' value='$accountcode'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Read Codec:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='read_codec' value='$read_codec'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Write Codec:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='write_codec' value='$write_codec'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Remote Media IP:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='remote_media_ip' value='$remote_media_ip'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Network Address:</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='network_addr' value='$network_addr'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td colspan='2' align='right'><input type='submit' name='submit' class='btn' value='Search'></td>";
	echo "	</tr>";
	echo "</table>";
	echo "</form>";


	echo "	</td>";
	echo "	</tr>";
	echo "</table>";
	echo "</div>";


require_once "includes/footer.php";

} //end if not post
?>
