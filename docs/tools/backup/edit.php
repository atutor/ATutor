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

require(AT_INCLUDE_PATH.'classes/Backup/Backup.class.php');

$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/';
$_section[1][0] = _AT('backup_manager');
$_section[1][1] = 'tools/backup/index.php';
$_section[2][0] = _AT('edit');

$_SESSION['done'] = 0;
session_write_close();

if (isset($_POST['cancel'])) {
	header('Location: index.php');
	exit;
} 

$Backup =& new Backup($db, $_SESSION['course_id']);

if (isset($_POST['edit'])) {
	$Backup->edit($_POST['backup_id'], $_POST['new_description']);
	header('Location: index.php?f='.FEEDBACK);
	exit;
} 

require(AT_INCLUDE_PATH.'header.inc.php');

$row = $Backup->getRow($_REQUEST['backup_id']);
//check for errors

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

?>
<h4>Edit <?php echo Backup::generateFileName($_SESSION['course_id'], $row['date_timestamp']); ?></h4>
<form name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="backup_id" value="<?php echo $_GET['backup_id']; ?>" />
<table cellspacing="1" cellpadding="0" border="0" width="95%" summary="" align="center" class="bodyline">
	<tr>
		<td class="row1" colspan="2">Enter a new description for this backup, then select the "Edit" button.</td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>

	<tr><td class="row1" align="right"><label for="description"><strong>Description:</strong></label></td>
		<td class="row1" align="left"><textarea cols="30" rows="2" class="formfield" id="description" name="new_description"><?php echo $row['description']; ?></textarea></td>
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