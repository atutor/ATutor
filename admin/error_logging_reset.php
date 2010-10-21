<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

admin_authenticate(AT_ADMIN_PRIV_ADMIN);

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: ./error_logging.php');
	exit;
} else if (isset($_POST['submit_yes'])) {
	
	//clean up the db
	require_once(AT_INCLUDE_PATH.'../mods/_core/file_manager/filemanager.inc.php');
	
	if (($result = clr_dir(AT_CONTENT_DIR . 'logs/'))) {
		$msg->addFeedback('ERROR_LOG_RESET');
	} else {
		$msg->addError('ERROR_LOG_NOT_RESET');
	}

	header('Location: ./error_logging.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

//print confirmation
$hidden_vars['all'] = TRUE;

$confirm = array('RESET_ERROR_LOG', $_SERVER['PHP_SELF']);
$msg->addConfirm($confirm, $hidden_vars);
$msg->printConfirm();

require(AT_INCLUDE_PATH.'footer.inc.php');
?>