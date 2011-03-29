<?php
	$apps[$x]['name'] = "Voicemail Messages";
	$apps[$x]['guid'] = '789EA83B-4063-5076-55BA-2F7D63AFA86B';
	$apps[$x]['category'] = 'voicemail';
	$apps[$x]['subcategory'] = 'PBX';
	$apps[$x]['version'] = '';
	$apps[$x]['menu'][0]['title']['en'] = 'Voicemail';
	$apps[$x]['menu'][0]['guid'] = 'E10D5672-0F82-6E5D-5022-A02AC8545198';
	$apps[$x]['menu'][0]['parent_guid'] = 'FD29E39C-C936-F5FC-8E2B-611681B266B5';
	$apps[$x]['menu'][0]['category'] = 'internal';
	$apps[$x]['menu'][0]['path'] = '/mod/voicemail_msgs/v_voicemail_msgs.php';
	$apps[$x]['menu'][0]['groups'][] = 'user';
	$apps[$x]['menu'][0]['groups'][] = 'admin';
	$apps[$x]['menu'][0]['groups'][] = 'superadmin';
	$apps[$x]['permissions'][] = 'voicemail_view';
	$apps[$x]['permissions'][] = 'voicemail_add';
	$apps[$x]['permissions'][] = 'voicemail_edit';
	$apps[$x]['permissions'][] = 'voicemail_delete';
	$apps[$x]['license'] = 'Mozilla Public License 1.1';
	$apps[$x]['url'] = 'http://www.fusionpbx.com';
	$apps[$x]['description']['en'] = 'Voicemails can be listed, played, downloaded and deleted. ';
?>