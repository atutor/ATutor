<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/test_result_functions.inc.php');

if (isset($_POST['back'])) {
	header('Location: my_tests.php');
	exit;
} 

if (defined('AT_FORCE_GET_FILE') && AT_FORCE_GET_FILE) {
	$content_base_href = 'get.php/';
} else {
	$course_base_href = 'content/' . $_SESSION['course_id'] . '/';
}

require(AT_INCLUDE_PATH.'header.inc.php');

$tid = intval($_GET['tid']);
$rid = intval($_GET['rid']);

$sql	= "SELECT title FROM ".TABLE_PREFIX."tests WHERE test_id=$tid AND course_id=$_SESSION[course_id]";
$result	= mysql_query($sql, $db);
$row	= mysql_fetch_array($result);
$test_title	= $row['title'];

$mark_right = ' <img src="'.$_base_path.'images/checkmark.gif" alt="'._AT('correct_answer').'" title="'._AT('correct_answer').'" />';
$mark_wrong = ' <img src="'.$_base_path.'images/x.gif" alt="'._AT('wrong_answer').'" title="'._AT('wrong_answer').'" />';

$sql	= "SELECT * FROM ".TABLE_PREFIX."tests_results WHERE result_id=$rid AND member_id=$_SESSION[member_id]";
$result	= mysql_query($sql, $db); 
if (!$row = mysql_fetch_assoc($result)){
	$msg->printErrors('RESULT_NOT_FOUND');
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}
$final_score= $row['final_score'];

//make sure they're allowed to see results now
$sql	= "SELECT result_release, out_of FROM ".TABLE_PREFIX."tests WHERE test_id=$tid AND course_id=$_SESSION[course_id]";
$result	= mysql_query($sql, $db); 
$row = mysql_fetch_assoc($result);

