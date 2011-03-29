<?php
	$apps[$x]['name'] = "Settings";
	$apps[$x]['guid'] = 'B6B1B2E5-4BA5-044C-8A5C-18709A15EB60';
	$apps[$x]['category'] = 'PBX';
	$apps[$x]['subcategory'] = '';
	$apps[$x]['version'] = '';
	$apps[$x]['menu'][0]['title']['en'] = 'Settings';
	$apps[$x]['menu'][0]['guid'] = '148EA42A-3711-3D64-181B-07A6A3C3ED60';
	$apps[$x]['menu'][0]['parent_guid'] = '02194288-6D56-6D3E-0B1A-D53A2BC10788';
	$apps[$x]['menu'][0]['category'] = 'internal';
	$apps[$x]['menu'][0]['path'] = '/core/settings/v_settings_edit.php?id=1';
	$apps[$x]['menu'][0]['groups'][] = 'superadmin';
	$apps[$x]['permissions'][] = 'settings_view';
	$apps[$x]['permissions'][] = 'settings_edit';
	$apps[$x]['license'] = 'Mozilla Public License 1.1';
	$apps[$x]['url'] = 'http://www.fusionpbx.com';
	$apps[$x]['description']['en'] = 'PBX Settings.';
?>