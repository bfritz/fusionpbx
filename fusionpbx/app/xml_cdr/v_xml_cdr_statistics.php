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
	Portions created by the Initial Developer are Copyright (C) 2008-2012
	the Initial Developer. All Rights Reserved.

	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>
*/
require_once "root.php";
require_once "includes/require.php";
require_once "includes/checkauth.php";
if (permission_exists('xml_cdr_view')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

//additional includes
	require_once "xml_cdr_statistics_inc.php";
	require_once "includes/header.php";

//page title and description
	echo "<div align='center'>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
	echo "<tr>\n";
	echo "	<td width='30%' align='left' valign='top' nowrap='nowrap'><b>Call Detail Record Statistics</b></td>\n";
	echo "	<td width='70%' align='right' valign='top'>\n";
	echo "		<input type='button' class='btn' value='CSV' onclick=\"document.location.href='xml_cdr_statistics_csv.php';\">\n";
	echo "		<input type='button' class='btn' name='' alt='back' onclick=\"window.location='v_xml_cdr.php'\" value='Back'>\n";
	echo "	</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "		<td align='left' colspan='2'>\n";
	echo "			Call Detail Records Statics summarize the call information. \n";
	echo "			<br />\n";
	echo "			<br />\n";
	echo "		</td>\n";
	echo "</tr>\n";
	echo "</table>\n";

//set the style
	$c = 0;
	$row_style["0"] = "row_style0";
	$row_style["1"] = "row_style1";


	?>
	<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="/includes/jquery/flot/excanvas.min.js"></script><![endif]-->
    <script language="javascript" type="text/javascript" src="/includes/jquery/jquery-1.7.2.min.js"></script>
    <script language="javascript" type="text/javascript" src="/includes/jquery/flot/jquery.flot.js"></script>
	<table>
		<tr>
			<td align='left'>
				<div id="placeholder" style="width:700px;height:180px;"></div>
			</td>
			<td align='left' valign='top'>
				<p id="choices"></p>
			</td>
		</tr>
	</table>
	<script type="text/javascript">
	$(function () {
		var datasets = {
			"volume": {
				label: "Volume",
				data: <?php echo json_encode($graph['volume']); ?>
			},
			"minutes": {
				label: "Minutes",
				data: <?php echo json_encode($graph['minutes']); ?>
			},
			"call_per_min": {
				label: "Calls Per Min",
				data: <?php echo json_encode($graph['call_per_min']); ?>
			},
			"missed": {
				label: "Missed",
				data: <?php echo json_encode($graph['missed']); ?>
			},
			"asr": {
				label: "ASR",
				data: <?php echo json_encode($graph['asr']); ?>
			},
			"aloc": {
				label: "ALOC",
				data: <?php echo json_encode($graph['aloc']); ?>
			},		
		};

		// hard-code color indices to prevent them from shifting as
		// countries are turned on/off
		var i = 0;
		$.each(datasets, function(key, val) {
			val.color = i;
			++i;
		});
		
		// insert checkboxes 
		var choiceContainer = $("#choices");
		$.each(datasets, function(key, val) {
			choiceContainer.append('<br /><input type="checkbox" name="' + key +
								   '" checked="checked" id="id' + key + '">' +
								   '<label for="id' + key + '">'
									+ val.label + '</label>');
		});
		choiceContainer.find("input").click(plotAccordingToChoices);

		
		function plotAccordingToChoices() {
			var data = [];

			choiceContainer.find("input:checked").each(function () {
				var key = $(this).attr("name");
				if (key && datasets[key])
					data.push(datasets[key]);
			});

			if (data.length > 0)
				$.plot($("#placeholder"), data, {
					yaxis: { min: 0 },
					xaxis: { tickDecimals: 0 }
				});
		}

		plotAccordingToChoices();
	});
	</script>
	<?php

//show the results
	echo "<table width='100%' cellpadding='0' cellspacing='0'>\n";
	echo "<tr>\n";
	echo "	<th>Hours</th>\n";
	echo "	<th>Date</th>\n";
	echo "	<th nowrap='nowrap'>Time</th>\n";
	echo "	<th>Volume</th>\n";
	echo "	<th>Minutes</th>\n";
	echo "	<th>Calls Per Min</th>\n";
	echo "	<th>Missed</th>\n";
	echo "	<th>ASR</th>\n";
	echo "	<th>ALOC</th>\n";
	echo "</tr>\n";

	$i = 0;
	foreach ($stats as $row) {
		echo "<tr >\n";
		if ($i < 24) {
			echo "	<td valign='top' class='".$row_style[$c]."'>".($i+1)."</td>\n";
		}
		elseif ($i == 24) {
			echo "	<br /><br />\n";
			echo "</tr>\n";
			echo "<tr>\n";
			echo "	<td>\n";
			echo "		<br /><br />\n";
			echo "	</td>\n";
			echo "</tr>\n";
			echo "<tr>\n";
			echo "	<th nowrap='nowrap'>Days</th>\n";
			echo "	<th nowrap='nowrap'>Date</th>\n";
			echo "	<th nowrap='nowrap'>Time</th>\n";
			echo "	<th>Volume</th>\n";
			echo "	<th>Minutes</th>\n";
			echo "	<th nowrap='nowrap'>Calls Per Min</th>\n";
			echo "	<th>Missed</th>\n";
			echo "	<th>ASR</th>\n";
			echo "	<th>ALOC</th>\n";
			echo "</tr>\n";
			echo "<tr>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>1</td>\n";
		}
		elseif ($i == 25) {
			echo "<tr>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>7</td>\n";
		}
		elseif ($i == 26) {
			echo "	<td valign='top' class='".$row_style[$c]."'>30</td>\n";
		}
		if ($i < 24) {
			echo "	<td valign='top' class='".$row_style[$c]."'>".date('j M', $row['start_epoch'])."</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".date('H:i', $row['start_epoch'])." - ".date('H:i', $row['stop_epoch'])."&nbsp;</td>\n";
		}
		else {
			echo "	<td valign='top' class='".$row_style[$c]."'>".date('j M', $row['start_epoch'])."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".date('H:i', $row['start_epoch'])." - ".date('j M H:i', $row['stop_epoch'])."&nbsp;</td>\n";
		}
		echo "	<td valign='top' class='".$row_style[$c]."'>".$row['volume']."&nbsp;</td>\n";
		echo "	<td valign='top' class='".$row_style[$c]."'>".(round($row['minutes'],2))."&nbsp;</td>\n";
		echo "	<td valign='top' class='".$row_style[$c]."'>".(round($row['avg_min'],2))."&nbsp;</td>\n";
		echo "	<td valign='top' class='".$row_style[$c]."'>".$row['missed']."&nbsp;</td>\n";
		echo "	<td valign='top' class='".$row_style[$c]."'>".(round($row['asr'],2))."&nbsp;</td>\n";
		echo "	<td valign='top' class='".$row_style[$c]."'>".(round($row['aloc'],2))."&nbsp;</td>\n";
		echo "</tr >\n";
		if ($c==0) { $c=1; } else { $c=0; }
		$i++;
	}
	echo "</table>\n";

//show the footer
	require_once "includes/footer.php";
?>