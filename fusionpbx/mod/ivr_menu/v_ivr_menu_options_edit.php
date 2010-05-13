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

//Action add or update
if (isset($_REQUEST["id"])) {
	$action = "update";
	$ivr_menu_option_id = check_str($_REQUEST["id"]);
}
else {
	$action = "add";
}

if (strlen($_GET["ivr_menu_id"]) > 0) {
	$ivr_menu_id = check_str($_GET["ivr_menu_id"]);
}

//POST to PHP variables
if (count($_POST)>0) {
	//$v_id = check_str($_POST["v_id"]);
	$ivr_menu_id = check_str($_POST["ivr_menu_id"]);
	$ivr_menu_options_digits = check_str($_POST["ivr_menu_options_digits"]);
	$ivr_menu_options_action = check_str($_POST["ivr_menu_options_action"]);
	$ivr_menu_options_param = check_str($_POST["ivr_menu_options_param"]);
	$ivr_menu_options_desc = check_str($_POST["ivr_menu_options_desc"]);

	//set the default ivr_menu_options_action
		if (strlen($ivr_menu_options_action) == 0) {
			$ivr_menu_options_action = "menu-exec-app";
		}

	//seperate the action and the param
		$options_array = explode(":", $ivr_menu_options_param);
		$ivr_menu_options_action = $options_array[0];
		$ivr_menu_options_param = $options_array[1];
}

if (count($_POST)>0 && strlen($_POST["persistformvar"]) == 0) {

	$msg = '';

	////recommend moving this to the config.php file
	$uploadtempdir = $_ENV["TEMP"]."\\";
	ini_set('upload_tmp_dir', $uploadtempdir);
	////$imagedir = $_ENV["TEMP"]."\\";
	////$filedir = $_ENV["TEMP"]."\\";

	if ($action == "update") {
		$ivr_menu_option_id = check_str($_POST["ivr_menu_option_id"]);
	}

	//check for all required data
		//if (strlen($v_id) == 0) { $msg .= "Please provide: v_id<br>\n"; }
		//if (strlen($ivr_menu_id) == 0) { $msg .= "Please provide: ivr_menu_id<br>\n"; }
		if (strlen($ivr_menu_options_digits) == 0) { $msg .= "Please provide: Option<br>\n"; }
		//if (strlen($ivr_menu_options_action) == 0) { $msg .= "Please provide: Type<br>\n"; }
		//if (strlen($ivr_menu_options_param) == 0) { $msg .= "Please provide: Destination<br>\n"; }
		//if (strlen($ivr_menu_options_desc) == 0) { $msg .= "Please provide: Description<br>\n"; }
		if (strlen($msg) > 0 && strlen($_POST["persistformvar"]) == 0) {
			require_once "includes/header.php";
			require_once "includes/persistformvar.php";
			echo "<div align='center'>\n";
			echo "<table><tr><td>\n";
			echo $msg."<br />";
			echo "</td></tr></table>\n";
			persistformvar($_POST);
			echo "</div>\n";
			require_once "includes/footer.php";
			return;
		}

	$tmp = "\n";
	$tmp .= "v_id: $v_id\n";
	$tmp .= "ivr_menu_id: $ivr_menu_id\n";
	$tmp .= "Option: $ivr_menu_options_digits\n";
	$tmp .= "Type: $ivr_menu_options_action\n";
	$tmp .= "Destination: $ivr_menu_options_param\n";
	$tmp .= "Description: $ivr_menu_options_desc\n";


//Add or update the database
if ($_POST["persistformvar"] != "true") {
	if ($action == "add") {
		$sql = "insert into v_ivr_menu_options ";
		$sql .= "(";
		$sql .= "v_id, ";
		$sql .= "ivr_menu_id, ";
		$sql .= "ivr_menu_options_digits, ";
		$sql .= "ivr_menu_options_action, ";
		$sql .= "ivr_menu_options_param, ";
		$sql .= "ivr_menu_options_desc ";
		$sql .= ")";
		$sql .= "values ";
		$sql .= "(";
		$sql .= "'$v_id', ";
		$sql .= "'$ivr_menu_id', ";
		$sql .= "'$ivr_menu_options_digits', ";
		$sql .= "'$ivr_menu_options_action', ";
		$sql .= "'$ivr_menu_options_param', ";
		$sql .= "'$ivr_menu_options_desc' ";
		$sql .= ")";
		$db->exec(check_sql($sql));
		//$lastinsertid = $db->lastInsertId($id);
		unset($sql);

		//synchronize the xml config
		sync_package_v_ivr_menu();

		require_once "includes/header.php";
		echo "<meta http-equiv=\"refresh\" content=\"2;url=v_ivr_menu_edit.php?id=$ivr_menu_id\">\n";
		echo "<div align='center'>\n";
		echo "Add Complete\n";
		echo "</div>\n";
		require_once "includes/footer.php";
		return;
	} //if ($action == "add")

	if ($action == "update") {
		$sql = "update v_ivr_menu_options set ";
		$sql .= "v_id = '$v_id', ";
		$sql .= "ivr_menu_id = '$ivr_menu_id', ";
		$sql .= "ivr_menu_options_digits = '$ivr_menu_options_digits', ";
		$sql .= "ivr_menu_options_action = '$ivr_menu_options_action', ";
		$sql .= "ivr_menu_options_param = '$ivr_menu_options_param', ";
		$sql .= "ivr_menu_options_desc = '$ivr_menu_options_desc' ";
		$sql .= "where ivr_menu_option_id = '$ivr_menu_option_id'";
		$db->exec(check_sql($sql));
		unset($sql);

		//synchronize the xml config
		sync_package_v_ivr_menu();

		require_once "includes/header.php";
		echo "<meta http-equiv=\"refresh\" content=\"2;url=v_ivr_menu_edit.php?id=$ivr_menu_id\">\n";
		echo "<div align='center'>\n";
		echo "Update Complete\n";
		echo "</div>\n";
		require_once "includes/footer.php";
		return;
	} //if ($action == "update")
} //if ($_POST["persistformvar"] != "true") { 

} //(count($_POST)>0 && strlen($_POST["persistformvar"]) == 0)

