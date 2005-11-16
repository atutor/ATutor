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
	$sql	= "SELECT M.login, M.first_name, M.last_name, M.email FROM ".TABLE_PREFIX."members M WHERE M.member_id={$system_courses[$id][member_id]}";
	$result = mysql_query($sql, $db);
	$row = mysql_fetch_assoc($result);
}

if ($row) {
	$instructor_name = AT_print($row['login'], 'members.login');
	$instructor_email = AT_print($row['email'], 'members.email');
} else {
	$msg->addError('INST_INFO_NOT_FOUND');
	header('Location: ' . $to);
	exit;
}

if (isset($_POST['submit'])) {
	$to_email = $_POST['email'];
	$_POST['subject'] = trim($_POST['subject']);
	$_POST['body']	  = trim($_POST['body']);

	if ($_POST['subject'] == '') {
		$msg->addError('MSG_SUBJECT_EMPTY');
	}
		
	if ($_POST['body'] == '') {
		$msg->addError('MSG_BODY_EMPTY');
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
		
		$msg->addFeedback('MSG_SENT');
		header('Location: ' . $to);
		exit;
	}

}

require (AT_INCLUDE_PATH.'header.inc.php');
?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="id" value="<?php echo $id; ?>" />

<div class="input-form">
	<div class="row">
		<?php echo _AT('to'); ?><br />
		<?php echo $instructor_name; ?>
	</div>

	<div class="row">
		<label for="from"><?php echo _AT('from_name'); ?></label><br />
		<input type="text" class="formfield" name="from" id="from" size="40" value="<?php echo $student_name;?>" />
	</div>

	<div class="row">
		<label for="from_email"><?php echo _AT('from_email'); ?></label><br />
		<input type="text" class="formfield" name="from_email" id="from_email" size="40" value="<?php echo $student_email;?>" />
	</div>

	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="subject"><?php echo _AT('subject'); ?></label><br />
		<input type="text" class="formfield" name="subject" id="subject" size="40" value="<?php echo $_POST['subject']; ?>" />
	</div>

	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="body"><?php echo _AT('body'); ?></label><br />
		<textarea class="formfield" cols="55" rows="15" id="body" name="body" wrap="wrap"><?php echo $_POST['body']; ?></textarea>
	</div>

	<div class="buttons row">
		<input type="submit" name="submit" class="button" value="<?php echo _AT('send_message'); ?>" accesskey="s" />  
		<input type="submit" name="cancel" class="button" value="<?php echo _AT('cancel'); ?>" />
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>