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
require(AT_INCLUDE_PATH.'../mods/_standard/forums/lib/forums.inc.php');

$_section[0][0] = _AT('discussions');
$_section[0][1] = 'discussions/';

$pid = intval($_GET['pid']);
$fid = intval($_GET['fid']);

// check if they have access
if (!valid_forum_user($fid) || !$_SESSION['enroll']) {
	$msg->addError('FORUM_NOT_FOUND');
	header('Location: list.php');
	exit;
}

$sql = "SELECT subject FROM ".TABLE_PREFIX."forums_threads WHERE post_id=$pid AND forum_id=$fid";
$result = mysql_query($sql, $db);
if (!($row = mysql_fetch_assoc($result))) {
	$msg->addError('FORUM_NOT_FOUND');
	header('Location: list.php');
	exit;
} // else:
$thread_name = $row['subject'];

/**
 * Protect against url injection
 * Maintain consistency in data by not allowing any subscription to a reply thread, only top level id's (0).
 */
 $sql = "SELECT parent_id FROM " . TABLE_PREFIX."forums_threads WHERE post_id=$pid AND forum_id=$fid";
 $result = mysql_query($sql, $db);
 if ($row = mysql_fetch_assoc($result)) {
 	if ($row['parent_id'] > 0) { // not allowed, only top level
 		$msg->addError('FORUM_NO_SUBSCRIBE');
 		header('Location: view.php?fid='.$fid.SEP.'pid='.$row['parent_id']); // take us back to where we were
 		exit;
 	}
 }
 
if ($_GET['us']) {
	// unsubscribe:
	$sql	= "UPDATE ".TABLE_PREFIX."forums_accessed SET subscribe=0 WHERE post_id=$pid AND member_id=$_SESSION[member_id]";
	$result = mysql_query($sql, $db);
} else {
	// subscribe:
	$sql	= "REPLACE INTO ".TABLE_PREFIX."forums_accessed VALUES ($pid, $_SESSION[member_id], NOW(), 1)";
	$result = mysql_query($sql, $db);
}


if($_REQUEST['t']){
	$this_pid = 'index.php?fid='.$fid;
} else{
	$this_pid = 'view.php?fid='.$fid.SEP.'pid='.$pid;
}

if ($_GET['us'] == '1') {
	$msg->addFeedback(array('THREAD_UNSUBSCRIBED', $thread_name));
	header('Location: '.AT_BASE_HREF.'mods/_standard/forums/forum/'.$this_pid);
	exit;
}

/* else: */
	$msg->addFeedback(array('THREAD_SUBSCRIBED', $thread_name ));
	header('Location: '.AT_BASE_HREF.'mods/_standard/forums/forum/'.$this_pid);
	exit;

?>