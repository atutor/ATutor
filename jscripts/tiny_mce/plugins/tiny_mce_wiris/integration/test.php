<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<?php

//
//  Copyright (c) 2011, Maths for More S.L. http://www.wiris.com
//  This file is part of WIRIS Plugin.
//
//  WIRIS Plugin is free software: you can redistribute it and/or modify
//  it under the terms of the GNU General Public License as published by
//  the Free Software Foundation, either version 3 of the License, or
//  any later version.
//
//  WIRIS Plugin is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
//  GNU General Public License for more details.
//
//  You should have received a copy of the GNU General Public License
//  along with WIRIS Plugin. If not, see <http://www.gnu.org/licenses/>.
//

require_once 'api.php';

function wrs_assert_simple($condition) {
	if ($condition) {
		return '<span class="ok">OK</span>';
	}
	else {
		return '<span class="error">ERROR</span>';
	}
}

function wrs_assert($condition, $report_text, $solution_link) {
	if ($condition){
		return $report_text;
	}
	else{
		$report_text = '<span class="error">' . $report_text . '</span>';
		if ($solution_link != ''){
			$report_text .= '<a target="_blank" href="' . $solution_link . '"><img alt="" src="../core/help.gif" /></a>';
		}
		return $report_text;
	}
}

function wrs_createTableRow($test_name, $report_text, $solution_link, $condition, $error=true){
	$output = '<td>' . $test_name . '</td>';
	if ($error){
		$output .= '<td>' . wrs_assert($condition, $report_text, $solution_link) . '</td>';
		$output .= '<td>' . wrs_assert_simple($condition) . '</td>';
	}else{
		$output .= '<td>' . $report_text . '</td>';
		$output .= '<td></td>';
	}
	return $output;
}

