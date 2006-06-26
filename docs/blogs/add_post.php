<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$
define('AT_INCLUDE_PATH', '../include/');
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
	header('Location: view.php?ot='.BLOGS_GROUP.SEP.'oid='.$_POST['oid']);
	exit;
} else if (isset($_POST['submit'])) {
	$_POST['title'] = $addslashes(trim($_POST['title']));
	$_POST['body']  = $addslashes(trim($_POST['body']));

	if ($_POST['body'] == '') {
		$msg->addError('EMPTY_BODY');
	}

	if (!$msg->containsErrors()) {
		$_POST['title'] = htmlspecialchars($_POST['title']);
		$_POST['body']  = htmlspecialchars($_POST['body']);
		$_POST['private'] = abs($_POST['private']);
		$sql = "INSERT INTO ".TABLE_PREFIX."blog_posts VALUES (0, $_SESSION[member_id], ".BLOGS_GROUP.", $_POST[oid], $_POST[private], NOW(), 0, '$_POST[title]', '$_POST[body]')";
		mysql_query($sql, $db);

		$msg->addFeedback('POST_ADDED_SUCCESSFULLY');

		header('Location: view.php?ot='.BLOGS_GROUP.SEP.'oid='.$_POST['oid']);
		exit;
	}
}

// this will also be dynamic as the parent page changes
$_pages['blogs/add_post.php?ot='.BLOGS_GROUP.SEP.'oid='.$_REQUEST['oid']]['title_var'] = 'add';
$_pages['blogs/add_post.php?ot='.BLOGS_GROUP.SEP.'oid='.$_REQUEST['oid']]['parent'] = 'blogs/view.php';

$_pages['blogs/add_post.php']['title_var'] = 'add';
$_pages['blogs/add_post.php']['parent']    = 'blogs/view.php?ot='.BLOGS_GROUP.SEP.'oid='.$_REQUEST['oid'];

$_pages['blogs/view.php?ot='.BLOGS_GROUP.SEP.'oid='.$_REQUEST['oid']]['title'] = blogs_get_blog_name(BLOGS_GROUP, $_REQUEST['oid']);
$_pages['blogs/view.php?ot='.BLOGS_GROUP.SEP.'oid='.$_REQUEST['oid']]['parent']    = 'blogs/index.php';
$_pages['blogs/view.php?ot='.BLOGS_GROUP.SEP.'oid='.$_REQUEST['oid']]['children']  = array('blogs/add_post.php');


$onload = 'document.form.title.focus();';
require (AT_INCLUDE_PATH.'header.inc.php');
?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
<input type="hidden" name="ot" value="<?php echo BLOGS_GROUP; ?>" />
<input type="hidden" name="oid" value="<?php echo abs($_REQUEST['oid']); ?>" />
<div class="input-form">
	<div class="row">
		<label for="title"><?php echo _AT('title'); ?></label><br />
		<input type="text" name="title" id="title" value="<?php echo htmlspecialchars(stripslashes($_POST['title'])); ?>" size="50" />
	</div>
	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="body"><?php echo _AT('body'); ?></label><br />
		<textarea name="body" id="body" cols="40" rows="10"></textarea>
	</div>

	<div class="row">	
		<a href="<?php echo htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES); ?>#jumpcodes" title="<?php echo _AT('jump_codes'); ?>"><img src="images/clr.gif" height="1" width="1" alt="<?php echo _AT('jump_codes'); ?>" border="0" /></a><?php require(AT_INCLUDE_PATH.'html/code_picker.inc.php'); ?>

		<a name="jumpcodes"></a>
	</div>

	<div class="row">
		<input type="checkbox" name="private" value="1" id="private" /><label for="private"><?php echo _AT('private'); ?></label>
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('post'); ?>" accesskey="s" /> <input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" /> 
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>