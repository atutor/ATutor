<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg, Heidi Hazelton	*/
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
$warning = '<img src="images/warning.png" width="16" height="16" border="0" alt="Warning" title="Warning" />';

$no_good = FALSE;
$not_as_good = FALSE;
?>
<table style="float: right;
	background-color: #FEFDEF;
	border: 1pt solid #B8AE9C;
	margin: 20pt 5pt 5pt 5pt;
	padding-right: 10pt;
	padding-bottom: 5pt;
	padding-left: 10pt;
">
	<tr style="align:center;"><th colspan="2" style="background-color:transparent;">Icon Legend</th></tr>
	<tr><td><?php echo $good;?></td><td>Pass, can proceed</td></tr>
	<tr><td><?php echo $warning;?></td><td>Warning but can proceed</td></tr>
	<tr><td><?php echo $bad;?></td><td>Failed, can not proceed</td></tr>
</table>

<h3>Welcome to the ATutor Installation</h3>
<p>This process will step you through your ATutor installation or upgrade.</p>
<p>During this process be sure not to use your browser's <em>Refresh</em> or <em>Reload</em> feature as it may complicate the installation process.</p>

<p>Before you continue you may want to review the <a href="../documentation/admin/" target="_new"><em>ATutor Handbook</em></a> for more detailed instructions.</p>

<h4>Requirements</h4>
<p>Please review the requirements below before proceeding.</p>
		<table class="data" style="width: 75%; max-width: 600px;">
		<tbody>
		<tr>
			<th scope="col">File Integrity</th>
			<th scope="col">Detected</th>
			<th scope="col">Status</th>
		</tr>
		<tr>
			<td>Case Sensitivity</td>
			<td><?php if (file_exists('../include/classes/CSVExport.class.php') && file_exists('../include/classes/csvexport.class.php')) {
						echo 'Ignored</td><td align="center">';
						echo $good;
					} else if (file_exists('../include/classes/CSVExport.class.php')) {
						echo 'Enforced</td><td align="center">';
						echo $good;
					} else {
						echo 'Enforced</td><td align="center">';
						echo $bad;
						$no_good = TRUE;
					} ?></td>
		</tr>
		</tbody>
		<tbody>
		<tr>
			<th scope="col">PHP Options</th>
			<th scope="col">Detected</th>
			<th scope="col">Status</th>
		</tr>
		<tr>
			<td>PHP 4.3.0+</td>
			<td><?php echo phpversion(); ?></td>
			<td align="center"><?php	if (version_compare(phpversion(), '4.3.0', '>=')) {
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
			<td><kbd>mbstring</kbd></td>
			<td><?php if (extension_loaded('mbstring')) {
						echo 'Enabled</td><td align="center">';
						echo $good;
					} else {
						echo 'Disabled</td><td align="center">';
						echo $bad;
						$not_as_good = TRUE;
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
			<td><kbd>session.auto_start = 0</kbd></td>
			<td><?php if (ini_get('session.auto_start')) {
							echo '1</td><td align="center">';
							echo $bad;
							$no_good = TRUE;
						} else {
							echo '0</td><td align="center">';
							echo $good;
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
		<tr>
			<td>Mail configuration</td>
			<td><?php
			$smtp_server = ini_get('SMTP');
			if (($smtp_server == '' || $smtp_server == 'localhost') && ini_get('sendmail_path') == '') {
					echo 'Disabled</td><td align="center">';
					echo $warning;
				} else {
					echo 'Enabled</td><td align="center">';
					echo $good;
				}
			?></td>
		</tr>
		</tbody>
		<tbody>
		<tr>
			<th scope="col">MySQL Options</th>
			<th scope="col">Detected</th>
			<th scope="col">Status</th>
		</tr>
		<tr>
			<td>MySQL 4.1.10+</td>
			<td><?php if (defined('MYSQL_NUM')) {
						$mysql_version = mysql_get_client_info();
						echo 'Found  Version '.$mysql_version.'</td><td align="center">';
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
<?php elseif ($not_as_good): ?>
	<table cellspacing="0" class="tableborder" cellpadding="1" align="center" width="70%">
	<tr>
		<td class="row1"><strong>ATutor has indicated that the 'mbstring' library is missing from the PHP.  <br />
						We strongly encourage you to install the 'mbstring' library before continuing, however, if you choose not to install the library from PHP, a third party library within ATutor will be used.  <br/><br/>
						For production systems, we strongly encourage you to install the PHP with <a href="http://ca.php.net/manual/en/ref.mbstring.php" target="php_site">mbstring</a> support.  <br/><br/>
						You may choose to by pass the mbstring check for the installation at your own risk by clicking <a href="javascript:void(0);" onclick="javascript:document.form.next.disabled=false;">continue</a>.</strong></td>
		<td class="row1"></td>
	</tr>
	<tr>
		<td align="right" class="row1" nowrap="nowrap"><strong>New Installation &raquo;</strong></td>
		<td class="row1" width="150" align="center">
		<div id=''>
			<form action="install.php" method="post" name="form">
			<input type="hidden" name="new_version" value="<?php echo $new_version; ?>" />
			<input type="submit" class="button" value="  Install  " name="next" disabled="true" />
			</form>
		</div>
		</td>
	</tr>
	</table>
	<table cellspacing="0" cellpadding="10" align="center" width="45%">
	<tr>
		<td align="center"><b>Or</b></td>
	</tr>
	</table>
	<table cellspacing="0" class="tableborder" cellpadding="1" align="center" width="70%">
	<tr>
		<td class="row1"><strong>Upgrading from previous ATutor must have mbstring library installed.</strong></td>
		<td class="row1"></td>
	</tr>
	<tr>
		<td align="right" class="row1" nowrap="nowrap"><strong>Upgrade an Existing Installation &raquo;</strong></td>
		<td class="row1" width="150" align="center">
		<input type="button" class="button" value="Upgrade" name="next" disabled="true"/>
		</form></td>
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