if ( ($row['result_release']==AT_RELEASE_NEVER) || ($row['result_release']==AT_RELEASE_MARKED && $final_score=='') ) {
	$msg->printErrors('RESULTS_NOT_RELEASED');
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

$out_of = $row['out_of'];

// $sql	= "SELECT * FROM ".TABLE_PREFIX."tests_questions WHERE course_id=$_SESSION[course_id] AND test_id=$tid ORDER BY ordering, question_id";

/* Retrieve randomly choosed questions */
$sql	= "SELECT question_id FROM ".TABLE_PREFIX."tests_answers WHERE result_id=$rid";
$result	= mysql_query($sql, $db); 
$row = mysql_fetch_array($result);
$random_id_string = $row[question_id];
$row = mysql_fetch_array($result);	
while ($row['question_id'] != '') {
	$random_id_string = $random_id_string.','.$row['question_id'];
	$row = mysql_fetch_array($result);
}
if (!$random_id_string) {
	$random_id_string = 0;
}

$sql	= "SELECT TQ.*, TQA.* FROM ".TABLE_PREFIX."tests_questions TQ INNER JOIN ".TABLE_PREFIX."tests_questions_assoc TQA USING (question_id) WHERE TQA.test_id=$tid AND TQ.question_id IN ($random_id_string) ORDER BY TQA.ordering, TQ.question_id";	
$result	= mysql_query($sql, $db); 

$count = 1;
echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'">';

if ($row = mysql_fetch_assoc($result)){
	echo '<div class="input-form">';
	echo '<h2>'.AT_print($test_title, 'tests.title').'</h2>';

	do {
		/* get the results for this question */
		$sql		= "SELECT * FROM ".TABLE_PREFIX."tests_answers WHERE result_id=$rid AND question_id=$row[question_id] AND member_id=$_SESSION[member_id]";
		$result_a	= mysql_query($sql, $db); 
		$answer_row = mysql_fetch_assoc($result_a);

		echo '<div class="row"><h3>'.$count.')</h3>';
		$count++;

		switch ($row['type']) {
			case AT_TESTS_MC:
				/* multiple choice question */

				if ($row['weight']) {
					print_score($row['answer_'.$answer_row['answer']], $row['weight'], $row['question_id'], $answer_row['score'], false, true);
					echo '<br />';
				}

				echo AT_print($row['question'], 'tests_questions.question').'<br /><p>';

				if (array_sum(array_slice($row, 16, -6)) > 1) {
					$answer_row['answer'] = explode('|', $answer_row['answer']);
				}

				/* for each non-empty choice: */
				for ($i=0; ($i < 10) && ($row['choice_'.$i] != ''); $i++) {
					if ($i > 0) {
						echo '<br />';
					}
					if (is_array($answer_row['answer'])) {
						print_result($row['choice_'.$i], $row['answer_'.$i], $i, (int) in_array($i, $answer_row['answer']), $row['answer_'.$answer_row['answer']]);
		
						if (is_array($answer_row['answer']) && ($row['answer_'.$i] == 1) && in_array($i, $answer_row['answer'])) {
							echo $mark_right;
						} else if (is_array($answer_row['answer']) && ($row['answer_'.$i] != 1) && in_array($i, $answer_row['answer'])) {
							echo $mark_wrong;
						}
					} else {
						print_result($row['choice_'.$i], $row['answer_'.$i], $i, $answer_row['answer'], $row['answer_'.$answer_row['answer']]);
						if (($row['answer_'.$i] == 1)  && (!$row['answer_'.$answer_row['answer']])) {
							echo $mark_right;
						}
					}
				}
				echo '<br />';
				if (!is_array($answer_row['answer'])) {
					print_result('<em>'._AT('left_blank').'</em>', -1, -1, (int) (-1 == $answer_row['answer']), false);
				}

				echo '</p>';
				$my_score=($my_score+$answer_row['score']);
				$this_total += $row['weight'];
				break;

			case AT_TESTS_TF:
				/* true or false question */
				if ($row['weight']) {
					print_score($row['answer_'.$answer_row['answer']], $row['weight'], $row['question_id'], $answer_row['score'], false, true);
					echo '<br />';
				}
				echo AT_print($row['question'], 'tests_questions.question').'<br /><p>';

				/* avman */
				if($answer_row['answer']== $row['answer_0']){
					$correct=1;
				} else {
					$correct='';
				}

				print_result(_AT('true'), $row['answer_0'], 1, (int) ($answer_row['answer'] == 1), $correct);

				print_result(_AT('false'), $row['answer_1'], 2, (int) ($answer_row['answer'] == 2), $correct);

				echo '<br />';
				print_result('<em>'._AT('left_blank').'</em>', -1, -1, (int) ($answer_row['answer'] == -1), false);
				$my_score=($my_score+$answer_row['score']);
				$this_total += $row['weight'];
				echo '</p>';
				break;

			case AT_TESTS_LONG:
				/* long answer question */

				if ($row['weight']) {
					print_score($row['answer_'.$answer_row['answer']], $row['weight'], $row['question_id'], $answer_row['score'], false, true);
					echo '<br />';
				}

				echo AT_print($row['question'], 'tests_questions.question').'<br /><p><br />';
				echo AT_print($answer_row['answer'], 'tests_answers.answer');	
				echo '</p><br />';
				$my_score=($my_score+$answer_row['score']);
				$this_total += $row['weight'];
				echo '</p><br />';
				break;

			case AT_TESTS_LIKERT:
				/* Likert question */
				echo AT_print($row['question'], 'tests_questions.question').'<br /><p>';

				/* for each non-empty choice: */
				for ($i=0; ($i < 10) && ($row['choice_'.$i] != ''); $i++) {
					if ($i > 0) {
						echo '<br />';
					}
					print_result($row['choice_'.$i], '' , $i, AT_print($answer_row['answer'], 'tests_answers.answer'), 'none');
				}

				echo '<br />';

				print_result('<em>'._AT('left_blank').'</em>', -1, -1, AT_print($answer_row['answer'], 'tests_answers.answer'), 'none');
				echo '</p>';
				$my_score=($my_score+$answer_row['score']);
				$this_total += $row['weight'];
				break;
		}


		if ($row['feedback'] == '') {
			//echo '<em>'._AT('none').'</em>.';
		} else {
			echo '<p><strong>'._AT('feedback').':</strong> ';
			echo nl2br($row['feedback']).'</p>';
		}
		echo '</div>';
	} while ($row = mysql_fetch_assoc($result));

	if ($this_total) {
		echo '<div class="row"><strong>'.$my_score.'/'.$this_total.'</strong></div>';
	}
} else {
	echo '<p>'._AT('no_questions').'</p>';
}
echo '<div class="row buttons">';
	echo '<input type="submit" value="'._AT('back').'" name="back" />';
echo '</div>';
echo '</div></form>';
require(AT_INCLUDE_PATH.'footer.inc.php');
?>