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

if (!defined('AT_INCLUDE_PATH')) { exit; }


function get_forums($course) {
	global $db;

	$sql	= "SELECT * FROM ".TABLE_PREFIX."forums_courses fc, ".TABLE_PREFIX."forums f WHERE fc.course_id=$course AND fc.forum_id=f.forum_id ORDER BY title";
	$result = mysql_query($sql, $db);

	while ($row = mysql_fetch_assoc($result)) {
		$forums[] = $row;
	}
	
	return $forums;	
}

function get_forum($forum_id) {
	global $db;

	$sql	= "SELECT * FROM ".TABLE_PREFIX."forums_courses fc, ".TABLE_PREFIX."forums f WHERE fc.course_id=$_SESSION[course_id] AND fc.forum_id=f.forum_id and fc.forum_id=$forum_id ORDER BY title";
	$result = mysql_query($sql, $db);
	$forum = mysql_fetch_assoc($result);

	return $forum;	
}

function valid_forum_user($forum_id) {
	global $db;

	$sql	= "SELECT * FROM ".TABLE_PREFIX."forums_courses WHERE course_id=$_SESSION[course_id] AND forum_id=$forum_id";
	$result = mysql_query($sql, $db);
	$row = mysql_fetch_assoc($result);

	if (empty($row)) {
		return FALSE;
	}

	return TRUE;	
}

function add_forum($_POST) {
	global $db;
	global $addslashes;

	$_POST['title'] = $addslashes($_POST['title']);
	$_POST['body']  = $addslashes($_POST['body']);

	$sql	= "INSERT INTO ".TABLE_PREFIX."forums VALUES (0,'$_POST[title]', '$_POST[body]', 0, 0, NOW())";
	$result = mysql_query($sql,$db);

	$sql	= "INSERT INTO ".TABLE_PREFIX."forums_courses VALUES (LAST_INSERT_ID(),  $_SESSION[course_id])";
	$result = mysql_query($sql,$db);

	return;
}

function edit_forum($_POST) {
	global $db;
	global $addslashes;

	$_POST['title']  = $addslashes($_POST['title']);
	$_POST['body']  = $addslashes($_POST['body']);

	$sql	= "UPDATE ".TABLE_PREFIX."forums SET title='$_POST[title]', description='$_POST[body]' WHERE forum_id=$_POST[fid]";
	$result = mysql_query($sql,$db);
}

function delete_forum($forum_id) {
	global $db;

	$sql = "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."forums_courses WHERE forum_id=$forum_id";
	$result = mysql_query($sql, $db);
	$row = mysql_fetch_assoc($result);
	if ($row['cnt'] == 1) {
		$sql	= "SELECT post_id FROM ".TABLE_PREFIX."forums_threads WHERE forum_id=$forum_id";
		$result = mysql_query($sql, $db);
		while ($row = mysql_fetch_array($result)) {
			$sql	 = "DELETE FROM ".TABLE_PREFIX."forums_accessed WHERE post_id=$row[post_id]";
			$result2 = mysql_query($sql, $db);

			$sql	 = "DELETE FROM ".TABLE_PREFIX."forums_subscriptions WHERE post_id=$row[post_id]";
			$result2 = mysql_query($sql, $db);
		}

		$sql = "DELETE FROM ".TABLE_PREFIX."forums_threads WHERE forum_id=$forum_id";
		$result = mysql_query($sql, $db);

		$sql = "DELETE FROM ".TABLE_PREFIX."forums WHERE forum_id=$forum_id";
		$result = mysql_query($sql, $db);
		
		$sql = "OPTIMIZE TABLE ".TABLE_PREFIX."forums_threads";
		$result = mysql_query($sql, $db);

		$sql = "DELETE FROM ".TABLE_PREFIX."forums_courses WHERE forum_id=$forum_id AND course_id=$_SESSION[course_id]";
		$result = mysql_query($sql, $db);
	} else if ($row['cnt'] > 1) {
		$sql = "DELETE FROM ".TABLE_PREFIX."forums_courses WHERE forum_id=$forum_id AND course_id=$_SESSION[course_id]";
		$result = mysql_query($sql, $db);
	}

}

?>