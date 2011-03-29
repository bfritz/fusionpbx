<?php
	$apps[$x]['name'] = "Modules";
	$apps[$x]['guid'] = '5EB9CBA1-8CB6-5D21-E36A-775475F16B5E';
	$apps[$x]['category'] = '';
	$apps[$x]['subcategory'] = '';
	$apps[$x]['version'] = '';
	$apps[$x]['menu'][0]['title']['en'] = 'Modules';
	$apps[$x]['menu'][0]['guid'] = '49FDB4E1-5417-0E7A-84B3-EB77F5263EA7';
	$apps[$x]['menu'][0]['parent_guid'] = '02194288-6D56-6D3E-0B1A-D53A2BC10788';
	$apps[$x]['menu'][0]['category'] = 'internal';
	$apps[$x]['menu'][0]['path'] = '/mod/modules/v_modules.php';
	$apps[$x]['menu'][0]['groups'][] = 'superadmin';
	$apps[$x]['permissions'][] = 'modules_view';
	$apps[$x]['permissions'][] = 'modules_add';
	$apps[$x]['permissions'][] = 'modules_edit';
	$apps[$x]['permissions'][] = 'modules_delete';
	$apps[$x]['license'] = 'Mozilla Public License 1.1';
	$apps[$x]['url'] = 'http://www.fusionpbx.com';
	$apps[$x]['description']['en'] = 'Modules extend the features of the system. Use this page to enable or disable modules.';
?>