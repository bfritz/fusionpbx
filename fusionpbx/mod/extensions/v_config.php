<?php
	$apps[$x]['name'] = "Extensions";
	$apps[$x]['guid'] = 'E68D9689-2769-E013-28FA-6214BF47FCA3';
	$apps[$x]['category'] = 'PBX';
	$apps[$x]['subcategory'] = '';
	$apps[$x]['version'] = '';
	$apps[$x]['menu'][0]['title']['en'] = 'Extensions';
	$apps[$x]['menu'][0]['guid'] = 'D3036A99-9A9F-2AD6-A82A-1FE7BEBBE2D3';
	$apps[$x]['menu'][0]['parent_guid'] = 'BC96D773-EE57-0CDD-C3AC-2D91ABA61B55';
	$apps[$x]['menu'][0]['category'] = 'internal';
	$apps[$x]['menu'][0]['path'] = '/mod/extensions/v_extensions.php';
	$apps[$x]['menu'][0]['groups'][] = 'admin';
	$apps[$x]['menu'][0]['groups'][] = 'superadmin';
	$apps[$x]['permissions'][] = 'extension_view';
	$apps[$x]['permissions'][] = 'extension_add';
	$apps[$x]['permissions'][] = 'extension_edit';
	$apps[$x]['permissions'][] = 'extension_delete';
	$apps[$x]['license'] = 'Mozilla Public License 1.1';
	$apps[$x]['url'] = 'http://www.fusionpbx.com';
	$apps[$x]['description']['en'] = 'Used Configure SIP extensions. ';
?>