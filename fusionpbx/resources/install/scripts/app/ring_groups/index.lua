--	ring_groups.lua
--	Part of FusionPBX
--	Copyright (C) 2010-2013 Mark J Crane <markjcrane@fusionpbx.com>
--	All rights reserved.
--
--	Redistribution and use in source and binary forms, with or without
--	modification, are permitted provided that the following conditions are met:
--
--	1. Redistributions of source code must retain the above copyright notice,
--	   this list of conditions and the following disclaimer.
--
--	2. Redistributions in binary form must reproduce the above copyright
--	   notice, this list of conditions and the following disclaimer in the
--	   documentation and/or other materials provided with the distribution.
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

--connect to the database
	dofile(scripts_dir.."/resources/functions/database_handle.lua");
	dbh = database_handle('system');

--include functions
	dofile(scripts_dir.."/resources/functions/trim.lua");
	dofile(scripts_dir.."/resources/functions/explode.lua");

--get the variables
	domain_name = session:getVariable("domain_name");
	ring_group_uuid = session:getVariable("ring_group_uuid");
	recordings_dir = session:getVariable("recordings_dir");
	sounds_dir = session:getVariable("sounds_dir");

--variables that don't require ${} when used in the dialplan conditions
	username = session:getVariable("username");
	dialplan = session:getVariable("dialplan");
	caller_id_name = session:getVariable("caller_id_name");
	caller_id_number = session:getVariable("caller_id_number");
	network_addr = session:getVariable("network_addr");
	ani = session:getVariable("ani");
	aniii = session:getVariable("aniii");
	rdnis = session:getVariable("rdnis");
	destination_number = session:getVariable("destination_number");
	source = session:getVariable("source");
	uuid = session:getVariable("uuid");
	context = session:getVariable("context");

--define additional variables
	uuids = "";
	prompt = "false";
	external = "false";

--set the sounds path for the language, dialect and voice
	default_language = session:getVariable("default_language");
	default_dialect = session:getVariable("default_dialect");
	default_voice = session:getVariable("default_voice");
	if (not default_language) then default_language = 'en'; end
	if (not default_dialect) then default_dialect = 'us'; end
	if (not default_voice) then default_voice = 'callie'; end

--prepare the api object
	api = freeswitch.API();

--define the session hangup
	--function on_hangup(s,status)
	--	freeswitch.consoleLog("NOTICE","---- on_hangup: "..status.."\n");
	--	error();
	--end

--get the ring group
	ring_group_forward_enabled = "";
	ring_group_forward_destination = "";
	sql = "SELECT * FROM v_ring_groups ";
	sql = sql .. "where ring_group_uuid = '"..ring_group_uuid.."' ";
	status = dbh:query(sql, function(row)
		domain_uuid = row["domain_uuid"];
		ring_group_forward_enabled = row["ring_group_forward_enabled"];
		ring_group_forward_destination = row["ring_group_forward_destination"];
	end);

