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

authenticate(AT_PRIV_ADMIN); 

$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/';
$_section[1][0] = _AT('backup_manager');
$_section[1][1] = 'tools/backup/index.php';
$_section[2][0] = _AT('edit');

global $savant;
$msg =& new Message($savant);

if (isset($_GET['delete'])) {
	require(AT_INCLUDE_PATH.'classes/Backup/Backup.class.php');

	$Backup =& new Backup($db, $_SESSION['course_id']);
	$Backup->delete($_GET['delete']);
	$msg->addFeedback('BACKUP_DELETED');
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

echo '<h4>'._AT('delete').'</h4>';

	$msg->addWarning('DELETE_BACKUP');
	$msg->printAll();

?>
<a href="<?php echo $_SERVER['PHP_SELF']; ?>?delete=<?php echo $_GET['backup_id']; ?>"><?php echo _AT('yes_delete'); ?></a> - <a href="tools/backup/index.php?f=<?php echo AT_FEEDBACK_CANCELLED; ?>"><?php echo _AT('no_cancel'); ?></a>
<?php require (AT_INCLUDE_PATH.'footer.inc.php');  ?>