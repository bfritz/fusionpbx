<?php
	$apps[$x]['name'] = "Upgrade";
	$apps[$x]['guid'] = '8B1D7EB5-1009-052C-E1A8-D1F4887A3F5C';
	$apps[$x]['category'] = 'Core';
	$apps[$x]['subcategory'] = '';
	$apps[$x]['version'] = '';
	//$apps[$x]['menu'][0]['title']['en'] = 'Upgrade';
	//$apps[$x]['menu'][0]['guid'] = '';
	//$apps[$x]['menu'][0]['parent_guid'] = '';
	//$apps[$x]['menu'][0]['category'] = 'internal';
	//$apps[$x]['menu'][0]['path'] = '';
	$apps[$x]['menu'][0]['groups'][] = 'superadmin';
	$apps[$x]['permissions'][] = 'menu_view';
	$apps[$x]['permissions'][] = 'menu_add';
	$apps[$x]['permissions'][] = 'menu_edit';
	$apps[$x]['permissions'][] = 'menu_delete';
	$apps[$x]['permissions'][] = 'menu_restore';
	$apps[$x]['url'] = 'http://www.fusionpbx.com';
	$apps[$x]['description']['en'] = 'Upgrade tool to update the software.';
?>