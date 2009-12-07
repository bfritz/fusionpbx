include("config.js");
	//var sounds_dir
	//var admin_pin
	//var tmp_dir
	//var recordings_dir

var sipuri = argv[0];
var recording = argv[1];
var caller_id_name = argv[2];
var caller_id_number = argv[3];
var call_timeout = argv[4];
var tmp_sipuri;

caller_id_name = caller_id_name.replace("+", " ");
console_log( "info", "sipuri: "+sipuri+"\n" );
console_log( "info", "recording: "+recording+"\n" );
console_log( "info", "caller_id_name: "+caller_id_name+"\n" );
console_log( "info", "caller_id_number: "+caller_id_number+"\n" );
console_log( "info", "call_timeout: "+call_timeout+"\n" );

function originate (sipuri, recording, caller_id_name, caller_id_number, call_timeout) {

	var dtmf = new Object();
	var cid;
	dtmf.digits = "";
	cid = ",origination_caller_id_name="+caller_id_name+",origination_caller_id_number="+caller_id_number;

	new_session = new Session("{ignore_early_media=true"+cid+"}"+sipuri);
	if (call_timeout > 0) {
		new_session.execute("set", "call_timeout="+call_timeout);
		console_log( "info", "call_timeout2: "+call_timeout+"\n" );
	}

	if ( new_session.ready() ) {

		console_log( "info", "followme: new_session uuid "+new_session.uuid+"\n" );
		console_log( "info", "followme: no dtmf detected\n" );

		digitmaxlength = 1;
		while (new_session.ready()) {

			if (recording.length > 0) {
				new_session.streamFile( recordings_dir+"/"+recording);
			}

			if (new_session.ready()) {
				if (dtmf.digits.length == 0) {
					dtmf.digits +=  new_session.getDigits(1, "#", 10000); // 10 seconds
					if (dtmf.digits.length == 0) {
					}
					else {
						break; //dtmf found end the while loop
					}
				}
			}
			break;
		}

		if ( dtmf.digits.length > "0" ) {
			if ( dtmf.digits == "1" ) {
				console_log( "info", "followme: call accepted\n" ); //accept
				//new_session.execute("fifo", extension+"@${domain_name} out nowait");
				return true;
			}
			else if ( dtmf.digits == "2" ) {
				console_log( "info", "followme: call rejected\n" ); //reject
				new_session.hangup;
				return false;
			}
			else if ( dtmf.digits == "3" ) {
				console_log( "info", "followme: call sent to voicemail\n" ); //reject
				new_session.hangup;
				exit;
				return true;
			}
		}
		else {
			console_log( "info", "followme: no dtmf detected\n" ); //reject
			new_session.hangup;
			return false;
		}

	}
}

sipuri_array = sipuri.split(",");
for (i = 0; i < sipuri_array.length; i++){
	tmp_sipuri = sipuri_array[i];
	console_log("info", "tmp_sipuri: "+tmp_sipuri);
	result = originate (tmp_sipuri, recording, caller_id_name, caller_id_number, call_timeout);
	if (result) {
		break;
		exit;
	}
}