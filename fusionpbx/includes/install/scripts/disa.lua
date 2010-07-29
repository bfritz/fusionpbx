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
--	Copyright (C) 2008-2010
--	the Initial Developer. All Rights Reserved.
--
--	Contributor(s):
--	Mark J Crane <markjcrane@fusionpbx.com>

predefined_destination = "";
max_tries = "3";
digit_timeout = "5000";

if ( session:ready() ) then
	session:answer( );
	pin_number = session:getVariable("pin_number");
	sounds_dir = session:getVariable("sounds_dir");
	caller_id_name = session:getVariable("caller_id_name");
	caller_id_number = session:getVariable("caller_id_number");
	predefined_destination = session:getVariable("predefined_destination");
	digit_min_length = session:getVariable("digit_min_length");
	digit_max_length = session:getVariable("digit_max_length");

	--set defaults
		if (digit_min_length) then
			--do nothing
		else
			digit_min_length = "2";
		end

		if (digit_max_length) then
			--do nothing
		else
			digit_max_length = "11";
		end

	--if the pin number is provided then require it
		if (pin_number) then
			min_digits = string.len(pin_number);
			max_digits = string.len(pin_number)+1;
			digits = session:playAndGetDigits(min_digits, max_digits, max_tries, digit_timeout, "#", sounds_dir.."/custom/please_enter_the_pin_number.wav", "", "\\d+");
			if (digits == pin_number) then
				--pin is correct
			else
				session:streamFile(sounds_dir.."/custom/your_pin_number_is_incorect_goodbye.wav");
				session:hangup("NORMAL_CLEARING");
				return;
			end
		end

	--if a predefined_destination is provided then send the call there otherwise prompt for the destination number then send the call
		if (predefined_destination) then
			session:execute("transfer", predefined_destination .. " XML default");
		else
			dtmf = ""; --clear dtmf digits to prepare for next dtmf request
			destination_number = session:playAndGetDigits(digit_min_length, digit_max_length, max_tries, digit_timeout, "#", sounds_dir.."/custom/please_enter_the_phone_number.wav", "", "\\d+");
			--if (string.len(destination_number) == 10) then destination_number = "1"..destination_number; end
			session:execute("transfer", destination_number .. " XML default");

			--alternate method
				--session:execute("set", "hangup_after_bridge=true");
				--session:execute("set", "continue_on_fail=true");
				--session:execute("bridge", "sofia/gateway/flowroute.com/"..destination_number);

			--alternate method
				--local session2 = freeswitch.Session("{ignore_early_media=true}sofia/gateway/flowroute.com/"..destination_number);
				--t1 = os.date('*t');
				--call_start_time = os.time(t1);
				--freeswitch.bridge(session, session2);
		end
end

--function HangupHook(s, status, arg)
	--session:execute("info", "");
	--freeswitch.consoleLog("NOTICE", "HangupHook: " .. status .. "\n");
--end
--session:setHangupHook("HangupHook", "");
