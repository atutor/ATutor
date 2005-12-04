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

	$_POST['admin_username'] = trim($_POST['admin_username']);
	$_POST['admin_password'] = trim($_POST['admin_password']);
	$_POST['admin_email']    = trim($_POST['admin_email']);
	$_POST['site_name']      = trim($_POST['site_name']);
	$_POST['home_url']	     = trim($_POST['home_url']);
	$_POST['email']	         = trim($_POST['email']);

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
	if ($_POST['site_name'] == '') {
		$errors[] = 'Site name cannot be empty.';
	}
	if ($_POST['email'] == '') {
		$errors[] = 'Contact email cannot be empty.';
	} else if (!eregi("^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,6}$", $_POST['email'])) {
		$errors[] = 'Contact email is not valid.';
	}


	/* Personal Account checking: */
	if ($_POST['account_username'] == ''){
		$errors[] = 'Personal Account Username cannot be empty.';
	} else {
		/* check for special characters */
		if (!(eregi("^[a-zA-Z0-9_]([a-zA-Z0-9_])*$", $_POST['account_username']))){
			$errors[] = 'Personal Account Username is not valid.';
		} else {
			if ($_POST['account_username'] == $_POST['admin_username']) {
				$errors[] = 'That Personal Account Username is already being used for the Administrator account, choose another.';
			}
		}
	}
	if ($_POST['account_password'] == '') {
		$errors[] = 'Personal Account Password cannot be empty.';
	}
	if ($_POST['account_email'] == '') {
		$errors[] = 'Personal Account email cannot be empty.';
	} else if (!eregi("^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,6}$", $_POST['email'])) {
		$errors[] = 'Invalid Personal Account email is not valid.';
	}

	if (!isset($errors)) {
		$db = @mysql_connect($_POST['step2']['db_host'] . ':' . $_POST['step2']['db_port'], $_POST['step2']['db_login'], urldecode($_POST['step2']['db_password']));
		@mysql_select_db($_POST['step2']['db_name'], $db);

		if ($_POST['instructor']) {
			$status = 3;
		} else {
			$status = 2;
		}
		$sql = "INSERT INTO ".$_POST['step2']['tb_prefix']."admins VALUES ('$_POST[admin_username]', '$_POST[admin_password]', '', '$_POST[admin_email]', 'en', 1, NOW())";
		$result= mysql_query($sql, $db);

		$sql = "INSERT INTO ".$_POST['step2']['tb_prefix']."members VALUES (0,'$_POST[account_username]','$_POST[account_password]','$_POST[account_email]','','','', '','', '','','','','', '',$status,'', NOW(),'en', '')";
		$result = mysql_query($sql ,$db);

		$_POST['site_name'] = $addslashes($_POST['site_name']);
		$sql = "INSERT INTO ".$_POST['step2']['tb_prefix']."config VALUES ('site_name', '$_POST[site_name]')";
		$result = mysql_query($sql ,$db);

		$_POST['email'] = $addslashes($_POST['email']);
		$sql = "INSERT INTO ".$_POST['step2']['tb_prefix']."config VALUES ('contact_email', '$_POST[email]')";
		$result = mysql_query($sql ,$db);

		$_POST['home_url'] = $addslashes($_POST['home_url']);
		if ($_POST['home_url'] != '') {
			$sql = "INSERT INTO ".$_POST['step2']['tb_prefix']."config VALUES ('home_url', '$_POST[home_url]')";
			$result = mysql_query($sql ,$db);
		}

		if ($_POST['welcome_course'] && $_POST['instructor']) {
			$_POST['tb_prefix'] = $_POST['step2']['tb_prefix'];
			queryFromFile('db/atutor_welcome_course.sql');
		}

		unset($_POST['admin_username']);
		unset($_POST['admin_password']);
		unset($_POST['admin_email']);
		unset($_POST['account_username']);
		unset($_POST['account_password']);
		unset($_POST['account_email']);
		unset($_POST['home_url']);
		unset($_POST['email']);
		unset($_POST['site_name']);

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

if (isset($_POST['step1']['old_version']) && $_POST['upgrade_action']) {
	$defaults['admin_username'] = urldecode($_POST['step1']['admin_username']);
	$defaults['admin_password'] = urldecode($_POST['step1']['admin_password']);
	$defaults['admin_email']    = urldecode($_POST['step1']['admin_email']);

	$defaults['site_name']   = urldecode($_POST['step1']['site_name']);
	$defaults['header_img']  = urldecode($_POST['step1']['header_img']);
	$defaults['header_logo'] = urldecode($_POST['step1']['header_logo']);
	$defaults['home_url']    = urldecode($_POST['step1']['home_url']);
} else {
	$defaults = $_defaults;
}

?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
	<input type="hidden" name="action" value="process" />
	<input type="hidden" name="step" value="<?php echo $step; ?>" />
	<?php print_hidden($step); ?>

	<?php
		/* detect mail settings. if sendmail_path is empty then use SMTP. */
		if (@ini_get('sendmail_path') == '') { 
			echo '<input type="hidden" name="smtp" value="true" />';
		} else {
			echo '<input type="hidden" name="smtp" value="false" />';
		}
	?>
	<br />
		<table width="70%" class="tableborder" cellspacing="0" cellpadding="1" align="center">
		<tr>
			<th colspan="2">Super Administrator Account</th>
		<tr>
		<tr>
			<td colspan="2" class="row1">The Super Administrator account is used for managing ATutor. The Super Administrator can also create additional Administrators each with their own privileges and roles. Administrator accounts cannot enroll in courses.</td>
		</tr>
		<tr>
			<td class="row1"><div class="required" title="Required Field">*</div><b><label for="username">Administrator Username:</label></b><br />
			May contain only letters, numbers, or underscores.</td>
			<td class="row1"><input type="text" name="admin_username" id="username" maxlength="20" size="20" value="<?php if (!empty($_POST['admin_username'])) { echo stripslashes(htmlspecialchars($_POST['admin_username'])); } else { echo $defaults['admin_username']; } ?>" class="formfield" /></td>
		</tr>
		<tr>
			<td class="row1"><div class="required" title="Required Field">*</div><b><label for="password">Administrator Password:</label></b></td>
			<td class="row1"><input type="text" name="admin_password" id="password" maxlength="15" size="15" value="<?php if (!empty($_POST['admin_password'])) { echo stripslashes(htmlspecialchars($_POST['admin_password'])); } else { echo $defaults['admin_password']; } ?>" class="formfield" /></td>
		</tr>
		<tr>
			<td class="row1"><div class="required" title="Required Field">*</div><b><label for="email">Administrator Email:</label></b></td>
			<td class="row1"><input type="text" name="admin_email" id="email" size="30" value="<?php if (!empty($_POST['admin_email'])) { echo stripslashes(htmlspecialchars($_POST['admin_email'])); } else { echo $defaults['admin_email']; } ?>" class="formfield" /></td>
		</tr>
		</table>

	<br />

		<table width="70%" class="tableborder" cellspacing="0" cellpadding="1" align="center">
		<tr>
			<th colspan="2">System Preferences</th>
		</tr>
		<tr>
			<td class="row1"><div class="required" title="Required Field">*</div><b><label for="sitename">Site Name:</b><br />
			The name of your course server website.<br />Default: <kbd><?php echo $defaults['site_name']; ?></kbd></td>
			<td class="row1"><input type="text" name="site_name" size="28" maxlength="60" id="sitename" value="<?php if (!empty($_POST['site_name'])) { echo stripslashes(htmlspecialchars($_POST['site_name'])); } else { echo $defaults['site_name']; } ?>" class="formfield" /></td>
		</tr>
		<tr>
			<td class="row1"><div class="required" title="Required Field">*</div><b><label for="cemail">Contact Email:</label></b><br />
			The email that will be used as the return email when needed and when instructor account requests are made.</td>
			<td class="row1"><input type="text" name="email" id="cemail" size="30" value="<?php if (!empty($_POST['email'])) { echo stripslashes(htmlspecialchars($_POST['email'])); } else { echo $defaults['email']; } ?>" class="formfield" /></td>
		</tr>
		<tr>
			<td class="row1"><div class="optional" title="Optional Field">?</div><b><label for="home_url">Optional 'Home' URL:</b><br />
			This will be the URL for the 'Home' link in the Public Area.  Leave empty to have this link not appear. <br /></td>
			<td class="row1"><input type="text" name="home_url" size="28" maxlength="60" id="home_url" value="<?php if (!empty($_POST['home_url'])) { echo stripslashes(htmlspecialchars($_POST['home_url'])); } else { echo $defaults['home_url']; } ?>" class="formfield" /></td>
		</tr>
		</table>

	<br />

		<table width="70%" class="tableborder" cellspacing="0" cellpadding="1" align="center">
		<tr>
			<th colspan="2">Personal Account</th>
		</tr>
		<tr>
			<td colspan="2" class="row1">You will need a personal account to view and, optionally, create courses.</td>
		</tr>
		<tr>
			<td class="row1"><div class="required" title="Required Field">*</div><b><label for="account_username">Username:</label></b><br />
			May contain only letters, numbers, and underscores.</td>
			<td class="row1"><input type="text" name="account_username" id="account_username" maxlength="20" size="20" value="<?php if (!empty($_POST['account_username'])) { echo stripslashes(htmlspecialchars($_POST['account_username'])); } ?>" class="formfield" /></td>
		</tr>
		<tr>
			<td class="row1"><div class="required" title="Required Field">*</div><b><label for="account_password">Password:</label></b></td>
			<td class="row1"><input type="text" name="account_password" id="account_password" maxlength="15" size="15" value="<?php if (!empty($_POST['account_password'])) { echo stripslashes(htmlspecialchars($_POST['account_password'])); } ?>" class="formfield" /></td>
		</tr>
		<tr>
			<td class="row1"><div class="required" title="Required Field">*</div><b><label for="account_email">Email:</label></b></td>
			<td class="row1"><input type="text" name="account_email" id="account_email" size="30" maxlength="60" value="<?php if (!empty($_POST['account_email'])) { echo stripslashes(htmlspecialchars($_POST['account_email'])); } ?>" class="formfield" /></td>
		</tr>
		<tr>
			<td class="row1"><div class="optional" title="Optional Field">?</div><b>Instructor Account:</b><br />
			Do you want this to be an instructor account allowing you to create courses?<br />
			Default: <kbd>Yes</kbd></td>
			<td class="row1"><input type="radio" name="instructor" value="1" id="en_y" <?php if($_POST['instructor']== 1 || empty($_POST['instructor'])) { echo "checked"; }?>/><label for="en_y">Yes</label>, <input type="radio" name="instructor" value="0" id="en_n" <?php if($_POST['instructor']===0) { echo "checked"; }?>/><label for="en_n">No</label></td>
		</tr>
		<tr>
			<td class="row1"><div class="optional" title="Optional Field">?</div><b>Welcome Course:</b><br />
			Do you want to create the basic <em>Welcome Course</em>? Only possible if an instructor account above is created.<br />
			Default: <kbd>Yes</kbd></td>
			<td class="row1"><input type="radio" name="welcome_course" value="1" id="wc_y" <?php if($_POST['welcome_course']== 1 || empty($_POST['welcome_course'])) { echo 'checked'; }?>/><label for="wc_y">Yes</label>, <input type="radio" name="welcome_course" value="0" id="wc_n" <?php if ($_POST['welcome_course'] === 0) { echo 'checked'; }?>/><label for="wc_n">No</label></td>
		</tr>
		</table>

	<br />
	<br />
	<div align="center"><input type="submit" class="button" value=" Next &raquo;" name="submit" /></div>
</form>