<?php
	$apps[$x]['name'] = "Phones";
	$apps[$x]['guid'] = '4EFA1A1A-32E7-BF83-534B-6C8299958A8E';
	$apps[$x]['category'] = 'PBX';
	$apps[$x]['subcategory'] = '';
	$apps[$x]['version'] = '';
	$apps[$x]['menu'][0]['title']['en'] = 'Phones';
	$apps[$x]['menu'][0]['guid'] = 'F9DCE498-B7F9-740F-E592-9E8FF3DAC2A0';
	$apps[$x]['menu'][0]['parent_guid'] = 'BC96D773-EE57-0CDD-C3AC-2D91ABA61B55';
	$apps[$x]['menu'][0]['category'] = 'internal';
	$apps[$x]['menu'][0]['path'] = '/mod/hardware_phones/v_hardware_phones.php';
	$apps[$x]['menu'][0]['groups'][] = 'admin';
	$apps[$x]['menu'][0]['groups'][] = 'superadmin';
	$apps[$x]['permissions'][] = 'phone_view';
	$apps[$x]['permissions'][] = 'phone_add';
	$apps[$x]['permissions'][] = 'phone_edit';
	$apps[$x]['permissions'][] = 'phone_delete';
	$apps[$x]['license'] = 'Mozilla Public License 1.1';
	$apps[$x]['url'] = 'http://www.fusionpbx.com';
	$apps[$x]['description']['en'] = 'Phone provisioning list.';
?>