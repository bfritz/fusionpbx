<?php
	//application details
		$apps[$x]['name'] = 'Databases';
		$apps[$x]['uuid'] = '8D229B6D-1383-FCEC-74C6-4CE1682479E2';
		$apps[$x]['category'] = '';
		$apps[$x]['subcategory'] = '';
		$apps[$x]['version'] = '';
		$apps[$x]['license'] = 'Mozilla Public License 1.1';
		$apps[$x]['url'] = 'http://www.fusionpbx.com';
		$apps[$x]['description']['en'] = '';

	//menu details
		$apps[$x]['menu'][0]['title']['en'] = 'Databases';
		$apps[$x]['menu'][0]['uuid'] = 'EBBD754D-CA74-D5B1-A77E-9206BA3ECC3F';
		$apps[$x]['menu'][0]['parent_uuid'] = '594D99C5-6128-9C88-CA35-4B33392CEC0F';
		$apps[$x]['menu'][0]['category'] = 'internal';
		$apps[$x]['menu'][0]['path'] = '/mod/database_connections/databases.php';
		$apps[$x]['menu'][0]['groups'][] = 'superadmin';

	//permission details
		$apps[$x]['permissions'][0]['name'] = 'database_view';
		$apps[$x]['permissions'][0]['groups'][] = 'superadmin';

		$apps[$x]['permissions'][1]['name'] = 'database_add';
		$apps[$x]['permissions'][1]['groups'][] = 'superadmin';

		$apps[$x]['permissions'][2]['name'] = 'database_edit';
		$apps[$x]['permissions'][2]['groups'][] = 'superadmin';

		$apps[$x]['permissions'][3]['name'] = 'database_delete';
		$apps[$x]['permissions'][3]['groups'][] = 'superadmin';

	//schema details
		$y = 0; //table array index
		$z = 0; //field array index
		$apps[$x]['db'][$y]['table'] = 'v_databases';
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'id';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'database_connection_id';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'serial';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'integer';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'INT NOT NULL AUTO_INCREMENT PRIMARY KEY';
		$apps[$x]['db'][$y]['fields'][$z]['deprecated'] = 'true';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'database_uuid';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'database_connection_uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'char(36)';
		$apps[$x]['db'][$y]['fields'][$z]['key'] = 'primary';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'v_id';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$apps[$x]['db'][$y]['fields'][$z]['deprecated'] = 'true';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'database_type';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'db_type';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = 'Select the database type.';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'database_host';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'db_host';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = 'Enter the host name.';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'database_port';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'db_port';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = 'Enter the port number.';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'database_name';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'db_name';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = 'Enter the database name.';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'database_username';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'db_username';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = 'Enter the database username.';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'database_password';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'db_password';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = 'Enter the database password.';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'database_path';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'db_path';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = 'Enter the database file path.';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'database_description';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'db_description';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = 'Enter the description.';
		$z++;
?>