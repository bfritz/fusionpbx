<?php
/* $Id$ */
/*
	v_cdr_import.php
	Copyright (C) 2008 Mark J Crane
	All rights reserved.

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
include "root.php";
require "includes/config.php";
if(php_sapi_name() == 'cli') {
	//allow access for command line interface
}
else {
	//require authentication
	require_once "includes/checkauth.php";
	if (ifgroup("member") || ifgroup("admin") || ifgroup("superadmin")) {
		//access granted
	}
	else {
		echo "access denied";
		exit;
	}
}

require "includes/lib_cdr.php";

//---- begin import cdr records -----------------------------------------------------------------------------------

	$v_file = $v_log_dir."/cdr-csv/Master.csv";
	//echo filesize($v_file);
	//Open file (DON'T USE a+ pointer will be wrong!)
	$fh = fopen($v_file, 'r');
	
	$read = 524288;
	//$read = 524288;
	//$read = 1048576;
	//$read = 16777216; //Read 16meg chunks
	$x = 0;
	$part = 0;
	$strcount=0;
	while(!feof($fh)) {
		$rbuf = fread($fh, $read);
		for($i=$read;$i > 0 || $n == chr(10);$i--) {
			$n=substr($rbuf, $i, 1);
			if($n == chr(10))break;
				//If we are at the end of the file, just grab the rest and stop loop
			elseif(feof($fh)) {
				$i = $read;
				$buf = substr($rbuf, 0, $i+1);
				break;
		   }
		}
		$count = $db->exec("BEGIN;"); //returns affected rows

		//This is the buffer we want to do stuff with, maybe thow to a function?
		$buf = substr($rbuf, 0, $i+1);
		$buf = str_replace("{v_id}", $v_id, $buf);
		$totalsize = strlen($buf)+$totalsize;

		$lnarray = explode ("\n", $buf);
		//print_r($lnarray);

		$columnvaluecount=0;
		foreach($lnarray as $sql) {

			//--- Begin SQLite -------------------------------------

					if (strlen($sql) > 0) {
						//echo $sql."<br /><br />\n";
						$count = $db->exec(check_sql($sql)); //returns affected rows

						$x++;
						if ($x > 10000) {
							$count = $db->exec("COMMIT;"); //returns affected rows
							$count = $db->exec("BEGIN;"); //returns affected rows
						}

					}
					//echo "Affected Rows: ".$count."<br>";
					//echo "Last Insert Id: ".$db->lastInsertId($id)."<br>";
					unset($sql);

			//---EndSQLite-------------------------------------

			//if ($columnvaluecount > 10) { break; }
			$columnvaluecount++;
		}

		//Point marker back to last \n point
		$part = ftell($fh)-($read-($i+1));
		fseek($fh, $part);
		if ($strcount >= 5000) { break; } //handle up to a gig file
		$strcount++;

	}

	$count = $db->exec("COMMIT;"); //returns affected rows
	fclose($fh);

	//truncate the file now that it has been processed
		$fh = fopen($v_file, 'w');
		fclose($fh);


//---- begin import cdr records -----------------------------------------------------------------------------------

?>
