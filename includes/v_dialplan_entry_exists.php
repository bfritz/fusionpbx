<?php
/* $Id$ */
/*
	v_dialplan_entry_exists.php
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

	// Recordings, make sure it exists in the dialplan if not add it
		$v_recording_action = 'add';
		$sql = "";
		$sql .= "select * from v_dialplan_includes ";
		$sql .= "where v_id = '$v_id' ";
		$sql .= "and opt1name = 'recordings' ";
		$sql .= "and opt1value = '732673' ";
		$prepstatement2 = $db->prepare($sql);
		$prepstatement2->execute();
		while($row2 = $prepstatement2->fetch()) {
			$v_recording_action = 'update';
			break; //limit to 1 row
		}
		unset ($sql, $prepstatement2);
		//echo "action: ".$action."<br />";

		if ($v_recording_action == 'add') {
			//create recordings extension in the dialplan
				$extensionname = 'Recordings';
				$dialplanorder ='9001';
				$context = 'default';
				$enabled = 'true';
				$descr = '*732673 default system recordings tool';
				$opt1name = 'recordings';
				$opt1value = '732673';
				$dialplan_include_id = v_dialplan_includes_add($v_id, $extensionname, $dialplanorder, $context, $enabled, $descr, $opt1name, $opt1value);

				$tag = 'condition'; //condition, action, antiaction
				$fieldtype = 'destination_number';
				$fielddata = '^\*(732673)$';
				$fieldorder = '000';
				v_dialplan_includes_details_add($v_id, $dialplan_include_id, $tag, $fieldorder, $fieldtype, $fielddata);

				$tag = 'action'; //condition, action, antiaction
				$fieldtype = 'javascript';
				$fielddata = 'recordings.js';
				$fieldorder = '001';
				v_dialplan_includes_details_add($v_id, $dialplan_include_id, $tag, $fieldorder, $fieldtype, $fielddata);
		}


	// DISA make sure it exists in the dialplan if not add it
		$v_disa_action = 'add';
		$sql = "";
		$sql .= "select * from v_dialplan_includes ";
		$sql .= "where v_id = '$v_id' ";
		$sql .= "and opt1name = 'disa' ";
		$sql .= "and opt1value = '3472' ";
		$prepstatement2 = $db->prepare($sql);
		$prepstatement2->execute();
		while($row2 = $prepstatement2->fetch()) {
			$v_disa_action = 'update';
			break; //limit to 1 row
		}
		unset ($sql, $prepstatement2);
		//echo "action: ".$action."<br />";

		if ($v_disa_action == 'add') {
			//create recordings extension in the dialplan
				$extensionname = 'DISA';
				$dialplanorder ='9001';
				$context = 'default';
				$enabled = 'true';
				$descr = '*3472 Direct Inward System Access ';
				$opt1name = 'disa';
				$opt1value = '3472';
				$dialplan_include_id = v_dialplan_includes_add($v_id, $extensionname, $dialplanorder, $context, $enabled, $descr, $opt1name, $opt1value);

				$tag = 'condition'; //condition, action, antiaction
				$fieldtype = 'destination_number';
				$fielddata = '^\*(3472)$';
				$fieldorder = '000';
				v_dialplan_includes_details_add($v_id, $dialplan_include_id, $tag, $fieldorder, $fieldtype, $fielddata);

				$tag = 'action'; //condition, action, antiaction
				$fieldtype = 'javascript';
				$fielddata = 'disa.js';
				$fieldorder = '001';
				v_dialplan_includes_details_add($v_id, $dialplan_include_id, $tag, $fieldorder, $fieldtype, $fielddata);
		}

	// synchronize the dialplan
		if ($v_recording_action == 'add' || $v_disa_action == 'add') {
			sync_package_v_dialplan_includes();
		}

?>