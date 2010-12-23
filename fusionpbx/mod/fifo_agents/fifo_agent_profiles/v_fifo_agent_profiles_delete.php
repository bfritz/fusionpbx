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
}

if (strlen($id)>0) {

	//delete child data
		$sql = "";
		$sql .= "delete from v_fifo_agent_profile_members ";
		$sql .= "where v_id = '$v_id' ";
		$sql .= "and fifo_agent_profile_id = '$id' ";
		$prepstatement = $db->prepare(check_sql($sql));
		$prepstatement->execute();
		unset($sql, $prepstatement);

	//delete parent data
		$sql = "";
		$sql .= "delete from v_fifo_agent_profiles ";
		$sql .= "where v_id = '$v_id' ";
		$sql .= "and fifo_agent_profile_id = '$id' ";
		$prepstatement = $db->prepare(check_sql($sql));
		$prepstatement->execute();
		unset($sql, $prepstatement);
}

require_once "includes/header.php";
echo "<meta http-equiv=\"refresh\" content=\"2;url=v_fifo_agent_profiles.php\">\n";
echo "<div align='center'>\n";
echo "Delete Complete\n";
echo "</div>\n";

require_once "includes/footer.php";
return;

?>

