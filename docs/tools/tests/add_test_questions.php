<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
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
require(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/';
$_section[1][0] = _AT('test_manager');
$_section[1][1] = 'tools/tests/index.php';
$_section[2][0] = _AT('question_bank');

$msg =& new Message($savant);

authenticate(AT_PRIV_TEST_CREATE);

if (isset($_POST['submit_yes'])) {
	$tid = intval($_POST['tid']);
	$sql = "INSERT INTO ".TABLE_PREFIX."tests_questions_assoc VALUES ";
	foreach ($_POST['questions'] as $question) {
		$question = intval($question);
		$sql .= '('.$tid.', '.$question.'),';
	}
	$sql = substr($sql, 0, -1);
	$result = mysql_query($sql, $db);

	$msg->addFeedback('');
	header('Location: questions.php?tid='.$tid);
	exit;
} else if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: question_bank.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

$questions = addslashes(implode(',',$_POST['add_questions']));
$sql = "SELECT question, question_id FROM ".TABLE_PREFIX."tests_questions WHERE question_id IN ($questions) AND course_id=$_SESSION[course_id] ORDER BY question";
$result = mysql_query($sql, $db);
$questions = '';
while ($row = mysql_fetch_assoc($result)) {
	$questions .= '<li>'.$row['question'].'</li>';
	$questions_array['questions['.$row['question_id'].']'] = $row['question_id'];
}
$questions_array['tid'] = $_POST['test_id'];
$msg->addConfirm(array('ADD_TEST_QUESTIONS', $questions), $questions_array);

$msg->printConfirm();

?>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>