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


$_user_location = 'admin';
define('AT_INCLUDE_PATH', '../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');


if ($_POST['cancel']) {
	header('Location: index.php');
	exit;
} else if ($_POST['submit']) {
	$_POST['subject'] = trim($_POST['subject']);
	$_POST['body'] = trim($_POST['body']);

	if ($_POST['subject'] == '') {
		$errors[]=AT_ERROR_MSG_SUBJECT_EMPTY;
	}

	if ($_POST['body'] == '') {
		$errors[]=AT_ERROR_MSG_BODY_EMPTY;
	}

	if (($_POST['to'] == '') || ($_POST['to'] == 0)) {
		 $errors[] = AT_ERROR_NO_RECIPIENT;
	}
	
	if (!$errors) {
		if ($_POST['to'] == 1) {
			// choose all instructors
			$sql	= "SELECT * FROM ".TABLE_PREFIX."members WHERE status = 1 ORDER BY login";
		} else if ($_POST['to'] == 2) {
			// choose all students
			$sql 	= "SELECT * FROM ".TABLE_PREFIX."members WHERE status = 0 ORDER BY login";
		} else {
			// choose all members
			$sql 	= "SELECT * FROM ".TABLE_PREFIX."members ORDER BY login";
		}
		
		$result = mysql_query($sql,$db);

		require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');

		$mail = new ATutorMailer;

		while ($row = mysql_fetch_assoc($result)) {
			$mail->AddBCC($row['email']);
		}


		$mail->From     = ADMIN_EMAIL;
		$mail->FromName = ADMIN_USERNAME;
		$mail->AddAddress(ADMIN_EMAIL);
		$mail->Subject = $_POST['subject'];
		$mail->Body    = $_POST['body'];

		if(!$mail->Send()) {
		   echo _AT('AT_ERROR_MSG_NOT_SENT');
		   exit;
		}
		unset($mail);

		header('Location: index.php?f='.AT_FEEDBACK_MSG_SENT);
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}
}

$title = _AT('admin_email');
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
	echo _AT('admin_email');
}
echo '</h3>'."\n";

print_errors($errors);

	$sql	= "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."members ORDER BY login";
	$result = mysql_query($sql,$db);
	$row	= mysql_fetch_array($result);
	if ($row['cnt'] == 0) {
		$errors[]=AT_ERROR_NO_MEMBERS;
		print_errors($errors);
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}

?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="admin" value="admin" />
<table cellspacing="1" cellpadding="0" border="0" class="bodyline" width="95%" summary="">
<tr>
	<th colspan="2" align="left" class="cyan"><?php echo  _AT('send_message'); ?></th>
</tr>
<tr>
	<td class="row1" align="right"><b><label for="to"><?php echo  _AT('to'); ?>:</label></b></td>
	<td class="row1"><select class="formfield" name="to" size="1" id="to">
			<option value="0"></option>
			<option value="1">Instructors</option>
			<option value="2">Students</option>
			<option value="3">All</option>
			</select> 
		</td>
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