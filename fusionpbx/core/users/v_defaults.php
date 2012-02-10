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

//if the are no groups add the default groups
	$sql = "SELECT * FROM v_groups ";
	$sql .= "WHERE domain_uuid = '$domain_uuid' ";
	$sub_result = $db->query($sql)->fetch();
	$prep_statement = $db->prepare(check_sql($sql));
	if ($prep_statement) {
		$prep_statement->execute();
		$sub_result = $prep_statement->fetchAll(PDO::FETCH_ASSOC);
		if (count($sub_result) == 0) {
			$x = 0;
			$tmp[$x]['group_name'] = 'superadmin';
			$tmp[$x]['group_desc'] = 'Super Administrator Group';
			$x++;
			$tmp[$x]['group_name'] = 'admin';
			$tmp[$x]['group_desc'] = 'Administrator Group';
			$x++;
			$tmp[$x]['group_name'] = 'user';
			$tmp[$x]['group_desc'] = 'User Group';
			$x++;
			$tmp[$x]['group_name'] = 'public';
			$tmp[$x]['group_desc'] = 'Public Group';
			$x++;
			$tmp[$x]['group_name'] = 'agent';
			$tmp[$x]['group_desc'] = 'Call Center Agent Group';
			foreach($tmp as $row) {
				if (strlen($row['group_name']) > 0) {
					$sql = "insert into v_groups ";
					$sql .= "(";
					$sql .= "domain_uuid, ";
					$sql .= "group_name, ";
					$sql .= "group_desc ";
					$sql .= ")";
					$sql .= "values ";
					$sql .= "(";
					$sql .= "'$domain_uuid', ";
					$sql .= "'".$row['group_name']."', ";
					$sql .= "'".$row['group_desc']."' ";
					$sql .= ")";
					$db->exec(check_sql($sql));
					unset($sql);
				}
			}
		}
	}
	unset($prep_statement, $sub_result);

//if there are no permissions listed in v_group_permissions then set the default permissions
	$sql = "";
	$sql .= "select count(*) as count from v_group_permissions ";
	$sql .= "where domain_uuid = '$domain_uuid' ";
	$prep_statement = $db->prepare($sql);
	$prep_statement->execute();
	$sub_result = $prep_statement->fetch(PDO::FETCH_ASSOC);
	unset ($prep_statement);
	if ($sub_result['count'] > 0) {
		if ($display_type == "text") {
			echo "	Group Permissions:	no change\n";
		}
	}
	else {
		if ($display_type == "text") {
			echo "	Group Permissions:	added\n";
		}
		//no permissions found add the defaults
		$db->beginTransaction();
		foreach($apps as $app) {
			foreach ($app['permissions'] as $sub_row) {
				foreach ($sub_row['groups'] as $group) {
					//add the record
					$sql = "insert into v_group_permissions ";
					$sql .= "(";
					$sql .= "domain_uuid, ";
					$sql .= "permission_name, ";
					$sql .= "group_name ";
					$sql .= ")";
					$sql .= "values ";
					$sql .= "(";
					$sql .= "'$domain_uuid', ";
					$sql .= "'".$sub_row['name']."', ";
					$sql .= "'".$group."' ";
					$sql .= ")";
					$db->exec($sql);
					unset($sql);
				}
			}
		}
		$db->commit();
	}

?>