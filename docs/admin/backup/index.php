<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton	*/
/* Adaptive Technology Resource Centre / University of Toronto				*/
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
if ($_SESSION['course_id'] > -1) { exit; }

$page = 'backups';
$_user_location = 'admin';

require(AT_INCLUDE_PATH.'classes/Backup/Backup.class.php');
require(AT_INCLUDE_PATH.'lib/filemanager.inc.php');

if (isset($_POST['backup_id'])) {
	$ids = explode('_', $_POST['backup_id']);
	$backup_id = $ids[0];
	$course_id = $ids[1];
}

if (isset($_POST['restore'])) {
	if (!isset($backup_id)) {
		$msg->addError('DID_NOT_SELECT_A_BACKUP');
	}
	else {
		header('Location: restore.php?backup_id=' . $backup_id . SEP . 'course_id=' . $course_id);
		exit;
	}

} else if (isset($_POST['download'])) {
	if (!isset($backup_id)) {
		$msg->addError('DID_NOT_SELECT_A_BACKUP');
	}
	else {
		$Backup =& new Backup($db, $course_id);
		$Backup->download($backup_id);
		exit; // never reached
	}

} else if (isset($_POST['delete'])) {
	if (!isset($backup_id)) {
		$msg->addError('DID_NOT_SELECT_A_BACKUP');
	}
	else {
		header('Location: delete.php?backup_id=' . $backup_id . SEP . 'course_id=' . $course_id);
		exit;
	}

} else if (isset($_POST['edit'])) {
	if (!isset($backup_id)) {
		$msg->addError('DID_NOT_SELECT_A_BACKUP');
	}
	else {
		header('Location: edit.php?backup_id=' . $backup_id . SEP . 'course_id=' . $course_id);
		exit;
	}
}

require(AT_INCLUDE_PATH.'header.inc.php');

$msg->printAll();
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
<?php
	$Backup =& new Backup($db);

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

		echo '<tbody>';
		echo '<tr><th colspan="4">'.$course['title'].'</h4></th></tr>';

		if (empty($list)) { ?>
			<tr>
				<td colspan="4"><?php echo _AT('none_found'); ?></td>
			</tr><?php
		} else {

			foreach ($list as $row) {
				echo '<tr onmousedown="document.form1[\'c'.$row['backup_id'].'_'.$row['course_id'].'\'].checked = true;"><td><input type="radio" value="'.$row['backup_id'].'_'.$row['course_id'].'" name="backup_id" id="c'.$row['backup_id'].'_'.$row['course_id'].'"/>';
				echo '<label for="c'.$row['backup_id'].'_'.$row['course_id'].'">'.$row['file_name'].'</label></small></td>';
				echo '<td>'.AT_date(_AT('filemanager_date_format'), $row['date_timestamp'], AT_DATE_UNIX_TIMESTAMP).'</td>';
				echo '<td align="right">'.get_human_size($row['file_size']).'</td>';
				echo '<td>'.$row['description'].'</td>';
				echo '</tr>';
			}
		}
		echo '</tbody>';
	}
?>
</table>

</form>

<?php require (AT_INCLUDE_PATH.'footer.inc.php');  ?>