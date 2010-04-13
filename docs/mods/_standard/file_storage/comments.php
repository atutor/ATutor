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
// $Id: comments.php 8901 2009-11-11 19:10:19Z cindy $

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'../mods/_standard/file_storage/file_storage.inc.php');

$owner_type = abs($_REQUEST['ot']);
$owner_id   = abs($_REQUEST['oid']);
$owner_arg_prefix = '?ot='.$owner_type.SEP.'oid='.$owner_id. SEP;
if (!fs_authenticate($owner_type, $owner_id)) { 
	$msg->addError('ACCESS_DENIED');
	header('Location: '.url_rewrite('mods/_standard/file_storage/index.php', AT_PRETTY_URL_IS_HEADER));
	exit;
}

if (isset($_GET['done'])) {
	header('Location: '.url_rewrite('mods/_standard/file_storage/index.php'.$owner_arg_prefix.'folder='.abs($_GET['folder']), AT_PRETTY_URL_IS_HEADER));
	exit;
} else if (isset($_GET['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.url_rewrite('mods/_standard/file_storage/index.php'.$owner_arg_prefix.'folder='.abs($_GET['folder']), AT_PRETTY_URL_IS_HEADER));
	exit;
} else if (isset($_POST['edit_cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.url_rewrite('mods/_standard/file_storage/comments.php'.$owner_arg_prefix.'id='.$_GET['id'], AT_PRETTY_URL_IS_HEADER));
	exit;
} else if (isset($_POST['edit_submit'])) {
	$_POST['comment'] = trim($_POST['comment']);
	$_POST['comment_id'] = abs($_POST['comment_id']);

	if (!$_POST['edit_comment']) {
		$msg->addError(array('EMPTY_FIELDS', _AT('comments')));
	}

	if (!$msg->containsErrors()) {
		$_POST['edit_comment'] = $addslashes($_POST['edit_comment']);

		$sql = "UPDATE ".TABLE_PREFIX."files_comments SET comment='$_POST[edit_comment]', date=date WHERE member_id=$_SESSION[member_id] AND comment_id=$_POST[comment_id]";
		mysql_query($sql, $db);
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		header('Location: '.url_rewrite('mods/_standard/file_storage/comments.php'.$owner_arg_prefix.'id='.$_GET['id'], AT_PRETTY_URL_IS_HEADER));
		exit;
	}
} else if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.url_rewrite('mods/_standard/file_storage/index.php'.$owner_arg_prefix.'folder='.$_POST['folder'], AT_PRETTY_URL_IS_HEADER));
	exit;
} else if (isset($_POST['submit'])) {
	$_POST['comment'] = trim($_POST['comment']);
	$_POST['id'] = abs($_POST['id']);

	if (!$_POST['comment']) {
		$msg->addError(array('EMPTY_FIELDS', _AT('comments')));
	}

	if (!$msg->containsErrors()) {
		$_POST['comment'] = $addslashes($_POST['comment']);

		$sql = "INSERT INTO ".TABLE_PREFIX."files_comments VALUES (NULL, $_POST[id], $_SESSION[member_id], NOW(), '$_POST[comment]')";
		if (mysql_query($sql, $db)) {
			$sql = "UPDATE ".TABLE_PREFIX."files SET num_comments=num_comments+1, date=date WHERE file_id=$_POST[id]";
			mysql_query($sql, $db);
		}

		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		header('Location: '.url_rewrite('mods/_standard/file_storage/comments.php'.$owner_arg_prefix.'id='.$_POST['id'], AT_PRETTY_URL_IS_HEADER));
		exit;
	}
	$_GET['id'] = $_POST['id'];
}

if (isset($_GET['comment_id'])) {
	$onload = 'document.form.edit_comment.focus();';
}

require(AT_INCLUDE_PATH.'header.inc.php');

$id = abs($_GET['id']);

$files = fs_get_revisions($id, $owner_type, $owner_id);
if (!$files) {
	$msg->printErrors('FILE_NOT_FOUND');
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}
?>

<?php if ($_config['fs_versioning']): ?>
	<form method="get" action="<?php echo 'mods/_standard/file_storage/comments.php'
	//@harris echo $_SERVER['PHP_SELF']; ?>">
	<input type="hidden" name="ot" value="<?php echo $owner_type; ?>" />
	<input type="hidden" name="oid" value="<?php echo $owner_id; ?>" />
	<div class="input-form" style="width: 95%">
		<div class="row">
			<select name="id" size="<?php echo min(count($files), 5);?>">
				<?php foreach ($files as $file): ?>
					<?php
						$selected = '';
						if ($file['file_id'] == $id) {
							$current_file = $file;
							$selected = ' selected="selected"';
						}
					?>
					<option value="<?php echo $file['file_id'];?>" <?php echo $selected; ?>><?php echo _AT('revision'); ?> <?php echo $file['num_revisions']; ?>. <?php echo $file['file_name']; ?> - <?php echo $file['num_comments']; ?> <?php echo _AT('comments'); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="row buttons">
			<input type="submit" name="comments" value="<?php echo _AT('comments'); ?>" />
			<input type="submit" name="done" value="<?php echo _AT('done'); ?>" />
		</div>
	</div>
	<input type="hidden" name="folder" value="<?php echo $current_file['folder_id']; ?>" />
	</form>
<?php else: ?>
	<?php $current_file = current($files); ?>
<?php endif; ?>

<div class="input-form">
	<div class="row">
		<h3><?php echo $current_file['file_name']; ?> <small> - <?php echo _AT('revision'); ?> <?php echo $current_file['num_revisions']; ?></small></h3>
		<span style="font-size: small"><?php echo get_display_name($current_file['member_id']); ?> - <?php echo AT_date(_AT('filemanager_date_format'), $current_file['date'], AT_DATE_MYSQL_DATETIME); ?></span>
		<p><?php echo nl2br(htmlspecialchars($current_file['description'])); ?></p>
	</div>
</div>

<?php
$_GET['comment_id'] = isset($_GET['comment_id']) ? intval($_GET['comment_id']) : 0;
	$sql = "SELECT * FROM ".TABLE_PREFIX."files_comments WHERE file_id=$id ORDER BY date ASC";
	$result = mysql_query($sql, $db);
if ($row = mysql_fetch_assoc($result)): ?>
	<?php do { ?>
		<div class="input-form">
			<?php if (($row['member_id'] == $_SESSION['member_id']) && ($row['comment_id'] == $_GET['comment_id'])): ?>
				<form method="post" action="mods/_standard/file_storage/comments.php<?php echo $owner_arg_prefix.'id='.$id;?>" name="form">
				<input type="hidden" name="comment_id" value="<?php echo $row['comment_id']; ?>" />
				<div class="row">
					<a name="c<?php echo $row['comment_id']; ?>"></a><h4><?php echo get_display_name($row['member_id']); ?> - <?php echo AT_DATE(_AT('server_date_format'), $row['date'], AT_DATE_MYSQL_DATETIME); ?></h4>
					<textarea rows="4" cols="40" name="edit_comment"><?php echo htmlspecialchars($row['comment']); ?></textarea>
				</div>
				<div class="row buttons">
					<input type="submit" name="edit_submit" value="<?php echo _AT('save'); ?>" />
					<input type="submit" name="edit_cancel" value="<?php echo _AT('cancel'); ?>" />
				</div>
				</form>
						
			<?php else: ?>
				<div class="row">
					<h4><?php echo get_display_name($row['member_id']); ?> - <?php echo AT_date(_AT('filemanager_date_format'), $row['date'], AT_DATE_MYSQL_DATETIME); ?></h4>
					<p><?php echo nl2br(htmlspecialchars($row['comment'])); ?></p>
						<?php if ($row['member_id'] == $_SESSION['member_id'] || $current_file['member_id'] == $_SESSION['member_id']): ?>
							<div style="text-align:right; font-size: smaller">
								<a href="<?php echo url_rewrite('mods/_standard/file_storage/comments.php'.$owner_arg_prefix.'id='.$id.SEP.'comment_id='.$row['comment_id'].'#c'.$row['comment_id']); ?>"><?php echo _AT('edit'); ?></a> | <a href="mods/_standard/file_storage/delete_comment.php<?php echo $owner_arg_prefix . 'file_id='.$id.SEP; ?>id=<?php echo $row['comment_id']; ?>"><?php echo _AT('delete'); ?></a>
							</div>
						<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	<?php } while ($row = mysql_fetch_assoc($result)); ?>
<?php elseif(0): ?>
	<div class="input-form">
		<div class="row">
			<p><?php echo _AT('none_found'); ?></p>
		</div>
	</div>
<?php endif; ?>

<?php if ($_SESSION['is_guest'] == 0): ?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF'].$owner_arg_prefix; ?>id=<?php echo $id; ?>">
<input type="hidden" name="id" value="<?php echo $id; ?>" />
<input type="hidden" name="folder" value="<?php echo $current_file['folder_id']; ?>" />
<div class="input-form">
	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="comment"><?php echo _AT('comment'); ?></label><br />
		<textarea cols="40" rows="4" id="comment" name="comment"></textarea>
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('post'); ?>" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
</div>
</form>
<?php endif; ?>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>