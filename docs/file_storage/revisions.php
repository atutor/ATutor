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
// $Id: revisions.php 5923 2006-03-02 17:10:44Z joel $

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/filemanager.inc.php'); // for get_human_size()
require(AT_INCLUDE_PATH.'lib/file_storage.inc.php');


if (isset($_GET['download'], $_GET['revision'])) {
	header('Location: index.php?download=1'.SEP.'files'.urlencode('[]').'='.$_GET['revision']);
	exit;
} else if (isset($_GET['delete'], $_GET['revision'])) {
	header('Location: delete_revision.php?id='.$_GET['revision']);
	exit;
} else if (isset($_GET['cancel'])) {
	header('Location: index.php?folder='.$_GET['folder']);
	exit;
} else if (isset($_GET['comments'])) {
	header('Location: comments.php?id='.$_GET['revision']);
	exit;
}


require(AT_INCLUDE_PATH.'header.inc.php');

$id = abs($_GET['id']);

$files = fs_get_revisions($id);
$current_file = current($files);
?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
<input type="hidden" name="folder" value="<?php echo $folder_id; ?>" />
<div style="margin: 0px auto; width: 70%">
	<div class="input-form" style="float: left; width: 48%">
		<div class="row">
			<h3><a onclick="javascript:document.getElementById('upload').style.display='';">Upload File</a></h3>
		</div>
		<div style="display: none;" name="upload" id="upload">
			<div class="row">
				<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="file"><?php echo _AT('file'); ?></label><br />
				<input type="file" name="file" id="file" />
			</div>
			<div class="row">
				<label for="comments"><?php echo _AT('notes'); ?></label><br />
				<textarea name="comments" id="comments" rows="1" cols="20"></textarea>
			</div>
			<div class="row buttons">
				<input type="submit" name="upload" value="<?php echo _AT('upload'); ?>" />
			</div>
		</div>
	</div>
</div>
</form>
<div style="clear: both;"></div>

<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
<input type="hidden" name="folder" value="<?php echo $current_file['folder_id']; ?>" />
<table class="data">
<thead>
<tr>
	<th>&nbsp;</th>
	<th><?php echo _AT('revision');  ?></th>
	<th><?php echo _AT('file_name'); ?></th>
	<th><?php echo _AT('date');      ?></th>
	<th><?php echo _AT('author');    ?></th>
	<th><?php echo _AT('comments');  ?></th>
	<th><?php echo _AT('size');      ?></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="7">
		<input type="submit" name="download" value="<?php echo _AT('download'); ?>" />
		<input type="submit" name="comments" value="<?php echo _AT('comments'); ?>" />
		<input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</td>
</tr>
</tfoot>
<tbody>
<?php foreach ($files as $file): ?>
	<tr onmousedown="document.form['r<?php echo $file['file_id']; ?>'].checked = true; rowselect(this);" id="r_<?php echo $file['file_id']; ?>_0">
		<td valign="top"><input type="radio" name="revision" value="<?php echo $file['file_id']; ?>" id="r<?php echo $file['file_id']; ?>" /></td>
		<td valign="top"><?php echo $file['num_revisions']; ?></td>
		<td valign="top">
				<?php echo $file['file_name']; ?>
				<?php if ($file['comments']): ?>
					<p><?php echo nl2br($file['comments']); ?></p>
				<?php endif; ?>
		</td>
		<td valign="top"><?php echo $file['date']; ?></td>
		<td valign="top"><?php echo get_login($file['member_id']); ?></td>
		<td valign="top"><?php echo $file['num_comments']; ?></td>
		<td valign="top"><?php echo get_human_size($file['file_size']); ?></td>
	</tr>
<?php endforeach; ?>
</tbody>
</table>
</form>


<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>