<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay & Joel Kronenberg        */
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
			$row	= mysql_fetch_array($result);

			$to_email = $row['email'];

			$message  = $row['first_name'].' '.$row['last_name']."\n\n";
			$message .= _AT('enrol_messagenew', $system_courses[$_POST['form_course_id']]['title'], $_base_href );

			if ($to_email != '') {
				require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');

				$mail = new ATutorMailer;

				$mail->From     = ADMIN_EMAIL;
				$mail->AddAddress($to_email);
				$mail->Subject = _AT('enrol_message3');
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

$title = _AT('course_enrolment');
require(AT_INCLUDE_PATH.'header.inc.php');
?>

<?php
if ($_SESSION['valid_user']) {

	$sql	= "SELECT * FROM ".TABLE_PREFIX."course_enrollment WHERE member_id=$_SESSION[member_id] AND course_id=$course";
	$result = mysql_query($sql, $db);
	$row	= mysql_fetch_array($result);

	if ((!$_POST['submit']) && ($row == '')) {
?>
		<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		<input type="hidden" name="form_course_id" value="<?php echo $course; ?>">
		<?php
		$infos[] = AT_INFOS_PRIVATE_ENROL;
		print_infos($infos);
		?>
		<input type="submit" name="submit" class="button" value="<?php echo _AT('request_enrollment');  ?>">
		</form>
<?php

		} else if ($_POST['submit']) {
			$infos[] = AT_INFOS_APPROVAL_PENDING;
			print_infos($infos);

		} else if ($course_info[1] != $_SESSION['member_id'] ){
		 // request has already been made
		 	$infos[] = AT_ERROR_ALREADY_ENROLED;
		 	print_infos($infos);

		} else {
			$errors[]=AT_ERROR_ALREADY_OWNED;
			print_errors($errors);
		}

} else {
	$errors[]=AT_ERROR_LOGIN_ENROL;
	print_errors($errors);
	echo '<br /><a href="login.php">'._AT('account_login').'</a><br />
	<a href="registration.php">'._AT('register_an_account').'</a><br /></td></tr>';
}


	require(AT_INCLUDE_PATH.'footer.inc.php');
?>