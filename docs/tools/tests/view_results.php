<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay & Joel Kronenberg        */
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
authenticate(AT_PRIV_TESTS);
require(AT_INCLUDE_PATH.'lib/test_result_functions.inc.php');

$tid = intval($_GET['tid']);
if ($tid == 0){
	$tid = intval($_POST['tid']);
}

$_pages['tools/tests/view_results.php']['title_var']  = 'view_results';
$_pages['tools/tests/view_results.php']['parent'] = 'tools/tests/results.php?tid='.$tid;

$_pages['tools/tests/results.php?tid='.$tid]['title_var'] = 'submissions';
$_pages['tools/tests/results.php?tid='.$tid]['parent'] = 'tools/tests/index.php';


if ($_POST['cancel']) {
	$msg->addFeedback('CANCELLED');
	header('Location: results.php?tid='.$tid);
	exit;
} else if ($_POST['back']) {
	header('Location: results.php?tid='.$tid);
	exit;
} else if ($_POST['submit']) {
	$tid = intval($_POST['tid']);
	$rid = intval($_POST['rid']);
		
	$final_score = 0;
	if (is_array($_POST['scores'])) {
		foreach ($_POST['scores'] as $qid => $score) {
			$score		  = floatval($score);
			$final_score += $score;

			$sql	= "UPDATE ".TABLE_PREFIX."tests_answers SET score='$score' WHERE result_id=$rid AND question_id=$qid";
			$result	= mysql_query($sql, $db);
		}
	}

	$sql	= "UPDATE ".TABLE_PREFIX."tests_results SET final_score='$final_score' WHERE result_id=$rid";
	$result	= mysql_query($sql, $db);

	$msg->addFeedback('RESULTS_UPDATED');
	header('Location: results.php?tid='.$tid);
	exit;
}

if (defined('AT_FORCE_GET_FILE') && AT_FORCE_GET_FILE) {
	$content_base_href = 'get.php/';
} else {
	$course_base_href = 'content/' . $_SESSION['course_id'] . '/';
}

require(AT_INCLUDE_PATH.'header.inc.php');
	
$sql	= "SELECT * FROM ".TABLE_PREFIX."tests WHERE test_id=$tid AND course_id=$_SESSION[course_id]";
$result	= mysql_query($sql, $db);

