<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$

$page = 'tests';
define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_TESTS);

$tid = intval($_POST['tid']);

$_pages['mods/_standard/tests/questions.php?tid='.$tid]['title_var']    = 'questions';
$_pages['mods/_standard/tests/questions.php?tid='.$tid]['parent']   = 'mods/_standard/tests/index.php';
$_pages['mods/_standard/tests/questions.php?tid='.$tid]['children'] = array('mods/_standard/tests/add_test_questions.php?tid='.$tid);

$_pages['mods/_standard/tests/add_test_questions.php?tid='.$tid]['title_var']  = 'add_questions';
$_pages['mods/_standard/tests/add_test_questions.php?tid='.$tid]['parent'] = 'mods/_standard/tests/questions.php?tid='.$tid;

$_pages['mods/_standard/tests/add_test_questions_confirm.php']['title_var'] = 'add_questions';
$_pages['mods/_standard/tests/add_test_questions_confirm.php']['parent']    = 'mods/_standard/tests/questions.php?tid='.$tid;

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: questions.php?tid='.$tid);
	exit;
} else if (isset($_POST['submit_yes'])) {
	//get order
	$sql = "SELECT MAX(ordering) AS max_ordering FROM ".TABLE_PREFIX."tests_questions_assoc WHERE test_id=".$tid;
	$result = mysql_query($sql, $db);
	$order	= mysql_fetch_assoc($result);
	$order = $order['max_ordering'];

	$sql = "REPLACE INTO ".TABLE_PREFIX."tests_questions_assoc VALUES ";
	foreach ($_POST['questions'] as $question) {
		$order++;
		$question = intval($question);
		$sql .= '('.$tid.', '.$question.', 0, '.$order.', 0),';
	}
	$sql = substr($sql, 0, -1);
	$result = mysql_query($sql, $db);

	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: questions.php?tid='.$tid);
	exit;
} else if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: add_test_questions.php?tid='.$tid);
	exit;
}

if (!is_array($_POST['questions']) || !count($_POST['questions'])) {
	$msg->addError('NO_QUESTIONS_SELECTED');
	header('Location: add_test_questions.php?tid='.$tid);
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

foreach ($_POST['questions'] as $id => $cat_array) {
	foreach ($cat_array as $idx => $q) {
		$_POST['questions'][$id][$idx] = intval($q);
	}
}
foreach ($_POST['questions'] as $cat_array) {
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