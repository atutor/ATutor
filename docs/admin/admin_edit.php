<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
/* edit the user status between student/instructor              */
/****************************************************************/

$page = 'users';
$_user_location = 'admin';

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
if ($_SESSION['course_id'] > -1) { exit; }


if ($_POST['submit']) {
	$_POST['form_status']	= intval($_POST['form_status']);
	$_POST['form_id']		= intval($_POST['form_id']);
	$_POST['old_status']	= intval($_POST['old_status']);

	$sql = "UPDATE ".TABLE_PREFIX."members SET status=$_POST[form_status] WHERE member_id=$_POST[form_id]";
	$result = mysql_query($sql, $db);

	if (!$result) {
		echo 'DB Error';
		exit;
	}
	if ($_POST['form_status'] > $_POST['old_status']) {
		/* delete the request: */
		$sql = "DELETE FROM ".TABLE_PREFIX."instructor_approvals WHERE member_id=$_POST[form_id]";
		$result = mysql_query($sql, $db);

		/* notify the users that they have been approved: */
		$sql   = "SELECT email, first_name, last_name FROM ".TABLE_PREFIX."members WHERE member_id=$_POST[form_id]";
		$result = mysql_query($sql, $db);
		if ($row = mysql_fetch_array($result)) {
			$to_email = $row['email'];

			if ($row['first_name']!="" || $row['last_name']!="") {
				$message  = $row['first_name'].' '.$row['last_name'].",\n\n";		
			}	
			$message .= _AT('instructor_request_reply', $_base_href);

			if ($to_email != '') {
				require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');

				$mail = new ATutorMailer;

				$mail->From     = ADMIN_EMAIL;
				$mail->AddAddress($to_email);
				$mail->Subject = _AT('instructor_request');
				$mail->Body    = $message;

				if(!$mail->Send()) {
				   echo 'There was an error sending the message';
				   exit;
				}

				unset($mail);
			}
		}
	}
	Header('Location: ./profile.php?member_id='.$_POST['form_id']);
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php'); 
?>
<h3><?php echo _AT('edit_user'); ?></h3>

<?php
		require_once(AT_INCLUDE_PATH.'classes/Message/Message.class.php');
	
		global $savant;
		$msg =& new Message($savant);
		
		$msg->printAll();
	/*
		if (isset($_GET['f'])) { 
			$f = intval($_GET['f']);
			if ($f <= 0) {
				/* it's probably an array *
				$f = unserialize(urldecode($_GET['f']));
			}
			print_feedback($f);
		}
		if (isset($errors)) { print_errors($errors); }
		if(isset($warnings)){ print_warnings($warnings); }
	*/
		$id		= intval($_GET[id]);
		$sql	= "SELECT * FROM ".TABLE_PREFIX."members WHERE member_id=$id";
		$result = mysql_query($sql, $db);
		if (!($row = mysql_fetch_array($result)))
		{
			echo _AT('no_user_found');
		} else {
			echo _AT('login_name').': <b>'.AT_print($row['login'], 'members.login').'</b>';
			echo '<br />';
			echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'">';
			echo '<input type="hidden" name="form_id" value="'.$id.'" />';
			if ($row['status'])
			{
				$inst = ' checked="checked"';
			} else {
				$stnd = ' checked="checked"';
			}
			echo _AT('status').': <input type="radio" name="form_status" value="1" id="inst"'.$inst.' /><label for="inst">'._AT('instructor').'</label>, <input type="radio" name="form_status" value="0" id="stnd"'.$stnd.' /><label for="stnd">'._AT('student1').'</label>';
			echo '<br />';
			echo '<br />';
			echo '<input type="submit" name="submit" class="button" value="'._AT('update_status').'" />';
			echo '<input type="hidden" name="old_status" value="'.AT_print($row['status'], 'members.status').'" />';
			echo '</form>';
		}


	require(AT_INCLUDE_PATH.'footer.inc.php'); 
?>