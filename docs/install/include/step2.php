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

if(isset($_POST['submit'])) {
	unset($errors);
	//check DB & table connection

	$db = @mysql_connect($_POST['db_host'] . ':' . $_POST['db_port'], $_POST['db_login'], $_POST['db_password']);
	if (!$db) {
		$errors[] = 'Unable to connect to database server.';
	} else {
		if (!mysql_select_db($_POST['db_name'], $db)) {
			$sql = "CREATE DATABASE $_POST[db_name]";
			$result = mysql_query($sql, $db);
			if (!$result) {
				$errors[] = 'Unable to select or create database <b>'.$_POST['db_name'].'</b>.';
			} else {
				$progress[] = 'Database <b>'.$_POST['db_name'].'</b> created successfully.';
				mysql_select_db($_POST['db_name'], $db);

			}
		}

		if (!$errors) {

			$progress[] = 'Connected to database <b>'.$_POST['db_name'].'</b> successfully.';
			$errors = array();
			
			/* @See include/classes/dbmanager.php */
			queryFromFile('db/atutor_schema.sql');
			queryFromFile('db/atutor_lang_base.sql');

			if (!$errors) {
				print_progress($step);

				unset($_POST['submit']);
				unset($_POST['action']);
				store_steps($step);
				print_feedback($progress);

				echo '<form action="'.$_SERVER['PHP_SELF'].'" method="post" name="form">
				<input type="hidden" name="step" value="3" />';
				print_hidden(3);
				echo '<input type="submit" class="button" value="Next » " name="submit" /></form>';
				return;
			}
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

<center><table width="65%" class="tableborder" cellspacing="0" cellpadding="1" border="0">
<tr>
	<td class="row1"><small><b>Database Hostname:</b><br />
		Hostname of the database server. Default: <code>localhost</code></small></td>
	<td class="row1" valign="middle"><input type="text" name="db_host" value="<?php if (!empty($_POST['db_host'])) { echo $_POST['db_host']; } else { echo 'localhost'; } ?>" class="formfield" /></td>
</tr>
<tr>
	<td class="row1"><small><b>Database Port:</b><br />
		The port to the database server. Default: <code>3306</code></small></td>
	<td class="row1"><input type="text" name="db_port" value="<?php if (!empty($_POST['db_port'])) { echo $_POST['db_port']; } else { echo '3306'; } ?>" class="formfield" /></td>
</tr>
<tr>
	<td class="row1"><small><b>Database Username:</b><br />
		The username to the database server.</small></td>
	<td class="row1"><input type="text" name="db_login" value="<?php echo $_POST['db_login']; ?>" class="formfield" /></td>
</tr>
<tr>
	<td class="row1"><small><b>Database Password:</b><br />
		The password to the database server.</small></td>
	<td class="row1"><input type="text" name="db_password" value="<?php echo $_POST['db_password']; ?>" class="formfield" /></td>
</tr>
<tr>
	<td class="row1"><small><b>Database Name:</b><br />
		The name of the database to use. It will be created if it does not exist.<br />Default: <code>atutor</code></small></td>
	<td class="row1"><input type="text" name="db_name" value="<?php if (!empty($_POST['db_name'])) { echo $_POST['db_name']; } else { echo 'atutor'; } ?>" class="formfield" /></td>
</tr>
<tr>
	<td class="row1"><small><b>Table Prefix:</b><br />
		The prefix to add to table names to avoid conflicts with existing tables.<br />
		Default: <code>AT_</code></small></td>
	<td class="row1"><input type="text" name="tb_prefix" value="<?php if (!empty($_POST['tb_prefix'])) { echo $_POST['tb_prefix']; } else { echo 'AT_'; } ?>" class="formfield" /></td>
</tr>

</table></center>

<br /><br /><p align="center"><input type="submit" class="button" value="Next » " name="submit" /></p>

</form>