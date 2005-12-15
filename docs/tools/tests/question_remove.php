<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

$page = 'tests';
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_TESTS);

$tid = intval($_REQUEST['tid']);
$qid = intval($_REQUEST['qid']);

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: questions.php?tid=' . $tid);
	exit;
} else if (isset($_POST['submit_yes'])) {
	$sql	= "DELETE FROM ".TABLE_PREFIX."tests_questions_assoc WHERE question_id=$qid AND test_id=$tid";
	$result	= mysql_query($sql, $db);
		
	$msg->addFeedback('QUESTION_REMOVED');
	header('Location: questions.php?tid=' . $tid);
	exit;

} /* else: */

$_pages['tools/tests/questions.php?tid='.$_GET['tid']]['title_var']    = 'questions';
$_pages['tools/tests/questions.php?tid='.$_GET['tid']]['parent']   = 'tools/tests/index.php';
$_pages['tools/tests/questions.php?tid='.$_GET['tid']]['children'] = array('tools/tests/add_test_questions.php?tid='.$_GET['tid']);

$_pages['tools/tests/add_test_questions.php?tid='.$_GET['tid']]['title_var']    = 'add_questions';
$_pages['tools/tests/add_test_questions.php?tid='.$_GET['tid']]['parent']   = 'tools/tests/questions.php?tid='.$_GET['tid'];

$_pages['tools/tests/question_remove.php']['title_var'] = 'remove_question';
$_pages['tools/tests/question_remove.php']['parent']    = 'tools/tests/questions.php?tid='.$_GET['tid'];

require(AT_INCLUDE_PATH.'header.inc.php');

unset($hidden_vars);
$hidden_vars['qid'] = $_GET['qid'];
$hidden_vars['tid'] = $_GET['tid'];
$msg->addConfirm('REMOVE_TEST_QUESTION', $hidden_vars);

$msg->printConfirm();

require(AT_INCLUDE_PATH.'footer.inc.php');
?>