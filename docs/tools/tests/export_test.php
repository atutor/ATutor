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
// $Id: preview.php 7208 2008-01-09 16:07:24Z greg $
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'classes/testQuestions.class.php');

authenticate(AT_PRIV_TESTS);

$tid = intval($_GET['tid']);

/* Retrieve the content_id of this test */
$sql = "SELECT title, random, num_questions, instructions FROM ".TABLE_PREFIX."tests WHERE test_id=$tid";
$result	= mysql_query($sql, $db); 
if (!($test_row = mysql_fetch_assoc($result))) {
	$msg->addError('ITEM_NOT_FOUND');
	header(url_rewrite('tools/tests/index.php', AT_PRETTY_URL_IS_HEADER));
	exit;
}

//Randomized or not, export all the questions that are associated with it.
$sql	= "SELECT TQ.question_id, TQA.weight FROM ".TABLE_PREFIX."tests_questions TQ INNER JOIN ".TABLE_PREFIX."tests_questions_assoc TQA USING (question_id) WHERE TQ.course_id=$_SESSION[course_id] AND TQA.test_id=$tid ORDER BY TQA.ordering, TQA.question_id";
$result	= mysql_query($sql, $db);
$ids = array();

while (($question_row = mysql_fetch_assoc($result)) != false){
	$ids[] = $question_row['question_id'];
}

//export
test_qti_export($ids, $test_row['title']);
?>