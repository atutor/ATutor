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
// $Id: profile.php 6025 2006-03-28 20:13:55Z joel $

$page = 'profile';
$_user_location	= 'users';

define('AT_INCLUDE_PATH', '../include/');
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
	Header('Location: profile.php');
	exit;
}

if (isset($_POST['submit'])) {
	if (!empty($_POST['old_password'])) {
		//check if old password entered is correct
		$sql	= "SELECT password FROM ".TABLE_PREFIX."members WHERE member_id=$_SESSION[member_id]";
		$result = mysql_query($sql,$db);
		if ($row = mysql_fetch_assoc($result)) {
			if ($row['password'] != trim($_POST['old_password'])) {
				$msg->addError('WRONG_PASSWORD');
				Header('Location: password_change.php');
				exit;
			}
		}
	} else {
		$msg->addError(array('EMPTY_FIELDS', _AT('password')));
		header('Location: password_change.php');
		exit;
	}

	// new password check
	if ($_POST['password'] == '') { 
		$msg->addError(array('EMPTY_FIELDS', _AT('password')));
	} else {
		if ($_POST['password'] != $_POST['password2']) {
			$msg->addError('PASSWORD_MISMATCH');
		} else if (!preg_match('/^\w{8,}$/u', $_POST['password'])) { // strlen($_POST['password']) < 8
			$msg->addError('PASSWORD_LENGTH');
		} else if ((preg_match('/[a-z]+/i', $_POST['password']) + preg_match('/[0-9]+/i', $_POST['password']) + preg_match('/[_\-\/+!@#%^$*&)(|.]+/i', $_POST['password'])) < 2) {
			$msg->addError('PASSWORD_CHARS');
		}
	}
		
	if (!$msg->containsErrors()) {			
		// insert into the db.
		$_POST['password']   = $addslashes($_POST['password']);

		$sql = "UPDATE ".TABLE_PREFIX."members SET password='$_POST[password]', creation_date=creation_date, last_login=last_login WHERE member_id=$_SESSION[member_id]";
		$result = mysql_query($sql,$db);
		if (!$result) {
			require(AT_INCLUDE_PATH.'header.inc.php');
			$msg->printErrors('DB_NOT_UPDATED');
			require(AT_INCLUDE_PATH.'footer.inc.php');
			exit;
		}

		$msg->addFeedback('PASSWORD_CHANGED');
		header('Location: ./profile.php');
		exit;
	}
}

/* template starts here */
$onload = 'document.form.old_password.focus();';
$savant->display('users/password_change.tmpl.php');

?>