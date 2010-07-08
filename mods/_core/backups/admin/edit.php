<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2010                                                  */
/* Inclusive Design Institute                                               */
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/
// $Id: edit.php 8901 2009-11-11 19:10:19Z cindy $

define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_BACKUPS);

$page = 'backups';
$_user_location = 'admin';

require(AT_INCLUDE_PATH.'../mods/_core/backups/classes/Backup.class.php');

$_SESSION['done'] = 0;

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php');
	exit;
} 

$Backup = new Backup($db, $_REQUEST['course']);
$backup_row = $Backup->getRow($_REQUEST['backup_id']);

if (isset($_POST['edit'])) {
	$Backup->edit($_POST['backup_id'], $_POST['new_description']);
	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: index.php');
	exit;
} 

//check for errors

require(AT_INCLUDE_PATH.'header.inc.php');

?>

<form name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data" onsubmit="">
<input type="hidden" name="backup_id" value="<?php echo $_GET['backup_id']; ?>" />
<input type="hidden" name="course" value="<?php echo $_GET['course']; ?>" />

<div class="input-form">
	<div class="row">
		<?php echo _AT('file_name'); ?><br />
		<?php echo _AT('edit_backup', $backup_row['file_name']); ?>
	</div>

	<div class="row">
		<label for="desc"><?php echo _AT('description'); ?></label><br />
		<textarea cols="30" rows="2" name="new_description" id="desc"><?php echo htmlentities_utf8($backup_row['description']); ?></textarea>
	</div>

	<div class="row buttons">
		<input type="submit" name="edit" value="<?php echo _AT('save'); ?>" /> <input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
</div>
</form>
<?php require (AT_INCLUDE_PATH.'footer.inc.php');  ?>