<?php
	$apps[$x]['name'] = "Conferences";
	$apps[$x]['guid'] = 'B81412E8-7253-91F4-E48E-42FC2C9A38D9';
	$apps[$x]['category'] = 'PBX';
	$apps[$x]['subcategory'] = '';
	$apps[$x]['version'] = '';
	$apps[$x]['menu'][0]['title']['en'] = 'Conferences';
	$apps[$x]['menu'][0]['guid'] = '9F2A8C08-3E65-C41C-A716-3B53D42BC4D4';
	$apps[$x]['menu'][0]['parent_guid'] = 'FD29E39C-C936-F5FC-8E2B-611681B266B5';
	$apps[$x]['menu'][0]['category'] = 'internal';
	$apps[$x]['menu'][0]['path'] = '/mod/conferences/v_conferences.php';
	$apps[$x]['menu'][0]['groups'][] = 'user';
	$apps[$x]['menu'][0]['groups'][] = 'admin';
	$apps[$x]['menu'][0]['groups'][] = 'superadmin';
	$apps[$x]['permissions'][] = 'conferences_view';
	$apps[$x]['permissions'][] = 'conferences_add';
	$apps[$x]['permissions'][] = 'conferences_edit';
	$apps[$x]['permissions'][] = 'conferences_delete';
	$apps[$x]['license'] = 'Mozilla Public License 1.1';
	$apps[$x]['url'] = 'http://www.fusionpbx.com';
	$apps[$x]['description']['en'] = 'Conferences is used to setup conference rooms with a name, description, and optional pin number.';
?>