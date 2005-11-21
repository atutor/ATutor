<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg, Heidi Hazelton	*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', 'include/');
error_reporting(E_ALL ^ E_NOTICE);

require('../include/lib/constants.inc.php');

$new_version = VERSION;

header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');

$session_error = '';
error_reporting(E_ALL);
ob_start();
session_start();
$session_error = ob_get_contents();
ob_end_clean();
error_reporting(E_ALL ^ E_NOTICE);

require(AT_INCLUDE_PATH.'header.php');
$bad  = '<img src="images/bad.gif" width="14" height="13" border="0" alt="Bad" title="Bad" />';
$good = '<img src="images/feedback.gif" width="16" height="13" border="0" alt="Good" title="Good" />';

$no_good = FALSE;
?>
<h3>Welcome to the ATutor Installation</h3>
<p>This process will step you through your ATutor installation or upgrade.</p>
<p>During this process be sure not to use your browser's <em>Refresh</em> or <em>Reload</em> option as it may complicate the installation process.</p>

<p>Before you continue you may want to review the <a href="../documentation/admin/" target="_new"><em>ATutor Handbook</em></a> for more detailed instructions.</p>

<h4>Requirements</h4>
<p>Please review the requirements below before proceeding.</p>
		<table class="data" style="width: 75%; max-width: 600px;">
		<tbody>
		<tr>
			<th scope="cols">Web Server Options</th>
			<th scope="cols">Detected</th>
			<th scope="cols">Status</th>
		</tr>
		<tr>
			<td>Apache 1.3.0+</td>
			<td><?php echo $_SERVER['SERVER_SOFTWARE']; ?></td>
			<td align="center"><?php echo $good; ?></td>
		</tr>
		<tbody>
		<tr>
			<th scope="cols">PHP Options</th>
			<th scope="cols">Detected</th>
			<th scope="cols">Status</th>
		</tr>
		<tr>
			<td>PHP 4.2.0+</td>
			<td><?php echo phpversion(); ?></td>
			<td align="center"><?php	if (version_compare(phpversion(), '4.2.0', '>=')) {
							echo $good;
						} else {
							echo $bad;
							$no_good = TRUE;
						} ?></td>
		</tr>
		<tr>
			<td><kbd>zlib</kbd></td>
			<td><?php if (extension_loaded('zlib')) {
						echo 'Enabled</td><td align="center">';
						echo $good;
					} else {
						echo 'Disabled</td><td align="center">';
						echo $bad;
						$no_good = TRUE;
					} ?></td>
		</tr>
		<tr>
			<td><kbd>mysql</kbd></td>
			<td><?php if (extension_loaded('mysql')) {
						echo 'Enabled</td><td align="center">';
						echo $good;
					} else {
						echo 'Disabled</td><td align="center">';
						echo $bad;
						$no_good = TRUE;
					} ?></td>
		</tr>
		<tr>
			<td><kbd>safe_mode = Off</kbd></td>
			<td><?php if (ini_get('safe_mode')) {
							echo 'On</td><td align="center">'; 
							echo $bad;
							$no_good = TRUE;
						} else {
							echo 'Off</td><td align="center">';
							echo $good;
						} ?></td>
		</tr>
		<tr>
			<td><kbd>file_uploads = On</kbd></td>
			<td><?php if (ini_get('file_uploads')) {
							echo 'On</td><td align="center">';
							echo $good;
						} else {
							echo 'Off</td><td align="center">';
							echo $bad;
							$no_good = TRUE;
						} ?></td>
		</tr>
		<tr>
			<td><kbd>upload_max_filesize</kbd> &gt;= 2 MB</td>
			<td><?php echo $filesize = ini_get('upload_max_filesize'); ?></td>
			<td align="center"><?php 
				$filesize_int = intval($filesize);
				if ("$filesize_int" == $filesize) {
					// value is in Bytes
					if ($filesize_int < 2 * 1024 * 1024) {
						echo $bad;
					} else {
						echo $good;
					}
				} else if (stristr($filesize, 'M') !== FALSE) {
					// value is in MegaBytes
					if ($filesize_int < 2) {
						echo $bad;
					} else {
						echo $good;
					}
				} else if (stristr($filesize, 'K') !== FALSE) {
					// value is in KiloBytes
					if ($filesize_int < 2 * 1024) {
						echo $bad;
					} else {
						echo $good;
					}
				} else if (stristr($filesize, 'G') !== FALSE) {
					// value is in GigaBytes
					echo $good;
				} else {
					// not set?
				}
				?></td>
		</tr>
		<tr>
			<td><kbd>post_max_size</kbd> &gt;= 8 MB</td>
			<td><?php echo $filesize = ini_get('post_max_size'); ?></td>
			<td align="center"><?php 
				$filesize_int = intval($filesize);
				if ("$filesize_int" == $filesize) {
					// value is in Bytes
					if ($filesize_int < 8 * 1024 * 1024) {
						echo $bad;
					} else {
						echo $good;
					}
				} else if (stristr($filesize, 'M') !== FALSE) {
					// value is in MegaBytes
					if ($filesize_int < 8) {
						echo $bad;
					} else {
						echo $good;
					}
				} else if (stristr($filesize, 'K') !== FALSE) {
					// value is in KiloBytes
					if ($filesize_int < 8 * 1024) {
						echo $bad;
					} else {
						echo $good;
					}
				} else if (stristr($filesize, 'G') !== FALSE) {
					// value is in GigaBytes
					echo $good;
				} else {
					// not set?
				}
				?></td>
		</tr>
		<tr>
			<td><kbd>sessions</kbd></td>
			<td><?php if (extension_loaded('session')) {
						echo 'Enabled</td><td align="center">';
						echo $good;
					} else {
						echo 'Disabled</td><td align="center">';
						echo $bad;
						$no_good = TRUE;
					} ?></td>
		</tr>
		<tr>
			<td><kbd>session.save_path</kbd></td>
			<td><?php
				if ($session_error == '') {
					echo 'Directory Writeable</td><td align="center">';
					echo $good;
				} else {
					echo 'Directory Not Writeable</td><td align="center">';
					echo $bad;
					$no_good = TRUE;					
				}
			?></td>
		</tr>
		<tr>
			<td><kbd>.</kbd> in <kbd>include_path</kbd></td>
			<td><?php
				$include_path = explode(PATH_SEPARATOR, ini_get('include_path'));
				if (in_array('.', $include_path)) {
					echo 'Enabled</td><td align="center">';
					echo $good;
				} else {
					echo 'Disabled</td><td align="center">';
					echo $bad;
					$no_good = TRUE;					
				}
			?></td>
		</tr>
		</tbody>
		<tbody>
		<tr>
			<th scope="cols">MySQL Options</th>
			<th scope="cols">Detected</th>
			<th scope="cols">Status</th>
		</tr>
		<tr>
			<td>MySQL 3.23.0+ or 4.0.16+</td>
			<td><?php if (defined('MYSQL_NUM')) {
						echo 'Found Unknown Version</td><td align="center">';
						echo $good;
					} else {
						echo 'Not Found</td><td align="center">';
						echo $bad;
						$no_good = TRUE;
					} ?></td>
		</tr>
		</tbody>
		</table>
