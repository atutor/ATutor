<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: move.php 5923 2006-03-02 17:10:44Z joel $

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/file_storage.inc.php');

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php?folder='.abs($_POST['folder']));
	exit;
} else if (isset($_POST['submit'])) {
	$_POST['new_folder'] = abs($_POST['new_folder']);

	// authenticate new_folder with owner_type and owner_id //
	

	if ($_POST['folder'] == $_POST['new_folder']) {
		// src = dest
		$msg->addFeedback('CANCELLED');
		header('Location: index.php?folder='.$_POST['new_folder']);
		exit;
	}

	if (isset($_POST['files'])) {
		foreach ($_POST['files'] as $file) {
			$file = abs($file);
			// check if this file name already exists
			$sql = "SELECT file_name FROM ".TABLE_PREFIX."files WHERE file_id=$file";
			$result = mysql_query($sql, $db);
			$row = mysql_fetch_assoc($result);

			$sql = "SELECT file_id FROM ".TABLE_PREFIX."files WHERE folder_id={$_POST['new_folder']} AND file_id<>$file AND file_name='{$row['file_name']}' AND parent_file_id=0 ORDER BY file_id DESC LIMIT 1";
			$result = mysql_query($sql, $db);
			if ($row = mysql_fetch_assoc($result)) {
				fs_delete_file($row['file_id']);
			}

			$sql = "UPDATE ".TABLE_PREFIX."files SET folder_id={$_POST['new_folder']} WHERE file_id=$file";
			mysql_query($sql, $db);
		}
		$msg->addFeedback('FILES_MOVED');
	}

	if (isset($_POST['folders'])) {
		foreach ($_POST['folders'] as $folder) {
			$file = abs($file);
			$sql = "UPDATE ".TABLE_PREFIX."folders SET parent_folder_id={$_POST['new_folder']} WHERE folder_id=$folder";
			mysql_query($sql, $db);
		}
		$msg->addFeedback('DIRS_MOVED');
	}
	header('Location: index.php?folder='.$_POST['new_folder']);
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

$folder_id = abs($_GET['folder']);

if ($_SESSION['workspace'] == WORKSPACE_COURSE) {
	$owner_id = $_SESSION['course_id'];
} else if ($_SESSION['workspace'] == WORKSPACE_PERSONAL) {
	$owner_id = $_SESSION['member_id'];
} else if ($_SESSION['workspace'] == WORKSPACE_GROUP) {
	$owner_id = $group_id;
}

$folders = array();
$sql = "SELECT folder_id, parent_folder_id, title FROM ".TABLE_PREFIX."folders WHERE owner_type=$_SESSION[workspace] AND owner_id=$owner_id ORDER BY parent_folder_id, title";
$result = mysql_query($sql, $db);
while ($row = mysql_fetch_assoc($result)) {
	$folders[$row['parent_folder_id']][$row['folder_id']] = $row;
}

?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<?php if ($_GET['files']): foreach ($_GET['files'] as $tmpfile): ?>
	<input type="hidden" name="files[]" value="<?php echo $tmpfile; ?>" />
<?php endforeach; endif; ?>

<?php if ($_GET['folders']): foreach ($_GET['folders'] as $tmpfolder): ?>
	<input type="hidden" name="folders[]" value="<?php echo $tmpfolder; ?>" />
<?php endforeach; endif; ?>

<input type="hidden" name="folder" value="<?php echo $folder_id; ?>" />
<div class="input-form">
	<div class="row">
		<p><?php echo _AT('select_directory'); ?></p>
	</div>

	<div class="row">
		<ul>
			<li class="folders"><input type="radio" name="new_folder" value="0" id="fhome" <?php
				if ($folder_id == 0) {
					echo ' checked="checked"';
				}
			?>/><label for="fhome"><?php echo _AT('my_files'); ?>
			<?php 
				if ($folder_id == $current_folder_id) {
					echo ' '._AT('current_location');
				}
			?>
			<?php fs_print_folders($folder_id, 0, $folders); ?>
			</li>
		</ul>
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('move'); ?>" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>