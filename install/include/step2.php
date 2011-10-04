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

if (!defined('AT_INCLUDE_PATH')) { exit; }

if(isset($_POST['submit'])) {
	unset($errors);
	unset($progress);

	//check DB & table connection

	$db = @mysql_connect($_POST['db_host'] . ':' . $_POST['db_port'], $_POST['db_login'], $_POST['db_password']);
	if (!$db) {
		$errors[] = 'Unable to connect to database server.';
	} else {
		// check mysql version number
		$sql = "SELECT VERSION() AS version";
		$result = mysql_query($sql, $db);
		$row = mysql_fetch_assoc($result);
		$row['version'] = str_replace (array('-community-nt', '-max', '-standard'), '', strtolower($row['version']));
		if (version_compare($row['version'], '4.1.10', '>=') === FALSE) {
			$errors[] = 'MySQL version '.$row['version'].' was detected. ATutor requires version 4.1.10 or later.';
		}

		if (!isset($errors)){
			if (!mysql_select_db($_POST['db_name'], $db)) {
				$sql = "CREATE DATABASE `$_POST[db_name]` CHARACTER SET utf8 COLLATE utf8_general_ci";
				$result = mysql_query($sql, $db);
				if (!$result) {
					$errors[] = 'Unable to select or create database <b>'.$_POST['db_name'].'</b>.';
				} else {
					$progress[] = 'Database <b>'.$_POST['db_name'].'</b> created successfully.';
					mysql_select_db($_POST['db_name'], $db);
				}
			} else {
				/* Check if the database that existed is in UTF-8, if not, ask for retry */
				$sql = "SHOW CREATE DATABASE `$_POST[db_name]`";
				$result = mysql_query($sql, $db);
				$row = mysql_fetch_assoc($result);
				
				if (!preg_match('/CHARACTER SET utf8/i', $row['Create Database'])){
					$sql2 = 'ALTER DATABASE `'.$_POST['db_name'].'` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci';
					$result2 = mysql_query($sql2);
					if (!$result2){
						$errors[] = 'Database <b>'.$_POST['db_name'].'</b> is not in UTF8.  Please set the database character set to UTF8 before continuing by using the following query: <br /> ALTER DATABASE `'.$_POST['db_name'].'` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci.  <br />To use ALTER DATABASE, you need the ALTER privilege on the database.  You can also check the MySQL manual <a href="http://dev.mysql.com/doc/refman/4.1/en/alter-database.html" target="mysql_window">here</a>.';
					}
				}
			}
		}

		if (!isset($errors)) {
			$progress[] = 'Connected to database <b>'.$_POST['db_name'].'</b> successfully.';
			$errors = array();
			
			/* @See include/classes/dbmanager.php */
			queryFromFile('db/atutor_schema.sql');
			queryFromFile('db/atutor_language_text.sql');

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
