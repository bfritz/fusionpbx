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
		$apps[$x]['menu'][0]['path'] = '/app/php_edit/index.php';
		$apps[$x]['menu'][0]['groups'][] = 'superadmin';

	//permission details
		$apps[$x]['permissions'][0]['name'] = 'php_editor_view';
		$apps[$x]['permissions'][0]['groups'][] = 'superadmin';

		$apps[$x]['permissions'][1]['name'] = 'php_editor_save';
		$apps[$x]['permissions'][1]['groups'][] = 'superadmin';

	//schema details
		$y = 0; //table array index
		$z = 0; //field array index
		$apps[$x]['db'][$y]['table'] = 'v_clips';
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'clip_uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'char(36)';
		$apps[$x]['db'][$y]['fields'][$z]['key']['type'] = 'primary';
		$apps[$x]['db'][$y]['fields'][$z]['description']['en'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'clip_name';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description']['en'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'clip_folder';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description']['en'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'clip_text_start';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description']['en'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'clip_text_end';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description']['en'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'clip_order';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description']['en'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'clip_desc';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description']['en'] = '';
?>