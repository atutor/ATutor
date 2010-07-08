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
// $Id: delete_post.php 8700 2009-07-14 20:13:28Z hwong $
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

	$sql = "DELETE FROM ".TABLE_PREFIX."blog_posts WHERE owner_type=".BLOGS_GROUP." AND owner_id=$_REQUEST[oid] AND post_id=$id";
	mysql_query($sql, $db);

	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');

	header('Location: view.php?ot='.BLOGS_GROUP.SEP.'oid='.$_POST['oid']);
	exit;
}

$id = abs($_REQUEST['id']);
$sql = "SELECT title, body FROM ".TABLE_PREFIX."blog_posts WHERE owner_type=".BLOGS_GROUP." AND owner_id=$owner_id AND post_id=$id";
$result = mysql_query($sql, $db);
$post_row = mysql_fetch_assoc($result);

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
$sql = 'SELECT title FROM '.TABLE_PREFIX.'blog_posts WHERE post_id='.$id;
$result = mysql_query($sql, $db);
$row = mysql_fetch_assoc($result);
$msg->addConfirm(array('DELETE', $row['title']), $hidden_vars);
$msg->printConfirm();
?>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>