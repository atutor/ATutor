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
// $Id$

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
	$_POST['new_folder'] = abs($_POST['new_folder']);

	if ($_POST['folder'] == $_POST['new_folder']) {
		// src = dest
		$msg->addFeedback('CANCELLED');
		header('Location: '.url_rewrite('mods/_standard/file_storage/index.php'.$owner_arg_prefix.'folder='.$_POST['new_folder'], AT_PRETTY_URL_IS_HEADER));
		exit;
	}

	if (isset($_POST['files'])) {
		foreach ($_POST['files'] as $file) {
			$file = abs($file);
			// check if this file name already exists
			$sql = "SELECT file_name FROM %sfiles WHERE file_id=%d";
			$row = queryDB($sql, array(TABLE_PREFIX, $file), TRUE);
			
			$sql = "SELECT file_id FROM %sfiles WHERE folder_id=%d AND file_id<>%d AND file_name='%s' AND parent_file_id=0 AND owner_type=%d AND owner_id=%d ORDER BY file_id DESC LIMIT 1";
			$row = queryDB($sql, array(TABLE_PREFIX, $_POST['new_folder'], $file, $row['file_name'], $owner_type, $owner_id), TRUE);
			if(count($row) > 0){
				fs_delete_file($row['file_id'], $owner_type, $owner_id);
			}

			$sql = "UPDATE %sfiles SET folder_id=%d, date=date WHERE file_id=%d AND owner_type=%d AND owner_id=%d";
			queryDB($sql, array(TABLE_PREFIX, $_POST['new_folder'], $file, $owner_type, $owner_id));
		}
		$msg->addFeedback('FILES_MOVED');
	}

	if (isset($_POST['folders'])) {
		foreach ($_POST['folders'] as $folder) {
			$file = abs($file);

			$sql = "UPDATE %sfolders SET parent_folder_id=%d WHERE folder_id=%d AND owner_type=%d AND owner_id=%d";
			queryDB($sql, array(TABLE_PREFIX, $_POST['new_folder'], $folder, $owner_type, $owner_id));
		}
		$msg->addFeedback('DIRS_MOVED');
	}
	header('Location: '.url_rewrite('mods/_standard/file_storage/index.php'.$owner_arg_prefix.'folder='.$_POST['new_folder'], AT_PRETTY_URL_IS_HEADER));
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

$folder_id = abs($_GET['folder']);

// can't use fs_get_folders() because we want all folders, not just at one level
$folders = array();

$sql = "SELECT folder_id, parent_folder_id, title FROM %sfolders WHERE owner_type=%d AND owner_id=%d ORDER BY parent_folder_id, title";
$rows_folders = queryDB($sql, array(TABLE_PREFIX, $owner_type, $owner_id));

foreach($rows_folders as $row){
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
<input type="hidden" name="ot" value="<?php echo $owner_type; ?>" />
<input type="hidden" name="oid" value="<?php echo $owner_id; ?>" />
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
			?>/><label for="fhome"><?php echo fs_get_workspace($owner_type, $owner_id); ?></label>
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