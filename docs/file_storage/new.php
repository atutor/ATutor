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
// $Id: new.php 5866 2005-12-15 16:16:03Z joel $

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/file_storage.inc.php');

$owner_type = abs($_REQUEST['ot']);
$owner_id   = abs($_REQUEST['oid']);
$owner_arg_prefix = '?ot='.$owner_type.SEP.'oid='.$owner_id. SEP;
if (!($owner_status = fs_authenticate($owner_type, $owner_id)) || !query_bit($owner_status, WORKSPACE_AUTH_WRITE)) { 
	exit('NOT AUTHENTICATED');
}

if (isset($_POST['cancel'])) {

} else if (isset($_POST['submit'])) {
	$_POST['comments'] = trim($_POST['comments']);
	$_POST['name'] = trim($_POST['name']);

	$parent_folder_id = abs($_POST['folder']);

	// check that we own this folder
	if ($parent_folder_id) {
		$sql = "SELECT folder_id FROM ".TABLE_PREFIX."folders WHERE folder_id=$parent_folder_id AND owner_type=$owner_type AND owner_id=$owner_id";
		$result = mysql_query($sql, $db);
		if (!$row = mysql_fetch_assoc($result)) {
			exit('not authenticated');
		}
	}

	if (!$_POST['name']) {
		$msg->addError('NEED_FILENAME');
	}

	if (!$msg->containsErrors()) {
		$_POST['comments'] = $addslashes($_POST['comments']);
		$_POST['name'] = $addslashes($_POST['name']);
		$_POST['body'] = stripslashes($addslashes($_POST['body']));

		if ($_POST['comments']) {
			$num_comments = 1;
		} else {
			$num_comments = 0;
		}

		$size = strlen($_POST['body']);
		$sql = "INSERT INTO ".TABLE_PREFIX."files VALUES (0, $owner_type, $owner_id, $_SESSION[member_id], $parent_folder_id, 0, NOW(), $num_comments, 0, '$_POST[name]',$size)";
		$result = mysql_query($sql, $db);

		if ($result && $file_id = mysql_insert_id($db)) {
			$file_path = fs_get_file_path($file_id) . $file_id;
			$fp = fopen($file_path, 'wb');
			fwrite($fp, $_POST['body'], $size);
			fclose($fp);

			// check if this file name already exists
			$sql = "SELECT file_id, num_revisions FROM ".TABLE_PREFIX."files WHERE owner_type=$owner_type AND owner_id=$owner_id AND folder_id=$parent_folder_id AND file_id<>$file_id AND file_name='$_POST[name]' AND parent_file_id=0 ORDER BY file_id DESC LIMIT 1";
			$result = mysql_query($sql, $db);
			if ($row = mysql_fetch_assoc($result)) {
				if ($_config['fs_versioning']) {
					$sql = "UPDATE ".TABLE_PREFIX."files SET parent_file_id=$file_id WHERE file_id=$row[file_id]";
					$result = mysql_query($sql, $db);

					$sql = "UPDATE ".TABLE_PREFIX."files SET num_revisions=$row[num_revisions]+1 WHERE file_id=$file_id";
					$result = mysql_query($sql, $db);
				} else {
					fs_delete_file($row['file_id'], $owner_type, $owner_id);
				}
			}

			if ($_POST['comments']){
				$sql = "INSERT INTO ".TABLE_PREFIX."files_comments VALUES (0, $file_id, $_SESSION[member_id], NOW(), '{$_POST['comments']}')";
				mysql_query($sql, $db);
			}

			$msg->addFeedback(array('FILE_SAVED', $_POST['name']));
			header('Location: index.php'.$owner_arg_prefix.'folder='.$parent_folder_id);
			exit;
		}
	}
}

require(AT_INCLUDE_PATH.'header.inc.php');

?>
<form action="<?php echo $_SERVER['PHP_SELF'] . $owner_arg_prefix; ?>" method="post" name="form">
<input type="hidden" name="folder" value="<?php echo abs($_REQUEST['folder']); ?>" />
<div class="input-form">
	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="name"><?php echo _AT('file_name'); ?></label><br />
		<input type="text" name="name" id="name" value="<?php echo htmlspecialchars($_POST['name']); ?>" size="40" maxlength="70" />
	</div>

	<div class="row">
		<label for="comment"><?php echo _AT('revision_comment'); ?></label><br />
		<textarea name="comment" id="comment" cols="30" rows="2"></textarea>
	</div>

	<div class="row">
		<label for="body"><?php echo _AT('contents');  ?></label><br />
		<textarea name="body" id="body" rows="25"><?php echo htmlspecialchars($_POST['body']); ?></textarea>
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>"  />		
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>