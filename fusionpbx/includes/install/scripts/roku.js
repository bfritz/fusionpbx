	include("config.js");
		//var sounds_dir
		//var admin_pin = "456789";
		//var tmp_dir

	//var admin_pin = ""; //don't require a pin
	//if you choose not to require a pin then then you may want to add a dialplan condition for a specific caller id
	var digitmaxlength = 0;
	var timeoutpin = 7500;
	var timeouttransfer = 7500;

	function mycb( session, type, obj, arg ) {
		try {
			if ( type == "dtmf" ) {
				console_log( "info", "digit: "+obj.digit+"\n" );
				if ( obj.digit == "#" ) {
					//console_log( "info", "detected pound sign.\n" );
					exit = true;
					return( false );
				}
				dtmf.digits += obj.digit;
				if ( dtmf.digits.length >= digitmaxlength ) {
					exit = true;
					return( false );
				}
			}
		} catch (e) {
			console_log( "err", e+"\n" );
		}
		return( true );
	} //end function mycb


	var dtmf = new Object( );
	dtmf.digits = "";

	if ( session.ready( ) ) {
		session.answer( );

		// if admin_pin has been defined then request the pin number
		if (admin_pin.length > 0) {
			digitmaxlength = 6;
			session.streamFile( sounds_dir+"/custom/8000/please_enter_the_pin_number.wav", mycb, "dtmf");
			session.collectInput( mycb, dtmf, timeoutpin );
			//console_log( "info", "adming pin: " + dtmf.digits + "\n" );
		}

		if (dtmf.digits == admin_pin || admin_pin.length == 0) {

			//console_log( "info", "Roku pin is correct\n" );

			dtmf.digits = ""; //clear dtmf digits to prepare for next dtmf request
			digitmaxlength = 1;
			session.streamFile( sounds_dir+"/custom/8000/please_enter_the_phone_number.wav", mycb, "dtmf");
			//session.collectInput( mycb, dtmf, timeouttransfer );
			var x = 0;
			while (true) {
				//collect the remaining digits (allow up to 10 additional digits)
				dtmf.digits = session.getDigits(1, "#", 10000);

				if (dtmf.digits.length > 0) {
					//press star to exit
					if (dtmf.digits == "*") {
						break;
					}
					console_log( "info", "Roku: " + dtmf.digits + "\n" );
					system("/fusionpbx/Program/php/php.exe /fusionpbx/Program/www/localhost/mod/roku/roku.php "+ dtmf.digits);
				}
				if (x > 17500) {
					break;
				}
				x++;
			}
			session.hangup("NORMAL_CLEARING");

		}
		else {
			session.streamFile( sounds_dir+"/custom/8000/your_pin_number_is_incorect_goodbye.wav", mycb, "dtmf");
			console_log( "info", "Roku Pin: " + dtmf.digits + " is incorrect\n" );
		}

	}
