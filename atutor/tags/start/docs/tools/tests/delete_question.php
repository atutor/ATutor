<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/

	$_include_path = '../../include/';
	require($_include_path.'vitals.inc.php');
	$_section[0][0] = _AT('tools');
	$_section[0][1] = 'tools/';
	$_section[1][0] = _AT('tests');
	$_section[1][1] = 'tools/tests';
	$_section[2][0] = _AT('questions');
	$_section[2][1] = 'tools/tests/questions.php?tid='.$_GET['tid'];
	$_section[3][0] = _AT('delete_question');


	$tt = $_POST['tt'];
	$tid = intval($_GET['tid']);
	if ($tid == 0){
		$tid = intval($_POST['tid']);
	}

	if ($_GET['d']) {
		$tid = intval($_GET['tid']);
		$qid = intval($_GET['qid']);

		$sql	= "DELETE FROM ".TABLE_PREFIX."tests_questions WHERE question_id=$qid AND test_id=$tid AND course_id=$_SESSION[course_id]";
		$result	= mysql_query($sql, $db);
		
		header('Location: ../tests/questions.php?tid='.$tid.SEP.'tt='.$_GET['tt'].SEP.'f='.urlencode_feedback(AT_FEEDBACK_QUESTION_DELETED));
		exit;

	} /* else: */

	require($_include_path.'header.inc.php');
	echo '<h2>'._AT('delete_question').'</h2>';
	print_warnings(AT_WARNING_DELETE_QUESTION);

	echo '<a href="tools/tests/delete_question.php?tid='.$_GET['tid'].SEP.'tt='.$_GET['tt'].SEP.'qid='.$_GET['qid'].SEP.'d=1">Yes/Delete</a>, <a href="tools/tests/questions.php?tid='.$_GET['tid'].SEP.'tt='.$_GET['tt'].SEP.'f='.urlencode_feedback(AT_FEEDBACK_CANCELLED).'">No/Cancel</a>';
	echo '</p>';
	
	require($_include_path.'footer.inc.php');
?>