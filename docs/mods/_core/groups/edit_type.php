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
// $Id: edit_type.php 7482 2008-05-06 17:44:49Z greg $

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_GROUPS);

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php');
	exit;
} else if (isset($_POST['submit'])) {
	$_POST['title']   = trim($_POST['title']);

	if (!$_POST['title']) {
		$msg->addError(array('EMPTY_FIELDS', _AT('title')));
	}

	if (!$msg->containsErrors()) {
		$_POST['title']       = $addslashes($_POST['title']);

		$type_id = intval($_POST['type_id']);

		$sql = "UPDATE ".TABLE_PREFIX."groups_types SET title='$_POST[title]' WHERE course_id=$_SESSION[course_id] AND type_id=$type_id";
		$result = mysql_query($sql, $db);

		$msg->addFeedback('GROUP_TYPE_EDITED_SUCCESSFULLY');

		header('Location: index.php');
		exit;
	}
	$_GET['id'] = abs($_POST['type_id']);
}

require(AT_INCLUDE_PATH.'header.inc.php');

	$_GET['id'] = intval($_GET['id']);

	$sql = "SELECT * FROM ".TABLE_PREFIX."groups_types WHERE type_id=$_GET[id] AND course_id=$_SESSION[course_id]";
	$result = mysql_query($sql,$db);
	if (!($row = mysql_fetch_assoc($result))) {
		$msg->printErrors('GROUP_TYPE_NOT_FOUND');
		require (AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}
?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
<input type="hidden" name="type_id" value="<?php echo $row['type_id']; ?>" />
<div class="input-form">
		<fieldset class="group_form"><legend class="group_form"><?php echo _AT('edit'); ?></legend>
	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="title"><?php echo _AT('title'); ?></label><br />
		<input type="text" name="title" id="title" value="<?php echo $row['title']; ?>" size="30" maxlength="40" />
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
	</fieldset>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>