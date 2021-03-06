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
--	Copyright (C) 2010
--	the Initial Developer. All Rights Reserved.
--
--	Contributor(s):
--	Mark J Crane <markjcrane@fusionpbx.com>

sounds_dir = "";
recordings_dir = "";
pin_number = "";
max_tries = "3";
digit_timeout = "3000";

function file_exists(fname)
	local f = io.open(fname, "r")
	if (f and f:read()) then return true end
end

if ( session:ready() ) then
	session:answer();
	--session:execute("info", "");
	extension = session:getVariable("user_name");
	pin_number = session:getVariable("pin_number");
	sounds_dir = session:getVariable("sounds_dir");
	dialplan_default_dir = session:getVariable("dialplan_default_dir");
	call_forward_number = session:getVariable("call_forward_number");
	extension_required = session:getVariable("extension_required");

	if (pin_number) then
		digits = session:playAndGetDigits(3, 8, 3, digit_timeout, "#", sounds_dir.."/custom/please_enter_the_pin_number.wav", "", "\\d+");
		if (digits == pin_number) then
			--pin is correct

			if (extension_required) then
				if (extension_required == "true") then
					extension = session:playAndGetDigits(3, 6, max_tries, digit_timeout, "#", sounds_dir.."/custom/please_enter_the_extension_number.wav", "", "\\d+");
				end
			end

			if (file_exists(dialplan_default_dir.."/999_call_forward_"..extension..".xml")) then
				--freeswitch.consoleLog("NOTICE", "file_exists: true\n");
				os.remove (dialplan_default_dir.."/999_call_forward_"..extension..".xml");

			--stream file
				session:streamFile(sounds_dir.."/custom/call_forward_has_been_deleted.wav");

			--wait for the file to be written before proceeding
				session:sleep(1000);

			else
				freeswitch.consoleLog("NOTICE", "file_exists: false\n");

				dtmf = ""; --clear dtmf digits to prepare for next dtmf request
				if (call_forward_number) then
					-- do nothing
				else
					-- call_forward_number is not defined so request it
					call_forward_number = session:playAndGetDigits(3, 15, max_tries, digit_timeout, "#", sounds_dir.."/custom/please_enter_the_phone_number.wav", "", "\\d+");
				end
				if (string.len(call_forward_number) > 0) then
				--write the xml file
					xml = "<extension name=\"call_forward_"..extension.."\" >\n";
					xml = xml .. "	<condition field=\"destination_number\" expression=\"^"..extension.."$\">\n";
					xml = xml .. "		<action application=\"transfer\" data=\""..call_forward_number.." XML default\"/>\n";
					xml = xml .. "	</condition>\n";
					xml = xml .. "</extension>\n";
					local file = assert(io.open(dialplan_default_dir.."/999_call_forward_"..extension..".xml", "w"));
					file:write(xml);
					file:close();

				--wait for the file to be written before proceeding
					--session:sleep(20000); 

				--stream file
					session:streamFile(sounds_dir.."/custom/call_forward_has_been_set.wav");
				end
			end

			--reloadxml
				api = freeswitch.API();
				reply = api:executeString("reloadxml");

			--wait for the file to be written before proceeding
				session:sleep(1000);

			session:hangup();

		else
			session:streamFile(sounds_dir.."/custom/your_pin_number_is_incorect_goodbye.wav");
		end
	else

		if (extension_required) then
			if (extension_required == "true") then
				extension = session:playAndGetDigits(3, 6, max_tries, digit_timeout, "#", sounds_dir.."/custom/please_enter_the_extension_number.wav", "", "\\d+");
			end
		end

		if (file_exists(dialplan_default_dir.."/999_call_forward_"..extension..".xml")) then
			freeswitch.consoleLog("NOTICE", "file_exists: true\n");
			os.remove (dialplan_default_dir.."/999_call_forward_"..extension..".xml");

		--stream file
			session:streamFile(sounds_dir.."/custom/call_forward_has_been_deleted.wav");

		--wait for the file to be written before proceeding
			session:sleep(1000);

		else
			freeswitch.consoleLog("NOTICE", "file_exists: false\n");

			dtmf = ""; --clear dtmf digits to prepare for next dtmf request
			if (call_forward_number) then
				-- do nothing
			else
				call_forward_number = session:playAndGetDigits(3, 15, max_tries, digit_timeout, "#", sounds_dir.."/custom/please_enter_the_phone_number.wav", "", "\\d+");
			end
			if (string.len(call_forward_number) > 0) then
			--write the xml file
				xml = "<extension name=\"call_forward_"..extension.."\" >\n";
				xml = xml .. "	<condition field=\"destination_number\" expression=\"^"..extension.."$\">\n";
				xml = xml .. "		<action application=\"transfer\" data=\""..call_forward_number.." XML default\"/>\n";
				xml = xml .. "	</condition>\n";
				xml = xml .. "</extension>\n";
				session:execute("log", xml);
				local file = assert(io.open(dialplan_default_dir.."/999_call_forward_"..extension..".xml", "w"));
				file:write(xml);
				file:close();

			--wait for the file to be written before proceeding
				--session:sleep(20000); 

			--stream file
				session:streamFile(sounds_dir.."/custom/call_forward_has_been_set.wav");
			end
		end

		--reloadxml
			api = freeswitch.API();
			reply = api:executeString("reloadxml");

		--wait for the file to be written before proceeding
			session:sleep(1000);

		session:hangup();
	end
end