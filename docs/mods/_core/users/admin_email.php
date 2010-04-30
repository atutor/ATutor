<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id: admin_email.php 7208 2008-01-09 16:07:24Z greg $

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
			$sql	= "SELECT * FROM ".TABLE_PREFIX."members WHERE status = ".AT_STATUS_INSTRUCTOR;
		} else if ($_POST['to'] == 2) {
			// choose all students
			$sql 	= "SELECT * FROM ".TABLE_PREFIX."members WHERE status = ".AT_STATUS_STUDENT;
		} else {
			// choose all members
			$sql 	= "SELECT * FROM ".TABLE_PREFIX."members WHERE status = ".AT_STATUS_INSTRUCTOR." OR status = ".AT_STATUS_STUDENT;
		}
		
		$result = mysql_query($sql,$db);

		require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');

		$mail = new ATutorMailer;

		while ($row = mysql_fetch_assoc($result)) {
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

$sql	= "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."members ORDER BY login";
$result = mysql_query($sql,$db);
$row	= mysql_fetch_array($result);
if ($row['cnt'] == 0) {
	$msg->printErrors('NO_MEMBERS');
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
<input type="hidden" name="admin" value="admin" />

<div class="input-form">
	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><?php echo  _AT('to'); ?><br />
		<input type="radio" name="to" value="3" checked="checked" id="all" /><label for="all"><?php echo _AT('all_users'); ?></label>  
	  <input type="radio" name="to" value="1" id="inst" <?php if ($_POST['to'] == AT_STATUS_INSTRUCTOR) { echo 'checked="checked"'; } ?> /><label for="inst"><?php echo  _AT('instructors'); ?></label>
	  <input type="radio" name="to" value="2" id="stud" <?php if ($_POST['to'] == AT_STATUS_STUDENT) { echo 'checked="checked"'; } ?> /><label for="stud"><?php echo  _AT('students'); ?></label>
	</div>

	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="subject"><?php echo _AT('subject'); ?></label><br />
		<input type="text" name="subject" size="40" id="subject" value="<?php echo $_POST['subject']; ?>" />
	</div>

	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="body"><?php echo _AT('body'); ?></label><br />
		<textarea cols="55" rows="18" name="body" id="body"><?php echo $_POST['body']; ?></textarea>
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('send'); ?>" accesskey="s" /> 
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>