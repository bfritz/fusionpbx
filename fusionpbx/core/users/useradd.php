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
require_once "includes/checkauth.php";

if (ifgroup("admin") || ifgroup("superadmin")) {
	//access allowed
}
else {
	echo "access denied";
	return;
}

$path = check_str($_GET["path"]);
$msg = check_str($_GET["msg"]);

$username = check_str($_POST["username"]);
$password = check_str($_POST["password"]);
$groupid = check_str($_POST["groupid"]);
//$passwordquestion = check_str($_POST["passwordquestion"]);
//$passwordanswer = check_str($_POST["passwordanswer"]);


if (strlen($username) > 0) {

	$sqlinsert = "insert into v_users ";
	$sqlinsert .= "(";	
	$sqlinsert .= "v_id, ";
	$sqlinsert .= "username, ";
	$sqlinsert .= "password, ";
	$sqlinsert .= "usertype, ";
	$sqlinsert .= "usercategory ";
	//$sqlinsert .= "passwordquestion, ";
	//$sqlinsert .= "passwordanswer ";
	$sqlinsert .= ")";
	$sqlinsert .= "values ";
	$sqlinsert .= "(";
	$sqlinsert .= "'$v_id', ";
	$sqlinsert .= "'$username', ";
	$sqlinsert .= "'".md5('e3.7d.12'.$password)."', ";
	$sql .= "'$usertype', ";
	$sql .= "'$usercategory' ";
	//$sqlinsert .= "'$passwordquestion', ";
	//$sqlinsert .= "'$passwordanswer' ";
	$sqlinsert .= ")";
	//echo $sqlinsert;
	if (!$db->exec($sqlinsert)) {
		//echo $db->errorCode() . "<br>";
		$info = $db->errorInfo();
	  print_r($info);
		// $info[0] == $db->errorCode() unified error code
		// $info[1] is the driver specific error code
		// $info[2] is the driver specific error string
	}

	//add the user to the group
	$sqlinsert = "insert into v_group_members ";
	$sqlinsert .= "(";
	$sqlinsert .= "v_id, ";
	$sqlinsert .= "groupid, ";
	$sqlinsert .= "username ";
	$sqlinsert .= ")";
	$sqlinsert .= "values ";
	$sqlinsert .= "(";
	$sqlinsert .= "'$v_id', ";
	$sqlinsert .= "'$groupid', ";
	$sqlinsert .= "'$username' ";
	$sqlinsert .= ")";
	//echo $sqlinsert;
	if (!$db->exec($sqlinsert)) {
		//echo $db->errorCode() . "<br>";
		$info = $db->errorInfo();
		print_r($info);
		// $info[0] == $db->errorCode() unified error code
		// $info[1] is the driver specific error code
		// $info[2] is the driver specific error string
	}

	//get the groups the user is a member of
	$sql = "SELECT * FROM v_group_members ";
	$sql .= "where v_id = '".$v_id."' ";
	$sql .= "and username = '".$_SESSION["username"]."' ";
	//echo $sql;
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	$resultcount = count($result);

	foreach($result as $field) {

		//get the permissions assigned to the groups
		//save the permissions in a list to a session
			$sql = "SELECT * FROM tblgrouppermissions ";
			$sql .= "where v_id = '".$v_id."' ";
			$sql .= "and groupid = '".$field[groupid]."' ";
			//echo $sql."<br>";
			$prepstatementsub = $db->prepare($sql);
			$prepstatementsub->execute();
			$resultsub = $prepstatementsub->fetchAll();
			$permissions = "||";
			foreach($resultsub as $fieldsub) {
				//echo "permissionid: ".$fieldsub[permissionid]."<br>";
				$permissions .= $fieldsub[permissionid]."||";
			}
			//echo $_SESSION["permissions"];
			unset($sql, $resultsub, $permissions);

	}
	unset($sql, $result, $rowcount);
	if (strlen($path) == 0) {
		header("Location: index.php");
	}
	else {
		header("Location: $path");
	}
	exit;
}

include "includes/header.php";
echo "<br><br>";
echo "<div align='center'>";

echo "<table width='325'>\n";
echo "<tr>\n";
echo "<td>\n";
echo "Please choose a username and password. This user account ";
echo "is used to protect access to this application. ";
echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "<br>";

echo "<form name='login' METHOD=\"POST\" action=\"useradd.php\">\n";

echo "<table>\n";
echo "<tr>\n";
echo "<td>\n";
echo "UserName:\n";
echo "</td>\n";
echo "<td>\n";
echo "  <input type=\"text\" class='frm' name=\"username\">\n";
echo "</td>\n";
echo "</tr>\n";
echo "\n";
echo "<tr>\n";
echo "<td>\n";
echo "Password:\n";
echo "</td>\n";
echo "\n";
echo "<td>\n";
echo "<input type=\"password\" class='frm' name=\"password\">\n";
echo "</td>\n";
echo "</tr>\n";

echo "<tr>\n";
echo "<td>\n";
echo "Assign to Group:\n";
echo "</td>\n";
echo "\n";
echo "<td>\n";

//---- Begin Select List --------------------
$sql = "SELECT * FROM v_groups ";
$sql .= "where v_id = '".$v_id."' ";
$prepstatement = $db->prepare(check_sql($sql));
$prepstatement->execute();
echo "<select name=\"groupid\" class='frm'>\n";
echo "<option value=\"\"></option>\n";
$result = $prepstatement->fetchAll();
//$catcount = count($result);
foreach($result as $field) {
    echo "<option value='".$field[groupid]."'>".$field[groupid]."</option>\n";
}

echo "</select>";
unset($sql, $result);
//---- End Select List --------------------


echo "</td>\n";
echo "</tr>\n";



/*
echo "<tr>\n";
echo "<td colspan='2'>\n";
echo "<br>";
echo "Password Hint\n";
echo "</td>\n";
echo "</tr>\n";

echo "<tr>\n";
echo "<td>\n";
echo "Question:\n";
echo "</td>\n";
echo "<td>\n";
echo "  <input type=\"text\" class='frm' name=\"passwordquestion\">\n";
echo "</td>\n";
echo "</tr>\n";

echo "<tr>\n";
echo "<td>\n";
echo "Answer:\n";
echo "</td>\n";
echo "<td>\n";
echo "  <input type=\"text\" class='frm' name=\"passwordanswer\">\n";
echo "</td>\n";
echo "</tr>\n";
*/

echo "<tr>\n";
echo "<td>\n";
echo "</td>\n";
echo "<td align=\"right\">\n";
echo "  <input type=\"hidden\" name=\"path\" value=\"$path\">\n";
echo "  <input type=\"submit\" class='btn' value=\"Create Account\">\n";
echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "</form>";

echo "</div>";


echo "<br><br>";
echo "<br><br>";


include "includes/footer.php";
?>
