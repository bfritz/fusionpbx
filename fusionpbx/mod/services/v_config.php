<?php
	$apps[$x]['name'] = "Services";
	$apps[$x]['guid'] = '35FF1F56-513A-1F6C-A393-955838FF12EE';
	$apps[$x]['category'] = 'System';
	$apps[$x]['subcategory'] = '';
	$apps[$x]['version'] = '';
	$apps[$x]['menu'][0]['title']['en'] = 'Services';
	$apps[$x]['menu'][0]['guid'] = 'C28F14E9-E5AD-E992-0931-D5F5F0DB6A79';
	$apps[$x]['menu'][0]['parent_guid'] = '0438B504-8613-7887-C420-C837FFB20CB1';
	$apps[$x]['menu'][0]['category'] = 'internal';
	$apps[$x]['menu'][0]['path'] = '/mod/services/v_services.php';
	$apps[$x]['menu'][0]['groups'][] = 'superadmin';
	$apps[$x]['permissions'][] = 'services_view';
	$apps[$x]['permissions'][] = 'services_add';
	$apps[$x]['permissions'][] = 'services_edit';
	$apps[$x]['permissions'][] = 'services_delete';
	$apps[$x]['license'] = 'Mozilla Public License 1.1';
	$apps[$x]['url'] = 'http://www.fusionpbx.com';
	$apps[$x]['description']['en'] = 'Monitor System Services.';
?>