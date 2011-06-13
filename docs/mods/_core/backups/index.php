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

if (isset($_POST['restore'], $_POST['backup_id'])) {
	header('Location: restore.php?backup_id=' . $_POST['backup_id']);
	exit;
} else if (isset($_POST['download'], $_POST['backup_id'])) {
	$Backup = new Backup($db, $_SESSION['course_id']);
	$Backup->download($_POST['backup_id']);
	exit; // never reached
} else if (isset($_POST['delete'], $_POST['backup_id'])) {
	header('Location: delete.php?backup_id=' . $_POST['backup_id']);
	exit;
} else if (isset($_POST['edit'], $_POST['backup_id'])) {
	header('Location: edit.php?backup_id=' . $_POST['backup_id']);
	exit;
} else if (!empty($_POST)) {
	$msg->addError('NO_ITEM_SELECTED');
}

require(AT_INCLUDE_PATH.'header.inc.php');

$Backup = new Backup($db, $_SESSION['course_id']);
$list = $Backup->getAvailableList();
$savant->assign('list', $list);
$savant->display('instructor/backups/index.tmpl.php');
require (AT_INCLUDE_PATH.'footer.inc.php');  ?>
