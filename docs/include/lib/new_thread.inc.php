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
if (!defined('AT_INCLUDE_PATH')) { exit; }

if (!$_SESSION['valid_user']) {
	$msg->printInfos('LOGIN_TO_POST');
	return;
}

$msg->printErrors();

if ($_POST['submit']) {
	$subject	= $_POST['subject'];
	$body		= $_POST['body'];
	$parent_id	= $_POST['parent_id'];
	$parent_name	= $_POST['parent_name'];
} else if ($_GET['reply'] != '') {
	$subject = $saved_post['subject'];

	if (substr($subject, 0, 3) != 'Re:') {
		$subject = 'Re: '.$subject;
	}
}

?>
<form action="forum/new_thread.php" method="post" name="form">
<input name="parent_id" type="hidden" value="<?php echo $parent_id; ?>" />
<input name="fid" type="hidden" value="<?php echo $fid; ?>" />
<input name="reply" type="hidden" value="<?php echo $_GET['reply']; ?>" />
<input name="page" type="hidden" value="<?php echo $_GET['page']; ?>" />
<input name="parent_name" type="hidden" value="<?php echo $parent_name; ?>" />
<br />
<table cellspacing="1" cellpadding="0" border="0" class="bodyline" align="center" summary="" width="450">
<tr>
	<th colspan="2" class="cyan"><?php echo _AT('add_post'); ?></th>
</tr>
<tr>
	<td class="row1" align="right"><a name="post"></a><label for="subject"><b><?php echo _AT('subject'); ?>:</b></label></td>
	<td class="row1"><input class="formfield" maxlength="80" name="subject" size="36" value="<?php echo stripslashes(htmlspecialchars($subject)); ?>" id="subject" /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right" valign="top"><label for="body"><b><?php echo _AT('body'); ?>:</b></label></td>
	<td class="row1"><textarea class="formfield" cols="45" name="body" rows="10" id="body"><?php echo $body; ?></textarea><br />
	<small class="spacer">&middot; <?php echo _AT('forum_links'); ?><br />
	&middot; <?php echo _AT('forum_email_links'); ?><br />
	&middot; <?php echo _AT('forum_html_disabled'); ?></small></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<?php
	if ($_GET['reply']) {
?>
<tr>
	<td class="row1" align="right" valign="top"><label for="body"><b><?php echo _AT('forum_reply_to'); ?>:</b></label></td>
	<td class="row1"><textarea class="formfield" cols="45" name="replytext" rows="5"><?php echo $saved_post['body']; ?></textarea></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<?php
	} /* end if ($_GET['reply']) */
?>
<tr>
	<td class="row1" colspan="2"><a href="<?php echo $_SERVER['REQUEST_URI']; ?>#jumpcodes" title="<?php echo _AT('jump_codes'); ?>"><img src="images/clr.gif" height="1" width="1" alt="<?php echo _AT('jump_codes'); ?>" border="0" /></a><?php require(AT_INCLUDE_PATH.'html/code_picker.inc.php'); ?></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" colspan="2"><a name="jumpcodes"></a><?php
	if (!$subscribed) {
	?><input type="checkbox" name="subscribe" value="1" id="sub" /><label for="sub"><?php echo _AT('thread_subscribe'); ?></label><?php } else {
	echo _AT('thread_already_subscribed');
	}?><br /><br /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" colspan="2" align="center"><input name="submit" class="button" accesskey="s" type="submit" value=" <?php echo _AT('post'); ?> [Alt-s]" /></td>
</tr>
</table>

</form>