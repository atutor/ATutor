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
// $Id: delete.php 1748 2004-10-04 14:27:50Z joel $

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_ADMIN); 

$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/';
$_section[1][0] = _AT('backup_manager');
$_section[1][1] = 'tools/backup/index.php';
$_section[2][0] = _AT('edit');

if (isset($_GET['delete'])) {
	require(AT_INCLUDE_PATH.'classes/Backup/Backup.class.php');

	$Backup =& new Backup($db, $_SESSION['course_id']);
	$Backup->delete($_GET['delete']);

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
<h4>Delete</h4>
<?php
	echo 'print delete warning...';
?>
<a href="<?php echo $_SERVER['PHP_SELF']; ?>?delete=<?php echo $_GET['backup_id']; ?>">Yes/Delete</a> | <a href="tools/backup/index.php">No/Cancel</a>
<?php require (AT_INCLUDE_PATH.'footer.inc.php');  ?>