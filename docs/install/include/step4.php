<?php
exit('this file is no longer used');

/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay, Joel Kronenberg, Heidi Hazelton	*/
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
	$_POST['username'] = trim($_POST['username']);
	$_POST['password'] = trim($_POST['password']);
	$_POST['email']    = trim($_POST['email']);

	$_POST['instructor'] = intval($_POST['instructor']);
	$_POST['welcome_course'] = intval($_POST['welcome_course']);

	/* login name check */
	if ($_POST['username'] == ''){
		$errors[] = 'Username cannot be empty.';
	} else {
		/* check for special characters */
		if (!(eregi("^[a-zA-Z0-9_]([a-zA-Z0-9_])*$", $_POST['username']))){
			$errors[] = 'Username is not valid.';
		} else {
			if ($_POST['username'] == $_POST['step3']['admin_username']) {
				$errors[] = 'That Username is already being used for the Administrator account, choose another.';
			}
		}
	}

	if ($_POST['password'] == '') {
		$errors[] = 'Password cannot be empty.';
	}

	if ($_POST['email'] == '') {
		$errors[] = 'Email cannot be empty.';
	} else if (!eregi("^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,6}$", $_POST['email'])) {
		$errors[] = 'Invalid email format.';
	}
	
	if (!isset($errors)) {
		unset($_POST['submit']);
		unset($_POST['action']);

		if ($_POST['instructor']) {
			$status = 3;
		} else {
			$status = 2;
		}

		$db = mysql_connect($_POST['step2']['db_host'] . ':' . $_POST['step2']['db_port'], $_POST['step2']['db_login'], urldecode($_POST['step2']['db_password']));
		mysql_select_db($_POST['step2']['db_name'], $db);

		$sql = "INSERT INTO ".$_POST['step2']['tb_prefix']."members VALUES (0,'$_POST[username]','$_POST[password]','$_POST[email]','','','', '','', '','','','','', '',$status,'', NOW(),'en', 0, 1)";
		$result = mysql_query($sql ,$db);
		$m_id	= mysql_insert_id($db);

		if ($_POST['welcome_course'] && $_POST['instructor']) {
			$_POST['tb_prefix'] = $_POST['step2']['tb_prefix'];
			queryFromFile('db/atutor_welcome_course.sql');
		}
		
		store_steps($step);
		$step++;
		return;
	}
} else {
	unset($_POST['email']);
}

print_progress($step);

if (isset($errors)) {
	print_errors($errors);
}

?>
<p>You will need a personal account to view and, optionally, create courses.</p>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
	<input type="hidden" name="step" value="4" />
	<input type="hidden" name="action" value="process" />
	<?php
		print_hidden($step);
	?>
	<table width="70%" class="tableborder" cellspacing="0" cellpadding="1" align="center">
	<tr>
		<td class="row1"><small><b><label for="username">Username:</label></b><br />
		May contain only letters, numbers, or underscores.<br />20 character maximum.</small></td>
		<td class="row1"><input type="text" name="username" id="username" maxlength="20" size="20" value="<?php if (!empty($_POST['username'])) { echo stripslashes(htmlspecialchars($_POST['username'])); } ?>" class="formfield" /></td>
	</tr>
	<tr>
		<td class="row1"><small><b><label for="password">Password:</label></b><br />
		Use a combination of letters, numbers and symbols.<br />15 character maximum.</small></td>
		<td class="row1"><input type="text" name="password" id="password" maxlength="15" size="15" value="<?php if (!empty($_POST['password'])) { echo stripslashes(htmlspecialchars($_POST['password'])); } ?>" class="formfield" /></td>
	</tr>
	<tr>
		<td class="row1"><small><b><label for="email">Email:</label></b></small></td>
		<td class="row1"><input type="text" name="email" id="email" size="30" maxlength="60" value="<?php if (!empty($_POST['email'])) { echo stripslashes(htmlspecialchars($_POST['email'])); } ?>" class="formfield" /></td>
	</tr>
	<tr>
		<td class="row1"><small><b>Instructor Account:</b><br />
		Do you want this to be an instructor account allowing you to create courses?<br />
		Default: <kbd>Yes</kbd></small></td>
		<td class="row1"><input type="radio" name="instructor" value="1" id="en_y" <?php if($_POST['instructor']== 1 || empty($_POST['instructor'])) { echo "checked"; }?>/><label for="en_y">Yes</label>, <input type="radio" name="instructor" value="0" id="en_n" <?php if($_POST['instructor']===0) { echo "checked"; }?>/><label for="en_n">No</label></td>
	</tr>
	<tr>
		<td class="row1"><small><b>Welcome Course:</b><br />
		Do you want the basic <em>Welcome Course</em> created? Only possible if an instructor account above is created.<br />
		Default: <kbd>Yes</kbd></small></td>
		<td class="row1"><input type="radio" name="welcome_course" value="1" id="wc_y" <?php if($_POST['welcome_course']== 1 || empty($_POST['welcome_course'])) { echo 'checked'; }?>/><label for="wc_y">Yes</label>, <input type="radio" name="welcome_course" value="0" id="wc_n" <?php if ($_POST['welcome_course'] === 0) { echo 'checked'; }?>/><label for="wc_n">No</label></td>
	</tr>
	</table>

	<br /><br /><p align="center"><input type="submit" class="button" value="Next &raquo; " name="submit" /></p>

</form>