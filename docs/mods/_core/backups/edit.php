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

if (isset($_POST['cancel']) || !isset($_REQUEST['backup_id'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php');
	exit;
}

$Backup = new Backup($db, $_SESSION['course_id']);

if (isset($_POST['edit'])) {
	$Backup->edit($_POST['backup_id'], $_POST['new_description']);
	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: index.php');
	exit;
} 

require(AT_INCLUDE_PATH.'header.inc.php');

$row = $Backup->getRow($_REQUEST['backup_id']);
//check for errors
$savant->assign('row', $row);
$savant->display('instructor/backups/edit.tmpl.php');
require (AT_INCLUDE_PATH.'footer.inc.php');  ?>