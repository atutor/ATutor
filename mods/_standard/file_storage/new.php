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
	header('Location: index.php');
	exit;
}

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php'.$owner_arg_prefix.'folder='.abs($_POST['folder']));
	exit;
} else if (isset($_POST['submit'])) {
	$_POST['comments'] = trim($_POST['comments']);
	$_POST['name'] = trim($_POST['name']);

	$parent_folder_id = abs($_POST['folder']);

	// check that we own this folder
	if ($parent_folder_id) {

		$sql = "SELECT folder_id FROM %sfolders WHERE folder_id=%d AND owner_type=%d AND owner_id=%d";
		$rows_folders = queryDB($sql, array(TABLE_PREFIX, $parent_folder_id, $owner_type, $owner_id));
		
		if(count($rows_folders) == 0){
			$msg->addError('ACCESS_DENIED');
			header('Location: index.php');
			exit;
		}
	}

	if (!$_POST['name']) {
		$msg->addError(array('EMPTY_FIELDS', _AT('file_name')));
	}

	if (!$msg->containsErrors()) {
		$_POST['description'] = $addslashes(trim($_POST['description']));
		$_POST['comment'] = $addslashes(trim($_POST['comment']));
		$_POST['name'] = $addslashes($_POST['name']);
		$_POST['body'] = $stripslashes($_POST['body']); // file gets saved to disk not db, so no need to escape.

		if ($_POST['comment']) {
			$num_comments = 1;
		} else {
			$num_comments = 0;
		}

		$size = strlen($_POST['body']);

		$sql = "INSERT INTO %sfiles VALUES (NULL, %d, %d, %d, %d, 0, NOW(), %d, 0, '%s',%d, '%s')";
		$result = queryDB($sql, array(TABLE_PREFIX, $owner_type, $owner_id, $_SESSION['member_id'], $parent_folder_id, $num_comments, $_POST['name'], $size, $_POST['description']));

	    if($result > 0 && ($file_id = at_insert_id())) {
			$file_path = fs_get_file_path($file_id) . $file_id;
			$fp = fopen($file_path, 'wb');
			fwrite($fp, $_POST['body'], $size);
			fclose($fp);

			// check if this file name already exists

			$sql = "SELECT file_id, num_revisions FROM %sfiles WHERE owner_type=%d AND owner_id=%d AND folder_id=%d AND file_id<>%d AND file_name='%s' AND parent_file_id=0 ORDER BY file_id DESC LIMIT 1";
			$rows_revisions = queryDB($sql, array(TABLE_PREFIX, $owner_type, $owner_id, $parent_folder_id, $file_id, $_POST['name']));
            if(count($rows_revisions) > 0){
				if ($_config['fs_versioning']) {

					$sql = "UPDATE %sfiles SET parent_file_id=%d, date=date WHERE file_id=%d";
					$result = queryDB($sql, array(TABLE_PREFIX, $file_id, $row['file_id']));

					$sql = "UPDATE %sfiles SET num_revisions=%d+1, date=date WHERE file_id=%d";
					$result = queryDB($sql, array(TABLE_PREFIX, $row['num_revisions'], $file_id));
					
				} else {
					fs_delete_file($row['file_id'], $owner_type, $owner_id);
				}
			}

			if ($_POST['comment']){

				$sql = "INSERT INTO %sfiles_comments VALUES (NULL, %d, %d, NOW(), '%s')";
				queryDB($sql, array(TABLE_PREFIX, $file_id, $_SESSION['member_id'], $_POST['comment']));
			}

			$msg->addFeedback(array('FILE_SAVED', $_POST['name']));
			header('Location: index.php'.$owner_arg_prefix.'folder='.$parent_folder_id);
			exit;
		}
	}
}

$onload = 'document.form.name.focus();';

require(AT_INCLUDE_PATH.'header.inc.php');

if (($_POST['setvisual'] && !$_POST['settext']) || $_GET['setvisual']) {
	require(AT_INCLUDE_PATH.'lib/tinymce.inc.php');

	load_editor(false, 'body');
}
if (isset($_POST['description'])) {
	$_POST['description'] = $stripslashes($_POST['description']);
	$_POST['name']        = $stripslashes($_POST['name']);
	$_POST['comment']     = $stripslashes($_POST['comment']);
	$_POST['body']        = $stripslashes($_POST['body']);
}
?>
<form action="<?php echo $_SERVER['PHP_SELF'] . $owner_arg_prefix; ?>" method="post" name="form">
<input type="hidden" name="folder" value="<?php echo abs($_REQUEST['folder']); ?>" />
<input type="submit" name="submit" style="display:none;"/>
<div class="input-form">
	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="name"><?php echo _AT('file_name'); ?></label><br />
		<input type="text" name="name" id="name" value="<?php echo htmlspecialchars($_POST['name']); ?>" size="40" maxlength="70" />
	</div>

	<div class="row">
		<label for="description"><?php echo _AT('description'); ?></label><br />
		<textarea name="description" id="description" cols="30" rows="2"><?php echo htmlspecialchars($_POST['description']); ?></textarea>
	</div>

	<div class="row">
		<label for="comment"><?php echo _AT('revision_comment'); ?></label><br />
		<textarea name="comment" id="comment" cols="30" rows="2"><?php echo htmlspecialchars($_POST['comment']); ?></textarea>
	</div>

	<div class="row">
		<?php
			if (($_POST['setvisual'] && !$_POST['settext']) || $_GET['setvisual']){
				echo '<input type="hidden" name="setvisual" value="'.$_POST['setvisual'].'" />';
				echo '<input type="submit" name="settext" value="'._AT('switch_text').'" />';
			} else {
				echo '<input type="submit" name="setvisual" value="'._AT('switch_visual').'" />';
			}
		?>
	</div>

	<div class="row">
		<label for="body"><?php echo _AT('contents');  ?></label><br />
		<textarea name="body" id="body" rows="25" cols="30"><?php echo htmlspecialchars($_POST['body']); ?></textarea>
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>"  />		
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>