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
	//access granted
}
else {
	echo "access denied";
	exit;
}
require_once "includes/header.php";

if ($_GET['a'] == "default") {
	//conf_mount_rw();
	//exec("cp ".$v_conf_dir.".orig/dialplan/default.xml ".$v_conf_dir."/dialplan/default.xml");

	//read default config file
	$fd = fopen($v_conf_dir.".orig/dialplan/default.xml", "r");
	$v_content = fread($fd, filesize($v_conf_dir.".orig/dialplan/default.xml"));
	//echo $v_content;
	fclose($fd);

	//write the default config fget
	$fd = fopen($v_conf_dir."/dialplan/default.xml", "w");
	fwrite($fd, $v_content);
	fclose($fd);

	$savemsg = "Default Restored";
	//conf_mount_ro();
}

if ($_POST['a'] == "save") {
	//conf_mount_rw();
	$v_content = str_replace("\r","",$_POST['code']);
	$fd = fopen($v_conf_dir."/dialplan/default.xml", "w");
	fwrite($fd, $v_content);
	fclose($fd);
	$savemsg = "Saved";
	//conf_mount_ro();
}


$fd = fopen($v_conf_dir."/dialplan/default.xml", "r");
$v_content = fread($fd, filesize($v_conf_dir."/dialplan/default.xml"));
fclose($fd);

?>



<script language="Javascript">
function sf() { document.forms[0].savetopath.focus(); }
</script>
<script language="Javascript" type="text/javascript" src="<?php echo PROJECT_PATH; ?>/includes/edit_area/edit_area_full.js"></script>
<script language="Javascript" type="text/javascript">
	// initialisation
	editAreaLoader.init({
		id: "code"	// id of the textarea to transform
		,start_highlight: true
		,allow_toggle: false
		,language: "en"
		,syntax: "html"	
		,toolbar: "search, go_to_line,|, fullscreen, |, undo, redo, |, select_font, |, syntax_selection, |, change_smooth_selection, highlight, reset_highlight, |, help"
		,syntax_selection_allow: "css,html,js,php,xml,c,cpp,sql"
		,show_line_colors: true
	});
</script>

<div align='center'>


<table width="100%" border="0" cellpadding="0" cellspacing="0">
   <tr>
     <td class="" >

<form action="v_dialplan.php" method="post" name="iform" id="iform">
<?php

?>
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
	  <tr>
		<td align='left' width='100%'><span class="vexpl"><span class="red"><strong>Default Dialplan<br>
			</strong></span>
			The default dialplan is used to setup call destinations based on conditions and context. 
			You can use the dialplan to send calls to gateways, auto attendants, external numbers, to scripts, or any destination.
			<br />
			<br />
		</td>
		<td width='10%' align='right' valign='top'><input type='submit' class='btn' value='save' /></td>
      </tr>

	<tr>
	<td colspan='2' class='' valign='top' align='left' nowrap>
		<textarea style="width:100%" id="code" name="code" rows="31"><?php echo htmlentities($v_content); ?></textarea>
		<br />
		<br />
	</td>
	</tr>

	<tr>
		<td align='left'>
		<?php
		if ($v_path_show) {
			echo "<b>location:</b> ".$v_conf_dir."/dialplan/default.xml\n";
		}
		?>
		</td>
		<td align='right'>
			<input type='hidden' name='f' value='<?php echo $_GET['f']; ?>' />
			<input type='hidden' name='a' value='save' />
			<?php
			echo "<input type='button' class='btn' value='Restore Default' onclick=\"document.location.href='v_dialplan.php?a=default&f=default.xml';\" />";
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

