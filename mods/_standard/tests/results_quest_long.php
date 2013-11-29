<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$
//THIS FILE IS NOT USED?
exit;
$page = 'tests';
define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_TESTS);
$tid = intval($_REQUEST['tid']);
$qid = intval($_GET['qid']);

$_pages['mods/_standard/tests/results_quest_long.php']['title_var']  = 'view_responses';
$_pages['mods/_standard/tests/results_quest_long.php']['parent'] = 'mods/_standard/tests/results_all_quest.php?tid='.$tid;

$_pages['mods/_standard/tests/results_all_quest.php?tid='.$tid]['title_var']  = 'question_statistics';
$_pages['mods/_standard/tests/results_all_quest.php?tid='.$tid]['parent']  = 'mods/_standard/tests/index.php';
$_pages['mods/_standard/tests/results_all_quest.php?tid='.$tid]['children'] = array('mods/_standard/tests/results_all.php?tid='.$tid);

$_pages['mods/_standard/tests/results_all.php?tid='.$tid]['title_var']  = 'mark_statistics';
$_pages['mods/_standard/tests/results_all.php?tid='.$tid]['parent']  = 'mods/_standard/tests/results_all_quest.php';


if ($_POST['back']) {
	header('Location: results_all_quest.php?tid='.$tid);
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

$sql	= "SELECT title FROM %stests WHERE test_id=%d";
$row = queryDB($sql, array(TABLE_PREFIX, $tid), TRUE);

echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'">';
echo '<input type="hidden" name="tid" value="'.$tid.'">';

echo '<div class="input-form">';
echo '<h2>'.AT_print($row['title'], 'tests.title').'</h2>';

echo '<br /><p>'._AT('response_text').' <strong>'.AT_print(urldecode($_GET['q']), 'tests_questions.question').'</strong></p>';

//get the answers

$sql = "SELECT count(*), A.answer
		FROM %stests_answers A, %stests_results R
		WHERE A.question_id=%d AND R.result_id=A.result_id AND R.final_score<>'' AND R.test_id=%d
		GROUP BY A.answer
		ORDER BY A.answer";

$rows_answers = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, $qid, $tid));

foreach($rows_answers as $row){
	if ($row['answer'] != -1 && $row['answer'] != '') {
		echo '<div class="row">';
		echo '-'.AT_print($row['answer'], 'tests_answers.answer');
		echo '</div>';
	}
} 

echo '<div class="row buttons">';
	echo '<input type="submit" value="'._AT('back').'" name="back" />';
echo '</div>';

echo '</div></form>';

require(AT_INCLUDE_PATH.'footer.inc.php');
?>
