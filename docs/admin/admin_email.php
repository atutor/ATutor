<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/


$_user_location = 'admin';
define('AT_INCLUDE_PATH', '../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

if ($_POST['cancel']) {
	$msg->addFeedback('CANCELLED');

	header('Location: users.php#feedback');
	exit;
} else if ($_POST['submit']) {
	$_POST['subject'] = trim($_POST['subject']);
	$_POST['body'] = trim($_POST['body']);

	if ($_POST['subject'] == '') {
		$msg->addError('MSG_SUBJECT_EMPTY');
	}

	if ($_POST['body'] == '') {
		$msg->addError('MSG_BODY_EMPTY');
	}

	if (($_POST['to'] == '') || ($_POST['to'] == 0)) {
		$msg->addError('NO_RECIPIENT');
	}
	
	if (!$msg->containsErrors()) {
		if ($_POST['to'] == 1) {
			// choose all instructors
			$sql	= "SELECT * FROM ".TABLE_PREFIX."members WHERE status = 1 ORDER BY login";
		} else if ($_POST['to'] == 2) {
			// choose all students
			$sql 	= "SELECT * FROM ".TABLE_PREFIX."members WHERE status = 0 ORDER BY login";
		} else {
			// choose all members
			$sql 	= "SELECT * FROM ".TABLE_PREFIX."members ORDER BY login";
		}
		
		$result = mysql_query($sql,$db);

		require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');

		$mail = new ATutorMailer;

		while ($row = mysql_fetch_assoc($result)) {
			$mail->AddBCC($row['email']);
		}


		$mail->From     = ADMIN_EMAIL;
		$mail->FromName = ADMIN_USERNAME;
		$mail->AddAddress(ADMIN_EMAIL);
		$mail->Subject = $_POST['subject'];
		$mail->Body    = $_POST['body'];

		if(!$mail->Send()) {
		   $msg->printErrors('MSG_NOT_SENT');
		   exit;
		}
		unset($mail);

		$msg->addFeedback('MSG_SENT');
		header('Location: users.php#feedback');
		exit;
	}
}

$title = _AT('admin_email');

$onload = 'onload="document.form.subject.focus()"';

require(AT_INCLUDE_PATH.'header.inc.php');



$msg->printErrors();

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
		<label for="to"><?php echo  _AT('to'); ?></label><br />
		<input type="radio" name="to" value="3" checked="checked" id="all" /><label for="all"><?php echo  _AT('all'); ?></label>  
	  <input type="radio" name="to" value="1" id="inst" <?php if ($_POST['to'] == 1) { echo 'checked="checked"'; } ?> /><label for="inst"><?php echo  _AT('instructors'); ?></label>
	  <input type="radio" name="to" value="2" id="stud" <?php if ($_POST['to'] == 2) { echo 'checked="checked"'; } ?> /><label for="stud"><?php echo  _AT('students'); ?></label>
	</div>

	<div class="row">
		<label for="subject"><?php echo _AT('subject'); ?></label><br />
		<input type="text" name="subject" size="40" id="subject" value="<?php echo $_POST['subject']; ?>" />
	</div>

	<div class="row">
		<label for="body"><?php echo _AT('body'); ?></label><br />
		<textarea cols="55" rows="18" name="body" id="body"><?php echo $_POST['body']; ?></textarea>
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('send'); ?>" /> 
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>