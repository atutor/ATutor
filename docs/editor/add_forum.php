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
require_once(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

global $savant;
$msg =& new Message($savant);

authenticate(AT_PRIV_FORUMS);

if ($_POST['cancel']) {
	
		$msg->addFeedback('CANCELLED');
		Header('Location: '.$_base_href.'forum/list.php');
		exit;
}

if ($_POST['add_forum'] && (authenticate(AT_PRIV_FORUMS, AT_PRIV_RETURN))) {
	if ($_POST['title'] == '') {
		$msg->addError('FORUM_TITLE_EMPTY');
	}

	if (!$msg->containsErrors()) {
		require (AT_INCLUDE_PATH.'lib/forums.inc.php');
		add_forum($_POST);
		
		$msg->addFeedback('FORUM_ADDED');
		header('Location: '.$_base_href.'forum/list.php');
		exit;
	}
}

$_section[0][0] = _AT('discussions');
$_section[0][1] = 'discussions/';
$_section[1][0] = _AT('forums');
$_section[1][1] = 'forum/list.php';
$_section[2][0] = _AT('add_forum');

$onload = 'onLoad="document.form.title.focus()"';

require(AT_INCLUDE_PATH.'header.inc.php');

$msg->printErrors();

echo '<h2>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '<img src="images/icons/default/square-large-discussions.gif" class="menuimage" border="0" vspace="2" width="42" height="40" alt="" /> ';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo '<a href="discussions/index.php?g=11">'._AT('discussions').'</a>';
	}
echo '</h2>';

echo'<h3>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '<img src="images/icons/default/forum-large.gif" width="42" height="38" border="0" alt="" class="menuimageh3" />';
}

echo _AT('add_forum').'</h3>';
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="add_forum" value="true">
<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center">
<tr>
	<th colspan="2" class="cyan"><img src="images/pen2.gif" border="0" class="menuimage12" alt="<?php echo _AT('editor_on'); ?>" title="<?php echo _AT('editor_on'); ?>" height="14" width="16" /><?php  echo _AT('add_forum'); ?></th>
</tr>
<tr>
	<td class="row1" align="right"><?php print_popup_help('ADD_FORUM_MINI'); ?><b><label for="title"><?php  echo _AT('title'); ?>:</label></b></td>
	<td class="row1"><input type="text" name="title" class="formfield" size="40" id="title" /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" valign="top" align="right"><b><label for="body"><?php echo _AT('description'); ?>:</label></b></td>
	<td class="row1"><textarea name="body" cols="45" rows="10" class="formfield" id="body" wrap="wrap"></textarea><br /><br /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" colspan="2" align="center"><br /><input type="submit" name="submit" value="<?php  echo _AT('add_forum'); ?> [Alt-s]" class="button" accesskey="s"> - <input type="submit" name="cancel" value="<?php  echo _AT('cancel'); ?>" class="button"></td>
</tr>
</table>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>