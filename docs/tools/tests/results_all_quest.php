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
$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/index.php';
$_section[1][0] = _AT('test_manager');
$_section[1][1] = 'tools/tests/index.php';
$_section[2][0] = _AT('results');

authenticate(AT_PRIV_TEST_MARK);

$tid = intval($_GET['tid']);
if ($tid == 0){
	$tid = intval($_POST['tid']);
}

function print_likert($q, $answers, $num_scale, $num_results) {
	echo '<br />';
	echo '<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center" width="90%">';
	echo '<tr>';
	echo '<th scope="col" width="40%"><small>'._AT('question').'</small></th>';
	echo '<th scope="col" nowrap="nowrap"><small>'._AT('left_blank').'</small></th>';
	echo '<th scope="col"><small>'._AT('average').' '._AT('answer').'</small></th>';
	for ($i=0; $i<=$num_scale; $i++) {
		echo '<th scope="col" title="'.$q['choice_'.$i].'">'.($i+1).'</th>';
	}
	echo '</tr>';

	echo '<tr>';
	echo '<td>'.$q['question'].'</td>';

	$num_blanks = intval($answers['-1']['count']);
	echo '<td align="center" width="70" valign="top">'.$num_blanks.'</td>';

	//avg choice 
	$sum = 0;
	for ($j=0; $j<=$num_scale; $j++) {
		$sum += ($j+1) * $answers[$j]['count'];
	}

	$num_results -= $num_blanks;

	//check if only blanks given
	echo '<td align="center" width="70" valign="top">';
	if ($num_results) {
		echo round($sum/$num_results, 1).'/'.($num_scale+1);
	} else  {
		echo '-';
	}
	echo '</td>';

	for ($j=0; $j<=$num_scale; $j++) {
		$percentage = $num_results ? round($answers[$j]['count']/$num_results*100) : 0;
		echo '<td align="center" valign="top">'.intval($answers[$j]['count']).'/'.$num_results.'<br />'.$percentage.'%</td>';		
	}
	echo '</tr>';
	echo '</table>';	

	return true;
}

function print_true_false($q, $answers, $num_results) {
	echo '<br />';
	echo '<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center" width="90%">';
	echo '<tr>';
	echo '<th scope="col" width="40%"><small>'._AT('question').'</small></th>';	
	echo '<th scope="col" nowrap="nowrap"><small>'._AT('left_blank').'</small></th>';	

	if ($q['answer_0'] == 1) {		
		echo '<th scope="col"><small>'._AT('true').'<span style="color:red;">*</span></small></th>';
		echo '<th scope="col"><small>'._AT('false').'</small></th>';
	} elseif ($q['answer_0'] == 2) {
		echo '<th scope="col"><small>'._AT('true').'</small></th>';
		echo '<th scope="col"><small>'._AT('false').'<span style="color:red;">*</span></small></th>';
	} else {
		echo '<th scope="col"><small>'._AT('true').'</small></th>';
		echo '<th scope="col"><small>'._AT('false').'</small></th>';
	}
	echo '</tr>';

	echo '<tr>';
	echo '<td>'.$q['question'].'</td>';

	$num_blanks = intval($answers['-1']['count']);
	//blank
	echo '<td align="center" width="70" valign="top">'.$num_blanks.'</td>';

	$num_results -= $num_blanks;

	$percentage1 = $num_results ? round($answers[1]['count']/$num_results*100) : 0;
	$percentage2 = $num_results ? round($answers[1]['count']/$num_results*100) : 0;

	echo '<td align="center" valign="top">'.intval($answers[1]['count']) .'/'.$num_results.'<br />'. $percentage1.'%</td>';
	echo '<td align="center" valign="top">'.intval($answers[2]['count']) .'/'.$num_results.'<br />'.$percentage2.'%</td>';	

	echo '</tr>';
	echo '</table>';	

	return true;
}
function print_multiple_choice($q, $answers, $num, $num_results) {

	echo '<br />';
	echo '<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center" width="90%">';
	echo '<tr>';
	echo '<th scope="col" width="40%"><small>'._AT('question').'</small></th>';
	echo '<th scope="col" nowrap="nowrap"><small>'._AT('left_blank').'</small></th>';

	for ($i=1; $i<=$num+1; $i++) {
		if ($q['answer_'.($i-1)]) {		
			echo '<th scope="col"><small>'.$q['choice_'.($i-1)].'<span style="color:red;">*</span></small></th>';
		} else {
			echo '<th scope="col"><small>'.$q['choice_'.($i-1)].'</small></th>';
		}
	}
	echo '</tr>';

	echo '<tr>';
	echo '<td>'.$q['question'].'</td>';

	$sum = 0;
	for ($j=0; $j<=$num; $j++) {
		$sum += $answers[$j]['score'];
	}

	$num_blanks = intval($answers['-1']['count']);
	//blank
	echo '<td align="center" width="70" valign="top">'.$num_blanks.'</td>';

	$num_results -= $num_blanks;

	for ($j=0; $j<=$num; $j++) {
		$percentage = $num_results ? round($answers[$j]['count']/$num_results*100) : 0;
		echo '<td align="center" valign="top">'.intval($answers[$j]['count']).'/'.$num_results.'<br />'.$percentage.'%</td>';		
	}
	echo '</tr>';
	echo '</table>';	

	return true;
}

