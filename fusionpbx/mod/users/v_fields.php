<?php
	// Add/Edit Form Fields
	$forms[1]['header'] = "Please fill out this form completely. All BOLD fields are required.";
	$forms[1]['fields'][1] = array('username', "Username:", "text", TRUE, "Please provid a Username.<br>\n");
	$forms[1]['fields'][2] = array('password', "Password:", "password", TRUE, "Please provid a Username.<br>\n");
	$forms[1]['fields'][3] = array('confirmpassword', "Confirm Password:", "password", FALSE, "");
	$forms[1]['fields'][4] = array('usercompanyname', "Company Name:", "text", FALSE, "");
	$forms[1]['fields'][5] = array('userfirstname', "First Name:", "text", TRUE, "Please provide a first name.<br>\n");
	$forms[1]['fields'][6] = array('userlastname', "Last Name:", "text", TRUE, "Please provide a last name.<br>\n");
	$forms[1]['fields'][7] = array('useremail', "Email:", "text", TRUE, "Please provide an email address.<br>\n");
	$forms[1]['fields'][8] = array('userphone1', "Phone Number:", "text", TRUE, "Please provide a phone number.<br>\n");
	$forms[1]['fields'][9] = array('userphone1ext', "Extension:", "text", FALSE, "");

	$forms[2]['header'] = "Billing Address";
	$forms[2]['fields'][1] = array('userbillingaddress1', "Address 1:", "text", TRUE, "Please provide a street address.<br>\n");
	$forms[2]['fields'][2] = array('userbillingaddress2', "Address 2:", "text", FALSE, "");
	$forms[2]['fields'][3] = array('userbillingcity', "City:", "text", TRUE, "Please provide a city.<br>\n");
	$forms[2]['fields'][4] = array('userbillingstateprovince', "State/Province:", "text", TRUE, "Please provide a state or province.<br>\n");
	$forms[2]['fields'][5] = array('userbillingcountry', "Country:", "text", TRUE, "Please provide a country.<br>\n");
	$forms[2]['fields'][6] = array('userbillingpostalcode', "ZIP/Postal Code:", "text", TRUE, "Please provide a postal code.<br>\n");

	$forms[3]['header'] = "Billing Address";
	$forms[3]['fields'][1] = array('usershippingaddress1', "Address 1:", "text", TRUE, "Please provide a street address.<br>\n");
	$forms[3]['fields'][2] = array('usershippingaddress2', "Address 2:", "text", FALSE, "");
	$forms[3]['fields'][3] = array('usershippingcity', "City:", "text", TRUE, "Please provide a city.<br>\n");
	$forms[3]['fields'][4] = array('usershippingstateprovince', "State/Province:", "text", TRUE, "Please provide a state or province.<br>\n");
	$forms[3]['fields'][5] = array('usershippingcountry', "Country:", "text", TRUE, "Please provide a country.<br>\n");
	$forms[3]['fields'][6] = array('usershippingpostalcode', "ZIP/Postal Code:", "text", TRUE, "Please provide a postal code.<br>\n");

?>
