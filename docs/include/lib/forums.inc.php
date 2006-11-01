<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay, Joel Kronenberg & Heidi Hazelton	*/
/* Adaptive Technology Resource Centre / University of Toronto				*/
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/
// $Id$

if (!defined('AT_INCLUDE_PATH')) { exit; }

/**
* Returns an array of (shared and non-shared) forums belonging to the given course
* @access  public
* @param   integer $course		id of the course
* @return  string array			each row is a forum 
* @see     $db					in include/vitals.inc.php
* @see     is_shared_forum()
* @author  Heidi Hazelton
* @author  Joel Kronenberg
*/
function get_forums($course) {
	global $db;

	if ($course) {
		$sql	= "SELECT F.* FROM ".TABLE_PREFIX."forums_courses FC INNER JOIN ".TABLE_PREFIX."forums F USING (forum_id) WHERE FC.course_id=$course GROUP BY FC.forum_id ORDER BY F.title";
	} else {
		$sql	= "SELECT F.*, FC.course_id FROM ".TABLE_PREFIX."forums_courses FC INNER JOIN ".TABLE_PREFIX."forums F USING (forum_id) GROUP BY FC.forum_id ORDER BY F.title";
	}

	// 'nonshared' forums are always listed first:
	$forums['nonshared'] = array();
	$forums['shared']    = array();
	$forums['group']     = array();

	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_assoc($result)) {
		// for each forum, check if it's shared or not:

		if (is_shared_forum($row['forum_id'])) {
			$forums['shared'][] = $row;
		} else {
			$forums['nonshared'][] = $row;
		}
	}
		
	// retrieve the group forums:

	if (!$_SESSION['groups']) {
		return $forums;
	}

	$groups =  implode(',',$_SESSION['groups']);

	$sql = "SELECT F.*, G.group_id FROM ".TABLE_PREFIX."forums_groups G INNER JOIN ".TABLE_PREFIX."forums F USING (forum_id) WHERE G.group_id IN ($groups) ORDER BY F.title";
	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_assoc($result)) {
		$row['title'] = get_group_title($row['group_id']);
		$forums['group'][] = $row;
	}

	return $forums;	
}

/**
* Returns true/false whether or not this forum is shared.
* @access  public
* @param   integer $forum_id	id of the forum
* @return  boolean				true if this forum is shared, false otherwise
* @see     $db					in include/vitals.inc.php
* @author  Joel Kronenberg
*/
function is_shared_forum($forum_id) {
	global $db;

	$sql = "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."forums_courses WHERE forum_id=$forum_id";
	$result = mysql_query($sql, $db);
	$row = mysql_fetch_assoc($result);

	if ($row['cnt'] > 1) {
		return TRUE;
	} // else:
	
	return FALSE;
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

	$sql	= "SELECT forum_id FROM ".TABLE_PREFIX."forums_courses WHERE (course_id=$_SESSION[course_id] OR course_id=0) AND forum_id=$forum_id";
	$result = mysql_query($sql, $db);
	$row = mysql_fetch_assoc($result);

	if (empty($row)) {
		// not a course forum, let's check group:
		$groups = implode(',', $_SESSION['groups']);
		$sql	= "SELECT forum_id FROM ".TABLE_PREFIX."forums_groups WHERE group_id IN ($groups) AND forum_id=$forum_id";
		$result = mysql_query($sql, $db);
		if ($row = mysql_fetch_assoc($result)) {
			return TRUE;
		}

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

	$sql	= "INSERT INTO ".TABLE_PREFIX."forums VALUES (NULL,'$_POST[title]', '$_POST[body]', 0, 0, NOW())";
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

	$_POST['fid']    = intval($_POST['fid']);

	$sql	= "UPDATE ".TABLE_PREFIX."forums SET title='$_POST[title]', description='$_POST[body]', last_post=last_post WHERE forum_id=$_POST[fid]";
	$result = mysql_query($sql,$db);

	return;
}

/**
* Deletes a forum (checks if its shared).
* Assumes the forum is not shared.
* Assumes the user has the priv to delete this forum.
* @access  public
* @param   array $_POST			add-forum form variables
* @see     $db					in include/vitals.inc.php
* @see     $addslashes			in include/vitals.inc.php
* @author  Heidi Hazelton
*/
function delete_forum($forum_id) {
	global $db;

	$sql	= "SELECT post_id FROM ".TABLE_PREFIX."forums_threads WHERE forum_id=$forum_id";
	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_array($result)) {
		$sql	 = "DELETE FROM ".TABLE_PREFIX."forums_accessed WHERE post_id=$row[post_id]";
		$result2 = mysql_query($sql, $db);
	}

	$sql	= "DELETE FROM ".TABLE_PREFIX."forums_subscriptions WHERE forum_id=$forum_id";
	$result = mysql_query($sql, $db);

	$sql    = "DELETE FROM ".TABLE_PREFIX."forums_threads WHERE forum_id=$forum_id";
	$result = mysql_query($sql, $db);

	$sql = "DELETE FROM ".TABLE_PREFIX."forums_courses WHERE forum_id=$forum_id";
	$result = mysql_query($sql, $db);

	$sql    = "DELETE FROM ".TABLE_PREFIX."forums WHERE forum_id=$forum_id";
	$result = mysql_query($sql, $db);
	
	$sql = "OPTIMIZE TABLE ".TABLE_PREFIX."forums_threads";
	$result = mysql_query($sql, $db);

}

?>