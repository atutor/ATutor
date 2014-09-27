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

$sql	= "SELECT access, member_id FROM %scourses WHERE course_id=%d";
$course_info = queryDB($sql, array(TABLE_PREFIX, $course), TRUE);

if ($_POST['submit']) {
	$_SESSION['enroll'] = AT_ENROLL_YES;

	if ($course_info['access'] == 'private') {
		$sql	= "INSERT INTO %scourse_enrollment VALUES (%d, %d, 'n', 0, '%s', 0)";
		$result = queryDB($sql, array(TABLE_PREFIX, $_SESSION['member_id'], $course, _AT('student')));
		
		// send the email - if needed
		if ($system_courses[$course]['notify'] == 1) {
			$mail_list = array();	//initialize an array to store all the pending emails

			//Get the list of students with enrollment privilege
			$module =& $moduleFactory->getModule('_core/enrolment');
			
			$sql	= "SELECT email, first_name, last_name, `privileges` FROM %smembers m INNER JOIN %scourse_enrollment ce ON m.member_id=ce.member_id WHERE ce.privileges > 0 AND ce.course_id=%d";
			$rows_members = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, $course));
            
            foreach($rows_members as $row){
				if (query_bit($row['privileges'], $module->getPrivilege())){
					unset($row['privileges']);	//we don't need the privilege to flow around
					$mail_list[] = $row;
				}
			}
			
			//Get instructor information
			$ins_id = $system_courses[$course]['member_id'];

			$sql	= "SELECT email, first_name, last_name FROM %smembers WHERE member_id=%d";
			$row = queryDB($sql, array(TABLE_PREFIX, $ins_id), TRUE);

			$mail_list[] = $row;

			//Send email notification to both assistants with privileges & Instructor
			foreach ($mail_list as $row){
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
		}
		$msg->addFeedback('APPROVAL_PENDING');
		header('Location: index.php');
		exit;
	} else {

		$sql	= "INSERT INTO %scourse_enrollment VALUES (%d, %d, 'y', 0, '%s', 0)";
		$result = queryDB($sql, array(TABLE_PREFIX, $_SESSION['member_id'], $course, _AT('student')));

	}
}

$sql	= "SELECT * FROM %scourse_enrollment WHERE member_id=%d AND course_id=%d";
$row_in = queryDB($sql, array(TABLE_PREFIX, $_SESSION['member_id'], $course), TRUE);

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