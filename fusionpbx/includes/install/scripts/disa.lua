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
--	Portions created by the Initial Developer are Copyright (C) 2008-2010
--	the Initial Developer. All Rights Reserved.
--
--	Contributor(s):
--	Mark J Crane <markjcrane@fusionpbx.com>

predefined_destination = "";
pin_digit_min_length = "";
digit_min_length = "2";
digit_max_length = "11";
retries = "3";
digit_timeout = "5000";

--freeswitch.consoleLog("NOTICE", "DISA:\n");

--function HangupHook(s, status, arg)
	--session:execute("info", "");
	--freeswitch.consoleLog("NOTICE", "HangupHook: " .. status .. "\n");
--end
--session:setHangupHook("HangupHook", "");

if ( session:ready() ) then
	session:answer( );
	pin_number = session:getVariable("pin_number");
	sounds_dir = session:getVariable("sounds_dir");
	caller_id_name = session:getVariable("caller_id_name");
	caller_id_number = session:getVariable("caller_id_number");
	predefined_destination = session:getVariable("predefined_destination");

	-- if a pin number has been defined then request the pin number
	if (pin_number) then
		pin_length = string.len(pin_number);
		digits = session:playAndGetDigits(pin_length, pin_length, retries, 3000, "#", sounds_dir.."/custom/8000/please_enter_the_pin_number.wav", "", "\\d+");
		if (digits == pin_number) then
			--pin is correct 
			--session:execute("set", "hangup_after_bridge=true");
			--session:execute("set", "continue_on_fail=true");

			if (predefined_destination) then
				session:execute("transfer", predefined_destination .. " XML default");
			else
				dtmf = ""; --clear dtmf digits to prepare for next dtmf request
				destination_number = session:playAndGetDigits(digit_min_length, digit_max_length, retries, digit_timeout, "#", sounds_dir.."/custom/8000/please_enter_the_phone_number.wav", "", "\\d+");
				--if (string.len(destination_number) == 10) then destination_number = "1"..destination_number; end
				session:execute("transfer", destination_number .. " XML default");
				--session:execute("bridge", "sofia/gateway/flowroute.com/"..destination_number);

				--local session2 = freeswitch.Session("{ignore_early_media=true}sofia/gateway/flowroute.com/"..destination_number);
				--t1 = os.date('*t');
				--call_start_time = os.time(t1);
				--freeswitch.bridge(session, session2);
			end

		else 
			session:streamFile( sounds_dir.."/custom/8000/your_pin_number_is_incorect_goodbye.wav");
			session:hangup();
		end

	else
		--session:execute("set", "hangup_after_bridge=true");
		--session:execute("set", "continue_on_fail=true");

		if (predefined_destination) then
				session:execute("transfer", predefined_destination .. " XML default");
		else
			dtmf = ""; --clear dtmf digits to prepare for next dtmf request
			digits = session:playAndGetDigits(digit_min_length, digit_max_length, retries, digit_timeout, "#", sounds_dir.."/custom/8000/please_enter_the_phone_number.wav", "", "\\d+");
			session:execute("transfer", digits .. " XML default");
		end
	end
end
