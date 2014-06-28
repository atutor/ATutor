<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

if (!defined('AT_INCLUDE_PATH')) { exit; }

if (!$_SESSION['valid_user']) {
	$msg->printInfos('LOGIN_TO_POST');
	return;
}
global $msg;
$msg->printErrors();

if (isset($_POST['submit'])) {
$parent_id	= $_POST['parent_id'];
$parent_name	= $_POST['parent_name'];
$subject =  $_POST['subject'];
$body = $_POST['body'];
    //post reply is set when there is an error occuring.
    if ($_POST['reply']!=''){
        $saved_post['body'] = $_POST['replytext'];
        $reply_hidden = '<input name="reply" type="hidden" value="'.AT_print($_REQUEST['reply'], 'input.text').'" />';
    }
    
} else if (isset($_GET['reply']) && $_GET['reply'] != '') {
	$subject = $saved_post['subject'];
	$reply_hidden = '<input name="reply" type="hidden" value="'.AT_print($_REQUEST['reply'], 'input.text').'" />';

	if (substr($subject, 0, 3) != 'Re:') {
		$subject = 'Re: '.$subject;
	}
}

?>
<a name="post" id="post"></a>
<form action="mods/_standard/forums/forum/new_thread.php" method="post" name="form">
<?php
if(isset($_REQUEST['pid'])){
    $parent_id	= $_REQUEST['pid'];
}else if($row['parent_id'] == 0  || !isset($row['parent_id'])){
	$parent_id	= $row['post_id'];
}else{
	$parent_id	= $row['parent_id'];
}
?>
<input name="parent_id" type="hidden" value="<?php echo $parent_id; ?>" />
<input name="fid" type="hidden" value="<?php echo intval($_REQUEST['fid']); ?>" />
<input name="page" type="hidden" value="<?php echo intval($_REQUEST['page']); ?>" />
<input name="parent_name" type="hidden" value="<?php echo urlencode($parent_name); ?>" />
<?php echo $reply_hidden; //print hidden reply field if it exists. ?>

<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('post_message'); ?></legend>
	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="subject"><?php echo _AT('subject'); ?></label><br />
		<input type="text" maxlength="80" name="subject" size="36" value="<?php echo AT_print($subject, 'input.text'); ?>" id="subject" />
	</div>

	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="body"><?php echo _AT('body'); ?></label><br />
		<textarea cols="45" name="body" rows="10" id="body"><?php echo AT_print($_POST['body'], 'input.text'); ?></textarea>

		<small class="spacer"><br />&middot; <?php echo _AT('forum_links'); ?><br />
		&middot; <?php echo _AT('forum_email_links'); ?><br />
		&middot; <?php echo _AT('forum_html_disabled'); ?></small>
	</div>
	<div class="row">	
		<a href="<?php echo htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES); ?>#jumpcodes" title="<?php echo _AT('jump_codes'); ?>" ><img src="images/clr.gif" height="1" width="1" alt="<?php echo _AT('jump_codes'); ?>" border="0" style="float:left;"/></a><?php require(AT_INCLUDE_PATH.'html/code_picker.inc.php'); ?>
	</div>
	<?php if (!$subscribed): ?>
		<div class="row">
			<input type="checkbox" name="subscribe" value="1" id="sub" />
			<label for="sub"><?php echo _AT('thread_subscribe'); ?></label>
		</div>
	<?php else: ?>
		<div class="row">
			<?php echo _AT('thread_already_subscribed'); ?>
		</div>
	<?php endif; ?>
</fieldset>
	<div class="row buttons">
			<a name="jumpcodes" id="jumpcode"></a>
		<input name="submit" accesskey="s" type="submit" value=" <?php echo _AT('post'); ?>" />
		<?php if ($new_thread == TRUE) : ?>
			<input name="cancel" type="submit" value="<?php echo _AT('cancel'); ?>" />
		<?php endif; ?>
	</div>
</div>
</form>