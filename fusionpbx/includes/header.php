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
	$sql .= "select * from v_menu ";
	$sql .= "where menustr = '".$_SERVER["SCRIPT_NAME"]."' ";
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	foreach ($result as &$row) {
		$_SESSION["menu_parent_guid"] = $row["menu_parent_guid"];
		break;
	}
	unset($result);

//get the content
	$sql = "";
	$sql .= "select * from v_rss ";
	$sql .= "where v_id = '$v_id' ";
	$sql .= "and rsscategory = 'content' ";
	if (strlen($content) == 0) {
		$sql .= "and rsslink = '".$_SERVER["PHP_SELF"]."' ";
	}
	else {
		$sql .= "and rsslink = '".$content."' ";
	}
	$sql .= "and length(rssdeldate) = 0 ";
	$sql .= "or v_id = '$v_id' ";
	$sql .= "and rsscategory = 'content' ";
	if (strlen($content) == 0) {
		$sql .= "and rsslink = '".$_SERVER["PHP_SELF"]."' ";
	}
	else {
		$sql .= "and rsslink = '".$content."' ";
	}
	$sql .= "and rssdeldate is null ";
	$sql .= "order by rssorder asc ";
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	$resultcount = count($result);

	$customtitle = '';
	foreach($result as $row) {
		$template_rsssubcategory = $row[rsssubcategory];
		if (strlen($row[rssgroup]) == 0) {
			//content is public
			$content_from_db = &$row[rssdesc];
			$customtitle = $row[rsstitle];
		}
		else {
			if (ifgroup($row[rssgroup])) { //viewable only to designated group
				$content_from_db = &$row[rssdesc];
				$customtitle = $row[rsstitle];
			}
		}
	} //end foreach
	unset($sql, $result, $rowcount);

//start the output buffer
	ob_start();

?>
