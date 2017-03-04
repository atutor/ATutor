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

define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

admin_authenticate(AT_ADMIN_PRIV_ADMIN);

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: ./log.php');
	exit;
} else if (isset($_POST['submit_yes'])) {
	check_csrf_token();
	//clean up the db
	$sql    = "DELETE FROM %sadmin_log";
	$result = queryDB($sql, array(TABLE_PREFIX));
	global $sqlout;
	write_to_log(AT_ADMIN_LOG_DELETE, 'admin_log', $result, $sqlout);

	$msg->addFeedback('ADMIN_LOG_RESET');
	header('Location: ./log.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

//print confirmation
$hidden_vars['all'] = TRUE;
$hidden_vars['csrftoken'] = $_SESSION['token'];

$confirm = array('RESET_ADMIN_LOG', $_SERVER['PHP_SELF']);
$msg->addConfirm($confirm, $hidden_vars);
$msg->printConfirm();

require(AT_INCLUDE_PATH.'footer.inc.php');
?>