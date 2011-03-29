<?php
	$apps[$x]['name'] = "Account Settings";
	$apps[$x]['guid'] = '3A3337F7-78D1-23E3-0CFD-F14499B8ED97';
	$apps[$x]['category'] = 'PBX';
	$apps[$x]['subcategory'] = '';
	$apps[$x]['version'] = '';
	$apps[$x]['menu'][0]['title']['en'] = 'Account Settings';
	$apps[$x]['menu'][0]['guid'] = '4D532F0B-C206-C39D-FF33-FC67D668FB69';
	$apps[$x]['menu'][0]['parent_guid'] = '02194288-6D56-6D3E-0B1A-D53A2BC10788';
	$apps[$x]['menu'][0]['category'] = 'internal';
	$apps[$x]['menu'][0]['path'] = '/mod/users/usersupdate.php';
	$apps[$x]['menu'][0]['groups'][] = 'user';
	$apps[$x]['menu'][0]['groups'][] = 'admin';
	$apps[$x]['menu'][0]['groups'][] = 'superadmin';
	$apps[$x]['permissions'][] = 'user_account_settings_view';
	$apps[$x]['permissions'][] = 'user_account_settings_edit';
	$apps[$x]['license'] = 'Mozilla Public License 1.1';
	$apps[$x]['url'] = 'http://www.fusionpbx.com';
	$apps[$x]['description']['en'] = 'User account settings can be changed by the user.';
?>