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

if (isset($_POST['deny']) && isset($_POST['id'])) {
	header('Location: admin_deny.php?id='.$_POST['id']);
	exit;

} else if (isset($_POST['approve']) && isset($_POST['id'])) {
	check_csrf_token();

	$id = intval($_POST['id']);

	$sql = 'DELETE FROM %sinstructor_approvals WHERE member_id=%d';
	$result = queryDB($sql, array(TABLE_PREFIX, $id));
    global $sqlout;
	write_to_log(AT_ADMIN_LOG_DELETE, 'instructor_approvals', $result, $sqlout);

	$sql = 'UPDATE %smembers SET status=%d, creation_date=creation_date, last_login=last_login WHERE member_id=%d';
	$result = queryDB($sql, array(TABLE_PREFIX, AT_STATUS_INSTRUCTOR, $id));
    global $sqlout;
	write_to_log(AT_ADMIN_LOG_UPDATE, 'members', $result, $sqlout);

	/* notify the users that they have been approved: */
	$sql   = "SELECT email, first_name, last_name FROM %smembers WHERE member_id=%d";
	$row_member = queryDB($sql, array(TABLE_PREFIX, $id), TRUE);
	
	if(count($row_member) > 0){
        $row = $row_member;
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
} else if (!empty($_POST) && !$_POST['submit']) {
	$msg->addError('NO_ITEM_SELECTED');
}

/* Authentication info */
$timestamp = gmdate("Y-m-d\TH:i:s\Z");
$publicKey = hash('sha256', mt_rand());

require(AT_INCLUDE_PATH.'header.inc.php'); 

$sql	= "SELECT M.login, M.first_name, M.last_name, M.email, M.member_id, A.* FROM %smembers M, %sinstructor_approvals A WHERE A.member_id=M.member_id ORDER BY M.login";
$rows_approvals = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX));
$num_pending = count($rows_approvals);

$savant->assign('rows_approvals', $rows_approvals);
$savant->assign('num_pending', $num_pending);
$savant->display('admin/users/instructor_requests.tmpl.php');

require(AT_INCLUDE_PATH.'footer.inc.php'); 
?>