<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca                                             */
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/

define('AT_INCLUDE_PATH', 'include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

$course = intval($_GET['course']);
if ($course == 0) {
	$course = intval($_POST['form_course_id']);
}
if ($course == 0) {
	exit;
}

$sql	= "SELECT access, member_id FROM ".TABLE_PREFIX."courses WHERE course_id=$course";
$result = mysql_query($sql, $db);
$course_info = mysql_fetch_array($result);

if ($_POST['submit']) {
	$_SESSION['enroll'] = true;
	$_POST['form_course_id'] = intval($_POST['form_course_id']);

	if ($course_info[0] == 'private') {
		$sql	= "INSERT INTO ".TABLE_PREFIX."course_enrollment VALUES ($_SESSION[member_id], $_POST[form_course_id], 'n', 0, '"._AT('student')."', 0)";
		$result = mysql_query($sql, $db);

		// send the email thing. if needed

		$sql	= "SELECT notify, member_id FROM ".TABLE_PREFIX."courses WHERE course_id=$_POST[form_course_id] AND notify=1";
		$result = mysql_query($sql, $db);
		if ($row = mysql_fetch_array($result)) {
			// notify is enabled. get the email
			$sql	= "SELECT email, first_name, last_name FROM ".TABLE_PREFIX."members WHERE member_id=$row[member_id]";
			$result = mysql_query($sql, $db);
			$row	= mysql_fetch_assoc($result);

			$to_email = $row['email'];

			$message  = $row['first_name'].' '.$row['last_name'].",\n\n";
			$message .= _AT('enrol_msg', $system_courses[$_POST['form_course_id']]['title']);
			$message .= _AT('enrol_login');
			if ($to_email != '') {

				require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');

				$mail = new ATutorMailer;

				$mail->From     = ADMIN_EMAIL;
				$mail->AddAddress($to_email);
				$mail->Subject = _AT('course_enrolment');
				$mail->Body    = $message;

				if(!$mail->Send()) {
				   echo 'There was an error sending the message';
				   exit;
				}
				unset($mail);
			}
		}
	} else {
		// public or protected
		$sql	= "INSERT INTO ".TABLE_PREFIX."course_enrollment VALUES ($_SESSION[member_id], $_POST[form_course_id], 'y', 0, '"._AT('student')."', 0)";
		$result = mysql_query($sql, $db);
	}
}

if ($_SESSION['valid_user']) {
	$sql	= "SELECT * FROM ".TABLE_PREFIX."course_enrollment WHERE member_id=$_SESSION[member_id] AND course_id=$course";
	$result = mysql_query($sql, $db);
	$row	= mysql_fetch_array($result);

	if (($course_info[0] == 'public') || ($course_info[0] == 'protected')) {
		if ($row != '') {
			$feedback[]=array(AT_FEEDBACK_NOW_ENROLLED, $system_courses[$course][title]);
			//print_feedback($feedback);
			header("Location:index.php?f=".urlencode_feedback($feedback));
		} else if ($course_info[1] != $_SESSION['member_id']) {

			require(AT_INCLUDE_PATH.'header.inc.php'); ?>
			<h2><?php  echo _AT('course_enrolment'); ?></h2>

			<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<input type="hidden" name="form_course_id" value="<?php echo $course; ?>">
			<?php  echo _AT('use_enrol_button'); ?>
			<br />
			<input type="submit" name="submit" class="button" value="<?php  echo _AT('enroll'); ?>">
			</form>
<?php
		} else {
			// we own this course!
			$errors[]=AT_ERROR_ALREADY_OWNED;
			print_errors($errors);
		}
	} else { // private
		if ((!$_POST['submit']) && ($row == '')) {

			require(AT_INCLUDE_PATH.'header.inc.php'); ?>
			<h2><?php  echo _AT('course_enrolment'); ?></h2>

		<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		<input type="hidden" name="form_course_id" value="<?php echo $course; ?>">
		<?php 		
		$infos[] = AT_INFOS_PRIVATE_ENROL;
		print_infos($infos); ?>
		<input type="submit" name="submit" class="button" value="<?php echo _AT('request_enrollment'); ?>">
		</form>
<?php

		} else if ($_POST['submit']) {
			$feedback[]=AT_FEEDBACK_APPROVAL_PENDING;
			print_feedback($feedback);
		} else if ($course_info[1] != $_SESSION['member_id'] ){
			 // request has already been made
		 	$errors[]=AT_ERROR_ALREADY_ENROLED;
			 print_errors($errors);
		} else {
			$errors[]=AT_ERROR_ALREADY_OWNED;
			print_errors($errors);
		}
	}

} else {
	require(AT_INCLUDE_PATH.'header.inc.php'); ?>
	<h2><?php  echo _AT('course_enrolment'); ?></h2>
	<?php
	$errors[]=AT_ERROR_LOGIN_ENROL;
	print_errors($errors);
	echo '<br /><a href="login.php?course='.$_SESSION[course_id].'">'._AT('login_into_atutor').'</a><br /><a href="registration.php">'._AT('register_an_account').'</a><br />';
}

	require(AT_INCLUDE_PATH.'footer.inc.php');
?> 