<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/

$_user_location	= 'users';
define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

require_once(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

global $savant;
$msg =& new Message($savant);

if ( ($_POST['description'] == '') && isset($_POST['form_request_instructor'])){
	$msg->addError('DESC_REQUIRED');
} else if (isset($_POST['form_request_instructor'])) {
	 if (AUTO_APPROVE_INSTRUCTORS == true) {
		$sql	= "UPDATE ".TABLE_PREFIX."members SET status=1 WHERE member_id=$_SESSION[member_id]";
		$result = mysql_query($sql, $db);

		$msg->addFeedback('ACCOUNT_APPROVED');
	} else {
		$_POST['description'] = $addslashes($_POST['description']);

		$sql	= "INSERT INTO ".TABLE_PREFIX."instructor_approvals VALUES ($_SESSION[member_id], NOW(), '$_POST[description]')";
		$result = mysql_query($sql, $db);
		/* email notification send to admin upon instructor request */

		if (EMAIL_NOTIFY && ADMIN_EMAIL!='') {

			$sql	= "SELECT * FROM ".TABLE_PREFIX."members WHERE member_id=$_SESSION[member_id]";
			$result = mysql_query($sql, $db);				
			if ($row = mysql_fetch_array($result)) {
				$login = $row['login'];
				$email = $row['email'];
			}
			$message = _AT('req_message_instructor', $login, $_POST['description'], $_base_href);

			require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');

			$mail = new ATutorMailer;

			$mail->From     = $email;
			$mail->AddAddress(ADMIN_EMAIL);
			$mail->Subject = _AT('req_message9');
			$mail->Body    = $message;

			if(!$mail->Send()) {
			   echo 'There was an error sending the message';
			   exit;
			}

			unset($mail);

		}
		$msg->addFeedback('ACCOUNT_PENDING');
	}

	header('Location: index.php');
	exit;
} 

$title = _AT('request_instructor_account');
require(AT_INCLUDE_PATH.'header.inc.php');

if ($msg->containsErrors()) { $msg->printErrors(); }

if (ALLOW_INSTRUCTOR_REQUESTS && ($row['status']!= 1) ) {
	$sql	= "SELECT * FROM ".TABLE_PREFIX."instructor_approvals WHERE member_id=$_SESSION[member_id]";
	$result = mysql_query($sql, $db);
	if (!($row = mysql_fetch_array($result))) {
		$msg->printInfos('REQUEST_ACCOUNT');
?>
		<br /><form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
		<p align="center">
			<input type="hidden" name="form_request_instructor" value="true" />
			<label for="desc"><?php echo _AT('give_description'); ?></label><br /><br />
			<textarea cols="40" rows="3" class="formfield" id="desc" name="description" scroll="no"></textarea><br /><br />
			<input type="submit" name="submit" value="<?php echo _AT('request_instructor_account'); ?>" class="button" />
		</p>
		</form>
<?php
	} else {
		/* already waiting for approval */
		$msg->printInfos('ACCOUNT_PENDING');
	}
} 

	require(AT_INCLUDE_PATH.'footer.inc.php');

?>