<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
	$page = 'tests';
	define('AT_INCLUDE_PATH', '../../include/');
	require(AT_INCLUDE_PATH.'vitals.inc.php');

	if (!authenticate(AT_PRIV_TEST_CREATE, true)) {
		$msg->addError('ACCESS_DENIED');
		header('Location: index.php');
		exit;
	}

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

	require(AT_INCLUDE_PATH.'header.inc.php');
	echo '<h2>'._AT('remove_question').'</h2>';

	unset($hidden_vars);
	$hidden_vars['qid'] = $_GET['qid'];
	$hidden_vars['tid'] = $_GET['tid'];
	$msg->addConfirm('REMOVE_TEST_QUESTION', $hidden_vars);

	$msg->printConfirm();

	require(AT_INCLUDE_PATH.'footer.inc.php');
?>