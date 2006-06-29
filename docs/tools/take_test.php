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
define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/test_result_functions.inc.php');


/* check to make sure we can access this test: */
if ($_SESSION['enroll'] == AT_ENROLL_NO || $_SESSION['enroll'] == AT_ENROLL_ALUMNUS) {
	require(AT_INCLUDE_PATH.'header.inc.php');
	$msg->printInfos('NOT_ENROLLED');

	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

$tid = intval($_REQUEST['tid']);

if (!authenticate_test($tid)) {
	header('Location: my_tests.php');
	exit;
}

//make sure max attempts not reached, and still on going
$sql		= "SELECT *, UNIX_TIMESTAMP(start_date) AS start_date, UNIX_TIMESTAMP(end_date) AS end_date FROM ".TABLE_PREFIX."tests WHERE test_id=".$tid." AND course_id=".$_SESSION['course_id'];
$result= mysql_query($sql, $db);
$test_row = mysql_fetch_assoc($result);
$out_of = $test_row['out_of'];

$sql		= "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."tests_results WHERE test_id=".$tid." AND member_id=".$_SESSION['member_id'];
$takes_result= mysql_query($sql, $db);
$takes = mysql_fetch_assoc($takes_result);	


if ( (($test_row['start_date'] > time()) || ($test_row['end_date'] < time())) || 
   ( ($test_row['num_takes'] != AT_TESTS_TAKE_UNLIMITED) && ($takes['cnt'] >= $test_row['num_takes']) )  ) {
	require(AT_INCLUDE_PATH.'header.inc.php');
	$msg->printErrors('MAX_ATTEMPTS');
	
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

if (isset($_POST['submit'])) {
	// insert

	$sql	= "INSERT INTO ".TABLE_PREFIX."tests_results VALUES (0, $tid, $_SESSION[member_id], NOW(), '')";
	$result	= mysql_query($sql, $db);
	$result_id = mysql_insert_id($db);

	$final_score     = 0;
	$set_final_score = TRUE; // whether or not to save the final score in the results table.

	$sql	= "SELECT TQA.weight, TQA.question_id, TQ.type, TQ.answer_0, TQ.answer_1, TQ.answer_2, TQ.answer_3, TQ.answer_4, TQ.answer_5, TQ.answer_6, TQ.answer_7, TQ.answer_8, TQ.answer_9 FROM ".TABLE_PREFIX."tests_questions_assoc TQA INNER JOIN ".TABLE_PREFIX."tests_questions TQ USING (question_id) WHERE TQA.test_id=$tid ORDER BY TQA.ordering, TQ.question_id";
	$result	= mysql_query($sql, $db);
	while ($row = mysql_fetch_assoc($result)) {
		if (isset($_POST['answers'][$row['question_id']])) {
			$score = 0;

			switch ($row['type']) {
				case AT_TESTS_MC:
					// multiple choice
					$num_correct = array_sum(array_slice($row, 3));
					if ($num_correct > 1) {
						// multi correct
						if (is_array($_POST['answers'][$row['question_id']])) {
							$num_answer_correct = 0;
							foreach ($_POST['answers'][$row['question_id']] as $answer) {
								if ($row['answer_' . $answer]) {
									// correct answer
									$num_answer_correct++;
								} else {
									// wrong answer
									$num_answer_correct--;
								}
							}
							if ($num_answer_correct == $num_correct) {
								$score = $row['weight'];
							} else {
								$score = 0;
							}
						} else {
							// no answer given
							$_POST['answers'][$row['question_id']] = '-1'; // left blank
							$score = 0;
						}
						$_POST['answers'][$row['question_id']] = implode('|', $_POST['answers'][$row['question_id']]);
					} else {
						// single correct answer
						$_POST['answers'][$row['question_id']] = intval($_POST['answers'][$row['question_id']]);
						if ($row['answer_' . $_POST['answers'][$row['question_id']]]) {
							$score = $row['weight'];
						} else if ($_POST['answers'][$row['question_id']] == -1) {
							$has_answer = 0;
							for($i=0; $i<10; $i++) {
								$has_answer += $row['answer_'.$i];
							}
							if (!$has_answer && $row['weight']) {
								// If MC has no answer and user answered "leave blank"
								$score = $row['weight'];
							}
						}
					}

					break;

				case AT_TESTS_TF:
					// true or false
					$_POST['answers'][$row['question_id']] = intval($_POST['answers'][$row['question_id']]);

					if ($row['answer_0'] == $_POST['answers'][$row['question_id']]) {
						$score = $row['weight'];
					}
					break;

				case AT_TESTS_LONG:
					// open ended question
					$_POST['answers'][$row['question_id']] = $addslashes($_POST['answers'][$row['question_id']]);
					$scores = ''; // open ended can't be marked automatically

					$set_final_score = FALSE;
					break;

				case AT_TESTS_LIKERT:
					$_POST['answers'][$row['question_id']] = intval($_POST['answers'][$row['question_id']]);
					break;
			} // end switch

			$sql	= "INSERT INTO ".TABLE_PREFIX."tests_answers VALUES ($result_id, $row[question_id], $_SESSION[member_id], '{$_POST[answers][$row[question_id]]}', '$score', '')";
			mysql_query($sql, $db);

			$final_score += $score;
		}
	}

	if ($set_final_score || !$out_of) {
		// update the final score (when no open ended questions are found)
		$sql	= "UPDATE ".TABLE_PREFIX."tests_results SET final_score=$final_score WHERE result_id=$result_id AND member_id=$_SESSION[member_id]";
		$result	= mysql_query($sql, $db);
	}

	$msg->addFeedback('TEST_SAVED');
	header('Location: ../tools/my_tests.php');
	exit;		
}

if (defined('AT_FORCE_GET_FILE') && AT_FORCE_GET_FILE) {
	$content_base_href = 'get.php/';
} else {
	$course_base_href = 'content/' . $_SESSION['course_id'] . '/';
}

require(AT_INCLUDE_PATH.'header.inc.php');

/* Retrieve the content_id of this test */
$num_questions = $test_row['num_questions'];	
$content_id = $test_row['content_id'];
$anonymous = $test_row['anonymous'];
$instructions = $test_row['instructions'];
$title = $test_row['title'];

if ($test_row['random']) {
	/* Retrieve 'num_questions' question_id randomly choosed from those who are related to this test_id*/

	$non_required_questions = array();
	$required_questions     = array();

	$sql    = "SELECT question_id, required FROM ".TABLE_PREFIX."tests_questions_assoc WHERE test_id=$tid";
	$result	= mysql_query($sql, $db);
	
	while ($row = mysql_fetch_assoc($result)) {
		if ($row['required'] == 1) {
			$required_questions[] = $row['question_id'];
		} else {
			$non_required_questions[] = $row['question_id'];
		}
	}

	$num_required = count($required_questions);
	if ($num_required < $num_questions) {
		shuffle($non_required_questions);
		$required_questions = array_merge($required_questions, array_slice($non_required_questions, 0, $num_questions - $num_required));
	}

	$random_id_string = implode(',', $required_questions);

	$sql = "SELECT TQ.*, TQA.* FROM ".TABLE_PREFIX."tests_questions TQ INNER JOIN ".TABLE_PREFIX."tests_questions_assoc TQA USING (question_id) WHERE TQ.course_id=$_SESSION[course_id] AND TQA.test_id=$tid AND TQA.question_id IN ($random_id_string)";

} else {
	$sql	= "SELECT TQ.*, TQA.* FROM ".TABLE_PREFIX."tests_questions TQ INNER JOIN ".TABLE_PREFIX."tests_questions_assoc TQA USING (question_id) WHERE TQ.course_id=$_SESSION[course_id] AND TQA.test_id=$tid ORDER BY TQA.ordering, TQA.question_id";
}

$result	= mysql_query($sql, $db);

$questions = array();
while ($row = mysql_fetch_assoc($result)) {
	$questions[] = $row;
}

if ($test_row['random']) {
	srand((float)microtime() * 1000000);
	shuffle($questions);
}

$count = 1;
if ($result && $questions) {
	echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'">';
	echo '<input type="hidden" name="tid" value="'.$tid.'" />';

	echo '<div class="input-form" style="width:80%;">';
	echo '<div class="row">';
	echo '<h2>'.$title.'</h2>';

	if ($instructions!='') {
		echo '<p><br /><strong>'._AT('instructions').'</strong>:  '. $instructions .'</p>';
	}
	if ($anonymous) {
		echo '<em><strong>'._AT('test_anonymous').'</strong></em>';
	}
	echo '</div>';
	foreach ($questions as $row) {
		echo '<div class="row"><h3>'.$count.')</h3> ';
		$count++;
		if ($row['properties'] == AT_TESTS_QPROP_ALIGN_VERT) {
			$spacer = '<br />';
		} else {
			$spacer = ', ';
		}

		switch ($row['type']) {
			case AT_TESTS_MC:

				if ($row['weight']) {
					if ($row['weight'] == 1) {
						echo '('.$row['weight'].' '.strtolower(_AT('mark')).')';
					} else {
						echo '('.$row['weight'].' '._AT('marks').')';
					}
				}
				echo '<p>'.AT_print($row['question'], 'tests_questions.question').'</p><p>';
				if (array_sum(array_slice($row, 16, -6)) > 1) {
					echo '<input type="hidden" name="answers['.$row['question_id'].'][]" value="-1" />';
					for ($i=0; $i < 10; $i++) {
						if ($row['choice_'.$i] != '') {
							if ($i > 0) {
								echo $spacer;
							}

							echo '<input type="checkbox" name="answers['.$row['question_id'].'][]" value="'.$i.'" id="choice_'.$row['question_id'].'_'.$i.'" /><label for="choice_'.$row['question_id'].'_'.$i.'">'.AT_print($row['choice_'.$i], 'tests_questions.choice_'.$i).'</label>';
						}
					}
				} else {
					for ($i=0; $i < 10; $i++) {
						if ($row['choice_'.$i] != '') {
							if ($i > 0) {
								echo $spacer;
							}

							echo '<input type="radio" name="answers['.$row['question_id'].']" value="'.$i.'" id="choice_'.$row['question_id'].'_'.$i.'" /><label for="choice_'.$row['question_id'].'_'.$i.'">'.AT_print($row['choice_'.$i], 'tests_questions.choice_'.$i).'</label>';
						}
					}

					echo $spacer;
					echo '<input type="radio" name="answers['.$row['question_id'].']" value="-1" id="choice_'.$row['question_id'].'_x" checked="checked" /><label for="choice_'.$row['question_id'].'_x"><em>'._AT('leave_blank').'</em></label>';
				}
				break;

			case AT_TESTS_TF:
				/* true or false question */
				if ($row['weight']) {
					if ($row['weight'] == 1) {
						echo '('.$row['weight'].' '.strtolower(_AT('mark')).')';
					} else {
						echo '('.$row['weight'].' '._AT('marks').')';
					}
				}

				echo '<p>'.AT_print($row['question'], 'tests_questions').'</p><p>';
				echo '<input type="radio" name="answers['.$row['question_id'].']" value="1" id="choice_'.$row['question_id'].'_0" /><label for="choice_'.$row['question_id'].'_0">'._AT('true').'</label>';

				echo $spacer;
				echo '<input type="radio" name="answers['.$row['question_id'].']" value="2" id="choice_'.$row['question_id'].'_1" /><label for="choice_'.$row['question_id'].'_1">'._AT('false').'</label>';

				echo $spacer;
				echo '<input type="radio" name="answers['.$row['question_id'].']" value="-1" id="choice_'.$row['question_id'].'_x" checked="checked" /><label for="choice_'.$row['question_id'].'_x"><em>'._AT('leave_blank').'</em></label>';
				break;

			case AT_TESTS_LONG:
				if ($row['weight']) {
					if ($row['weight'] == 1) {
						echo '('.$row['weight'].' '.strtolower(_AT('mark')).')';
					} else {
						echo '('.$row['weight'].' '._AT('marks').')';
					}
				}
				echo '<p>'.AT_print($row['question'], 'tests_questions').'</p><p>';
				switch ($row['properties']) {
					case 1:
							/* one word */
							echo '<input type="text" name="answers['.$row['question_id'].']" class="formfield" size="15" />';
						break;

					case 2:
							/* sentence */
							echo '<input type="text" name="answers['.$row['question_id'].']" class="formfield" size="45" />';
						break;
				
					case 3:
							/* paragraph */
							echo '<textarea cols="55" rows="5" name="answers['.$row['question_id'].']" class="formfield"></textarea>';
						break;

					case 4:
							/* page */
							echo '<textarea cols="55" rows="25" name="answers['.$row['question_id'].']" class="formfield"></textarea>';
						break;
				}
				break;
			case AT_TESTS_LIKERT:
				if ($row['weight']) {
					if ($row['weight'] == 1) {
						echo '('.$row['weight'].' '.strtolower(_AT('mark')).')';
					} else {
						echo '('.$row['weight'].' '._AT('marks').')';
					}
				}
				echo '<p>'.AT_print($row['question'], 'tests_questions.question').'</p><p>';

				for ($i=0; $i < 10; $i++) {
					if ($row['choice_'.$i] != '') {
						if ($i > 0) {
							echo $spacer;
						}

						echo '<input type="radio" name="answers['.$row['question_id'].']" value="'.$i.'" id="choice_'.$row['question_id'].'_'.$i.'" /><label for="choice_'.$row['question_id'].'_'.$i.'">'.AT_print($row['choice_'.$i], 'tests_answers.answer').'</label>';
					}
				}

				echo $spacer;
				echo '<input type="radio" name="answers['.$row['question_id'].']" value="-1" id="choice_'.$row['question_id'].'_x" checked="checked" /><label for="choice_'.$row['question_id'].'_x"><em>'._AT('leave_blank').'</em></label>';
				break;					
		}
		echo '</p>';
		echo '</div>';
	} while ($row = mysql_fetch_assoc($result));

	echo '<div class="row buttons">';
	echo '<input type="submit" name="submit" value="'._AT('submit').'" accesskey="s" />';
	echo '</div>';
	echo '</div>';
	echo '</form><br />';

} else {
	echo '<p>'._AT('no_questions').'</p>';
}

require(AT_INCLUDE_PATH.'footer.inc.php');
?>