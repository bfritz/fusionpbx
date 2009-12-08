<?php
/* $Id$ */
/*
	v_profile_edit.php
	Copyright (C) 2008 Mark J Crane
	All rights reserved.

	Redistribution and use in source and binary forms, with or without
	modification, are permitted provided that the following conditions are met:

	1. Redistributions of source code must retain the above copyright notice,
	   this list of conditions and the following disclaimer.

	2. Redistributions in binary form must reproduce the above copyright
	   notice, this list of conditions and the following disclaimer in the
	   documentation and/or other materials provided with the distribution.

	THIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES,
	INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY
	AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
	AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
	OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
	SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
	INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
	CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
	ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
	POSSIBILITY OF SUCH DAMAGE.
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

$fd = fopen($v_conf_dir."/sip_profiles/".$_GET['f'], "r");
$v_content = fread($fd, filesize($v_conf_dir."/sip_profiles/".$_GET['f']));
fclose($fd);

require_once "includes/header.php";

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
	 <td class="tabcont" >

<form action="v_profiles.php" method="post" name="iform" id="iform">

	<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td align='left'><p><span class="vexpl"><span class="red"><strong>Edit Profile<br>
			</strong></span>
			Use this to configure your SIP profiles.
			<br />
			<br />
		</td>
		<td align='right' valign='top'>Filename: <input type="text" name="f" value="<?php echo $_GET['f']; ?>" /><input type="submit" class='btn' value="save" /></td>
	</tr>


	<tr>
	<td colspan='2' class='' valign='top' align='left' nowrap>
		<textarea style="width:100%;" id="code" name="code" rows="35" class='txt'><?php echo htmlentities($v_content); ?></textarea>
	<br />
	<br />
	</td>
	</tr>


	<tr>
		<td align='left'>
		<?php
		if ($v_path_show) {
			echo "<b>location:</b> ".$v_conf_dir."/sip_profiles/".$_GET['f']."</td>";
		}
		?>
		<td align='right'>
			<input type="hidden" name="a" value="save" />
			<?php
			echo "<input type='button' class='btn' value='Restore Default' onclick=\"document.location.href='v_profiles.php?a=default&f=".$_GET['f']."';\" />";
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

