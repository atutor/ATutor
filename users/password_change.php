<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$

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
	if (!empty($_POST['form_old_password_hidden'])) {
		//check if old password entered is correct
		$sql	= "SELECT password FROM %smembers WHERE member_id=%d";
		$row = queryDB($sql, array(TABLE_PREFIX, $_SESSION['member_id']), TRUE);
		
        if(count($row) > 0){
			if ($row['password'] != $_POST['form_old_password_hidden']) {
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

	/* password check: password is verified front end by javascript. here is to handle the errors from javascript */
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
		// insert into the db.
		$password   = $addslashes($_POST['form_password_hidden']);

		$sql = "UPDATE %smembers SET password='%s', creation_date=creation_date, last_login=last_login WHERE member_id=%d";
		$result = queryDB($sql, array(TABLE_PREFIX, $password, $_SESSION['member_id']));
		
		if ($result == 0) {
			$msg->addError('DB_NOT_UPDATED');\
			header('Location:'. $_SERVER['PHP_SELF']);
			exit;
		}

		$msg->addFeedback('PASSWORD_CHANGED');
		header('Location: ./profile.php');
		exit;
	}
}

/* template starts here */
$savant->display('users/password_change.tmpl.php');

?>