<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay & Joel Kronenberg        */
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
require(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

$msg =& new Message($savant);

$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/';
$_section[1][0] = _AT('tests');
$_section[1][1] = 'tools/tests';
$_section[2][0] = _AT('question_database');
$_section[2][1] = 'tools/tests/question_db.php';
$_section[3][0] = _AT('delete_question');

authenticate(AT_PRIV_TEST_CREATE);

	$tid = intval($_REQUEST['tid']);

	if (isset($_POST['submit_no'])) {
		$msg->addFeedback('CANCELLED');
		header('Location: question_db.php');
		exit;
	} else if (isset($_POST['submit_yes'])) {
		$qid = intval($_POST['qid']);

		$sql	= "DELETE FROM ".TABLE_PREFIX."tests_questions WHERE question_id=$qid AND course_id=$_SESSION[course_id]";
		$result	= mysql_query($sql, $db);

		if (mysql_affected_rows($db) == 1) {
			$sql	= "DELETE FROM ".TABLE_PREFIX."tests_questions_assoc WHERE question_id=$qid";
			$result	= mysql_query($sql, $db);
		}
		
		$msg->addFeedback('QUESTION_DELETED');
		header('Location: question_db.php');
		exit;

	} /* else: */

	require(AT_INCLUDE_PATH.'header.inc.php');
	echo '<h2>'._AT('delete_question').'</h2>';

	unset($hidden_vars);
	$hidden_vars['qid'] = $_GET['qid'];
	$msg->addConfirm('DELETE_TEST_QUESTION', $hidden_vars);

	$msg->printConfirm();

	require(AT_INCLUDE_PATH.'footer.inc.php');
?>