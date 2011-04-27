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

//get the output from the buffer
	$body = $content_from_db.ob_get_contents(); 
	ob_end_clean(); //clean the buffer

//set a default template
	if (strlen($_SESSION["template_name"]) == 0) { $_SESSION["template_name"] = 'default'; }

//set a default template
	//$_SESSION["template_content"] = ''; //force the template to generate on every page load
	if (strlen($_SESSION["template_content"])==0) { //build template if session template has no length
		$v_template_path = $_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/themes';
		if (strlen($template_rsssubcategory) > 0) {
			//this template was assigned by the content manager
				//get the contents of the template and save it to the template variable
				$template_full_path = $v_template_path.'/themes/'.$template_rsssubcategory.'/template.php';
				if (!file_exists($template_full_path)) {
					$_SESSION["template_name"] = 'default';
					$template_full_path = $v_template_path.'/themes/default/template.php';
				}
				$template = file_get_contents($template_full_path);
				$_SESSION["template_content"] = $template;
		}
		else {
			//get the contents of the template and save it to the template variable
				$template_full_path = $v_template_path.'/'.$_SESSION["template_name"].'/template.php';
				if (!file_exists($template_full_path)) {
					$_SESSION["template_name"] = 'default';
					$template_full_path = $v_template_path.'/themes/default/template.php';
				}
				$template = file_get_contents($template_full_path);
				$_SESSION["template_content"] = $template;
		}
	}

//start the output buffer
	ob_start();
	$template = $strheadertop.$_SESSION["template_content"];
	eval('?>' . $template . '<?php ');
	$template = ob_get_contents(); //get the output from the buffer
	ob_end_clean(); //clean the buffer

//get the menu
	require_once "includes/menu.php";

//prepare the template to display the output
	$customhead = $customhead.$templatemenucss;
	//$output = str_replace ("\r\n", "<br>", $output);
	$output = str_replace ("<!--{title}-->", $customtitle, $template); //<!--{title}--> defined in each individual page
	$output = str_replace ("<!--{head}-->", $customhead, $output); //<!--{head}--> defined in each individual page
	if (strlen($v_menu) > 0) {
		$output = str_replace ("<!--{menu}-->", $v_menu, $output); //defined in /includes/menu.php
	}
	else {
		$output = str_replace ("<!--{menu}-->", $_SESSION["menu"], $output); //defined in /includes/menu.php
	}
	$output = str_replace ("<!--{project_path}-->", PROJECT_PATH, $output); //defined in /includes/menu.php

	$pos = strrpos($output, "<!--{body}-->");
	if ($pos === false) {
		$output = $body; //if tag not found just show the body
	}
	else {
		//replace the body
		$output = str_replace ("<!--{body}-->", $body, $output);
	}

//send the output to the browser
	echo $output;
	unset($output);

//$statsauth = "a3az349x2bf3fdfa8dbt7x34fas5X";
//require_once "stats/stat_sadd.php";

?>