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
// $Id$

define('AT_INCLUDE_PATH', '../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_COURSE_EMAIL, AT_PRIV_RETURN);

$course = intval($_GET['course']);

if ($course == 0) {
	$course = $_SESSION['course_id'];
}

if (isset($_POST['cancel'])) {
	header('Location: index.php');
	exit;
} else if (isset($_POST['submit'])) {
	$_POST['to_enrolled'] = trim($_POST['to_enrolled']);
	$_POST['to_unenrolled'] = trim($_POST['to_unenrolled']);
	$_POST['to_alumni'] = trim($_POST['to_alumni']);
	$_POST['to_instructor'] = trim($_POST['to_instructor']);

	$_POST['subject'] = trim($_POST['subject']);
	$_POST['body'] = trim($_POST['body']);

	if ( ($_POST['to_enrolled']   == '') &&
		 ($_POST['to_unenrolled'] == '') &&
		 ($_POST['to_alumni']     == '') &&
		 ($_POST['to_instructor'] == '') )
	{
		$msg->addError('MSG_TO_EMPTY');
	}

	if ($_POST['subject'] == '') {
		$msg->addError('MSG_SUBJECT_EMPTY');
	}

	if ($_POST['body'] == '') {
		$msg->addError('MSG_BODY_EMPTY');
	}

	if (!$msg->containsErrors()) {
		$sql	= "SELECT * FROM ".TABLE_PREFIX."course_enrollment C, ".TABLE_PREFIX."members M WHERE C.course_id=$course AND C.member_id=M.member_id AND ";
		
		if ($_POST['to_enrolled']) {
			// choose all enrolled
			$sql 	.= "(C.approved='y' AND C.role<>'Instructor') OR ";
		}

		if ($_POST['to_unenrolled']) {
			// choose all unenrolled
			$sql .= "C.approved='n' OR ";
		}
		
		if ($_POST['to_alumni']) {
			// choose all alumni
			$sql 	.= "C.approved='a' OR ";
		}

		if ($_POST['to_instructor']){
			// choose all instructor
			$sql	.= "C.role='Instructor' OR ";
		} 
		
		$sql = substr_replace ($sql, '', -4);
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
		   //echo 'There was an error sending the message';
		   $msg->printErrors('SENDING_ERROR');
		   exit;
		}
		unset($mail);

		$msg->addFeedback('MSG_SENT');
		header('Location: index.php');
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}
}

require(AT_INCLUDE_PATH.'header.inc.php');

$sql	= "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."course_enrollment C, ".TABLE_PREFIX."members M WHERE C.course_id=$course AND C.member_id=M.member_id AND M.member_id<>$_SESSION[member_id] ORDER BY C.approved, M.login";
$result = mysql_query($sql,$db);
$row	= mysql_fetch_array($result);
if ($row['cnt'] == 0) {
	$msg->printInfos('NO_STUDENTS');
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="course" value="<?php echo $course; ?>" />

<div class="input-form">
	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div>
		<?php echo  _AT('to'); ?><br />
		<input type="checkbox" name="to_instructor" value="1" id="instructor" /><label for="instructor"><?php echo  _AT('instructors'); ?></label>
		<input type="checkbox" name="to_enrolled" value="1" id="enrolled" /><label for="enrolled"><?php echo  _AT('enrolled'); ?></label>
		<input type="checkbox" name="to_unenrolled" value="1" id="unenrolled" /><label for="unenrolled"><?php echo  _AT('unenrolled'); ?></label>
		<input type="checkbox" name="to_alumni" value="1" id="alumni" /><label for="alumni"><?php echo  _AT('alumni'); ?></label>
	</div>

	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="subject"><?php echo _AT('subject'); ?></label><br />
		<input type="text" name="subject" size="40" id="subject" value="<?php echo $_POST['subject']; ?>" />
	</div>

	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="body"><?php echo _AT('body'); ?></label><br />
		<textarea cols="55" rows="18" name="body" id="body"><?php echo $_POST['body']; ?></textarea>
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('send'); ?>" accesskey="s" /> 
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>