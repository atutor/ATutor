<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
/* Inclusive Design Institute                                   */
/* http://atutor.ca                                             */
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: bounce.php 10055 2010-06-29 20:30:24Z cindy $

$_user_location	= 'public';
define(AT_INCLUDE_PATH, '../../../include/');
include(AT_INCLUDE_PATH.'vitals.inc.php');
include(AT_JB_INCLUDE.'classes/Job.class.php');

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: login.php');
	exit;

} else if (isset($_POST['form_password_reminder'])) {
	//get database info to create & email change-password-link
	$_POST['form_email'] = $addslashes($_POST['form_email']);
	$sql	= "SELECT id, username, employer_name, password, email FROM ".TABLE_PREFIX."jb_employers WHERE email='$_POST[form_email]'";
	$result = mysql_query($sql,$db);
	if ($row = mysql_fetch_assoc($result)) {
		
		//date link was generated (# days since epoch)
		$gen = intval(((time()/60)/60)/24);

		$hash = sha1($row['id'] + $gen + $row['password']);
		$hash_bit = substr($hash, 5, 15);
		
		$change_link = $_base_href.AT_JB_BASENAME.'employer/password_reminder.php?id='.$row['id'].'&g='.$gen.'&h='.$hash_bit;
		if($row['employer_name'] != ''){
			$reply_name = $row['employer_name'];
		}else{
			$reply_name = $row['employer_name'];
		}
		$tmp_message  = _AT(array('password_request2',$reply_name, $row['employer_name'], AT_PASSWORD_REMINDER_EXPIRY, $change_link));

		//send email
		require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');
		$mail = new ATutorMailer;
		$mail->From     = $_config['contact_email'];
		$mail->AddAddress($row['email']);
		$mail->Subject = $_config['site_name'].'-'._AT('job_board') . ': ' . _AT('password_forgot');
		$mail->Body    = $tmp_message;

		if(!$mail->Send()) {
		   $msg->addError('SENDING_ERROR');
		   $savant->display('password_reminder_feedback.tmpl.php'); 
		   exit;
		}

		$msg->addFeedback('CONFIRM_EMAIL2');
		unset($mail);

		$savant->display('password_reminder_feedback.tmpl.php'); 

	} else {
		$msg->addError('EMAIL_NOT_FOUND');
		$savant->display('password_reminder.tmpl.php'); 
	}

} else if (isset($_REQUEST['id']) && isset($_REQUEST['g']) && isset($_REQUEST['h'])) {
//coming from an email link

	//check if expired
	$current = intval(((time()/60)/60)/24);
	$expiry_date =  $_REQUEST['g'] + AT_PASSWORD_REMINDER_EXPIRY; //2 days after creation

	if ($current > $expiry_date) {
		$msg->addError('INVALID_LINK'); 
		$savant->display('password_reminder_feedback.tmpl.php'); 
		exit;
	}

	/* check if already visited (possibley add a "last login" field to members table)... if password was changed, won't work anyway. do later. */

	//check for valid hash
	$sql	= "SELECT password, email FROM ".TABLE_PREFIX."jb_employers WHERE id=".intval($_REQUEST['id']);
	$result = mysql_query($sql,$db);
	if ($row = mysql_fetch_assoc($result)) {
		$email = $row['email'];

		$hash = sha1($_REQUEST['id'] + $_REQUEST['g'] + $row['password']);
		$hash_bit = substr($hash, 5, 15);

		if ($_REQUEST['h'] != $hash_bit) {
			$msg->addError('INVALID_LINK');
			$savant->display('password_reminder_feedback.tmpl.php'); 
		} else if (($_REQUEST['h'] == $hash_bit) && !isset($_POST['form_change'])) {
			$savant->assign('id', $_REQUEST['id']);
			$savant->assign('g', $_REQUEST['g']);
			$savant->assign('h', $_REQUEST['h']);
			$savant->display('employer/password_change.tmpl.php');
		}
	} else {
		$msg->addError('INVALID_LINK');
		$savant->display('password_reminder_feedback.tmpl.php'); 
		exit;
	}

	//changing the password
	if (isset($_POST['form_change'])) {

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
			//save data
			$password   = $addslashes($_POST['form_password_hidden']);

			$sql	= "UPDATE ".TABLE_PREFIX."jb_employers SET password='".$password."', last_login=last_login WHERE id=".intval($_REQUEST['id']);
			$result = mysql_query($sql,$db);

			//send confirmation email
			require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');

			$tmp_message  = _AT(array('password_change_confirm', $_config['site_name'].': '._AT('job_board'), $_base_href.AT_JB_BASENAME.'employer/login.php'))."\n\n";

			$mail = new ATutorMailer;
			$mail->From     = $_config['contact_email'];
			$mail->AddAddress($email);
			$mail->Subject = $_config['site_name'] . ': ' . _AT('password_forgot');
			$mail->Body    = $tmp_message;

			if(!$mail->Send()) {
			   $msg->printErrors('SENDING_ERROR');
			   exit;
			}

			$msg->addFeedback('PASSWORD_CHANGED');
			unset($mail);
			
			header('Location:login.php');

		} else {
			$savant->assign('id', $_REQUEST['id']);
			$savant->assign('g', $_REQUEST['g']);
			$savant->assign('h', $_REQUEST['h']);
			$savant->display('password_change.tmpl.php');
		} 
	}

} else {
	$savant->display('password_reminder.tmpl.php');
}


?>