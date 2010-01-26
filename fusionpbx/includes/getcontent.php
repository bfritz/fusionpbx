<?php
include "root.php";
require_once "includes/config.php";
session_start();

	//require_once "includes/header.php";
	$content = $_GET["c"]; //link

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

	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	$resultcount = count($result);

	foreach($result as $row) {
		echo urldecode($row[rssdesc]);
	} //end foreach
	unset($sql, $result, $rowcount);


//require_once "includes/footer.php";

?>
