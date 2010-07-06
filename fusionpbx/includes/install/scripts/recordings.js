  include("config.js");
    //admin_pin defined in config.js

  //var admin_pin = ""; //don't require a pin
    //if you choose not to require a pin then then you may want to add a dialplan condition for a specific caller id

 var digitmaxlength = 0;
 var timeoutpin = 7500;
 var timeouttransfer = 7500;
 var objdate = new Date();

 var adjusthours = 0; //Adjust Server time that is set to GMT 7 hours
 var adjustoperator = "-"; //+ or -

 if (adjustoperator == "-") {
   var objdate2 = new Date(objdate.getFullYear(),objdate.getMonth(),objdate.getDate(),(objdate.getHours() - adjusthours),objdate.getMinutes(),objdate.getSeconds());
 }
 if (adjustoperator == "+") {
   var objdate2 = new Date(objdate.getFullYear(),objdate.getMonth(),objdate.getDate(),(objdate.getHours() + adjusthours),objdate.getMinutes(),objdate.getSeconds());
 }

 var Hours = objdate2.getHours();
 var Mins = objdate2.getMinutes();
 var Seconds = objdate2.getSeconds();
 var Month = objdate2.getMonth() + 1;
 var Date = objdate2.getDate();
 var Year = objdate2.getYear()
 var Day = objdate2.getDay()+1;
 var exit = false;


  function mycb( session, type, data, arg ) {
     if ( type == "dtmf" ) {
       //console_log( "info", "digit: "+data.digit+"\n" );
       if ( data.digit == "#" ) {
         //console_log( "info", "detected pound sign.\n" );
         return( true );
       }
       dtmf.digits += data.digit;

       if ( dtmf.digits.length < digitmaxlength ) {
         return( true );
       }
     }
     return( false );
  }

  //console_log( "info", "Recording Request\n" );

  var dtmf = new Object( );
  dtmf.digits = "";

  if ( session.ready( ) ) {
      session.answer( );


  if (admin_pin.length > 0) {
      digitmaxlength = 6;
      session.execute("set", "playback_terminators=#");
      session.streamFile( "C:/fusionpbx/program/FreeSWITCH/sounds/custom/please_enter_the_pin_number.wav", mycb, "dtmf");
      session.collectInput( mycb, dtmf, timeoutpin );
  }

  if (dtmf.digits == admin_pin || admin_pin.length == 0) {
      session.streamFile( "C:/fusionpbx/program/FreeSWITCH/sounds/custom/begin_recording.wav", mycb, "dtmf");
      session.execute("set", "playback_terminators=#");
      session.execute("record", "C:/fusionpbx/program/FreeSWITCH/recordings/temp"+Year+Month+Day+Hours+Mins+Seconds+".wav 180 200");
  }
  else {
      console_log( "info", "Pin: " + dtmf.digits + " is incorrect\n" );
      session.streamFile( "C:/fusionpbx/program/FreeSWITCH/sounds/custom/your_pin_number_is_incorect_goodbye.wav", mycb, "dtmf");
  }
  session.hangup();

 }