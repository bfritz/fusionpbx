--	Part of FusionPBX
--	Copyright (C) 2013 Mark J Crane <markjcrane@fusionpbx.com>
--	All rights reserved.
--
--	Redistribution and use in source and binary forms, with or without
--	modification, are permitted provided that the following conditions are met:
--
--	1. Redistributions of source code must retain the above copyright notice,
--	  this list of conditions and the following disclaimer.
--
--	2. Redistributions in binary form must reproduce the above copyright
--	  notice, this list of conditions and the following disclaimer in the
--	  documentation and/or other materials provided with the distribution.
--
--	THIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES,
--	INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY
--	AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
--	AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
--	OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
--	SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
--	INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
--	CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
--	ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
--	POSSIBILITY OF SUCH DAMAGE.

--set default values
	min_digits = 1;
	max_digits = 8;
	max_tries = 3;
	max_timeouts = 3;
	digit_timeout = 3000;
	stream_seek = false;

--direct dial
	direct_dial = {}
	direct_dial["enabled"] = "true";
	direct_dial["max_digits"] = 4;

--debug
	debug["info"] = false;
	debug["sql"] = false;

--get the argv values
	script_name = argv[0];
	voicemail_action = argv[1];

--starting values
	dtmf_digits = '';
	timeouts = 0;
	password_tries = 0;

--connect to the database
	dofile(scripts_dir.."/resources/functions/database_handle.lua");
	dbh = database_handle('system');

--set the api
	api = freeswitch.API();

--if the session exists
	if (session ~= nil) then
		--answer the session
			if (session:ready()) then
				session:answer();
			end

		--unset bind meta app
			session:execute("unbind_meta_app", "");

		--set the callback function
			if (session:ready()) then
				session:setVariable("playback_terminators", "#");
				session:setInputCallback("on_dtmf", "");
			end

		--get session variables
			context = session:getVariable("context");
			sounds_dir = session:getVariable("sounds_dir");
			domain_name = session:getVariable("domain_name");
			uuid = session:getVariable("uuid");
			voicemail_id = session:getVariable("voicemail_id");
			voicemail_action = session:getVariable("voicemail_action");
			destination_number = session:getVariable("destination_number");
			caller_id_name = session:getVariable("caller_id_name");
			caller_id_number = session:getVariable("caller_id_number");
			skip_instructions = session:getVariable("skip_instructions");
			skip_greeting = session:getVariable("skip_greeting");
			vm_message_ext = session:getVariable("vm_message_ext");
			if (not vm_message_ext) then vm_message_ext = 'wav'; end

		--set the sounds path for the language, dialect and voice
			default_language = session:getVariable("default_language");
			default_dialect = session:getVariable("default_dialect");
			default_voice = session:getVariable("default_voice");
			if (not default_language) then default_language = 'en'; end
			if (not default_dialect) then default_dialect = 'us'; end
			if (not default_voice) then default_voice = 'callie'; end

		--get the domain_uuid
			domain_uuid = session:getVariable("domain_uuid");
			if (domain_count > 1) then
				if (domain_uuid == nil) then
					--get the domain_uuid using the domain name required for multi-tenant
						if (domain_name ~= nil) then
							sql = "SELECT domain_uuid FROM v_domains ";
							sql = sql .. "WHERE domain_name = '" .. domain_name .. "' ";
							if (debug["sql"]) then
								freeswitch.consoleLog("notice", "[xml_handler] SQL: " .. sql .. "\n");
							end
							status = dbh:query(sql, function(rows)
								domain_uuid = rows["domain_uuid"];
							end);
						end
				end
			end
			if (domain_uuid ~= nil) then
				domain_uuid = string.lower(domain_uuid);
			end

		--set the voicemail_dir
			voicemail_dir = voicemail_dir.."/default/"..domain_name;
			if (debug["info"]) then
				freeswitch.consoleLog("notice", "[voicemail] voicemail_dir: " .. voicemail_dir .. "\n");
			end

		--get the voicemail settings
			if (voicemail_id ~= nil) then
				if (session:ready()) then
					--get the information from the database
						sql = [[SELECT * FROM v_voicemails
							WHERE domain_uuid = ']] .. domain_uuid ..[['
							AND voicemail_id = ']] .. voicemail_id ..[['
							AND voicemail_enabled = 'true' ]];
						if (debug["sql"]) then
							freeswitch.consoleLog("notice", "[voicemail] SQL: " .. sql .. "\n");
						end
						status = dbh:query(sql, function(row)
							voicemail_uuid = string.lower(row["voicemail_uuid"]);
							voicemail_password = row["voicemail_password"];
							greeting_id = row["greeting_id"];
							voicemail_mail_to = row["voicemail_mail_to"];
							voicemail_attach_file = row["voicemail_attach_file"];
							voicemail_local_after_email = row["voicemail_local_after_email"];
						end);
					--set default values
						if (voicemail_local_after_email == nil) then
							voicemail_local_after_email = "true";
						end
						if (voicemail_attach_file == nil) then
							voicemail_attach_file = "true";
						end
				end
			end
	end

