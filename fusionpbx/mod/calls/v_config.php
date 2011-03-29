<?php
	$apps[$x]['name'] = "Calls";
	$apps[$x]['guid'] = '19806921-E8ED-DCFF-B325-DD3E5DA4959D';
	$apps[$x]['category'] = 'PBX';
	$apps[$x]['subcategory'] = '';
	$apps[$x]['version'] = '';
	$apps[$x]['menu'][0]['title']['en'] = 'Calls';
	$apps[$x]['menu'][0]['guid'] = '';
	$apps[$x]['menu'][0]['parent_guid'] = '';
	$apps[$x]['menu'][0]['category'] = 'internal';
	$apps[$x]['menu'][0]['path'] = '/mod/calls/v_calls.php';
	$apps[$x]['menu'][0]['groups'][] = 'user';
	$apps[$x]['menu'][0]['groups'][] = 'admin';
	$apps[$x]['menu'][0]['groups'][] = 'superadmin';
	$apps[$x]['permissions'][] = 'calls_view';
	$apps[$x]['permissions'][] = 'calls_add';
	$apps[$x]['permissions'][] = 'calls_edit';
	$apps[$x]['permissions'][] = 'calls_delete';
	$apps[$x]['license'] = 'Mozilla Public License 1.1';
	$apps[$x]['url'] = 'http://www.fusionpbx.com';
	$apps[$x]['description']['en'] = 'Call Forward, Follow Me and Do Not Disturb.';
?>