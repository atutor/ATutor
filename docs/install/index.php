<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay, Joel Kronenberg, Heidi Hazelton	*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/

define('AT_INCLUDE_PATH', 'include/');
error_reporting(E_ALL ^ E_NOTICE);

require('../include/lib/constants.inc.php');

$new_version = VERSION;

require(AT_INCLUDE_PATH.'header.php');

?>

<h3>Welcome to the ATutor Installation</h3>
<p>This process will guide you through your ATutor installation or upgrade.</p>
<p>During the installation or upgrade be sure not to use your browser's <em>Refresh</em> option as it may complicate the installation process.</p>

<h4>Requirements</h4>
<p>Please review the requirements below before proceeding.</p>
<ul>
	<li>HTTP Web Server (<a href="http://apache.org">Apache</a> 1.3.x is highly recommended. We do not recommend Apache 2.x) <strong>Detected: <?php echo $_SERVER['SERVER_SOFTWARE']; ?></strong><br /><br /></li>

	<li><a href="http://php.net">PHP</a> 4.2.0 or higher (Version 4.3.0 or higher is recommended) <strong>Detected: PHP <?php echo phpversion(); ?></strong><br />
		With the following extensions enabled:
		<ul>
			<li><code>--with-zlib</code>, with Zlib enabled (Required) <strong>Detected: <?php if (defined('FORCE_GZIP')) {
																									echo 'Enabled'; 
																								} else {
																									echo 'Disabled';
																								} ?></strong></li>
			<li><code>--with-mysql</code>, with MySQL support (Required) <strong>Detected: <?php if (defined('MYSQL_NUM')) {
																									echo 'Enabled'; 
																								} else {
																									echo 'Disabled';
																								} ?></strong></li>
			<li><code>--enable-ftp</code>, with FTP support (Optional) <strong>Detected: <?php if (defined('FTP_ASCII')) {
																									echo 'Enabled'; 
																								} else {
																									echo 'Disabled';
																								} ?></strong></li>
		</ul>
		<br /><br /></li>

	<li><a href="http://mysql.com">MySQL</a> 3.23.x or higher (MySQL 4.x is not yet officially supported) <strong>Detected: <?php if (defined('MYSQL_NUM')) {
																									echo 'Version Unknown'; 
																								} else {
																									echo 'Disabled';
																								} ?></strong></li>
</ul>

<br /><br />

<table cellspacing="0" class="tableborder" cellpadding="1" align="center" width="70%">
<tr>
	<td align="right" class="row1" nowrap="nowrap"><b>Install a Fresh Version »</b></td>
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
	<td align="right" class="row1" nowrap="nowrap"><b>Upgrade an Existing Installation »</b></td>
	<td class="row1" width="150" align="center"><form action="upgrade.php" method="post" name="form">
	<input type="hidden" name="new_version" value="<?php echo $new_version; ?>" />
	<input type="submit" class="button" value="Upgrade" name="next" />
	</form></td>
</tr>
</table>

<?php
	require(AT_INCLUDE_PATH.'footer.php');
?>