<?php
	$apps[$x]['name'] = "PHP Service";
	$apps[$x]['guid'] = '93F55DA0-3B33-DA5B-C6DB-4CDD6DE97FBD';
	$apps[$x]['category'] = 'System';
	$apps[$x]['subcategory'] = '';
	$apps[$x]['version'] = '';
	$apps[$x]['menu'][0]['title']['en'] = 'PHP Service';
	$apps[$x]['menu'][0]['guid'] = 'A8196E2F-5F60-E723-AA3E-83ED76B2EF09';
	$apps[$x]['menu'][0]['parent_guid'] = '594D99C5-6128-9C88-CA35-4B33392CEC0F';
	$apps[$x]['menu'][0]['category'] = 'internal';
	$apps[$x]['menu'][0]['path'] = '/mod/php_service/v_php_service.php';
	$apps[$x]['menu'][0]['groups'][] = 'superadmin';
	$apps[$x]['permissions'][] = 'php_service_view';
	$apps[$x]['permissions'][] = 'php_service_add';
	$apps[$x]['permissions'][] = 'php_service_edit';
	$apps[$x]['permissions'][] = 'php_service_delet';
	$apps[$x]['license'] = 'Mozilla Public License 1.1';
	$apps[$x]['url'] = 'http://www.fusionpbx.com';
	$apps[$x]['description']['en'] = 'Manages multiple dynamic and customizable services. There are many possible uses including alerts, ssh access control, scheduling commands to run, and many others uses that are yet to be discovered.';
?>