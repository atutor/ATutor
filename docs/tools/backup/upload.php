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

authenticate(AT_PRIV_ADMIN); 

$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/';
$_section[1][0] = _AT('backup_manager');
$_section[1][1] = 'tools/backup/index.php';
$_section[2][0] = _AT('upload_backup');

$_SESSION['done'] = 0;
session_write_close();

require(AT_INCLUDE_PATH.'classes/Backup/Backup.class.php');

if (isset($_POST['cancel'])) {
	header('Location: index.php?f=' . AT_FEEDBACK_CANCELLED);
	exit;
} else if (isset($_POST['upload'])) {
	$Backup =& new Backup($db, $_SESSION['course_id']);
	$errors = $Backup->upload($_FILES, $_POST['description']);

	if (!empty($errors)) {
		require(AT_INCLUDE_PATH.'header.inc.php');
		print_errors($errors);
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	} else {
		header('Location: index.php?f=');
		exit;
	}
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
	echo '<a href="tools/backup/index.php" class="hide" >'._AT('backup_manager').'</a>';
}
echo '</h3>';


require(AT_INCLUDE_PATH.'html/feedback.inc.php');
?>
<h4>Upload</h4>
<form name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
<table cellspacing="1" cellpadding="0" border="0" width="95%" summary="" align="center" class="bodyline">
	<tr>
		<td class="row1" colspan="2">To upload a backup from a file, enter a description of the backup, choose a file to upload, and select the "Upload" button.</td>
	</tr>
	<tr><td height="1" class="row2" colspan="3"></td></tr>
	<tr>
		<td class="row1" align="right"><label for="descrip"><strong>Optional Description:</strong></label></td>
		<td class="row1" align="left"> <textarea id="descrip" cols="30" rows="2" class="formfield" name="description"><?php echo $backup_row['description']; ?></textarea></td>
	</tr>
	<tr><td height="1" class="row2" colspan="3"></td></tr>
	<tr>
		<td class="row1" align="right"><label for="file"><strong>File:</strong></label></td>
		<td class="row1" align="left"><input type="file" name="file" id="file" class="formfield" /></td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr><td colspan="2" height="1" class="row2" colspan="3"></td></tr>

	<tr><td class="row1" align="center" colspan="2">
		<input type="submit" name="upload" value="<?php echo _AT('upload'); ?>" class="button" /> | <input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" class="button" />
		</p>
		</td>
	</tr>
</table>
</form>

<?php require (AT_INCLUDE_PATH.'footer.inc.php');  ?>