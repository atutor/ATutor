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
$page	 = 'browse_courses';
$_user_location = 'public';

define('AT_INCLUDE_PATH', 'include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

require_once(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

global $savant;
$msg =& new Message($savant);

$course = intval($_GET['course']);
$sql	= "SELECT access, member_id FROM ".TABLE_PREFIX."courses WHERE course_id=$course";
$result = mysql_query($sql, $db);
$course_info = mysql_fetch_array($result);
//debug($course_info);
//exit;



if (!$_SESSION['valid_user']) {

	$msg->addError("LOGIN_ENROL");
	header("Location: browse.php");
	exit;

}else{
	//check if user owns the course
	$sql	= "SELECT  member_id FROM ".TABLE_PREFIX."courses WHERE course_id=".$course." AND member_id =". $_SESSION['member_id'];
	$result = mysql_query($sql, $db);
	if(mysql_num_rows($result)){
			$msg->addError('ALREADY_OWNED');
			header("Location:browse.php");
			exit;
	}
	//check if user is already enrolled in the course
	$sql	= "SELECT * FROM ".TABLE_PREFIX."course_enrollment WHERE member_id=$_SESSION[member_id] AND course_id=$course";
	$result = mysql_query($sql, $db);
	$row	= mysql_fetch_array($result);
	if ($row != '') {
		$msg->addError('YOU_ARE_ENROLLED');
		header("Location:browse.php");
		exit;
	}
}

if ($_GET['browset']) {
	$_SESSION['enroll'] = AT_ENROLL_YES;
	$_GET['course'] = intval($_GET['course']);

	if ($course_info[0] == 'private') {
		$sql	= "INSERT INTO ".TABLE_PREFIX."course_enrollment VALUES ($_SESSION[member_id], $_GET[course], 'n', 0, '"._AT('student')."', 0)";
		$result = mysql_query($sql, $db);

		// send the email thing. if needed

		$sql	= "SELECT notify, member_id FROM ".TABLE_PREFIX."courses WHERE course_id=$_GET[course] AND notify=1";
		$result = mysql_query($sql, $db);
		if ($row = mysql_fetch_array($result)) {
			// notify is enabled. get the email
			$sql	= "SELECT email, first_name, last_name FROM ".TABLE_PREFIX."members WHERE member_id=$row[member_id]";
			$result = mysql_query($sql, $db);
			$row	= mysql_fetch_assoc($result);

			$to_email = $row['email'];

			$message  = $row['first_name'].' '.$row['last_name'].",\n\n";
			$message .= _AT('enrol_msg', $system_courses[$_GET['course']]['title']);
			$message .= _AT('enrol_login');
			if ($to_email != '') {

				require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');

				$mail = new ATutorMailer;

				$mail->From     = ADMIN_EMAIL;
				$mail->AddAddress($to_email);
				$mail->Subject = _AT('course_enrolment');
				$mail->Body    = $message;

				if(!$mail->Send()) {
				   //echo 'There was an error sending the message';
				   $msg->printErrors('SENDING_ERROR');
				   exit;
				}
				unset($mail);
			}
		}
	} else {
		// public or protected
		$sql	= "INSERT INTO ".TABLE_PREFIX."course_enrollment VALUES ($_SESSION[member_id], $_GET[course], 'y', 0, '"._AT('student')."', 0)";
		$result = mysql_query($sql, $db);
	}
}


if($_GET['browse']){
	$sql	= "SELECT * FROM ".TABLE_PREFIX."course_enrollment WHERE member_id=$_SESSION[member_id] AND course_id=$course";
	$result = mysql_query($sql, $db);
	$row	= mysql_fetch_array($result);

	// if its a public or private course, automatically enroll the user
	if($course_info[0] =="public" || $course_info[0] == "protected"){
		echo "this is a public course";
		if ($row != '') {
			$feedback = array('NOW_ENROLLED', $system_courses[$course][title]);
			$msg->addFeedback($feedback);
			header("Location:index.php");
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
		}
	}else if($course_info[0] =="private"){

		echo "this is a private course";
	}

}

	require(AT_INCLUDE_PATH.'footer.inc.php');
?>
