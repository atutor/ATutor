<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2010                                                  */
/* Inclusive Design Institute                                               */
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

require_once(AT_INCLUDE_PATH.'../mods/_standard/forums/lib/forums.inc.php');

$fid = intval($_REQUEST['fid']);

if (isset($_GET['pid'])) {
	$pid = intval($_GET['pid']);
} else {
	$pid = intval($_POST['pid']);
}

if (!$pid || !$fid || !valid_forum_user($fid)) {
	$msg->addError('ITEM_NOT_FOUND');
	header('Location: ../../../forum/list.php');
	exit;
}

$sql = "SELECT *, UNIX_TIMESTAMP(date) AS udate FROM %sforums_threads WHERE post_id=%d";
$post_row = queryDB($sql, array(TABLE_PREFIX, $pid), TRUE);

if(count($post_row) == 0){
	$msg->addError('ITEM_NOT_FOUND');
	header('Location: '.url_rewrite('/mods/_standard/forums/forum/list.php', AT_PRETTY_URL_IS_HEADER));
	exit;
}

$forum_info = get_forum($fid, $_SESSION['course_id']);

$expiry = $post_row['udate'] + $forum_info['mins_to_edit'] * 60;

// check if we're either a) an assistant or, b) own this post and within the time allowed:
if (!(     authenticate(AT_PRIV_FORUMS, AT_PRIV_RETURN) 
		|| ($post_row['member_id'] == $_SESSION['member_id'] && ($expiry > time() || isset($_POST['edit_post']) ) )
	  ) 
   ) {
	$msg->addError('POST_EDIT_EXPIRE');
	header('Location: '.url_rewrite('mods/_standard/forums/forum/list.php', AT_PRETTY_URL_IS_HEADER));
	exit;
}

if ($_POST['cancel']) {
	$msg->addFeedback('CANCELLED');
    Header('Location: '.url_rewrite('mods/_standard/forums/forum/view.php?fid='.$_POST['fid'].SEP.'pid='.$_POST['pid'], AT_PRETTY_URL_IS_HEADER));
	exit;
}

if ($_POST['edit_post']) {
	$missing_fields = array();

//	$_POST['subject']	= str_replace('<', '&lt;', trim($_POST['subject']));
//	$_POST['body']		= str_replace('<', '&lt;', trim($_POST['body']));
	$_POST['pid']		= intval($_POST['pid']);

	$_POST['subject']  = $addslashes($_POST['subject']);
	//If subject > 60,then chop subject
	$_POST['subject'] = validate_length($_POST['subject'], 60);

	$_POST['body']  = $addslashes($_POST['body']);

	if ($_POST['subject'] == '')  {
		$missing_fields[] = _AT('subject');
	}

	if ($_POST['body'] == '') {
		$missing_fields[] = _AT('body');
	}
	if ($missing_fields) {
		$missing_fields = implode(', ', $missing_fields);
		$msg->addError(array('EMPTY_FIELDS', $missing_fields));
	}
	if (!$msg->containsErrors()) {

		$sql = "UPDATE %sforums_threads SET subject='%s', body='%s', last_comment=last_comment, date=date WHERE post_id=%d";
		$result = queryDB($sql, array(TABLE_PREFIX, $_POST['subject'], $_POST['body'], $_POST['pid']));
		
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		if ($_POST['ppid'] == 0) {
			$_POST['ppid'] = $_POST['pid'];
		}
		header('Location: '.url_rewrite('mods/_standard/forums/forum/view.php?fid='.$_POST['fid'].SEP.'pid='.$_POST['ppid'], AT_PRETTY_URL_IS_HEADER));
		exit;
	}
}

$_pages['mods/_standard/forums/forum/index.php?fid='.$fid]['title']    = $forum_info['title'];
$_pages['mods/_standard/forums/forum/index.php?fid='.$fid]['parent']   = 'mods/_standard/forums/forum/list.php';
$_pages['mods/_standard/forums/forum/index.php?fid='.$fid]['children'] = array('mods/_standard/forums/forum/new_thread.php?fid='.$fid);

$_pages['mods/_standard/forums/forum/new_thread.php?fid='.$fid]['title_var'] = 'new_thread';
$_pages['mods/_standard/forums/forum/new_thread.php?fid='.$fid]['parent']    = 'mods/_standard/forums/forum/index.php?fid='.$fid;

$_pages['mods/_standard/forums/forum/view.php']['title']  = $post_row['subject'];
$_pages['mods/_standard/forums/forum/view.php']['parent'] = 'mods/_standard/forums/forum/index.php?fid='.$fid;

$_pages['mods/_standard/forums/edit_post.php']['title_var'] = 'edit_post';
$_pages['mods/_standard/forums/edit_post.php']['parent']    = 'mods/_standard/forums/forum/index.php?fid='.$fid;
$_pages['mods/_standard/forums/edit_post.php']['children']  = array();

$onload = 'document.form.subject.focus();';

require(AT_INCLUDE_PATH.'header.inc.php');
$savant->assign('pid', $pid);
$savant->assign('ppid', $post_row['parent_id']);
$savant->assign('forumid', $post_row['forum_id']);
$savant->assign('subject', $post_row['subject']);
$savant->assign('body', $post_row['body']);
$savant->display('instructor/forums/edit_post.tmpl.php');
?>



<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>