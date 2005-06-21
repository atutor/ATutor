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

if (!defined('AT_INCLUDE_PATH')) { exit; }

if(isset($_POST['submit']) && ($_POST['action'] == 'process')) {
	unset($errors);

	if (version_compare($_POST['step1']['old_version'], '1.5', '<')) {
		$_POST['admin_username'] = trim($_POST['admin_username']);
		$_POST['admin_password'] = trim($_POST['admin_password']);
		$_POST['admin_email']    = trim($_POST['admin_email']);
		$_POST['site_name']      = trim($_POST['site_name']);
		$_POST['home_url']	     = trim($_POST['home_url']);

		/* Super Administrator Account checking: */
		if ($_POST['admin_username'] == ''){
			$errors[] = 'Administrator username cannot be empty.';
		} else {
			/* check for special characters */
			if (!(eregi("^[a-zA-Z0-9_]([a-zA-Z0-9_])*$", $_POST['admin_username']))){
				$errors[] = 'Administrator username is not valid.';
			}
		}
		if ($_POST['admin_password'] == '') {
			$errors[] = 'Administrator password cannot be empty.';
		}
		if ($_POST['admin_email'] == '') {
			$errors[] = 'Administrator email cannot be empty.';
		} else if (!eregi("^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,6}$", $_POST['admin_email'])) {
			$errors[] = 'Administrator email is not valid.';
		}

		/* System Preferences checking: */
		if ($_POST['email'] == '') {
			$errors[] = 'Contact email cannot be empty.';
		} else if (!eregi("^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,6}$", $_POST['email'])) {
			$errors[] = 'Contact email is not valid.';
		}

		if (!isset($errors)) {
			$db = @mysql_connect($_POST['step1']['db_host'] . ':' . $_POST['step1']['db_port'], $_POST['step1']['db_login'], $_POST['step1']['db_password']);
			@mysql_select_db($_POST['step1']['db_name'], $db);

			$sql = "INSERT INTO ".$_POST['step1']['tb_prefix']."admins VALUES ('$_POST[admin_username]', '$_POST[admin_password]', '', '$_POST[admin_email]', 1, NOW())";
			$result= mysql_query($sql, $db);

			unset($_POST['admin_username']);
			unset($_POST['admin_password']);
			unset($_POST['admin_email']);
		}
	}


	if (!isset($errors)) {
		unset($errors);
		unset($_POST['submit']);
		unset($action);
		store_steps($step);
		$step++;
		return;
	}
}

print_progress($step);

if (isset($errors)) {
	print_errors($errors);
}


?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
	<input type="hidden" name="action" value="process" />
	<input type="hidden" name="step" value="<?php echo $step; ?>" />
	<?php print_hidden($step); ?>

<?php if (version_compare($_POST['step1']['old_version'], '1.5', '<')): ?>
	<p>Below are new configuration options that are available for this version.</p>

	<br />
		<table width="70%" class="tableborder" cellspacing="0" cellpadding="1" align="center">
		<tr>
			<th colspan="2">Super Administrator</th>
		</tr>
		<tr>
			<td class="row1" colspan="2">The Super Administrator account is used for managing ATutor. Since ATutor version 1.5 the Super Administrator can also create additional Administrators each with their own privileges and roles.</td>
		</tr>
		<tr>
			<td class="row1"><div class="required" title="Required Field">*</div><b><label for="username">Administrator Username:</label></b><br />
			May contain only letters, numbers, or underscores.</td>
			<td class="row1"><input type="text" name="admin_username" id="username" maxlength="20" size="20" value="<?php if (!empty($_POST['admin_username'])) { echo stripslashes(htmlspecialchars($_POST['admin_username'])); } else { echo urldecode($_POST['step1']['admin_username']); } ?>" class="formfield" /></td>
		</tr>
		<tr>
			<td class="row1"><div class="required" title="Required Field">*</div><b><label for="password">Administrator Password:</label></b></td>
			<td class="row1"><input type="text" name="admin_password" id="password" maxlength="15" size="15" value="<?php if (!empty($_POST['admin_password'])) { echo stripslashes(htmlspecialchars($_POST['admin_password'])); } else { echo urldecode($_POST['step1']['admin_password']); } ?>" class="formfield" /></td>
		</tr>
		<tr>
			<td class="row1"><div class="required" title="Required Field">*</div><b><label for="email">Administrator Email:</label></b></td>
			<td class="row1"><input type="text" name="admin_email" id="email" size="30" value="<?php if (!empty($_POST['admin_email'])) { echo stripslashes(htmlspecialchars($_POST['admin_email'])); } else { echo urldecode($_POST['step1']['admin_email']); } ?>" class="formfield" /></td>
		</tr>
		</table>

		<br />

		<table width="70%" class="tableborder" cellspacing="0" cellpadding="1" align="center">
		<tr>
			<th colspan="2">System Preferences</th>
		</tr>
		<tr>
			<td class="row1"><div class="required" title="Required Field">*</div><b><label for="cemail">Contact Email:</label></b><br />
			The email that will be used as the return email when needed and when instructor account requests are made.</td>
			<td class="row1"><input type="text" name="email" id="cemail" size="30" value="<?php if (!empty($_POST['email'])) { echo stripslashes(htmlspecialchars($_POST['admin_email'])); } else { echo urldecode($_POST['step1']['admin_email']); } ?>" class="formfield" /></td>
		</tr>
		</table>
<?php else: ?>
	<p>There are no new configuration options for this version.</p>
<?php endif; ?>

	<br />
	<br />
	<div align="center"><input type="submit" class="button" value=" Next »" name="submit" /></div>
</form>