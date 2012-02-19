<?php
	//application details
		$apps[$x]['name'] = "XML Editor";
		$apps[$x]['uuid'] = '784772b5-6004-4ff3-ca21-cad4acab158f';
		$apps[$x]['category'] = 'PBX';
		$apps[$x]['subcategory'] = '';
		$apps[$x]['version'] = '';
		$apps[$x]['license'] = 'Mozilla Public License 1.1';
		$apps[$x]['url'] = 'http://www.fusionpbx.com';
		$apps[$x]['description']['en'] = 'XML Editor is an easy ajax based xml editor.';

	//menu details
		$apps[$x]['menu'][0]['title']['en'] = 'XML Editor';
		$apps[$x]['menu'][0]['uuid'] = '16013877-606a-2a05-7d6a-c1b215839131';
		$apps[$x]['menu'][0]['parent_uuid'] = '594d99c5-6128-9c88-ca35-4b33392cec0f';
		$apps[$x]['menu'][0]['category'] = 'external';
		$apps[$x]['menu'][0]['path'] = '/mod/xml_edit/';
		$apps[$x]['menu'][0]['groups'][] = 'superadmin';

	//permission details
		$apps[$x]['permissions'][0]['name'] = 'xml_editor_view';
		$apps[$x]['permissions'][0]['groups'][] = 'superadmin';
		
		$apps[$x]['permissions'][1]['name'] = 'xml_editor_save';
		$apps[$x]['permissions'][1]['groups'][] = 'superadmin';
?>