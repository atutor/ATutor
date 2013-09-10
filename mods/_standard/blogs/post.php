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
require_once (AT_INCLUDE_PATH.'vitals.inc.php');

// authenticate ot+oid..
$owner_type = abs($_REQUEST['ot']);
$owner_id = abs($_REQUEST['oid']);
if (!($owner_status = blogs_authenticate($owner_type, $owner_id))) {
	$msg->addError('ACCESS_DENIED');
	header('Location: '.url_rewrite('mods/_standard/blogs/index.php'));
	exit;
}

$id = abs($_REQUEST['id']);
$auth = '';
if (!query_bit($owner_status, BLOGS_AUTH_WRITE)) {
	$auth = 'private=0 AND ';
}

$sql = "SELECT member_id, private, date, title, body FROM %sblog_posts WHERE $auth owner_type=%d AND owner_id=%d AND post_id=%d ORDER BY date DESC";
$post_row = queryDB($sql, array(TABLE_PREFIX, BLOGS_GROUP, $owner_id, $id), TRUE);


if (isset($_POST['submit']) && $_SESSION['member_id']) {
	// post a comment
	$_POST['body'] = $addslashes(trim($_POST['body']));
	$_POST['private'] = abs($_POST['private']);

	if ($_POST['body'] == '') {
		$msg->addError(array('EMPTY_FIELDS', _AT('comments')));
	}

	if (!$msg->containsErrors()) {

		$sql = "INSERT INTO %sblog_posts_comments VALUES (NULL, %d, %d, NOW(), %d, '%s')";
		$result = queryDB($sql, array(TABLE_PREFIX, $id, $_SESSION['member_id'], $_POST['private'], $_POST['body']));
		$comments_affected_rows = $result;
		
		if (!isset($sub)) { 
			require_once(AT_INCLUDE_PATH .'classes/subscribe.class.php');
			$sub = new subscription(); 
		}
		$sub->send_mail('blogcomment', $owner_id, at_insert_id());
		
		if ($comments_affected_rows == 1) {
			$sql = "UPDATE %sblog_posts SET num_comments=num_comments+1, date=date WHERE post_id=%d";
			queryDB($sql, array(TABLE_PREFIX, $id));
		}
		
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');

		header('Location: '.url_rewrite('mods/_standard/blogs/post.php?ot='.$owner_type.SEP.'oid='.$owner_id.SEP.'id='.$id, AT_PRETTY_URL_IS_HEADER));
		exit;
	}
}

if(count($post_row) == 0){
	header('Location: '.url_rewrite('mods/_standard/blogs/view.php?ot='.$owner_type.SEP.'oid='.$owner_id));
	exit;
}

$_pages['mods/_standard/blogs/post.php']['title'] = $post_row['title'] . ($post_row['private'] ? ' - '._AT('private') : '');
$_pages['mods/_standard/blogs/post.php']['parent']    = 'mods/_standard/blogs/view.php?ot='.BLOGS_GROUP.SEP.'oid='.$owner_id;
if (query_bit($owner_status, BLOGS_AUTH_WRITE)) {
	$_pages['mods/_standard/blogs/post.php']['children']  = array('mods/_standard/blogs/edit_post.php?ot='.BLOGS_GROUP.SEP.'oid='.$owner_id.SEP.'id='.$id, 'mods/_standard/blogs/delete_post.php?ot='.BLOGS_GROUP.SEP.'oid='.$owner_id.SEP.'id='.$id);
} else {
	$_pages['mods/_standard/blogs/post.php']['children']  = array();
}

$_pages['mods/_standard/blogs/view.php?ot='.BLOGS_GROUP.SEP.'oid='.$owner_id]['title'] = blogs_get_blog_name(BLOGS_GROUP, $owner_id);
$_pages['mods/_standard/blogs/view.php?ot='.BLOGS_GROUP.SEP.'oid='.$owner_id]['parent']    = 'mods/_standard/blogs/index.php';

if (query_bit($owner_status, BLOGS_AUTH_WRITE)) {
	$_pages['mods/_standard/blogs/view.php?ot='.BLOGS_GROUP.SEP.'oid='.$owner_id]['children']  = array('mods/_standard/blogs/add_post.php');
} else {
	$_pages['mods/_standard/blogs/view.php?ot='.BLOGS_GROUP.SEP.'oid='.$owner_id]['children']  = array();
}


require (AT_INCLUDE_PATH.'header.inc.php');

?>
	<div class="entry">
		<h3 class="date"><?php echo get_display_name($post_row['member_id']); ?> - <?php echo AT_date(_AT('forum_date_format'), $post_row['date'], AT_DATE_MYSQL_DATETIME); ?></h3>

		<p><?php echo AT_print($post_row['body'], 'blog_posts.body'); ?></p>
	</div>

<a name="comments"></a><h2><?php echo _AT('comments'); ?></h2>
<?php
	$sql = "SELECT comment_id, member_id, date, comment FROM %sblog_posts_comments WHERE post_id=%d ORDER BY date";
	$rows_comments = queryDB($sql, array(TABLE_PREFIX, $id));
?>
<?php 
foreach($rows_comments as $row){ ?>
	<div class="input-form">
		<div class="row">
			<h4 class="date"><?php echo get_display_name($row['member_id']); ?> - <?php echo AT_date(_AT('forum_date_format'), $row['date'], AT_DATE_MYSQL_DATETIME); ?></h4>

			<p><?php echo AT_print($row['comment'], 'blog_posts_comments.comment'); ?></p>

			<?php if (query_bit($owner_status, BLOGS_AUTH_WRITE)): ?>
				<div style="text-align: right; font-size: smaller;">
					<a href="mods/_standard/blogs/delete_comment.php?ot=<?php echo $owner_type.SEP.'oid='.$owner_id.SEP.'id='.$id.SEP.'delete_id='.$row['comment_id']; ?>"><?php echo _AT('delete'); ?></a>
				</div>
			<?php endif; ?>
		</div>
	</div>

<?php } ?>

<?php if ($_SESSION['member_id']): ?>
	<form method="post" action="<?php echo $_SERVER['PHP_SELF'].'?ot='.$owner_type.SEP.'oid='.$owner_id; ?>" name="form">
	<input type="hidden" name="id" value="<?php echo $id; ?>" />
	<div class="input-form">
		<div class="row">
			<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="commentsarea"><?php echo _AT('comments'); ?></label><br />
			<textarea name="body" id="commentsarea" cols="40" rows="3"></textarea>
		</div>

		<div class="row">	
			<a href="<?php echo htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES); ?>#jumpcodes" title="<?php echo _AT('jump_codes'); ?>"><img src="images/clr.gif" height="1" width="1" alt="<?php echo _AT('jump_codes'); ?>" border="0" /></a><?php require(AT_INCLUDE_PATH.'html/code_picker.inc.php'); ?>

			<a name="jumpcodes"></a>
		</div>

		<div class="row buttons">
			<input type="submit" name="submit" value="<?php echo _AT('post'); ?>" accesskey="s" />
		</div>
	</div>
	</form>
<?php endif; ?>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>