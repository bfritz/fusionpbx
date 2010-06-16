<?php
require_once "root.php";
require_once "includes/config.php";
require_once "includes/checkauth.php";
if (ifgroup("superadmin")) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

if (count($_GET)>0) {
	$id = check_str($_GET["id"]);
}

if (strlen($id)>0) {
	$sql = "";
	$sql .= "select * from v_php_service ";
	$sql .= "where php_service_id = '$id' ";
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	foreach ($result as &$row) {
		$service_name = $row["service_name"];
		$tmp_service_name = str_replace(" ", "_", $service_name);
		break; //limit to 1 row
	}
	unset ($prepstatement, $result, $row);

	//delete the php service file
		unlink($v_secure.'/php_service_'.$tmp_service_name.'.php');
	//delete the start up script
		unlink($v_startup_script_dir.'/php_service_'.$tmp_service_name.'.sh');
	//delete the pid file
		unlink($tmp_dir.'/php_service_'.$tmp_service_name.'.pid');

	$sql = "";
	$sql .= "delete from v_php_service ";
	$sql .= "where php_service_id = '$id' ";
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	unset($sql);
}

require_once "includes/header.php";
echo "<meta http-equiv=\"refresh\" content=\"2;url=v_php_service.php\">\n";
echo "<div align='center'>\n";
echo "Delete Complete\n";
echo "</div>\n";

require_once "includes/footer.php";
return;

?>

