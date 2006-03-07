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
// $Id: edit.php 5923 2006-03-02 17:10:44Z joel $

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php?folder='.abs($_POST['parent_folder']));
	exit;
} else if (isset($_POST['submit'])) {
	$_POST['id'] = abs($_POST['id']);

	if (!$_POST['name']) {
		$msg->addError('MISSING_FOLDER_NAME');
	}

	if (!$msg->containsErrors()) {
		$_POST['name'] = $addslashes($_POST['name']);
		$folder = abs($_POST['folder']);
		$parent_folder = abs($_POST['parent_folder']);

		$sql = "UPDATE ".TABLE_PREFIX."folders SET title='$_POST[name]' WHERE folder_id=$_POST[id] AND parent_folder_id=$parent_folder";
		mysql_query($sql, $db);

		$msg->addFeedback('FOLDER_EDITED_SUCCESSFULLY');
		header('Location: index.php?folder='.$parent_folder);
		exit;
	}

	$_GET['id'] = $_POST['id'];
}

require(AT_INCLUDE_PATH.'header.inc.php');

$id = abs($_GET['id']);

$sql = "SELECT title, parent_folder_id FROM ".TABLE_PREFIX."folders WHERE folder_id=$id";
$result = mysql_query($sql, $db);
if (!$row = mysql_fetch_assoc($result)) {
	$msg->printErrors('FOLDER_NOT_EXIST');
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}
?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="id" value="<?php echo $id; ?>" />
<input type="hidden" name="parent_folder" value="<?php echo $row['parent_folder_id']; ?>" />
<div class="input-form">
	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="name"><?php echo _AT('name'); ?></label><br />
		<input type="text" name="name" id="name" value="<?php echo $row['title']; ?>" size="40" maxlength="70" />
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>