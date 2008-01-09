<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_USERS);

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.AT_BASE_HREF.'admin/admins/index.php');
	exit;
} else if (isset($_POST['submit'])) {
	if ($_POST['password'] == '') { 
		$msg->addError(array('EMPTY_FIELDS', _AT('password')));
	} else {
		// check for valid passwords
		if ($_POST['password'] != $_POST['password2']){
			$msg->addError('PASSWORD_MISMATCH');
		}
	}

	if (!$msg->containsErrors()) {
		$_POST['password']     = $addslashes($_POST['password']);

		$sql    = "UPDATE ".TABLE_PREFIX."admins SET password='$_POST[password]', last_login=last_login WHERE login='$_POST[login]'";
		$result = mysql_query($sql, $db);

		$sql    = "UPDATE ".TABLE_PREFIX."admins SET password='********' WHERE login='$_POST[login]'";
		write_to_log(AT_ADMIN_LOG_UPDATE, 'admins', mysql_affected_rows($db), $sql);

		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		header('Location: '.AT_BASE_HREF.'admin/admins/index.php');
		exit;
	}
	$_POST['login'] = $stripslashes($_POST['login']);
}


require(AT_INCLUDE_PATH.'header.inc.php');

$_GET['login'] = $addslashes($_REQUEST['login']);

$sql = "SELECT login FROM ".TABLE_PREFIX."admins WHERE login='$_GET[login]'";
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
	if (query_bit($row['privileges'], AT_ADMIN_PRIV_ADMIN)) {
		$_POST['priv_admin'] = 1;
	}
	$_POST['privs'] = intval($row['privileges']);
}

?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<input type="hidden" name="login" value="<?php echo $row['login']; ?>" />
	<div class="input-form">
		<div class="row">
			<h3><?php echo htmlspecialchars($row['login']); ?></h3>
		</div>

		<div class="row">
			<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="password"><?php echo _AT('password'); ?></label><br />
			<input type="password" name="password" id="password" value="" size="30" />
		</div>

		<div class="row">
			<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="password2"><?php echo _AT('confirm_password'); ?></label><br />
			<input type="password" name="password2" id="password2" value="" size="30" />
		</div>

		<div class="row buttons">
			<input type="submit" name="submit" value="<?php echo _AT('submit'); ?>" accesskey="s" />
			<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
		</div>
	</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>