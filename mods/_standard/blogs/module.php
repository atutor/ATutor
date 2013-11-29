<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
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


if($_SESSION['is_admin'] > 0 || authenticate(AT_PRIV_GROUPS, TRUE)){	
	$this->_pages_i['mods/_core/groups/create.php']['title_var'] = 'create_groups';
	$this->_pages_i['mods/_core/groups/create.php']['parent']    = 'mods/_core/groups/index.php';
    $this->_pages_i['mods/_standard/blogs/index.php']['children']  = array('mods/_core/groups/create.php');
    $this->_pages['mods/_standard/blogs/index.php']['children']  = array();
}

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

		$sql = "SELECT type_id FROM %sgroups WHERE group_id=%d";
		$rows_groups = queryDB($sql, array(TABLE_PREFIX, $owner_id));
		
		if(count($rows_groups) == 0){
			return BLOGS_AUTH_NONE;
		}
        $sql = "SELECT type_id FROM %sgroups_types WHERE type_id=%d AND course_id=%d";
        $rows_types = queryDB($sql, array(TABLE_PREFIX, $row['type_id'], $_SESSION['course_id']));
        if(count($rows_types) == 0){
        
            return BLOGS_AUTH_NONE;
        }

		return BLOGS_AUTH_READ;
	}
	return BLOGS_AUTH_NONE;
}

function blogs_get_blog_name($owner_type, $owner_id) {
	if ($owner_type == BLOGS_GROUP) {
		// get group name
		$sql = "SELECT title FROM %sgroups WHERE group_id=%d";
		$row = queryDB($sql, array(TABLE_PREFIX, $owner_id), TRUE);
		
		return $row['title'];
	}
}
?>