//pre-populate the form
if (count($_GET)>0 && $_POST["persistformvar"] != "true") {
	$ivr_menu_option_id = $_GET["id"];
	$sql = "";
	$sql .= "select * from v_ivr_menu_options ";
	$sql .= "where ivr_menu_option_id = '$ivr_menu_option_id' ";
	$sql .= "and v_id = '$v_id' ";
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	foreach ($result as &$row) {
		$v_id = $row["v_id"];
		$ivr_menu_id = $row["ivr_menu_id"];
		$ivr_menu_options_digits = $row["ivr_menu_options_digits"];
		$ivr_menu_options_action = $row["ivr_menu_options_action"];
		$ivr_menu_options_param = $row["ivr_menu_options_param"];
		$ivr_menu_options_desc = $row["ivr_menu_options_desc"];
		break; //limit to 1 row
	}
	unset ($prepstatement);
}


	require_once "includes/header.php";


	echo "<div align='center'>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing=''>\n";

	echo "<tr class='border'>\n";
	echo "	<td align=\"left\">\n";
	echo "	  <br>";


	echo "<form method='post' name='frm' action=''>\n";

	echo "<div align='center'>\n";
	echo "<table width='100%'  border='0' cellpadding='6' cellspacing='0'>\n";

	echo "<tr>\n";
	if ($action == "add") {
		echo "<td align='left' width='30%' nowrap><b>IVR Menu Option Add</b></td>\n";
	}
	if ($action == "update") {
		echo "<td align='left' width='30%' nowrap><b>IVR Menu Option Edit</b></td>\n";
	}
	echo "<td width='70%' align='right'><input type='button' class='btn' name='' alt='back' onclick=\"window.location='v_ivr_menu_edit.php?id=$ivr_menu_id'\" value='Back'></td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td colspan='2'>\n";
	echo "The recording presents options to the caller. Options match key presses (DTMF digits) from the caller which directs the call to the destinations. <br /><br />\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "	Option:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "  <input class='formfld' type='text' name='ivr_menu_options_digits' maxlength='255' value='$ivr_menu_options_digits'>\n";
	echo "<br />\n";
	echo "Any number 1-5 digits.\n";
	echo "</td>\n";
	echo "</tr>\n";

	/*
	if (ifgroup("superadmin")) {
		echo "<tr>\n";
		echo "<td class='vncell' valign='top' align='left' nowrap>\n";
		echo "	Type:\n";
		echo "</td>\n";
		echo "<td class='vtable' align='left'>\n";

		echo "		<select name='ivr_menu_options_action' class='formfld'>\n";
		echo "		<option></option>\n";
		if (strlen($ivr_menu_options_action) == 0) {
			echo "		<option value='menu-exec-app' selected='selected'>menu-exec-app</option>\n";
		}
		else {
			if ($ivr_menu_options_action == "menu-exec-app") {
				echo "		<option value='menu-exec-app' selected='selected'>menu-exec-app</option>\n";
			}
			else {
				echo "		<option value='menu-exec-app'>menu-exec-app</option>\n";
			}
		}
		if ($ivr_menu_options_action == "menu-sub") {
			echo "		<option value='menu-sub' selected='selected'>menu-sub</option>\n";
		}
		else {
			echo "		<option value='menu-sub'>menu-sub</option>\n";
		}
		if ($ivr_menu_options_action == "menu-exec-app") {
			echo "		<option value='menu-exec-app' selected='selected'>menu-exec-app</option>\n";
		}
		else {
			echo "		<option value='menu-exec-app'>menu-exec-app</option>\n";
		}
		if ($ivr_menu_options_action == "menu-top") {
			echo "		<option value='menu-top' selected='selected'>menu-top</option>\n";
		}
		else {
			echo "		<option value='menu-top'>menu-top</option>\n";
		}
		if ($ivr_menu_options_action == "menu-playback") {
			echo "		<option value='menu-playback' selected='selected'>menu-playback</option>\n";
		}
		else {
			echo "		<option value='menu-playback'>menu-playback</option>\n";
		}
		if ($ivr_menu_options_action == "menu-exit") {
			echo "		<option value='menu-exit' selected='selected'>menu-exit</option>\n";
		}
		else {
			echo "		<option value='menu-exit'>menu-exit</option>\n";
		}
		echo "		</select>\n";

		echo "<br />\n";
		echo "The type is required when a custom destination is defined. \n";
		echo "</td>\n";
		echo "</tr>\n";
	}
	*/

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "	Destination:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";

	if (ifgroup("superadmin")) {
		echo "<script>\n";
		echo "var Objs;\n";
		echo "\n";
		echo "function changeToInput(obj){\n";
		echo "	tb=document.createElement('INPUT');\n";
		echo "	tb.type='text';\n";
		echo "	tb.name=obj.name;\n";
		echo "	tb.setAttribute('class', 'formfld');\n";
		echo "	tb.value=obj.options[obj.selectedIndex].value;\n";
		echo "	tbb=document.createElement('INPUT');\n";
		echo "	tbb.setAttribute('class', 'btn');\n";
		echo "	tbb.type='button';\n";
		echo "	tbb.value='<';\n";
		echo "	tbb.objs=[obj,tb,tbb];\n";
		echo "	tbb.onclick=function(){ Replace(this.objs); }\n";
		echo "	obj.parentNode.insertBefore(tb,obj);\n";
		echo "	obj.parentNode.insertBefore(tbb,obj);\n";
		echo "	obj.parentNode.removeChild(obj);\n";
		echo "}\n";
		echo "\n";
		echo "function Replace(obj){\n";
		echo "	obj[2].parentNode.insertBefore(obj[0],obj[2]);\n";
		echo "	obj[0].parentNode.removeChild(obj[1]);\n";
		echo "	obj[0].parentNode.removeChild(obj[2]);\n";
		echo "}\n";
		echo "</script>\n";
		echo "\n";
	}

	//default selection found to false
		$selection_found = false;

	if (ifgroup("superadmin")) {
		echo "		<select name='ivr_menu_options_param' class='formfld' onchange='changeToInput(this);'>\n";
		if (strlen($ivr_menu_options_param) > 0) {
			echo "		<option value='$ivr_menu_options_param' selected='selected'>".$ivr_menu_options_param."</option>\n";
		}
	}
	else {
		echo "		<select name='ivr_menu_options_param' class='formfld'>\n";
	}

	echo "		<option></option>\n";

	//list extensions
		$sql = "";
		$sql .= "select * from v_extensions ";
		$sql .= "where v_id = '$v_id' ";
		$sql .= "and enabled = 'true' ";
		$prepstatement = $db->prepare(check_sql($sql));
		$prepstatement->execute();
		$result = $prepstatement->fetchAll();
		echo "<optgroup label='Extensions'>\n";
		foreach ($result as &$row) {
			$extension = $row["extension"];
			if ("transfer $extension XML default" == $ivr_menu_options_param) {
				echo "		<option value='menu-exec-app:transfer $extension XML default' selected='selected'>".$extension."</option>\n";
				$selection_found = true;
			}
			else {
				echo "		<option value='menu-exec-app:transfer $extension XML default'>".$extension."</option>\n";
			}
		}
		echo "</optgroup>\n";
		unset ($prepstatement, $extension);

	//list voicemail
		$sql = "";
		$sql .= "select * from v_extensions ";
		$sql .= "where v_id = '$v_id' ";
		$sql .= "and enabled = 'true' ";
		$prepstatement = $db->prepare(check_sql($sql));
		$prepstatement->execute();
		$result = $prepstatement->fetchAll();
		echo "<optgroup label='Voicemail'>\n";
		foreach ($result as &$row) {
			$extension = $row["extension"]; //default ${domain_name} 
			if ("voicemail default \${domain} $extension" == $ivr_menu_options_param) {
				echo "		<option value='menu-exec-app:voicemail default \${domain} $extension' selected='selected'>".$extension."</option>\n";
				$selection_found = true;
			}
			else {
				echo "		<option value='menu-exec-app:voicemail default \${domain} $extension'>".$extension."</option>\n";
			}
		}
		echo "</optgroup>\n";
		unset ($prepstatement, $extension);

	//list hunt groups
		$sql = "";
		$sql .= "select * from v_hunt_group ";
		$sql .= "where v_id = '$v_id' ";
		//$sql .= "and enabled = 'true' ";
		$prepstatement = $db->prepare(check_sql($sql));
		$prepstatement->execute();
		$result = $prepstatement->fetchAll();
		echo "<optgroup label='Hunt Groups'>\n";
		foreach ($result as &$row) {
			//$v_id = $row["v_id"];
			$extension = $row["huntgroupextension"];
			if ("transfer $extension XML default" == $ivr_menu_options_param) {
				echo "		<option value='menu-exec-app:transfer $extension XML default' selected='selected'>".$extension."</option>\n";
				$selection_found = true;
			}
			else {
				echo "		<option value='menu-exec-app:transfer $extension XML default'>".$extension."</option>\n";
			}
		}
		echo "</optgroup>\n";
		unset ($prepstatement, $extension);

	//list ivr menus
		$sql = "";
		$sql .= "select * from v_ivr_menu ";
		$sql .= "where v_id = '$v_id' ";
		$sql .= "and ivr_menu_enabled = 'true' ";
		$prepstatement = $db->prepare(check_sql($sql));
		$prepstatement->execute();
		$result = $prepstatement->fetchAll();
		echo "<optgroup label='IVR Menu'>\n";
		foreach ($result as &$row) {
			$extension = $row["ivr_menu_extension"];
			if ("transfer $extension XML default" == $ivr_menu_options_param) {
				echo "		<option value='menu-exec-app:transfer $extension XML default' selected='selected'>".$extension."</option>\n";
				$selection_found = true;
			}
			else {
				echo "		<option value='menu-exec-app:transfer $extension XML default'>".$extension."</option>\n";
			}
		}
		echo "</optgroup>\n";
		unset ($prepstatement, $extension);

	//list fax extensions
		$sql = "";
		$sql .= "select * from v_fax ";
		$sql .= "where v_id = '$v_id' ";
		$prepstatement = $db->prepare(check_sql($sql));
		$prepstatement->execute();
		$result = $prepstatement->fetchAll();
		echo "<optgroup label='FAX'>\n";
		foreach ($result as &$row) {
			$extension = $row["faxextension"];
			if ("transfer $extension XML default" == $ivr_menu_options_param) {
				echo "		<option value='menu-exec-app:transfer $extension XML default' selected='selected'>".$extension."</option>\n";
				$selection_found = true;
			}
			else {
				echo "		<option value='menu-exec-app:transfer $extension XML default'>".$extension."</option>\n";
			}
		}
		echo "</optgroup>\n";
		unset ($prepstatement, $extension);

	//list ivr sub menus
		$sql = "";
		$sql .= "select * from v_ivr_menu ";
		$sql .= "where v_id = '$v_id' ";
		$sql .= "and ivr_menu_enabled = 'true' ";
		$prepstatement = $db->prepare(check_sql($sql));
		$prepstatement->execute();
		$result = $prepstatement->fetchAll();
		echo "<optgroup label='Sub IVR'>\n";
		foreach ($result as &$row) {
			$extension_name = $row["ivr_menu_name"];
			$extension_name = str_replace(" ", "_", $extension_name);
			if ($extension_name == $ivr_menu_options_param) {
				echo "		<option value='menu-sub:$extension_name' selected='selected'>".$extension_name."</option>\n";
				$selection_found = true;
			}
			else {
				echo "		<option value='menu-sub:$extension_name'>".$extension_name."</option>\n";
			}
		}
		echo "</optgroup>\n";
		unset ($prepstatement, $extension_name);

		echo "<optgroup label='IVR Misc'>\n";
		if ($ivr_menu_options_action == "menu-top") {
			echo "		<option value='menu-top:' selected='selected'>Top</option>\n";
			$selection_found = true;
		}
		else {
			echo "		<option value='menu-top:'>Top</option>\n";
		}
		if ($ivr_menu_options_action == "menu-exit") {
			echo "		<option value='menu-exit:' selected='selected'>Exit</option>\n";
			$selection_found = true;
		}
		else {
			echo "		<option value='menu-exit:'>Exit</option>\n";
		}
		if (strlen($ivr_menu_options_param) > 0) {
			if (!$selection_found) {
				echo "		<option value='$ivr_menu_options_param' selected='selected'>".$ivr_menu_options_param."</option>\n";
			}
		}
		echo "</optgroup>\n";

	echo "		</select>\n";

	echo "<br />\n";
	echo "Select the destination.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "	Description:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='ivr_menu_options_desc' maxlength='255' value=\"$ivr_menu_options_desc\">\n";
	echo "<br />\n";
	echo "Enter a description here for your reference.\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "	<tr>\n";
	echo "		<td colspan='2' align='right'>\n";
	echo "				<input type='hidden' name='ivr_menu_id' value='$ivr_menu_id'>\n";
	if ($action == "update") {
		echo "				<input type='hidden' name='ivr_menu_option_id' value='$ivr_menu_option_id'>\n";
	}
	echo "				<input type='submit' name='submit' class='btn' value='Save'>\n";
	echo "		</td>\n";
	echo "	</tr>";
	echo "</table>";
	echo "</form>";

	echo "	</td>";
	echo "	</tr>";
	echo "</table>";
	echo "</div>";


require_once "includes/footer.php";
?>
