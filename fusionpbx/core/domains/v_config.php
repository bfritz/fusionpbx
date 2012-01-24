<?php
	//application details
		$apps[$x]['name'] = 'Domains';
		$apps[$x]['guid'] = 'B31E723A-BF70-670C-A49B-470D2A232F71';
		$apps[$x]['category'] = '';
		$apps[$x]['subcategory'] = '';
		$apps[$x]['version'] = '';
		$apps[$x]['license'] = 'Mozilla Public License 1.1';
		$apps[$x]['url'] = 'http://www.fusionpbx.com';
		$apps[$x]['description']['en'] = '';

	//menu details
		$apps[$x]['menu'][$y]['title']['en'] = 'Domains';
		$apps[$x]['menu'][$y]['guid'] = '4FA7E90B-6D6C-12D4-712F-62857402B801';
		$apps[$x]['menu'][$y]['parent_guid'] = 'FD29E39C-C936-F5FC-8E2B-611681B266B5';
		$apps[$x]['menu'][$y]['category'] = 'internal';
		$apps[$x]['menu'][$y]['path'] = '/core/domains/v_domains.php';
		//$apps[$x]['menu'][$y]['groups'][] = 'user';
		//$apps[$x]['menu'][$y]['groups'][] = 'admin';
		$apps[$x]['menu'][$y]['groups'][] = 'superadmin';

	//permission details
		$y = 0;
		$apps[$x]['permissions'][$y]['name'] = 'domain_view';
		$apps[$x]['permissions'][$y]['groups'][] = 'superadmin';
		$y++;
		$apps[$x]['permissions'][$y]['name'] = 'domain_add';
		$apps[$x]['permissions'][$y]['groups'][] = 'superadmin';
		$y++;
		$apps[$x]['permissions'][$y]['name'] = 'domain_edit';
		$apps[$x]['permissions'][$y]['groups'][] = 'superadmin';
		$y++;
		$apps[$x]['permissions'][$y]['name'] = 'domain_delete';
		$apps[$x]['permissions'][$y]['groups'][] = 'superadmin';
		$y++;
		$apps[$x]['permissions'][$y]['name'] = 'domain_view';
		$apps[$x]['permissions'][$y]['groups'][] = 'superadmin';
		$y++;
		$apps[$x]['permissions'][$y]['name'] = 'domain_setting_add';
		$apps[$x]['permissions'][$y]['groups'][] = 'superadmin';
		$y++;
		$apps[$x]['permissions'][$y]['name'] = 'domain_setting_edit';
		$apps[$x]['permissions'][$y]['groups'][] = 'superadmin';
		$y++;
		$apps[$x]['permissions'][$y]['name'] = 'domain_setting_delete';
		$apps[$x]['permissions'][$y]['groups'][] = 'superadmin';
		$y++;

	//schema details
		$y = 0; //table array index
		$z = 0; //field array index
		$apps[$x]['db'][$y]['table'] = 'v_domains';
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'id';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'serial';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'integer';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'INT NOT NULL AUTO_INCREMENT';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'domain_uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'char(36)';
		$apps[$x]['db'][$y]['fields'][$z]['key'] = 'foreign';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'domain_uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'char(36)';
		$apps[$x]['db'][$y]['fields'][$z]['key'] = 'primary';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'domain_name';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = 'Enter the domain name.';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'domain_description';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = 'Enter the description.';

		$y = 1; //table array index
		$z = 0; //field array index
		$apps[$x]['db'][$y]['table'] = 'v_domain_settings';
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'id';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'serial';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'integer';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'INT NOT NULL AUTO_INCREMENT';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'domain_uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'char(36)';
		$apps[$x]['db'][$y]['fields'][$z]['key'] = 'foreign';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'domain_setting_uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'char(36)';
		$apps[$x]['db'][$y]['fields'][$z]['key'] = 'primary';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'domain_setting_name';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = 'Enter the name.';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'domain_setting_value';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = 'Enter the value.';
		$z++;
?>