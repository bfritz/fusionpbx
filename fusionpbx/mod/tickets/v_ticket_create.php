<?php
/* $Id$ */
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
	Ken Rice <krice@tollfreegateway.com>
	Mark J Crane <markjcrane@fusionpbx.com>
*/

include "root.php";
require_once "includes/config.php";
require_once "includes/checkauth.php";

if (permission_exists('ticket_add') || permission_exists('ticket_update')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

require_once "includes/header.php";

$v_domain = $_SESSION['domains'][$v_id]['domain'];

//get a list of Available Queues
$sql = "";
$sql .= "select * from v_ticket_queues ";
$sql .= "where v_id = '$v_id' ";
$sql .= "order by queue_name ";
$prepstatement = $db->prepare(check_sql($sql));
$prepstatement->execute();
$x = 0; 
$result = $prepstatement->fetchAll();
foreach ($result as &$row) { 
        $queues[$x] = $row;
        $x++;
}
unset ($prepstatement);

//add or update the database
if (isset($_REQUEST["id"])) {
	$action = "update";
	$profile_id = check_str($_REQUEST["id"]);
} else {  
	$action = "add";
}


if ($action == "update") {
	// TODO: Check to see if Ticket Exists and user has access to that ticket then redirect to that ticket else Display Ticket Error

}

if ((!isset($_REQUEST['submit'])) || ($_REQUEST['submit'] != 'Save')) {
	// If we arent saving a Profile Display the form.
	include "ticket_create.php";	
	goto end;
}

foreach ($_REQUEST as $field => $data){
	$request[$field] = check_str($data);
}

// DataChecking Goes Here
$error = "";
if (strlen($request['subject']) < 1) $error .= "Ticket Subject is a Required Field<br />\n";
if (strlen($request['problem_description']) < 1) $error .= "Ticket Body is a Required Field<br />\n";
if (strlen($error) > 0) { 
	include "errors.php";
	$profile = $request;
	include "profile_edit.php";	
	goto end;
}

// Save New Entry
if ($action == "add" && permission_exists('ticket_add')) {
	$ticket_uuid = guid();
	$sql = "";
	$sql .= "insert into v_tickets (";
 	$sql .= "v_id, ";
 	$sql .= "queue_id, ";
 	$sql .= "user_id, ";
 	$sql .= "customer_id, ";
 	$sql .= "subject, ";
 	$sql .= "create_user_id, ";
 	$sql .= "create_stamp, ";
 	$sql .= "last_update_user_id, ";
 	$sql .= "last_update_stamp, ";
 	$sql .= "ticket_uuid, ";
 	$sql .= "ticket_status, ";
 	$sql .= "customer_ticket_number ";
	$sql .= ") values (";
 	$sql .= "$v_id, ";
 	$sql .= "'" . $request['queue_id'] . "', ";
 	$sql .= "'" . $_SESSION['user_id'] . "', ";
 	$sql .= "'" . $_SESSION['customer_id'] . "', ";
 	$sql .= "'" . $request['subject'] . "', ";
 	$sql .= "'" . $_SESSION['user_id'] . "', ";
 	$sql .= "now(), ";
 	$sql .= "'" . $_SESSION['user_id'] . "', ";
 	$sql .= "now(), ";
 	$sql .= "'" . $ticket_uuid . "', ";
 	$sql .= "'1', ";
 	$sql .= "'" . $request['customer_ticket_number'] . "'";
	$sql .= ") ";
	if ($db_type == "pgsql") {
	 	$sql .= "RETURNING ticket_id;";
		$prepstatement = $db->prepare(check_sql($sql));
		$prepstatement->execute();
        	$result = $prepstatement->fetchAll();
		$ticket_id = $result[0]['ticket_id'];
	} elseif ($db_type == "sqlite" || $db_type == "mysql" ) {
                $db->exec(check_sql($sql));
		$ticket_id = $db->lastInsertId();
	}

	$ticket_number = date("ymd") . "-" . sprintf("%03d", substr($ticket_id, -3));
	
	$sql = "UPDATE v_tickets set ticket_number = '". $ticket_number. "' where ticket_id = " . $ticket_id . " ";
	$db->exec(check_sql($sql));

	$sql = "";
	$sql .= "INSERT into v_ticket_notes (";
	$sql .= "ticket_id, ";
	$sql .= "create_user_id, ";
	$sql .= "create_stamp, ";
	$sql .= "ticket_note ";
	$sql .= ") VALUES ( ";
	$sql .= "$ticket_id, ";
	$sql .= "'" . $_SESSION['user_id'] . "', ";
	$sql .= "now(), ";
	$sql .= "'" . base64_encode($request['problem_description']) . "' ";
	$sql .= ") ";
	$db->exec(check_sql($sql));
	

	$sql = "";
	$sql .= "SELECT * from v_ticket_queues ";
	$sql .= "where queue_id = " . $request['queue_id'] . " ";
	$sql .= "and v_id = $v_id ";
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$x = 0; 
	$result = $prepstatement->fetchAll();
	foreach ($result as &$row) { 
        	$queue = $row;
        	break;
	}
	
	$subject = sprintf("[%s] New Ticket: %s", $queue['queue_name'], $request['subject']);
	$to = $queue['queue_email'];
	$message = "";
	$message .= "Ticket Number $ticketnumber has been created by $username in " . $queue['queue_name'] . "\n";
	$message .= "Ticket Link: http://" . $_SESSION['v_domain'] . PROJECT_PATH . "/mod/tickets/v_ticket_update.php?uuid=" . urlencode($ticket_uuid). "\n";
	$message .= "Ticket body: \n";
	$message .= $request['problem_description'] . "\n";
	$from = "From: " . $_SESSION['support_email'];
	mail($to, $subject, $message, $from);

unset ($prepstatement);




	goto writeout;

} 
writeout:
include "client_template.php";

end:
//show the footer
require_once "includes/footer.php";

?>
