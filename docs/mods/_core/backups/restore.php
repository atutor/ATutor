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
// $Id$

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_ADMIN); 
require(AT_INCLUDE_PATH.'../mods/_core/backups/classes/Backup.class.php');
require_once(AT_INCLUDE_PATH.'../mods/_core/file_manager/filemanager.inc.php');

$Backup = new Backup($db, $_SESSION['course_id']);

if (!isset($_REQUEST['backup_id'])) {
	header('Location: index.php');
	exit;
}

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php');
	exit;
} else if (isset($_POST['submit'])) {
	if (!$_POST['material']) {
		$msg->addError(array('EMPTY_FIELDS', _AT('material')));
	} else {
		$Backup->restore($_POST['material'], $_POST['action'], $_POST['backup_id']);
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		header('Location: index.php');
		exit;
	}
}

require(AT_INCLUDE_PATH.'header.inc.php');

$row = $Backup->getRow($_REQUEST['backup_id']);


$savant->display('instructor/backups/restore.tmpl.php');
require (AT_INCLUDE_PATH.'footer.inc.php');  ?>