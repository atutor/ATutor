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
// $Id: reset_log.php 7208 2008-01-09 16:07:24Z greg $

define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

admin_authenticate(AT_ADMIN_PRIV_ADMIN);

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: ./log.php');
	exit;
} else if (isset($_POST['submit_yes'])) {
	//clean up the db
	$sql    = "DELETE FROM ".TABLE_PREFIX."admin_log";
	$result = mysql_query($sql, $db);

	write_to_log(AT_ADMIN_LOG_DELETE, 'admin_log', mysql_affected_rows($db), $sql);

	$msg->addFeedback('ADMIN_LOG_RESET');
	header('Location: ./log.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

//print confirmation
$hidden_vars['all'] = TRUE;

$confirm = array('RESET_ADMIN_LOG', $_SERVER['PHP_SELF']);
$msg->addConfirm($confirm, $hidden_vars);
$msg->printConfirm();

require(AT_INCLUDE_PATH.'footer.inc.php');
?>