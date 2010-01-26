<?php
/* $Id$ */
/*
	v_auto_attendant_delete.php
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

if (count($_GET)>0) {
	$id = $_GET["id"];
}

if (strlen($id)>0) {

	//delete child data
		$sql = "";
		$sql .= "delete from v_auto_attendant_options ";
		$sql .= "where v_id = $v_id ";
		$sql .= "and auto_attendant_id = '$id' ";
		$prepstatement = $db->prepare(check_sql($sql));
		$prepstatement->execute();
		unset($sql);

	//delete parent data
		$sql = "";
		$sql .= "delete from v_auto_attendant ";
		$sql .= "where v_id = $v_id ";
		$sql .= "and auto_attendant_id = '$id' ";
		$prepstatement = $db->prepare(check_sql($sql));
		$prepstatement->execute();
		unset($sql);

	//delete the dialplan entries
		$sql = "";
		$sql .= "select * from v_dialplan_includes ";
		$sql .= "where v_id = '$v_id' ";
		$sql .= "and opt1name = 'auto_attendant_id' ";
		$sql .= "and opt1value = '".$id."' ";
		//echo "sql: ".$sql."<br />\n";
		$prepstatement2 = $db->prepare($sql);
		$prepstatement2->execute();
		while($row2 = $prepstatement2->fetch()) {
			$dialplan_include_id = $row2['dialplan_include_id'];
			//echo "dialplan_include_id: ".$dialplan_include_id."<br />\n";
			break; //limit to 1 row
		}
		unset ($sql, $prepstatement2);

		$sql = "";
		$sql = "delete from v_dialplan_includes_details ";
		$sql .= "where v_id = '$v_id' ";
		$sql .= "and dialplan_include_id = '$dialplan_include_id' ";
		//echo "sql: ".$sql."<br />\n";
		$db->query($sql);
		unset($sql);

		$sql = "";
		$sql = "delete from v_dialplan_includes ";
		$sql .= "where v_id = '$v_id' ";
		$sql .= "and opt1name = 'auto_attendant_id' ";
		$sql .= "and opt1value = '$id' ";
		//echo "sql: ".$sql."<br />\n";
		$db->query($sql);
		unset($sql);

	//synchronize the xml config
		sync_package_v_auto_attendant();

	//synchronize the xml config
		sync_package_v_dialplan_includes();

}

require_once "includes/header.php";
echo "<meta http-equiv=\"refresh\" content=\"2;url=v_auto_attendant.php\">\n";
echo "<div align='center'>\n";
echo "Delete Complete\n";
echo "</div>\n";

require_once "includes/footer.php";
return;

?>

