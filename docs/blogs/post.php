<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: index.php 5824 2005-12-08 16:43:32Z joel $
define('AT_INCLUDE_PATH', '../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

// authenticate ot+oid..
$owner_type = abs($_REQUEST['ot']);
$owner_id = abs($_REQUEST['oid']);
if (!($owner_status = blogs_authenticate($owner_type, $owner_id))) {
	$msg->addError('ACCESS_DENIED');
	header('Location: index.php');
	exit;
}

$id = abs($_GET['id']);
$auth = '';
if (!query_bit($owner_status, BLOGS_AUTH_WRITE)) {
	$auth = 'private=0 AND ';
}
$sql = "SELECT member_id, private, date, title, body FROM ".TABLE_PREFIX."blog_posts WHERE $auth owner_type=".BLOGS_GROUP." AND owner_id=$_REQUEST[oid] AND post_id=$id ORDER BY date DESC";
$result = mysql_query($sql, $db);

if (!$post_row = mysql_fetch_assoc($result)) {
	header('Location: view.php?ot='.$owner_type.SEP.'oid='.$owner_id);
	exit;
}

$_pages['blogs/post.php']['title'] = $post_row['title'] . ($post_row['private'] ? ' - '._AT('private') : '');
$_pages['blogs/post.php']['parent']    = 'blogs/view.php?ot='.BLOGS_GROUP.SEP.'oid='.$_REQUEST['oid'];
if (query_bit($owner_status, BLOGS_AUTH_WRITE)) {
	$_pages['blogs/post.php']['children']  = array('blogs/edit_post.php?ot='.BLOGS_GROUP.SEP.'oid='.$_REQUEST['oid'].SEP.'id='.$_REQUEST['id'], 'blogs/delete_post.php?ot='.BLOGS_GROUP.SEP.'oid='.$_REQUEST['oid'].SEP.'id='.$_REQUEST['id']);
} else {
	$_pages['blogs/post.php']['children']  = array();
}

$_pages['blogs/view.php?ot='.BLOGS_GROUP.SEP.'oid='.$_REQUEST['oid']]['title'] = blogs_get_blog_name(BLOGS_GROUP, $_REQUEST['oid']);
$_pages['blogs/view.php?ot='.BLOGS_GROUP.SEP.'oid='.$_REQUEST['oid']]['parent']    = 'blogs/index.php';

if (query_bit($owner_status, BLOGS_AUTH_WRITE)) {
	$_pages['blogs/view.php?ot='.BLOGS_GROUP.SEP.'oid='.$_REQUEST['oid']]['children']  = array('blogs/add_post.php');
} else {
	$_pages['blogs/view.php?ot='.BLOGS_GROUP.SEP.'oid='.$_REQUEST['oid']]['children']  = array();
}


require (AT_INCLUDE_PATH.'header.inc.php');

?>
	<div class="entry">
		<h3 class="date"><?php echo get_login($post_row['member_id']); ?> - <?php echo AT_date(_AT('forum_date_format'), $post_row['date'], AT_DATE_MYSQL_DATETIME); ?></h3>

		<p><?php echo nl2br($post_row['body']); ?></p>
	</div>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>