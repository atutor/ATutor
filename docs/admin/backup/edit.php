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
// $Id: index.php 1715 2004-09-30 14:18:46Z heidi $

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

$page = 'backups';
$_user_location = 'admin';

require(AT_INCLUDE_PATH.'classes/Backup/Backup.class.php');

$_SESSION['done'] = 0;
session_write_close();

if (isset($_POST['cancel'])) {
	header('Location: index.php?f=' . AT_FEEDBACK_CANCELLED);
	exit;
} 

$Backup =& new Backup($db, $_REQUEST['course_id']);
$backup_row = $Backup->getRow($_REQUEST['backup_id']);

if (isset($_POST['edit'])) {
	$Backup->edit($_POST['backup_id'], $_POST['new_description']);
	header('Location: index.php?f='.FEEDBACK);
	exit;
} 

//check for errors

require(AT_INCLUDE_PATH.'header.inc.php');

echo '<h3>Backups</h3><br />';

?>
<h4>Edit <?php echo Backup::generateFileName($_REQUEST['course_id'], $backup_row['date_timestamp']); ?></h4>

<form name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data" onsubmit="">
<input type="hidden" name="backup_id" value="<?php echo $_GET['backup_id']; ?>" />
<input type="hidden" name="course_id" value="<?php echo $_GET['course_id']; ?>" />
<table cellspacing="1" cellpadding="0" border="0" width="95%" summary="" align="center" class="bodyline">
	<tr>
		<td class="row1" colspan="2">Enter a new description for this backup, then select the "Edit" button.</td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>

	<tr><td class="row1" align="right">Description:</td>
		<td class="row1" align="left"><textarea cols="30" rows="2" class="formfield" name="new_description"><?php echo $backup_row['description']; ?></textarea></td>
	</tr>

	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr><td class="row1" align="center" colspan="2">
		<br /><input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" class="button" /> | <input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" class="button" />
		</p>
		</td>
	</tr>
</table>
</form>
<?php require (AT_INCLUDE_PATH.'footer.inc.php');  ?>