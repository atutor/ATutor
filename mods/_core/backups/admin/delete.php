<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2010                                                  */
/* Inclusive Design Institute                                               */
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/
// $Id: delete.php 10142 2010-08-17 19:17:26Z hwong $

define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_BACKUPS);

$page = 'backups';
$_user_location = 'admin';

if (isset($_POST['submit_yes'])) {
	require_once(AT_INCLUDE_PATH.'../mods/_core/backups/classes/Backup.class.php');

	$Backup = new Backup($db, $_POST['course']);
	$Backup->delete($_POST['backup_id']);

	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: index.php');
	exit;
} else if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

$hidden_vars['backup_id'] = $_GET['backup_id'];
$hidden_vars['course']    = $_GET['course'];
$msg->addConfirm('DELETE', $hidden_vars);
$msg->printConfirm();

require (AT_INCLUDE_PATH.'footer.inc.php');
?>