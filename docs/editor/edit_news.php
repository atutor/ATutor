<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/

define('AT_INCLUDE_PATH', '../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
$content_base_href = 'get.php/';

authenticate(AT_PRIV_ANNOUNCEMENTS);

	require_once(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

	global $savant;
	$msg =& new Message($savant);

	if ($_POST['cancel']) {
		$msg->addFeedback('CANCELLED');
		Header('Location: ../index.php');
		exit;
	}

if ($_POST['edit_news']) {
	$_POST['title'] = trim($_POST['title']);
	$_POST['body_text']  = trim($_POST['body_text']);
	$_POST['aid']	= intval($_POST['aid']);
	$_POST['formatting']	= intval($_POST['formatting']);

	if (($_POST['title'] == '') && ($_POST['body_text'] == '')) {
		$msg->addErros('ANN_BOTH_EMPTY');
	}

	if (!$msg->containsErrors() && isset($_POST['submit'])) {
		$_POST['title']  = $addslashes($_POST['title']);
		$_POST['body_text']  = $addslashes($_POST['body_text']);

		$sql = "UPDATE ".TABLE_PREFIX."news SET title='$_POST[title]', body='$_POST[body_text]', formatting=$_POST[formatting] WHERE news_id=$_POST[aid] AND course_id=$_SESSION[course_id]";
		$result = mysql_query($sql,$db);

		$msg->addFeedback('NEWS_UPDATED');
		Header('Location: ../index.php');
		exit;
	}
}

$_section[0][0] = _AT('edit_announcement');

//$onload = 'onLoad="document.form.title.focus()"';
	//used for visual editor
	if (($_POST['setvisual'] && !$_POST['settext']) || $_GET['setvisual']){
		$onload = 'onload="initEditor();"';
	}else {
		$onload = ' onload="document.form.title.focus();"';
	}
require(AT_INCLUDE_PATH.'header.inc.php');

$msg->printErrors();

?>
<h2><?php echo _AT('edit_announcement'); ?></h2>
<?php
	
	if (isset($_GET['aid'])) {
		$aid = intval($_GET['aid']);
	} else {
		$aid = intval($_POST['aid']);
	}

	if ($aid == 0) {
		$msg->printErrors('ANN_ID_ZERO');
		require (AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}
	
	$sql = "SELECT * FROM ".TABLE_PREFIX."news WHERE news_id=$aid AND member_id=$_SESSION[member_id] AND course_id=$_SESSION[course_id]";
	$result = mysql_query($sql,$db);
	if (!($row = mysql_fetch_array($result))) {
		$msg->printErrors('ANN_NOT_FOUND');
		require (AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}
	$_POST['formatting'] = intval($row['formatting']);

require(AT_INCLUDE_PATH.'html/editor_tabs/news.inc.php');

?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="edit_news" value="true">
<input type="hidden" name="aid" value="<?php echo $row['news_id']; ?>">
<p>
<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center">
<tr>
	<th colspan="2" class="cyan"><img src="<?php echo $_base_href; ?>images/pen2.gif" border="0" class="menuimage12" alt="<?php echo _AT('editor_on'); ?>" title="<?php echo _AT('editor_on'); ?>" height="14" width="16" /><?php echo _AT('edit_announcement'); ?></th>
</tr>

<tr><td height="1" class="row2" colspan="2"></td></tr>

<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td align="right" class="row1"><b><?php echo _AT('title'); ?>:</b></td>
	<td class="row1"><input type="text" name="title" id="title" value="<?php echo htmlspecialchars(stripslashes($row['title'])); ?>" class="formfield" size="40"></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td align="right" class="row1">	
	<?php print_popup_help('FORMATTING'); ?>
	<b><?php echo _AT('formatting'); ?>:</b></td>
	<td class="row1">
	<input type="radio" name="formatting" value="0" id="text" <?php if ($_POST['formatting'] === 0) { echo 'checked="checked"'; } ?> onclick="javascript: document.form.setvisual.disabled=true;" <?php if ($_POST['setvisual'] && !$_POST['settext']) { echo 'disabled="disabled"'; } ?> />
	<label for="text"><?php echo _AT('plain_text'); ?></label>,

	<input type="radio" name="formatting" value="1" id="html" <?php if ($_POST['formatting'] == 1 || $_POST['setvisual']) { echo 'checked="checked"'; } ?> onclick="javascript: document.form.setvisual.disabled=false;"  /><label for="html"><?php echo _AT('html'); ?></label>

	<?php
if (($_POST['setvisual'] && !$_POST['settext']) || $_GET['setvisual']){
	echo '<input type="hidden" name="setvisual" value="'.$_POST['setvisual'].'" />';
	echo '<input type="submit" name="settext" value="'._AT('switch_text').'" class="button" />';
} else {
	echo '<input type="submit" name="setvisual" value="'._AT('switch_visual').'" class="button" ';
	if ($_POST['formatting']==0) { echo 'disabled="disabled"'; }
	echo '/>';
} ?></td>
</tr>

<tr>
	<td class="row1" valign="top" align="right"><b><?php echo _AT('body'); ?>:</b></td>
	<td class="row1"><textarea name="body_text" cols="55" rows="15" id="body_text" class="formfield" wrap="wrap"><?php echo $row['body']; ?></textarea></td>
</tr>

<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" colspan="2" align="center"><br /><a name="jumpcodes"></a><input type="submit" name="submit" value="<?php echo _AT('edit_announcement'); ?>[Alt-s]" accesskey="s" class="button"> - <input type="submit" name="cancel" class="button" value="<?php echo _AT('cancel'); ?> " /></td>
</tr>
</table>
</p>
</form>
<?php
	require (AT_INCLUDE_PATH.'footer.inc.php');
?>
