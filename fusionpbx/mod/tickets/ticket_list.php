<div align='center'>

<table width="100%" border="0" cellpadding="6" cellspacing="0">
  <tr>
	<td align='left'><b>Active Tickets</b><br>
		Open Tickets
	</td>
  </tr>
</table>
<br />

<table width='100%' border='0' cellpadding='0' cellspacing='0'>
<tr>
	<th>Ticket Number</th>
	<th>Queue</th>
	<th>Status</th>
	<th>Last Update</th>
	<th>Subject</th>
<td align='right' width='42'>
	<?php if (permission_exists('ticket_add')) { ?>
		<a href='v_ticket_create.php' alt='Create Ticket'><?php echo $v_link_label_add; ?></a>
	<?php } ?>
</td>
</tr>
<?php
foreach($tickets as $ticket){
?>
<tr>
	<td><?php echo $ticket['ticket_number']; ?></td>
	<td><?php echo $queues[$ticket['queue_id']]; ?></td>
        <td><?php echo $statuses[$ticket['ticket_status']]; ?></td>
	<td><?php echo $ticket['last_update_stamp']; ?></td>
	<td><?php echo $ticket['subject']; ?></td>
<td align='right' width='42'>
	<?php if (permission_exists('ticket_update')) { ?>
	<a href='v_ticket_update.php?id=<?php echo $ticket['ticket_id']; ?>' alt='Update Ticket'><?php echo $v_link_label_edit; ?></a>
	<?php } ?>
	<?php if (permission_exists('ticket_delete')) { ?>
	<a href='v_profile_delete.php?id=<?php echo $ticket['ticket_id']; ?>' onclick="return confirm('Do you really want to delete this?')" 
		alt='delete'><?php echo $v_link_label_delete; ?></a>
	<?php } ?>
</td>
</tr>
<?php 
}
?>
<tr>
<td colspan='6' align='right' width='42'>
	<?php if (permission_exists('ticket_add')) { ?>
		<a href='v_ticket_create.php' alt='Create Ticket'><?php echo $v_link_label_add; ?></a>
	<?php } ?>
</td>
</tr>
</table>
</div>
