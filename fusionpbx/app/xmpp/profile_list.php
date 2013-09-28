<div align='center'>

<table width="100%" border="0" cellpadding="6" cellspacing="0">
  <tr>
	<td align='left'><b><?php echo $text['header-xmpp']?></b><br>
		<?php echo $text['description-xmpp-list']?>
	</td>
  </tr>
</table>
<br />

<table width='100%' border='0' cellpadding='0' cellspacing='0'>
<tr>
	<th><?php echo $text['label-profile_name']?></th>
	<th><?php echo $text['label-context']?></th>
	<th><?php echo $text['label-status']?></th>
	<th><?php echo $text['label-enabled']?></th>
	<th><?php echo $text['label-description']?></th>
<td align='right' width='42'>
	<?php if (permission_exists('xmpp_add')) { ?>
		<a href='xmpp_profile_edit.php' alt='<?php echo $text['button-add']?>'><?php echo $v_link_label_add; ?></a>
	<?php } ?>
</td>
</tr>
<?php

$c = 0;
$row_style["0"] = "row_style0";
$row_style["1"] = "row_style1";

foreach($profiles_array as $profile){
?>
<tr>
	<td class='<?php echo $row_style[$c]; ?>'><?php echo $profile['profile_name']; ?>&nbsp;</td>
	<td class='<?php echo $row_style[$c]; ?>'><?php echo $profile['context']; ?>&nbsp;</td>
	<td class='<?php echo $row_style[$c]; ?>'><?php echo $profile['status']; ?>&nbsp;</td>
	<td class='<?php echo $row_style[$c]; ?>'><?php echo $profile['enabled']; ?>&nbsp;</td>
	<td class='<?php echo $row_style[$c]; ?>'><?php echo $profile['description']; ?>&nbsp;</td>
	<td align='right' width='42'>
		<?php if (permission_exists('xmpp_edit')) { ?>
		<a href='xmpp_profile_edit.php?id=<?php echo $profile['xmpp_profile_uuid']; ?>' alt='<?php echo $text['button-edit']?>'><?php echo $v_link_label_edit; ?></a>
		<?php } ?>
		<?php if (permission_exists('xmpp_delete')) { ?>
		<a href='profile_delete.php?id=<?php echo $profile['xmpp_profile_uuid']; ?>' onclick="return confirm('<?php echo $text['confirm-delete']?>')"
			alt='<?php echo $text['button-delete']?>'><?php echo $v_link_label_delete; ?></a>
		<?php } ?>
	</td>
</tr>
<?php
if ($c==0) { $c=1; } else { $c=0; }
}
?>
<tr>
<td colspan='6' align='right' width='42'>
	<?php if (permission_exists('xmpp_add')) { ?>
		<a href='xmpp_profile_edit.php' alt='<?php echo $text['button-add']?>'><?php echo $v_link_label_add; ?></a>
	<?php } ?>
</td>
</tr>
</table>
</div>
