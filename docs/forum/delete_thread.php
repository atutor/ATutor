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

$fid = intval($_GET['fid']);

$_section[0][0] = _AT('discussions');
$_section[0][1] = 'discussions/';
$_section[1][0] = _AT('forums');
$_section[1][1] = 'forum/list.php';
$_section[2][0] = AT_print(get_forum($_GET['fid']), 'forums.title');
$_section[2][1] = 'forum/index.php?fid='.$_GET['fid'];
$_section[3][0] = _AT('delete_thread');

authenticate(AT_PRIV_FORUMS);

if ($_GET['d'] == '1') {
	$pid  = intval($_GET['pid']);
	$ppid = intval($_GET['ppid']);
	$fid = intval($_GET['fid']);

	if ($ppid == 0) {   /* If deleting an entire post */
		/* First get number of comments from specific post */
		$sql	= "SELECT * FROM ".TABLE_PREFIX."forums_threads where post_id=$pid AND course_id=$_SESSION[course_id]";
		$result = mysql_query($sql, $db);
		if ($row = mysql_fetch_array($result)) {

			/* Decrement count for number of posts and topics*/
			$sql	= "UPDATE ".TABLE_PREFIX."forums SET num_posts=num_posts-1-".$row['num_comments'].", num_topics=num_topics-1 WHERE forum_id=$fid";
			$result = mysql_query($sql, $db);
		}
		$sql	= "DELETE FROM ".TABLE_PREFIX."forums_threads WHERE (parent_id=$pid OR post_id=$pid) AND course_id=$_SESSION[course_id]";
		$result = mysql_query($sql, $db);

	} else {   /* Just deleting a single thread */
	    /* Decrement count of comments in forums_threads table*/
		$sql	= "UPDATE ".TABLE_PREFIX."forums_threads SET num_comments=num_comments-1 WHERE post_id=$ppid AND course_id=$_SESSION[course_id]";
		$result = mysql_query($sql, $db);

		/* Decrement count of posts in forums table */
		$sql	= "UPDATE ".TABLE_PREFIX."forums SET num_posts=num_posts-1 WHERE forum_id=$fid";
		$result = mysql_query($sql, $db);

		$sql	= "DELETE FROM ".TABLE_PREFIX."forums_threads WHERE post_id=$pid AND course_id=$_SESSION[course_id]";
		$result = mysql_query($sql, $db);
	}

	$sql	= "DELETE FROM ".TABLE_PREFIX."forums_subscriptions WHERE post_id=$pid";
	$result = mysql_query($sql, $db);

	$sql	= "DELETE FROM ".TABLE_PREFIX."forums_accessed WHERE post_id=$pid";
	$result = mysql_query($sql, $db);

	if ($ppid) {
		header('Location: view.php?fid='.$fid.SEP.'pid='.$ppid.SEP.'f='.urlencode_feedback(AT_FEEDBACK_MESSAGE_DELETED));
		exit;
	} else {
		header('Location: index.php?fid='.$fid.SEP.'f='.urlencode_feedback(AT_FEEDBACK_THREAD_DELETED));
		exit;
	}
}

require(AT_INCLUDE_PATH.'header.inc.php');
	echo '<h2>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '<img src="images/icons/default/square-large-discussions.gif" width="42" height="38" border="0" alt="" class="menuimage" /> ';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo _AT('discussions');
	}
	echo '</h2>';

echo'<h3>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '<img src="images/icons/default/forum-large.gif" width="42" height="38" border="0" alt="" class="menuimageh3" />';
}
echo '<a href="forum/list.php">'._AT('forums').'</a>';

echo '</h3>';

echo'<h3>&nbsp;';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '<img src="images/icons/default/forum-large.gif" width="42" height="38" border="0" alt="" class="menuimageh3" />';
}
echo '<a href="forum/index.php?fid='.$fid.SEP.'g=11">'.AT_print(get_forum($fid), 'forums.title').'</a>';

echo '</h3>';



if($ppid=='' || $ppid =='0'){
	$warnings[]=AT_WARNING_DELETE_THREAD;
	$ppid = '0';
} else {
	$warnings[]=AT_WARNING_DELETE_MESSAGE;
}

print_warnings($warnings);

echo '<p><a href="'.$_SERVER['PHP_SELF'].'?fid='.$_GET['fid'].SEP.'pid='.$_GET['pid'].SEP.'ppid='.$_GET['ppid'].SEP.'d=1">'._AT('yes_delete').'</a>, <a href="forum/index.php?fid='.$_GET['fid'].SEP.'f='.urlencode_feedback(AT_FEEDBACK_CANCELLED).'">'._AT('no_cancel').'</a></p>';

require(AT_INCLUDE_PATH.'footer.inc.php');
?>