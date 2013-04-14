<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* http://atutor.ca                                                     */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$

if (!defined('AT_INSTALLER_INCLUDE_PATH') || !defined('AT_INCLUDE_PATH')) { exit; }

include(AT_INCLUDE_PATH . 'install/install.inc.php');

if(isset($_POST['submit'])) {
	//check DB & table connection
	$db = create_and_switch_db($_POST['db_host'], $_POST['db_port'], $_POST['db_login'], $_POST['db_password'], $_POST['tb_prefix'], $_POST['db_name'], true);
	
	if (!isset($errors)) {
	
		$sqlUtility = new SqlUtility();
		$sqlUtility->queryFromFile(AT_INCLUDE_PATH . 'install/db/atutor_schema.sql', $addslashes($_POST['tb_prefix']));
		$sqlUtility->queryFromFile(AT_INCLUDE_PATH . 'install/db/atutor_language_text.sql', $addslashes($_POST['tb_prefix']));
		
		if (!$errors) {
			print_progress($step);
	
			unset($_POST['submit']);
			unset($_POST['action']);
			store_steps($step);
			print_feedback($progress);
	
			echo '<form action="'.$_SERVER['PHP_SELF'].'" method="post" name="form">
			<input type="hidden" name="step" value="3" />';
			print_hidden(3);
			echo '<p align="center"><input type="submit" class="button" value="Next &raquo; " name="submit" /></p></form>';
			return;
		}
	}
}

print_progress($step);


echo '<p>Please enter your database information: </p>';


if (isset($progress)) {
	print_feedback($progress);
}

if (isset($errors)) {
	print_errors($errors);
}

?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
	<input type="hidden" name="action" value="process" />
	<input type="hidden" name="step" value="2" />
	<input type="hidden" name="new_version" value="<?php echo $_POST['new_version']; ?>" />

	<table width="65%" class="tableborder" cellspacing="0" cellpadding="1" border="0" align="center">
	<tr>
		<td class="row1"><span class="required" title="Required Field">*</span><b><label for="db">Database Hostname:</label></b><br />
			Hostname of the database server. Default: <kbd>localhost</kbd></td>
		<td class="row1" valign="middle"><input type="text" name="db_host" id="db" value="<?php if (!empty($_POST['db_host'])) { echo stripslashes(htmlspecialchars($_POST['db_host'])); } else { echo 'localhost'; } ?>" class="formfield" /></td>
	</tr>
	<tr>
		<td class="row1"><span class="required" title="Required Field">*</span><b><label for="port">Database Port:</label></b><br />
			The port to the database server. Default: <kbd>3306</kbd></td>
		<td class="row1"><input type="text" name="db_port" id="port" value="<?php if (!empty($_POST['db_port'])) { echo stripslashes(htmlspecialchars($_POST['db_port'])); } else { echo '3306'; } ?>" class="formfield" /></td>
	</tr>
	<tr>
		<td class="row1"><span class="required" title="Required Field">*</span><b><label for="username">Database Username:</label></b><br />
			The username to the database server.</td>
		<td class="row1"><input type="text" name="db_login" id="username" value="<?php echo stripslashes(htmlspecialchars($_POST['db_login'])); ?>" class="formfield" /></td>
	</tr>
	<tr>
		<td class="row1"><span class="required" title="Required Field">*</span><b><label for="pass">Database Password:</label></b><br />
			The password to the database server.</td>
		<td class="row1"><input type="text" name="db_password" id="pass" value="<?php echo stripslashes(htmlspecialchars($_POST['db_password'])); ?>" class="formfield" /></td>
	</tr>
	<tr>
		<td class="row1"><span class="required" title="Required Field">*</span><b><label for="name">Database Name:</label></b><br />
			The name of the database to use. It will be created if it does not exist.<br />Default: <kbd>atutor</kbd></td>
		<td class="row1"><input type="text" name="db_name" id="name" value="<?php if (!empty($_POST['db_name'])) { echo stripslashes(htmlspecialchars($_POST['db_name'])); } else { echo 'atutor'; } ?>" class="formfield" /></td>
	</tr>
	<tr>
		<td class="row1"><div class="optional" title="Optional Field">?</div><b><label for="prefix">Table Prefix:</label></b><br />
			The prefix to add to table names to avoid conflicts with existing tables.<br />
			Default: <kbd>AT_</kbd></td>
		<td class="row1"><input type="text" name="tb_prefix" id="prefix" value="<?php if (!empty($_POST['tb_prefix'])) { echo stripslashes(htmlspecialchars($_POST['tb_prefix'])); } else { echo 'AT_'; } ?>" class="formfield" /></td>
	</tr>
	</table>

	<br /><br /><p align="center"><input type="submit" class="button" value="Next &raquo; " name="submit" /></p>

</form>
