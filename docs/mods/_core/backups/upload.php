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

$_SESSION['done'] = 0;

require(AT_INCLUDE_PATH.'../mods/_core/backups/classes/Backup.class.php');
$Backup = new Backup($db, $_SESSION['course_id']);

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php');
	exit;
} else if (isset($_POST['upload']) && ($Backup->getNumAvailable() < AT_COURSE_BACKUPS)) {
	$Backup->upload($_FILES, $_POST['description']);

	$_SESSION['done'] = 1;

	if($msg->containsErrors()) {
		header('Location: upload.php');
		exit;
	} else {
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		header('Location: index.php');
		exit;
	}
}

require(AT_INCLUDE_PATH.'header.inc.php');

?>



<script language="javascript" type="text/javascript">
function openWindow(page) {
	newWindow = window.open(page, "progWin", "width=400,height=200,toolbar=no,location=no");
	newWindow.focus();
}
</script>

<?php 
$savant->assign('Backup', $Backup);
$savant->display('instructor/backups/upload.tmpl.php');
require (AT_INCLUDE_PATH.'footer.inc.php');  ?>