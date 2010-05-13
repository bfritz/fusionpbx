<?php
require_once "root.php";
require_once "includes/config.php";
require_once "includes/checkauth.php";
if (ifgroup("admin") || ifgroup("superadmin")) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

if (count($_GET)>0) {
	$id = check_str($_GET["id"]);
	$ivr_menu_id = check_str($_GET["ivr_menu_id"]);
}

if (strlen($id)>0) {
	$sql = "";
	$sql .= "delete from v_ivr_menu_options ";
	$sql .= "where ivr_menu_option_id = '$id' ";
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	unset($sql);

	//synchronize the xml config
	sync_package_v_ivr_menu();
}

require_once "includes/header.php";
echo "<meta http-equiv=\"refresh\" content=\"2;url=v_ivr_menu_edit.php?id=$ivr_menu_id\">\n";
echo "<div align='center'>\n";
echo "Delete Complete\n";
echo "</div>\n";

require_once "includes/footer.php";
return;

?>

