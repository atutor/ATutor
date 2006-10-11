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

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_USERS);

$_REQUEST['id'] = $addslashes($_REQUEST['id']);

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.$_base_href.'admin/master_list.php');
	exit;
} else if (isset($_POST['submit'])) {
	$_POST['public_field'] = trim($_POST['public_field']);
	if ($_POST['public_field'] == '') {
		$msg->addError(array('EMPTY_FIELDS', _AT('student_id')));
	}

	if (!$msg->containsErrors()) {
		$_POST['public_field'] = $addslashes($_POST['public_field']);

		$sql = "UPDATE ".TABLE_PREFIX."master_list SET public_field='$_POST[public_field]' WHERE public_field='$_POST[id]'";
		$result = mysql_query($sql, $db);

		write_to_log(AT_ADMIN_LOG_UPDATE, 'master_list', mysql_affected_rows($db), $sql);

		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');

		header('Location: '.$_base_href.'admin/master_list.php');
		exit;
	}
} 

require(AT_INCLUDE_PATH.'header.inc.php'); 

$sql = "SELECT * FROM ".TABLE_PREFIX."master_list WHERE public_field='$_REQUEST[id]'";
$result = mysql_query($sql, $db);
if (!($row = mysql_fetch_assoc($result))) {
	$msg->addError('USER_NOT_FOUND');
	$msg->printErrors();
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
} else {
	$_POST = $row;
}
?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
<input type="hidden" name="id" value="<?php echo $_REQUEST['id']; ?>" />
<div class="input-form">
	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="student_id"><?php echo _AT('student_id'); ?></label><br />
		<input type="text" name="public_field" id="student_id" size="25" value="<?php echo $_POST['public_field']; ?>" />
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>