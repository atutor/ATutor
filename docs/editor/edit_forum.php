<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton	*/
/* Adaptive Technology Resource Centre / University of Toronto				*/
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_FORUMS);

require (AT_INCLUDE_PATH.'lib/forums.inc.php');
require_once(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

global $savant;
$msg =& new Message($savant);

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.$_base_href.'forum/list.php');
	exit;
} else if (isset($_POST['edit_forum'])) {
	$_POST['fid'] = intval($_POST['fid']);

	// check if this forum is shared:
	// (if this forum is shared, then we do not want to delete it.)

	if ($_POST['title'] == '') {
		$msg->addError('TITLE_EMPTY');
	}

	if (!$msg->containsErrors()) {
		if (!is_shared_forum($_POST['fid'])) {
			edit_forum($_POST);
		}
		

		$msg->addFeedback('FORUM_UPDATED');
		header('Location: ../forum/list.php');
		exit;
	}
}
$_section[0][0] = _AT('discussions');
$_section[0][1] = 'discussions/';
$_section[1][0] = _AT('forums');
$_section[1][1] = 'forum/list.php';
$_section[2][0] = _AT('edit_forum');

$onload = 'onLoad="document.form.title.focus()"';
require(AT_INCLUDE_PATH.'header.inc.php');

$fid = intval($_REQUEST['fid']);

if (!isset($_POST['submit'])) {
	$row = get_forum($fid, $_SESSION['course_id']);
	if (!is_array($row)) {
		$msg->addError('FORUM_NOT_FOUND');
		$msg->printALL();
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}
} else {
	$row['description'] = $_POST['body'];
}

$msg->printErrors();

echo '<h2>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '<img src="images/icons/default/square-large-discussions.gif" width="42" height="38" border="0" alt="" class="menuimage" /> ';
}
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
	echo '<a href="discussions/">'._AT('discussions').'</a>';
}
echo '</h2>';

echo'<h3>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '<img src="images/icons/default/forum-large.gif" width="42" height="38" border="0" alt="" class="menuimageh3" />';
}

echo _AT('edit_forum').'</h3>';

?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
	<input type="hidden" name="edit_forum" value="true">
	<input type="hidden" name="fid" value="<?php echo $fid; ?>">
	<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center">
	<tr>
		<th colspan="2" class="cyan"><img src="images/pen2.gif" border="0" class="menuimage12" alt="<?php echo _AT('editor_on'); ?>" title="<?php echo _AT('editor_on'); ?>" height="14" width="16" /><?php echo _AT('edit_forum'); ?></th>
	</tr>
	<tr>
		<td class="row1" align="right"><b><label for="title"><?php  echo _AT('title'); ?>:</label></b></td>
		<td class="row1"><input type="text" name="title" class="formfield" size="50" id="title" value="<?php echo htmlspecialchars(stripslashes($row['title'])); ?>"></td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1" valign="top" align="right"><b><label for="body"><?php  echo _AT('description'); ?>:</label></b></td>
		<td class="row1"><textarea name="body" cols="45" rows="10" class="formfield" id="body" wrap="wrap"><?php echo $row['description']; ?></textarea><br /><br /></td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1" colspan="2" align="center"><br /><input type="submit" name="submit" value="<?php  echo _AT('edit_forum'); ?> [Alt-s]" accesskey="s" class="button"> - <input type="submit" name="cancel" class="button" value="<?php  echo _AT('cancel'); ?>" /></td>
	</tr>
	</table>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>