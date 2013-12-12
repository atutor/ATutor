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

if (!isset($_SESSION['token']) || !$_SESSION['token']) {
	$_SESSION['token'] = md5(mt_rand());
}

if (isset($_POST['submit'])) {

	$this_password = $_POST['form_password_hidden'];

	// password check
	if (!empty($this_password)) {
		//check if old password entered is correct

		$sql	= "SELECT password FROM %smembers WHERE member_id=%d";
		$row_pwd = queryDB($sql,array(TABLE_PREFIX, $_SESSION['member_id']), TRUE);
		
		if(count($row_pwd) >0){
			if ($row_pwd['password'] != $this_password) {
				$msg->addError('WRONG_PASSWORD');
				Header('Location: email_change.php');
				exit;
			}
		}
	} else {
		$msg->addError(array('EMPTY_FIELDS', _AT('password')));
		header('Location: email_change.php');
		exit;
	}
		
	// email check
	if ($_POST['email'] == '') {
		$msg->addError(array('EMPTY_FIELDS', _AT('email')));
	} else {
		if(!preg_match("/^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,6}$/i", $_POST['email'])) {
			$msg->addError('EMAIL_INVALID');
		}

		$sql = "SELECT * FROM %smembers WHERE email='%s' AND member_id<>%d";
		$result = queryDB($sql, array(TABLE_PREFIX, $_POST['email'], $_SESSION['member_id']));
		
		if(count($result) > 0){
			$msg->addError('EMAIL_EXISTS');
		}
	}

	if (!$msg->containsErrors()) {			
		if (defined('AT_EMAIL_CONFIRMATION') && AT_EMAIL_CONFIRMATION) {
			//send confirmation email
			$sql	= "SELECT email, creation_date FROM %smembers WHERE member_id=%d";
			$row = queryDB($sql, array(TABLE_PREFIX, $_SESSION['member_id']), TRUE);

			if ($row['email'] != $_POST['email']) {
				$code = substr(md5($_POST['email'] . $row['creation_date'] . $_SESSION['member_id']), 0, 10);
				$confirmation_link = AT_BASE_HREF . 'confirm.php?id='.$_SESSION['member_id'].SEP .'e='.urlencode($_POST['email']).SEP.'m='.$code;

				/* send the email confirmation message: */
				require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');
				$mail = new ATutorMailer();

				$mail->From     = $_config['contact_email'];
				$mail->AddAddress($_POST['email']);
				$mail->Subject = SITE_NAME . ' - ' . _AT('email_confirmation_subject');
				$mail->Body    = _AT('email_confirmation_message2', $_config['site_name'], $confirmation_link);

				$mail->Send();

				$msg->addFeedback('CONFIRM_EMAIL');
			} else {
				$msg->addFeedback('CANCELLED');
			}
		} else {

			//insert into database
			$sql = "UPDATE %smembers SET email='%s', creation_date=creation_date, last_login=last_login WHERE member_id=%d";
			$result = queryDB($sql, array(TABLE_PREFIX, $_POST['email'], $_SESSION['member_id']));
			if ($result > 0 ) {
				$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
			} else{
			    $msg->addFeedback('CANCELLED');
			}

			
		}
		header('Location: ./profile.php');
		exit;
	}
}

$sql	= 'SELECT email FROM %smembers WHERE member_id=%d';
$row = queryDB($sql, array(TABLE_PREFIX, $_SESSION['member_id']), TRUE);

if (!isset($_POST['submit'])) {
	$_POST = $row;
}

/* template starts here */
$savant->assign('row', $row);
$savant->display('users/email_change.tmpl.php');

?>