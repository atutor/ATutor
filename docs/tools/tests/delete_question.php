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
	require(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

	global $savant;
	$msg =& new Message($savant);

	$_section[0][0] = _AT('tools');
	$_section[0][1] = 'tools/';
	$_section[1][0] = _AT('tests');
	$_section[1][1] = 'tools/tests';
	$_section[2][0] = _AT('questions');
	$_section[2][1] = 'tools/tests/questions.php?tid='.$_GET['tid'];
	$_section[3][0] = _AT('delete_question');

	authenticate(AT_PRIV_TEST_CREATE);

	$tt = $_POST['tt'];
	$tid = intval($_GET['tid']);
	if ($tid == 0){
		$tid = intval($_POST['tid']);
	}

	if ($_GET['d']) {
		/* We must ensure that any previous feedback is flushed, since AT_FEEDBACK_CANCELLED might be present
		 * if Yes/Delete was chosen below
		 */
		$msg->deleteFeedback('CANCELLED'); // makes sure its not there 
		 
		$tid = intval($_GET['tid']);
		$qid = intval($_GET['qid']);

		$sql	= "DELETE FROM ".TABLE_PREFIX."tests_questions WHERE question_id=$qid AND test_id=$tid AND course_id=$_SESSION[course_id]";
		$result	= mysql_query($sql, $db);
		
		$msg->addFeedback('QUESTION_DELETED');
		header('Location: ../tests/questions.php?tid='.$tid.SEP.'tt='.$_GET['tt']);
		exit;

	} /* else: */

	require(AT_INCLUDE_PATH.'header.inc.php');
	echo '<h2>'._AT('delete_question').'</h2>';
	$msg->printWarnings('DELETE_QUESTION');
	
	/* Since we do not know which choice will be taken, assume it No/Cancel, addFeedback('CANCELLED)
	 * If sent to questions.php then OK, else if sent back here & if $_GET['d']=1 then assumed choice was not taken
	 * ensure that addFeeback('CANCELLED') is properly cleaned up, see above
	 */
	$msg->addFeedback('CANCELLED');
	echo '<a href="tools/tests/delete_question.php?tid='.$_GET['tid'].SEP.'tt='.$_GET['tt'].SEP.'qid='.$_GET['qid'].SEP.'d=1">Yes/Delete</a>, <a href="tools/tests/questions.php?tid='.$_GET['tid'].SEP.'tt='.$_GET['tt'].'">No/Cancel</a>';
	echo '</p>';
	
	require(AT_INCLUDE_PATH.'footer.inc.php');
?>