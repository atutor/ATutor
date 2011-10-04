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
		$sql = "SELECT folder_id FROM ".TABLE_PREFIX."folders WHERE folder_id=$parent_folder_id AND owner_type=$owner_type AND owner_id=$owner_id";
		$result = mysql_query($sql, $db);
		if (!$row = mysql_fetch_assoc($result)) {
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
		$sql = "INSERT INTO ".TABLE_PREFIX."files VALUES (NULL, $owner_type, $owner_id, $_SESSION[member_id], $parent_folder_id, 0, NOW(), $num_comments, 0, '$_POST[name]',$size, '$_POST[description]')";
		$result = mysql_query($sql, $db);

		if ($result && ($file_id = mysql_insert_id($db))) {
			$file_path = fs_get_file_path($file_id) . $file_id;
			$fp = fopen($file_path, 'wb');
			fwrite($fp, $_POST['body'], $size);
			fclose($fp);

			// check if this file name already exists
			$sql = "SELECT file_id, num_revisions FROM ".TABLE_PREFIX."files WHERE owner_type=$owner_type AND owner_id=$owner_id AND folder_id=$parent_folder_id AND file_id<>$file_id AND file_name='$_POST[name]' AND parent_file_id=0 ORDER BY file_id DESC LIMIT 1";
			$result = mysql_query($sql, $db);
			if ($row = mysql_fetch_assoc($result)) {
				if ($_config['fs_versioning']) {
					$sql = "UPDATE ".TABLE_PREFIX."files SET parent_file_id=$file_id, date=date WHERE file_id=$row[file_id]";
					$result = mysql_query($sql, $db);

					$sql = "UPDATE ".TABLE_PREFIX."files SET num_revisions=$row[num_revisions]+1, date=date WHERE file_id=$file_id";
					$result = mysql_query($sql, $db);
				} else {
					fs_delete_file($row['file_id'], $owner_type, $owner_id);
				}
			}

			if ($_POST['comment']){
				$sql = "INSERT INTO ".TABLE_PREFIX."files_comments VALUES (NULL, $file_id, $_SESSION[member_id], NOW(), '{$_POST['comment']}')";
				mysql_query($sql, $db);
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