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
		//$menuparentid = $row["menuparentid"];
		$_SESSION["menuparentid"] = $row["menuparentid"];
		break; //limit to 1 row
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

//get the template information
	$sql = "";
	$sql .= "select * from v_templates ";
	if (strlen($template_rsssubcategory) > 0) {
		$sql .= "where v_id = '$v_id' ";
		$sql .= "and templatename = '$template_rsssubcategory' ";
	}
	else {
		$sql .= "where v_id = '$v_id' ";
		$sql .= "and template_default = 'true' ";
	}
	//echo $sql;
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	foreach ($result as &$row) {
		$template = base64_decode($row["template"]);
		$templatemenutype = $row["templatemenutype"];
		$templatemenucss = base64_decode($row["templatemenucss"]);

		//$adduser = $row["adduser"];
		//$adddate = $row["adddate"];
		break; //limit to 1 row
	}


$body = $content_from_db.ob_get_contents(); //get the output from the buffer
ob_end_clean(); //clean the buffer

ob_start();
$template = $strheadertop.$template;
eval('?>' . $template . '<?php ');
$template = ob_get_contents(); //get the output from the buffer
ob_end_clean(); //clean the buffer


require_once "includes/menu.php";

//prepare the output
	$customhead = $customhead.$templatemenucss;
	//$customhead ='test';
	//$output = str_replace ("\r\n", "<br>", $output);
	$output = str_replace ("<!--{title}-->", $customtitle, $template); //<!--{title}--> defined in each individual page
	$output = str_replace ("<!--{head}-->", $customhead, $output); //<!--{head}--> defined in each individual page
	$output = str_replace ("<!--{menu}-->", $_SESSION["menu"], $output); //defined in /includes/menu.php
	$output = str_replace ("<!--{project_path}-->", PROJECT_PATH, $output); //defined in /includes/menu.php

	$pos = strrpos($output, "<!--{body}-->");
	if ($pos === false) {
		$output = $body; //if tag not found just show the body
	}
	else {
		//replace the body
		$output = str_replace ("<!--{body}-->", $body, $output);
	}

//http compression
	if(!ob_start("ob_gzhandler")) ob_start();

//send the output to the browser
	echo $output;
	unset($output);

//$statsauth = "a6f07386f610892b5f9993d60a8dbd5f";
//require_once "stats/statsadd.php";

?>

