<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: edit_post.php 7609 2008-06-18 19:17:19Z hwong $
define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

// authenticate ot+oid ....
$owner_type = abs($_REQUEST['ot']);
$owner_id = abs($_REQUEST['oid']);
if (!($owner_status = blogs_authenticate($owner_type, $owner_id)) || !query_bit($owner_status, BLOGS_AUTH_WRITE)) {
	$msg->addError('ACCESS_DENIED');
	header('Location: index.php');
	exit;
}

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.url_rewrite('mods/_standard/blogs/view.php?ot='.BLOGS_GROUP.SEP.'oid='.$_POST['oid'], AT_PRETTY_URL_IS_HEADER));
	exit;
} else if (isset($_POST['submit'])) {
	$_POST['title'] = $addslashes(trim($_POST['title']));
	$_POST['body']  = $addslashes(trim($_POST['body']));
	$id = abs($_POST['id']);

	if ($_POST['body'] == '') {
		$msg->addError(array('EMPTY_FIELDS', _AT('body')));
	}

	if (!$msg->containsErrors()) {
		$_POST['title'] = htmlspecialchars($_POST['title']);
		$_POST['body']  = htmlspecialchars($_POST['body']);
		$_POST['private'] = abs($_POST['private']);
		$sql = "UPDATE ".TABLE_PREFIX."blog_posts SET private=$_POST[private], title='$_POST[title]', body='$_POST[body]', date=date WHERE owner_type=".BLOGS_GROUP." AND owner_id=$_REQUEST[oid] AND post_id=$id";
		mysql_query($sql, $db);

		$msg->addFeedback('POST_ADDED_SUCCESSFULLY');

		header('Location: '.url_rewrite('mods/_standard/blogs/post.php?ot='.BLOGS_GROUP.SEP.'oid='.$_POST['oid'].SEP.'id='.$id, AT_PRETTY_URL_IS_HEADER));
		exit;
	}
}

$id = abs($_REQUEST['id']);
$sql = "SELECT private, title, body FROM ".TABLE_PREFIX."blog_posts WHERE owner_type=".BLOGS_GROUP." AND owner_id=$_REQUEST[oid] AND post_id=$id";
$result = mysql_query($sql, $db);
$post_row = mysql_fetch_assoc($result);

$_pages['mods/_standard/blogs/edit_post.php']['parent']    = 'mods/_standard/blogs/post.php?ot='.BLOGS_GROUP.SEP.'oid='.$_REQUEST['oid'].SEP.'id='.$_REQUEST['id'];
$_pages['mods/_standard/blogs/post.php?ot='.BLOGS_GROUP.SEP.'oid='.$_REQUEST['oid'].SEP.'id='.$_REQUEST['id']] = $_pages['mods/_standard/blogs/post.php'];
$_pages['mods/_standard/blogs/post.php?ot='.BLOGS_GROUP.SEP.'oid='.$_REQUEST['oid'].SEP.'id='.$_REQUEST['id']]['children'] = array('mods/_standard/blogs/edit_post.php', 'mods/_standard/blogs/delete_post.php?ot='.BLOGS_GROUP.SEP.'oid='.$_REQUEST['oid'].SEP.'id='.$_REQUEST['id']);

$_pages['mods/_standard/blogs/post.php?ot='.BLOGS_GROUP.SEP.'oid='.$_REQUEST['oid'].SEP.'id='.$_REQUEST['id']]['parent'] = 'mods/_standard/blogs/view.php?ot='.BLOGS_GROUP.SEP.'oid='.$_REQUEST['oid'];
$_pages['mods/_standard/blogs/post.php?ot='.BLOGS_GROUP.SEP.'oid='.$_REQUEST['oid'].SEP.'id='.$_REQUEST['id']]['title'] = $post_row['title'];
$_pages['mods/_standard/blogs/post.php?ot='.BLOGS_GROUP.SEP.'oid='.$_REQUEST['oid'].SEP.'id='.$_REQUEST['id']]['children'] = array('mods/_standard/blogs/edit_post.php', 'mods/_standard/blogs/delete_post.php?ot='.BLOGS_GROUP.SEP.'oid='.$_REQUEST['oid'].SEP.'id='.$_REQUEST['id']);

$_pages['mods/_standard/blogs/view.php?ot='.BLOGS_GROUP.SEP.'oid='.$_REQUEST['oid']]['title'] = blogs_get_blog_name(BLOGS_GROUP, $_REQUEST['oid']);
$_pages['mods/_standard/blogs/view.php?ot='.BLOGS_GROUP.SEP.'oid='.$_REQUEST['oid']]['parent']    = 'mods/_standard/blogs/index.php';
$_pages['mods/_standard/blogs/view.php?ot='.BLOGS_GROUP.SEP.'oid='.$_REQUEST['oid']]['children']  = array('mods/_standard/blogs/add_post.php');


$onload = 'document.form.title.focus();';
require (AT_INCLUDE_PATH.'header.inc.php');

?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
<input type="hidden" name="ot" value="<?php echo BLOGS_GROUP; ?>" />
<input type="hidden" name="oid" value="<?php echo abs($_REQUEST['oid']); ?>" />
<input type="hidden" name="id" value="<?php echo $id; ?>" />
<div class="input-form">
	<div class="row">
		<label for="title"><?php echo _AT('title'); ?></label><br />
		<input type="text" name="title" id="title" value="<?php echo $post_row['title']; ?>" size="50" />
	</div>
	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="body"><?php echo _AT('body'); ?></label><br />
		<textarea name="body" id="body" cols="40" rows="10"><?php echo $post_row['body']; ?></textarea>
	</div>

	<div class="row">	
		<a href="<?php echo htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES); ?>#jumpcodes" title="<?php echo _AT('jump_codes'); ?>"><img src="images/clr.gif" height="1" width="1" alt="<?php echo _AT('jump_codes'); ?>" border="0" /></a><?php require(AT_INCLUDE_PATH.'html/code_picker.inc.php'); ?>

		<a name="jumpcodes"></a>
	</div>

	<div class="row">
		<input type="checkbox" name="private" value="1" id="private" <?php if ($post_row['private']) { echo 'checked="checked"'; } ?> /><label for="private"><?php echo _AT('private'); ?></label>
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s" /> <input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" /> 
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>