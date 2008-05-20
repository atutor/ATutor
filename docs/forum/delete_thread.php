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

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/forums.inc.php');

authenticate(AT_PRIV_FORUMS);

$pid  = intval($_REQUEST['pid']);
$ppid = intval($_REQUEST['ppid']);
$fid  = intval($_REQUEST['fid']);

if (!valid_forum_user($fid)) {
	$msg->addError('FORUM_NOT_FOUND');
	header('Location: list.php');
	exit;
}

if (isset($_POST['submit_no'])) {
	
	$msg->addFeedback('CANCELLED'); 
	if ($_POST['nest']) {
		header('Location: view.php?fid='.$_POST['fid'].SEP.'pid='. ($_POST['ppid'] ? $_POST['ppid'] : $_POST['pid']));
		exit;
	} else {
		header('Location: index.php?fid='.$_POST['fid']);
		exit;
	}

	exit;

} else if (isset($_POST['submit_yes'])) {
	// check if they have access
	if (!valid_forum_user($fid)) {
		$msg->addError('FORUM_NOT_FOUND');
		header('Location: list.php');
		exit;
	}

	if ($ppid == 0) {   /* If deleting an entire post */
		/* First get number of comments from specific post */
		$sql	= "SELECT * FROM ".TABLE_PREFIX."forums_threads WHERE post_id=$pid AND forum_id=$fid";
		$result = mysql_query($sql, $db);
		if (!($row = mysql_fetch_assoc($result))) {
			$msg->addError('FORUM_NOT_FOUND');
			header('Location: list.php');
			exit;

		} // else:

		/* Decrement count for number of posts and topics*/
		$sql	= "UPDATE ".TABLE_PREFIX."forums SET num_posts=num_posts-1-".$row['num_comments'].", num_topics=num_topics-1, last_post=last_post WHERE forum_id=$fid";
		$result = mysql_query($sql, $db);

		$sql	= "DELETE FROM ".TABLE_PREFIX."forums_threads WHERE (parent_id=$pid OR post_id=$pid) AND forum_id=$fid";
		$result = mysql_query($sql, $db);

		$sql	= "DELETE FROM ".TABLE_PREFIX."forums_accessed WHERE post_id=$pid";
		$result = mysql_query($sql, $db);

	} else {   /* Just deleting a single thread */
		$sql	= "DELETE FROM ".TABLE_PREFIX."forums_threads WHERE post_id=$pid AND forum_id=$fid";
		$result = mysql_query($sql, $db);
		if (mysql_affected_rows($db) == 0) {
			$msg->addError('FORUM_NOT_FOUND');
			header('Location: list.php');
			exit;
		}

	    /* Decrement count of comments in forums_threads table*/
		$sql	= "UPDATE ".TABLE_PREFIX."forums_threads SET num_comments=num_comments-1, last_comment=last_comment, date=date WHERE post_id=$ppid";
		$result = mysql_query($sql, $db);

		/* Decrement count of posts in forums table */
		$sql	= "UPDATE ".TABLE_PREFIX."forums SET num_posts=num_posts-1, last_post=last_post WHERE forum_id=$fid";
		$result = mysql_query($sql, $db);

	}

	if ($ppid) {
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		header('Location: view.php?fid='.$fid.SEP.'pid='.$ppid);
		exit;
	} else {
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		header('Location: index.php?fid='.$fid);
		exit;
	}
}

$_pages['forum/index.php?fid='.$fid]['title']    = get_forum_name($fid);
$_pages['forum/index.php?fid='.$fid]['parent']   = 'forum/list.php';
$_pages['forum/index.php?fid='.$fid]['children'] = array('forum/new_thread.php?fid='.$fid);

$_pages['forum/new_thread.php?fid='.$fid]['title_var'] = 'new_thread';
$_pages['forum/new_thread.php?fid='.$fid]['parent']    = 'forum/index.php?fid='.$fid;

$_pages['forum/view.php']['title']  = $post_row['subject'];
$_pages['forum/view.php']['parent'] = 'forum/index.php?fid='.$fid;

$_pages['forum/delete_thread.php']['title_var'] = 'delete_post';
$_pages['forum/delete_thread.php']['parent']    = 'forum/index.php?fid='.$fid;
$_pages['forum/delete_thread.php']['children']  = array();

require(AT_INCLUDE_PATH.'header.inc.php');


$sql = "SELECT * from ".TABLE_PREFIX."forums_threads WHERE post_id = '$pid'";
$result = mysql_query($sql, $db);
while ($row = mysql_fetch_assoc($result)){
	$title = $row['subject'];
}

$hidden_vars['fid']  = $_GET['fid'];
$hidden_vars['pid']  = $_GET['pid'];
$hidden_vars['ppid'] = $_GET['ppid'];
$hidden_vars['nest'] = $_GET['nest'];

$msg->addConfirm(array('DELETE', $title),$hidden_vars);
if (($ppid=='') || ($ppid =='0')) {
	$ppid = '0';
}

$msg->printConfirm();

require(AT_INCLUDE_PATH.'footer.inc.php');
?>