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

//if reloadxml then run reloadacl, and reloadxml
	if ($_SESSION["reload_xml"]) {
		if ($_SESSION['apply_settings'] == "true") {
			//show the apply settings prompt
		}
		else {
			//create the event socket connection
				$fp = event_socket_create($_SESSION['event_socket_ip_address'], $_SESSION['event_socket_port'], $_SESSION['event_socket_password']);
			//reload the access control list this also runs reloadxml
				$tmp_cmd = 'api reloadacl';
				$response = event_socket_request($fp, $tmp_cmd);
				unset($tmp_cmd);
				usleep(1000);
			//close the connection
				fclose($fp);
			//clear the apply settings reminder
				$_SESSION["reload_xml"] = false;
		}
	}

//set a default template
	if (strlen($_SESSION["template_name"]) == 0) { $_SESSION["template_name"] = 'default'; }

//set a default template
	$v_template_path = $_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/themes';
	if (strlen($_SESSION["template_name"])==0) {
		//get the contents of the template and save it to the template variable
		$template_full_path = $v_template_path.'/'.$_SESSION["template_name"].'/template.php';
		if (!file_exists($template_full_path)) {
			$_SESSION["template_name"] = 'default';
		}
	}

//start the output buffer
	include $v_template_path.'/'.$_SESSION["template_name"].'/config.php';

//start the output buffer
	ob_start();

// get the content
	if (strlen($content) == 0) {
		$content = $_GET["c"]; //link
	}
	//echo "content: ".$content;

//get the parent id
	$sql = "";
	$sql .= "select * from v_menu_items ";
	$sql .= "where menu_uuid = '".$_SERVER["menu_uuid"]."' ";
	$sql .= "and menu_item_str = '".$_SERVER["SCRIPT_NAME"]."' ";
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	foreach ($result as &$row) {
		$_SESSION["menu_item_parent_uuid"] = $row["menu_item_parent_uuid"];
		break;
	}
	unset($result);

//get the content
	$sql = "";
	$sql .= "select * from v_rss ";
	$sql .= "where domain_uuid = '$domain_uuid' ";
	$sql .= "and rss_category = 'content' ";
	if (strlen($content) == 0) {
		$sql .= "and rss_link = '".$_SERVER["PHP_SELF"]."' ";
	}
	else {
		$sql .= "and rss_link = '".$content."' ";
	}
	$sql .= "and length(rss_del_date) = 0 ";
	$sql .= "or domain_uuid = '$domain_uuid' ";
	$sql .= "and rss_category = 'content' ";
	if (strlen($content) == 0) {
		$sql .= "and rss_link = '".$_SERVER["PHP_SELF"]."' ";
	}
	else {
		$sql .= "and rss_link = '".$content."' ";
	}
	$sql .= "and rss_del_date is null ";
	$sql .= "order by rss_order asc ";
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	$resultcount = count($result);

	$customtitle = '';
	foreach($result as $row) {
		$template_rss_sub_category = $row[rss_sub_category];
		if (strlen($row[rss_group]) == 0) {
			//content is public
			$content_from_db = &$row[rss_desc];
			$customtitle = $row[rss_title];
		}
		else {
			if (ifgroup($row[rss_group])) { //viewable only to designated group
				$content_from_db = &$row[rss_desc];
				$customtitle = $row[rss_title];
			}
		}
	} //end foreach
	unset($sql, $result, $rowcount);

//start the output buffer
	ob_start();

?>
