<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton	*/
/* Adaptive Technology Resource Centre / University of Toronto				*/
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_ADMIN); 

$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/';
$_section[1][0] = _AT('backup_manager');
$_section[1][1] = 'tools/backup/index.php';
$_section[2][0] = _AT('upload_backup');

$_SESSION['done'] = 0;

require(AT_INCLUDE_PATH.'classes/Backup/Backup.class.php');

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php');
	exit;
} else if (isset($_POST['upload'])) {
	$Backup =& new Backup($db, $_SESSION['course_id']);
	
	$Backup->upload($_FILES, $_POST['description']);

	if($msg->containsErrors()) {
		require(AT_INCLUDE_PATH.'header.inc.php');
		$msg->printErrors();
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	} else {
		$msg->addFeedback('BACKUP_UPLOADED');
		header('Location: index.php');
		exit;
	}
session_write_close();
}

require(AT_INCLUDE_PATH.'header.inc.php');

$msg->printAll();

?>
<form name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
<div class="input-form">
	<div class="row">
		<p><?php echo _AT('restore_upload'); ?></p>
	</div>

	<div class="row">
		<label for="descrip"><?php echo _AT('optional_description'); ?></label><br />
		<textarea id="descrip" cols="30" rows="2" name="description"></textarea>
	</div>
	
	<div class="row">
		<label for="file"><?php echo _AT('file'); ?></label><br />
		<input type="file" name="file" id="file" />
	</div>

	<div class="row buttons">
		<input type="submit" name="upload" value="<?php echo _AT('upload'); ?>" /> 
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
</div>
</form>

<?php require (AT_INCLUDE_PATH.'footer.inc.php');  ?>