?>
<html>
	<head>
		<title>WIRIS plugin test page</title>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />

		<style type="text/css">
			body{font-family: Arial;}
			span{font-weight: bold;}
			span.ok {color: #009900;}
			span.error {color: #dd0000;}
			table, th, td, tr {
				border: solid 1px #000000;
				border-collapse:collapse;
				padding: 5px;
			}
			th{background-color: #eeeeee;}
			img{border:none;}
		</style>
	</head>
	
	<body>
		<h1>WIRIS plugin test page</h1>
		
		<table>
				<tr>
					<th>Test</th>
					<th>Report</th>
					<th>Status</th>
				</tr>
				<tr>
				<?php
					$test_name = 'WIRIS plugin version';
					$file = '../VERSION';
					if (@fopen($file, 'r')){
						$content = file($file);
						$report_text = '<b>' . $content[0] . '</b>';
					}else{
						$report_text = "";
					}
					$solution_link = '';
					echo wrs_createTableRow($test_name, $report_text, $solution_link, @fopen($file, 'r') !== false);
				?>				
				</tr>				
				<tr>
				<?php
					$moodle = false;
					if (file_exists(dirname(__FILE__) . '/../../../../../../../../config.php')){
						require_once dirname(__FILE__) . '/../../../../../../../../config.php';                                
						$moodle = true;
					}
					$test_name = 'Loading configuration';
					$report_text = 'Loading ' . WRS_CONFIG_FILE;
					$solution_link = '';
					$config = wrs_loadConfig(WRS_CONFIG_FILE);
					echo wrs_createTableRow($test_name, $report_text, $solution_link, !empty($config));
				?>				
				</tr>
				<tr>
				<?php
					if ($moodle){
						$test_name = 'Checking $CFG';
						$report_text = '';
						$solution_link = '';
						$exists = false;
						$handle = fopen($CFG->dirroot . "/config.php", "r");
						if ($handle) {
							$needle = 'global $CFG;';
							while (($buffer = fgets($handle)) !== false) {
								if (strpos($buffer, $needle) !== false){
									$exists = true;
								}
							}
							fclose($handle);
						}                         
						if (!$exists){
							$report_text = 'global $CFG not found';
						}else{
							$report_text = 'global $CFG found.';
						}
						echo wrs_createTableRow($test_name, $report_text, $solution_link, $exists);
					}
				?>				
				</tr>      
				<tr>
				<?php
				$test_name = 'Checking proxy';
				$report_text = 'Disabled';
				$solution_link = '';
				if (isset($config['wirisproxy']) && $config['wirisproxy'] == 'true'){
					$conn = @fsockopen($config['wirisproxy_host'], $config['wirisproxy_port']);
					if ($conn){
						$report_text = 'Enabled';
						echo wrs_createTableRow($test_name, $report_text, $solution_link, true);
					}else{
						$report_text = 'Enabled but a connection error has been encountered.';    
						echo wrs_createTableRow($test_name, $report_text, $solution_link, false);    
					}
				}else{
					echo wrs_createTableRow($test_name, $report_text, $solution_link, true);    
				}
				?>				
				</tr>
				<tr>
				<?php
				if (!isset($config['wirisproxy']) || $config['wirisproxy'] != 'true'){
					$test_name = 'Checking if WIRIS server is reachable';    
					$report_text = 'Connecting to ' . $config['wirisimageservicehost'];
					$solution_link = '';
					echo wrs_createTableRow($test_name, $report_text, $solution_link, @fsockopen($config['wirisimageservicehost'], '80') && fsockopen($config['wirisimageservicehost'], '443'));
				}
				?>				
				</tr>
				<tr>
				<?php
					$test_name = 'Connecting to WIRIS image server';
					if (function_exists('curl_init')){
						$report_text = 'Using cURL to ' . $config['wirisimageservicehost'];
						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, $config['wirisimageservicehost']);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
						$result = curl_exec($ch);
						curl_close($ch);
					}else{
						$report_text = 'Using file_get_contents to ' . $config['wirisimageservicehost'];
						$result = file_get_contents('http://' . $config['wirisimageservicehost']);
					}				
					if ($result !== false){
						$result = true;
					}
					$solution_link = '';
					echo wrs_createTableRow($test_name, $report_text, $solution_link, $result);
				?>				
				</tr>		
				<tr>
				<?php
					$file = wrs_getFormulaDirectory($config) . '/test.xml';
					$test_name = 'Writing a formula file';
					$report_text = 'Writing file ' . $file;
					$solution_link = '';
					echo wrs_createTableRow($test_name, $report_text, $solution_link, @fopen($file, 'w') !== false);
				?>				
				</tr>
				<tr>
				<?php
					$file = wrs_getFormulaDirectory($config) . '/test.xml';
					$test_name = 'Reading a formula file';
					$report_text = 'Reading file ' . $file;
					$solution_link = '';
					echo wrs_createTableRow($test_name, $report_text, $solution_link, @fopen($file, 'r') !== false);
				?>				
				</tr>	
				<tr>
				<?php
					$file = wrs_getCacheDirectory($config) . '/test.png';
					$test_name = 'Writing an image file';
					$report_text = 'Writing file ' . $file;
					$solution_link = '';
					echo wrs_createTableRow($test_name, $report_text, $solution_link, @fopen($file, 'w') !== false);
				?>				
				</tr>	
				<tr>
				<?php
					$file = wrs_getCacheDirectory($config) . '/test.png';
					$test_name = 'Reading an image file';
					$report_text = 'Reading file ' . $file;
					$solution_link = '';
					echo wrs_createTableRow($test_name, $report_text, $solution_link, @fopen($file, 'r') !== false);
				?>				
				</tr>	
				<tr>
				<?php
					$test_name = 'Creating a random image';
					$mathml='<math xmlns="http://www.w3.org/1998/Math/MathML"><mrow><mn>' . rand(0,9999) . '</mn><mo>+</mo><mn>' . rand(0,9999) . '</mn></mrow></math>';
					$api = new com_wiris_plugin_PluginAPI;
					$src = $api->mathml2img($mathml, 'http://' . dirname($_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"]));
					$query = parse_url($src, PHP_URL_QUERY);
					$query_array = array();
					parse_str($query, $query_array);
					$report_text = '<img align="middle" src="' . $src . '" />';
					$solution_link = '';
					$imageFile = wrs_getCacheDirectory($config) . '/' . $query_array['formula'];
					//Used to create the image
					@fopen($src, 'r');
					//*******
					echo wrs_createTableRow($test_name, $report_text, $solution_link, file_exists($imageFile));
				?>				
				</tr>	
		</table>

		<br/>
		<h1>PHP tests</h1>
		<h3>Checking the existence of PHP functions that WIRIS plugin uses</h3>
		
		<table>
			<tr>
				<th>Function</th>
				<th>Status</th>
			</tr>			
			<tr>
				<td>fclose</td>
				<td>
					<?php
						echo wrs_assert_simple(function_exists('fclose'));
					?>
				</td>					
			</tr>			
			<tr>
				<td>fgets</td>
				<td>
					<?php
						echo wrs_assert_simple(function_exists('fgets'));
					?>
				</td>					
			</tr>			
			<tr>
				<td>file_put_contents</td>
				<td>
					<?php
						echo wrs_assert_simple(function_exists('file_put_contents'));
					?>
				</td>					
			</tr>			
			<tr>
				<td>fopen</td>
				<td>
					<?php
						echo wrs_assert_simple(function_exists('fopen'));
					?>
				</td>					
			</tr>			
			<tr>
				<td>http_build_query</td>
				<td>
					<?php
						echo wrs_assert_simple(function_exists('http_build_query'));
					?>
				</td>					
			</tr>			
			<tr>
				<td>is_file</td>
				<td>
					<?php
						echo wrs_assert_simple(function_exists('is_file'));
					?>
				</td>					
			</tr>			
			<tr>
				<td>mb_strlen</td>
				<td>
					<?php
						echo wrs_assert_simple(function_exists('mb_strlen'));
					?>
				</td>					
			</tr>			
			<tr>
				<td>readfile</td>
				<td>
					<?php
						echo wrs_assert_simple(function_exists('readfile'));
					?>
				</td>					
			</tr>			
		</table>
                
	</body>
</html>
