<?php
	$apps[$x]['name'] = "FIFO";
	$apps[$x]['guid'] = '16589224-C876-AEB3-F59F-523A1C0801F7';
	$apps[$x]['category'] = 'PBX';
	$apps[$x]['subcategory'] = '';
	$apps[$x]['version'] = '';
	$apps[$x]['menu'][0]['title']['en'] = 'Queues';
	$apps[$x]['menu'][0]['guid'] = 'C535AC0B-1DA1-0F9C-4653-7934C6F4732C';
	$apps[$x]['menu'][0]['parent_guid'] = 'FD29E39C-C936-F5FC-8E2B-611681B266B5';
	$apps[$x]['menu'][0]['category'] = 'internal';
	$apps[$x]['menu'][0]['path'] = '/mod/fifo/v_fifo.php';
	$apps[$x]['menu'][0]['groups'][] = 'admin';
	$apps[$x]['menu'][0]['groups'][] = 'superadmin';
	$apps[$x]['permissions'][] = 'fifo_view';
	$apps[$x]['permissions'][] = 'fifo_add';
	$apps[$x]['permissions'][] = 'fifo_edit';
	$apps[$x]['permissions'][] = 'fifo_delete';
	$apps[$x]['license'] = 'Mozilla Public License 1.1';
	$apps[$x]['url'] = 'http://www.fusionpbx.com';
	$apps[$x]['description']['en'] = 'Queues are used to setup waiting lines for callers. Also known as FIFO Queues.';
?>