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
		$sql .= "delete from v_ivr_menu_options ";
		$sql .= "where v_id = '$v_id' ";
		$sql .= "and ivr_menu_id = '$id' ";
		$db->query($sql);
		unset($sql);

	//delete parent data
		$sql = "";
		$sql .= "delete from v_ivr_menu ";
		$sql .= "where v_id = '$v_id' ";
		$sql .= "and ivr_menu_id = '$id' ";
		$db->query($sql);
		unset($sql);

	//delete the dialplan entries
		$sql = "";
		$sql .= "select * from v_dialplan_includes ";
		$sql .= "where v_id = '$v_id' ";
		$sql .= "and opt1name = 'ivr_menu_id' ";
		$sql .= "and opt1value = '".$id."' ";
		//echo "sql: ".$sql."<br />\n";
		$prepstatement2 = $db->prepare($sql);
		$prepstatement2->execute();
		while($row2 = $prepstatement2->fetch()) {
			$dialplan_include_id = $row2['dialplan_include_id'];
			//echo "dialplan_include_id: ".$dialplan_include_id."<br />\n";
			break; //limit to 1 row
		}
		unset ($sql, $prepstatement2);

		//delete the child dialplan information
			$sql = "";
			$sql = "delete from v_dialplan_includes_details ";
			$sql .= "where v_id = '$v_id' ";
			$sql .= "and dialplan_include_id = '$dialplan_include_id' ";
			//echo "sql: ".$sql."<br />\n";
			$db->query($sql);
			unset($sql);

		//delete the parent dialplan information
			$sql = "";
			$sql .= "delete from v_dialplan_includes ";
			$sql .= "where v_id = '$v_id' ";
			$sql .= "and opt1name = 'ivr_menu_id' ";
			$sql .= "and opt1value = '".$id."' ";
			//echo "sql: ".$sql."<br />\n";
			$db->query($sql);
			unset ($sql);

	//synchronize the xml config
		sync_package_v_ivr_menu();

	//synchronize the xml config
		sync_package_v_dialplan_includes();
}

require_once "includes/header.php";
echo "<meta http-equiv=\"refresh\" content=\"2;url=v_ivr_menu.php\">\n";
echo "<div align='center'>\n";
echo "Delete Complete\n";
echo "</div>\n";

require_once "includes/footer.php";
return;

?>

