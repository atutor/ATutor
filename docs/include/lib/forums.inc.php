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

/**
* Returns an array of forums belonging to the given course
* @access  public
* @param   integer $course		id of the course
* @return  string array			each row is a forum 
* @see     $db					in include/vitals.inc.php
* @author  Heidi Hazelton
*/
function get_forums($course) {
	global $db;

	$sql	= "SELECT * FROM ".TABLE_PREFIX."forums_courses fc, ".TABLE_PREFIX."forums f WHERE (fc.course_id=$course OR fc.course_id=0) AND fc.forum_id=f.forum_id ORDER BY title";
	$result = mysql_query($sql, $db);

	while ($row = mysql_fetch_assoc($result)) {
		$forums[] = $row;
	}
	
	return $forums;	
}

/**
* Returns forum information for given forum_id 
* @access  public
* @param   integer $forum_id	id of the forum
* @param   integer $course		id of the course (for non-admins)
* @return  string array			each row is a forum 
* @see     $db					in include/vitals.inc.php
* @author  Heidi Hazelton
*/
function get_forum($forum_id, $course = '') {
	global $db;

	if (!empty($course)) {
		$sql	= "SELECT * FROM ".TABLE_PREFIX."forums_courses fc, ".TABLE_PREFIX."forums f WHERE (fc.course_id=$course OR fc.course_id=0) AND fc.forum_id=f.forum_id and fc.forum_id=$forum_id ORDER BY title";
		$result = mysql_query($sql, $db);
		$forum = mysql_fetch_assoc($result);
	} else if (empty($course)) {  	//only admins should be retrieving forums w/o a course!  add this check
		$sql = "SELECT * FROM ".TABLE_PREFIX."forums WHERE forum_id=$forum_id";
		$result = mysql_query($sql, $db);
		$forum = mysql_fetch_assoc($result);
	} else {
		return;
	}

	return $forum;	
}

/**
* Checks to see if signed in member is allowed to view the forum page
* @access  public
* @param   integer $forum_id	id of the forum
* @return  boolean				view (true) or not view (false)
* @see     $db					in include/vitals.inc.php
* @author  Heidi Hazelton
*/
function valid_forum_user($forum_id) {
	global $db;

	$sql	= "SELECT * FROM ".TABLE_PREFIX."forums_courses WHERE (course_id=$_SESSION[course_id] OR course_id=0) AND forum_id=$forum_id";
	$result = mysql_query($sql, $db);
	$row = mysql_fetch_assoc($result);

	if (empty($row)) {
		return FALSE;
	}

	return TRUE;	
}

/**
* Adds a forum
* @access  public
* @param   array $_POST			add-forum form variables
* @see     $db					in include/vitals.inc.php
* @see     $addslashes			in include/vitals.inc.php
* @author  Heidi Hazelton
*/
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

/**
* Edits a forum
* @access  public
* @param   array $_POST			add-forum form variables
* @see     $db					in include/vitals.inc.php
* @see     $addslashes			in include/vitals.inc.php
* @author  Heidi Hazelton
*/
function edit_forum($_POST) {
	global $db;
	global $addslashes;

	$_POST['title']  = $addslashes($_POST['title']);
	$_POST['body']   = $addslashes($_POST['body']);

	$sql	= "UPDATE ".TABLE_PREFIX."forums SET title='$_POST[title]', description='$_POST[body]' WHERE forum_id=$_POST[fid]";
	$result = mysql_query($sql,$db);

	return;
}

/**
* Deletes a forum (checks if its shared)
* @access  public
* @param   array $_POST			add-forum form variables
* @see     $db					in include/vitals.inc.php
* @see     $addslashes			in include/vitals.inc.php
* @author  Heidi Hazelton
*/
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

	return;
}

?>