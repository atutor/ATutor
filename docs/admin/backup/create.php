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
$Backup =& new Backup($db, $_SESSION['course_id']);

if (isset($_POST['cancel'])) {
	header('Location: index.php?f=' . AT_FEEDBACK_CANCELLED);
	exit;
} else if (isset($_POST['submit'])) {
	//make backup of current course


	$Backup->create($_POST['description']);

	header('Location: index.php?f=');
	exit;
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

?>
<h4><?php echo _AT('Create Backup'); ?></h4>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<table cellspacing="1" cellpadding="0" border="0" width="95%" summary="" align="center" class="bodyline">
	<tr>
		<td class="row1" colspan="2"><p>Creating a backup of this course will archive all available material into a single compressed file. Once the backup file is created, it will be made available on the backup manager for download and safe-keeping, or to be restored back into this or any other course.</p>

		<p>Depending on the course size and available server resources, the time needed to backup this course may take more than 5 minutes.</p>
		
		<p>Note: You are currently restricted to <?php echo AT_COURSE_BACKUPS; ?> backups per course.</p></td>
	</tr>
	<tr><td colspan="2" height="1" class="row2" colspan="3"></td></tr>
	<?php if ($Backup->getNumAvailable() >= AT_COURSE_BACKUPS): ?>
		<tr>
			<td class="row1" colspan="2"><p><strong>You have reached the maximum number of backups allowed.</strong></p></td>
		</tr>
	<?php else: ?>
	<tr>
		<td class="row1" align="right"><label for="desc"><strong>Optional Description:</strong></label></td>
		<td class="row1"><textarea cols="35" rows="2" class="formfield" id="desc" name="description" scroll="no"></textarea><br /><br /></td>
	</tr>
	<tr><td colspan="2" height="1" class="row2" colspan="3"></td></tr>
	<tr><td colspan="2" height="1" class="row2" colspan="3"></td></tr>
	<tr>
		<td class="row1" colspan="2" align="center"><input type="submit" name="submit" value="<?php echo _AT('create'); ?>" class="button" /> | <input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" class="button" /></td>
	</tr>
	<?php endif; ?>
</table>
</form>

<?php require (AT_INCLUDE_PATH.'footer.inc.php');  ?>