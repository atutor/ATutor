<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: assignment.php 10142 2010-08-17 19:17:26Z hwong $

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'../mods/_standard/file_storage/file_storage.inc.php');

$owner_type = abs($_REQUEST['ot']);
$owner_id   = abs($_REQUEST['oid']);
$owner_arg_prefix = '?ot='.$owner_type.SEP.'oid='.$owner_id. SEP;
if (!($owner_status = fs_authenticate($owner_type, $owner_id)) || !query_bit($owner_status, WORKSPACE_AUTH_WRITE)) { 
	$msg->addError('ACCESS_DENIED');
	header('Location: '.url_rewrite('mods/_standard/file_storage/index.php', AT_PRETTY_URL_IS_HEADER));
	exit;
}

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.url_rewrite('mods/_standard/file_storage/index.php'.$owner_arg_prefix.'folder='.abs($_POST['folder']), AT_PRETTY_URL_IS_HEADER));
	exit;
} else if (isset($_POST['submit'])) {
	$_POST['assignment'] = abs($_POST['assignment']);
	$assignment_row    = fs_get_assignment($_POST['assignment']);

	if (!$assignment_row) {
		$msg->addError('ACCESS_DENIED');
		header('Location: '.url_rewrite('mods/_standard/file_storage/index.php', AT_PRETTY_URL_IS_HEADER));
		exit;
	}

	if (!$assignment_row['assign_to']) {
		if (!$_SESSION['enroll']) {
			$msg->addError('ACCESS_DENIED');
			header('Location: '.url_rewrite('mods/_standard/file_storage/index.php', AT_PRETTY_URL_IS_HEADER));
			exit;
		}

	} else {
		$sql = "SELECT group_id FROM ".TABLE_PREFIX."groups WHERE group_id=$owner_id AND type_id=$assignment_row[assign_to]";
		$result = mysql_query($sql, $db);
		if (!$row = mysql_fetch_assoc($result)) {
			$msg->addError('ACCESS_DENIED');
			header('Location: '.url_rewrite('mods/_standard/file_storage/index.php', AT_PRETTY_URL_IS_HEADER));
			exit;
		}
	}

	if ($assignment_row['u_date_cutoff'] && ($assignment_row['u_date_cutoff'] < time())) {
		$msg->addError('ASSIGNMENT_CUTOFF');
		header('Location: '.url_rewrite('mods/_standard/file_storage/index.php'.$owner_arg_prefix.'folder='.$_POST['folder'], AT_PRETTY_URL_IS_HEADER));
		exit;
	}

	foreach ($_POST['files'] as $file) {
		$file = abs($file);
		fs_copy_file($file, $owner_type, $owner_id, WORKSPACE_ASSIGNMENT, $_POST['assignment'], $owner_id);
	}

	$msg->addFeedback('ASSIGNMENT_HANDED_IN');
	header('Location: '.url_rewrite('mods/_standard/file_storage/index.php'.$owner_arg_prefix.'folder='.$_POST['folder'], AT_PRETTY_URL_IS_HEADER));
	exit;
}

// get all the assignments assigned to $owner_id (which is either a student ID or a group type ID)
if ($owner_type == WORKSPACE_GROUP) {
	// get all the assignments assigned to this group type

	$sql = "SELECT type_id FROM ".TABLE_PREFIX."groups WHERE group_id=$owner_id LIMIT 1";
	$result = mysql_query($sql, $db);
	$row = mysql_fetch_assoc($result);

	$sql = "SELECT assignment_id, title, date_due, date_cutoff FROM ".TABLE_PREFIX."assignments WHERE assign_to=$row[type_id] AND course_id=$_SESSION[course_id] AND (date_cutoff=0 OR UNIX_TIMESTAMP(date_cutoff) > ".time().") ORDER BY title";

} else if ($owner_type == WORKSPACE_PERSONAL) {
	// get all the assignments assigned to students

	$sql = "SELECT assignment_id, title, date_due FROM ".TABLE_PREFIX."assignments WHERE assign_to=0 AND course_id=$_SESSION[course_id] AND (date_cutoff=0 OR UNIX_TIMESTAMP(date_cutoff) > ".time().") ORDER BY title";
} else {
	exit('wrong workspace');
}

$assignments = array();
$result = mysql_query($sql, $db);
while ($row = mysql_fetch_assoc($result)) {
	$assignments[] = $row;
}

if (!$assignments) {
	$msg->addError('NO_ASSIGNMENTS_FOUND');
	header('Location: '.url_rewrite('mods/_standard/file_storage/index.php'.$owner_arg_prefix.'folder='.$_GET['folder'], AT_PRETTY_URL_IS_HEADER));
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF'].$owner_arg_prefix; ?>">
<input type="hidden" name="folder" value="<?php echo abs($_GET['folder']); ?>" />
<?php foreach ($_GET['files'] as $key => $file): ?>
	<?php $_GET['files'][$key] = $file = abs($file); ?>
	<input type="hidden" name="files[]" value="<?php echo $file; ?>" />
<?php endforeach; ?>

<div class="input-form">
	
	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="name"><?php echo _AT('assignment'); ?></label><br />
		<select name="assignment" size="<?php echo min(5, count($assignments)); ?>">
			<?php foreach ($assignments as $assignment): ?>
				<?php if ($assignment['date_due'] != '0000-00-00 00:00:00'): ?>
					<option value="<?php echo $assignment['assignment_id']; ?>"><?php echo $assignment['title']; ?> - <?php echo _AT('due') . ': ' . AT_date(_AT('filemanager_date_format'), $assignment['date_due'], AT_DATE_MYSQL_DATETIME); ?></option>
				<?php else: ?>
					<option value="<?php echo $assignment['assignment_id']; ?>"><?php echo $assignment['title']; ?> - <?php echo _AT('no_due_date'); ?></option>
				<?php endif; ?>
			<?php endforeach; ?>
		</select>
	</div>

	<div class="row">
		<?php echo _AT('files'); ?>
		<ul style="list-style: none; margin: 0px; padding: 0px 10px;">
			<?php
				$file_list = implode(',', $_GET['files']);
				$sql = "SELECT file_name FROM ".TABLE_PREFIX."files WHERE file_id IN ($file_list) AND owner_type=$owner_type AND owner_id=$owner_id ORDER BY file_name";
				$result = mysql_query($sql, $db);
			?>
			<?php while ($row = mysql_fetch_assoc($result)): ?>
				<li><img src="images/file_types/<?php echo fs_get_file_type_icon($row['file_name']); ?>.gif" height="16" width="16" alt="" title="" /> <?php echo $row['file_name']; ?></li>
			<?php endwhile; ?>
		</ul>
	</div>

	<div class="buttons row">
		<input type="submit" name="submit" value="<?php echo _AT('submit'); ?>" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>