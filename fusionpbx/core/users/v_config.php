<?php
	$apps[$x]['name'] = "User Manager";
	$apps[$x]['guid'] = '112124B3-95C2-5352-7E9D-D14C0B88F207';
	$apps[$x]['category'] = 'Core';
	$apps[$x]['subcategory'] = '';
	$apps[$x]['version'] = '';
	$apps[$x]['menu'][0]['title']['en'] = 'User Manager';
	$apps[$x]['menu'][0]['guid'] = '0D57CC1E-1874-47B9-7DDD-FE1F57CEC99B';
	$apps[$x]['menu'][0]['parent_guid'] = 'BC96D773-EE57-0CDD-C3AC-2D91ABA61B55';
	$apps[$x]['menu'][0]['category'] = 'internal';
	$apps[$x]['menu'][0]['path'] = '/core/users/index.php';
	$apps[$x]['menu'][0]['groups'][] = 'admin';
	$apps[$x]['menu'][0]['groups'][] = 'superadmin';
	$apps[$x]['menu'][1]['title']['en'] = 'Group Manager';
	$apps[$x]['menu'][1]['guid'] = '3B4ACC6D-827B-F537-BF21-0093D94FFEC7';
	$apps[$x]['menu'][1]['parent_guid'] = '594D99C5-6128-9C88-CA35-4B33392CEC0F';
	$apps[$x]['menu'][1]['category'] = 'internal';
	$apps[$x]['menu'][1]['path'] = '/core/users/grouplist.php';
	$apps[$x]['menu'][1]['groups'][] = 'superadmin';
	$apps[$x]['permissions'][] = 'user_view';
	$apps[$x]['permissions'][] = 'user_add';
	$apps[$x]['permissions'][] = 'user_edit';
	$apps[$x]['permissions'][] = 'user_delete';
	$apps[$x]['license'] = 'Mozilla Public License 1.1';
	$apps[$x]['url'] = 'http://www.fusionpbx.com';
	$apps[$x]['description']['en'] = 'Add, edit, delete, and search for users.';
?>