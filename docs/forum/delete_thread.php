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
require(AT_INCLUDE_PATH.'vitals.inc.php');
require_once(AT_INCLUDE_PATH.'classes/Message/Message.class.php');
require(AT_INCLUDE_PATH.'lib/forums.inc.php');

authenticate(AT_PRIV_FORUMS);

global $savant;
$msg =& new Message($savant);

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
	if ($_POST['ppid']) {
		header('Location: view.php?fid='.$_POST['fid'].SEP.'pid='.$_POST['ppid']);
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
		$sql	= "UPDATE ".TABLE_PREFIX."forums SET num_posts=num_posts-1-".$row['num_comments'].", num_topics=num_topics-1 WHERE forum_id=$fid";
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
		$sql	= "UPDATE ".TABLE_PREFIX."forums_threads SET num_comments=num_comments-1 WHERE post_id=$ppid";
		$result = mysql_query($sql, $db);

		/* Decrement count of posts in forums table */
		$sql	= "UPDATE ".TABLE_PREFIX."forums SET num_posts=num_posts-1 WHERE forum_id=$fid";
		$result = mysql_query($sql, $db);

	}

	if ($ppid) {
		$msg->addFeedback('MESSAGE_DELETED');
		header('Location: view.php?fid='.$fid.SEP.'pid='.$ppid);
		exit;
	} else {
		$msg->addFeedback('THREAD_DELETED');
		header('Location: index.php?fid='.$fid);
		exit;
	}
}

$_section[0][0] = _AT('discussions');
$_section[0][1] = 'discussions/';
$_section[1][0] = _AT('forums');
$_section[1][1] = 'forum/list.php';
$_section[2][0] = AT_print(get_forum_name($_REQUEST['fid']), 'forums.title');
$_section[2][1] = 'forum/index.php?fid='.$__REQUEST['fid'];
$_section[3][0] = _AT('delete_thread');

require(AT_INCLUDE_PATH.'header.inc.php');
echo '<h2>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '<img src="images/icons/default/square-large-discussions.gif" width="42" height="38" border="0" alt="" class="menuimage" /> ';
}
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
	echo '<a href="discussions/index.php?g=11">'._AT('discussions').'</a>';
}
echo '</h2>';

echo'<h3>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '<img src="images/icons/default/forum-large.gif" width="42" height="38" border="0" alt="" class="menuimageh3" />';
}
echo '<a href="forum/list.php">'._AT('forums').'</a> - <a href="forum/index.php?fid='.$fid.SEP.'g=11">'.AT_print(get_forum_name($fid), 'forums.title').'</a>';

echo '</h3>';

$hidden_vars['fid']  = $_GET['fid'];
$hidden_vars['pid']  = $_GET['pid'];
$hidden_vars['ppid'] = $_GET['ppid'];

if (($ppid=='') || ($ppid =='0')) {
	$msg->addConfirm('DELETE_THREAD', $hidden_vars);
	$ppid = '0';
} else {
	$msg->addConfirm('DELETE_MESSAGE', $hidden_vars);
}

$msg->printConfirm();

require(AT_INCLUDE_PATH.'footer.inc.php');
?>