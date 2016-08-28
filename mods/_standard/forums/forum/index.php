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

define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
$fid = intval($_GET['fid']);
tool_origin('off');
if (!isset($_GET['fid']) || !$fid) {
	header('Location: list.php');
	exit;
}
require(AT_INCLUDE_PATH.'../mods/_standard/forums/lib/forums.inc.php');

if (!valid_forum_user($fid)) {
	require(AT_INCLUDE_PATH.'header.inc.php');
	$msg->addError('FORUM_DENIED');
	$msg->printErrors();
	require(AT_INCLUDE_PATH.'footer.inc.php');
}

$_pages['mods/_standard/forums/forum/index.php']['title']    = get_forum_name($fid);
$_pages['mods/_standard/forums/forum/index.php']['parent']   = 'mods/_standard/forums/forum/list.php';
$_pages['mods/_standard/forums/forum/index.php']['children'] = array('mods/_standard/forums/forum/new_thread.php?fid='.$fid, 'search.php?search_within=forums');

$_pages['mods/_standard/forums/forum/new_thread.php?fid='.$fid]['title_var'] = 'new_thread';
$_pages['mods/_standard/forums/forum/new_thread.php?fid='.$fid]['parent']    = 'mods/_standard/forums/forum/index.php';

$_pages['search.php?search_within=forums']['title_var'] = 'search';
$_pages['search.php?search_within=forums']['parent']    = 'mods/_standard/forums/forum/index.php?fid='.$fid;



/* the last accessed field */
$last_accessed = array();
if ($_SESSION['valid_user'] === true && $_SESSION['enroll']) {

	$sql	= "SELECT post_id, last_accessed + 0 AS last_accessed, subscribe FROM %sforums_accessed WHERE member_id=%d";
	$rows_forums = queryDB($sql, array(TABLE_PREFIX, $_SESSION['member_id']));

    foreach($rows_forums as $row){
		$post_id = $row['post_id'];
		unset($row['post_id']);
		$last_accessed[$post_id] = $row;

	}
}

require(AT_INCLUDE_PATH . 'header.inc.php');

require(AT_INCLUDE_PATH . '../mods/_standard/forums/html/forum.inc.php');

require(AT_INCLUDE_PATH . 'footer.inc.php');
?>