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


if ($_GET['a'] == "default") {
	//conf_mount_rw();
	//exec("cp ".$v_conf_dir.".orig/sip_profiles/".$_GET['f']." ".$v_conf_dir."/sip_profiles/".$_GET['f']);
	
	$fd = fopen($v_conf_dir.".orig/sip_profiles/".$_GET['f'], "r");
	$v_content = fread($fd, filesize($v_conf_dir.".orig/sip_profiles/".$_GET['f']));
	//echo $v_content;
	fclose($fd);
	
	//write the default config fget
	$fd = fopen($v_conf_dir."/sip_profiles/".$_GET['f'], "w");
	fwrite($fd, $v_content);
	fclose($fd);
	
	$savemsg = "Restore Default";
	//conf_mount_ro();
}

if ($_POST['a'] == "save") {
	//conf_mount_rw();
	$v_content = ereg_replace("\r","",$_POST['code']);
	$fd = fopen($v_conf_dir."/sip_profiles/".$_POST['f'], "w");
	fwrite($fd, $v_content);
	fclose($fd);
	$savemsg = "Saved";
	//conf_mount_ro();
}

	
if ($_GET['a'] == "del") {
    if ($_GET['type'] == 'profile') {
        //if ($a_profiles[$_GET['id']]) {
            //unset($a_extensions[$_GET['id']]);
            //write_config();

            unlink($v_conf_dir."/sip_profiles/".$_GET['f']);			
            header("Location: v_profiles.php");
            exit;
        //}
    }
}

require_once "includes/header.php";

?>




<?php
//include("fbegin.inc");
//if ($v_label_show) {
//	echo "<p class=\"pgtitle\">$v_label: Profiles</p>\n";
//}

$c = 0;
$rowstyle["0"] = "rowstyle0";
$rowstyle["1"] = "rowstyle1";
?>

<div align='center'>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
	 <td class="tabcont" align='left'>

<form action="v_profiles.php" method="post" name="iform" id="iform">
	<table width="100%" border="0" cellpadding="6" cellspacing="0">
		<tr>
		<td align='left'><p><span class="vexpl"><span class="red"><strong>Profiles<br>
			</strong></span>
			Use this to configure your SIP profiles.
			</p></td>
		</tr>
	</table>
	<br />

	<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<!--<tr><td colspan='2'><img src='/images/spacer.gif' width='100%' height='1' style='background-color: #BBBBBB;'></td></tr>-->
	<tr>
	  <th width="25%" class="">Name</th>
	  <th width="70%" class="">Description</th>
	  <td width="5%" class="list"></td>
	  </th>
	</tr>
	<!--<tr><td colspan='2'><img src='/images/spacer.gif' width='100%' height='1' style='background-color: #BBBBBB;'></td></tr>-->

	<?php
	foreach (ListFiles($v_conf_dir.'/sip_profiles') as $key=>$file){
		if (substr($file, -4) == ".xml") {
			echo "<tr>\n";
			echo "	<td class='".$rowstyle[$c]."' ondblclick=\"document.location='v_profile_edit.php?f=".$file."'\";\" valign='middle'>\n";
			echo $file;
			echo "&nbsp;\n";
			echo "	</td>\n";
			echo "	<td class='rowstylebg' ondblclick=\"document.location='v_profile_edit.php?f=".$file."\">\n";
	
			switch ($file) {
			case "internal.xml":
				echo "The Internal profile by default requires registration which is most often used for extensions. ";
				echo "By default the Internal profile binds to the WAN IP which is accessible to the internal network. ";
				echo "A rule can be set from PFSense -> Firewall -> Rules -> WAN to the the WAN IP for port 5060 which ";
				echo "enables phones register from outside the network.";
				echo "";
				echo "&nbsp;";
				break; 
			case "internal-ipv6.xml":
				echo "The Internal IPV6 profile binds to the IP version 6 address and is similar to the Internal profile.\n";
				echo "&nbsp;";
				break;
			case "external.xml":
				echo "The External profile handles outbound registrations to a SIP provider or other SIP Server. The SIP provider sends calls to you, and you ";
				echo "send calls to your provider, through the external profile. The external profile allows anonymous calling, which is ";
				echo "required as your provider will never authenticate with you to send you a call. Calls can be sent using a SIP URL \"my.domain.com:5080\" ";
				echo "&nbsp;";
				break;
			case "lan.xml":
				echo "The LAN profile is the same as the Internal profile except that it is bound to the LAN IP.\n";
				echo "&nbsp;";
				break;
			default:
				//echo "<font color='#FFFFFF'>default</font>&nbsp;";
			}
	
			echo "	</td>\n";
			echo "	<td valign='middle' nowrap class='list' valign='top'>\n";
			echo "	  <table border='0' cellspacing='2' cellpadding='1'>\n";
			echo "		<tr>\n";
			echo "		  <td valign='middle'><a href='v_profile_edit.php?type=profile&f=".$file."' alt='edit'>$v_link_label_edit</a></td>\n";
			echo "		  <td><a href='v_profiles.php?type=profile&a=del&f=".$file."'  alt='delete' onclick=\"return confirm('Do you really want to delete this?')\">$v_link_label_delete</a></td>\n";
			echo "		</tr>\n";
			echo "	 </table>\n";
			echo "	</td>\n";
			echo "</tr>\n";
			//echo "<tr><td colspan='2'><img src='/images/spacer.gif' width='100%' height='1' style='background-color: #BBBBBB;'></td></tr>\n";
	
			if ($c==0) { $c=1; } else { $c=0; }
			$i++;
		}
	}

	?>
	</table>
</form>


<?php
if ($v_path_show) {
	echo "<br />\n";
	echo $v_conf_dir."/sip_profiles\n";
}
?>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>

</td>
</tr>
</table>

</div>



<?php 
require_once "includes/footer.php";
?>