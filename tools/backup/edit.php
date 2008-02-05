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
// $Id$

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_ADMIN); 

require(AT_INCLUDE_PATH.'classes/Backup/Backup.class.php');

if (isset($_POST['cancel']) || !isset($_REQUEST['backup_id'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php');
	exit;
}

$Backup =& new Backup($db, $_SESSION['course_id']);

if (isset($_POST['edit'])) {
	$Backup->edit($_POST['backup_id'], $_POST['new_description']);
	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: index.php');
	exit;
} 

require(AT_INCLUDE_PATH.'header.inc.php');

$row = $Backup->getRow($_REQUEST['backup_id']);
//check for errors


?>
<form name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="backup_id" value="<?php echo $_GET['backup_id']; ?>" />
<div class="input-form">
	<div class="row">
		<label for="description"><?php echo _AT('optional_description'); ?></label>
		<textarea cols="30" rows="2" id="description" name="new_description"><?php echo $row['description']; ?></textarea>
	</div>

	<div class="row buttons">
		<input type="submit" name="edit" value="<?php echo _AT('save'); ?>" accesskey="s" /> 
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
</div>
</form>
<?php require (AT_INCLUDE_PATH.'footer.inc.php');  ?>