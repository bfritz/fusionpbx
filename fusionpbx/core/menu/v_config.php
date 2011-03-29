<?php
	$apps[$x]['name'] = "Menu Manager";
	$apps[$x]['guid'] = 'F4B3B3D2-6287-489C-2A00-64529E46F2D7';
	$apps[$x]['category'] = 'Core';
	$apps[$x]['subcategory'] = '';
	$apps[$x]['version'] = '';
	$apps[$x]['menu'][0]['title']['en'] = 'Menu Manager';
	$apps[$x]['menu'][0]['guid'] = 'DA3A9AB4-C28E-EA8D-50CC-E8405AC8E76E';
	$apps[$x]['menu'][0]['parent_guid'] = '594D99C5-6128-9C88-CA35-4B33392CEC0F';
	$apps[$x]['menu'][0]['category'] = 'internal';
	$apps[$x]['menu'][0]['path'] = '/core/menu/menu_list.php';
	$apps[$x]['menu'][0]['groups'][] = 'superadmin';
	$apps[$x]['permissions'][] = 'menu_view';
	$apps[$x]['permissions'][] = 'menu_add';
	$apps[$x]['permissions'][] = 'menu_edit';
	$apps[$x]['permissions'][] = 'menu_delete';
	$apps[$x]['permissions'][] = 'menu_restore';
	$apps[$x]['license'] = 'Mozilla Public License 1.1';
	$apps[$x]['url'] = 'http://www.fusionpbx.com';
	$apps[$x]['description']['en'] = 'The menu can be customized using this tool.';
?>