function print_long($q, $answers) {
	global $tid, $tt;
	echo '<br />';
	echo '<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center" width="90%">';
	echo '<tr>';
	echo '<th scope="col" width="40%"><small>'._AT('question').'</small></th>';	
	echo '<th scope="col"><small>'._AT('left_blank').'</small></th>';
	echo '<th scope="col"><small>'._AT('results').'</small></th>';	
	echo '</tr>';

	echo '<tr>';
	echo '<td>'.$q['question'].'</td>';

	$num_blanks = intval($answers['-1']['count']);
	//blank
	echo '<td align="center" width="70" valign="top">'.$num_blanks.'</td>';
	
	echo '<td align="center" valign="top">';
	echo '<a href="tools/tests/results_quest_long.php?tid='.$tid.SEP.'qid='.$q['question_id'].SEP.'q='.urlencode($q['question']).'">'._AT('view_responses').'</a>';
	echo '</td>';
	echo '</tr>';
	echo '</table>';
}

require(AT_INCLUDE_PATH.'header.inc.php');
echo '<h2>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '<a href="tools/" class="hide"><img src="images/icons/default/square-large-tools.gif"  class="menuimage" border="0" vspace="2" width="42" height="40" alt="" /></a>';
}
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
	echo ' <a href="tools/" class="hide">'._AT('tools').'</a>';
}
echo '</h2>';

echo '<h3>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '&nbsp;<img src="images/icons/default/test-manager-large.gif"  class="menuimageh3" width="42" height="38" alt="" /> ';
}
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
	echo '<a href="tools/tests/">'._AT('test_manager').'</a>';
}
echo '</h3>';

$sql	= "SELECT * FROM ".TABLE_PREFIX."tests_questions Q WHERE Q.test_id=$tid AND Q.course_id=$_SESSION[course_id] ORDER BY ordering";
$result	= mysql_query($sql, $db);
$questions = array();
$total_weight = 0;
while ($row = mysql_fetch_array($result)) {
	$row['score']	= 0;
	$questions[]	= $row;
	$q_sql .= $row['question_id'].',';
	$total_weight += $row['weight'];
}
$q_sql = substr($q_sql, 0, -1);
$num_questions = count($questions);

//check if survey
$sql	= "SELECT automark, title FROM ".TABLE_PREFIX."tests WHERE test_id=$tid";
$result = mysql_query($sql, $db);
$row = mysql_fetch_array($result);
$tt = $row['title'];

echo '<h3>'._AT('results_for', AT_print($tt, 'tests.title')).'</h3>';

echo '<br />';
echo '<strong>'._AT('question_statistics').'</strong> | <a href="tools/tests/results_all.php?tid='.$tid.'">' . _AT('mark_statistics') . '</a>';
//echo ' | <a href="tools/tests/results_all_csv.php?tid='.$tid.'">' . _AT('download_test_csv') . '</a>';

//get total #results
$sql	= "SELECT COUNT(*) FROM ".TABLE_PREFIX."tests_results R WHERE R.test_id=$tid AND R.final_score<>''";
$result = mysql_query($sql, $db);
$num_results = mysql_fetch_array($result);

if (!$num_results[0]) {
	echo '<br /><em>'._AT('no_results_yet').'</em>';
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

echo '<br /><br />';

echo _AT('total').' '._AT('results').': '.$num_results[0].'<br />';
if ($row['automark'] != AT_MARK_UNMARKED) {
	echo '<font color="red">*</font>'._AT('correct_answer').'<br />';
}

/****************************************************************/
// This is to prevent division by zero in cases where the test has not been taken but an average is calculated (i.e. 0/0)
if ($num_results[0] == 0) {
	$num_results[0] = 1;
}
/****************************************************************/

//get all the questions in this test, store them
$sql = "SELECT *
		FROM ".TABLE_PREFIX."tests_questions  
		WHERE test_id=$tid
		ORDER BY question_id";

$result = mysql_query($sql, $db);
$questions = array();	
while ($row = mysql_fetch_assoc($result)) {
	$questions[$row['question_id']] = $row;
}
$long_qs = substr($long_qs, 0, -1);

//get the answers:  count | q_id | answer
$sql = "SELECT count(*), A.question_id, A.answer, A.score
		FROM ".TABLE_PREFIX."tests_answers A, ".TABLE_PREFIX."tests_results R
		WHERE R.result_id=A.result_id AND R.final_score<>''
		GROUP BY A.question_id, A.answer
		ORDER BY A.question_id, A.answer";
$result = mysql_query($sql, $db);
$ans = array();	
while ($row = mysql_fetch_assoc($result)) {
	$ans[$row['question_id']][$row['answer']] = array('count'=>$row['count(*)'], 'score'=>$row['score']);
}

//print out rows
foreach ($questions as $q_id => $q) {

	switch ($q['type']) {
		case AT_TESTS_MC:
			for ($i=0; $i<=10; $i++) {
				if ($q['choice_'.$i] == '') {
					$i--;
					break;
				}
			}
			print_multiple_choice($q, $ans[$q_id], $i, $num_results[0]);
			break;

		case AT_TESTS_TF:
			print_true_false($q, $ans[$q_id], $num_results[0]);
			break;

		case AT_TESTS_LONG:

			//get score of answers
			print_long($q, $ans[$q_id]);
			break;

		case AT_TESTS_LIKERT:
			for ($i=0; $i<=10; $i++) {
				if ($q['choice_'.$i] == '') {
					$i--;
					break;
				}
			}
			print_likert($q, $ans[$q_id], $i, $num_results[0]);
			break;
	}
}

require(AT_INCLUDE_PATH.'footer.inc.php');
?>