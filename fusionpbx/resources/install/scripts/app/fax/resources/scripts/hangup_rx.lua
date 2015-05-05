--
--	FusionPBX
--	Version: MPL 1.1
--
--	The contents of this file are subject to the Mozilla Public License Version
--	1.1 (the "License"); you may not use this file except in compliance with
--	the License. You may obtain a copy of the License at
--	http://www.mozilla.org/MPL/
--
--	Software distributed under the License is distributed on an "AS IS" basis,
--	WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
--	for the specific language governing rights and limitations under the
--	License.
--
--	The Original Code is FusionPBX
--
--	The Initial Developer of the Original Code is
--	Mark J Crane <markjcrane@fusionpbx.com>
--	Copyright (C) 2015
--	the Initial Developer. All Rights Reserved.
--
--	Contributor(s):
--		Mark J. Crane

--create the api object
	api = freeswitch.API();

--include config.lua
	scripts_dir = string.sub(debug.getinfo(1).source,2,string.len(debug.getinfo(1).source)-(string.len(argv[0])+1));
	dofile(scripts_dir.."/resources/functions/config.lua");
	dofile(config());

--connect to the database
	dofile(scripts_dir.."/resources/functions/database_handle.lua");
	dbh = database_handle('system');

--define the explode function
	function explode ( seperator, str ) 
		local pos, arr = 0, {}
		for st, sp in function() return string.find( str, seperator, pos, true ) end do -- for each divider found
			table.insert( arr, string.sub( str, pos, st-1 ) ) -- attach chars left of current divider
			pos = sp + 1 -- jump past current divider
		end
		table.insert( arr, string.sub( str, pos ) ) -- attach chars right of last divider
		return arr
	end

--array count
	function count(t)
		c = 0;
		for k,v in pairs(t) do
  			c = c+1;
		end
		return c;
	end

--settings
	dofile(scripts_dir.."/resources/functions/settings.lua");
	settings = settings(domain_uuid);
	storage_type = "";
	storage_path = "";
	if (settings['fax'] ~= nil) then
		if (settings['fax']['storage_type'] ~= nil) then
			if (settings['fax']['storage_type']['text'] ~= nil) then
				storage_type = settings['fax']['storage_type']['text'];
			end
		end
		if (settings['fax']['storage_path'] ~= nil) then
			if (settings['fax']['storage_path']['text'] ~= nil) then
				storage_path = settings['fax']['storage_path']['text'];
				storage_path = storage_path:gsub("${domain_name}", domain_name);
				storage_path = storage_path:gsub("${voicemail_id}", voicemail_id);
				storage_path = storage_path:gsub("${voicemail_dir}", voicemail_dir);
			end
		end
	end

-- show all channel variables
	serialized = env:serialize()
	freeswitch.consoleLog("INFO","[fax]\n" .. serialized .. "\n")

-- example channel variables relating to fax
	--variable_fax_success: 0
	--variable_fax_result_code: 49
	--variable_fax_result_text: The%20call%20dropped%20prematurely
	--variable_fax_ecm_used: off
	--variable_fax_local_station_id: SpanDSP%20Fax%20Ident
	--variable_fax_document_transferred_pages: 0
	--variable_fax_document_total_pages: 0
	--variable_fax_image_resolution: 0x0
	--variable_fax_image_size: 0
	--variable_fax_bad_rows: 0
	--variable_fax_transfer_rate: 14400

-- set channel variables to lua variables
	domain_uuid = env:getHeader("domain_uuid");
	domain_name = env:getHeader("domain_name");
	fax_uuid = env:getHeader("fax_uuid");
	uuid = env:getHeader("uuid");
	fax_success = env:getHeader("fax_success");
	fax_result_text = env:getHeader("fax_result_text");
	fax_local_station_id = env:getHeader("fax_local_station_id");
	fax_ecm_used = env:getHeader("fax_ecm_used");
	fax_uri = env:getHeader("fax_uri");
	fax_extension_number = env:getHeader("fax_extension_number");
	caller_id_name = env:getHeader("caller_id_name");
	caller_id_number = env:getHeader("caller_id_number");
	fax_bad_rows = env:getHeader("fax_bad_rows");
	fax_transfer_rate = env:getHeader("fax_transfer_rate");
	sip_to_user = env:getHeader("sip_to_user");
	bridge_hangup_cause = env:getHeader("bridge_hangup_cause");
	fax_result_code = env:getHeader("fax_result_code");
	fax_document_total_pages = env:getHeader("fax_document_total_pages");
	hangup_cause_q850 = tonumber(env:getHeader("hangup_cause_q850"));
	fax_file = env:getHeader("fax_file");

