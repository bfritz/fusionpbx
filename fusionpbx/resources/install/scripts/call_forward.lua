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
--	Copyright (C) 2010-2014
--	the Initial Developer. All Rights Reserved.
--
--	Contributor(s):
--	Mark J Crane <markjcrane@fusionpbx.com>

--set default variables
	min_digits = "1";
	max_digits = "11";
	max_tries = "3";
	digit_timeout = "3000";

--debug
	debug["sql"] = false;

--define the trim function
	function trim (s)
		return (string.gsub(s, "^%s*(.-)%s*$", "%1"))
	end

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

--create the api object
	api = freeswitch.API();

--include config.lua
	scripts_dir = string.sub(debug.getinfo(1).source,2,string.len(debug.getinfo(1).source)-(string.len(argv[0])+1));
	dofile(scripts_dir.."/resources/functions/config.lua");
	dofile(config());

--check if the session is ready
	if ( session:ready() ) then
		--answer the call
			session:answer();
	
		--get the variables
			enabled = session:getVariable("enabled");
			pin_number = session:getVariable("pin_number");
			sounds_dir = session:getVariable("sounds_dir");
			domain_uuid = session:getVariable("domain_uuid");
			domain_name = session:getVariable("domain_name");
			extension_uuid = session:getVariable("extension_uuid");
			context = session:getVariable("context");
			if (not context ) then context = 'default'; end
	
		--set the sounds path for the language, dialect and voice
			default_language = session:getVariable("default_language");
			default_dialect = session:getVariable("default_dialect");
			default_voice = session:getVariable("default_voice");
			if (not default_language) then default_language = 'en'; end
			if (not default_dialect) then default_dialect = 'us'; end
			if (not default_voice) then default_voice = 'callie'; end
	
		--a moment to sleep
			session:sleep(1000);
	
		--connect to the database
			dofile(scripts_dir.."/resources/functions/database_handle.lua");
			dbh = database_handle('system');
	
		--determine whether to update the dial string
			sql = "select * from v_extensions ";
			sql = sql .. "where domain_uuid = '"..domain_uuid.."' ";
			sql = sql .. "and extension_uuid = '"..extension_uuid.."' ";
			if (debug["sql"]) then
				freeswitch.consoleLog("notice", "[call_forward] "..sql.."\n");
			end
			status = dbh:query(sql, function(row)
				extension = row.extension;
				number_alias = row.number_alias;
				accountcode = row.accountcode;
				follow_me_uuid = row.follow_me_uuid;
				--freeswitch.consoleLog("NOTICE", "[call forward] extension "..row.extension.."\n");
				--freeswitch.consoleLog("NOTICE", "[call forward] accountcode "..row.accountcode.."\n");
			end);
	
		--set the variables
			if (enabled == "true") then
				forward_all_destination = session:playAndGetDigits(min_digits, max_digits, max_tries, digit_timeout, "#", sounds_dir.."/"..default_language.."/"..default_dialect.."/"..default_voice.."/ivr/ivr-enter_destination_telephone_number.wav", "", "\\d+");
			end
	
		--set the dial string
			if (enabled == "true") then
				dial_string = "{presence_id="..forward_all_destination.."@"..domain_name;
				dial_string = dial_string .. ",instant_ringback=true";
				dial_string = dial_string .. ",domain_uuid="..domain_uuid;
				dial_string = dial_string .. ",sip_invite_domain="..domain_name;
				dial_string = dial_string .. ",domain_name="..domain_name;
				dial_string = dial_string .. ",domain="..domain_name;
				if (accountcode ~= nil) then
					dial_string = dial_string .. ",accountcode="..accountcode;
				end
				dial_string = dial_string .. "}";
	
				cmd = "user_exists id ".. forward_all_destination .." "..domain_name;
				user_exists = trim(api:executeString(cmd));
				if (user_exists) then
					dial_string = dial_string .. "user/"..forward_all_destination.."@"..domain_name;
				else
					dial_string = dial_string .. "loopback/"..forward_all_destination;
				end
			end
	
		--set call forward
			if (enabled == "true") then
				--set forward_all_enabled
					forward_all_enabled = "true";
				--notify the caller
					session:streamFile(sounds_dir.."/"..default_language.."/"..default_dialect.."/"..default_voice.."/ivr/ivr-call_forwarding_has_been_set.wav");
			end
		
		--unset call forward
			if (enabled == "false") then
				--set forward_all_enabled
					forward_all_enabled = "false";
				--notify the caller
					session:streamFile(sounds_dir.."/"..default_language.."/"..default_dialect.."/"..default_voice.."/ivr/ivr-call_forwarding_has_been_cancelled.wav");
			end
	
		--disable the follow me
			if (enabled == "true" and follow_me_uuid ~= nil) then
				sql = "update v_follow_me set ";
				sql = sql .. "follow_me_enabled = 'false' ";
				sql = sql .. "where domain_uuid = '"..domain_uuid.."' ";
				sql = sql .. "and follow_me_uuid = '"..follow_me_uuid.."' ";
				if (debug["sql"]) then
					freeswitch.consoleLog("notice", "[call_forward] "..sql.."\n");
				end
				dbh:query(sql);
			end
	
		--update the extension
			sql = "update v_extensions set ";
			if (enabled == "true") then
				sql = sql .. "forward_all_destination = '"..forward_all_destination.."', ";
				sql = sql .. "dial_string = '"..dial_string.."', ";
				sql = sql .. "do_not_disturb = 'false', ";
			else
				sql = sql .. "dial_string = null, ";
			end
			sql = sql .. "forward_all_enabled = '"..forward_all_enabled.."' ";
			sql = sql .. "where domain_uuid = '"..domain_uuid.."' ";
			sql = sql .. "and extension_uuid = '"..extension_uuid.."' ";
			if (debug["sql"]) then
				freeswitch.consoleLog("notice", "[call_forward] "..sql.."\n");
			end
			dbh:query(sql);
					
		--clear the cache
			if (extension ~= nil) then
				api:execute("memcache", "delete directory:"..extension.."@"..domain_name);
			end
	
		--wait for the file to be written before proceeding
			session:sleep(1000);
	
		--end the call
			session:hangup();
	
	end