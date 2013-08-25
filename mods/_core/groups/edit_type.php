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
// $Id$

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

		$sql = "UPDATE %sgroups_types SET title='%s' WHERE course_id=%d AND type_id=%d";
		$result = queryDB($sql, array(TABLE_PREFIX, $_POST['title'], $_SESSION['course_id'], $type_id));
		
		if($result > 0){
		    $msg->addFeedback('GROUP_TYPE_EDITED_SUCCESSFULLY');
        } 
		header('Location: index.php');
		exit;
	}
	$_GET['id'] = abs($_POST['type_id']);
}

require(AT_INCLUDE_PATH.'header.inc.php');

	$_GET['id'] = intval($_GET['id']);

	$sql = "SELECT * FROM %sgroups_types WHERE type_id=%d AND course_id=%d";
	$row = queryDB($sql,array(TABLE_PREFIX, $_GET['id'], $_SESSION['course_id']), TRUE);
	
	if(count($row) == 0){
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
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="title"><?php echo _AT('title'); ?></label><br />
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