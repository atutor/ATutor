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
// $Id: create.php 8901 2009-11-11 19:10:19Z cindy $

define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_BACKUPS);

$page = 'backups';
$_user_location = 'admin';

$course = $_POST['course'];
require(AT_INCLUDE_PATH.'../mods/_core/backups/classes/Backup.class.php');

$Backup = new Backup($db);

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php');
	exit;
} else if (isset($_POST['submit'])) {

	$Backup->setCourseID($_POST['course']);
	$error = $Backup->create($_POST['description']);
	if ($error !== FALSE) {
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		header('Location: index.php');
		exit;
	}
}

require(AT_INCLUDE_PATH.'header.inc.php');

?>


<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">

<div class="input-form">
	<div class="row">
		<p><?php echo _AT('create_backup_about', AT_COURSE_BACKUPS); ?></p>
	</div>

	<?php if ($system_courses): ?>
		<?php if (isset($_POST['submit']) && ($Backup->getNumAvailable() >= AT_COURSE_BACKUPS)): ?>
			<div class="row">
				<p><strong><?php echo _AT('max_backups_reached'); ?></strong></p>
			</div>
		<?php else: ?>
			<div class="row">
				<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="courses"><?php echo _AT('course'); ?></label><br />
				<select name="course" id="courses"><?php
					foreach ($system_courses as $id => $course) {
						echo '<option value="'.$id.'">'.$course['title'].'</option>';
					}
				?>
				</select>
			</div>
			<div class="row">
				<label for="desc"><?php echo _AT('optional_description'); ?></label><br />
				<textarea cols="35" rows="2" id="desc" name="description" scroll="no"></textarea>
			</div>
			<div class="row buttons">
				<input type="submit" name="submit" value="<?php echo _AT('save'); ?>"  /> <input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>"  />
			</div>
		<?php endif; ?>
	<?php else: ?>
		<div class="row">
			<p><?php echo _AT('no_courses_found'); ?></p>
		</div>
	<?php endif; ?>
</div>
</form>

<?php require (AT_INCLUDE_PATH.'footer.inc.php');  ?>