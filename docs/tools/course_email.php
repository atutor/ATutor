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
require (AT_INCLUDE_PATH.'lib/atutor_mail.inc.php');

$course = intval($_GET['course']);

if ($course == 0) {
	$course = $_SESSION['course_id'];
}


/* make sure we own this course that we're approving for! */

if (!(authenticate(AT_PRIV_ADMIN, AT_PRIV_RETURN)) && !(authenticate(AT_PRIV_COURSE_EMAIL, AT_PRIV_RETURN))) {
	$errors[]=AT_ERROR_PREFS_NO_ACCESS;
	print_errors($errors);
	exit;
}

if ($_POST['cancel']) {
	Header('Location: index.php');
	exit;
}

$title = _AT('course_email');
require(AT_INCLUDE_PATH.'header.inc.php');

echo '<a name="content"></a>';

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
	echo '&nbsp;<img src="images/icons/default/file-manager-large.gif"  class="menuimageh3" width="42" height="38" alt="" /> ';
}
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
	echo _AT('course_email');
}
echo '</h3>'."\n";

if ($_POST['submit']) {
	$_POST['subject'] = trim($_POST['subject']);
	$_POST['body'] = trim($_POST['body']);

	if ($_POST['subject'] == '') {
		$errors[]=AT_ERROR_MSG_SUBJECT_EMPTY;
	}

	if ($_POST['body'] == '') {
		$errors[]=AT_ERROR_MSG_BODY_EMPTY;
	}


	if (!$errors) {
		// note: doesn't list the owner of the course or the person (TA) editing the list.
		$sql	= "SELECT * FROM ".TABLE_PREFIX."course_enrollment C, ".TABLE_PREFIX."members M WHERE C.course_id=$course AND C.member_id=M.member_id AND M.member_id<>$_SESSION[member_id] ORDER BY C.approved, M.login";

		$result = mysql_query($sql,$db);

		while ($row = mysql_fetch_array($result)) {
			if ($bcc != '') {
				$bcc .= ', ';
			}
			$bcc .= $row['email'];
		}

		$result = mysql_query("SELECT email FROM ".TABLE_PREFIX."members WHERE member_id=$_SESSION[member_id]", $db);
		$row	= mysql_fetch_array($result);


		atutor_mail($row['email'], $_POST['subject'], $_POST['body'], $row['email'], $bcc);
		header("Location: index.php?f=".urlencode_feedback(AT_FEEDBACK_MSG_SENT));
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}
}

/* we own this course! */

print_errors($errors);

	$sql	= "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."course_enrollment C, ".TABLE_PREFIX."members M WHERE C.course_id=$course AND C.member_id=M.member_id AND M.member_id<>$_SESSION[member_id] ORDER BY C.approved, M.login";
	$result = mysql_query($sql,$db);
	$row	= mysql_fetch_array($result);
	if ($row['cnt'] == 0) {
		$errors[]=AT_ERROR_NO_STUDENTS;
		print_errors($errors);
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}

?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="course" value="<?php echo $course; ?>" />
<table cellspacing="1" cellpadding="0" border="0" class="bodyline" width="95%" summary="">
<tr>
	<th colspan="2" align="left" class="left"><?php echo  _AT('send_message'); ?></th>
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
