<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg, Heidi Hazelton	*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

if (!defined('AT_INCLUDE_PATH')) { exit; }

require('../svn.php');

$svn_data   = explode("\n", $svn_data);

if (substr($svn_data[1], 0, 1) == 'r') {
	$svn_data = $svn_data[1];
} else if (substr($svn_data[2], 0, 1) == 'r') {
	$svn_data = $svn_data[2];
}

if (count($svn_data) > 1) {
	$build = 'unknown';
	$build_date = date('Y-m-d H:i:s');
} else {
	$svn_data   = explode(' ', $svn_data);

	$build      = $svn_data[0];
	$build_date = $svn_data[4] .' '. $svn_data[5];
}

if (!$build) {
	$build = 'unknown';
}

$os = php_uname('s') . ' '. php_uname('r'). ' '. php_uname('v'). ' '. php_uname('m');


if (isset($_POST['submit'])) {
	unset($_POST['submit']);
	unset($action);

	if ($_POST['log_yes']) {

		$request  = '&upgrade=' . urlencode(stripslashes($addslashes($_POST['log_upgrade'])));
		$request .= '&version=' . urlencode(stripslashes($addslashes($new_version)));
		$request .= '&build='   . urlencode(stripslashes($addslashes($build)));
		$request .= '&build_date='   . urlencode(stripslashes($addslashes($build_date)));
		$request .= '&os='      . urlencode(stripslashes($addslashes($_POST['log_os'])));
		$request .= '&server='  . urlencode(stripslashes($addslashes($_POST['log_server'])));
		$request .= '&php='     . urlencode(stripslashes($addslashes($_POST['log_php'])));
		$request .= '&mysql='   . urlencode(stripslashes($addslashes($_POST['log_mysql'])));
		if ($_POST['log_url_yes']) {
			$request .= '&url=' . urlencode(stripslashes($addslashes($_POST['log_url'])));
		}

		$header = "POST /install_log.php HTTP/1.1\r\n";
		$header .= "Host: atutor.ca\r\n";
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= "Content-Length: " . strlen($request) . "\r\n\r\n";
		$fp = fsockopen('www.atutor.ca', 80, $errno, $errstr, 30);

		if ($fp) {
			fputs ($fp, $header . $request . "\r\n\r\n");
			while (!feof($fp)) {
				$res .= fgets ($fp, 1024);
			}
			fclose($fp);
		}
	}

	store_steps($step);
	$step++;
	return;
}


print_progress($step);

?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="step" value="<?php echo $step; ?>" />
<?php
	if ($_POST['step1']['old_path'] != '') {
		echo '<input type="hidden" name="log_upgrade" value="1" />';
	} else {
		echo '<input type="hidden" name="log_upgrade" value="0" />';
	}
		print_hidden($step);
	?>
<br />
	<table width="80%" class="tableborder" cellspacing="0" cellpadding="1" align="center">	
	<tr>
		<td class="row1" colspan="2">Would you like to send the following information to the atutor.ca server anonymously? The information we gather helps us plan our development resources to better suit the needs of the community.</td>
	</tr>
	<tr>
		<td class="row1" width="20%"><b>ATutor Version:</b></td>
		<td class="row1"><?php echo $new_version; ?> (build <?php echo $build . ' - '.$build_date; ?>)</td>
	</tr>
	<tr>
		<td class="row1" nowrap="nowrap"><b>Operating System:</b></td>
		<td class="row1"><?php echo $os; ?> <input type="hidden" name="log_os" value="<?php echo $os; ?>" /></td>
	</tr>
	<tr>
		<td class="row1"><b>Web Server:</b></td>
		<td class="row1"><?php echo $_SERVER['SERVER_SOFTWARE']; ?> <input type="hidden" name="log_server" value="<?php echo $_SERVER['SERVER_SOFTWARE']; ?>" /></td>
	</tr>
	<tr>
		<td class="row1"><b>PHP Version:</b></td>
		<td class="row1"><?php echo phpversion(); ?> <input type="hidden" name="log_php" value="<?php echo phpversion(); ?>" /></td>
	</tr>
	<tr>
		<td class="row1"><b>MySQL Version:</b></td>
		<td class="row1"><?php

			if ($_POST['step1']['old_path'] != '') {
				$db     = @mysql_connect($_POST['step1']['db_host'] . ':' . $_POST['step1']['db_port'], $_POST['step1']['db_login'], urldecode($_POST['step1']['db_password']));
			} else {
				$db     = @mysql_connect($_POST['step2']['db_host'] . ':' . $_POST['step2']['db_port'], $_POST['step2']['db_login'], $_POST['step2']['db_password']);
			}

			$sql    = 'SELECT VERSION() AS version';
			$result = @mysql_query($sql, $db);
			$row    = @mysql_fetch_assoc($result);
			echo $row['version'];
			?> <input type="hidden" name="log_mysql" value="<?php echo $row['version']; ?>" /></td>
	</tr>
	<tr>
		<td class="row1" valign="top"><div class="optional" title="Optional Field">?</div><b>Optional URL:</b></td>
		<td class="row1"><?php
			$url = 'http' . ((isset($_SERVER['HTTPS']) || strtolower($_SERVER['HTTPS']) == 'on') ? 's' : '') . '://' . $_SERVER['SERVER_NAME'] . substr($_SERVER['PHP_SELF'], 0, -strlen('install/install.php'));
			echo $url; ?><input type="hidden" name="log_url" value="<?php echo $url; ?>" /><br />
		<input type="checkbox" name="log_url_yes" value="1" id="url_yes" /><label for="url_yes">Include this URL as well.</label></td>
	</tr>
	<tr>
		<td class="row1" colspan="2"><div class="optional" title="Optional Field">?</div><input type="checkbox" name="log_yes" value="1" checked="checked" id="yes_send" /><label for="yes_send">Yes, send this information to atutor.ca.</label></td>
	</tr>
	</table>

<br />
<p align="center"><input type="submit" class="button" value=" Next &raquo; " name="submit" />

</form>