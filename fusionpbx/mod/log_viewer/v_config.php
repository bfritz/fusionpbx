<?php
	$apps[$x]['name'] = "Log Viewer";
	$apps[$x]['guid'] = '159A2724-77E1-2782-9366-DB08B3750E06';
	$apps[$x]['category'] = 'PBX';
	$apps[$x]['subcategory'] = '';
	$apps[$x]['version'] = '';
	$apps[$x]['menu'][0]['title']['en'] = 'Log Viewer';
	$apps[$x]['menu'][0]['guid'] = '781EBBEC-A55A-9D60-F7BB-F54AB2EE4E7E';
	$apps[$x]['menu'][0]['parent_guid'] = '0438B504-8613-7887-C420-C837FFB20CB1';
	$apps[$x]['menu'][0]['category'] = 'internal';
	$apps[$x]['menu'][0]['path'] = '/mod/log_viewer/v_log_viewer.php';
	$apps[$x]['menu'][0]['groups'][] = 'superadmin';
	$apps[$x]['permissions'][] = '_view';
	$apps[$x]['permissions'][] = '_add';
	$apps[$x]['permissions'][] = '_edit';
	$apps[$x]['permissions'][] = '_delete';
	$apps[$x]['license'] = 'Mozilla Public License 1.1';
	$apps[$x]['url'] = 'http://www.fusionpbx.com';
	$apps[$x]['description']['en'] = 'Display the switch logs.';
?>