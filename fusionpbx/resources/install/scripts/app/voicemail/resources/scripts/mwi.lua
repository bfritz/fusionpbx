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

--include the lua script
	scripts_dir = string.sub(debug.getinfo(1).source,2,string.len(debug.getinfo(1).source)-(string.len(argv[0])+1));
	dofile(scripts_dir.."/resources/functions/config.lua");
	dofile(config());

--define general settings
	tmp_file = "/usr/local/freeswitch/log/mwi.tmp";
	sleep = 300;

--debug
	debug["sql"] = false;
	debug["info"] = false;

--connect to the database
	dofile(scripts_dir.."/resources/functions/database_handle.lua");
	dbh = database_handle('system');

--used to stop the lua service
	local file = assert(io.open(tmp_file, "w"));
	file:write("remove this file to stop the script");

--add the trim function
	function trim(s)
		return s:gsub("^%s+", ""):gsub("%s+$", "")
	end

--check if a file exists
	function file_exists(name)
		local f=io.open(name,"r")
		if f~=nil then io.close(f) return true else return false end
	end

--create the api object
	api = freeswitch.API();

--run lua as a service
	while true do

		--exit the loop when the file does not exist
			if (not file_exists(tmp_file)) then
				freeswitch.consoleLog("NOTICE", tmp_file.." not found\n");
				break;
			end

		--Send MWI events for voicemail boxes with messages
			sql = [[SELECT v.voicemail_id, v.voicemail_uuid, v.domain_uuid, d.domain_name, COUNT(*) AS message_count 
				FROM v_voicemail_messages as m, v_voicemails as v, v_domains as d 
				WHERE v.voicemail_uuid = m.voicemail_uuid 
				AND v.domain_uuid = d.domain_uuid 
				GROUP BY v.voicemail_id, v.voicemail_uuid, v.domain_uuid, d.domain_name;]];
			if (debug["sql"]) then
				freeswitch.consoleLog("notice", "[voicemail] SQL: " .. sql .. "\n");
			end
			status = dbh:query(sql, function(row)
				--send the message waiting event
					local event = freeswitch.Event("message_waiting");
					if (row["message_count"] == "0") then
						event:addHeader("MWI-Messages-Waiting", "no");
					else
						event:addHeader("MWI-Messages-Waiting", "yes");
					end
					event:addHeader("MWI-Message-Account", "sip:"..row["voicemail_id"].."@"..row["domain_name"]);
					event:fire();
				--log to console
					if (debug["info"]) then
						freeswitch.consoleLog("notice", "[voicemail] mailbox: "..row["voicemail_id"].."@"..row["domain_name"].." messages: " .. row["message_count"] .. " \n");
					end
			end);

		--slow the loop down
			os.execute("sleep "..sleep);

		--testing exit immediately
			--break;
	end
