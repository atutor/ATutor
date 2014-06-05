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
// $Id: index.php 2526 2004-11-25 18:54:16Z greg$

define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
include(AT_INCLUDE_PATH.'../mods/_standard/forums/lib/forums.inc.php');

$fid = intval($_REQUEST['fid']);

// check if they have access
if (!valid_forum_user($fid) || !$_SESSION['enroll']) {
	$msg->addError('FORUM_NOT_FOUND');
	header('Location: list.php');
	exit;
}

$sql = "SELECT title FROM %sforums WHERE forum_id=%d";
$row_forum = queryDB($sql, array(TABLE_PREFIX, $fid), TRUE);

if(count($row_forum) > 0){
	$forum_title = $row_forum['title'];
} else {
	$msg->addError('FORUM_NOT_FOUND');
	header('Location: list.php');
	exit;
}

if (isset($_GET['us'])) {
	$sql = "DELETE from %sforums_subscriptions WHERE forum_id = %d AND member_id = %d";
	$result = queryDB($sql, array(TABLE_PREFIX, $fid, $_SESSION['member_id']));
	$msg->addFeedback(array(FORUM_UNSUBSCRIBED, $forum_title));

} else {
	$sql = "INSERT into %sforums_subscriptions VALUES(%d, %d)";
	queryDB($sql, array(TABLE_PREFIX, $fid, $_SESSION['member_id']));
	$msg->addFeedback(array(FORUM_SUBSCRIBED,$forum_title));
}

header('Location: list.php');
exit;
?>