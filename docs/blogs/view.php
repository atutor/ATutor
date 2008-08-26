<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$
define('AT_INCLUDE_PATH', '../include/');
require_once (AT_INCLUDE_PATH.'vitals.inc.php');

$owner_type = intval($_REQUEST['ot']);
$owner_id   = intval($_REQUEST['oid']);

$owner_arg_prefix = '?ot='.$owner_type.SEP.'oid='.$owner_id. SEP;
if (!($owner_status = blogs_authenticate($owner_type, $owner_id))) {
	$msg->addError('ACCESS_DENIED');
	header('Location: index.php');
	exit;
}

// these will all by dynamically defined on the view page
$_pages['blogs/view.php']['title'] = blogs_get_blog_name(BLOGS_GROUP, $_REQUEST['oid']);
$_pages['blogs/view.php']['parent']    = 'blogs/index.php';

if (query_bit($owner_status, BLOGS_AUTH_WRITE)) {
	$_pages['blogs/view.php']['children']  = array('blogs/add_post.php?ot='.BLOGS_GROUP.SEP.'oid='.$_REQUEST['oid']);
} else {
	$_pages['blogs/view.php']['children']  = array();
}

$_pages['blogs/add_post.php?ot='.BLOGS_GROUP.SEP.'oid='.$_REQUEST['oid']]['title_var'] = 'add';

$_pages['blogs/view.php?ot='.BLOGS_GROUP.SEP.'oid='.$_REQUEST['oid']]['title'] = blogs_get_blog_name(BLOGS_GROUP, $_REQUEST['oid']);
$_pages['blogs/view.php?ot='.BLOGS_GROUP.SEP.'oid='.$_REQUEST['oid']]['parent']    = 'blogs/index.php';
if (query_bit($owner_status, BLOGS_AUTH_WRITE)) {
	$_pages['blogs/view.php?ot='.BLOGS_GROUP.SEP.'oid='.$_REQUEST['oid']]['children'] = array('blogs/add_post.php');
} else {
	$_pages['blogs/view.php?ot='.BLOGS_GROUP.SEP.'oid='.$_REQUEST['oid']]['children'] = array();
}

require (AT_INCLUDE_PATH.'header.inc.php');

$auth = '';
if (!query_bit($owner_status, BLOGS_AUTH_WRITE)) {
	$auth = 'private=0 AND ';
}


if (isset($_GET['p'])) {
	$page = abs($_GET['p']);
} else {
	$page = 1;
}

$num_posts_per_page = 20;
$start = ($page - 1) * $num_posts_per_page;

$count = 0;

$sql = "SELECT post_id, member_id, private, num_comments, date, title, body FROM ".TABLE_PREFIX."blog_posts WHERE $auth owner_type=".BLOGS_GROUP." AND owner_id=$_REQUEST[oid] ORDER BY date DESC LIMIT $start, " . ($num_posts_per_page+1);
$result = mysql_query($sql, $db);
?>
<?php if (mysql_num_rows($result)): ?>
	<?php while (($row = mysql_fetch_assoc($result)) && ($count < $num_posts_per_page)): $count++; ?>
		<div class="entry">
			<h2><a href="<?php echo url_rewrite('blogs/post.php?ot='.BLOGS_GROUP.SEP.'oid='.$_REQUEST['oid'].SEP.'id='.$row['post_id']); ?>"><?php echo AT_PRINT($row['title'], 'blog_posts.title'); ?></a>
			<?php if ($row['private']): ?>
				- <?php echo _AT('private'); ?>
			<?php endif; ?></h2>
			<h3 class="date"><?php echo get_display_name($row['member_id']); ?> - <?php echo AT_date(_AT('forum_date_format'), at_timezone($row['date']), AT_DATE_MYSQL_DATETIME); ?></h3>

			<p><?php echo AT_PRINT($row['body'], 'blog_posts.body'); ?></p>

			<p><a href="<?php echo url_rewrite('blogs/post.php?ot='.BLOGS_GROUP.SEP.'oid='.$_REQUEST['oid'].SEP.'id='.$row['post_id']); ?>#comments"><?php echo _AT('comments_num', $row['num_comments']); ?></a></p>
			<hr />
		</div>
	<?php endwhile; ?>
	<?php
		if (mysql_num_rows($result) > $num_posts_per_page) {

			echo '<a href="'.url_rewrite('blogs/view.php?ot='.$owner_type.SEP.'oid='.$owner_id.SEP.'p='.(++$page)).'">'._AT('previous_posts').'</a>';
		}
	?>
<?php else: ?>
	<p><?php echo _AT('none_found'); ?></p>
<?php endif; ?>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>