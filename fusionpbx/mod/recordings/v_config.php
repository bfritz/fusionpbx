<?php
	$apps[$x]['name'] = "Recordings";
	$apps[$x]['guid'] = '83913217-C7A2-9E90-925D-A866EB40B60E';
	$apps[$x]['category'] = 'PBX';
	$apps[$x]['subcategory'] = '';
	$apps[$x]['version'] = '';
	$apps[$x]['menu'][0]['title']['en'] = 'Recordings';
	$apps[$x]['menu'][0]['guid'] = 'E4290FD2-3CCC-A758-1714-660D38453104';
	$apps[$x]['menu'][0]['parent_guid'] = 'FD29E39C-C936-F5FC-8E2B-611681B266B5';
	$apps[$x]['menu'][0]['category'] = 'internal';
	$apps[$x]['menu'][0]['path'] = '/mod/recordings/v_recordings.php';
	$apps[$x]['menu'][0]['groups'][] = 'admin';
	$apps[$x]['menu'][0]['groups'][] = 'superadmin';
	$apps[$x]['permissions'][] = 'recordings_view';
	$apps[$x]['permissions'][] = 'recordings_add';
	$apps[$x]['permissions'][] = 'recordings_edit';
	$apps[$x]['permissions'][] = 'recordings_delete';
	$apps[$x]['permissions'][] = 'recordings_upload';
	$apps[$x]['license'] = 'Mozilla Public License 1.1';
	$apps[$x]['url'] = 'http://www.fusionpbx.com';
	$apps[$x]['description']['en'] = 'Manager recordings primarily used with an IVR.';
?>