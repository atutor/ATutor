<?php

exit('this file is no longer used');

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
// $Id$
$page = 'tests';
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_TESTS);
$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/';
$_section[1][0] = _AT('tests');
$_section[1][1] = 'tools/tests';
$_section[2][0] = _AT('results');


$_GET['tt'] = str_replace(' ', '_', $_GET['tt']);

$tt = urldecode($_GET['tt']);
if($tt == ''){
	$tt = str_replace(' ', '_', $_POST['tt']);
}

$tid = intval($_GET['tid']);
if ($tid == 0){
	$tid = intval($_POST['tid']);
}

$nl = "\n";


function quote_csv($line) {
	$line = str_replace('"', '""', $line);

	$line = str_replace("\n", '\n', $line);
	$line = str_replace("\r", '\r', $line);
	$line = str_replace("\x00", '\0', $line);

	return '"'.$line.'"';
}


function print_likert($question, $answers, $num, $num_results) {
	$nl = "\n";

	echo _AT('question').', '._AT('left_blank').', '._AT('average').' '._AT('answer');
	for ($i=1; $i<=$num+1; $i++) {
		echo ', '.$i;
	}
	echo $nl;

	echo $question;

	//blank
	echo ', '.round($answers[-1]['count']/$num_results*100).'%';

	//avg choice 
	$sum = 0;
	for ($j=0; $j<=$num; $j++) {
		$sum += ($j+1) * $answers[$j]['count'];
	}

	$num_valid = $num_results - $answers[-1]['count'];
	//check if only blanks given
	echo ', ';
	if ($num_valid) {
		echo round($sum/$num_valid, 1);
	} else  {
		echo '-';
	}

	for ($j=0; $j<=$num; $j++) {
		echo ', '.round($answers[$j]['count']/$num_results*100).'%';
	}
	echo $nl;

	return true;
}


function print_true_false($q, $answers, $num_results) {
	$nl = "\n";

	echo _AT('question').', '._AT('left_blank').', '._AT('true').', '._AT('false');

	echo $nl;
	echo $q['question'];

	//blank
	echo ', '.round($answers[-1]['count']/$num_results*100).'%';

	echo ', '.round($answers[1]['count']/$num_results*100).'%';
	echo ', '.round($answers[2]['count']/$num_results*100).'%';

	echo $nl;

	return true;
}


function print_multiple_choice($q, $answers, $num, $num_results) {
	$nl = "\n";

	echo _AT('question').', '._AT('left_blank');

	for ($i=1; $i<=$num+1; $i++) {
		echo ', '.$q['choice_'.($i-1)];
	}

	echo $nl;
	echo $q['question'];

	$sum = 0;
	for ($j=0; $j<=$num; $j++) {
		$sum += $answers[$j]['score'];
	}

	//blank
	echo ', '.round($answers[-1]['count']/$num_results*100).'%';

	for ($j=0; $j<=$num; $j++) {
		echo ', '.round($answers[$j]['count']/$num_results*100).'%';
	}

	echo $nl;

	return true;
}


function print_long($q, $answers, $num_results) {
	$nl = "\n";

	echo _AT('question').', '._AT('left_blank').', '._AT('results');

	echo $nl;
	echo $q['question'];

	echo ', '.round($answers[-1]['count']/$num_results*100).'%';

	if ($answers) {
		foreach ($answers as $answer=>$info) {
			if ($answer != -1) {
				echo ', '.round($info['count']/$num_results*100).'% - '.$answer.$nl.', ';
			}
		}
	} else {
		echo ', 0%'.$nl;      // if no one has taken this test
	}
}

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

//get total #results
$sql	= "SELECT COUNT(*) FROM ".TABLE_PREFIX."tests_results R WHERE R.test_id=$tid AND R.final_score<>''";
$result = mysql_query($sql, $db);
$num_results = mysql_num_rows($result);

// This is to prevent division by zero in cases where the test has not been taken but an average is calculated (i.e. 0/0)
if (!$num_results) {
	$num_results = 1;
}

debug($num_results);
exit;

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
$sql = "SELECT count(*), question_id, answer, score
		FROM ".TABLE_PREFIX."tests_answers 
		GROUP BY question_id, answer
		ORDER BY question_id, answer";
$result = mysql_query($sql, $db);
$ans = array();	
while ($row = mysql_fetch_assoc($result)) {
	$ans[$row['question_id']][$row['answer']] = array('count'=>$row['count(*)'], 'score'=>$row['score']);
}


/*header('Content-Type: application/x-excel');
header('Content-Disposition: inline; filename="'.$_GET['tt'].'.csv"');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');*/

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
			echo $nl;
			break;

		case AT_TESTS_TF:
			print_true_false($q, $ans[$q_id], $num_results[0]);
			echo $nl;
			break;

		case AT_TESTS_LONG:

			//get score of answers
			print_long($q, $ans[$q_id], $num_results[0]);
			echo $nl;
			break;

		case AT_TESTS_LIKERT:
			for ($i=0; $i<=10; $i++) {
				if ($q['choice_'.$i] == '') {
					$i--;
					break;
				}
			}
			print_likert($q['question'], $ans[$q_id], $i, $num_results[0]);
			echo $nl;
			break;
	}
}

?>