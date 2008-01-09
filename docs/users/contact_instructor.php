<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

if ($_REQUEST['from_browse']) {
	$page = 'browse_courses';
} else {
	$page = 'my_courses';
}

$_user_location	= 'users';
define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

if ($_POST['cancel']) {
	if ($_POST['from_browse']) {
		header('Location: browse.php');
	} else {
		header('Location: index.php');
	}
	exit;
}

	require(AT_INCLUDE_PATH.'header.inc.php');

	echo '<h2>'._AT('contact_instructor').'</h2>';

	$sql	= "SELECT first_name, last_name, email FROM ".TABLE_PREFIX."members WHERE member_id=$_SESSION[member_id]";
	$result = mysql_query($sql, $db);
	if ($row = mysql_fetch_array($result)) {
		$student_name = $row['last_name'];
		$student_name .= ($row['first_name'] ? ', '.$row['first_name'] : '');

		$student_email = $row['email'];
	} else {
		$msg->printErrors('STUD_INFO_NOT_FOUND');
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}

	if ($_GET['course']) {
		$course = intval($_GET['course']);
	} else {
		$course = intval($_POST['course']);
	}

	$sql	= "SELECT M.first_name, M.last_name, M.email, C.title FROM ".TABLE_PREFIX."members M, ".TABLE_PREFIX."courses C WHERE M.member_id=C.member_id AND C.course_id=$course";
	$result = mysql_query($sql, $db);

	if ($row = mysql_fetch_array($result)) {
		$instructor_name = $row['last_name'];
		$instructor_name .= ($row['first_name'] ? ', '.$row['first_name'] : '');

		$instructor_email = $row['email'];
	} else {
		$msg->printErrors('INST_INFO_NOT_FOUND');
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}

	if ($_POST['submit']) {
		$missing_fields = array();

		$to_email = $instructor_email;
		$_POST['subject'] = trim($_POST['subject']);
		$_POST['body']	  = trim($_POST['body']);

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
			$tmp_message = _AT('from_atutor', $row['title'])."\n\n";
			$tmp_message .= $_POST['body']."\n\n";

			if ($to_email != '') {
				require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');

				$mail = new ATutorMailer;

				$mail->From     = $_POST['from_email'];
				$mail->FromName = $_POST['from'];
				$mail->AddAddress($to_email);
				$mail->Subject = $_POST['subject'];
				$mail->Body    = $tmp_message;

				if(!$mail->Send()) {
				   //echo 'There was an error sending the message';
				   $msg->printErrors('SENDING_ERROR');
				   exit;
				}

				unset($mail);

				$msg->printFeedbacks('ACTION_COMPLETED_SUCCESSFULLY');
				require(AT_INCLUDE_PATH.'footer.inc.php');
				exit;
			}
		}
	}

$msg->printErrors();

?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="course" value="<?php echo $course; ?>" />
<input type="hidden" name="from_browse" value="<?php echo $_REQUEST['from_browse']; ?>" />
<table cellspacing="1" cellpadding="0" border="0" summary="" width="85%" class="bodyline" align="center">
<tr>
	<th colspan="2" align="left" class="cyan"><?php echo _AT('instructor_contact_form'); ?></th>
</tr>
<tr>
	<td class="row1" align="right"><b><?php echo _AT('contact_name'); ?>:</b></td>
	<td class="row1"><?php echo $row[title]; ?></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><b><?php echo _AT('to_name'); ?>:</b></td>
	<td class="row1"><?php echo $instructor_name; ?> (<?php echo _AT('course_instructor'); ?>)</td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><b><?php echo _AT('to_email'); ?>:</b></td>
	<td class="row1"><i><?php echo _AT('hidden'); ?></i></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><label for="from"><b><?php echo _AT('from_name'); ?>:</b></label></td>
	<td class="row1"><input type="text" class="formfield" name="from" id="from" size="40" value="<?php echo $student_name;?>" /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><label for="from_email"><b><?php echo _AT('from_email'); ?>:</b></label></td>
	<td class="row1"><input type="text" class="formfield" name="from_email" id="from_email" size="40" value="<?php echo $student_email;?>" /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><label for="subject"><b><?php echo _AT('subject'); ?>:</b></label></td>
	<td class="row1"><input type="text"  class="formfield" name="subject" id="subject" size="40" value="<?php echo $_POST['subject']; ?>" /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right" valign="top"><label for="body"><b><?php echo _AT('body'); ?>:</b></label></td>
	<td class="row1"><textarea class="formfield" cols="55" rows="15" id="body" name="body"><?php echo $_POST['body']; ?></textarea><br /><br /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="center" colspan="2"><input type="submit" name="submit" class="button" value="<?php echo _AT('send'); ?> [Alt-s]" accesskey="s" /> <input type="submit" name="cancel" class="button" value="<?php echo _AT('cancel'); ?>" /></td>
</tr>
</table>
</form>
<br />

<?php
	require(AT_INCLUDE_PATH.'footer.inc.php');
?>