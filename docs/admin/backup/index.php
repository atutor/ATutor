<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg & Heidi Hazelton	*/
/* Adaptive Technology Resource Centre / University of Toronto				*/
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/
// $Id: index.php 1748 2004-10-04 14:27:50Z joel $

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

$page = 'backups';
$_user_location = 'admin';

require(AT_INCLUDE_PATH.'classes/Backup/Backup.class.php');
require(AT_INCLUDE_PATH.'lib/filemanager.inc.php');

if (isset($_POST['backup_id'])) {
	$ids = explode('_', $_POST['backup_id']);
	$backup_id = $ids[0];
	$course_id = $ids[1];
}

if (isset($_POST['restore']) && isset($backup_id)) {
	header('Location: restore.php?backup_id=' . $backup_id . SEP . 'course_id=' . $course_id);
	exit;

} else if (isset($_POST['download']) && isset($backup_id)) {
	$Backup =& new Backup($db, $course_id);
	$Backup->download($backup_id);
	exit; // never reached

} else if (isset($_POST['delete']) && isset($backup_id)) {
	header('Location: delete.php?backup_id=' . $backup_id . SEP . 'course_id=' . $course_id);
	exit;
	// $f[] = AT_FEEDBACK_BACKUP_DELETED;

} else if (isset($_POST['edit']) && isset($backup_id)) {
	header('Location: edit.php?backup_id=' . $backup_id . SEP . 'course_id=' . $course_id);
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');
echo '<h3>Backups</h3>';
require(AT_INCLUDE_PATH.'html/feedback.inc.php');
?>

<form name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data" onsubmit="">

<p align="center"><strong><a href="admin/backup/create.php">Create</a> | <a href="admin/backup/upload.php">Upload</a></strong></p>

<table cellspacing="1" cellpadding="0" border="0" class="bodyline" width="95%" summary="" align="center">
	<tr>
		<th class="row1"><?php echo _AT('file_name'); ?></th>
		<th class="row1"><?php echo _AT('date_created'); ?></th>
		<th class="row1"><?php echo _AT('file_size'); ?></th>
		<th class="row1"><?php echo _AT('description'); ?></th>
	</tr>
	<tr><td height="1" class="row2" colspan="4"></td></tr>
<?php
	$Backup =& new Backup($db);

	$sql	= "SELECT course_id, title FROM ".TABLE_PREFIX."courses ORDER BY title";
	$result = mysql_query($sql, $db);
	while ($course = mysql_fetch_assoc($result)) {

		$Backup->setCourseID($course['course_id']);
		$list = $Backup->getAvailableList($course['course_id']);

		echo '<tr><td class="row1" colspan="4"><span style="font-size:x-small; font-weight:bold;">'.$course['title'].'</span></td></tr>';
		echo '<tr><td height="1" class="row2" colspan="4"></td></tr>';

		foreach ($list as $row) {
			echo '<tr><td class="row1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="radio" value="'.$row['backup_id'].'_'.$row['course_id'].'" name="backup_id" id="'.$row['backup_id'].'" />';
			echo '<label for="'.$row['backup_id'].'">'.Backup::generateFileName($row['course_id'], $row['date_timestamp']).'</label></td>';
			echo '<td class="row1">'.AT_date(_AT('filemanager_date_format'), $row['date_timestamp'], AT_DATE_UNIX_TIMESTAMP).'</td>';
			echo '<td class="row1" align="right">'.get_human_size($row['file_size']).'</td>';
			echo '<td class="row1">'.$row['description'].'</td>';
			echo '</tr>';
			echo '<tr><td height="1" class="row2" colspan="4"></td></tr>';
		}
	}

?>
	<tr><td height="1" class="row2" colspan="4"></td></tr>
	<tr>
		<td class="row1" align="center" colspan="4">
			<br /><input type="submit" name="restore" value="<?php echo _AT('restore'); ?>" class="button" /> | 
				  <input type="submit" name="download" value="<?php echo _AT('download'); ?>" class="button" /> | 
				  <input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" class="button" /> | 
				  <input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" class="button" /><br /><br />
		</td>
	</tr>
	</table>
</form>

<?php require (AT_INCLUDE_PATH.'footer.inc.php');  ?>