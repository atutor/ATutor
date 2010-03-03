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
// $Id: edit.php 8237 2008-11-18 16:42:18Z hwong $

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
	$_POST['id'] = abs($_POST['id']);


	if (!$_POST['name']) {
		$msg->addError('MISSING_FILENAME');
	}

	if (!$msg->containsErrors()) {
		$_POST['name'] = $addslashes($_POST['name']);
		$_POST['comment'] = $addslashes(trim($_POST['comment']));
		$_POST['description'] = $addslashes(trim($_POST['description']));
		$_POST['body'] = $stripslashes($_POST['body']); // saved to disk not db so no need to escape.
		$original_file = fs_get_file_path($_POST['id']);
		$folder = abs($_POST['folder']);

		if (!$_POST['edit'] || (file_get_contents($original_file . $_POST['id']) == $_POST['body'])) {
			// file is not editable ,or it is editable but no changes made.
			// only add the comment (if any) and the file name

			$num_comments = 0;
			
			if ($_POST['comment']){
				$sql = "INSERT INTO ".TABLE_PREFIX."files_comments VALUES (NULL, $_POST[id], $_SESSION[member_id], NOW(), '{$_POST['comment']}')";
				mysql_query($sql, $db);

				$num_comments = 1;
			}

			$sql = "UPDATE ".TABLE_PREFIX."files SET file_name='$_POST[name]', description='$_POST[description]', num_comments=num_comments+$num_comments, date=date WHERE file_id=$_POST[id] AND owner_type=$owner_type AND owner_id=$owner_id";
			mysql_query($sql, $db);
		} else {
			// this file is editable, and has changed

			$size = strlen($_POST['body']);

			if ($_POST['comment']) {
				$num_comments = 1;
			} else {
				$num_comments = 0;
			}
			$sql = "SELECT * FROM ".TABLE_PREFIX."files WHERE file_id=$_POST[id] AND owner_type=$owner_type AND owner_id=$owner_id";
			$result = mysql_query($sql, $db);
			$row = mysql_fetch_assoc($result);

			if ($_config['fs_versioning']) {
				$sql = "INSERT INTO ".TABLE_PREFIX."files VALUES (NULL, {$row['owner_type']}, {$row['owner_id']}, $_SESSION[member_id], {$row['folder_id']}, 0, NOW(), $num_comments, {$row['num_revisions']}+1, '{$_POST['name']}', $size, '$_POST[description]')";
				$result = mysql_query($sql, $db);

				$file_id = mysql_insert_id($db);

				$file_path = fs_get_file_path($file_id);
				if ($fp = fopen($file_path . $file_id, 'wb')) {
					ftruncate($fp, 0);
					fwrite($fp, $_POST['body'], $size);
					fclose($fp);

					$sql = "UPDATE ".TABLE_PREFIX."files SET parent_file_id=$file_id, date=date WHERE file_id=$_POST[id] AND owner_type=$owner_type AND owner_id=$owner_id";
					$result = mysql_query($sql, $db);

					if ($_POST['comment']){
						$sql = "INSERT INTO ".TABLE_PREFIX."files_comments VALUES (NULL, $file_id, $_SESSION[member_id], NOW(), '{$_POST['comment']}')";
						mysql_query($sql, $db);
					}
				}
			} else {
				$file_path = fs_get_file_path($_POST['id']);
				if ($fp = fopen($file_path . $_POST['id'], 'wb')) {
					ftruncate($fp, 0);
					fwrite($fp, $_POST['body'], $size);
					fclose($fp);
				}
			}
		}
		$msg->addFeedback('FILE_EDITED_SUCCESSFULLY');
		header('Location: '.url_rewrite('mods/_standard/file_storage/index.php'.$owner_arg_prefix.'folder='.$folder, AT_PRETTY_URL_IS_HEADER));
		exit;
	}

	$_GET['id'] = $_POST['id'];
}

$onload = 'document.form.name.focus();';

require(AT_INCLUDE_PATH.'header.inc.php');

if (($_POST['setvisual'] && !$_POST['settext']) || $_GET['setvisual']) {
	require(AT_INCLUDE_PATH.'lib/tinymce.inc.php');

	load_editor(false, 'body');
}

$id = abs($_REQUEST['id']);

