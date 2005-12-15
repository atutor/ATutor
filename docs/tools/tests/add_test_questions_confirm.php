<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

$page = 'tests';
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_TESTS);

$tid = intval($_POST['tid']);

$_pages['tools/tests/questions.php?tid='.$tid]['title_var']    = 'questions';
$_pages['tools/tests/questions.php?tid='.$tid]['parent']   = 'tools/tests/index.php';
$_pages['tools/tests/questions.php?tid='.$tid]['children'] = array('tools/tests/add_test_questions.php?tid='.$tid);

$_pages['tools/tests/add_test_questions.php?tid='.$tid]['title_var']  = 'add_questions';
$_pages['tools/tests/add_test_questions.php?tid='.$tid]['parent'] = 'tools/tests/questions.php?tid='.$tid;

$_pages['tools/tests/add_test_questions_confirm.php']['title_var'] = 'add_questions';
$_pages['tools/tests/add_test_questions_confirm.php']['parent']    = 'tools/tests/questions.php?tid='.$tid;

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: questions.php?tid='.$tid);
	exit;
} else if (isset($_POST['submit_yes'])) {
	$sql = "REPLACE INTO ".TABLE_PREFIX."tests_questions_assoc VALUES ";
	foreach ($_POST['questions'] as $question) {
		$question = intval($question);
		$sql .= '('.$tid.', '.$question.', 0, 0, 0),';
	}
	$sql = substr($sql, 0, -1);
	$result = mysql_query($sql, $db);

	$msg->addFeedback('QUESTION_ADDED');
	header('Location: questions.php?tid='.$tid);
	exit;
} else if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: add_test_questions.php?tid='.$tid);
	exit;
}

if (!is_array($_POST['add_questions']) || !count($_POST['add_questions'])) {
	$msg->addError('NO_QUESTIONS_SELECTED');
	header('Location: add_test_questions.php?tid='.$tid);
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

foreach ($_POST['add_questions'] as $cat_array) {
	$questions .= addslashes(implode(',',$cat_array)).',';
}
$questions = substr($questions, 0, -1);

$sql = "SELECT question, question_id FROM ".TABLE_PREFIX."tests_questions WHERE question_id IN ($questions) AND course_id=$_SESSION[course_id] ORDER BY question";
$result = mysql_query($sql, $db);
$questions = '';
while ($row = mysql_fetch_assoc($result)) {
	$questions .= '<li>'.htmlspecialchars($row['question']).'</li>';
	$questions_array['questions['.$row['question_id'].']'] = $row['question_id'];
}
$questions_array['tid'] = $_POST['tid'];
$msg->addConfirm(array('ADD_TEST_QUESTIONS', $questions), $questions_array);

$msg->printConfirm();
?>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>