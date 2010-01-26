<?php
include "root.php";
require_once "includes/config.php";


$path = check_str($_GET["path"]);
$msg = check_str($_GET["msg"]);

$sql = "SELECT * FROM v_users ";
$prepstatement = $db->prepare(check_sql($sql));
$prepstatement->execute();

$result = $prepstatement->fetchAll();
if (count($result) > 0) { //if user account exists then show login
	include "includes/header.php";
	echo "<br><br>";
	echo "<div align='center'>";
	if (strlen($msg) > 0) {
		echo "<div align='center'>\n";
		echo "<table width='50%'>\n";
		echo "<tr>\n";
		echo "<th align='left'>Message</th>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "<td class='rowstyle1'><strong>$msg</strong></td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		echo "</div>\n";
		echo "<br /><br />\n\n";
	}

	if (strlen($path) == 0) {
		echo "<form name='login' METHOD=\"POST\" action=\"".PROJECT_PATH."/index.php\">\n";
	}
	else {
		echo "<form name='login' METHOD=\"POST\" action=\"$path\">\n";
	}

	echo "<table width='200'>\n";
	echo "<tr>\n";
	echo "<td align='left'>\n";
	echo "	UserName:\n";
	echo "</td>\n";
	echo "<td>\n";
	echo "  <input type=\"text\" class='frm' name=\"username\">\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "\n";
	echo "<tr>\n";
	echo "<td align='left'>\n";
	echo "	Password:\n";
	echo "</td>\n";
	echo "\n";
	echo "<td align='left'>\n";
	echo "	<input type=\"password\" class='frm' name=\"password\">\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "\n";
	echo "<tr>\n";
	echo "<td>\n";
	echo "</td>\n";
	echo "<td align=\"right\">\n";
	//echo "  <input type=\"hidden\" name=\"path\" value=\"$path\">\n";
	echo "  <input type=\"submit\" class='btn' value=\"Login\">\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</form>";

	//if (strlen($msg) == 0) {
	//    echo "<br><a href='loginpasswordchange.php'>Change Password</a>";
	//}
	//else {
	//    echo "<br><a href='loginpasswordforgot.php'>Forgot Password</a>";
	//}

	echo "</div>";

}
echo "<br><br>";
echo "<br><br>";


include "includes/footer.php";
?>
