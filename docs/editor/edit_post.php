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

define('AT_INCLUDE_PATH', '../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_FORUMS);

require(AT_INCLUDE_PATH.'lib/forums.inc.php');

$fid = intval($_REQUEST['fid']);

if (!$fid) {
	header('Location: ../forum/list.php');
	exit;
}

if (!valid_forum_user($fid)) {
	require(AT_INCLUDE_PATH.'header.inc.php');
	$msg->addError('FORUM_DENIED');
	require(AT_INCLUDE_PATH.'footer.inc.php');
}

if ($_POST['cancel']) {
	$msg->addFeedback('CANCELLED');
	Header('Location: ../forum/view.php?fid='.$_POST['fid'].SEP.'pid='.$_POST['pid']);
	exit;
}

if ($_POST['edit_post']) {
	$_POST['subject']	= str_replace('<', '&lt;', trim($_POST['subject']));
	$_POST['body']		= str_replace('<', '&lt;', trim($_POST['body']));
	$_POST['pid']		= intval($_POST['pid']);

	$_POST['subject']  = $addslashes($_POST['subject']);
	$_POST['body']  = $addslashes($_POST['body']);

	$sql = "UPDATE ".TABLE_PREFIX."forums_threads SET subject='$_POST[subject]', body='$_POST[body]' WHERE post_id=$_POST[pid]";
	$result = mysql_query($sql,$db);

	$msg->addFeedback('POST_EDITED');
	if ($_POST['ppid'] == 0) {
		$_POST['ppid'] = $_POST['pid'];
	}
	header('Location: ../forum/view.php?fid='.$_POST['fid'].SEP.'pid='.$_POST['ppid']);
	exit;
}

define('AT_INCLUDE_PATH', '../include/');
$_section[0][0] = _AT('discussions');
$_section[0][1] = 'discussions/';
$_section[1][0] = _AT('forums');
$_section[1][1] = 'forum/list.php';
$_section[2][0] = AT_print(get_forum_name($_GET['fid']), 'forums.title');
$_section[2][1] = 'forum/index.php?fid='.$_GET['fid'];
$_section[3][0] = _AT('edit_post');

$onload = 'onload="document.form.subject.focus()"';

require(AT_INCLUDE_PATH.'header.inc.php');


echo '<h3><a href="forum/index.php?fid='.$_GET['fid'].SEP.'g=11">' . AT_print(get_forum_name($_GET['fid']), 'forums.title') . '</a><h3>';
	
if (isset($_GET['pid'])) {
	$pid = intval($_GET['pid']);
} else {
	$pid = intval($_POST['pid']);
}

if ($pid == 0) {
	$msg->addError('POST_ID_ZERO');
	require (AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

$sql = "SELECT * FROM ".TABLE_PREFIX."forums_threads WHERE post_id=$pid";
$result = mysql_query($sql,$db);
if (!($row = mysql_fetch_assoc($result))) {
	$msg->addError('POST_NOT_FOUND');
	require (AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="edit_post" value="true" />
<input type="hidden" name="pid" value="<?php echo $pid; ?>" />
<input type="hidden" name="ppid" value="<?php echo $row['parent_id']; ?>" />
<input type="hidden" name="fid" value="<?php echo $row['forum_id']; ?>" />

<div class="input-form">
	<div class="row">
		<label for="subject"><?php echo _AT('subject'); ?></label><br />
		<input maxlength="45" name="subject" size="36" value="<?php echo stripslashes(htmlspecialchars($row['subject'])); ?>" id="subject" />
	</div>

	<div class="row">
		<label for="body"><?php echo _AT('body'); ?></label>
		<textarea cols="65" name="body" rows="10" id="body"><?php echo $row['body']; ?></textarea>
	</div>
	
	<div class="row">
		<small class="spacer">&middot;<?php echo _AT('forum_links'); ?>
		&middot; <?php echo _AT('forum_email_links'); ?>
		&middot; <?php echo _AT('forum_html_disabled'); ?></small>
	</div>

	<div class="row buttons">
		<input name="submit" type="submit" value="  <?php echo _AT('save'); ?>" accesskey="s" />
		<input type="submit" name="cancel" value=" <?php echo _AT('cancel'); ?> " /></td>
	</div>

</div>
</form>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>