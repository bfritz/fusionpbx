<?php
/*
	FusionPBX
	Version: MPL 1.1

	The contents of this file are subject to the Mozilla Public License Version
	1.1 (the "License"); you may not use this file except in compliance with
	the License. You may obtain a copy of the License at
	http://www.mozilla.org/MPL/

	Software distributed under the License is distributed on an "AS IS" basis,
	WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
	for the specific language governing rights and limitations under the
	License.

	The Original Code is FusionPBX

	The Initial Developer of the Original Code is
	Mark J Crane <markjcrane@fusionpbx.com>
	Portions created by the Initial Developer are Copyright (C) 2008-2012
	the Initial Developer. All Rights Reserved.

	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>
*/
include "root.php";
require_once "includes/config.php";
require_once "includes/checkauth.php";
if (permission_exists('outbound_route_add')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

//show the header
	require_once "includes/header.php";
	require_once "includes/paging.php";

//get the http post values and set theme as php variables
	if (count($_POST)>0) {
		$extension_name = check_str($_POST["extension_name"]);
		$dialplan_order = check_str($_POST["dialplan_order"]);
		$dialplan_expression = check_str($_POST["dialplan_expression"]);
		$prefix_number = check_str($_POST["prefix_number"]);
		$condition_field_1 = check_str($_POST["condition_field_1"]);
		$condition_expression_1 = check_str($_POST["condition_expression_1"]);
		$condition_field_2 = check_str($_POST["condition_field_2"]);
		$condition_expression_2 = check_str($_POST["condition_expression_2"]);
		$gateway = check_str($_POST["gateway"]);

		//set the default type
			$gateway_type = 'gateway';
			$gateway_2_type = 'gateway';
			$gateway_3_type = 'gateway';

		//set the gateway type to enum
			if (strtolower(substr($gateway, 0, 7)) == "enum") {
				$gateway_type = 'enum';
			}
		//set the gateway type to freetdm
			if (strtolower(substr($gateway, 0, 7)) == "freetdm") {
				$gateway_type = 'freetdm';
			}
		//set the gateway type to dingaling
			if (strtolower(substr($gateway, 0, 4)) == "xmpp") {
				$gateway_type = 'xmpp';
			}
		//set the gateway_uuid and gateway_name
			if ($gateway_type == "gateway") {
				$gateway_array = explode(":",$gateway);
				$gateway_uuid = $gateway_array[0];
				$gateway_name = $gateway_array[1];
			}
			else {
				$gateway_name = '';
				$gateway_uuid = '';
			}

		//set the gateway_2 variable
			$gateway_2 = check_str($_POST["gateway_2"]);
		//set the gateway type to enum
			if (strtolower(substr($gateway_2, 0, 4)) == "enum") {
				$gateway_2_type = 'enum';
			}
		//set the gateway type to freetdm
			if (strtolower(substr($gateway_2, 0, 7)) == "freetdm") {
				$gateway_2_type = 'freetdm';
			}
		//set the gateway type to dingaling
			if (strtolower(substr($gateway_2, 0, 4)) == "xmpp") {
				$gateway_2_type = 'xmpp';
			}
		//set the gateway_2_id and gateway_2_name
			if ($gateway_2_type == "gateway" && strlen($_POST["gateway_2"]) > 0) {
				$gateway_2_array = explode(":",$gateway_2);
				$gateway_2_id = $gateway_2_array[0];
				$gateway_2_name = $gateway_2_array[1];
			}
			else {
				$gateway_2_id = '';
				$gateway_2_name = '';
			}

		//set the gateway_3 variable
			$gateway_3 = check_str($_POST["gateway_3"]);
		//set the gateway type to enum
			if (strtolower(substr($gateway_3, 0, 4)) == "enum") {
				$gateway_3_type = 'enum';
			}
		//set the gateway type to freetdm
			if (strtolower(substr($gateway_3, 0, 7)) == "freetdm") {
				$gateway_3_type = 'freetdm';
			}
		//set the gateway type to dingaling
			if (strtolower(substr($gateway_3, 0, 4)) == "xmpp") {
				$gateway_3_type = 'xmpp';
			}
		//set the gateway_3_id and gateway_3_name
			if ($gateway_3_type == "gateway" && strlen($_POST["gateway_3"]) > 0) {
				$gateway_3_array = explode(":",$gateway_3);
				$gateway_3_id = $gateway_3_array[0];
				$gateway_3_name = $gateway_3_array[1];
			}
			else {
				$gateway_3_id = '';
				$gateway_3_name = '';
			}

		if (permission_exists('outbound_route_any_gateway')) {
			//get the domain_uuid for gateway
				$sql = "";
				$sql .= "select * from v_gateways ";
				$sql .= "where gateway_uuid = '$gateway_uuid' ";
				$sql .= "and gateway = '$gateway_name' ";
				$prep_statement = $db->prepare(check_sql($sql));
				$prep_statement->execute();
				$result = $prep_statement->fetchAll();
				foreach ($result as &$row) {
					$gateway_domain_uuid = $row["domain_uuid"];
					break;
				}
				unset ($prep_statement);
			//get the domain_uuid for gateway_2
				$sql = "";
				$sql .= "select * from v_gateways ";
				$sql .= "where gateway_uuid = '$gateway_2_id' ";
				$sql .= "and gateway = '$gateway_2_name' ";
				$prep_statement = $db->prepare(check_sql($sql));
				$prep_statement->execute();
				$result = $prep_statement->fetchAll();
				foreach ($result as &$row) {
					$gateway_2_domain_uuid = $row["domain_uuid"];
					break;
				}
				unset ($prep_statement);
			//get the domain_uuid for gateway_3
				$sql = "";
				$sql .= "select * from v_gateways ";
				$sql .= "where gateway_uuid = '$gateway_3_id' ";
				$sql .= "and gateway = '$gateway_3_name' ";
				$prep_statement = $db->prepare(check_sql($sql));
				$prep_statement->execute();
				$result = $prep_statement->fetchAll();
				foreach ($result as &$row) {
					$gateway_3_domain_uuid = $row["domain_uuid"];
					break;
				}
				unset ($prep_statement);
		}

		$enabled = check_str($_POST["enabled"]);
		$description = check_str($_POST["description"]);
		if (strlen($enabled) == 0) { $enabled = "true"; } //set default to enabled
	}

//process the http form values
	if (count($_POST)>0 && strlen($_POST["persistformvar"]) == 0) {
		//check for all required data
			if (strlen($domain_uuid) == 0) { $msg .= "Please provide: domain_uuid<br>\n"; }
			if (strlen($gateway) == 0) { $msg .= "Please provide: Gateway Name<br>\n"; }
			//if (strlen($gateway_2) == 0) { $msg .= "Please provide: Alternat 1<br>\n"; }
			//if (strlen($gateway_3) == 0) { $msg .= "Please provide: Alternat 2<br>\n"; }
			if (strlen($dialplan_expression) == 0) { $msg .= "Please provide: Dialplan Expression<br>\n"; }
			//if (strlen($extension_name) == 0) { $msg .= "Please provide: Extension Name<br>\n"; }
			//if (strlen($condition_field_1) == 0) { $msg .= "Please provide: Condition Field<br>\n"; }
			//if (strlen($condition_expression_1) == 0) { $msg .= "Please provide: Condition Expression<br>\n"; }
			//if (strlen($enabled) == 0) { $msg .= "Please provide: Enabled True or False<br>\n"; }
			//if (strlen($description) == 0) { $msg .= "Please provide: Description<br>\n"; }
			if (strlen($msg) > 0 && strlen($_POST["persistformvar"]) == 0) {
				require_once "includes/header.php";
				require_once "includes/persistformvar.php";
				echo "<div align='center'>\n";
				echo "<table><tr><td>\n";
				echo $msg."<br />";
				echo "</td></tr></table>\n";
				persistformvar($_POST);
				echo "</div>\n";
				require_once "includes/footer.php";
				return;
			}

		//start the atomic transaction
			$count = $db->exec("BEGIN;"); //returns affected rows

		if (strlen(trim($_POST['dialplan_expression']))> 0) {

			$tmp_array = explode("\n", $_POST['dialplan_expression']);

			foreach($tmp_array as $dialplan_expression) {
				$dialplan_expression = trim($dialplan_expression);
				if (strlen($dialplan_expression)>0) {
					if (count($_SESSION["domains"]) > 1) {
						if (permission_exists('outbound_route_any_gateway')) {
							$tmp_gateway_name = $_SESSION['domains'][$gateway_domain_uuid]['domain'] .'-'.$gateway_name;
						}
						else {
							$tmp_gateway_name = $_SESSION['domains'][$domain_uuid]['domain'] .'-'.$gateway_name;
						}
						if (strlen($gateway_2_name) > 0) {
							if (permission_exists('outbound_route_any_gateway')) {
								$tmp_gateway_2_name = $_SESSION['domains'][$gateway_2_domain_uuid]['domain'] .'-'.$gateway_2_name;
							}
							else {
								$tmp_gateway_2_name = $_SESSION['domains'][$domain_uuid]['domain'] .'-'.$gateway_2_name;
							}
						}
						if (strlen($gateway_3_name) > 0) {
							if (permission_exists('outbound_route_any_gateway')) {
								$tmp_gateway_3_name = $_SESSION['domains'][$gateway_3_domain_uuid]['domain'] .'-'.$gateway_3_name;
							}
							else {
								$tmp_gateway_3_name = $_SESSION['domains'][$domain_uuid]['domain'] .'-'.$gateway_3_name;
							}
						}
					}
					else {
						$tmp_gateway_name = $gateway_name;
						if (strlen($gateway_2_name) > 0) {
							$tmp_gateway_2_name = $gateway_2_name;
						}
						if (strlen($gateway_3_name) > 0) {
							$tmp_gateway_3_name = $gateway_3_name;
						}
					}
					switch ($dialplan_expression) {
					case "^(\d{7})$":
						$label = "7 digits";
						$abbrv = "7d";
						break;
					case "^(\d{8})$":
						$label = "8 digits";
						$abbrv = "8d";
						break;
					case "^(\d{9})$":
						$label = "9 digits";
						$abbrv = "9d";
						break;
					case "^(\d{10})$":
						$label = "10 digits";
						$abbrv = "10d";
						break;
					case "^\+?(\d{11})$":
						$label = "11 digits";
						$abbrv = "11d";
						break;
					case "^(\d{12})$":
						$label = "12 digits";
						$abbrv = "12d";
						break;
					case "^(\d{13})$":
						$label = "13 digits";
						$abbrv = "13d";
						break;
					case "^(\d{14})$":
						$label = "14 digits";
						$abbrv = "14d";
						break;
					case "^(\d{12,15})$":
						$label = "International";
						$abbrv = "Intl";
						break;
					case "^(311)$":
						$label = "311";
						$abbrv = "311";
						break;
					case "^(411)$":
						$label = "411";
						$abbrv = "411";
						break;
					case "^(911)$":
						$label = "911";
						$abbrv = "911";
						break;
					case "^9(\d{3})$":
						$label = "dial 9, 3 digits";
						$abbrv = "9.3d";
						break;
					case "^9(\d{4})$":
						$label = "dial 9, 4 digits";
						$abbrv = "9.4d";
						break;	
					case "^9(\d{7})$":
						$label = "dial 9, 7 digits";
						$abbrv = "9.7d";
						break;
					case "^9(\d{10})$":
						$label = "dial 9, 10 digits";
						$abbrv = "9.10d";
						break;
					case "^9(\d{11})$":
						$label = "dial 9, 11 digits";
						$abbrv = "9.11d";
						break;
					case "^9(\d{12})$":
						$label = "dial 9, 12 digits";
						$abbrv = "9.Intl";
						break;
					case "^9(\d{13})$":
						$label = "dial 9, 13 digits";
						$abbrv = "9.13d";
						break;
					case "^9(\d{14})$":
						$label = "dial 9, 14 digits";
						break;
					case "^9(\d{12,15})$":
						$label = "dial 9, International";
						$abbrv = "9.Intl";
						break;
					case "^1?(8(00|55|66|77|88)[2-9]\d{6})$":
						$label = "toll free";
						$abbrv = "tollfree";
						break;
					default:
						$label = $dialplan_expression;
						$abbrv = filename_safe($dialplan_expression);
					}

					if ($gateway_type == "gateway") {
						$extension_name = $gateway_name.".".$abbrv;
						$action_data = "sofia/gateway/".$tmp_gateway_name."/".$prefix_number."\$1";
					}
					if (strlen($gateway_2_name) > 0 && $gateway_2_type == "gateway") {
						$extension_2_name = $gateway_2_name.".".$abbrv;
						$bridge_2_data .= "sofia/gateway/".$tmp_gateway_2_name."/".$prefix_number."\$1";
					}
					if (strlen($gateway_3_name) > 0 && $gateway_3_type == "gateway") {
						$extension_3_name = $gateway_3_name.".".$abbrv;
						$bridge_3_data .= "sofia/gateway/".$tmp_gateway_3_name."/".$prefix_number."\$1";
					}
					if ($gateway_type == "freetdm") {
						$extension_name = "freetdm.".$abbrv;
						$action_data = $gateway."/1/a/".$prefix_number."\$1";
					}
					if ($gateway_2_type == "freetdm") {
						$extension_2_name = "freetdm.".$abbrv;
						$bridge_2_data .= $gateway_2."/1/a/".$prefix_number."\$1";
					}
					if ($gateway_3_type == "freetdm") {
						$extension_3_name = "freetdm.".$abbrv;
						$bridge_3_data .= $gateway_3."/1/a/".$prefix_number."\$1";
					}
					if ($gateway_type == "xmpp") {
						$extension_name = "xmpp.".$abbrv;
						$action_data = "dingaling/gtalk/+".$prefix_number."\$1@voice.google.com";
					}
					if ($gateway_2_type == "xmpp") {
						$extension_2_name = "xmpp.".$abbrv;
						$bridge_2_data .= "dingaling/gtalk/+".$prefix_number."\$1@voice.google.com";
					}
					if ($gateway_3_type == "xmpp") {
						$extension_3_name = "xmpp.".$abbrv;
						$bridge_3_data .= "dingaling/gtalk/+".$prefix_number."\$1@voice.google.com";
					}
					if ($gateway_type == "enum") {
						if (strlen($bridge_2_data) == 0) {
							$extension_name = "enum.".$abbrv;
						}
						else {
							$extension_name = $extension_2_name;
						}
						$action_data = "\${enum_auto_route}";
					}
					if ($gateway_2_type == "enum") {
						$bridge_2_data .= "\${enum_auto_route}";
					}
					if ($gateway_3_type == "enum") {
						$bridge_3_data .= "\${enum_auto_route}";
					}
					if (strlen($dialplan_order) == 0) {
						$dialplan_order ='999';
					}
					$context = 'default';
					$opt_1_name = 'gateway_uuid';
					$opt_1_value = $gateway_uuid;
					$extension_continue = 'false';
					//$dialplan_uuid = v_dialplan_add($domain_uuid, $extension_name, $dialplan_order, $context, $enabled, $description, $opt_1_name, $opt_1_value);

					//add the main dialplan include entry
						$dialplan_uuid = uuid();
						$sql = "insert into v_dialplans ";
						$sql .= "(";
						$sql .= "domain_uuid, ";
						$sql .= "dialplan_uuid, ";
						$sql .= "extension_name, ";
						$sql .= "dialplan_order, ";
						$sql .= "extension_continue, ";
						$sql .= "context, ";
						$sql .= "opt_1_name, ";
						$sql .= "opt_1_value, ";
						$sql .= "enabled, ";
						$sql .= "descr ";
						$sql .= ") ";
						$sql .= "values ";
						$sql .= "(";
						$sql .= "'$domain_uuid', ";
						$sql .= "'$dialplan_uuid', ";
						$sql .= "'$extension_name', ";
						$sql .= "'$dialplan_order', ";
						$sql .= "'$extension_continue', ";
						$sql .= "'$context', ";
						$sql .= "'$opt_1_name', ";
						$sql .= "'$opt_1_value', ";
						$sql .= "'$enabled', ";
						$sql .= "'$description' ";
						$sql .= ")";
						if ($v_debug) {
							echo $sql."<br />";
						}
						$db->exec(check_sql($sql));
						unset($sql);

					$tag = 'condition'; //condition, action, antiaction
					$field_type = 'destination_number';
					$field_data = $dialplan_expression;
					$field_order = '005';
					v_dialplan_details_add($domain_uuid, $dialplan_uuid, $tag, $field_order, $field_type, $field_data);

					$tag = 'action'; //condition, action, antiaction
					$field_type = 'set';
					$field_data = 'sip_h_X-accountcode=${accountcode}';
					$field_order = '010';
					v_dialplan_details_add($domain_uuid, $dialplan_uuid, $tag, $field_order, $field_type, $field_data);

					$tag = 'action'; //condition, action, antiaction
					$field_type = 'set';
					$field_data = 'call_direction=outbound';
					$field_order = '015';
					v_dialplan_details_add($domain_uuid, $dialplan_uuid, $tag, $field_order, $field_type, $field_data);

					$tag = 'action'; //condition, action, antiaction
					$field_type = 'set';
					$field_data = 'hangup_after_bridge=true';
					$field_order = '020';
					v_dialplan_details_add($domain_uuid, $dialplan_uuid, $tag, $field_order, $field_type, $field_data);

					$tag = 'action'; //condition, action, antiaction
					$field_type = 'set';
					$field_data = 'effective_caller_id_name=${outbound_caller_id_name}';
					$field_order = '025';
					v_dialplan_details_add($domain_uuid, $dialplan_uuid, $tag, $field_order, $field_type, $field_data);

					$tag = 'action'; //condition, action, antiaction
					$field_type = 'set';
					$field_data = 'effective_caller_id_number=${outbound_caller_id_number}';
					$field_order = '030';
					v_dialplan_details_add($domain_uuid, $dialplan_uuid, $tag, $field_order, $field_type, $field_data);

					$tag = 'action'; //condition, action, antiaction
					$field_type = 'set';
					$field_data = 'inherit_codec=true';
					$field_order = '035';
					v_dialplan_details_add($domain_uuid, $dialplan_uuid, $tag, $field_order, $field_type, $field_data);

					if (strlen($bridge_2_data) > 0) {
						$tag = 'action'; //condition, action, antiaction
						$field_type = 'set';
						$field_data = 'continue_on_fail=true';
						$field_order = '040';
						v_dialplan_details_add($domain_uuid, $dialplan_uuid, $tag, $field_order, $field_type, $field_data);
					}

					if ($gateway_type == "enum" || $gateway_2_type == "enum") {
						$tag = 'action'; //condition, action, antiaction
						$field_type = 'enum';
						$field_data = $prefix_number."$1 e164.org";
						$field_order = '045';
						v_dialplan_details_add($domain_uuid, $dialplan_uuid, $tag, $field_order, $field_type, $field_data);
					}

					$tag = 'action'; //condition, action, antiaction
					$field_type = 'bridge';
					$field_data = $action_data;
					$field_order = '050';
					v_dialplan_details_add($domain_uuid, $dialplan_uuid, $tag, $field_order, $field_type, $field_data);

					if (strlen($bridge_2_data) > 0) {
						$tag = 'action'; //condition, action, antiaction
						$field_type = 'bridge';
						$field_data = $bridge_2_data;
						$field_order = '055';
						v_dialplan_details_add($domain_uuid, $dialplan_uuid, $tag, $field_order, $field_type, $field_data);
					}

					if (strlen($bridge_3_data) > 0) {
						$tag = 'action'; //condition, action, antiaction
						$field_type = 'bridge';
						$field_data = $bridge_3_data;
						$field_order = '060';
						v_dialplan_details_add($domain_uuid, $dialplan_uuid, $tag, $field_order, $field_type, $field_data);
					}

					unset($bridge_2_data);
					unset($bridge_3_data);
					unset($label);
					unset($abbrv);
					unset($dialplan_expression);
					unset($action_data);
				} //if strlen
			} //end for each

			//synchronize the xml config
				sync_package_v_dialplan();
			
			//changes in the dialplan may affect routes in the hunt groups
				sync_package_v_hunt_group();
		}

		//commit the atomic transaction
			$count = $db->exec("COMMIT;"); //returns affected rows

		//synchronize the xml config
			sync_package_v_dialplan();

		//redirect the user
			require_once "includes/header.php";
			echo "<meta http-equiv=\"refresh\" content=\"2;url=v_dialplan_outbound.php\">\n";
			echo "<div align='center'>\n";
			echo "Update Complete\n";
			echo "</div>\n";
			require_once "includes/footer.php";
			return;
	} //end if (count($_POST)>0 && strlen($_POST["persistformvar"]) == 0)
?>

<script type="text/javascript">
<!--
function type_onchange(field_type) {
	var field_value = document.getElementById(field_type).value;

	if (field_type == "condition_field_1") {
		if (field_value == "destination_number") {
			document.getElementById("desc_condition_expression_1").innerHTML = "expression: ^12081231234$";
		}
		else if (field_value == "zzz") {
			document.getElementById("desc_condition_expression_1").innerHTML = "";
		}
		else {
			document.getElementById("desc_condition_expression_1").innerHTML = "";
		}
	}
	if (field_type == "condition_field_2") {
		if (field_value == "destination_number") {
			document.getElementById("desc_condition_expression_2").innerHTML = "expression: ^12081231234$";
		}
		else if (field_value == "zzz") {
			document.getElementById("desc_condition_expression_2").innerHTML = "";
		}
		else {
			document.getElementById("desc_condition_expression_2").innerHTML = "";
		}
	}
-->
</script>

<?php
//show the content
	echo "<div align='center'>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='2'>\n";

	echo "<tr class='border'>\n";
	echo "	<td align=\"left\">\n";
	echo "		<br>";

	echo "<form method='post' name='frm' action=''>\n";
	echo "<div align='center'>\n";

	echo " 	<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";
	echo "	<tr>\n";
	echo "		<td align='left'><span class=\"vexpl\"><span class=\"red\"><strong>Outbound Routes\n";
	echo "			</strong></span></span>\n";
	echo "		</td>\n";
	echo "		<td align='right'>\n";
	echo "			<input type='button' class='btn' name='' alt='back' onclick=\"window.location='v_dialplan_outbound.php'\" value='Back'>\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td align='left' colspan='2'>\n";
	echo "			<span class=\"vexpl\">\n";
	echo "				Outbound dialplans have one or more conditions that are matched to attributes of a call. \n";
	echo "				When a call matches the conditions the call is then routed to the gateway.\n";
	echo "			</span>\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "	</table>";

	echo "<br />\n";
	echo "<br />\n";

	echo "<table width='100%'  border='0' cellpadding='6' cellspacing='0'>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Gateway:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	
	if (ifgroup("superadmin")) {
		echo "<script>\n";
		echo "var Objs;\n";
		echo "\n";
		echo "function changeToInput(obj){\n";
		echo "	tb=document.createElement('INPUT');\n";
		echo "	tb.type='text';\n";
		echo "	tb.name=obj.name;\n";
		echo "	tb.setAttribute('class', 'formfld');\n";
		echo "	tb.value=obj.options[obj.selectedIndex].value;\n";
		echo "	tbb=document.createElement('INPUT');\n";
		echo "	tbb.setAttribute('class', 'btn');\n";
		echo "	tbb.type='button';\n";
		echo "	tbb.value='<';\n";
		echo "	tbb.objs=[obj,tb,tbb];\n";
		echo "	tbb.onclick=function(){ Replace(this.objs); }\n";
		echo "	obj.parentNode.insertBefore(tb,obj);\n";
		echo "	obj.parentNode.insertBefore(tbb,obj);\n";
		echo "	obj.parentNode.removeChild(obj);\n";
		echo "}\n";
		echo "\n";
		echo "function Replace(obj){\n";
		echo "	obj[2].parentNode.insertBefore(obj[0],obj[2]);\n";
		echo "	obj[0].parentNode.removeChild(obj[1]);\n";
		echo "	obj[0].parentNode.removeChild(obj[2]);\n";
		echo "}\n";
		echo "</script>\n";
		echo "\n";
	}

	//set the onchange
	if (ifgroup("superadmin")) { $onchange = "onchange='changeToInput(this);'"; } else { $onchange = ''; }

	$sql = "";
	$sql .= " select * from v_gateways ";
	if (permission_exists('outbound_route_any_gateway')) {
		$sql .= " order by domain_uuid = '$domain_uuid' ";
	}
	else {
		$sql .= " where domain_uuid = '$domain_uuid' ";
	}
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$result = $prep_statement->fetchAll();
	unset ($prep_statement, $sql);
	echo "<select name=\"gateway\" id=\"gateway\" class=\"formfld\" $onchange style='width: 60%;'>\n";
	echo "<option value=''></option>\n";
	echo "<optgroup label='SIP Gateways'>";
	$previous_domain_uuid = '';
	foreach($result as $row) {
		if (permission_exists('outbound_route_any_gateway')) {
			if ($previous_domain_uuid != $row['domain_uuid']) {
				echo "</optgroup>";
				echo "<optgroup label='&nbsp; &nbsp;".$_SESSION['domains'][$row['domain_uuid']]['domain']."'>";
			}
			if ($row['gateway'] == $gateway_name) {
				echo "<option value=\"".$row['gateway_uuid'].":".$row['gateway']."\" selected=\"selected\">&nbsp; &nbsp;".$row['gateway']."</option>\n";
			}
			else {
				echo "<option value=\"".$row['gateway_uuid'].":".$row['gateway']."\">&nbsp; &nbsp;".$row['gateway']."</option>\n";
			}
		}
		else {
			if ($row['gateway'] == $gateway_name) {
				echo "<option value=\"".$row['gateway_uuid'].":".$row['gateway']."\" $onchange selected=\"selected\">".$row['gateway']."</option>\n";
			}
			else {
				echo "<option value=\"".$row['gateway_uuid'].":".$row['gateway']."\">".$row['gateway']."</option>\n";
			}
		}
		$previous_domain_uuid = $row['domain_uuid'];
	}
	unset($sql, $result, $row_count);
	echo "</optgroup>";
	echo "	<optgroup label='Additional Options'>";
	echo "	<option value=\"enum\">enum</option>\n";
	echo "	<option value=\"freetdm\">freetdm</option>\n";
	echo "	<option value=\"xmpp\">xmpp</option>\n";
	echo "</optgroup>";
	echo "</select>\n";
	echo "<br />\n";
	echo "Select the gateway to use with this outbound route.\n";
	echo "</td>\n";
	echo "</tr>\n";


	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "    Alternate 1:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	$sql = "";
	$sql .= " select * from v_gateways ";
	if (permission_exists('outbound_route_any_gateway')) {
		$sql .= " order by domain_uuid = '$domain_uuid' ";
	}
	else {
		$sql .= " where domain_uuid = '$domain_uuid' ";
	}
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$result = $prep_statement->fetchAll();
	unset ($prep_statement, $sql);
	echo "<select name=\"gateway_2\" id=\"gateway\" class=\"formfld\" $onchange style='width: 60%;'>\n";
	echo "<option value=''></option>\n";
	echo "<optgroup label='SIP Gateways'>";
	$previous_domain_uuid = '';
	foreach($result as $row) {
		if (permission_exists('outbound_route_any_gateway')) {
			if ($previous_domain_uuid != $row['domain_uuid']) {
				echo "</optgroup>";
				echo "<optgroup label='&nbsp; &nbsp;".$_SESSION['domains'][$row['domain_uuid']]['domain']."'>";
			}
			if ($row['gateway'] == $gateway_2_name) {
				echo "<option value=\"".$row['gateway_uuid'].":".$row['gateway']."\" selected=\"selected\">&nbsp; &nbsp;".$row['gateway']."</option>\n";
			}
			else {
				echo "<option value=\"".$row['gateway_uuid'].":".$row['gateway']."\">&nbsp; &nbsp;".$row['gateway']."</option>\n";
			}
		}
		else {
			if ($row['gateway'] == $gateway_2_name) {
				echo "<option value=\"".$row['gateway_uuid'].":".$row['gateway']."\" selected=\"selected\">".$row['gateway']."</option>\n";
			}
			else {
				echo "<option value=\"".$row['gateway_uuid'].":".$row['gateway']."\">".$row['gateway']."</option>\n";
			}
		}
		$previous_domain_uuid = $row['domain_uuid'];
	}
	unset($sql, $result, $row_count, $previous_domain_uuid);
	echo "</optgroup>";
	echo "<optgroup label='Additional Options'>";
	echo "	<option value=\"enum\">enum</option>\n";
	echo "	<option value=\"freetdm\">freetdm</option>\n";
	echo "	<option value=\"xmpp\">xmpp</option>\n";
	echo "</optgroup>";
	echo "</select>\n";
	echo "<br />\n";
	echo "Select another gateway as an alternative to use if the first one fails.\n";
	echo "</td>\n";
	echo "</tr>\n";


	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "    Alternate 2:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	$sql = "";
	$sql .= " select * from v_gateways ";
	if (permission_exists('outbound_route_any_gateway')) {
		$sql .= " order by domain_uuid = '$domain_uuid' ";
	}
	else {
		$sql .= " where domain_uuid = '$domain_uuid' ";
	}
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$result = $prep_statement->fetchAll();
	unset ($prep_statement, $sql);
	echo "<select name=\"gateway_3\" id=\"gateway\" class=\"formfld\" $onchange style='width: 60%;'>\n";
	echo "<option value=''></option>\n";
	echo "<optgroup label='SIP Gateways'>";
	$previous_domain_uuid = '';
	foreach($result as $row) {
		if (permission_exists('outbound_route_any_gateway')) {
			if ($previous_domain_uuid != $row['domain_uuid']) {
				echo "</optgroup>";
				echo "<optgroup label='&nbsp; &nbsp;".$_SESSION['domains'][$row['domain_uuid']]['domain']."'>";
			}
			if ($row['gateway'] == $gateway_3_name) {
				echo "<option value=\"".$row['gateway_uuid'].":".$row['gateway']."\" selected=\"selected\">&nbsp; &nbsp;".$row['gateway']."</option>\n";
			}
			else {
				echo "<option value=\"".$row['gateway_uuid'].":".$row['gateway']."\">&nbsp; &nbsp;".$row['gateway']."</option>\n";
			}
		}
		else {
			if ($row['gateway'] == $gateway_3_name) {
				echo "<option value=\"".$row['gateway_uuid'].":".$row['gateway']."\" selected=\"selected\">".$row['gateway']."</option>\n";
			}
			else {
				echo "<option value=\"".$row['gateway_uuid'].":".$row['gateway']."\">".$row['gateway']."</option>\n";
			}
		}
		$previous_domain_uuid = $row['domain_uuid'];
	}
	unset($sql, $result, $row_count, $previous_domain_uuid);
	echo "</optgroup>";
	echo "<optgroup label='Additional Options'>";
	echo "	<option value=\"enum\">enum</option>\n";
	echo "	<option value=\"freetdm\">freetdm</option>\n";
	echo "	<option value=\"xmpp\">xmpp</option>\n";
	echo "</optgroup>";
	echo "</select>\n";
	echo "<br />\n";
	echo "Select another gateway as an alternative to use if the second one fails.\n";
	echo "</td>\n";
	echo "</tr>\n";


	echo "<tr>\n";
	echo "  <td valign=\"top\" class=\"vncellreq\">Dialplan Expression:</td>\n";
	echo "  <td align='left' class=\"vtable\">";
	echo "    <textarea name=\"dialplan_expression\" id=\"dialplan_expression\" class=\"formfld\" style='width: 60%;' cols=\"30\" rows=\"4\" wrap=\"off\"></textarea>\n";
	echo "    <br>\n";
	echo "    <select name='dialplan_expression_select' id='dialplan_expression_select' onchange=\"document.getElementById('dialplan_expression').value += document.getElementById('dialplan_expression_select').value + '\\n';\" class='formfld' style='width: 60%;'>\n";
	echo "    <option></option>\n";
	echo "    <option value='^(\\d{2})\$'>2 digits</option>\n";
	echo "    <option value='^(\\d{3})\$'>3 digits</option>\n";
	echo "    <option value='^(\\d{4})\$'>4 digits</option>\n";
	echo "    <option value='^(\\d{5})\$'>5 digits</option>\n";
	echo "    <option value='^(\\d{6})\$'>6 digits</option>\n";
	echo "    <option value='^(\\d{7})\$'>7 digits local</option>\n";
	echo "    <option value='^(\\d{8})\$'>8 digits</option>\n";
	echo "    <option value='^(\\d{9})\$'>9 digits</option>\n";
	echo "    <option value='^(\\d{10})\$'>10 digits long distance</option>\n";
	echo "    <option value='^\+?(\\d{11})\$'>11 digits long distance</option>\n";
	echo "    <option value='^(\\d{12})\$'>12 digits</option>\n";
	echo "    <option value='^(\\d{13})\$'>13 digits</option>\n";
	echo "    <option value='^(\\d{14})\$'>14 digits</option>\n";
	echo "    <option value='^(\\d{15})\$'>15 digits International</option>\n";
	echo "    <option value='^311\$'>311 information</option>\n";
	echo "    <option value='^411\$'>411 information</option>\n";
	echo "    <option value='^911\$'>911 emergency</option>\n";
	echo "    <option value='^1?(8(00|55|66|77|88)[2-9]\\d{6})\$'>toll free</option>\n";
	echo "    <option value='^9(\\d{2})\$'>Dial 9 then 2 digits</option>\n";
	echo "    <option value='^9(\\d{3})\$'>Dial 9 then 3 digits</option>\n";
	echo "    <option value='^9(\\d{4})\$'>Dial 9 then 4 digits</option>\n";
	echo "    <option value='^9(\\d{5})\$'>Dial 9 then 5 digits</option>\n";
	echo "    <option value='^9(\\d{6})\$'>Dial 9 then 6 digits</option>\n";
	echo "    <option value='^9(\\d{7})\$'>Dial 9 then 7 digits</option>\n";
	echo "    <option value='^9(\\d{8})\$'>Dial 9 then 8 digits</option>\n";
	echo "    <option value='^9(\\d{9})\$'>Dial 9 then 9 digits</option>\n";
	echo "    <option value='^9(\\d{10})\$'>Dial 9 then 10 digits</option>\n";
	echo "    <option value='^9(\\d{11})\$'>Dial 9 then 11 digits</option>\n";
	echo "    <option value='^9(\\d{12})\$'>Dial 9 then 12 digits</option>\n";
	echo "    <option value='^9(\\d{13})\$'>Dial 9 then 13 digits</option>\n";
	echo "    <option value='^9(\\d{14})\$'>Dial 9 then 14 digits</option>\n";
	echo "    <option value='^9(\\d{15})\$'>Dial 9 then 15 digits</option>\n";
	echo "    </select>\n";
	echo "    <span class=\"vexpl\">\n";
	echo "    <br />\n";
	echo "    Shortcut to create the outbound dialplan entries for this Gateway. \n";
	echo "    </span></td>\n";
	echo "</tr>";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "    Prefix:\n";
	echo "</td>\n";
	echo "<td colspan='4' class='vtable' align='left'>\n";
	echo "    <input class='formfld' style='width: 60%;' type='text' name='prefix_number' maxlength='255' value=\"$prefix_number\">\n";
	echo "<br />\n";
	echo "Enter a prefix number to add to the beginning of the destination number.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Order:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "              <select name='dialplan_order' class='formfld' style='width: 60%;'>\n";
	//echo "              <option></option>\n";
	if (strlen(htmlspecialchars($dialplan_order))> 0) {
		echo "              <option selected='yes' value='".htmlspecialchars($dialplan_order)."'>".htmlspecialchars($dialplan_order)."</option>\n";
	}
	$i=0;
	while($i<=999) {
		if (strlen($i) == 1) { echo "              <option value='00$i'>00$i</option>\n"; }
		if (strlen($i) == 2) { echo "              <option value='0$i'>0$i</option>\n"; }
		if (strlen($i) == 3) { echo "              <option value='$i'>$i</option>\n"; }
		$i++;
	}
	echo "              </select>\n";
	echo "<br />\n";
	echo "Select the order number. The order number determines the order of the outbound routes when there is more than one.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Enabled:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <select class='formfld' name='enabled' style='width: 60%;'>\n";
	//echo "    <option value=''></option>\n";
	if ($enabled == "true") { 
		echo "    <option value='true' selected='selected'>true</option>\n";
	}
	else {
		echo "    <option value='true'>true</option>\n";
	}
	if ($enabled == "false") { 
		echo "    <option value='false' selected='selected'>false</option>\n";
	}
	else {
		echo "    <option value='false'>false</option>\n";
	}
	echo "    </select>\n";
	echo "<br />\n";
	echo "Choose to enable or disable the outbound route.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "    Description:\n";
	echo "</td>\n";
	echo "<td colspan='4' class='vtable' align='left'>\n";
	echo "    <input class='formfld' style='width: 60%;' type='text' name='description' maxlength='255' value=\"$description\">\n";
	echo "<br />\n";
	echo "Enter a description for the outbound route.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "	<td colspan='5' align='right'>\n";
	if ($action == "update") {
		echo "		<input type='hidden' name='dialplan_uuid' value='$dialplan_uuid'>\n";
	}
	echo "		<input type='submit' name='submit' class='btn' value='Save'>\n";
	echo "	</td>\n";
	echo "</tr>";

	echo "</table>";
	echo "</div>";
	echo "</form>";

	echo "</td>\n";
	echo "</tr>";
	echo "</table>";
	echo "</div>";

	echo "<br><br>";

//show the footer
	require_once "includes/footer.php";
?>
