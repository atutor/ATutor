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
require_once(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/index.php';
$_section[1][0] = _AT('backup_manager');
$_section[1][1] = 'tools/backup/index.php';
$_section[2][0] = _AT('create_backup');

global $savant;
$msg =& new Message($savant);

authenticate(AT_PRIV_ADMIN);

$course = $_SESSION['course_id'];
require(AT_INCLUDE_PATH.'classes/Backup/Backup.class.php');
$Backup =& new Backup($db, $_SESSION['course_id']);

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php');
	exit;
} else if (isset($_POST['submit'])) {
	//make backup of current course
	$Backup->create($_POST['description']);
	$msg->addFeedback('BACKUP_CREATED');
	header('Location: index.php');
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
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<table cellspacing="1" cellpadding="0" border="0" width="95%" summary="" align="center" class="bodyline">
	<tr>
		<th class="cyan" colspan="2"><?php echo _AT('create_backup'); ?></th>
	</tr>
	<tr>
		<td class="row1" colspan="2"><?php echo _AT('create_backup_about', AT_COURSE_BACKUPS); ?></td>
	</tr>
	<tr><td colspan="2" height="1" class="row2" colspan="3"></td></tr>
	<?php if ($Backup->getNumAvailable() >= AT_COURSE_BACKUPS): ?>
		<tr>
			<td class="row1" colspan="2"><p><strong><?php echo _AT('max_backups_reached'); ?></strong></p></td>
		</tr>
	<?php else: ?>
	<tr>
		<td class="row1" align="right"><label for="desc"><strong><?php echo _AT('optional_description'); ?>:</strong></label></td>
		<td class="row1"><textarea cols="35" rows="2" class="formfield" id="desc" name="description" scroll="no"></textarea><br /><br /></td>
	</tr>
	<tr><td colspan="2" height="1" class="row2" colspan="3"></td></tr>
	<tr><td colspan="2" height="1" class="row2" colspan="3"></td></tr>
	<tr>
		<td class="row1" colspan="2" align="center"><input type="submit" name="submit" value="<?php echo _AT('create'); ?>" class="button" /> - <input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" class="button" /></td>
	</tr>
	<?php endif; ?>
</table>
</form>

<?php require (AT_INCLUDE_PATH.'footer.inc.php');  ?>