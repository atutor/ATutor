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

	if (isset($_POST['cancel'])) {
		header('Location: ../index.php?f='.AT_FEEDBACK_CANCELLED);
		exit;
	}

	if (isset($_POST['add_news'], $_SESSION['is_admin'])) {
		$_POST['formatting'] = intval($_POST['formatting']);

		if (($_POST['title'] == '') && ($_POST['body'] == '')) {
			$errors[] = AT_ERROR_ANN_BOTH_EMPTY;
		}

		if (!isset($errors)) {
			$sql	= "INSERT INTO ".TABLE_PREFIX."news VALUES (0, $_SESSION[course_id], $_SESSION[member_id], NOW(), $_POST[formatting], '$_POST[title]', '$_POST[body]')";
			mysql_query($sql, $db);

			header('Location: ../index.php?f='.AT_FEEDBACK_NEWS_ADDED);
			exit;
		}
	}

	$_section[0][0] = _AT('add_announcement');

	$onload = 'onload="document.form.title.focus()"';

	require(AT_INCLUDE_PATH.'header.inc.php');
	
	print_errors($errors);

?>
<h2><?php echo _AT('add_announcement'); ?></h2>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="add_news" value="true" />
<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center">
<tr>
	<th colspan="2" class="left"><img src="images/pen2.gif" border="0" class="menuimage12" alt="<?php echo _AT('editor_on'); ?>" title="<?php echo _AT('editor_on'); ?>" height="14" width="16" /><?php echo _AT('add_announcement'); ?></th>
</tr>
<tr>
	<td class="row1" align="right"><?php print_popup_help(AT_HELP_ANNOUNCEMENT); ?><b><label for="title"><?php echo _AT('title'); ?>:</label></b></td>
	<td class="row1"><input type="text" name="title" class="formfield" size="40" id="title" /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" valign="top" align="right"><b><label for="body"><?php echo _AT('body'); ?>:</label></b></td>
	<td class="row1"><textarea name="body" cols="55" rows="15" class="formfield" id="body"></textarea></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td align="right" class="row1">	
	<?php print_popup_help(AT_HELP_FORMATTING); ?>
	<b><?php echo _AT('formatting'); ?>:</b></td>
	<td class="row1"><input type="radio" name="formatting" value="0" id="text" <?php if ($_POST['formatting'] === 0) { echo 'checked="checked"'; } ?> /><label for="text"><?php echo _AT('plain_text'); ?></label>, <input type="radio" name="formatting" value="1" id="html" <?php if ($_POST['formatting'] !== 0) { echo 'checked="checked"'; } ?> /><label for="html"><?php echo _AT('html'); ?></label> <?php

	?></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" colspan="2"><a href="<?php echo substr($_my_uri, 0, strlen($_my_uri)-1); ?>#jumpcodes" title="<?php echo _AT('jump_codes'); ?>"><img src="images/clr.gif" height="1" width="1" alt="<?php echo _AT('jump_codes'); ?>" border="0" /></a><?php require(AT_INCLUDE_PATH.'html/code_picker.inc.php'); ?><br /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" colspan="2" align="center"><br /><a name="jumpcodes"></a><input type="submit" name="submit" value="<?php echo _AT('add_announcement'); ?> Alt-s" class="button" accesskey="s" /> - <input type="submit" name="cancel" class="button" value="<?php echo _AT('cancel'); ?> " /></td>
</tr>
</table>
</form>

<?php
	require(AT_INCLUDE_PATH.'footer.inc.php');
?>