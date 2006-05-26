<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

define('BLOGS_GROUP', 1);

define('BLOGS_AUTH_NONE',  0);
define('BLOGS_AUTH_READ',  1);
define('BLOGS_AUTH_WRITE', 2); 
define('BLOGS_AUTH_RW',    3); // to save time


// if this module is to be made available to students on the Home or Main Navigation
$_group_tool = $_student_tool = 'blogs/index.php';

$_pages['blogs/index.php']['title_var'] = 'blogs';
$_pages['blogs/index.php']['img']       = 'images/home-blogs.gif';

$_pages['blogs/edit_post.php?ot='.BLOGS_GROUP.SEP.'oid='.$_REQUEST['oid'].SEP.'id='.$_REQUEST['id']]['title_var'] = 'edit';

$_pages['blogs/edit_post.php']['title_var']   = 'edit';
$_pages['blogs/edit_post.php']['parent']      = 'blogs/post.php?ot='.BLOGS_GROUP.SEP.'oid='.$_REQUEST['oid'].SEP.'id='.$_REQUEST['id'];

$_pages['blogs/delete_post.php?ot='.BLOGS_GROUP.SEP.'oid='.$_REQUEST['oid'].SEP.'id='.$_REQUEST['id']]['title_var'] = 'delete';
$_pages['blogs/delete_post.php']['title_var'] = 'delete';

$_pages['blogs/delete_comment.php']['title_var'] = 'delete_comment';


function blogs_get_group_url($group_id) {
	return 'blogs/view.php?ot='.BLOGS_GROUP.SEP.'oid='.$group_id;
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