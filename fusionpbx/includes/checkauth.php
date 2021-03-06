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
require_once "includes/config.php";
session_start();

/* set the salt for password hash generation
 * changing this string can cause existing users to no longer be able to log in,
 * unless you regenerate their passwords in the v_users table
 */
$salt   = 'e3.7d.12';


//if username session is not set the check username and password
//echo $_SESSION["username"];
if (strlen($_SESSION["username"]) == 0) {

	$_SESSION["menu"] = ""; //clear the menu

	//if username from form is not provided then send to login.php
		if (strlen(check_str($_POST["username"])) == 0) {
			$strphpself = $_SERVER["PHP_SELF"];
			//$strphpself = str_replace ("/", "", $strphpself);
			$msg = "Please provide a username.";
			header("Location: ".PROJECT_PATH."/login.php?path=".urlencode($strphpself)."&msg=".urlencode($msg));
			exit;
		}

	//check the username and password if they don't match then redirect back to login
		$sql = "select * from v_users ";
		$sql .= "where v_id=:v_id ";
		$sql .= "and username=:username ";
		$sql .= "and password=:password ";
		$prepstatement = $db->prepare(check_sql($sql));
		$prepstatement->bindParam(':v_id', $v_id);
		$prepstatement->bindParam(':username', check_str($_POST["username"]));
		$prepstatement->bindParam(':password', md5($salt.check_str($_POST["password"])));
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
			$_SESSION["username"] = check_str($_POST["username"]);
			//echo "username: ".$_SESSION["username"]." and password are correct";
		}

	//get the groups the user is a member of
		$sql = "SELECT * FROM v_group_members ";
		$sql .= "where v_id=:v_id ";
		$sql .= "and username=:username ";
		$prepstatement = $db->prepare(check_sql($sql));
		$prepstatement->bindParam(':v_id', $v_id);
		$prepstatement->bindParam(':username', $_SESSION["username"]);
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

//hide the path unless logged in as a superadmin.
if (!ifgroup("superadmin")) {
	$v_path_show = false;
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
