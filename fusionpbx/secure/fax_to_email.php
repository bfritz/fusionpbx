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
}

//echo "faxemail $faxemail\n";
//echo "faxextension $faxextension\n";
//echo "faxname $faxname\n";
//echo "cd $dir_fax; /usr/bin/tiff2png ".$dir_fax.$faxname.".png\n";


$dir_fax = '/usr/local/freeswitch/storage/fax/'.$faxextension.'/inbox/';


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


$tmp_subject = "Fax Received: ".$faxname;
$tmp_textplain  = "\nFax Received:\n";
$tmp_textplain .= "Name: ".$faxname."\n";
$tmp_textplain .= "Extension: ".$faxextension."\n";
$tmp_texthtml = $tmp_textplain;


ini_set(max_execution_time,900); //15 minutes
ini_set('memory_limit', '96M');

$fp = fopen("/tmp/faxtoemail.txt", "w");



//send the email

	include "class.phpmailer.php";
	include "class.smtp.php"; // optional, gets called from within class.phpmailer.php if not already loaded

	$mail = new PHPMailer();

	$mail->IsSMTP();                  	// set mailer to use SMTP
	if ($v_smtpauth == "true") {
		$mail->SMTPAuth = $v_smtpauth;      // turn on/off SMTP authentication
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
		$mail->AddAttachment($dir_fax.$faxname.'.tif');  // tif attachment
		$mail->AddAttachment($dir_fax.$faxname.'.pdf');  // pdf attachment
		$mail->AddAttachment($dir_fax.$faxname.'.png');  // png attachment
		//$filename='fax.tif'; $encoding = "base64"; $type = "image/tif";
		//$mail->AddStringAttachment(base64_decode($strfax),$filename,$encoding,$type);
	}

	if(!$mail->Send()) {
		echo "Mailer Error: " . $mail->ErrorInfo;
	}
	else {
		echo "Message sent!";
	}


$content = ob_get_contents(); //get the output from the buffer
ob_end_clean(); //clean the buffer

fwrite($fp, $content);
fclose($fp);

?>