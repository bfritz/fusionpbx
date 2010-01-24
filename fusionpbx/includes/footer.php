<?php

// get the content
	if (strlen($content) == 0) {
		$content = $_GET["c"]; //link
	}

	//echo "content: ".$content;


//get the parent id
	$sql = "";
	$sql .= "select * from v_menu ";
	$sql .= "where menustr = '".$_SERVER["SCRIPT_NAME"]."' ";
	$prepstatement = $db->prepare($sql);
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
	$prepstatement = $db->prepare($sql);
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
	$prepstatement = $db->prepare($sql);
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

echo $output;
unset($output);

//$statsauth = "a6f07386f610892b5f9993d60a8dbd5f";
//require_once "stats/statsadd.php";

?>

