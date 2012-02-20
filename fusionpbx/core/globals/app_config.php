<?php
	//application details
		$apps[$x]['name'] = 'Global Settings';
		$apps[$x]['guid'] = '2c2453c0-1bea-4475-9f44-4d969650de09';
		$apps[$x]['category'] = '';
		$apps[$x]['subcategory'] = '';
		$apps[$x]['version'] = '';
		$apps[$x]['license'] = 'Mozilla Public License 1.1';
		$apps[$x]['url'] = 'http://www.fusionpbx.com';
		$apps[$x]['description']['en'] = '';

	//menu details
		$apps[$x]['menu'][0]['title']['en'] = 'Global Settings';
		$apps[$x]['menu'][0]['uuid'] = '834b2739-9e99-4345-9b0b-7ec3ca332b67';
		$apps[$x]['menu'][0]['parent_uuid'] = '594d99c5-6128-9c88-ca35-4b33392cec0f';
		$apps[$x]['menu'][0]['category'] = 'internal';
		$apps[$x]['menu'][0]['path'] = '/core/globals/global_settings.php';
		$apps[$x]['menu'][0]['groups'][] = 'superadmin';

	//permission details
		$y = 0;
		$apps[$x]['permissions'][$y]['name'] = 'global_setting_add';
		$apps[$x]['permissions'][$y]['groups'][] = 'superadmin';
		$y++;
		$apps[$x]['permissions'][$y]['name'] = 'global_setting_edit';
		$apps[$x]['permissions'][$y]['groups'][] = 'superadmin';
		$y++;
		$apps[$x]['permissions'][$y]['name'] = 'global_setting_delete';
		$apps[$x]['permissions'][$y]['groups'][] = 'superadmin';
		$y++;

	//schema details
		$y = 0; //table array index
		$z = 0; //field array index
		$apps[$x]['db'][$y]['table'] = 'v_global_settings';
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'global_setting_uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'char(36)';
		$apps[$x]['db'][$y]['fields'][$z]['key'] = 'primary';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'global_setting_category';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = 'Enter the category.';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'global_setting_subcategory';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = 'Enter the subcategory.';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'global_setting_name';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = 'Enter the name.';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'global_setting_value';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = 'Enter the value.';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'global_setting_enabled';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'global_setting_description';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
?>