--general functions
	dofile(scripts_dir.."/resources/functions/base64.lua");
	dofile(scripts_dir.."/resources/functions/trim.lua");
	dofile(scripts_dir.."/resources/functions/file_exists.lua");
	dofile(scripts_dir.."/resources/functions/explode.lua");
	dofile(scripts_dir.."/resources/functions/format_seconds.lua");
	dofile(scripts_dir.."/resources/functions/mkdir.lua");
	dofile(scripts_dir.."/resources/functions/copy.lua");

--voicemail functions
	dofile(scripts_dir.."/app/voicemail/resources/functions/on_dtmf.lua");
	dofile(scripts_dir.."/app/voicemail/resources/functions/get_voicemail_id.lua");
	dofile(scripts_dir.."/app/voicemail/resources/functions/check_password.lua");
	dofile(scripts_dir.."/app/voicemail/resources/functions/change_password.lua");
	dofile(scripts_dir.."/app/voicemail/resources/functions/macro.lua");
	dofile(scripts_dir.."/app/voicemail/resources/functions/play_greeting.lua");
	dofile(scripts_dir.."/app/voicemail/resources/functions/record_message.lua");
	dofile(scripts_dir.."/app/voicemail/resources/functions/record_menu.lua");
	dofile(scripts_dir.."/app/voicemail/resources/functions/forward_to_extension.lua");
	dofile(scripts_dir.."/app/voicemail/resources/functions/main_menu.lua");
	dofile(scripts_dir.."/app/voicemail/resources/functions/listen_to_recording.lua");
	dofile(scripts_dir.."/app/voicemail/resources/functions/message_waiting.lua");
	dofile(scripts_dir.."/app/voicemail/resources/functions/send_email.lua");
	dofile(scripts_dir.."/app/voicemail/resources/functions/delete_recording.lua");
	dofile(scripts_dir.."/app/voicemail/resources/functions/message_saved.lua");
	dofile(scripts_dir.."/app/voicemail/resources/functions/return_call.lua");
	dofile(scripts_dir.."/app/voicemail/resources/functions/menu_messages.lua");
	dofile(scripts_dir.."/app/voicemail/resources/functions/advanced.lua");
	dofile(scripts_dir.."/app/voicemail/resources/functions/record_greeting.lua");
	dofile(scripts_dir.."/app/voicemail/resources/functions/choose_greeting.lua");
	dofile(scripts_dir.."/app/voicemail/resources/functions/record_name.lua");

--send a message waiting event
	if (voicemail_action == "mwi") then
		--get the mailbox info
			account = argv[2];
			array = explode("@", account);
			voicemail_id = array[1];
			domain_name = array[2];

		--send information the console
			debug["info"] = "true";

		--get voicemail message details
			sql = [[SELECT * FROM v_domains WHERE domain_name = ']] .. domain_name ..[[']]
			if (debug["sql"]) then
				freeswitch.consoleLog("notice", "[voicemail] SQL: " .. sql .. "\n");
			end
			status = dbh:query(sql, function(row)
				domain_uuid = string.lower(row["domain_uuid"]);
			end);

		--get the message count and send the mwi event
			message_waiting(voicemail_id, domain_uuid);
	end

--check messages
	if (voicemail_action == "check") then
		if (session:ready()) then
			--check the voicemail password
				check_password(voicemail_id, password_tries);
			--send to the main menu
				timeouts = 0;
				main_menu();
		end
	end

