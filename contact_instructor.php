<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

$_user_location = 'public';

define('AT_INCLUDE_PATH', 'include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

if (isset($_SESSION['member_id']) && $_SESSION['member_id']) {
	$to = $_base_href . 'users/browse.php';
} else {
	$to = $_base_href . 'browse.php';
}


if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: ' . $to);
	exit;
}

$row = array();

$id = intval($_REQUEST['id']);
if (isset($system_courses[$id], $system_courses[$id]['member_id'])) {
	$sql	= "SELECT M.member_id, M.first_name, M.last_name, M.email FROM ".TABLE_PREFIX."members M WHERE M.member_id={$system_courses[$id][member_id]}";
	$result = mysql_query($sql, $db);
	$row = mysql_fetch_assoc($result);
}

if ($row) {
	$instructor_name = get_display_name($row['member_id']);
	$instructor_email = AT_print($row['email'], 'members.email');
} else {
	$msg->addError('INST_INFO_NOT_FOUND');
	header('Location: ' . $to);
	exit;
}

if (isset($_POST['submit'])) {
	$missing_fields = array();

	$to_email = $_POST['email'];
	$_POST['subject'] = trim($_POST['subject']);
	$_POST['body']	  = trim($_POST['body']);

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

		require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');

		if (empty($_POST['from_email'])) {
			$_POST['from_email'] = $instructor_email;
		}
		if (empty($_POST['from'])) {
			$_POST['from'] = '';
		}

		$mail = new ATutorMailer;

		$mail->From     = $_POST['from_email'];
		$mail->FromName = $_POST['from'];
		$mail->AddAddress($instructor_email, $instructor_name);
		$mail->Subject = stripslashes($addslashes($_POST['subject']));
		$mail->Body    = stripslashes($addslashes($_POST['body']));

		if(!$mail->Send()) {
		   $msg->addError('SENDING_ERROR');
	   	   header('Location: ' . $to);
		   exit;
		}
		unset($mail);
		
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		header('Location: ' . $to);
		exit;
	}

}

require (AT_INCLUDE_PATH.'header.inc.php');
?>
<?php 
$savant->display('contact_instructor.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php'); ?>