if (!($row = mysql_fetch_array($result))){
	$msg->printErrors('TEST_NOT_FOUND');
	require (AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}
$test_title = $row['title'];
$out_of		= $row['out_of'];

$tid = intval($_GET['tid']);
$rid = intval($_GET['rid']);

$mark_right = '<img src="'.$_base_path.'images/checkmark.gif" alt="'._AT('correct_answer').'" />';
$mark_wrong = '<img src="'.$_base_path.'images/x.gif" alt="'._AT('wrong_answer').'" />';

$sql	= "SELECT TQ.*, TQA.* FROM ".TABLE_PREFIX."tests_questions TQ INNER JOIN ".TABLE_PREFIX."tests_questions_assoc TQA USING (question_id) WHERE TQ.course_id=$_SESSION[course_id] AND TQA.test_id=$tid ORDER BY TQA.ordering";
$result	= mysql_query($sql, $db);

$count = 1;
echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'">';
echo '<input type="hidden" name="tid" value="'.$tid.'">';
echo '<input type="hidden" name="rid" value="'.$rid.'">';

if ($row = mysql_fetch_assoc($result)){
	echo '<div class="input-form">';
	echo '<h2>'.AT_print($test_title, 'tests.title').'</h2>';

	do {
		/* get the results for this question */
		$sql		= "SELECT DISTINCT C.question_id as q,C.* FROM ".TABLE_PREFIX."tests_answers C WHERE C.result_id=$rid AND C.question_id=$row[question_id] group by question_id";
		$result_a	= mysql_query($sql, $db);
		$answer_row = mysql_fetch_assoc($result_a);
		echo '<div class="row">';
		if ($answer_row != '') {
			echo '<h3>'.$count.')</h3> ';
			$count++;
			
			if ($row['properties'] == AT_TESTS_QPROP_ALIGN_VERT) {
				$spacer = '<br />';
			} else {
				$spacer = ', ';
			}
			switch ($row['type']) {
				case AT_TESTS_MC:
					/* multiple choice question */
					if ($row['weight']) {
						print_score($row['answer_'.$answer_row['answer']], $row['weight'], $row['question_id'], $answer_row['score']);
						echo '<br /><br />';
					}
					echo AT_print($row['question'], 'tests_questions.question').'<br /><p>';

					if (array_sum(array_slice($row, 16, -6)) > 1) {
						$answer_row['answer'] = explode('|', $answer_row['answer']);
					}

					/* for each non-empty choice: */
					for ($i=0; ($i < 10) && ($row['choice_'.$i] != ''); $i++) {
						if ($i > 0) {
							echo $spacer;
						}

						if (is_array($answer_row['answer'])) {
							print_result($row['choice_'.$i], $row['answer_'.$i], $i, (int) in_array($i, $answer_row['answer']), $row['answer_'.$answer_row['answer']]);
			
							if (is_array($answer_row['answer']) && ($row['answer_'.$i] == 1) && in_array($i, $answer_row['answer'])) {
								echo $mark_right;
							} else if (is_array($answer_row['answer']) && ($row['answer_'.$i] != 1) && in_array($i, $answer_row['answer'])) {
								echo $mark_wrong;
							}
						} else {
							print_result($row['choice_'.$i], $row['answer_'.$i], $i, (int) ($i == $answer_row['answer']), $row['answer_'.$answer_row['answer']]);
							if (($row['answer_'.$i] == 1) && ($answer_row['answer'] == $i)) {
								echo $mark_right;
							} else if ($row['answer_'.$i] == 1) {
								echo $mark_wrong;
							}
						}
					}
					if (!is_array($answer_row['answer'])) {
						echo $spacer;
						print_result('<em>'._AT('left_blank').'</em>', -1, -1, (int) (-1 == $answer_row['answer']), false);
						if ($answer_row['answer'] == -1) {
							echo $mark_wrong;
						}
					}

					echo '</p>';
					break;

				case AT_TESTS_TF:
					/* true or false quastion */
					if($answer_row['answer']== $row['answer_0']){
						$correct=1;
					}else{
						$correct='';
					}
					if ($row['weight']) {
						print_score($correct, $row['weight'], $row['question_id'], $answer_row['score'], $put_zero = true);
						echo '<br /><br />';
					}

					echo AT_print($row['question'], 'tests_questions.question').'<br /><p>';

					print_result(_AT('true'), $row['answer_0'], 1, (int) ($answer_row['answer'] == 1), $correct);
					if (($answer_row['answer'] == 1) && ($row['answer_0'] == 1)){
						echo $mark_right;
					} else if ($row['answer_0'] == 1) {
						echo $mark_wrong;
					}
					echo '<br />';

					print_result(_AT('false'), $row['answer_0'], 2, (int) ($answer_row['answer'] == 2), $correct);
					if (($answer_row['answer'] == 2) && ($row['answer_0'] == 2)){
						echo $mark_right;
					} else if ($row['answer_0'] == 2) {
						echo $mark_wrong;
					}
					echo '<br />';
					print_result('<em>'._AT('left_blank').'</em>', -1, -1, (int) ($answer_row['answer'] == -1), false);
					if ($answer_row['answer'] == -1) {
						echo $mark_wrong;
					}

					echo '</p>';
					break;

				case AT_TESTS_LONG:
					if ($row['weight']) {
						print_score($row['answer_'.$answer_row['answer']], $row['weight'], $row['question_id'], $answer_row['score'], false);
						echo '<br /><br />';
					}
					echo AT_print($row['question'], 'tests_questions.question').'<br /><p><br />';
					echo AT_print($answer_row['answer'], 'tests_answers.answer');	
					echo '</p><br />';
					break;
				case AT_TESTS_LIKERT:
					echo AT_print($row['question'], 'tests_questions.question').'<br /><p>';

					/* for each non-empty choice: */
					for ($i=0; ($i < 10) && ($row['choice_'.$i] != ''); $i++) {
						if ($i > 0) {
							echo $spacer;
						}
						print_result($row['choice_'.$i], $row['answer_'.$i], $i, $answer_row['answer'], 'none');
					}

					echo $spacer;

					print_result('<em>'._AT('left_blank').'</em>', -1, -1, $answer_row['answer'], 'none');
					echo '</p>';
					break;
			}
		}			
		echo '</div>';
	} while ($row = mysql_fetch_assoc($result));

	echo '<div class="row buttons">';
	if ($out_of) {
		echo '<input type="submit" value="'._AT('save').'" name="submit" accesskey="s" /> <input type="submit" value="'._AT('cancel').'" name="cancel" />';
	} else {
		echo '<input type="submit" value="'._AT('back').'" name="back" />';
	}
	echo '</div>';

} else {
	echo '<p>'._AT('no_questions').'</p>';
}
	echo '</div>';

echo '</form>';

require(AT_INCLUDE_PATH.'footer.inc.php');
?>