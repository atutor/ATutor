<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/

if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

define('BLOGS_GROUP', 1);

define('BLOGS_AUTH_NONE',  0);
define('BLOGS_AUTH_READ',  1);
define('BLOGS_AUTH_WRITE', 2); 
define('BLOGS_AUTH_RW',    3); // to save time

// if this module is to be made available to students on the Home or Main Navigation
$_group_tool = $_student_tool = 'mods/_standard/blogs/index.php';

$_pages['mods/_standard/blogs/index.php']['title_var'] = 'blogs';
$_pages['mods/_standard/blogs/index.php']['img']       = 'images/home-blogs.png';
$_pages['mods/_standard/blogs/index.php']['icon']      = 'images/home-blogs_sm.png';

// module sublinks
$this->_list['blogs'] = array('title_var'=>'blogs','file'=>'mods/_standard/blogs/sublinks.php');

if (isset($_REQUEST['oid'])) {
	$_pages['mods/_standard/blogs/edit_post.php?ot='.BLOGS_GROUP.SEP.'oid='.$_REQUEST['oid'].SEP.'id='.$_REQUEST['id']]['title_var'] = 'edit';
	$_pages['mods/_standard/blogs/edit_post.php']['title_var']   = 'edit';
	$_pages['mods/_standard/blogs/edit_post.php']['parent']      = 'mods/_standard/blogs/post.php?ot='.BLOGS_GROUP.SEP.'oid='.$_REQUEST['oid'].SEP.'id='.$_REQUEST['id'];

	$_pages['mods/_standard/blogs/delete_post.php?ot='.BLOGS_GROUP.SEP.'oid='.$_REQUEST['oid'].SEP.'id='.$_REQUEST['id']]['title_var'] = 'delete';
	$_pages['mods/_standard/blogs/delete_post.php']['title_var'] = 'delete';
}
$_pages['mods/_standard/blogs/delete_comment.php']['title_var'] = 'delete';


function blogs_get_group_url($group_id) {
	return 'mods/_standard/blogs/view.php?ot='.BLOGS_GROUP.SEP.'oid='.$group_id;
}

/**
 * given an owner_type and owner_id
 * returns false if user cannot read or write to this workspace
 * returns BLOGS_AUTH_READ if the user can read
 * returns BLOGS_AUTH_WRITE if the user can write
 */
function blogs_authenticate($owner_type, $owner_id) {
	// ensure that this group is in the course
	if ($owner_type == BLOGS_GROUP) {
		if (isset($_SESSION['groups'][$owner_id])) {
			return BLOGS_AUTH_RW;
		}

		global $db;
		$sql = "SELECT type_id FROM ".TABLE_PREFIX."groups WHERE group_id=$owner_id";
		$result = mysql_query($sql, $db);
		if (!$row = mysql_fetch_assoc($result)) {
			return BLOGS_AUTH_NONE;
		}

		$sql = "SELECT type_id FROM ".TABLE_PREFIX."groups_types WHERE type_id=$row[type_id] AND course_id=$_SESSION[course_id]";
		$result = mysql_query($sql, $db);
		if (!$row = mysql_fetch_assoc($result)) {
			return BLOGS_AUTH_NONE;
		}

		return BLOGS_AUTH_READ;
	}
	return BLOGS_AUTH_NONE;
}

function blogs_get_blog_name($owner_type, $owner_id) {
	if ($owner_type == BLOGS_GROUP) {
		// get group name
		global $db;

		$sql = "SELECT title FROM ".TABLE_PREFIX."groups WHERE group_id=$owner_id";
		$result = mysql_query($sql, $db);
		$row = mysql_fetch_assoc($result);

		return $row['title'];
	}
}
?>