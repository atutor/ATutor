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
if ($_SESSION['course_id'] > -1) { exit; }

$page = 'backups';
$_user_location = 'admin';

if (isset($_GET['delete'])) {
	require(AT_INCLUDE_PATH.'classes/Backup/Backup.class.php');

	$Backup =& new Backup($db, $_GET['course_id']);
	$Backup->delete($_GET['delete']);

	header('Location: index.php?f=' . AT_FEEDBACK_BACKUP_DELETED);
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

echo '<h3>'._AT('backups').'</h3>';
echo '<h4>'._AT('delete').'</h4>';

	$warnings[] = AT_WARNING_DELETE_BACKUP;
	require(AT_INCLUDE_PATH . 'html/feedback.inc.php');
?>

<p><a href="<?php echo $_SERVER['PHP_SELF']; ?>?delete=<?php echo $_GET['backup_id'].SEP.'course_id='.$_GET['course_id']; ?>"><?php echo _AT('yes_delete'); ?></a> | <a href="admin/backup/index.php"><?php echo _AT('no_cancel'); ?></a></p>

<?php require (AT_INCLUDE_PATH.'footer.inc.php');  ?>