<?php
/*
	FusionPBX
	Version: MPL 1.1

	The contents of this file are subject to the Mozilla Public License Version
	1.1 (the "License"); you may not use this file except in compliance with
	the License. You may obtain a copy of the License at
	http://www.mozilla.org/MPL/

	Software distributed under the License is distributed on an "AS IS" basis,
	WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
	for the specific language governing rights and limitations under the
	License.

	The Original Code is FusionPBX

	The Initial Developer of the Original Code is
	Mark J Crane <markjcrane@fusionpbx.com>
	Portions created by the Initial Developer are Copyright (C) 2008-2010
	the Initial Developer. All Rights Reserved.

	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>
*/
include "v_config_cli.php";

ob_end_clean();
ob_start();

echo "\n---------------------------------\n";


$phpversion = substr(phpversion(), 0, 1);
if ($phpversion == '4') {
	$faxemail = $_REQUEST["email"];
	$faxextension = $_REQUEST["extension"];
	$faxname = $_REQUEST["name"];
	$faxmessages = $_REQUEST["messages"];
	$faxretry = $_REQUEST["retry"];
}
else {
	$tmparray = explode("=", $_SERVER["argv"][1]);
	$faxemail = $tmparray[1];
	unset($tmparray);
	
	$tmparray = explode("=", $_SERVER["argv"][2]);
	$faxextension = $tmparray[1];
	unset($tmparray);

	$tmparray = explode("=", $_SERVER["argv"][3]);
	$faxname = $tmparray[1];
	unset($tmparray);

	$tmparray = explode("=", $_SERVER["argv"][4]);
	$faxmessages = $tmparray[1];
	unset($tmparray);

	$tmparray = explode("=", $_SERVER["argv"][5]);
	$faxretry = $tmparray[1];
	unset($tmparray);
}

//echo "faxemail $faxemail\n";
//echo "faxextension $faxextension\n";
//echo "faxname $faxname\n";
//echo "cd $dir_fax; /usr/bin/tiff2png ".$dir_fax.$faxname.".png\n";


$dir_fax = '/usr/local/freeswitch/storage/fax/'.$faxextension.'/inbox/';


$faxfilewarning="";
if (file_exists($dir_fax.$faxname.".tif")) {

	if (!file_exists($dir_fax.$faxname.".png")) {
		//cd /usr/local/freeswitch/storage/fax/9975/inbox/;/usr/local/bin/tiff2png /usr/local/freeswitch/storage/fax/9975/inbox/1001-2009-06-06-01-15-11.tif
		//echo "cd $dir_fax; /usr/bin/tiff2png ".$dir_fax.$faxname.".tif\n";
		$tmp_tiff2png = exec("which tiff2png");
		if (strlen($tmp_tiff2png) > 0) {
			exec("cd ".$dir_fax."; ".$tmp_tiff2png." ".$dir_fax.$faxname.".tif");
		}
	}

	if (!file_exists($dir_fax.$faxname.".pdf")) {
		//echo "cd $dir_fax; /usr/bin/tiff2pdf -f -o ".$faxname.".pdf ".$dir_fax.$faxname.".tif\n";
		$tmp_tiff2pdf = exec("which tiff2pdf");
		if (strlen($tmp_tiff2pdf) > 0) {
			exec("cd ".$dir_fax."; ".$tmp_tiff2pdf." -f -o ".$faxname.".pdf ".$dir_fax.$faxname.".tif");
		}
	}
} else {
  $faxfilewarning=" Fax image not available on server.";
}


$tmp_subject = "Fax Received: ".$faxname;
$tmp_textplain  = "\nFax Received:\n";
$tmp_textplain .= "Name: ".$faxname."\n";
$tmp_textplain .= "Extension: ".$faxextension."\n";
$tmp_textplain .= "Messages: ".$faxmessages."\n";
$tmp_textplain .= $faxfilewarning."\n";
if ($faxretry=='yes') {
  $tmp_textplain .= "This message arrived earlier and has been queued until now due to email server issues.\n";
}
$tmp_texthtml = $tmp_textplain;


ini_set(max_execution_time,900); //15 minutes
ini_set('memory_limit', '96M');

$fp = fopen("/tmp/faxtoemail.txt", "w");



