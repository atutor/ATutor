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
// $Id$

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require_once(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

global $savant;
$msg =& new Message($savant);

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

$_section[0][0] = _AT('discussions');
$_section[0][1] = 'discussions/';
$_section[1][0] = _AT('forums');
$_section[1][1] = 'forum/list.php';
$_section[2][0] = AT_print(get_forum_name($fid), 'forums.title');
$_section[2][1] = 'forum/';


/* the last accessed field */
$last_accessed = array();
if ($_SESSION['valid_user']) {
	$sql	= "SELECT post_id, last_accessed, subscribe FROM ".TABLE_PREFIX."forums_accessed WHERE member_id=$_SESSION[member_id]";
	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_assoc($result)) {
		$post_id = $row['post_id'];
		unset($row['post_id']);
		$last_accessed[$post_id] = $row;
	}
}

if (authenticate(AT_PRIV_FORUMS, AT_PRIV_RETURN) && $_SESSION['prefs'][PREF_EDIT]) {
	$msg->addHelp('FORUM_STICKY');
	$msg->addHelp('FORUM_LOCK');
}
require(AT_INCLUDE_PATH.'header.inc.php');

echo '<h2>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '<img src="images/icons/default/square-large-discussions.gif" width="42" height="38" hspace="2" vspace="2" border="0" alt="" class="menuimage" /> ';
}

echo '<a href="discussions/index.php?g=11">'._AC('discussions').'</a>';
echo '</h2>';

echo'<h3>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '<img src="images/icons/default/forum-large.gif" width="42" height="38" border="0" alt="" class="menuimageh3" />';
}
echo '<a href="forum/list.php">'._AT('forums').'</a>';
echo ' - '.AT_print(get_forum_name($fid), 'forums.title');
echo '</h3>';

$msg->printAll();

require(AT_INCLUDE_PATH.'html/forum.inc.php');

require(AT_INCLUDE_PATH.'footer.inc.php');
?>