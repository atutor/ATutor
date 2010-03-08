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

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'../mods/_core/file_manager/filemanager.inc.php');

if (!authenticate(AT_PRIV_FILES,AT_PRIV_RETURN)) {
	authenticate(AT_PRIV_CONTENT);
}

$current_path = AT_CONTENT_DIR.$_SESSION['course_id'].'/';

$popup = $_REQUEST['popup'];
$framed = $_REQUEST['framed'];

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php?pathext='.$_POST['pathext'].SEP.'framed='.$_POST['framed'].SEP.'popup='.$_POST['popup'].SEP.'cp='.$_POST['cp'].SEP.'cid='.$_POST['cid'].SEP.'pid='.$_POST['pid'].SEP.'a_type='.$_POST['a_type']);
	exit;
}

if (isset($_POST['rename_action'])) {

	$_POST['new_name'] = trim($_POST['new_name']);
	$_POST['new_name'] = str_replace(' ', '_', $_POST['new_name']);
	$_POST['new_name'] = str_replace(array(' ', '/', '\\', ':', '*', '?', '"', '<', '>', '|', '\''), '', $_POST['new_name']);

	$_POST['oldname'] = trim($_POST['oldname']);
	$_POST['oldname'] = str_replace(' ', '_', $_POST['oldname']);
	$_POST['oldname'] = str_replace(array(' ', '/', '\\', ':', '*', '?', '"', '<', '>', '|', '\''), '', $_POST['oldname']);

	$path_parts_new = pathinfo($_POST['new_name']);
	$ext_new = $path_parts_new['extension'];
	$pathext = $_POST['pathext'];

	/* check if this file extension is allowed: */
	/* $IllegalExtentions is defined in ./include/config.inc.php */
	if (in_array($ext_new, $IllegalExtentions)) {
		$errors = array('FILE_ILLEGAL', $ext_new);
		$msg->addError($errors);
	}
	else if ($current_path.$pathext.$_POST['new_name'] == $current_path.$pathext.$_POST['oldname']) {
		//do nothing
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		header('Location: index.php?pathext='.urlencode($_POST['pathext']).SEP.'framed='.$_POST['framed'].SEP.'popup='.$_POST['popup'].SEP.'cp='.$_POST['cp'].SEP.'cid='.$_POST['cid'].SEP.'pid='.$_POST['pid'].SEP.'a_type='.$_POST['a_type']);
		exit;
	}

	//make sure new file is inside content directory
	else if (course_realpath($current_path . $pathext . $_POST['new_name']) == FALSE) {
		$msg->addError('CANNOT_RENAME');
	}	
	else if (course_realpath($current_path . $pathext . $_POST['oldname']) == FALSE) {
		$msg->addError('CANNOT_RENAME');
	}
	else if (file_exists($current_path . $pathext . $_POST['new_name'])) {
		$msg->addError('CANNOT_RENAME');
	}
	else {
		@rename($current_path.$pathext.$_POST['oldname'], $current_path.$pathext.$_POST['new_name']);
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		header('Location: index.php?pathext='.urlencode($_POST['pathext']).SEP.'framed='.$_POST['framed'].SEP.'popup='.$_POST['popup'].SEP.'cp='.$_POST['cp'].SEP.'cid='.$_POST['cid'].SEP.'pid='.$_POST['pid'].SEP.'a_type='.$_POST['a_type']);
		exit;
	}
}

require(AT_INCLUDE_PATH.'header.inc.php');
?>
<form name="rename" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<input type="hidden" name="pathext" value="<?php echo $_REQUEST['pathext']; ?>" />
<input type="hidden" name="oldname" value="<?php echo $_REQUEST['oldname']; ?>" />
<input type="hidden" name="framed" value="<?php echo $_REQUEST['framed']; ?>" />
<input type="hidden" name="popup" value="<?php echo $_REQUEST['popup']; ?>" />

<div class="input-form">
	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div>
		<label for="new"><?php echo _AT('new_name'); ?></label><br />
		<?php echo $_GET['pathext']; ?><input type="text" name="new_name" id="new" value="<?php echo $_REQUEST['oldname']; ?>" size="30" />
	</div>

	<div class="row buttons">
		<input type="submit" name="rename_action" value="<?php echo _AT('save'); ?>" accesskey="s" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>