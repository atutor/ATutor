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

	if ($_POST['cancel']) {
		Header('Location: '.$_base_href.'discussions/?f='.urlencode_feedback(AT_FEEDBACK_CANCELLED));
		exit;
	}

	if ($_POST['edit_forum'] && $_SESSION['is_admin']) {
		$_POST['title'] = str_replace('<', '&lt;', trim($_POST['title']));
		$_POST['body']  = str_replace('<', '&lt;', trim($_POST['body']));
		$_POST['fid']	= intval($_POST['fid']);

		if ($_POST['title'] == '') {
			$errors[]=AT_ERROR_FORUM_TITLE_EMPTY;
		}

		if (!$errors) {
			$sql	= "UPDATE ".TABLE_PREFIX."forums SET title='$_POST[title]', description='$_POST[body]' WHERE forum_id=$_POST[fid] AND course_id=$_SESSION[course_id]";
			$result = mysql_query($sql,$db);

			Header('Location: ../discussions/?f='.urlencode_feedback(AT_FEEDBACK_FORUM_UPDATED));
			exit;
		}
	}

	$_section[0][0] = _AT('edit_forum');

	$onload = 'onLoad="document.form.title.focus()"';

	require(AT_INCLUDE_PATH.'header.inc.php');

	$fid = intval($_GET['fid']);

	$sql = "SELECT * FROM ".TABLE_PREFIX."forums WHERE forum_id=$fid AND course_id=$_SESSION[course_id]";
	$result = mysql_query($sql,$db);
	if (!$errors) {
		if (!($row = mysql_fetch_array($result))) {
			$errors[]=AT_ERROR_FORUM_NOT_FOUND;
		}
	}


	print_errors($errors);
	
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
<p>
<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center">
<tr>
	<th colspan="2" class="left"><img src="images/pen2.gif" border="0" class="menuimage12" alt="<?php echo _AT('editor_on'); ?>" title="<?php echo _AT('editor_on'); ?>" height="14" width="16" /><?php  echo _AT('edit_forum'); ?></th>
</tr>
<tr>
	<td class="row1" align="right"><b><label for="title"><?php  echo _AT('forum_title'); ?>:</label></b></td>
	<td class="row1"><input type="text" name="title" class="formfield" size="50" id="title" value="<?php echo htmlspecialchars(stripslashes($row['title'])); ?>"></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" valign="top" align="right"><b><label for="body"><?php  echo _AT('forum_description'); ?>:</label></b></td>
	<td class="row1"><textarea name="body" cols="45" rows="10" class="formfield" id="body" wrap="wrap"><?php echo $row['description']; ?></textarea><br /><br /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" colspan="2" align="center"><br /><input type="submit" name="submit" value="<?php  echo _AT('edit_forum'); ?> [Alt-s]" accesskey="s" class="button"> - <input type="submit" name="cancel" class="button" value="<?php  echo _AT('cancel'); ?>" /></td>
</tr>
</table>
</p>
</form>

<?php
	require(AT_INCLUDE_PATH.'footer.inc.php');
?>