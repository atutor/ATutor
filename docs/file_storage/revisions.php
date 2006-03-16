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
// $Id$

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/filemanager.inc.php'); // for get_human_size()
require(AT_INCLUDE_PATH.'lib/file_storage.inc.php');

$owner_type = abs($_REQUEST['ot']);
$owner_id   = abs($_REQUEST['oid']);
$owner_arg_prefix = '?ot='.$owner_type.SEP.'oid='.$owner_id. SEP;
if (!($owner_status = fs_authenticate($owner_type, $owner_id))) { exit('NOT AUTHENTICATED'); }


if (isset($_GET['download'], $_GET['revision'])) {
	header('Location: index.php'.$owner_arg_prefix.'download=1'.SEP.'files'.urlencode('[]').'='.$_GET['revision']);
	exit;
} else if (query_bit($owner_status, WORKSPACE_AUTH_WRITE) && isset($_GET['delete'], $_GET['revision'])) {
	header('Location: delete_revision.php'.$owner_arg_prefix.'id='.$_GET['revision']);
	exit;
} else if (isset($_GET['cancel'])) {
	header('Location: index.php'.$owner_arg_prefix.'folder='.$_GET['folder']);
	exit;
} else if (isset($_GET['comments'])) {
	header('Location: comments.php'.$owner_arg_prefix.'id='.$_GET['revision']);
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

$id = abs($_GET['id']);

$orders = array('asc' => 'desc', 'desc' => 'asc');

if (isset($_GET['asc'])) {
	$order = 'asc';
	$col   = $addslashes($_GET['asc']);
} else if (isset($_GET['desc'])) {
	$order = 'desc';
	$col   = $addslashes($_GET['desc']);
} else {
	// no order set
	$order = 'desc';
	$col   = 'num_revisions';
}

$files = fs_get_revisions($id, $owner_type, $owner_id, $col, $order);
$current_file = current($files);


usort($files, 'fs_revisions_sort_compare');

?>

<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
<input type="hidden" name="folder" value="<?php echo $current_file['folder_id']; ?>" />
<input type="hidden" name="ot" value="<?php echo $owner_type; ?>" />
<input type="hidden" name="oid" value="<?php echo $owner_id; ?>" />
<table class="data">
<thead>
<tr>
	<th>&nbsp;</th>
	<th><a href="<?php echo $_SERVER['PHP_SELF'] . $owner_arg_prefix . 'id='.$id.SEP.$orders[$order]; ?>=num_revisions"><?php echo _AT('revision');  ?></a></th>
	<th><a href="<?php echo $_SERVER['PHP_SELF'] . $owner_arg_prefix . 'id='.$id.SEP.$orders[$order]; ?>=file_name"><?php echo _AT('file_name'); ?></a></th>
	<th><a href="<?php echo $_SERVER['PHP_SELF'] . $owner_arg_prefix . 'id='.$id.SEP.$orders[$order]; ?>=date"><?php echo _AT('date');      ?></a></th>
	<th><?php echo _AT('author');    ?></th>
	<th><a href="<?php echo $_SERVER['PHP_SELF'] . $owner_arg_prefix . 'id='.$id.SEP.$orders[$order]; ?>=num_comments"><?php echo _AT('comments');  ?></a></th>
	<th><a href="<?php echo $_SERVER['PHP_SELF'] . $owner_arg_prefix . 'id='.$id.SEP.$orders[$order]; ?>=file_size"><?php echo _AT('size');      ?></a></th>
</tr>
<colgroup>
	<?php if ($col == 'num_revisions'): ?>
		<col />
		<col class="sort" />
		<col span="5" />
	<?php elseif($col == 'file_name'): ?>
		<col span="2" />
		<col class="sort" />
		<col span="4" />
	<?php elseif($col == 'date'): ?>
		<col span="3" />
		<col class="sort" />
		<col span="3" />
	<?php elseif($col == 'num_comments'): ?>
		<col span="4" />
		<col class="sort" />
		<col span="2" />
	<?php elseif($col == 'file_size'): ?>
		<col span="6" />
		<col class="sort" />
	<?php endif; ?>
</colgroup>
</thead>
<tfoot>
<tr>
	<td colspan="7">
		<input type="submit" name="download" value="<?php echo _AT('download'); ?>" />
		<?php if (query_bit($owner_status, WORKSPACE_AUTH_WRITE)): ?>
			<input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" />
		<?php endif; ?>
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
		<td valign="top"><a href="<?php echo 'file_storage/comments.php'.$owner_arg_prefix.'id='.$file['file_id']; ?>"><?php echo $file['num_comments']; ?></a></td>
		<td valign="top"><?php echo get_human_size($file['file_size']); ?></td>
	</tr>
<?php endforeach; ?>
</tbody>
</table>
</form>


<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>