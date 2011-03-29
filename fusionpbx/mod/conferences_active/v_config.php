<?php
	$apps[$x]['name'] = "Conferences Active";
	$apps[$x]['guid'] = 'C168C943-833A-C29C-7EF9-D1EE78810B71';
	$apps[$x]['category'] = 'PBX';
	$apps[$x]['subcategory'] = '';
	$apps[$x]['version'] = '';
	$apps[$x]['menu'][0]['title']['en'] = 'Active Conferences';
	$apps[$x]['menu'][0]['guid'] = '2D857BBB-43B9-B8F7-A138-642868E0453A';
	$apps[$x]['menu'][0]['parent_guid'] = '0438B504-8613-7887-C420-C837FFB20CB1';
	$apps[$x]['menu'][0]['category'] = 'internal';
	$apps[$x]['menu'][0]['path'] = '/mod/conferences_active/v_conferences_active.php';
	$apps[$x]['menu'][0]['groups'][] = 'user';
	$apps[$x]['menu'][0]['groups'][] = 'admin';
	$apps[$x]['menu'][0]['groups'][] = 'superadmin';
	$apps[$x]['permissions'][] = 'conferences_active_view';
	$apps[$x]['permissions'][] = 'conferences_active_record';
	$apps[$x]['permissions'][] = 'conferences_active_lock';
	$apps[$x]['permissions'][] = 'conferences_active_kick';
	$apps[$x]['permissions'][] = 'conferences_active_energy';
	$apps[$x]['permissions'][] = 'conferences_active_volume';
	$apps[$x]['permissions'][] = 'conferences_active_gain';
	$apps[$x]['permissions'][] = 'conferences_active_mute';
	$apps[$x]['permissions'][] = 'conferences_active_deaf';
	$apps[$x]['license'] = 'Mozilla Public License 1.1';
	$apps[$x]['url'] = 'http://www.fusionpbx.com';
	$apps[$x]['description']['en'] = 'AJAX tool to view and manage all active callers in a conference room.';
?>