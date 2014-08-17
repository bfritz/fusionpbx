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

max_tries = "3";
digit_timeout = "5000";

function trim (s)
	return (string.gsub(s, "^%s*(.-)%s*$", "%1"))
end

function explode ( seperator, str ) 
	local pos, arr = 0, {}
	for st, sp in function() return string.find( str, seperator, pos, true ) end do -- for each divider found
		table.insert( arr, string.sub( str, pos, st-1 ) ) -- attach chars left of current divider
		pos = sp + 1 -- jump past current divider
	end
	table.insert( arr, string.sub( str, pos ) ) -- attach chars right of last divider
	return arr
end

if ( session:ready() ) then
	session:answer( );
	pin_number = session:getVariable("pin_number");
	sounds_dir = session:getVariable("sounds_dir");

	--set the sounds path for the language, dialect and voice
		default_language = session:getVariable("default_language");
		default_dialect = session:getVariable("default_dialect");
		default_voice = session:getVariable("default_voice");
		if (not default_language) then default_language = 'en'; end
		if (not default_dialect) then default_dialect = 'us'; end
		if (not default_voice) then default_voice = 'callie'; end

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
			--sleep
				session:sleep(500);
			--get the user pin number
				min_digits = 2;
				max_digits = 20;
				digits = session:playAndGetDigits(min_digits, max_digits, max_tries, digit_timeout, "#", "phrase:voicemail_enter_pass:#", "", "\\d+");
			--validate the user pin number
				pin_number_table = explode(",",pin_number);
				for index,pin_number in pairs(pin_number_table) do
					if (digits == pin_number) then
						--set the variable to true
							auth = true;
						--set the authorized pin number that was used
							session:setVariable("pin_number", pin_number);
						--end the loop
							break;
					end
				end
			--if not authorized play a message and then hangup
				if (not auth) then
					session:streamFile("phrase:voicemail_fail_auth:#");
					session:hangup("NORMAL_CLEARING");
					return;
				end
		end
end
