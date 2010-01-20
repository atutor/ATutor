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
// $Id: create.php 8901 2009-11-11 19:10:19Z cindy $

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_ADMIN);

$course = $_SESSION['course_id'];
require(AT_INCLUDE_PATH.'../mods/_core/backups/classes/Backup.class.php');
$Backup = new Backup($db, $_SESSION['course_id']);

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php');
	exit;
} else if (isset($_POST['submit'])) {
	//make backup of current course
	$Backup->create($_POST['description']);
	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: index.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">

<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('create_backup'); ?></legend>
	<div class="row">
		<?php echo _AT('create_backup_about', AT_COURSE_BACKUPS); ?>
	</div>

	<?php if ($Backup->getNumAvailable() >= AT_COURSE_BACKUPS): ?>
		<div class="row">
			<p><strong><?php echo _AT('max_backups_reached'); ?></strong></p>
		</div>
	<?php else: ?>
		<div class="row">
			<label for="desc"><?php echo _AT('optional_description'); ?></label>
			<textarea cols="35" rows="2" id="desc" name="description"></textarea>
		</div>

		<div class="row buttons">
			<input type="submit" name="submit" value="<?php echo _AT('create'); ?>" accesskey="s" /> 
			<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
		</div>
	<?php endif; ?>
</div>
</form>

<?php require (AT_INCLUDE_PATH.'footer.inc.php');  ?>