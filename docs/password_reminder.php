<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

$page	 = 'password_reminder';
$_user_location	= 'public';
define('AT_INCLUDE_PATH', 'include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

if (isset($_POST['cancel'])) {
	header('Location: ./login.php');
	exit;
} else if (isset($_POST['form_password_reminder'])) {
	$_POST['form_email'] = $addslashes($_POST['form_email']);
	$sql	= "SELECT login, password, email FROM ".TABLE_PREFIX."members WHERE email='$_POST[form_email]'";
	$result = mysql_query($sql,$db);
	if ($row = mysql_fetch_assoc($result)) {

		$r_login = $row['login'];	
		$r_passwd= $row['password'];
		$r_email = $row['email'];

		$tmp_message  = _AT(array('password_request2',$_base_href))."\n\n";
		$tmp_message .= _AT('web_site').' : '.$_base_href."\n";
		$tmp_message .= _AT('login_name').' : '.$r_login."\n";
		$tmp_message .= _AT('password').' : '.$r_passwd."\n";

		require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');

		$mail = new ATutorMailer;

		$mail->From     = $_config['contact_email'];
		$mail->AddAddress($r_email);
		$mail->Subject = $_config['site_name'] . ': ' . _AT('password_reminder');
		$mail->Body    = $tmp_message;

		if(!$mail->Send()) {
		   //echo 'There was an error sending the message';
		   $msg->printErrors('SENDING_ERROR');
		   exit;
		}

		$msg->addFeedback('PASSWORD_SUCCESS');

		unset($mail);

		$success = true;
	} else {
		$msg->addError('EMAIL_NOT_FOUND');
	}
}

/*****************************/
/* template starts down here */

if ($errors || !$success) {
	$onload = 'document.form.form_email.focus();';
	$savant->display('password_reminder.tmpl.php');
} else {
	$savant->display('password_reminder_feedback.tmpl.php');
}

?>