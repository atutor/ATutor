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
// $Id: results_quest_long.php 7208 2008-01-09 16:07:24Z greg $
$page = 'tests';
define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_TESTS);
$tid = $_REQUEST['tid'];

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

$sql	= "SELECT title FROM ".TABLE_PREFIX."tests WHERE test_id=$_GET[tid]";
$result = mysql_query($sql, $db);
$row = mysql_fetch_array($result);

echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'">';
echo '<input type="hidden" name="tid" value="'.$tid.'">';

echo '<div class="input-form">';
echo '<h2>'.AT_print($row['title'], 'tests.title').'</h2>';

echo '<br /><p>'._AT('response_text').' <strong>'.AT_print(urldecode($_GET['q']), 'tests_questions.question').'</strong></p>';

//get the answers
$sql = "SELECT count(*), A.answer
		FROM ".TABLE_PREFIX."tests_answers A, ".TABLE_PREFIX."tests_results R
		WHERE A.question_id=".$_GET['qid']." AND R.result_id=A.result_id AND R.final_score<>'' AND R.test_id=".$_GET['tid']."
		GROUP BY A.answer
		ORDER BY A.answer";

$result = mysql_query($sql, $db);

while ($row = mysql_fetch_assoc($result)) {
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