-- prevent nil errors
	if (fax_file == nil) then
		fax_file = env:getHeader("fax_filename");
	end
	if (fax_uri == nil) then
		fax_uri = "";
	end

--set default values
	if (not fax_success) then
		fax_success = "0";
		fax_result_code = 2;
	end
	if (hangup_cause_q850 == "17") then
		fax_success = "0";
		fax_result_code = 2;
	end
	if (not fax_result_text) then
		fax_result_text = "FS_NOT_SET";
	end

--get the fax settings from the database
	sql = [[SELECT * FROM v_fax 
		WHERE fax_uuid = ']] .. fax_uuid ..[[' 
		AND domain_uuid = ']] .. domain_uuid ..[[']];
	if (debug["sql"]) then
		freeswitch.consoleLog("notice", "[fax] SQL: " .. sql .. "\n");
	end
	status = dbh:query(sql, function(row)
		dialplan_uuid = row["dialplan_uuid"];
		fax_extension = row["fax_extension"];
		fax_accountcode = row["accountcode"];
		fax_destination_number = row["fax_destination_number"];
		fax_name = row["fax_name"];
		fax_email = row["fax_email"];
		fax_email_connection_type = row["fax_email_connection_type"];
		fax_email_connection_host = row["fax_email_connection_host"];
		fax_email_connection_port = row["fax_email_connection_port"];
		fax_email_connection_security = row["fax_email_connection_security"];
		fax_email_connection_validate = row["fax_email_connection_validate"];
		fax_email_connection_username = row["fax_email_connection_username"];
		fax_email_connection_password = row["fax_email_connection_password"];
		fax_email_connection_mailbox = row["fax_email_connection_mailbox"];
		fax_email_inbound_subject_tag = row["fax_email_inbound_subject_tag"];
		fax_email_outbound_subject_tag = row["fax_email_outbound_subject_tag"];
		fax_email_outbound_authorized_senders = row["fax_email_outbound_authorized_senders"];
		fax_caller_id_name = row["fax_caller_id_name"];
		fax_caller_id_number = row["fax_caller_id_number"];
		fax_forward_number = row["fax_forward_number"];
		fax_description = row["fax_description"];
	end);

--get the values from the fax file
	if (fax_file ~= nil) then
		array = explode("/", fax_file);
		fax_file_name = array[count(array)];
	end

--fax to email
	cmd = "'"..php_dir.."/"..php_bin.."' '"..document_root.."/secure/fax_to_email.php' ";
	cmd = cmd .. "email='"..fax_email.."' ";
	cmd = cmd .. "extension="..fax_extension.." ";
	cmd = cmd .. "name='"..fax_file.."' "; 
	cmd = cmd .. "messages='result: "..fax_result_text.."' ";
	cmd = cmd .. "sender: "; --fax_remote_station_id.." "
	cmd = cmd .. "pages:"..fax_document_total_pages.." ";
	cmd = cmd .. "domain="..domain_name.." "
	if (caller_id_name ~= nil) then
		cmd = cmd .. "caller_id_name='"..caller_id_name.."' ";
	end
	if (caller_id_number ~= nil) then
		cmd = cmd .. "caller_id_number="..caller_id_number.." ";
	end
	if (string.len(fax_forward_number) > 0) then
		cmd = cmd .. "fax_relay=true ";
	end
	freeswitch.consoleLog("notice", "[fax] command: " .. cmd .. "\n");
	result = api:execute("system", cmd);

