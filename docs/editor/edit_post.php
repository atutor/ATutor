<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002 by Greg Gay & Joel Kronenberg             */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/

define('AT_INCLUDE_PATH', '../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

	if ($_POST['cancel']) {
		Header('Location: ../forum/view.php?fid='.$_POST['fid'].SEP.'pid='.$_POST['pid'].SEP.'f='.urlencode_feedback(AT_FEEDBACK_CANCELLED));
		exit;
	}

if ($_POST['edit_post'] && $_SESSION['is_admin']) {
	$_POST['subject']	= str_replace('<', '&lt;', trim($_POST['subject']));
	$_POST['body']		= str_replace('<', '&lt;', trim($_POST['body']));
	$_POST['pid']		= intval($_POST['pid']);

	$sql = "UPDATE ".TABLE_PREFIX."forums_threads SET subject='$_POST[subject]', body='$_POST[body]' WHERE post_id=$_POST[pid] AND course_id=$_SESSION[course_id]";
	$result = mysql_query($sql,$db);

	Header('Location: ../forum/view.php?fid='.$_POST['fid'].SEP.'pid='.$_POST['pid'].SEP.'f='.urlencode_feedback(AT_FEEDBACK_POST_EDITED));
	exit;
}

define('AT_INCLUDE_PATH', '../include/');
$_section[0][0] = _AT('discussions');
$_section[0][1] = '../../discussions/';
$_section[1][0] = get_forum($_GET['fid']);
$_section[1][1] = '../../forum/?fid='.$_GET['fid'];
$_section[2][0] = _AT('edit_post');

$onload = 'onload="document.form.subject.focus()"';

require(AT_INCLUDE_PATH.'header.inc.php');

	echo '<h2>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '<img src="images/icons/default/square-large-discussions.gif" width="42" height="38" border="0" alt="" class="menuimage" /> ';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo '<a href="discussions/">'._AT('discussions').'</a>';
	}
	echo '</h2>';
?>
<h3>
<?php
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '<img src="images/icons/default/forum-large.gif" width="42" height="38" border="0" alt="" class="menuimageh3" />';
}
?>
Edit Post</h3>
<?php
	
	if (isset($_GET['pid'])) {
		$pid = intval($_GET['pid']);
	} else {
		$pid = intval($_POST['pid']);
	}

	if ($pid == 0) {
		$errors[]=AT_ERROR_POST_ID_ZERO;
		require (AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}
	
	$sql = "SELECT * FROM ".TABLE_PREFIX."forums_threads WHERE post_id=$pid AND course_id=$_SESSION[course_id]";
	$result = mysql_query($sql,$db);
	if (!($row = mysql_fetch_array($result))) {
		$errors[]=AT_ERROR_POST_NOT_FOUND;
		require (AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}

?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="edit_post" value="true" />
<input type="hidden" name="pid" value="<?php echo $pid; ?>" />
<input type="hidden" name="fid" value="<?php echo $row['forum_id']; ?>" />
<br />
<table cellspacing="1" cellpadding="0" border="0" class="bodyline" align="center" summary="">
<tr>
	<th colspan="2" class="left"><img src="images/pen2.gif" border="0" class="menuimage12" alt="<?php echo _AT('editor_on'); ?>" title="<?php echo _AT('editor_on'); ?>" height="14" width="16" /><?php echo _AT('edit_post'); ?></th>
</tr>
<tr>
	<td class="row1" align="right"><label for="subject"><b><?php echo _AT('subject'); ?>:</b></label></td>
	<td class="row1"><input class="formfield" maxlength="45" name="subject" size="36" value="<?php echo $row['subject']; ?>" id="subject" /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right" valign="top"><label for="body"><b><?php echo _AT('body'); ?>:</b></label></td>
	<td class="row1"><textarea class="formfield" cols="65" name="body" rows="10" id="body"><?php echo $row['body']; ?></textarea>
	
	<br /><small class="spacer">&middot;<?php echo _AT('forum_links'); ?><br />
	&middot; <?php echo _AT('forum_email_links'); ?><br />
	&middot; <?php echo _AT('forum_html_disabled'); ?></small>
	<br /><br /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" colspan="2" align="center"><input name="submit" class="button" type="submit" value="  <?php echo _AT('submit'); ?> [Alt-s]" accesskey="s" /> - <input type="submit" name="cancel" class="button" value=" <?php echo _AT('cancel'); ?> " /></td>
</tr>
</table>
</form>
<?php
	require (AT_INCLUDE_PATH.'footer.inc.php');
?>