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

define('AT_INCLUDE_PATH', '../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

$course = intval($_GET['course']);

if ($course == 0) {
	$course = $_SESSION['course_id'];
}


/* make sure we own this course that we're approving for! */

if (!(authenticate(AT_PRIV_ADMIN, AT_PRIV_RETURN)) && !(authenticate(AT_PRIV_COURSE_EMAIL, AT_PRIV_RETURN))) {
	$msg->printErrors('PREFS_NO_ACCESS');
	exit;
}

if ($_POST['cancel']) {
	header('Location: index.php');
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

	if (!$msg->containsErrors()) {
		// note: doesn't list the owner of the course or the person (TA) editing the list.
		$sql	= "SELECT * FROM ".TABLE_PREFIX."course_enrollment C, ".TABLE_PREFIX."members M WHERE C.course_id=$course AND C.member_id=M.member_id AND M.member_id<>$_SESSION[member_id] ORDER BY C.approved, M.login";

		$result = mysql_query($sql,$db);

		require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');

		$mail = new ATutorMailer;

		while ($row = mysql_fetch_assoc($result)) {
			$mail->AddBCC($row['email']);
		}

		$result = mysql_query("SELECT email, first_name, last_name FROM ".TABLE_PREFIX."members WHERE member_id=$_SESSION[member_id]", $db);
		$row	= mysql_fetch_assoc($result);

		$mail->From     = $row['email'];
		$mail->FromName = $row['first_name'] . ' ' . $row['last_name'];
		$mail->AddAddress($row['email']);
		$mail->Subject = $_POST['subject'];
		$mail->Body    = $_POST['body'];

		if(!$mail->Send()) {
		   echo 'There was an error sending the message';
		   exit;
		}
		unset($mail);

		$msg->addFeedback('MSG_SENT');
		header('Location: index.php');
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}
}

$title = _AT('course_email');
require(AT_INCLUDE_PATH.'header.inc.php');

/* we own this course! */
	$sql	= "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."course_enrollment C, ".TABLE_PREFIX."members M WHERE C.course_id=$course AND C.member_id=M.member_id AND M.member_id<>$_SESSION[member_id] ORDER BY C.approved, M.login";
	$result = mysql_query($sql,$db);
	$row	= mysql_fetch_array($result);
	if ($row['cnt'] == 0) {
		$msg->printErrors('NO_STUDENTS');
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}

?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="course" value="<?php echo $course; ?>" />

<div class="input-form">
	<div class="row">
		<label for="subject"><?php echo _AT('subject'); ?></label><br />
		<input type="text" name="subject" size="40" id="subject" value="<?php echo $_POST['subject']; ?>" />
	</div>

	<div class="row">
		<label for="body"><?php echo _AT('body'); ?></label><br />
		<textarea cols="55" rows="18" name="body" id="body"><?php echo $_POST['body']; ?></textarea>
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('send'); ?>" accesskey="s" /> 
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>