$sql = "SELECT file_name, folder_id, description FROM ".TABLE_PREFIX."files WHERE file_id=$id AND owner_type=$owner_type AND owner_id=$owner_id";
$result = mysql_query($sql, $db);
if (!$row = mysql_fetch_assoc($result)) {
	$msg->printErrors('FILE_NOT_EXIST');
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}
if (isset($_POST['description'])) {
	$row['description'] = $stripslashes($_POST['description']);
	$row['file_name']   = $stripslashes($_POST['name']);
	$row['comment']     = $stripslashes($_POST['comment']);
	$_POST['body']      = $stripslashes($_POST['body']);
}
$ext = fs_get_file_extension($row['file_name']);
$file_path = fs_get_file_path($id);
?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF'] . $owner_arg_prefix; ?>" name="form">
<input type="hidden" name="id" value="<?php echo $id; ?>" />
<input type="hidden" name="folder" value="<?php echo $row['folder_id']; ?>" />
<input type="submit" name="submit" style="display:none;"/>
<div class="input-form">
	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="name"><?php echo _AT('file_name'); ?></label><br />
		<input type="text" name="name" id="name" value="<?php echo htmlspecialchars($row['file_name']); ?>" size="40" maxlength="70" />
	</div>

	<div class="row">
		<label for="description"><?php echo _AT('description'); ?></label><br />
		<textarea name="description" id="description" cols="30" rows="2"><?php echo htmlspecialchars($row['description']); ?></textarea>
	</div>

	<?php if (in_array($ext, $editable_file_types)): ?>
		<input type="hidden" name="edit" value="1" />
		<div class="row">
			<label for="comment"><?php echo _AT('revision_comment'); ?></label><br />
			<textarea name="comment" id="comment" cols="30" rows="2"><?php echo htmlspecialchars($row['comment']); ?></textarea>
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
			<label for="body"><?php echo _AT('contents'); ?></label><br />
			<textarea name="body" id="body" cols="30" rows="20"><?php
				if (isset($_POST['body'])) {
					echo $_POST['body'];
				} else {
					echo htmlspecialchars(file_get_contents($file_path . $id));
				}
				?></textarea>
		</div>
	<?php else: ?>
		<div class="row">
			<label for="comment"><?php echo _AT('revision_comment'); ?></label><br />
			<textarea name="comment" id="comment" cols="30" rows="2"></textarea>
		</div>
		<div class="row">
			<?php echo _AT('contents'); ?><br />
			<?php echo _AT('not_editable'); ?>
			<br />
			<?php if (in_array($ext, array('gif', 'jpg','jpeg'))): ?>
				<img src="mods/_standard/file_storage/index.php<?php echo $owner_arg_prefix; ?>download=1<?php echo SEP; ?>files<?php echo urlencode('[]').'='.$id; ?>" alt="" title="" />

			<?php elseif ($ext == 'swf'): ?>
				<object type="application/x-shockwave-flash" data="mods/_standard/file_storage/index.php<?php echo $owner_arg_prefix; ?>download=1<?php echo SEP; ?>files<?php echo urlencode('[]').'='.$id; ?>" width="550" height="400"><param name="movie" value="mods/_standard/file_storage/index.php<?php echo $owner_arg_prefix; ?>download=1<?php echo SEP; ?>files<?php echo urlencode('[]').'='.$id; ?>" /></object>

			<?php elseif ($ext == 'mov'): ?>
				<object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" width="550" height="400" codebase="http://www.apple.com/qtactivex/qtplugin.cab"><param name="src" value="file_storage/index.php<?php echo $owner_arg_prefix; ?>download=1<?php echo SEP; ?>files<?php echo urlencode('[]').'='.$id; ?>" /><param name="autoplay" value="true" /><param name="controller" value="true" /><embed src="file_storage/index.php<?php echo $owner_arg_prefix; ?>download=1<?php echo SEP; ?>files<?php echo urlencode('[]').'='.$id; ?>" width="550" height="400" controller="true" pluginspage="http://www.apple.com/quicktime/download/"></embed></object>

			<?php elseif ($ext == 'mp3'): ?>
				<object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" width="200" height="15" codebase="http://www.apple.com/qtactivex/qtplugin.cab"><param name="src" value="mods/_standard/file_storage/index.php<?php echo $owner_arg_prefix; ?>download=1<?php echo SEP; ?>files<?php echo urlencode('[]').'='.$id; ?>" /><param name="autoplay" value="false" /><embed src="mods/_standard/file_storage/index.php<?php echo $owner_arg_prefix; ?>download=1<?php echo SEP; ?>files<?php echo urlencode('[]').'='.$id; ?>" width="200" height="15" autoplay="false" pluginspage="http://www.apple.com/quicktime/download/"></embed></object>

			<?php elseif (in_array($ext, array('wav', 'au'))): ?>
				<embed src="mods/_standard/file_storage/index.php<?php echo $owner_arg_prefix; ?>download=1<?php echo SEP; ?>files<?php echo urlencode('[]').'='.$id; ?>" autostart="false" width="145" height="60"><noembed><bgsound src="mods/_standard/file_storage/index.php<?php echo $owner_arg_prefix; ?>download=1<?php echo SEP; ?>files<?php echo urlencode('[]').'='.$id; ?>"></noembed></embed>

			<?php endif; ?>
		</div>

		<input type="hidden" name="edit" value="0" />
	<?php endif; ?>
	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>