<?php
	$apps[$x]['name'] = "Fax";
	$apps[$x]['guid'] = '24108154-4AC3-1DB6-1551-4731703A4440';
	$apps[$x]['category'] = '';
	$apps[$x]['subcategory'] = '';
	$apps[$x]['version'] = '';
	$apps[$x]['menu'][0]['title']['en'] = 'Fax Server';
	$apps[$x]['menu'][0]['guid'] = '9C9642E4-2B9B-2785-18D0-6C0A4EDE2B2F';
	$apps[$x]['menu'][0]['parent_guid'] = 'FD29E39C-C936-F5FC-8E2B-611681B266B5';
	$apps[$x]['menu'][0]['category'] = 'internal';
	$apps[$x]['menu'][0]['path'] = '/mod/fax/v_fax.php';
	$apps[$x]['menu'][0]['groups'][] = 'user';
	$apps[$x]['menu'][0]['groups'][] = 'admin';
	$apps[$x]['menu'][0]['groups'][] = 'superadmin';
	$apps[$x]['permissions'][] = 'fax_extension_view';
	$apps[$x]['permissions'][] = 'fax_extension_add';
	$apps[$x]['permissions'][] = 'fax_extension_edit';
	$apps[$x]['permissions'][] = 'fax_extension_delete';
	$apps[$x]['permissions'][] = 'fax_inbox_view';
	$apps[$x]['permissions'][] = 'fax_inbox_delete';
	$apps[$x]['permissions'][] = 'fax_sent_view';
	$apps[$x]['permissions'][] = 'fax_sent_delete';
	$apps[$x]['permissions'][] = 'fax_send';
	$apps[$x]['license'] = 'Mozilla Public License 1.1';
	$apps[$x]['url'] = 'http://www.fusionpbx.com';
	$apps[$x]['description']['en'] = 'To receive a FAX setup a fax extension and then direct the incoming FAX with a dedicated number or you can detect the FAX tone by using on the Public tab.';
?>