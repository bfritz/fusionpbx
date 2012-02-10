<?php
	//application details
		$apps[$x]['name'] = "PHP Editor";
		$apps[$x]['uuid'] = '0a36722f-eee1-889e-baa9-2ce05b09e365';
		$apps[$x]['category'] = 'System';
		$apps[$x]['subcategory'] = '';
		$apps[$x]['version'] = '';
		$apps[$x]['license'] = 'Mozilla Public License 1.1';
		$apps[$x]['url'] = 'http://www.fusionpbx.com';
		$apps[$x]['description']['en'] = 'PHP Editor for files in the main website directory.';

	//menu details
		$apps[$x]['menu'][0]['title']['en'] = 'PHP Editor';
		$apps[$x]['menu'][0]['uuid'] = 'eae1f2d6-789b-807c-cc26-44501e848693';
		$apps[$x]['menu'][0]['parent_uuid'] = '594d99c5-6128-9c88-ca35-4b33392cec0f';
		$apps[$x]['menu'][0]['category'] = 'external';
		$apps[$x]['menu'][0]['path'] = '/mod/php_edit/index.php';
		$apps[$x]['menu'][0]['groups'][] = 'superadmin';

	//permission details
		$apps[$x]['permissions'][0]['name'] = 'php_editor_view';
		$apps[$x]['permissions'][0]['groups'][] = 'superadmin';

		$apps[$x]['permissions'][1]['name'] = 'php_editor_save';
		$apps[$x]['permissions'][1]['groups'][] = 'superadmin';
?>