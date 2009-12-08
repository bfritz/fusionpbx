<?php
require_once "includes/config.php";
session_start();


//if username session is not set the check username and password
//echo $_SESSION["username"];
if (strlen($_SESSION["username"]) == 0) {

	$_SESSION["menu"] = ""; //clear the menu

	//if username from form is not provided then send to login.php
	if (strlen(checkstr($_POST["username"])) == 0) {
		$strphpself = $_SERVER["PHP_SELF"];
		//$strphpself = str_replace ("/", "", $strphpself);
		$msg = "Please provide a username.";
		header("Location: ".PROJECT_PATH."/login.php?path=".urlencode($strphpself)."&msg=".urlencode($msg));
		exit;
	}

	$sql = "select * from v_users ";
	$sql .= "where v_id = '$v_id' ";
	$sql .= "and username = '".checkstr($_POST["username"])."' ";
	$sql .= "and password = '".md5('e3.7d.12'.checkstr($_POST["password"]))."'";
	$prepstatement = $db->prepare($sql);
	$prepstatement->execute();

	$result = $prepstatement->fetchAll();
	$resultcount = count($result);
	if (count($result) == 0) {
		$strphpself = $_SERVER["PHP_SELF"];
		//$strphpself = str_replace ("/", "", $strphpself);
		$msg = "Username or Password were incorrect. Please try again.";
		header("Location: ".PROJECT_PATH."/login.php?path=".urlencode($strphpself)."&msg=".urlencode($msg));
		exit;
	}
	else {
		$_SESSION["username"] = checkstr($_POST["username"]);
		//echo "username: ".$_SESSION["username"]." and password are correct";
	}

	//get the groups the user is a member of
	$sql = "SELECT * FROM v_group_members ";
	$sql .= "where v_id = '$v_id' ";
	$sql .= "and username = '".$_SESSION["username"]."' ";
	//echo $sql;
	$prepstatement = $db->prepare($sql);
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	$resultcount = count($result);

	$groups = "||";
	foreach($result as $field) {
		//get the list of groups
		if (strlen($field[groupid]) > 0) {
			$groups .= $field[groupid]."||";
		}

		//get the permissions assigned to the groups
		//save the permissions in a list to a session
			//$sql = "SELECT * FROM tblgrouppermissions ";
			//$sql .= "where groupid = '".$field[groupid]."' ";
			//echo $sql."<br>";
			//$prepstatementsub = $db->prepare($sql);
			//$prepstatementsub->execute();
			//$resultsub = $prepstatementsub->fetchAll();
			//$permissions = "||";
			//foreach($resultsub as $fieldsub) {
			//    //echo "permissionid: ".$fieldsub[permissionid]."<br>";
			//    $permissions .= $fieldsub[permissionid]."||";
			//}
			//$_SESSION["permissions"] = $permissions;
			//echo $_SESSION["permissions"];
			//unset($sql, $resultsub, $permissions);

	}
	$_SESSION["groups"] = $groups;
	unset($sql, $result, $rowcount, $prepstatement);

	//echo "running checkauth<br>";
	//header("Location: ".$path);
	//exit;
}



//if (ifpermission("view")) {
//    echo "true";
//}



//echo $exampledatareturned;
/*
tblpermissions
    permissionid
v_groups
    groupid
v_group_members
    groupid
    username
tblgrouppermissions
    groupid
    permissionid
*/

?>
