<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/

define('AT_INCLUDE_PATH', '../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

global $savant;
$msg =& new Message($savant);

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

		header('Location: index.php?f='.AT_FEEDBACK_MSG_SENT);
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}
}

$title = _AT('course_email');
require(AT_INCLUDE_PATH.'header.inc.php');

echo '<h2>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '<img src="images/icons/default/square-large-tools.gif" border="0" vspace="2"  class="menuimageh2" width="42" height="40" alt="" />';
}
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
	echo ' <a href="tools/" class="hide" >'._AT('tools').'</a>';
}
echo '</h2>'."\n";

echo '<h3>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '&nbsp;<img src="images/icons/default/course_mail-large.gif"  class="menuimageh3" width="42" height="38" alt="" /> ';
}
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
	echo _AT('course_email');
}
echo '</h3>'."\n";

/* we own this course! */
$msg->printErrors();

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
<table cellspacing="1" cellpadding="0" border="0" class="bodyline" width="95%" summary="">
<tr>
	<th colspan="2" align="left" class="cyan"><?php echo  _AT('send_message'); ?></th>
</tr>
<tr>
	<td width="100" class="row1" align="right"><strong><label for="subject"><?php echo _AT('subject'); ?>:</label></strong></td>
	<td class="row1"><input type="text" name="subject" class="formfield" size="40" id="subject" value="<?php echo $_POST['subject']; ?>" /></td>
</tr>
<tr>
	<td height="1" class="row2" colspan="2"></td>
</tr>
<tr>
	<td width="100" class="row1" align="right" valign="top"><strong><label for="body"><?php echo _AT('body'); ?>:</label></strong></td>
	<td class="row1"><textarea cols="55" rows="18" name="body" id="body" class="formfield"><?php echo $_POST['body']; ?></textarea><br /><br /></td>
</tr>
<tr>
	<td height="1" class="row2" colspan="2"></td>
</tr>
<tr>
	<td height="1" class="row2" colspan="2"></td>
</tr>
<tr>
	<td colspan="2" class="row1" align="center"><input type="submit" name="submit" value="<?php echo _AT('send_message'); ?>" class="button" /> - <input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" class="button" /></td>
</tr>
</table>
</form>

<?php
	require(AT_INCLUDE_PATH.'footer.inc.php');
?>
