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

$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/index.php';
$_section[1][0] = _AT('backup_course');
$_section[1][1] = 'tools/backup/index.php';
$_section[2][0] = _AT('create_backup');

$_SESSION['done'] = 0;
session_write_close();

if (isset($_POST['cancel'])) {
	header('Location: index.php');
	exit;
} else if (isset($_POST['create'])) {
	//make backup of current course
	//add entry to at_backups table w/ $_POST['description']

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
		echo '<a href="tools/backup/index.php" class="hide" >'._AT('backup_course').'</a>';
	}
	echo '</h3>';

	if (!authenticate(AT_PRIV_ADMIN, AT_PRIV_RETURN)) {
		require (AT_INCLUDE_PATH.'header.inc.php'); 
		$errors[] = AT_ERROR_NOT_OWNER;
		print_errors($errors);
		require (AT_INCLUDE_PATH.'footer.inc.php'); 
		exit;
	}

$help[] = AT_HELP_IMPORT_EXPORT;
$help[] = AT_HELP_IMPORT_EXPORT1;
require(AT_INCLUDE_PATH.'html/feedback.inc.php');
?>
<?php print_help($help);  ?>

<h4>Create Backup</h4>
<form name="form1" method="post" action="tools/backup/create_backup.php" enctype="multipart/form-data" onsubmit="">
<table cellspacing="1" cellpadding="0" border="0" width="95%" summary="" align="center" class="bodyline">
	<tr>
		<td class="row1">
		To add a backup of this course (in its current state) to the list below, enter a description for the backup and select the "Create" button.</td>
	</tr>
	<tr><td height="1" class="row2" colspan="3"></td></tr>
	<tr>
		<td class="row1">
		<p align="center"><br /><textarea cols="40" rows="2" class="formfield" id="desc" name="description" scroll="no"></textarea><br /><br />
		<input type="submit" name="create" value="<?php echo _AT('create'); ?>" class="button" /> | <input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" class="button" />
		</p>
		</td>
	</tr>
</table>
</form>

<?php require (AT_INCLUDE_PATH.'footer.inc.php');  ?>