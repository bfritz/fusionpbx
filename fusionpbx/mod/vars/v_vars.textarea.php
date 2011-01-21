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
if (ifgroup("superadmin")) {
	//access granted
}
else {
	echo "access denied";
	exit;
}
require_once "includes/header.php";

if ($_GET['a'] == "default") {
	//conf_mount_rw();
	//exec("cp ".$v_conf_dir.".orig/vars.xml ".$v_conf_dir."/vars.xml");
	
	//read default config file
	$fd = fopen($v_conf_dir.".orig/vars.xml", "r");
	$v_content = fread($fd, filesize($v_conf_dir.".orig/vars.xml"));
	//echo $v_content;
	fclose($fd);
	
	//write the default config fget
	$fd = fopen($v_conf_dir."/vars.xml", "w");
	fwrite($fd, $v_content);
	fclose($fd);
	
	$savemsg = "Default Restored";
	//conf_mount_ro();
}

if ($_POST['a'] == "save") {
	//conf_mount_rw();
	$v_content = str_replace("\r","",$_POST['code']);
	$fd = fopen($v_conf_dir."/vars.xml", "w");
	fwrite($fd, $v_content);
	fclose($fd);
	$savemsg = "Saved";
	//conf_mount_ro();
}


$fd = fopen($v_conf_dir."/vars.xml", "r");
$v_content = fread($fd, filesize($v_conf_dir."/vars.xml"));
fclose($fd);

	//--- Begin: Edit Area -----------------------------------------------------

		echo "	<script language=\"javascript\" type=\"text/javascript\" src=\"/edit_area/edit_area_full.js\"></script>\n";
		echo "	<script language=\"Javascript\" type=\"text/javascript\">\n";
		echo "		// initialisation //load,\n";
		echo "		editAreaLoader.init({\n";
		echo "			id: \"code\"	// id of the textarea to transform //, |, help\n";
		echo "			,start_highlight: true\n";
		echo "			,font_size: \"8\"\n";
		echo "			,allow_toggle: false\n";
		echo "			,language: \"en\"\n";
		echo "			,syntax: \"html\"\n";
		echo "			,toolbar: \"search, go_to_line,|, fullscreen, |, undo, redo, |, select_font, |, syntax_selection, |, change_smooth_selection, highlight, reset_highlight, |, help\" //new_document,\n";
		echo "			,plugins: \"charmap\"\n";
		echo "			,charmap_default: \"arrows\"\n";
		echo "		});\n";
		echo "	</script>";
		echo "\n";
		echo "\n";

	//--- End: Edit Area -----------------------------------------------------
?>

<div align='center'>

<table width="90%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td>

			<form action="v_vars.php" method="post" name="iform" id="iform">

			<table width="100%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td width='100%'><span class="vexpl"><span class="red"><strong>Variables<br>
					</strong></span>
					Define preprocessor variables here. Can be accessed in the xml configation with $${var_name}.
					<br />
					<br />
				</td>
				<td width='10%' align='right' valign='top'><input type="submit" class='btn' value="save" /></td>
			</tr>

			<tr>
			<td colspan='2' class='' align='left'>
				<textarea name='code' style='width:100%' id='code' rows='35' wrap='off'><?php echo htmlentities($v_content); ?></textarea>
				<br />
				<br />
			</td>
			</tr>

			<tr>
				<td valign='top'>
				<?php
				if ($v_path_show) {
					echo "<b>location:</b> ".$v_conf_dir."/vars.xml\n";
				}
				?>
				</td>
				<td valign='top' align='right'>
					<input type="hidden" name="f" value="<?php echo $_GET['f']; ?>" />
					<input type="hidden" name="a" value="save" />
					<?php
					echo "<input type='button' class='btn' value='Restore Default' onclick=\"document.location.href='v_vars.php?a=default&f=vars.xml';\" />";
					?>
				</td>
			</tr>

			<tr>
			<td colspan='2'>
				<br /><br /><br />
				<br /><br /><br />
				<br /><br /><br />
				<br /><br /><br />
				<br /><br /><br />
				<br /><br /><br />
				<br /><br /><br />
				<br /><br /><br />
				<br /><br /><br />
				<br /><br /><br />
			</td>
			</tr>

			</table>
			</form>

		</td>
	</tr>
</table>

</div>




<?php
	require_once "includes/footer.php";
?>