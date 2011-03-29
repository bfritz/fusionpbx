<?php
	$apps[$x]['name'] = "Grammar Editor";
	$apps[$x]['guid'] = '2D5DB509-433D-1751-1740-EED43862B85F';
	$apps[$x]['category'] = 'PBX';
	$apps[$x]['subcategory'] = '';
	$apps[$x]['version'] = '';
	$apps[$x]['menu'][0]['title']['en'] = 'Grammar Editor';
	$apps[$x]['menu'][0]['guid'] = 'C3DB739E-89F9-0FA2-44CE-0F4C2FF43B1A';
	$apps[$x]['menu'][0]['parent_guid'] = '594D99C5-6128-9C88-CA35-4B33392CEC0F';
	$apps[$x]['menu'][0]['category'] = 'internal';
	$apps[$x]['menu'][0]['path'] = '<!--{project_path}-->/mod/grammar_edit/index.php';
	$apps[$x]['menu'][0]['groups'][] = 'superadmin';
	$apps[$x]['permissions'][] = 'grammar_view';
	$apps[$x]['permissions'][] = 'grammar_save';
	$apps[$x]['license'] = 'Mozilla Public License 1.1';
	$apps[$x]['url'] = 'http://www.fusionpbx.com';
	$apps[$x]['description']['en'] = 'Grammar editor is an AJAX based tool to edit speech recognition grammar files.';
?>