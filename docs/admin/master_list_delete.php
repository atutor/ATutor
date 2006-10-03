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

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.$_base_href.'admin/master_list.php');
	exit;
} else if (isset($_POST['submit_yes'])) {
	$_POST['id'] = $addslashes($_POST['id']);

	$sql = "DELETE FROM ".TABLE_PREFIX."master_list WHERE public_field='$_POST[id]'";
	$result = mysql_query($sql, $db);

	write_to_log(AT_ADMIN_LOG_DELETE, 'master_list', mysql_affected_rows($db), $sql);

	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: '.$_base_href.'admin/master_list.php');
	exit;
}
require(AT_INCLUDE_PATH.'header.inc.php'); ?>
<?php
$_GET['id'] = $addslashes($_GET['id']);
$sql = "SELECT * FROM ".TABLE_PREFIX."master_list WHERE public_field='$_GET[id]'";
$result = mysql_query($sql, $db);
if (!($row = mysql_fetch_assoc($result))) {
	echo _AT('no_user_found');
} else {
	$hidden_vars['id'] = $_GET['id'];
	$confirm = array('LIST_DELETE', $_GET['id']);
	$msg->addConfirm($confirm, $hidden_vars);
	$msg->printConfirm();
}
?>
<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>