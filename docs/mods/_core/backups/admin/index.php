<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton	*/
/* Adaptive Technology Resource Centre / University of Toronto				*/
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/
// $Id: index.php 9081 2010-01-13 20:26:03Z cindy $

define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_BACKUPS);

require(AT_INCLUDE_PATH.'../mods/_core/backups/classes/Backup.class.php');
require(AT_INCLUDE_PATH.'../mods/_core/file_manager/filemanager.inc.php');

if (isset($_POST['backup_id'])) {
	$ids = explode('_', $_POST['backup_id']);
	$backup_id = $ids[0];
	$course    = $ids[1];
}

if (isset($_POST['restore'], $backup_id)) {
	header('Location: restore.php?backup_id=' . $backup_id . SEP . 'course=' . $course);
	exit;

} else if (isset($_POST['download'], $backup_id)) {
	$Backup = new Backup($db, $course);
	$Backup->download($backup_id);
	exit; // never reached

} else if (isset($_POST['delete'], $backup_id)) {
	header('Location: delete.php?backup_id=' . $backup_id . SEP . 'course=' . $course);
	exit;

} else if (isset($_POST['edit'], $backup_id)) {
	header('Location: edit.php?backup_id=' . $backup_id . SEP . 'course=' . $course);
	exit;
} else if (!empty($_POST) && !$backup_id) {
	$msg->addError('NO_ITEM_SELECTED');
}


require(AT_INCLUDE_PATH.'header.inc.php');

?>
<form name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="course" value="<?php echo $_REQUEST['course']; ?>" />
	
<table class="data" summary="" rules="groups" style="width: 90%">
<thead>
	<tr>
		<th><?php echo _AT('file_name');    ?></th>
		<th><?php echo _AT('date_created'); ?></th>
		<th><?php echo _AT('file_size');    ?></th>
		<th><?php echo _AT('description');  ?></th>
	</tr>
</thead>
<tfoot>
<tr>
	<td colspan="4"><input type="submit" name="restore" value="<?php echo _AT('restore'); ?>" /> 
				  <input type="submit" name="download" value="<?php echo _AT('download'); ?>" />  
				  <input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" /> 
				  <input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" /></td>
</tr>
</tfoot>
<tbody>
<?php
	$num_backups = 0;
	$Backup = new Backup($db);

	if (isset($_REQUEST['course']) && $_REQUEST['course']) {
		$course = intval($_REQUEST['course']);
		$sql	= "SELECT course_id, title FROM ".TABLE_PREFIX."courses WHERE course_id=$course ORDER BY title";
	} else {
		$sql	= "SELECT course_id, title FROM ".TABLE_PREFIX."courses ORDER BY title";
	}
	$result = mysql_query($sql, $db);
	while ($course = mysql_fetch_assoc($result)) {

		$Backup->setCourseID($course['course_id']);
		$list = $Backup->getAvailableList();

		echo '<tr><th colspan="4">'.$course['title'].'</th></tr>';

		if (empty($list)) { ?>
			<tr>
				<td colspan="4"><?php echo _AT('none_found'); ?></td>
			</tr><?php
			$num_backups ++;

		} else {

			foreach ($list as $row) {
				echo '<tr onmousedown="document.form1[\'c'.$row['backup_id'].'_'.$row['course_id'].'\'].checked = true; rowselect(this);" id="r_'.$row['backup_id'].'"><td><input type="radio" value="'.$row['backup_id'].'_'.$row['course_id'].'" name="backup_id" id="c'.$row['backup_id'].'_'.$row['course_id'].'" />';
				echo '<label for="c'.$row['backup_id'].'_'.$row['course_id'].'">'.$row['file_name'].'</label></td>';
				echo '<td>'.AT_date(_AT('filemanager_date_format'), $row['date_timestamp'], AT_DATE_UNIX_TIMESTAMP).'</td>';
				echo '<td align="right">'.get_human_size($row['file_size']).'</td>';
				echo '<td>'.$row['description'].'</td>';
				echo '</tr>';
				$num_backups ++;
			}
		}
	}
?>
<?php if (!$num_backups): ?>
	<tr>
		<td colspan="4"><?php echo _AT('none_found'); ?></td>
	</tr>
<?php endif; ?>
</tbody>
</table>
</form>

<?php require (AT_INCLUDE_PATH.'footer.inc.php');  ?>