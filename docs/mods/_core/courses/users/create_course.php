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
// $Id: create_course.php 9117 2010-01-20 18:07:13Z greg $
$_user_location	= 'users';
define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'../mods/_core/backups/classes/Backup.class.php');
require(AT_INCLUDE_PATH.'../mods/_core/courses/lib/course.inc.php');

/* verify that this user has status to create courses */

if (get_instructor_status() === FALSE) {
	require(AT_INCLUDE_PATH.'header.inc.php');

	if (defined('ALLOW_INSTRUCTOR_REQUESTS') && ALLOW_INSTRUCTOR_REQUESTS) {
		$sql	= "SELECT member_id FROM ".TABLE_PREFIX."instructor_approvals WHERE member_id=$_SESSION[member_id]";
		$result = mysql_query($sql, $db);
		if (!($row = mysql_fetch_array($result))) : ?>
			<form action="mods/_core/courses/users/request_instructor.php" method="post">
			<input type="hidden" name="form_request_instructor" value="true" />
			<div class="input-form">
				<div class="row">
					<p><?php echo _AT('request_instructor'); ?></p>
				</div>

				<div class="row">
					<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="desc"><?php echo _AT('give_description'); ?></label><br />
					<textarea cols="40" rows="2" id="desc" name="description"></textarea>
				</div>

				<div class="row buttons">
					<input type="submit" name="submit" value="<?php echo _AT('send'); ?>" />
					<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
				</div>
			</div>
			</form>
		<?php else : ?>
			<div class="input-form">
				<div class="row">
					<p><?php echo _AT('request_instructor_pending'); ?></p>
				</div>
			</div>
		<?php endif; ?>
<?php
	}
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

$course = 0;
$isadmin   = FALSE;

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php');
	exit;
}else if (isset($_POST['form_course']) && $_POST['submit'] != '') {
	$_POST['instructor'] = $_SESSION['member_id'];

		$errors = add_update_course($_POST);

	if ($errors !== FALSE) {
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		header('Location: '.AT_BASE_HREF.'bounce.php?course='.$addslashes($errors).SEP.'p='.urlencode('index.php'));
		exit;
	}

}

$onload = 'document.course_form.title.focus();';

require(AT_INCLUDE_PATH.'header.inc.php');

require(AT_INCLUDE_PATH.'../mods/_core/courses/html/course_properties.inc.php');
require(AT_INCLUDE_PATH.'footer.inc.php');
?>