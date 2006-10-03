<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay, Joel Kronenberg & Heidi Hazelton	*/
/* Adaptive Technology Resource Centre / University of Toronto				*/
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate();

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.$_base_href.'admin/index.php');
	exit;
} else if (isset($_POST['submit'])) {
	/* password validation */
	if ($_POST['password'] == '') { 
		$msg->addError('PASSWORD_MISSING');
	} else {
		// check for valid passwords
		if ($_POST['password'] != $_POST['confirm_password']){
			$msg->addError('PASSWORD_MISMATCH');
		}
	}

	/* email validation */
	if ($_POST['email'] == '') {
		$msg->addError('EMAIL_MISSING');
	} else if (!eregi("^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,6}$", $_POST['email'])) {
		$msg->addError('EMAIL_INVALID');
	}
	$result = mysql_query("SELECT * FROM ".TABLE_PREFIX."members WHERE email LIKE '$_POST[email]'",$db);
	if (mysql_num_rows($result) != 0) {
		$valid = 'no';
		$msg->addError('EMAIL_EXISTS');
	}

	if (!$msg->containsErrors()) {
		$_POST['password']  = $addslashes($_POST['password']);
		$_POST['real_name'] = $addslashes($_POST['real_name']);
		$_POST['email']     = $addslashes($_POST['email']);

		$sql    = "UPDATE ".TABLE_PREFIX."admins SET password='$_POST[password]', real_name='$_POST[real_name]', email='$_POST[email]' WHERE login='$_SESSION[login]'";
		$result = mysql_query($sql, $db);

		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		header('Location: '.$_base_href.'admin/index.php');
		exit;
	}
	$_POST['password']          = $stripslashes($_POST['password']);
	$_POST['confirm_password']  = $stripslashes($_POST['confirm_password']);
	$_POST['real_name']         = $stripslashes($_POST['real_name']);
	$_POST['email']             = $stripslashes($_POST['email']);
} 

require(AT_INCLUDE_PATH.'header.inc.php'); 

$sql = "SELECT * FROM ".TABLE_PREFIX."admins WHERE login='$_SESSION[login]'";
$result = mysql_query($sql, $db);
if (!($row = mysql_fetch_assoc($result))) {
	$msg->addError('USER_NOT_FOUND');
	$msg->printErrors();
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}
if (!isset($_POST['submit'])) {
	$_POST = $row;
	$_POST['confirm_password'] = $_POST['password'];
}

?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<div class="input-form">
	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="password"><?php echo _AT('password'); ?></label><br />
		<input type="password" name="password" id="password" size="25" value="<?php echo htmlspecialchars($_POST['password']); ?>" />
	</div>

	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="password2"><?php echo _AT('confirm_password'); ?></label><br />
		<input type="password" name="confirm_password" id="password2" size="25" value="<?php echo htmlspecialchars($_POST['confirm_password']); ?>"  />
	</div>

	<div class="row">
		<label for="real_name"><?php echo _AT('real_name'); ?></label><br />
		<input type="text" name="real_name" id="real_name" size="30" value="<?php echo htmlspecialchars($_POST['real_name']); ?>" />
	</div>

	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="email"><?php echo _AT('email'); ?></label><br />
		<input type="text" name="email" id="email" size="30" value="<?php echo htmlspecialchars($_POST['email']); ?>" />
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>