//send the email

	include "class.phpmailer.php";
	include "class.smtp.php"; // optional, gets called from within class.phpmailer.php if not already loaded

	$mail = new PHPMailer();

	$mail->IsSMTP(); // set mailer to use SMTP
	if ($v_smtpauth == "true") {
		$mail->SMTPAuth = $v_smtpauth; // turn on/off SMTP authentication
	}
	$mail->Host   = $v_smtphost;
	if (strlen($v_smtpsecure)>0) {
		$mail->SMTPSecure = $v_smtpsecure;
	}
	if ($v_smtpusername) {
		$mail->Username = $v_smtpusername;
		$mail->Password = $v_smtppassword;
	}
	$mail->SMTPDebug  = 2;

	echo "v_smtpfrom: $v_smtpfrom\n";
	echo "v_smtpfromname: $v_smtpfromname\n";
	echo "tmp_subject: $tmp_subject\n";

	$mail->From       = $v_smtpfrom;
	$mail->FromName   = $v_smtpfromname;
	$mail->Subject    = $tmp_subject;
	$mail->AltBody    = $tmp_textplain;   // optional, comment out and test
	$mail->MsgHTML($tmp_texthtml);


	$tmp_to = $faxemail;
	$tmp_to = str_replace(";", ",", $tmp_to);
	$tmp_to_array = split(",", $tmp_to);
	foreach($tmp_to_array as $tmp_to_row) {
		if (strlen($tmp_to_row) > 0) {
			echo "tmp_to_row: $tmp_to_row\n";
			$mail->AddAddress($tmp_to_row);
		}
	}

	if (strlen($faxname) > 0) {
		if (!file_exists($dir_fax.$faxname.".pdf")) {
			$mail->AddAttachment($dir_fax.$faxname.'.tif');  // tif attachment
		}
		if (file_exists($dir_fax.$faxname.".pdf")) {
			$mail->AddAttachment($dir_fax.$faxname.'.pdf');  // pdf attachment
		}
		if (!file_exists($dir_fax.$faxname.".pdf")) {
			if (file_exists($dir_fax.$faxname.".png")) {
				$mail->AddAttachment($dir_fax.$faxname.'.png');  // png attachment
			}
		}
		//$filename='fax.tif'; $encoding = "base64"; $type = "image/tif";
		//$mail->AddStringAttachment(base64_decode($strfax),$filename,$encoding,$type);
	}

	if(!$mail->Send()) {
		echo "Mailer Error: " . $mail->ErrorInfo;
		$email_status=$mail;
	}
	else {
		echo "Message sent!";
		$email_status="ok";
	}

//echo "test:".$faxresult.$faxsender.$faxpages;

$content = ob_get_contents(); //get the output from the buffer
ob_end_clean(); //clean the buffer

fwrite($fp, $content);
fclose($fp);


// the following files are created:
//     /usr/local/freeswitch/storage/fax
//        emailedfaxes.log - this is a log of all the faxes we have successfully emailed.  (note that we need to work out how to rotate this log)
//        failedfaxemails.log - this is a log of all the faxes we have failed to email.  This log is in the form of instructions that we can re-execute in order to retry.
//            Whenever this exists there should be an at job present to run it sometime in the next 3 minutes (check with atq).  If we succeed in sending the messages
//            this file will be removed.
//     /tmp
//        faxemailretry.sh - this is the renamed failedfaxemails.log and is created only at the point in time that we are trying to re-send the emails.  Note however
//            that this will continue to exist even if we succeed as we do not delete it when finished.
//        failedfaxemails.sh - this is created when we have a email we need to re-send.  At the time it is created, an at job is created to execute it in 3 minutes time,
//            this allows us to try sending the email again at that time.  If the file exists but there is no at job this is because there are no longer any emails queued
//            as we have successfully sent them all.
$faxtoemailqueuedir="/usr/local/freeswitch/storage/fax";
// note that we need to IDENTIFY the error condition and only make this happen if the error occurs - currently we do it every time and this is bad!
if ($email_status == 'ok') {
	$fp = fopen($faxtoemailqueuedir."/emailedfaxes.log", "a");
	fwrite($fp, $faxname." received on ".$faxextension." emailed to ".$faxemail." ".$faxmessages."\n");
	fclose($fp);
} else {
	// create an instruction log to email messages once the connection to the mail server has been restored
	$fp = fopen($faxtoemailqueuedir."/failedfaxemails.log", "a");
	fwrite($fp, "/usr/bin/php /var/www/fusionpbx/secure/fax_to_email.php email=$faxemail extension=$faxextension name=$faxname messages='$faxmessages' retry=yes\n");
	fclose($fp);

	// create a script to do the delayed mailing
	$fp = fopen("/tmp/failedfaxemails.sh", "w");
	fwrite($fp, "rm /tmp/faxemailretry.sh\n");
	fwrite($fp, "mv ".$faxtoemailqueuedir."/failedfaxemails.log /tmp/faxemailretry.sh\n");
	fwrite($fp, "chmod 777 /tmp/faxemailretry.sh\n");
	fwrite($fp, "/tmp/faxemailretry.sh\n");
	fclose($fp);
	$tmp_response = exec("chmod 777 /tmp/failedfaxemails.sh");
	// note we use batch in order to execute when system load is low.  Alternatively this could be replaced with AT.
	$tmp_response = exec("batch -f /tmp/failedfaxemails.sh now + 3 minutes");
}

?>