<br />

<?php if ($no_good): ?>
	<table cellspacing="0" class="tableborder" cellpadding="1" align="center" width="70%">
	<tr>
		<td class="row1"><strong>Your server does not meet the minimum requirements!<br />
						Please correct the above errors to continue.</strong></td>
	</tr>
	</table>
<?php else: ?>
	<table cellspacing="0" class="tableborder" cellpadding="1" align="center" width="70%">
	<tr>
		<td align="right" class="row1" nowrap="nowrap"><strong>New Installation &raquo;</strong></td>
		<td class="row1" width="150" align="center"><form action="install.php" method="post" name="form">
		<input type="hidden" name="new_version" value="<?php echo $new_version; ?>" />
		<input type="submit" class="button" value="  Install  " name="next" />
		</form></td>
	</tr>
	</table>
	<table cellspacing="0" cellpadding="10" align="center" width="45%">
	<tr>
		<td align="center"><b>Or</b></td>
	</tr>
	</table>
	<table cellspacing="0" class="tableborder" cellpadding="1" align="center" width="70%">
	<tr>
		<td align="right" class="row1" nowrap="nowrap"><strong>Upgrade an Existing Installation &raquo;</strong></td>
		<td class="row1" width="150" align="center"><form action="upgrade.php" method="post" name="form">
		<input type="hidden" name="new_version" value="<?php echo $new_version; ?>" />
		<input type="submit" class="button" value="Upgrade" name="next" />
		</form></td>
	</tr>
	</table>
<?php endif; ?>

<?php require(AT_INCLUDE_PATH.'footer.php'); ?>