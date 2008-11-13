<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton	*/
/* Adaptive Technology Resource Centre / University of Toronto				*/
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
$fid = intval($_GET['fid']);

if (!isset($_GET['fid']) || !$fid) {
	header('Location: list.php');
	exit;
}
require(AT_INCLUDE_PATH.'lib/forums.inc.php');

if (!valid_forum_user($fid)) {
	require(AT_INCLUDE_PATH.'header.inc.php');
	$msg->addError('FORUM_DENIED');
	$msg->printErrors();
	require(AT_INCLUDE_PATH.'footer.inc.php');
}

$_pages['forum/index.php']['title']    = get_forum_name($fid);
$_pages['forum/index.php']['parent']   = 'forum/list.php';
$_pages['forum/index.php']['children'] = array('forum/new_thread.php?fid='.$fid, 'search.php?search_within[]=forums');

$_pages['forum/new_thread.php?fid='.$fid]['title_var'] = 'new_thread';
$_pages['forum/new_thread.php?fid='.$fid]['parent']    = 'forum/index.php';

$_pages['search.php?search_within[]=forums']['title_var'] = 'search';
$_pages['search.php?search_within[]=forums']['parent']    = 'forum/index.php';

/* the last accessed field */
$last_accessed = array();
if ($_SESSION['valid_user'] && $_SESSION['enroll']) {
	$sql	= "SELECT post_id, last_accessed + 0 AS last_accessed, subscribe FROM ".TABLE_PREFIX."forums_accessed WHERE member_id=$_SESSION[member_id]";
	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_assoc($result)) {
		$post_id = $row['post_id'];
		unset($row['post_id']);
		$last_accessed[$post_id] = $row;

	}
}

require(AT_INCLUDE_PATH . 'header.inc.php');

require(AT_INCLUDE_PATH . 'html/forum.inc.php');

require(AT_INCLUDE_PATH . 'footer.inc.php');
?>