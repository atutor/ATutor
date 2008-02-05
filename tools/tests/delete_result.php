<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_TESTS);

$tid = intval($_REQUEST['tid']);
$rid = intval($_REQUEST['rid']);

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.AT_BASE_HREF.'tools/tests/results.php?tid='.$tid);
	exit;

} else if (isset($_POST['submit_yes'])) {
		
	$sql	= "DELETE FROM ".TABLE_PREFIX."tests_answers WHERE result_id=$rid";
	$result	= mysql_query($sql, $db);

	$sql	= "DELETE FROM ".TABLE_PREFIX."tests_results WHERE result_id=$rid";
	$result	= mysql_query($sql, $db);
		
	$msg->addFeedback('RESULT_DELETED');
	header('Location: '.AT_BASE_HREF.'tools/tests/results.php?tid='.$tid);
	exit;
} 

$_pages['tools/tests/delete_result.php']['title_var']  = 'delete_results';
$_pages['tools/tests/delete_result.php']['parent'] = 'tools/tests/results.php?tid='.$tid;

$_pages['tools/tests/results.php?tid='.$tid]['title_var'] = 'submissions';
$_pages['tools/tests/results.php?tid='.$tid]['parent'] = 'tools/tests/index.php';

require(AT_INCLUDE_PATH.'header.inc.php');

unset($hidden_vars);
$hidden_vars['tid'] = $tid;
$hidden_vars['rid'] = $rid;
$msg->addConfirm('DELETE', $hidden_vars);

$msg->printConfirm();

require(AT_INCLUDE_PATH.'footer.inc.php');
?>