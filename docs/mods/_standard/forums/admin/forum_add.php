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
// $Id: forum_add.php 7208 2008-01-09 16:07:24Z greg $

define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_FORUMS);

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.AT_BASE_HREF.'mods/_standard/forums/admin/forums.php');
	exit;
} else if (isset($_POST['add_forum'])) {
	$missing_fields = array();

	if (empty($_POST['title'])) {
		$missing_fields[] = _AT('title');
	} 

	if (empty($_POST['courses'])) {
		$missing_fields[] = _AT('courses');
	} 

	if ($missing_fields) {
		$missing_fields = implode(', ', $missing_fields);
		$msg->addError(array('EMPTY_FIELDS', $missing_fields));
	}

	$_POST['edit'] = intval($_POST['edit']);

	if (!($msg->containsErrors())) {
		//add forum
		$sql	= "INSERT INTO ".TABLE_PREFIX."forums (title, description, mins_to_edit) VALUES ('" . $_POST['title'] . "','" . $_POST['description'] ."', $_POST[edit])";
		$result	= mysql_query($sql, $db);
		$forum_id = mysql_insert_id($db);
		write_to_log(AT_ADMIN_LOG_INSERT, 'forums', mysql_affected_rows($db), $sql);

		//for each course, add an entry to the forums_courses table
		foreach ($_POST['courses'] as $course) {
			$sql	= "INSERT INTO ".TABLE_PREFIX."forums_courses VALUES (" . $forum_id . "," . $course . ")";
			$result	= mysql_query($sql, $db);
			write_to_log(AT_ADMIN_LOG_INSERT, 'forums_courses', mysql_affected_rows($db), $sql);
		}

		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		if($course =="0"){
			$msg->addFeedback('FORUM_POSTING');
		}
		header('Location: '.AT_BASE_HREF.'mods/_standard/forums/admin/forums.php');
		exit;
	}
}

$onload = 'document.form.title.focus();';

require(AT_INCLUDE_PATH.'header.inc.php'); 

?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="add_forum" value="true">

<div class="input-form">
	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="title"><?php echo _AT('title'); ?></label><br />
		<input type="text" name="title" size="40" id="title" value="<?php echo $_POST['title']; ?>" />
	</div>

	<div class="row">
		<label for="body"><?php echo _AT('description'); ?></label><br />
		<textarea name="description" cols="45" rows="2" id="body" wrap="wrap"><?php echo $_POST['description']; ?></textarea>
	</div>

	<div class="row">
		<label for="edit"><?php echo _AT('allow_editing'); ?></label><br />
		<input type="text" name="edit" size="3" id="edit" value="<?php echo intval($row['mins_to_edit']); ?>" /> <?php echo _AT('in_minutes'); ?>
	</div>

	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="courses"><?php echo _AT('courses'); ?></label><br />
		<?php if ($system_courses): ?>
			<select name="courses[]" id="courses" multiple="multiple" size="5"><?php
		
				$sql = "SELECT course_id, title FROM ".TABLE_PREFIX."courses ORDER BY title";
				$result = mysql_query($sql, $db);
				while ($row = mysql_fetch_assoc($result)) {
					echo '<option value="'.$row['course_id'].'">'.$row['title'].'</option>';		
				}
				?>
			</select>
		<?php else: ?>
			<span id="courses"><?php echo _AT('no_courses_found'); ?></span>
		<?php endif; ?>
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s" /> 
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>