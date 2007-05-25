<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$
define('AT_INCLUDE_PATH', '../../include/');
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
	if (!empty($_POST['old_password'])) {
		//check if old password entered is correct
		$sql	= "SELECT password FROM ".TABLE_PREFIX."admins WHERE login='$_SESSION[login]'";
		$result = mysql_query($sql,$db);
		if ($row = mysql_fetch_assoc($result)) {
			if ($row['password'] != trim($_POST['old_password'])) {
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
	if ($_POST['password'] == '') { 
		$msg->addError(array('EMPTY_FIELDS', _AT('password')));
	} else {
		if ($_POST['password'] != $_POST['password2']) {
			$msg->addError('PASSWORD_MISMATCH');
		} else if (strlen($_POST['password']) < 8) {
			$msg->addError('PASSWORD_LENGTH');
		} else if ((preg_match('/[a-z]+/i', $_POST['password']) + preg_match('/[0-9]+/i', $_POST['password']) + preg_match('/[_\-\/+!@#%^$*&)(|.]+/i', $_POST['password'])) < 2) {
			$msg->addError('PASSWORD_CHARS');
		}
	}
		
	if (!$msg->containsErrors()) {			
		$_POST['password']   = $addslashes($_POST['password']);

		$sql    = "UPDATE ".TABLE_PREFIX."admins SET password='$_POST[password]', last_login=last_login WHERE login='$_SESSION[login]'";
		$result = mysql_query($sql, $db);

		$msg->addFeedback('PASSWORD_CHANGED');
		header('Location: '.AT_BASE_HREF.'admin/index.php');
		exit;
	}
}

/* template starts here */
$onload = 'document.form.old_password.focus();';
$savant->display('users/password_change.tmpl.php');

?>