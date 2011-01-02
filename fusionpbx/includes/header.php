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

ob_end_clean();
ob_start();

//$header = ''; //disable alternate header
//$strheader = str_replace ("<!--{menu}-->", $_SESSION["menu"], $strheader);

$strheadertop ='';
if (isset($_SERVER['HTTP_USER_AGENT']) && 
(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false))
	//return true;
	$strheadertop .= "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
else {
	//return false;
}
//$strheadertop .= "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\">\n";

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
	//echo $sql;
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

//set a default template
	if (strlen($_SESSION["template_name"]) == 0) { $_SESSION["template_name"] = 'default'; }

//set a default template
	if (strlen($template_rsssubcategory) > 0) {
		//this template was assigned by the content manager
			//get the contents of the template and save it to the template variable
			$template = file_get_contents($_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/themes/'.$template_rsssubcategory.'/template.php');
	}
	else {
		//get the contents of the template and save it to the template variable
			$template = file_get_contents($_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/themes/'.$_SESSION["template_name"].'/template.php');
	}

//start the output buffer
	ob_start();
	$template = $strheadertop.$template;
	eval('?>' . $template . '<?php ');
	$template = ob_get_contents(); //get the output from the buffer
	ob_end_clean(); //clean the buffer

//get the menu
	require_once "includes/menu.php";

//start the output buffer
	ob_start();

?>
