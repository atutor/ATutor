<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton	*/
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

if (isset($_POST['submit_yes'])) {
	require_once(AT_INCLUDE_PATH.'classes/Backup/Backup.class.php');

	$Backup =& new Backup($db, $_POST['course_id']);
	$Backup->delete($_POST['backup_id']);

	$msg->addFeedback('BACKUP_DELETED');
	header('Location: index.php');
	exit;
}

else if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');


	$hidden_vars['backup_id'] = $_GET['backup_id'];
	$hidden_vars['course_id'] = $_GET['course_id'];
	$msg->addConfirm('DELETE_BACKUP', $hidden_vars);
	$msg->printConfirm();
	

require (AT_INCLUDE_PATH.'footer.inc.php');

?>