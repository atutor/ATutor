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
	define('AT_INCLUDE_PATH', '../include/');
	require (AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_FORUMS);

	if ($_POST['cancel']) {
		Header('Location: '.$_base_href.'discussions/index.php?f='.urlencode_feedback(AT_FEEDBACK_CANCELLED));
		exit;
	}

	if ($_POST['add_forum'] && ($_SESSION['is_admin'] || authenticate(AT_PRIV_FORUMS, AT_PRIV_CHECK))) {
		//$_POST['title'] = str_replace('<', '&lt;', trim($_POST['title']));
		//$_POST['body']  = str_replace('<', '&lt;', trim($_POST['body']));

		if ($_POST['title'] == '') {
			$errors[] = AT_ERROR_FORUM_TITLE_EMPTY;
		}

		if (!$errors) {
			$sql	= "INSERT INTO ".TABLE_PREFIX."forums VALUES (0, $_SESSION[course_id], '$_POST[title]', '$_POST[body]')";
			$result = mysql_query($sql,$db);

			header('Location: '.$_base_href.'discussions/index.php?f='.AT_FEEDBACK_FORUM_ADDED);
			exit;
		}
	}

	$_section[0][0] = _AT('add_forum');

	$onload = 'onLoad="document.form.title.focus()"';

	require(AT_INCLUDE_PATH.'header.inc.php');

	print_errors($errors);

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
<p>
<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center">
<tr>
	<th colspan="2" class="left"><img src="images/pen2.gif" border="0" class="menuimage12" alt="<?php echo _AT('editor_on'); ?>" title="<?php echo _AT('editor_on'); ?>" height="14" width="16" /><?php  echo _AT('add_forum'); ?></th>
</tr>
<tr>
	<td class="row1" align="right"><?php print_popup_help(AT_HELP_ADD_FORUM_MINI); ?><b><label for="title"><?php  echo _AT('forum_title'); ?>:</label></b></td>
	<td class="row1"><input type="text" name="title" class="formfield" size="40" id="title"></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" valign="top" align="right"><b><label for="body"><?php echo _AT('forum_description'); ?>:</label></b></td>
	<td class="row1"><textarea name="body" cols="45" rows="10" class="formfield" id="body" wrap="wrap"></textarea><br /><br /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" colspan="2" align="center"><br /><input type="submit" name="submit" value="<?php  echo _AT('add_forum'); ?> [Alt-s]" class="button" accesskey="s"> - <input type="submit" name="cancel" value="<?php  echo _AT('cancel'); ?>" class="button"></td>
</tr>
</table>
</p>
</form>

<?php
	require(AT_INCLUDE_PATH.'footer.inc.php');
?>