<?php
/* $Id$ */
/*
  v_features.php
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

require_once "includes/v_dialplan_entry_exists.php";
require_once "includes/header.php";

?>


<div align='center'>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr><td class="tabnavtbl">
<?php

//build_menu();

?>
</td></tr>
</table>

<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
	 <td class="tabcont" align='left'>
	<!--
	<table width="100%" border="0" cellpadding="6" cellspacing="0">
	  <tr>
		<td><p><span class="vexpl"><span class="red"><strong>Features<br>
			</strong></span>
			List of a few of the features.
			</p></td>
	  </tr>
	</table>
	<br />-->

	<br />
	<br />

	<table width="100%" border="0" cellpadding="6" cellspacing="0">
	<tr>
	  <th colspan='2' align='left'>Auto Attendant</th>
	</tr>
	<tr>
		<td width='10%' class="vncell" style='text-align: center;'><a href='<?php echo PROJECT_PATH; ?>/mod/auto_attendant/v_auto_attendant.php'>Open</a></td>
		<td class="vtable">
			Auto Attendant provides callers the ability to choose between multiple options that direct calls to extensions, 
			voicemail, conferences, queues, other auto attendants, and external phone numbers.
		</td>
	</tr>
	</table>

	<br />
	<br />

	<table width="100%" border="0" cellpadding="6" cellspacing="0">
	<tr>
	  <th colspan='2' align='left'>Call Detail Records</th>
	</tr>
	<tr>
		<td width='10%' class="vncell" style='text-align: center;'><a href='<?php echo PROJECT_PATH; ?>/core/cdr/v_cdr.php'>Open</a></td>
		<td class="vtable">
			Call Detail Records (CDRs) are detailed information on the calls. The information contains source, 
			destination, duration, and other useful call details. Use the fields to filter the information for
			the specific call records that are desired. Then view the calls in the list or download them as comma
			seperated file by using the 'csv' button.
		</td>
	</tr>
	</table>

	<br />
	<br />


	<table width="100%" border="0" cellpadding="6" cellspacing="0">
	<tr>
	  <th colspan='2' align='left'>XML Editor</th>
	</tr>
	<tr>
		<td width='10%' class="vncell" style='text-align: center;'><a href='<?php echo PROJECT_PATH; ?>/mod/xml_edit/' target='_blank'>Open</a></td>
		<td class="vtable">
			Configuration editor enables advanced configuration changes.
		</td>
	</tr>
	</table>

	<br />
	<br />



	<table width="100%" border="0" cellpadding="6" cellspacing="0">
	<tr>
	  <th colspan='2' align='left'>Direct Inward System Access</th>
	</tr>
	<tr>
		<td width='10%' class="vncell"></td>
		<td class="vtable">
			Direct Inward System Access (DISA) allows inbound callers to make internal or external calls. For security reasons 
			it is disabled by default. To enable it first set a secure pin number from the Settings->Admin PIN Number.
			Then go to Dialplan tab and find the DISA entry and edit it to set 'Enabled' to 'true'. 
			To use DISA dial *3472 (disa) enter the admin pin code and the extension or phone number you wish to call.
		</td>
	</tr>
	</table>

	<br />
	<br />
	<?php
	if ($v_fax_show) {
	?>
	<table width="100%" border="0" cellpadding="6" cellspacing="0">
	<tr>
	  <th colspan='2' align='left'>FAX</th>
	</tr>
	<tr>
		<td width='10%' class="vncell" style='text-align: center;'><a href='<?php echo PROJECT_PATH; ?>/mod/fax/v_fax.php'>Open</a></td>
		<td class="vtable">
			Transmit and View Received Faxes.
		</td>
	</tr>
	</table>

	<br />
	<br />
	<?php
	}
	?>

	<table width="100%" border="0" cellpadding="6" cellspacing="0">
	<tr>
	  <th colspan='2' align='left'>Hunt Group</th>
	</tr>
	<tr>
		<td width='10%' class="vncell" style='text-align: center;'><a href='<?php echo PROJECT_PATH; ?>/mod/hunt_group/v_hunt_group.php'>Open</a></td>
		<td class="vtable">
			Hunt Group is a group of destinations to call at once or in succession.
		</td>
	</tr>
	</table>

	<br />
	<br />

	<table width="100%" border="0" cellpadding="6" cellspacing="0">
	<tr>
	  <th colspan='2' align='left'>Modules</th>
	</tr>
	<tr>
		<td width='10%' class="vncell" style='text-align: center;'><a href='<?php echo PROJECT_PATH; ?>/core/modules/v_modules.php'>Open</a></td>
		<td class="vtable">
			Modules add additional features and can be enabled or disabled to provide the desired features.
		</td>
	 </tr>
	 </table>

	<br />
	<br />

	<table width="100%" border="0" cellpadding="6" cellspacing="0">
	<tr>
	  <th colspan='2' align='left'>Music on Hold</th>
	</tr>
	<tr>
		<td width='10%' class="vncell" style='text-align: center;'><a href='<?php echo PROJECT_PATH; ?>/mod/recordings/v_recordings.php'>Open</a></td>
		<td class="vtable">
			Music on hold can be in WAV or MP3 format. To play an MP3 files you must have mod_shout enabled on the 'Modules' tab. 
			For best performance upload 16bit 8khz/16khz Mono WAV files.
		</td>
	</tr>
	</table>

	<br />
	<br />

	<table width="100%" border="0" cellpadding="6" cellspacing="0">
	<tr>
	  <th colspan='2' align='left'>Recordings</th>
	</tr>
	<tr>
		<td width='10%' class="vncell" style='text-align: center;'><a href='<?php echo PROJECT_PATH; ?>/mod/recordings/v_recordings.php'>Open</a></td>
		<td class="vtable">
			To make a recording dial *732673 (record) or you can make a 16bit 8khz/16khz
			Mono WAV file then copy it to the following directory then refresh the page to play
			it back. Click on the 'Filename' to download it or the 'Recording Name' to play the audio.
		</td>
	</tr>
	</table>

	<br />
	<br />

	<table width="100%" border="0" cellpadding="6" cellspacing="0">
	<tr>
	  <th colspan='2' align='left'>Voicemail Status</th>
	</tr>
	<tr>
		<td width='10%' class="vncell" style='text-align: center;'><a href='<?php echo PROJECT_PATH; ?>/mod/voicemail_status/v_voicemail.php'>Open</a></td>
		<td class="vtable">
			Provides a list of all voicemail boxes with the total number of voicemails for each box.
			Each voicemail box has a button to 'restore default preferences' this removes the greetings
			and sets the voicemail greetings back to default.
		</td>
	</tr>
	</table>
<br />
<br />
<br />
<br />
<br />
<br />
<br />
<br />

</td>
</tr>
</table>

</div>


<?php

require_once "includes/footer.php";

?>
</body>
</html>
