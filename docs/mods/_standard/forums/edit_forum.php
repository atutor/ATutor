<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton	*/
/* Adaptive Technology Resource Centre / University of Toronto				*/
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/
// $Id: edit_forum.php 7482 2008-05-06 17:44:49Z greg $

define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_FORUMS);

require (AT_INCLUDE_PATH.'../mods/_standard/forums/lib/forums.inc.php');

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.AT_BASE_HREF.'mods/_standard/forums/index.php');
	exit;
} else if (isset($_POST['edit_forum'])) {
	$_POST['fid'] = intval($_POST['fid']);

	if ($_POST['title'] == '') {
		$msg->addError(array('EMPTY_FIELDS', _AT('title')));
	} else {
		$_POST['title'] = validate_length($_POST['title'], 60);
	}

	if (!$msg->containsErrors()) {
		if (!is_shared_forum($_POST['fid'])) {
			edit_forum($_POST);
			$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		} else {
			$msg->addError('FORUM_NO_EDIT_SHARE');
		}
		
		header('Location: '.AT_BASE_HREF.'mods/_standard/forums/index.php');
		exit;
	}
}

$onload = 'document.form.title.focus();';
require(AT_INCLUDE_PATH.'header.inc.php');

$fid = intval($_REQUEST['fid']);

if (!isset($_POST['submit'])) {
	$row = get_forum($fid, $_SESSION['course_id']);
	if (!is_array($row)) {
		$msg->addError('FORUM_NOT_FOUND');
		$msg->printALL();
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}
} else {
	$row['description'] = $_POST['body'];
	$row['mins_to_edit'] = $_POST['edit'];
}

$msg->printErrors();

?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="edit_forum" value="true">
<input type="hidden" name="fid" value="<?php echo $fid; ?>">

<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('edit_forum'); ?></legend>
	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="title"><?php echo _AT('title'); ?></label><br />
		<input type="text" name="title" size="50" id="title" value="<?php echo htmlspecialchars(stripslashes($row['title'])); ?>" />
	</div>

	<div class="row">
		<label for="body"><?php echo _AT('description'); ?></label><br />
		<textarea name="body" cols="45" rows="2" id="body" wrap="wrap"><?php echo $row['description']; ?></textarea>
	</div>

	<div class="row">
		<label for="edit"><?php echo _AT('allow_editing'); ?></label><br />
		<input type="text" name="edit" size="3" id="edit" value="<?php echo intval($row['mins_to_edit']); ?>" /> <?php echo _AT('in_minutes'); ?>
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s" /> 
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
	</fieldset>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>