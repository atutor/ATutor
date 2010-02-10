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
// $Id$
$_user_location = 'admin';

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_ENROLLMENT);

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.AT_BASE_HREF.'mods/_core/users/users.php');
	exit;
} else if (isset($_POST['enrolled_unenroll'])) {
	$_POST['id'] = intval($_POST['id']);

	if (!is_array($_POST['enrolled'])) {
		$msg->addError('NO_ITEM_SELECTED');
	} else {
		$cids = implode(',', $_POST['enrolled']);
		$sql = "DELETE FROM ".TABLE_PREFIX."course_enrollment WHERE member_id={$_POST['id']} AND course_id IN ($cids)";
		mysql_query($sql, $db);

		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		header('Location: '.$_SERVER['PHP_SELF'] . '?id='.$_POST['id']);
		exit;
	}
} else if (isset($_POST['pending_remove'])) {
	$_POST['id'] = intval($_POST['id']);

	if (!is_array($_POST['pending'])) {
		$msg->addError('NO_ITEM_SELECTED');
	} else {
		$cids = implode(',', $_POST['pending']);
		$sql = "DELETE FROM ".TABLE_PREFIX."course_enrollment WHERE member_id={$_POST['id']} AND course_id IN ($cids)";
		mysql_query($sql, $db);

		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		header('Location: '.$_SERVER['PHP_SELF'] . '?id='.$_POST['id']);
		exit;
	}
} else if (isset($_POST['pending_enroll'])) {
	$_POST['id'] = intval($_POST['id']);

	if (!is_array($_POST['pending'])) {
		$msg->addError('NO_ITEM_SELECTED');
	} else {
		$cids = implode(',', $_POST['pending']);
		$sql = "UPDATE ".TABLE_PREFIX."course_enrollment SET approved='y' WHERE member_id={$_POST['id']} AND course_id IN ($cids)";
		mysql_query($sql, $db);

		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		header('Location: '.$_SERVER['PHP_SELF'] . '?id='.$_POST['id']);
		exit;
	}
} else if (isset($_POST['not_enrolled_enroll'])) {
	$_POST['id'] = intval($_POST['id']);

	if (!is_array($_POST['not_enrolled'])) {
		$msg->addError('NO_ITEM_SELECTED');
	} else {
		foreach ($_POST['not_enrolled'] as $cid) {
			$sql = "INSERT INTO ".TABLE_PREFIX."course_enrollment VALUES ({$_POST['id']}, $cid, 'y', 0, '', 0)";
			mysql_query($sql, $db);
		}
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		header('Location: '.$_SERVER['PHP_SELF'] . '?id='.$_POST['id']);
		exit;
	}
}

$id = intval($_GET['id']);

// add the user's name to the page heading:
$_pages['mods/_core/users/user_enrollment.php']['title'] = _AT('enrollment').': '.get_display_name($id);

require(AT_INCLUDE_PATH.'header.inc.php');

$sql	= "SELECT login FROM ".TABLE_PREFIX."members WHERE member_id=$id";
$result = mysql_query($sql, $db);

if (!$row = mysql_fetch_assoc($result)) {
	$msg->printErrors('USER_NOT_FOUND');
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

$enrollment = array();
$sql = "SELECT * FROM ".TABLE_PREFIX."course_enrollment WHERE member_id=$id";
$result = mysql_query($sql, $db);
while ($row = mysql_fetch_assoc($result)) {
	$enrollment[$row['course_id']] = $row;
}

$instruct     = array();
$enrolled     = array();
$pending      = array();
$not_enrolled = array();

foreach ($system_courses as $cid => $course) {
	if ($course['member_id'] == $id) {
		$instruct[] = $cid;
	} else if (isset($enrollment[$cid]) && $enrollment[$cid]['approved'] == 'y') {
		$enrolled[] = $cid;
	} else if (isset($enrollment[$cid]) && $enrollment[$cid]['approved'] == 'n') {
		$pending[] = $cid;
	} else {
		$not_enrolled[] = $cid;
	}
}

?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $id; ?>">
<input type="hidden" name="id" value="<?php echo $id; ?>"/>
<div class="input-form" style="min-width: 400px; width: 45%; float: left; margin: 5px">
	<div class="row">
	<h3><?php echo _AT('instructor'); ?></h3>
		<?php if ($instruct): ?>
			<ul>
			<?php foreach ($instruct as $cid): ?>
				<li><?php echo $system_courses[$cid]['title']; ?></li>
			<?php endforeach; ?>
			</ul>
		<?php else: ?>
			<?php echo _AT('none'); ?>
		<?php endif; ?>
	</div>
	<div class="row buttons">
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>"/>
	</div>
</div>

<div class="input-form" style="min-width: 400px; width: 45%; float: left; margin: 5px">
	<div class="row">
	<h3><?php echo _AT('enrolled'); ?></h3>
		<?php if ($enrolled): ?>
			<ul>
			<?php foreach ($enrolled as $cid): ?>
				<li><input type="checkbox" name="enrolled[]" value="<?php echo $cid; ?>" id="c<?php echo $cid; ?>"/><label for="c<?php echo $cid; ?>"><?php echo $system_courses[$cid]['title']; ?></label></li>
			<?php endforeach; ?>
			</ul>
		<?php else: ?>
			<?php echo _AT('none'); ?>
		<?php endif; ?>
	</div>
	<div class="row buttons">
	<?php if ($enrolled): ?>
		<input type="submit" name="enrolled_unenroll" value="<?php echo _AT('unenroll'); ?>"/>
	<?php endif; ?>
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>"/>
	</div>

</div>

<div class="input-form" style="min-width: 400px; width: 45%; float: left; margin: 5px">
	<div class="row">
	<h3><?php echo _AT('pending_enrollment'); ?></h3>
		<?php if ($pending): ?>
			<ul>
			<?php foreach ($pending as $cid): ?>
				<li><input type="checkbox" name="pending[]" value="<?php echo $cid; ?>" id="c<?php echo $cid; ?>"/><label for="c<?php echo $cid; ?>"><?php echo $system_courses[$cid]['title']; ?></label></li>
			<?php endforeach; ?>
			</ul>
		<?php else: ?>
			<?php echo _AT('none'); ?>
		<?php endif; ?>
	</div>
	<div class="row buttons">
	<?php if ($pending): ?>
		<input type="submit" name="pending_remove" value="<?php echo _AT('remove'); ?>"/>
		<input type="submit" name="pending_enroll" value="<?php echo _AT('enroll'); ?>"/>
	<?php endif; ?>
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>"/>
	</div>	
</div>

<div class="input-form" style="min-width: 400px; width: 45%; float: left; margin: 5px">
	<div class="row">
		<h3><?php echo _AT('not_enrolled');?></h3>
			<?php if ($not_enrolled): ?>
				<ul>
				<?php foreach ($not_enrolled as $cid): ?>
					<li><input type="checkbox" name="not_enrolled[]" value="<?php echo $cid; ?>" id="c<?php echo $cid; ?>"/><label for="c<?php echo $cid; ?>"><?php echo $system_courses[$cid]['title']; ?></label></li>
				<?php endforeach; ?>
				</ul>
			<?php else: ?>
				<?php echo _AT('none'); ?>
			<?php endif; ?>
	</div>
	<div class="row buttons">
	<?php if ($not_enrolled): ?>
		<input type="submit" name="not_enrolled_enroll" value="<?php echo _AT('enroll'); ?>"/>
	<?php endif; ?>
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>"/>
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>