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
// $Id$

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/';
$_section[1][0] = _AT('backup_manager');
$_section[1][1] = 'tools/';

authenticate(AT_PRIV_ADMIN);

require(AT_INCLUDE_PATH.'classes/Backup/Backup.class.php');
require(AT_INCLUDE_PATH.'lib/filemanager.inc.php');
require_once(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

global $savant;
$msg =& new Message($savant);

if (isset($_POST['restore']) && isset($_POST['backup_id'])) {
	header('Location: restore.php?backup_id=' . $_POST['backup_id']);
	exit;

} else if (isset($_POST['download']) && isset($_POST['backup_id'])) {
	$Backup =& new Backup($db, $_SESSION['course_id']);
	$Backup->download($_POST['backup_id']);
	exit; // never reached

} else if (isset($_POST['delete']) && isset($_POST['backup_id'])) {
	header('Location: delete.php?backup_id=' . $_POST['backup_id']);
	exit;

} else if (isset($_POST['edit']) && isset($_POST['backup_id'])) {
	header('Location: edit.php?backup_id=' . $_POST['backup_id']);
	exit;
} else if (isset($_POST['backup_id'])) {
	//$msg->addError('DID_NOT_SELECT_A_BACKUP');
}

require(AT_INCLUDE_PATH.'header.inc.php');

	echo '<h2>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '<img src="images/icons/default/square-large-tools.gif" border="0" vspace="2" class="menuimageh2" width="42" height="40" alt="" />';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo ' <a href="tools/" class="hide" >'._AT('tools').'</a>';
	}
	echo '</h2>';


	echo '<h3>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '&nbsp;<img src="images/icons/default/backups-large.gif" class="menuimageh3" width="42" height="38" alt="" /> ';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo _AT('backup_manager');
	}
	echo '</h3>';
	
$msg->printAll();
?>

<form name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">

<p align="center"><strong><a href="tools/backup/create.php"><?php echo _AT('create'); ?></a> | <a href="tools/backup/upload.php"><?php echo _AT('upload'); ?></a></strong></p>

<table cellspacing="1" cellpadding="0" border="0" class="bodyline" width="95%" summary="" align="center">
	<tr>
		<th class="cat"><?php echo _AT('file_name'); ?></th>
		<th class="cat"><?php echo _AT('date_created'); ?></th>
		<th class="cat"><?php echo _AT('file_size'); ?></th>
		<th class="cat"><?php echo _AT('description'); ?></th>
	</tr>
	<tr><td height="1" class="row2" colspan="4"></td></tr>
<?php
	$Backup =& new Backup($db, $_SESSION['course_id']);

	$list = $Backup->getAvailableList();

	if (!$list) {
		?>
	<tr>
		<td class="row1" align="center" colspan="4"><small><?php echo _AT('none_found'); ?></small></td>
	</tr>
	<?php
	} else {
		foreach ($list as $row) {
			echo '<tr>';
			echo '<td class="row1"><label><input type="radio" value="'.$row['backup_id'].'" name="backup_id" />';
			echo '<small>'.$row['file_name'].'</small></label></td>';
			echo '<td class="row1"><small>'.AT_date(_AT('filemanager_date_format'), $row['date_timestamp'], AT_DATE_UNIX_TIMESTAMP).'</small></td>';
			echo '<td class="row1" align="right"><small>'.get_human_size($row['file_size']).'</small></td>';
			echo '<td class="row1"><small>'.$row['description'].'</small></td>';
			echo '</tr>';
			echo '<tr><td height="1" class="row2" colspan="4"></td></tr>';
		}
?>
	<tr><td height="1" class="row2" colspan="4"></td></tr>
	<tr>
		<td class="row1" align="center" colspan="4">
			<br /><input type="submit" name="restore" value="<?php echo _AT('restore'); ?>" class="button" /> - 
				  <input type="submit" name="download" value="<?php echo _AT('download'); ?>" class="button" /> - 
				  <input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" class="button" /> - 
				  <input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" class="button" /><br /><br />
		</td>
	</tr>
	<?php } ?>
	</table>
</form>

<?php require (AT_INCLUDE_PATH.'footer.inc.php');  ?>