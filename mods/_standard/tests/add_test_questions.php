<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$
define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'../mods/_standard/tests/classes/testQuestions.class.php');

authenticate(AT_PRIV_TESTS);

if (isset($_GET['submit_create'])) {
	header('Location: create_question_'.$_GET['question_type'].'.php');
	exit;
}

$_pages['mods/_standard/tests/questions.php?tid='.$_GET['tid']]['title_var']    = 'questions';
$_pages['mods/_standard/tests/questions.php?tid='.$_GET['tid']]['parent']   = 'mods/_standard/tests/index.php';
$_pages['mods/_standard/tests/questions.php?tid='.$_GET['tid']]['children'] = array('mods/_standard/tests/add_test_questions.php');

$_pages['mods/_standard/tests/add_test_questions.php']['title_var']    = 'add_questions';
$_pages['mods/_standard/tests/add_test_questions.php']['parent']   = 'mods/_standard/tests/questions.php?tid='.$_GET['tid'];

if (defined('AT_FORCE_GET_FILE') && AT_FORCE_GET_FILE) {
	$course_base_href = 'get.php/';
} else {
	$course_base_href = 'content/' . $_SESSION['course_id'] . '/';
}

require(AT_INCLUDE_PATH.'header.inc.php');
?>

<?php $tid = intval($_GET['tid']); ?>

<?php require(AT_INCLUDE_PATH.'../mods/_standard/tests/html/tests_questions.inc.php'); ?>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>