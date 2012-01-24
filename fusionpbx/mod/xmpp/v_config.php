<?php
	//application details
		$apps[$x]['name'] = "XMPP Manager";
		$apps[$x]['uuid'] = '740F1C0D-6D82-FCDE-3873-0FC9779789EC';
		$apps[$x]['category'] = '';
		$apps[$x]['subcategory'] = '';
		$apps[$x]['version'] = '';
		$apps[$x]['license'] = 'Mozilla Public License 1.1';
		$apps[$x]['url'] = 'http://www.fusionpbx.com';
		$apps[$x]['description']['en'] = 'Allow User to Open a Flash Phone for his Extension.';

	//menu details
		$apps[$x]['menu'][0]['title']['en'] = 'XMPP Manager';
		$apps[$x]['menu'][0]['uuid'] = '1808365B-0F7C-7555-89D0-31B3D9A75ABB';
		$apps[$x]['menu'][0]['parent_uuid'] = 'BC96D773-EE57-0CDD-C3AC-2D91ABA61B55';
		$apps[$x]['menu'][0]['category'] = 'internal';
		$apps[$x]['menu'][0]['path'] = '/mod/xmpp/v_xmpp.php';
		$apps[$x]['menu'][0]['groups'][] = 'admin';
		$apps[$x]['menu'][0]['groups'][] = 'superadmin';

	//permission details
		$apps[$x]['permissions'][0]['name'] = 'xmpp_view';
		$apps[$x]['permissions'][0]['groups'][] = 'admin';
		$apps[$x]['permissions'][0]['groups'][] = 'superadmin';

		$apps[$x]['permissions'][1]['name'] = 'xmpp_add';
		$apps[$x]['permissions'][1]['groups'][] = 'admin';
		$apps[$x]['permissions'][1]['groups'][] = 'superadmin';

		$apps[$x]['permissions'][2]['name'] = 'xmpp_edit';
		$apps[$x]['permissions'][2]['groups'][] = 'admin';
		$apps[$x]['permissions'][2]['groups'][] = 'superadmin';

		$apps[$x]['permissions'][3]['name'] = 'xmpp_delete';
		$apps[$x]['permissions'][3]['groups'][] = 'admin';
		$apps[$x]['permissions'][3]['groups'][] = 'superadmin';

	//schema details
		$y = 0; //table array index
		$z = 0; //field array index
		$apps[$x]['db'][$y]['table'] = 'v_xmpp';
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'id';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'xmpp_profile_id';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'serial';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'integer';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'INT NOT NULL AUTO_INCREMENT';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = 'primary key';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'xmpp_profile_uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'char(36)';
		$apps[$x]['db'][$y]['fields'][$z]['key'] = 'primary';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'domain_uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'char(36)';
		$apps[$x]['db'][$y]['fields'][$z]['key'] = 'foreign';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'v_id';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$apps[$x]['db'][$y]['fields'][$z]['deprecated'] = 'true';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'profile_name';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text'; 
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'username';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text'; 
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'password';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text'; 
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'dialplan';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text'; 
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'context';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text'; 
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'rtp_ip';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text'; 
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'ext_rtp_ip';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text'; 
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'auto_login';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text'; 
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'sasl_type';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text'; 
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'xmpp_server';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text'; 
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'tls_enable';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text'; 
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'usr_rtp_timer';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text'; 
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'default_exten';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text'; 
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'vad';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text'; 
		$apps[$x]['db'][$y]['fields'][$z]['description'] = 'in/out/both';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'avatar';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text'; 
		$apps[$x]['db'][$y]['fields'][$z]['description'] = 'example: /path/to/tiny.jpg';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'candidate_acl';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text'; 
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'local_network_acl';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text'; 
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'enabled';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text'; 
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'description';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text'; 
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';

?>