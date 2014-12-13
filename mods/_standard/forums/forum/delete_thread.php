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

define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require_once(AT_INCLUDE_PATH.'../mods/_standard/forums/lib/forums.inc.php');

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
		$sql	= "SELECT * FROM %sforums_threads WHERE post_id=%d AND forum_id=%d";
		$row_posts = queryDB($sql, array(TABLE_PREFIX, $pid, $fid), TRUE);
		
		if(count($row_posts) == 0){
			$msg->addError('FORUM_NOT_FOUND');
			header('Location: list.php');
			exit;

		} // else:

		/* Decrement count for number of posts and topics*/

		$sql	= "UPDATE %sforums SET num_posts=num_posts-(1+%d), num_topics=num_topics-1, last_post=last_post WHERE forum_id=%d";
		$result = queryDB($sql, array(TABLE_PREFIX, $row_posts['num_comments'], $fid));

		$sql	= "DELETE FROM %sforums_threads WHERE (parent_id=%d OR post_id=%d) AND forum_id=%d";
		$result = queryDB($sql, array(TABLE_PREFIX, $pid, $pid, $fid));

		$sql	= "DELETE FROM %sforums_accessed WHERE post_id=%d";
		$result = queryDB($sql, array(TABLE_PREFIX, $pid));
		
	} else {   /* Just deleting a single thread */
		$sql	= "DELETE FROM %sforums_threads WHERE post_id=%d AND forum_id=%d";
		$result = queryDB($sql, array(TABLE_PREFIX, $pid, $fid));
		if($result == 0){
			$msg->addError('FORUM_NOT_FOUND');
			header('Location: list.php');
			exit;
		}

	    /* Decrement count of comments in forums_threads table*/
		$sql	= "UPDATE %sforums_threads SET num_comments=num_comments-1, last_comment=last_comment, date=date WHERE post_id=%d";
		$result = queryDB($sql,array(TABLE_PREFIX, $ppid));
		
		/* Decrement count of posts in forums table */
		$sql	= "UPDATE %sforums SET num_posts=num_posts-1, last_post=last_post WHERE forum_id=%d";
		$result = queryDB($sql, array(TABLE_PREFIX, $fid));
		
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

$_pages['mods/_standard/forums/forum/index.php?fid='.$fid]['title']    = get_forum_name($fid);
$_pages['mods/_standard/forums/forum/index.php?fid='.$fid]['parent']   = 'forum/list.php';
$_pages['mods/_standard/forums/forum/index.php?fid='.$fid]['children'] = array('mods/_standard/forums/forum/new_thread.php?fid='.$fid);

$_pages['mods/_standard/forums/forum/new_thread.php?fid='.$fid]['title_var'] = 'new_thread';
$_pages['mods/_standard/forums/forum/new_thread.php?fid='.$fid]['parent']    = 'mods/_standard/forums/forum/index.php?fid='.$fid;

$_pages['mods/_standard/forums/forum/view.php']['title']  = $post_row['subject'];
$_pages['mods/_standard/forums/forum/view.php']['parent'] = 'forum/index.php?fid='.$fid;

$_pages['mods/_standard/forums/forum/delete_thread.php']['title_var'] = 'delete_post';
$_pages['mods/_standard/forums/forum/delete_thread.php']['parent']    = 'mods/_standard/forums/forum/index.php?fid='.$fid;
$_pages['mods/_standard/forums/forum/delete_thread.php']['children']  = array();

require(AT_INCLUDE_PATH.'header.inc.php');

$sql = "SELECT * from %sforums_threads WHERE post_id = %d";
$row = queryDB($sql, array(TABLE_PREFIX, $pid), TRUE);

$title = AT_print($row['subject'], 'forums_threads.subject');


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