--add to fax logs
	sql = "insert into v_fax_logs ";
	sql = sql .. "(";
	sql = sql .. "fax_log_uuid, ";
	sql = sql .. "domain_uuid, ";
	if (fax_uuid ~= nil) then
		sql = sql .. "fax_uuid, ";
	end
	sql = sql .. "fax_success, ";
	sql = sql .. "fax_result_code, ";
	sql = sql .. "fax_result_text, ";
	sql = sql .. "fax_file, ";
	if (fax_ecm_used ~= nil) then
		sql = sql .. "fax_ecm_used, ";
	end
	if (fax_local_station_id ~= nil) then
		sql = sql .. "fax_local_station_id, ";
	end
	sql = sql .. "fax_document_transferred_pages, ";
	sql = sql .. "fax_document_total_pages, ";
	if (fax_image_resolution ~= nil) then
		sql = sql .. "fax_image_resolution, ";
	end
	if (fax_image_size ~= nil) then
		sql = sql .. "fax_image_size, ";
	end
	if (fax_bad_rows ~= nil) then
		sql = sql .. "fax_bad_rows, ";
	end
	if (fax_transfer_rate ~= nil) then
		sql = sql .. "fax_transfer_rate, ";
	end
	if (fax_uri ~= nil) then
		sql = sql .. "fax_uri, ";
	end
	sql = sql .. "fax_date, ";
	sql = sql .. "fax_epoch ";
	sql = sql .. ") ";
	sql = sql .. "values ";
	sql = sql .. "(";
	sql = sql .. "'"..uuid.."', ";
	sql = sql .. "'"..domain_uuid.."', ";
	if (fax_uuid ~= nil) then
		sql = sql .. "'"..fax_uuid.."', ";
	end
	sql = sql .. "'"..fax_success.."', ";
	sql = sql .. "'"..fax_result_code .."', ";
	sql = sql .. "'"..fax_result_text.."', ";
	sql = sql .. "'"..fax_file.."', ";
	if (fax_ecm_used ~= nil) then
		sql = sql .. "'"..fax_ecm_used.."', ";
	end
	if (fax_local_station_id ~= nil) then
		sql = sql .. "'"..fax_local_station_id.."', ";
	end
	if (fax_document_transferred_pages == nil) then
		sql = sql .. "'0', ";
	else
		sql = sql .. "'"..fax_document_transferred_pages.."', ";
	end
	if (fax_document_total_pages == nil) then
		sql = sql .. "'0', ";
	else
		sql = sql .. "'"..fax_document_total_pages.."', ";
	end
	if (fax_image_resolution ~= nil) then
		sql = sql .. "'"..fax_image_resolution.."', ";
	end
	if (fax_image_size ~= nil) then
		sql = sql .. "'"..fax_image_size.."', ";
	end
	if (fax_bad_rows ~= nil) then
		sql = sql .. "'"..fax_bad_rows.."', ";
	end
	if (fax_transfer_rate ~= nil) then
		sql = sql .. "'"..fax_transfer_rate.."', ";
	end
	if (fax_uri ~= nil) then
		sql = sql .. "'"..fax_uri.."', ";
	end
	if (database["type"] == "sqlite") then
		sql = sql .. "'"..os.date("%Y-%m-%d %X").."', ";
	else
		sql = sql .. "now(), ";
	end
	sql = sql .. "'"..os.time().."' ";
	sql = sql .. ")";
	if (debug["sql"]) then
		freeswitch.consoleLog("notice", "[fax] "..sql.."\n");
	end
	dbh:query(sql);