--leave a message
	if (voicemail_action == "save") then

		--valid voicemail
			if (voicemail_uuid ~= nil) then

				--save the recording
					timeouts = 0;
					play_greeting();
					record_message();

				--save the message to the voicemail messages
					if (message_length > 2) then
						local sql = {}
						table.insert(sql, "INSERT INTO v_voicemail_messages ");
						table.insert(sql, "(");
						table.insert(sql, "voicemail_message_uuid, ");
						table.insert(sql, "domain_uuid, ");
						table.insert(sql, "voicemail_uuid, ");
						table.insert(sql, "created_epoch, ");
						table.insert(sql, "caller_id_name, ");
						table.insert(sql, "caller_id_number, ");
						table.insert(sql, "message_length ");
						--table.insert(sql, "message_status, ");
						--table.insert(sql, "message_priority, ");
						table.insert(sql, ") ");
						table.insert(sql, "VALUES ");
						table.insert(sql, "( ");
						table.insert(sql, "'".. uuid .."', ");
						table.insert(sql, "'".. domain_uuid .."', ");
						table.insert(sql, "'".. voicemail_uuid .."', ");
						table.insert(sql, "'".. start_epoch .."', ");
						table.insert(sql, "'".. caller_id_name .."', ");
						table.insert(sql, "'".. caller_id_number .."', ");
						table.insert(sql, "'".. message_length .."' ");
						--table.insert(sql, "'".. message_status .."', ");
						--table.insert(sql, "'".. message_priority .."' ");
						table.insert(sql, ") ");
						sql = table.concat(sql, "\n");
						if (debug["sql"]) then
							freeswitch.consoleLog("notice", "[voicemail] SQL: " .. sql .. "\n");
						end
						dbh:query(sql);
					end

				--set the message waiting event
					if (message_length > 2) then
						local event = freeswitch.Event("message_waiting");
						event:addHeader("MWI-Messages-Waiting", "yes");
						event:addHeader("MWI-Message-Account", "sip:"..voicemail_id.."@"..domain_name);
						event:fire();
					end

				--send the email with the voicemail recording attached
					if (message_length > 2) then
						send_email(voicemail_id, uuid);
					end
			else
				--voicemail not enabled or does not exist
					referred_by = session:getVariable("sip_h_Referred-By");
					if (referred_by) then
						referred_by = referred_by:match('[%d]+');
						session:transfer(referred_by, "XML", context);
					end
			end
	end

--close the database connection
	dbh:release();

--notes
	--record the video
		--records audio only
			--result = session:execute("set", "enable_file_write_buffering=false");
			--mkdir(voicemail_dir.."/"..voicemail_id);
			--session:recordFile("/tmp/recording.fsv", 200, 200, 200);
		--records audio and video
			--result = session:execute("record_fsv", "file.fsv");
			--freeswitch.consoleLog("notice", "[voicemail] SQL: " .. result .. "\n");

	--play the video recording
		--plays the video
			--result = session:execute("play_fsv", "/tmp/recording.fsv");
		--plays the file but without the video
			--dtmf = session:playAndGetDigits(min_digits, max_digits, max_tries, digit_timeout, "#", "/tmp/recording.fsv", "", "\\d+");
		--freeswitch.consoleLog("notice", "[voicemail] SQL: " .. result .. "\n");

	--callback (works with DTMF)
		--http://wiki.freeswitch.org/wiki/Mod_fsv
		--mkdir(voicemail_dir.."/"..voicemail_id);
		--session:recordFile(file_name, max_len_secs, silence_threshold, silence_secs) 
		--session:sayPhrase(macro_name [,macro_data] [,language]);
		--session:sayPhrase("voicemail_menu", "1:2:3:#", default_language);
		--session:streamFile("directory/dir-to_select_entry.wav"); --works with setInputCallback
		--session:streamFile("tone_stream://L=1;%(1000, 0, 640)");
		--session:say("12345", default_language, "number", "pronounced");

		--speak
			--session:set_tts_parms("flite", "kal");
			--session:speak("Please say the name of the person you're trying to contact");

	--callback (execute and executeString does not work with DTMF)
		--session:execute(api_string);
		--session:executeString("playback "..mySound);

	--uuid_video_refresh
		--uuid_video_refresh,<uuid>,Send video refresh.,mod_commands
		--may be used to clear video buffer before using record_fsv
