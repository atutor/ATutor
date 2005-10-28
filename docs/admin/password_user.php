<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: users.php 5035 2005-06-28 18:02:56Z joel $

$_user_location = 'admin';

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_USERS);

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.$_base_href.'admin/users.php');
	exit;
} else if (isset($_POST['submit'])) {
	if ($_POST['password'] == '') { 
		$msg->addError('PASSWORD_MISSING');
	} else {
		// check for valid passwords
		if ($_POST['password'] != $_POST['password2']){
			$msg->addError('PASSWORD_MISMATCH');
		}
	}

	if (!$msg->containsErrors()) {
		$_POST['id'] = intval($_POST['id']);

		$sql = "UPDATE ".TABLE_PREFIX."members SET password= '$_POST[password]' WHERE member_id=$_POST[id]";
		$result = mysql_query($sql, $db);

		$msg->addFeedback('PROFILE_UPDATED_ADMIN');
		header('Location: '.$_base_href.'admin/users.php');
		exit;
	}
	$_GET['id'] = $_POST['id'];
}


require(AT_INCLUDE_PATH.'header.inc.php');

$id = intval($_GET['id']);

$sql	= "SELECT login FROM ".TABLE_PREFIX."members WHERE member_id=$id";
$result = mysql_query($sql, $db);

if (!$row = mysql_fetch_assoc($result)) {
	$msg->printErrors('USER_NOT_FOUND');
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<input type="hidden" name="id" value="<?php echo $id; ?>" />
	<div class="input-form">
		<div class="row">
			<h3><?php echo htmlspecialchars($row['login']); ?></h3>
		</div>

		<div class="row">
			<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="password"><?php echo _AT('password'); ?></label><br />
			<input type="text" name="password" id="password" value="" size="30" />
		</div>

		<div class="row">
			<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="password2"><?php echo _AT('confirm_password'); ?></label><br />
			<input type="text" name="password2" id="password2" value="" size="30" />
		</div>

		<div class="row buttons">
			<input type="submit" name="submit" value="<?php echo _AT('submit'); ?>" />
			<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
		</div>
	</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>