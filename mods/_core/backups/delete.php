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

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_ADMIN); 

if (isset($_POST['submit_yes'])) {
	require(AT_INCLUDE_PATH.'../mods/_core/backups/classes/Backup.class.php');
	$Backup = new Backup($db, $_SESSION['course_id']);
	$Backup->delete($_POST['backup_id']);
	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: index.php');
	exit;
}

else if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

	$delete_backup = intval($_REQUEST['backup_id']);
	$sql = "SELECT * from ".TABLE_PREFIX."backups WHERE backup_id = '$delete_backup'";
	$result = mysql_query($sql, $db);


while ($row = mysql_fetch_assoc($result)){
	$title = $row['file_name'];
}
	$index['backup_id'] = $_GET['backup_id'];
	$msg->addConfirm(array('DELETE', htmlentities_utf8($title)), $index);
	$msg->printConfirm();

require (AT_INCLUDE_PATH.'footer.inc.php');

?>