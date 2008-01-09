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
// $Id$
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'classes/testQuestions.class.php');

authenticate(AT_PRIV_TESTS);


$tid = intval($_REQUEST['tid']);

$_pages['tools/tests/results_all_quest.php']['title_var']  = 'question_statistics';
$_pages['tools/tests/results_all_quest.php']['parent']  = 'tools/tests/index.php';
$_pages['tools/tests/results_all_quest.php']['children'] = array('tools/tests/results_all.php?tid='.$tid);

$_pages['tools/tests/results_all.php?tid='.$tid]['title_var']  = 'mark_statistics';
$_pages['tools/tests/results_all.php?tid='.$tid]['parent'] = 'tools/tests/results_all_quest.php';

require(AT_INCLUDE_PATH.'header.inc.php');

$sql	= "SELECT TQ.*, TQA.* FROM ".TABLE_PREFIX."tests_questions TQ INNER JOIN ".TABLE_PREFIX."tests_questions_assoc TQA USING (question_id) WHERE TQ.course_id=$_SESSION[course_id] AND TQA.test_id=$tid ORDER BY TQA.ordering, TQA.question_id";
$result	= mysql_query($sql, $db);
$questions = array();
while ($row = mysql_fetch_array($result)) {
	$row['score']	= 0;
	$questions[]	= $row;
	$q_sql .= $row['question_id'].',';
}
$q_sql = substr($q_sql, 0, -1);
$num_questions = count($questions);

//check if survey
$sql	= "SELECT out_of, title, randomize_order FROM ".TABLE_PREFIX."tests WHERE test_id=$tid";
$result = mysql_query($sql, $db);
$row = mysql_fetch_assoc($result);
$tt = $row['title'];
$random = $row['randomize_order'];

echo '<h3>'.$row['title'].'</h3><br />';

//get all the questions in this test, store them
$sql	= "SELECT TQ.*, TQA.* FROM ".TABLE_PREFIX."tests_questions TQ INNER JOIN ".TABLE_PREFIX."tests_questions_assoc TQA USING (question_id) WHERE TQ.course_id=$_SESSION[course_id] AND TQA.test_id=$tid ORDER BY TQA.ordering, TQA.question_id";

$result = mysql_query($sql, $db);
$questions = array();	
while ($row = mysql_fetch_assoc($result)) {
	$questions[$row['question_id']] = $row;
}
$long_qs = substr($long_qs, 0, -1);

//get the answers:  count | q_id | answer
$sql = "SELECT count(*), A.question_id, A.answer, A.score
		FROM ".TABLE_PREFIX."tests_answers A, ".TABLE_PREFIX."tests_results R
		WHERE R.status=1 AND R.result_id=A.result_id AND R.final_score<>'' AND R.test_id=$tid
		GROUP BY A.question_id, A.answer
		ORDER BY A.question_id, A.answer";
$result = mysql_query($sql, $db);

echo '<img src="images/checkmark.gif" alt="Correct checkmark" />- '._AT('correct_answer').'<br /></p>';

$ans = array();	
while ($row = mysql_fetch_assoc($result)) {
	$ans[$row['question_id']][$row['answer']] = array('count'=>$row['count(*)'], 'score'=>$row['score']);
}

//print out rows
foreach ($questions as $q_id => $q) {
	/* for random: num_results is going to be specific to each question.
	 * This is a randomized test which means that it is possible each question has been answered a 
	 * different number of times.  Statistics are therefore based on the number of times each 
	 * question was answered, not the number of times the test has been taken.
	 */

	//catch random unanswered
	if($ans[$q_id]) {
		$obj = TestQuestions::getQuestion($q['type']);
		$obj->displayResultStatistics($q, $ans[$q_id]);
	}
}

require(AT_INCLUDE_PATH.'footer.inc.php');
?>