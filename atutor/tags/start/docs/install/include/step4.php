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

if(isset($_POST['submit']) && $_POST['action'] == 'process') {
	unset($errors);
	//check that all values have been set correctly	
	
/*	if ($_POST['admin_username'] == '') {
		$errors .= '<p class="error">Empty username.</p>';
	} else if (!(eregi("^[a-zA-Z0-9_]([a-zA-Z0-9_])*$", $_POST['admin_username']))){
		$errors .= '<p class="error">Invalid username.</p>';
	}
*/
	
	if ($_POST['admin_password'] == '' || $_POST['admin_cpassword'] == '') {
		$errors[] = 'Empty password.';
	} elseif ($_POST['admin_password'] != $_POST['admin_cpassword']){
		$errors[] = 'Password and confirmed password do not match.';
	}

	if ($_POST['admin_email'] == '') {
		$errors[] = 'Empty email.';
	} else if (!eregi("^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,3}$", $_POST['admin_email'])) {
		$errors[] = 'Invalid email format.';
	}
	
	if (empty($errors)) {
		unset($_POST['submit']);
		unset($_POST['action']);
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
<input type="hidden" name="step" value="4" />
<input type="hidden" name="action" value="process" />
<?php
	print_hidden($step);
?>
<center><table width="65%" class="tableborder" cellspacing="0" cellpadding="1">
<tr>
	<td class="row1"><small><b>Username:</b><br />
	The username for the admin area of ATutor.</small></td>
	<td class="row1">admin</td>
</tr>
<tr>
	<td class="row1"><small><b>Password:</b><br />
	The password for the admin area of ATutor.</small></td>
	<td class="row1"><input type="password" name="admin_password" value="<?php if (!empty($_POST['admin_password'])) { echo $_POST['admin_password']; } ?>" class="formfield" /></td>
</tr>
<tr>
	<td class="row1"><small><b>Confirm Password:</b><br />
	</small></td>
	<td class="row1"><input type="password" name="admin_cpassword" value="<?php if (!empty($_POST['admin_cpassword'])) { echo $_POST['admin_cpassword']; } ?>" class="formfield" /></td>
</tr>
<tr>
	<td class="row1"><small><b>Email:</b><br />
	The email that will be used as the return email when needed and when instructor account requests are made.</small></td>
	<td class="row1"><input type="text" name="admin_email" value="<?php if (!empty($_POST['admin_email'])) { echo $_POST['admin_email']; } ?>" class="formfield" /></td>
</tr>

</table></center>

<br /><br /><p align="center"><input type="submit" class="button" value="Next » " name="submit" /></p>

</form>