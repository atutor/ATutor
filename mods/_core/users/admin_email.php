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

define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_USERS);

if ($_POST['cancel']) {
	$msg->addFeedback('CANCELLED');

	header('Location: users.php#feedback');
	exit;
} else if ($_POST['submit']) {
	$missing_fields = array();

	$_POST['subject'] = trim($_POST['subject']);
	$_POST['body'] = trim($_POST['body']);

	if (($_POST['to'] == '') || ($_POST['to'] == 0)) {
		$missing_fields[] = _AT('to');
	}

	if ($_POST['subject'] == '') {
		$missing_fields[] = _AT('subject');
	}

	if ($_POST['body'] == '') {
		$missing_fields[] = _AT('body');
	}

	if ($missing_fields) {
		$missing_fields = implode(', ', $missing_fields);
		$msg->addError(array('EMPTY_FIELDS', $missing_fields));
	}
	if (!$msg->containsErrors()) {
		if ($_POST['to'] == 1) {
			// choose all instructors
			$sql	= "SELECT * FROM %smembers WHERE status = ".AT_STATUS_INSTRUCTOR;
		} else if ($_POST['to'] == 2) {
			// choose all students
			$sql 	= "SELECT * FROM %smembers WHERE status = ".AT_STATUS_STUDENT;
		} else {
			// choose all members
			$sql 	= "SELECT * FROM %smembers WHERE status = ".AT_STATUS_INSTRUCTOR." OR status = ".AT_STATUS_STUDENT;
		}
		
        $rows_members = queryDB($sql, array(TABLE_PREFIX));

		require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');

		$mail = new ATutorMailer;
            
        foreach($rows_members as $row){
			$mail->AddBCC($row['email']);
		}


		$mail->From     = $_config['contact_email'];
		$mail->FromName = $_config['site_name'];
		$mail->AddAddress($_config['contact_email']);
		$mail->Subject = $stripslashes($_POST['subject']);
		$mail->Body    = $stripslashes($_POST['body']);

		if(!$mail->Send()) {
		   //echo 'There was an error sending the message';
		   $msg->printErrors('SENDING_ERROR');
		   exit;
		}
		unset($mail);

		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		header('Location: users.php');
		exit;
	}
}

$title = _AT('admin_email');

$onload = 'document.form.subject.focus();';

require(AT_INCLUDE_PATH.'header.inc.php');

$sql	= "SELECT COUNT(*) AS cnt FROM %smembers ORDER BY login";
$row = queryDB($sql,array(TABLE_PREFIX), TRUE);

if ($row['cnt'] == 0) {
	$msg->printErrors('NO_MEMBERS');
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

$savant->display('admin/users/admin_email.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php'); ?>