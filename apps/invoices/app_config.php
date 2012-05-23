<?php
	//application details
		$apps[$x]['name'] = 'invoice';
		$apps[$x]['uuid'] = 'e5a1f4f5-7766-ec9c-118b-50f76b0788c0';
		$apps[$x]['category'] = '';
		$apps[$x]['subcategory'] = '';
		$apps[$x]['version'] = '';
		$apps[$x]['license'] = 'Mozilla Public License 1.1';
		$apps[$x]['contact_url'] = 'http://www.fusionpbx.com';
		$apps[$x]['description']['en'] = '';

	//menu details
		$apps[$x]['menu'][$y]['title']['en'] = 'Invoices';
		$apps[$x]['menu'][$y]['uuid'] = '6ebe753b-0f83-dc34-1c0b-51df2c6f0c3b';
		$apps[$x]['menu'][$y]['parent_uuid'] = 'fd29e39c-c936-f5fc-8e2b-611681b266b5';
		$apps[$x]['menu'][$y]['category'] = 'internal';
		$apps[$x]['menu'][$y]['path'] = '/app/invoices/v_invoices.php';
		//$apps[$x]['menu'][$y]['groups'][] = 'user';
		//$apps[$x]['menu'][$y]['groups'][] = 'admin';
		$apps[$x]['menu'][$y]['groups'][] = 'superadmin';

	//permission details
		$apps[$x]['permissions'][$y]['name'] = 'invoice_view';
		$apps[$x]['permissions'][$y]['groups'][] = 'superadmin';
		//$apps[$x]['permissions'][$y]['groups'][] = 'user';
		//$apps[$x]['permissions'][$y]['groups'][] = 'admin';

		$apps[$x]['permissions'][1]['name'] = 'invoice_add';
		$apps[$x]['permissions'][1]['groups'][] = 'superadmin';
		//$apps[$x]['permissions'][1]['groups'][] = 'admin';

		$apps[$x]['permissions'][2]['name'] = 'invoice_edit';
		$apps[$x]['permissions'][2]['groups'][] = 'superadmin';
		//$apps[$x]['permissions'][2]['groups'][] = 'admin';
		//$apps[$x]['permissions'][2]['groups'][] = 'user';

		$apps[$x]['permissions'][$y]['name'] = 'invoice_item_view';
		$apps[$x]['permissions'][$y]['groups'][] = 'superadmin';
		//$apps[$x]['permissions'][$y]['groups'][] = 'user';
		//$apps[$x]['permissions'][$y]['groups'][] = 'admin';

		$apps[$x]['permissions'][1]['name'] = 'invoice_item_add';
		$apps[$x]['permissions'][1]['groups'][] = 'superadmin';
		//$apps[$x]['permissions'][1]['groups'][] = 'admin';

		$apps[$x]['permissions'][2]['name'] = 'invoice_item_edit';
		$apps[$x]['permissions'][2]['groups'][] = 'superadmin';
		//$apps[$x]['permissions'][2]['groups'][] = 'admin';
		//$apps[$x]['permissions'][2]['groups'][] = 'user';

		$apps[$x]['permissions'][3]['name'] = 'invoice_item_delete';
		$apps[$x]['permissions'][3]['groups'][] = 'superadmin';
		//$apps[$x]['permissions'][3]['groups'][] = 'admin';

		$apps[$x]['permissions'][3]['name'] = 'invoice_delete';
		$apps[$x]['permissions'][3]['groups'][] = 'superadmin';
		//$apps[$x]['permissions'][3]['groups'][] = 'admin';

	//schema details
		$y = 0; //table array index
		$z = 0; //field array index
		$apps[$x]['db'][$y]['table'] = 'v_invoices';
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'id';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'invoice_id';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'serial';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'integer';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'INT NOT NULL AUTO_INCREMENT';
		$apps[$x]['db'][$y]['fields'][$z]['deprecated'] = 'true';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'invoice_uuid';
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
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'domain_id ';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'v_id';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$apps[$x]['db'][$y]['fields'][$z]['deprecated'] = 'true';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'contact_uuid_from';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'contact_id_from';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'char(36)';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = 'Contact ID invoice is sent from';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'contact_uuid_to';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'contact_id_to';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'char(36)';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = 'Contact ID invoice is sent to';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'invoice_number';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'numeric';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = 'Enter the invoice number.';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'invoice_date';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'timestamp with time zone';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'datetime';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'timestamp';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = 'Enter the date.';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'invoice_notes';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = 'Enter the invoice notes.';
		$z++;

		$y = 1; //table array index
		$apps[$x]['db'][$y]['table'] = 'v_invoice_items';
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'id';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'invoice_item_id';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'serial';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'integer';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'INT NOT NULL AUTO_INCREMENT';
		$apps[$x]['db'][$y]['fields'][$z]['deprecated'] = 'true';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'invoice_item_uuid';
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
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'invoice_id';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'numeric';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$apps[$x]['db'][$y]['fields'][$z]['deprecated'] = 'true';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'invoice_uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'char(36)';
		$apps[$x]['db'][$y]['fields'][$z]['key'] = 'primary';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'item_qty';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'numeric';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = 'Enter the Quantity';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'item_desc';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = 'Enter the description.';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'item_unit_price';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'numeric';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'numeric';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'decimal(10,2)';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = 'Enter the unit price.';
		$z++;

?>