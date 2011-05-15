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

$_user_location = 'admin';

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_USERS);

if (isset($_GET['deny']) && isset($_GET['id'])) {
	header('Location: admin_deny.php?id='.$_GET['id']);
	exit;
	/*
	$sql = 'DELETE FROM '.TABLE_PREFIX.'instructor_approvals WHERE member_id='.intval($_GET['id']);
	$result = mysql_query($sql, $db);

	write_to_log(AT_ADMIN_LOG_DELETE, 'instructor_approvals', mysql_affected_rows($db), $sql);
	*/

} else if (isset($_GET['approve']) && isset($_GET['id'])) {
	$id = intval($_GET['id']);

	$sql = 'DELETE FROM '.TABLE_PREFIX.'instructor_approvals WHERE member_id='.$id;
	$result = mysql_query($sql, $db);

	write_to_log(AT_ADMIN_LOG_DELETE, 'instructor_approvals', mysql_affected_rows($db), $sql);

	$sql = 'UPDATE '.TABLE_PREFIX.'members SET status='.AT_STATUS_INSTRUCTOR.', creation_date=creation_date, last_login=last_login WHERE member_id='.$id;
	$result = mysql_query($sql, $db);

	write_to_log(AT_ADMIN_LOG_UPDATE, 'members', mysql_affected_rows($db), $sql);

	/* notify the users that they have been approved: */
	$sql   = "SELECT email, first_name, last_name FROM ".TABLE_PREFIX."members WHERE member_id=$id";
	$result = mysql_query($sql, $db);
	if ($row = mysql_fetch_assoc($result)) {
		$to_email = $row['email'];

		if ($row['first_name']!="" || $row['last_name']!="") {
			$tmp_message  = $row['first_name'].' '.$row['last_name'].",\n\n";		
		}	
		$tmp_message .= _AT('instructor_request_reply', AT_BASE_HREF);

		if ($to_email != '') {
			require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');

			$mail = new ATutorMailer;

			$mail->From     = $_config['contact_email'];
			$mail->AddAddress($to_email);
			$mail->Subject = _AT('instructor_request');
			$mail->Body    = $tmp_message;

			if(!$mail->Send()) {
			   //echo 'There was an error sending the message';
			   $msg->addError('SENDING_ERROR');
			}

			unset($mail);
		}
	}

	$msg->addFeedback('PROFILE_UPDATED_ADMIN');
} else if (!empty($_GET) && !$_GET['submit']) {
	$msg->addError('NO_ITEM_SELECTED');
}

require(AT_INCLUDE_PATH.'header.inc.php'); 

$sql	= "SELECT M.login, M.first_name, M.last_name, M.email, M.member_id, A.* FROM ".TABLE_PREFIX."members M, ".TABLE_PREFIX."instructor_approvals A WHERE A.member_id=M.member_id ORDER BY M.login";
$result = mysql_query($sql, $db);
$num_pending = mysql_num_rows($result);
?>

<?php 
$savant->display('admin/users/instructor_requests.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php'); ?>