--add the fax files
	if (fax_success ~= nil) then
		if (fax_success =="1") then
			if (storage_type == "base64") then
				--include the base64 function
					dofile(scripts_dir.."/resources/functions/base64.lua");

				--base64 encode the file
					local f = io.open(fax_file, "rb");
					local file_content = f:read("*all");
					f:close();
					fax_base64 = base64.encode(file_content);
			end

			local sql = {}
			table.insert(sql, "insert into v_fax_files ");
			table.insert(sql, "(");
			table.insert(sql, "fax_file_uuid, ");
			table.insert(sql, "fax_uuid, ");
			table.insert(sql, "fax_mode, ");
			if (sip_to_user ~= nil) then
				table.insert(sql, "fax_destination, ");
			end
			table.insert(sql, "fax_file_type, ");
			table.insert(sql, "fax_file_path, ");
			if (caller_id_name ~= nil) then
				table.insert(sql, "fax_caller_id_name, ");
			end
			if (caller_id_number ~= nil) then
				table.insert(sql, "fax_caller_id_number, ");
			end
			table.insert(sql, "fax_date, ");
			table.insert(sql, "fax_epoch, ");
			if (storage_type == "base64") then
				table.insert(sql, "fax_base64, ");
			end
			table.insert(sql, "domain_uuid");
			table.insert(sql, ") ");
			table.insert(sql, "values ");
			table.insert(sql, "(");
			table.insert(sql, "'" .. uuid .. "', ");
			table.insert(sql, "'" .. fax_uuid .. "', ");
			table.insert(sql, "'rx', ");
			if (sip_to_user ~= nil) then
				table.insert(sql, "'" .. sip_to_user .. "', ");
			end
			table.insert(sql, "'tif', ");
			table.insert(sql, "'" .. fax_file .. "', ");
			if (caller_id_name ~= nil) then
				table.insert(sql, "'" .. caller_id_name .. "', ");
			end
			if (caller_id_number ~= nil) then
				table.insert(sql, "'" .. caller_id_number .. "', ");
			end
			if (database["type"] == "sqlite") then
				table.insert(sql, "'"..os.date("%Y-%m-%d %X").."', ");
			else
				table.insert(sql, "now(), ");
			end
			table.insert(sql, "'" .. os.time() .. "', ");
			if (storage_type == "base64") then
				table.insert(sql, "'" .. fax_base64 .. "', ");
			end
			table.insert(sql, "'" .. domain_uuid .. "'");
			table.insert(sql, ")");
			sql = table.concat(sql, "\n");
			if (debug["sql"]) then
				freeswitch.consoleLog("notice", "[fax] SQL: " .. sql .. "\n");
			end
			if (storage_type == "base64") then
				array = explode("://", database["system"]);
				local luasql = require "luasql.postgres";
				local env = assert (luasql.postgres());
				local dbh = env:connect(array[2]);
				res, serr = dbh:execute(sql);
				dbh:close();
				env:close();
			else
				result = dbh:query(sql);
			end
		end
	end

-- send the selected variables to the console
	if (fax_success ~= nil) then
		freeswitch.consoleLog("INFO","fax_success: '" .. fax_success .. "'\n");
	end
	freeswitch.consoleLog("INFO","domain_uuid: '" .. domain_uuid .. "'\n");
	freeswitch.consoleLog("INFO","domain_name: '" .. domain_name .. "'\n");
	freeswitch.consoleLog("INFO","fax_uuid: '" .. fax_uuid .. "'\n");
	freeswitch.consoleLog("INFO","fax_extension: '" .. fax_extension .. "'\n");
	freeswitch.consoleLog("INFO","fax_result_text: '" .. fax_result_text .. "'\n");
	freeswitch.consoleLog("INFO","fax_file: '" .. fax_file .. "'\n");
	freeswitch.consoleLog("INFO","uuid: '" .. uuid .. "'\n");
	--freeswitch.consoleLog("INFO","fax_ecm_used: '" .. fax_ecm_used .. "'\n");
	freeswitch.consoleLog("INFO","fax_uri: '" .. fax_uri.. "'\n");
	if (caller_id_name ~= nil) then
		freeswitch.consoleLog("INFO","caller_id_name: " .. caller_id_name .. "\n");
	end
	if (caller_id_number ~= nil) then
		freeswitch.consoleLog("INFO","caller_id_number: " .. caller_id_number .. "\n");
	end
	freeswitch.consoleLog("INFO","fax_result_code: ".. fax_result_code .."\n");
	--freeswitch.consoleLog("INFO","mailfrom_address: ".. from_address .."\n");
	--freeswitch.consoleLog("INFO","mailto_address: ".. email_address .."\n");
	freeswitch.consoleLog("INFO","hangup_cause_q850: '" .. hangup_cause_q850 .. "'\n");
