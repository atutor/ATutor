<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton	*/
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
admin_authenticate(AT_ADMIN_PRIV_ADMIN);

$_GET['alogin'] = $addslashes($_GET['alogin']);

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php');
	exit;
} else if (isset($_POST['submit_yes'])) {
	$sql = "DELETE FROM ".TABLE_PREFIX."admins WHERE login='$_POST[alogin]'";
	$result = mysql_query($sql, $db);

	write_to_log(AT_ADMIN_LOG_DELETE, 'admins', mysql_affected_rows($db), $sql);

	$msg->addFeedback('USER_DELETED');
	header('Location: index.php');
	exit;
}
?>
<?php require(AT_INCLUDE_PATH.'header.inc.php'); ?>
<?php

if (!strcasecmp($_GET['alogin'], $_SESSION['login'])) {
	$msg->addError('CANNOT_DELETE_OWN_ACCOUNT');
	$msg->printErrors();
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

$sql = "SELECT * FROM ".TABLE_PREFIX."admins WHERE login='$_GET[alogin]'";
$result = mysql_query($sql, $db);
if (!($row = mysql_fetch_assoc($result))) {
	echo _AT('no_user_found');
} else {
	$hidden_vars['alogin'] = $_GET['alogin'];
	$confirm = array('DELETE_USER', $row['login']);
	$msg->addConfirm($confirm, $hidden_vars);
	$msg->printConfirm();
}
?>
<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>