<?php
	//application details
		$apps[$x]['name'] = "Calls";
		$apps[$x]['uuid'] = '19806921-e8ed-dcff-b325-dd3e5da4959d';
		$apps[$x]['category'] = 'Switch';;
		$apps[$x]['subcategory'] = '';
		$apps[$x]['version'] = '';
		$apps[$x]['license'] = 'Mozilla Public License 1.1';
		$apps[$x]['url'] = 'http://www.fusionpbx.com';
		$apps[$x]['description']['en-us'] = 'Call Forward, Follow Me and Do Not Disturb.';
		$apps[$x]['description']['es-mx'] = '';
		$apps[$x]['description']['de'] = '';
		$apps[$x]['description']['de-ch'] = '';
		$apps[$x]['description']['de-at'] = '';
		$apps[$x]['description']['fr'] = '';
		$apps[$x]['description']['fr-ca'] = '';
		$apps[$x]['description']['fr-ch'] = '';
		$apps[$x]['description']['pt-pt'] = 'Desvio de Chamadas, Seguir-me e Não Perturbar.';
		$apps[$x]['description']['pt-br'] = '';

	//menu details
		//$apps[$x]['menu'][0]['title']['en-us'] = 'Calls';
		//$apps[$x]['menu'][0]['title']['es-mx'] = '';
		//$apps[$x]['menu'][0]['title']['de'] = '';
		//$apps[$x]['menu'][0]['title']['de-ch'] = '';
		//$apps[$x]['menu'][0]['title']['de-at'] = '';
		//$apps[$x]['menu'][0]['title']['fr'] = '';
		//$apps[$x]['menu'][0]['title']['fr-ca'] = '';
		//$apps[$x]['menu'][0]['title']['fr-ch'] = '';
		//$apps[$x]['menu'][0]['title']['pt-pt'] = 'Chamadas';
		//$apps[$x]['menu'][0]['title']['pt-br'] = '';
		//$apps[$x]['menu'][0]['uuid'] = '';
		//$apps[$x]['menu'][0]['parent_uuid'] = '';
		//$apps[$x]['menu'][0]['category'] = 'internal';
		//$apps[$x]['menu'][0]['path'] = '/app/calls/calls.php';
		//$apps[$x]['menu'][0]['groups'][] = 'user';
		//$apps[$x]['menu'][0]['groups'][] = 'admin';
		//$apps[$x]['menu'][0]['groups'][] = 'superadmin';

	//permission details
		$apps[$x]['permissions'][1]['name'] = 'follow_me';
		$apps[$x]['permissions'][1]['groups'][] = 'user';
		$apps[$x]['permissions'][1]['groups'][] = 'admin';
		$apps[$x]['permissions'][1]['groups'][] = 'superadmin';

		$apps[$x]['permissions'][2]['name'] = 'call_forward';
		$apps[$x]['permissions'][2]['groups'][] = 'user';
		$apps[$x]['permissions'][2]['groups'][] = 'admin';
		$apps[$x]['permissions'][2]['groups'][] = 'superadmin';

		$apps[$x]['permissions'][3]['name'] = 'do_not_disturb';
		$apps[$x]['permissions'][3]['groups'][] = 'user';
		$apps[$x]['permissions'][3]['groups'][] = 'admin';
		$apps[$x]['permissions'][3]['groups'][] = 'superadmin';
?>