<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$
define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

if ($_SESSION['valid_user'] !== true) {
	require(AT_INCLUDE_PATH.'header.inc.php');
	$info = array('INVALID_USER', $_SESSION['course_id']);
	$msg->printInfos($info);
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.AT_BASE_HREF.'admin/index.php');
	exit;
}

if (isset($_POST['submit'])) {
	if (!empty($_POST['form_old_password_hidden'])) {
		//check if old password entered is correct
		$sql	= "SELECT password FROM ".TABLE_PREFIX."admins WHERE login='$_SESSION[login]'";
		$result = mysql_query($sql,$db);
		if ($row = mysql_fetch_assoc($result)) {
			if ($row['password'] != $_POST['form_old_password_hidden']) {
				$msg->addError('WRONG_PASSWORD');
				Header('Location: my_password.php');
				exit;
			}
		}
	} else {
		$msg->addError(array('EMPTY_FIELDS', _AT('password')));
		header('Location: my_password.php');
		exit;
	}

	// new password check
	if ($_POST['password_error'] <> "")
	{
		$pwd_errors = explode(",", $_POST['password_error']);

		foreach ($pwd_errors as $pwd_error)
		{
			if ($pwd_error == "missing_password")
				$missing_fields[] = _AT('password');
			else
				$msg->addError($pwd_error);
		}
	}

	if (!$msg->containsErrors()) {			
		$password   = addslashes($_POST['form_password_hidden']);

		$sql    = "UPDATE ".TABLE_PREFIX."admins SET password='$password', last_login=last_login WHERE login='$_SESSION[login]'";
		$result = mysql_query($sql, $db);

		$msg->addFeedback('PASSWORD_CHANGED');
		header('Location: '.AT_BASE_HREF.'admin/index.php');
		exit;
	}
}

/* template starts here */
$savant->display('users/password_change.tmpl.php');

?>