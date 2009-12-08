<?php
/* $Id$ */
/*
	fax_to_email.php
	Copyright (C) 2008 Mark J Crane
	All rights reserved.

	FreeSWITCH (TM)
	http://www.freeswitch.org/

	Redistribution and use in source and binary forms, with or without
	modification, are permitted provided that the following conditions are met:

	1. Redistributions of source code must retain the above copyright notice,
	   this list of conditions and the following disclaimer.

	2. Redistributions in binary form must reproduce the above copyright
	   notice, this list of conditions and the following disclaimer in the
	   documentation and/or other materials provided with the distribution.

	THIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES,
	INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY
	AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
	AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
	OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
	SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
	INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
	CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
	ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
	POSSIBILITY OF SUCH DAMAGE.
*/
if(!isset($_SERVER["DOCUMENT_ROOT"])) { $_SERVER["DOCUMENT_ROOT"]=substr($_SERVER['SCRIPT_FILENAME'] , 0 , -strlen($_SERVER['PHP_SELF'])+1 );}
require_once $_SERVER["DOCUMENT_ROOT"]."/includes/config.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/includes/checkauth.php";
if (ifgroup("admin") || ifgroup("tenant")) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

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
//echo "cd $dir_fax; /usr/local/bin/tiff2png ".$dir_fax.$faxname.".png\n";


$dir_fax = '/usr/local/freeswitch/storage/fax/'.$faxextension.'/inbox/';


if (!file_exists($dir_fax.$faxname.".png")) {
	//cd /usr/local/freeswitch/storage/fax/9975/inbox/;/usr/local/bin/tiff2png /usr/local/freeswitch/storage/fax/9975/inbox/1001-2009-06-06-01-15-11.tif
	//echo "cd $dir_fax; /usr/local/bin/tiff2png ".$dir_fax.$faxname.".tif\n";
	exec("cd $dir_fax; /usr/local/bin/tiff2png ".$dir_fax.$faxname.".tif");
}

if (!file_exists($dir_fax.$faxname.".pdf")) {
	//echo "cd $dir_fax; /usr/local/bin/tiff2pdf -f -o ".$faxname.".pdf ".$dir_fax.$faxname.".tif\n";
	exec("cd $dir_fax; /usr/local/bin/tiff2pdf -f -o ".$faxname.".pdf ".$dir_fax.$faxname.".tif");
}


$tmp_subject = "Fax Received: ".$faxname;
$tmp_textplain  = "\nFax Received:\n";
$tmp_textplain .= "Name: ".$faxname."\n";
$tmp_textplain .= "Extension: ".$faxextension."\n";
$tmp_texthtml = $tmp_textplain;


$tmp_smtphost   = $config['installedpackages']['freeswitchsettings']['config'][0]['smtphost'];
$tmp_smtpsecure = $config['installedpackages']['freeswitchsettings']['config'][0]['smtpsecure'];  //options "", "TLS", "SSL"
$tmp_smtpsecure = strtolower($tmp_smtpsecure);
$tmp_smtpauth = $config['installedpackages']['freeswitchsettings']['config'][0]['smtpauth'];     // SMTP authentication: true or false
$tmp_smtpusername = $config['installedpackages']['freeswitchsettings']['config'][0]['smtpusername'];
$tmp_smtppassword = $config['installedpackages']['freeswitchsettings']['config'][0]['smtppassword'];
$tmp_smtpfrom = $config['installedpackages']['freeswitchsettings']['config'][0]['smtpfrom'];
$tmp_smtpfromname = $config['installedpackages']['freeswitchsettings']['config'][0]['smtpfromname'];

ini_set(max_execution_time,900); //15 minutes
ini_set('memory_limit', '96M');
$fd = fopen("php://stdin", "r");

$email = file_get_contents ("php://stdin");

fclose($fd);

if($fd){
    $fp = fopen("/tmp/faxtoemail.txt", "w");
}



//send the email

      include "/usr/local/www/packages/freeswitch/class.phpmailer.php";
      include "/usr/local/www/packages/freeswitch/class.smtp.php"; // optional, gets called from within class.phpmailer.php if not already loaded

      $mail = new PHPMailer();

      $mail->IsSMTP();                  	// set mailer to use SMTP
      if ($tmp_smtpauth == "true") {
          $mail->SMTPAuth = $tmp_smtpauth;      // turn on/off SMTP authentication
      }
      $mail->Host   = $tmp_smtphost;
      if (strlen($tmp_smtpsecure)>0) {
          $mail->SMTPSecure = $tmp_smtpsecure;
      }
      if ($tmp_smtpusername) {
          $mail->Username = $tmp_smtpusername;
          $mail->Password = $tmp_smtppassword;
      }
      $mail->SMTPDebug  = 2;

      echo "tmp_smtpfrom: $tmp_smtpfrom\n";
      echo "tmp_smtpfromname: $tmp_smtpfromname\n";
      echo "tmp_subject: $tmp_subject\n";

      $mail->From       = $tmp_smtpfrom;
      $mail->FromName   = $tmp_smtpfromname;
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