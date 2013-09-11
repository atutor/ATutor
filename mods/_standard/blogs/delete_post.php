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
// $Id$
define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

// authenticate ot+oid ....
$owner_type = abs($_REQUEST['ot']);
$owner_id = abs($_REQUEST['oid']);
if (!($owner_status = blogs_authenticate($owner_type, $owner_id)) || !query_bit($owner_status, BLOGS_AUTH_WRITE)) {
	$msg->addError('ACCESS_DENIED');
	header('Location: index.php');
	exit;
}

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	$id = abs($_POST['id']);
	header('Location: post.php?ot='.BLOGS_GROUP.SEP.'oid='.$_POST['oid'].SEP.'id='.$id);
	exit;
} else if (isset($_POST['submit_yes'])) {
	$id = abs($_POST['id']);

	$sql = "DELETE FROM %sblog_posts WHERE owner_type=%d AND owner_id=%d AND post_id=%d";
	queryDB($sql, array(TABLE_PREFIX, BLOGS_GROUP, $_REQUEST['oid'], $id));
	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');

	header('Location: view.php?ot='.BLOGS_GROUP.SEP.'oid='.$_POST['oid']);
	exit;
}

$id = abs($_REQUEST['id']);
$sql = "SELECT title, body FROM %sblog_posts WHERE owner_type=%d AND owner_id=%d AND post_id=%d";
$post_row = queryDB($sql, array(TABLE_PREFIX, BLOGS_GROUP, $owner_id, $id), TRUE);


$_pages['mods/_standard/blogs/delete_post.php']['parent']    = 'mods/_standard/blogs/post.php?ot='.BLOGS_GROUP.SEP.'oid='.$_REQUEST['oid'].SEP.'id='.$_REQUEST['id'];


$_pages['mods/_standard/blogs/post.php?ot='.BLOGS_GROUP.SEP.'oid='.$_REQUEST['oid'].SEP.'id='.$_REQUEST['id']]['children'] = array('mods/_standard/blogs/edit_post.php?ot='.BLOGS_GROUP.SEP.'oid='.$_REQUEST['oid'].SEP.'id='.$_REQUEST['id'], 'mods/_standard/blogs/delete_post.php');
$_pages['mods/_standard/blogs/post.php?ot='.BLOGS_GROUP.SEP.'oid='.$_REQUEST['oid'].SEP.'id='.$_REQUEST['id']]['parent'] = 'mods/_standard/blogs/view.php?ot='.BLOGS_GROUP.SEP.'oid='.$_REQUEST['oid'];
$_pages['mods/_standard/blogs/post.php?ot='.BLOGS_GROUP.SEP.'oid='.$_REQUEST['oid'].SEP.'id='.$_REQUEST['id']]['title'] = $post_row['title'];


$_pages['mods/_standard/blogs/view.php?ot='.BLOGS_GROUP.SEP.'oid='.$_REQUEST['oid']]['title'] = blogs_get_blog_name(BLOGS_GROUP, $_REQUEST['oid']);
$_pages['mods/_standard/blogs/view.php?ot='.BLOGS_GROUP.SEP.'oid='.$_REQUEST['oid']]['parent']    = 'mods/_standard/blogs/index.php';
$_pages['mods/_standard/blogs/view.php?ot='.BLOGS_GROUP.SEP.'oid='.$_REQUEST['oid']]['children']  = array('mods/_standard/blogs/add_post.php');

require (AT_INCLUDE_PATH.'header.inc.php');

$hidden_vars = array('id' => $id, 'ot' => $_REQUEST['ot'], 'oid' => $_REQUEST['oid']);
//get the post title to print into the confirm box
$sql = 'SELECT title FROM %sblog_posts WHERE post_id=%d';
$row = queryDB($sql, array(TABLE_PREFIX, $id), TRUE);

$msg->addConfirm(array('DELETE', $row['title']), $hidden_vars);
$msg->printConfirm();
?>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>