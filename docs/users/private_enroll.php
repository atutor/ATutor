<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2007 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

$_user_location	= 'users';
define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

if (!$_SESSION['valid_user']) {
	require(AT_INCLUDE_PATH.'header.inc.php');
	$msg->printErrors('LOGIN_ENROL');
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

$course = intval($_REQUEST['course']);
if ($course == 0) {
	exit;
}

$sql	= "SELECT access, member_id FROM ".TABLE_PREFIX."courses WHERE course_id=$course";
$result = mysql_query($sql, $db);
$course_info = mysql_fetch_assoc($result);

if ($_POST['submit']) {
	$_SESSION['enroll'] = AT_ENROLL_YES;

	if ($course_info['access'] == 'private') {
		$sql	= "INSERT INTO ".TABLE_PREFIX."course_enrollment VALUES ($_SESSION[member_id], $course, 'n', 0, '"._AT('student')."', 0)";
		$result = mysql_query($sql, $db);

		// send the email - if needed
		if ($system_courses[$course]['notify'] == 1) {

			$ins_id = $system_courses[$course]['member_id'];
			$sql	= "SELECT email, first_name, last_name FROM ".TABLE_PREFIX."members WHERE member_id=$ins_id";
			$result = mysql_query($sql, $db);
			$row	= mysql_fetch_assoc($result);

			$to_email = $row['email'];
			$tmp_message  = $row['first_name']  .' ' . $row['last_name']."\n\n";
			$tmp_message .= _AT('enrol_messagenew', $system_courses[$course]['title'], AT_BASE_HREF );

			if ($to_email != '') {
				require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');

				$mail = new ATutorMailer;
				$mail->From     = $_config['contact_email'];
				$mail->FromName = $_config['site_name'];
				$mail->AddAddress($to_email);
				$mail->Subject = _AT('enrol_message3');
				$mail->Body    = $tmp_message;

				if (!$mail->Send()) {
				   require(AT_INCLUDE_PATH.'header.inc.php');
				   $msg->printErrors('SENDING_ERROR');
				   require(AT_INCLUDE_PATH.'footer.inc.php');
				   exit;
				}
				unset($mail);
			}
		}
		require(AT_INCLUDE_PATH.'header.inc.php');
		$msg->printFeedbacks('APPROVAL_PENDING');
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	} else {
		$sql	= "INSERT INTO ".TABLE_PREFIX."course_enrollment VALUES ($_SESSION[member_id], $course, 'y', 0, '"._AT('student')."', 0)";
		$result = mysql_query($sql, $db);
	}
}

$sql	= "SELECT * FROM ".TABLE_PREFIX."course_enrollment WHERE member_id=$_SESSION[member_id] AND course_id=$course";
$result = mysql_query($sql, $db);
$row_in = mysql_fetch_assoc($result);

// request has already been made
if ($row_in['member_id'] == $_SESSION['member_id'] ) {
 	$msg->addFeedback('ALREADY_REQUESTED');
	header('Location: ./index.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');
?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="course" value="<?php echo $course; ?>">
<div class="input-form">
	<div class="row">
		<?php echo _AT('private_enroll'); ?>
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('request_enrollment'); ?>" />
	</div>
</div>
</form>

<?php	require(AT_INCLUDE_PATH.'footer.inc.php'); ?>