--process the ring group
	if (ring_group_forward_enabled == "true" and string.len(ring_group_forward_destination) > 0) then
		--forward the ring group
			session:execute("transfer", ring_group_forward_destination.." XML "..context);
	else
		--get the ring group destinations
			sql = 
			[[ SELECT r.ring_group_strategy, r.ring_group_timeout_sec, r.ring_group_timeout_app, d.destination_number, d.destination_delay, d.destination_timeout, d.destination_prompt, r.ring_group_timeout_data, r.ring_group_cid_name_prefix, r.ring_group_ringback
			FROM v_ring_groups as r, v_ring_group_destinations as d
			where d.ring_group_uuid = r.ring_group_uuid 
			and d.ring_group_uuid = ']]..ring_group_uuid..[[' 
			and r.ring_group_enabled = 'true' 
			order by d.destination_delay, d.destination_number asc ]]
			--freeswitch.consoleLog("notice", "SQL:" .. sql .. "\n");
			destinations = {};
			x = 1;
			assert(dbh:query(sql, function(row)
				if (row.destination_prompt == "1" or row.destination_prompt == "2") then
					prompt = "true";
				end
				cmd = "user_exists id ".. row.destination_number .." "..domain_name;
				user_exists = api:executeString(cmd);
				if (user_exists == "true") then
					row['user_exists'] = "true";
				else
					external = "true";
					row['user_exists'] = "false";
				end
				destinations[x] = row;
				x = x + 1;
			end));
			--freeswitch.consoleLog("NOTICE", "[ring_group] prompt "..prompt.."\n");
			--freeswitch.consoleLog("NOTICE", "[ring_group] external "..external.."\n");

		--get the dialplan data and save it to a table
			if (external) then
				sql = [[select * from v_dialplans as d, v_dialplan_details as s 
				where d.domain_uuid = ']] .. domain_uuid .. [[' 
				and d.app_uuid = '8c914ec3-9fc0-8ab5-4cda-6c9288bdc9a3' 
				and d.dialplan_enabled = 'true' 
				and d.dialplan_uuid = s.dialplan_uuid 
				order by 
				d.dialplan_order asc, 
				d.dialplan_name asc, 
				d.dialplan_uuid asc, 
				s.dialplan_detail_group asc, 
				CASE s.dialplan_detail_tag 
				WHEN 'condition' THEN 1 
				WHEN 'action' THEN 2 
				WHEN 'anti-action' THEN 3 
				ELSE 100 END, 
				s.dialplan_detail_order asc ]]
				--freeswitch.consoleLog("notice", "SQL:" .. sql .. "\n");
				dialplans = {};
				x = 1;
				assert(dbh:query(sql, function(row)
					dialplans[x] = row;
					x = x + 1;
				end));
			end

		--process the destinations
			x = 0;
			for key, row in pairs(destinations) do
				--set the values from the database as variables
					user_exists = row.user_exists;
					ring_group_timeout_sec = row.ring_group_timeout_sec;
					ring_group_timeout_app = row.ring_group_timeout_app;
					ring_group_timeout_data = row.ring_group_timeout_data;
					ring_group_cid_name_prefix = row.ring_group_cid_name_prefix;
					ring_group_ringback = row.ring_group_ringback;
					destination_number = row.destination_number;
					destination_delay = row.destination_delay;
					destination_timeout = row.destination_timeout;
					destination_prompt = row.destination_prompt;

				--set ringback
					if (ring_group_ringback == "${uk-ring}") then
						ring_group_ringback = "tone_stream://%(400,200,400,450);%(400,2200,400,450);loops=-1";
					end
					if (ring_group_ringback == "${us-ring}") then
						ring_group_ringback = "tone_stream://%(2000,4000,440.0,480.0);loops=-1";
					end
					if (ring_group_ringback == "${fr-ring}") then
						ring_group_ringback = "tone_stream://%(1500,3500,440.0,0.0);loops=-1";
					end
					if (ring_group_ringback == "${rs-ring}") then
						ring_group_ringback = "tone_stream://%(1000,4000,425.0,0.0);loops=-1";
					end
					if (ring_group_ringback == "") then
						ring_group_ringback = "local_stream://default";
					end
					session:setVariable("ringback", ring_group_ringback);
					session:setVariable("transfer_ringback", ring_group_ringback);

				--add the caller id prefix
					if (string.len(ring_group_cid_name_prefix) > 0) then
						origination_caller_id_name = ring_group_cid_name_prefix .. "#" .. caller_id_name;
					else
						origination_caller_id_name = caller_id_name;
					end
					origination_caller_id_number = caller_id_number;

				--setup the delimiter
					delimiter = ",";
					if (row.ring_group_strategy == "sequence") then
						delimiter = "|";
					end
					if (row.ring_group_strategy == "simultaneous") then
						delimiter = ",";
					end
					if (row.ring_group_strategy == "enterprise") then
						delimiter = ":_:";
					end

				--create a new uuid and add it to the uuid list
					new_uuid = api:executeString("create_uuid");
					if (string.len(uuids) == 0) then
						uuids = new_uuid;
					else
						uuids = uuids ..",".. new_uuid;
					end
					session:execute("set", "uuids="..uuids);
					if (prompt == "true") then
						origination_uuid = "origination_uuid="..new_uuid..",";
					else
						origination_uuid = "";
					end

				--process according to user_exists, sip_uri, external number
					if (user_exists == "true") then
						--send to user
						dial_string = "["..origination_uuid.."sip_invite_domain="..domain_name..",leg_timeout="..destination_timeout..",leg_delay_start="..destination_delay.."]user/" .. row.destination_number .. "@" .. domain_name;
					elseif (tonumber(destination_number) == nil) then
						--sip uri
						dial_string = "[origination_uuid="..new_uuid..",sip_invite_domain="..domain_name..",leg_timeout="..destination_timeout..",leg_delay_start="..destination_delay.."]" .. row.destination_number;
					else
						--external number
						y = 0;
						previous_dialplan_uuid = '';
						for k, r in pairs(dialplans) do
							if (y > 0) then
								if (previous_dialplan_uuid ~= r.dialplan_uuid) then
									regex_match = false;
									bridge_match = false;
									square = square .. "]";
									y = 0;
								end
							end
							if (r.dialplan_detail_tag == "condition") then
								if (r.dialplan_detail_type == "destination_number") then
									if (api:execute("regex", "m:~"..destination_number.."~"..r.dialplan_detail_data) == "true") then
										--get the regex result
											destination_result = trim(api:execute("regex", "m:~"..destination_number.."~"..r.dialplan_detail_data.."~\$1"));
										--set match equal to true
											regex_match = true
									end
								end
							end
							if (r.dialplan_detail_tag == "action") then
								if (regex_match) then
									--replace $1
										dialplan_detail_data = r.dialplan_detail_data:gsub("$1", destination_result);
									--if the session is set then process the actions
										if (y == 0) then
											square = "[sip_invite_domain="..domain_name..",leg_timeout="..destination_timeout..",leg_delay_start="..destination_delay..",origination_caller_id_name="..origination_caller_id_name..",";
										end
										if (r.dialplan_detail_type == "set") then
											--session:execute("eval", dialplan_detail_data);
											if (dialplan_detail_data == "sip_h_X-accountcode=${accountcode}") then
												if (session) then
													accountcode = session:getVariable("accountcode");
													if (accountcode) then
														square = square .. "sip_h_X-accountcode="..accountcode..",";
													end
												end
											elseif (dialplan_detail_data == "effective_caller_id_name=${outbound_caller_id_name}") then
											elseif (dialplan_detail_data == "effective_caller_id_number=${outbound_caller_id_number}") then
											else
												square = square .. dialplan_detail_data..",";
											end
										elseif (r.dialplan_detail_type == "bridge") then
											if (bridge_match) then
												dial_string = dial_string .. "," .. square .."]"..dialplan_detail_data;
												if (prompt == "true") then
													square = "[origination_uuid="..new_uuid..",";
												else
													square = "[";
												end
											else
												dial_string = square .."]"..dialplan_detail_data;
											end
											bridge_match = true;
										end
									--increment the value
										y = y + 1;
								end
							end
							previous_dialplan_uuid = r.dialplan_uuid;
						end
						--freeswitch.consoleLog("notice", "[ring group] dial_string: " .. dial_string .. "\n");
					end

				--prompt will use the confirm lua script and the content of else will use the concatenated dialstring seperated by the delimiter
					if (prompt == "true") then
						--determine confirm prompt
							if (destination_prompt == nil) then
								originate_prompt = "false";
							elseif (destination_prompt == "1") then
								originate_prompt = "true";
							elseif (destination_prompt == "2") then
								originate_prompt = "true";
							else
								originate_prompt = "false";
							end
						--originate each destination
							if (dial_string ~= nil) then
								dial_string = "{ignore_early_media=true,origination_caller_id_name="..origination_caller_id_name..",origination_caller_id_number="..origination_caller_id_number.."}"..dial_string;
								cmd = "";
								if (tonumber(destination_delay) > 0) then
									cmd = "sched_api +"..destination_delay.." "..new_uuid.." ";
								end
								cmd = cmd .. "bgapi originate "..dial_string.." '&lua('"..scripts_dir.."/app/ring_groups/resources/scripts/confirm.lua' "..uuid.." "..originate_prompt..")'";
								--freeswitch.consoleLog("notice", "[ring group] cmd: " .. cmd .. "\n");
								result = trim(api:executeString(cmd));
							end

					else
						--use a delimiter between dialstrings
							if (dial_string ~= nil) then
								if (x == 0) then
									app_data = "{ignore_early_media=true,origination_caller_id_name="..origination_caller_id_name..",origination_caller_id_number="..origination_caller_id_number.."}"..dial_string;
								else
									app_data = app_data .. delimiter .. dial_string;
								end
								--freeswitch.consoleLog("notice", "[ring group] app_data: " .. app_data .. "\n");
							end
					end

				--increment the value of x
					x = x + 1;
			end

		--session execute
			if (session:ready()) then
				--set the variables
					session:execute("set", "hangup_after_bridge=true");
					session:execute("set", "continue_on_fail=true");

				--set bind meta app
					session:execute("bind_meta_app", "1 ab s execute_extension::dx XML features");
					session:execute("bind_meta_app", "2 ab s record_session::"..recordings_dir.."}/archive/"..os.date("%Y").."/"..os.date("%m").."/"..os.date("%d").."}/"..uuid..".wav");
					session:execute("bind_meta_app", "3 ab s execute_extension::cf XML features");
					session:execute("bind_meta_app", "4 ab s execute_extension::att_xfer XML features");

				--prompt to accept call if true schedule the call timeout
					if (prompt == "true") then
						--schedule the timeout and route to the timeout destination
							if (ring_group_timeout_app == "transfer") then
								cmd = "sched_api +"..ring_group_timeout_sec.." ring_group:"..uuid.." bgapi uuid_transfer "..uuid.." "..ring_group_timeout_data;
							elseif (ring_group_timeout_app == "bridge") then
								cmd = "sched_api +"..ring_group_timeout_sec.." ring_group:"..uuid.." bgapi uuid_transfer "..uuid.." bridge:"..ring_group_timeout_data;
							else
								cmd = "sched_api +"..ring_group_timeout_sec.." ring_group:"..uuid.." bgapi uuid_kill "..uuid.." alloted_timeout";
							end
							--freeswitch.consoleLog("NOTICE", "[confirm] schedule timeout: "..cmd.."\n");
							results = trim(api:executeString(cmd));

						--start the uuid hangup monitor
							api = freeswitch.API();
							cmd = "luarun "..scripts_dir.."/app/ring_groups/resources/scripts/monitor.lua "..uuid.." 30";
							result = api:executeString(cmd);

						--answer the call this is required for uuid_broadcast
							session:answer();

						--park the call and add the simulated ringback
							cmd = "bgapi uuid_park "..uuid;
							result = api:executeString(cmd);
							cmd = "bgapi uuid_broadcast "..uuid.." "..ring_group_ringback.." aleg";
							result = api:executeString(cmd);
					else
						--no prompt
							if (app_data ~= nil) then
								--freeswitch.consoleLog("NOTICE", "[ring group] app_data: "..app_data.."\n");
								session:execute("bridge", app_data);
								if (session:getVariable("originate_disposition") == "ALLOTTED_TIMEOUT" or session:getVariable("originate_disposition") == "NO_ANSWER" or session:getVariable("originate_disposition") == "ORIGINATOR_CANCEL") then
									session:execute(ring_group_timeout_app, ring_group_timeout_data);
								end
							else
								if (ring_group_timeout_app ~= nil) then
									session:execute(ring_group_timeout_app, ring_group_timeout_data);
								else
									sql = "SELECT ring_group_timeout_app, ring_group_timeout_data FROM v_ring_groups ";
									sql = sql .. "where ring_group_uuid = '"..ring_group_uuid.."' ";
									freeswitch.consoleLog("notice", "SQL:" .. sql .. "\n");
									dbh:query(sql, function(row)
										session:execute(row.ring_group_timeout_app, row.ring_group_timeout_data);
									end);
								end
							end
					end
			end
	end

--actions
	--ACTIONS = {}
	--table.insert(ACTIONS, {"set", "hangup_after_bridge=true"});
	--table.insert(ACTIONS, {"set", "continue_on_fail=true"});
	--table.insert(ACTIONS, {"bridge", app_data});
	--table.insert(ACTIONS, {ring_group_timeout_app, ring_group_timeout_data});
