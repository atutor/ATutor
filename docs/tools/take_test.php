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

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/test_result_functions.inc.php');

$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/';
$_section[1][0] = _AT('my_tests');
$_section[1][1] = 'tools/my_tests.php';
$_section[2][0] = _AT('take_test');

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
$sql		= "SELECT UNIX_TIMESTAMP(start_date) as start_date, UNIX_TIMESTAMP(end_date) as end_date, num_takes, out_of FROM ".TABLE_PREFIX."tests WHERE test_id=".$tid." AND course_id=".$_SESSION['course_id'];
$result= mysql_query($sql, $db);
$row = mysql_fetch_assoc($result);
$out_of = $row['out_of'];

$sql		= "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."tests_results WHERE test_id=".$tid." AND member_id=".$_SESSION['member_id'];
$takes_result= mysql_query($sql, $db);
$takes = mysql_fetch_assoc($takes_result);	


if ( (($row['start_date'] > time()) || ($row['end_date'] < time())) || 
   ( ($row['num_takes'] != AT_TESTS_TAKE_UNLIMITED) && ($takes['cnt'] >= $row['num_takes']) )  ) {
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
					$_POST['answers'][$row['question_id']] = intval($_POST['answers'][$row['question_id']]);

					if ($row['answer_' . $_POST['answers'][$row['question_id']]]) {
						$score = $row['weight'];
					} else {
						$score = 0;
					}
					break;

				case AT_TESTS_TF:
					// true or false
					$_POST['answers'][$row['question_id']] = intval($_POST['answers'][$row['question_id']]);

					if ($row['answer_0'] == $_POST['answers'][$row['question_id']]) {
						$score = $row['weight'];
					} else {
						$score = 0;
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
					$score = 0;
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

$tid = intval($_GET['tid']);
/* Retrieve the content_id of this test */
$sql = "SELECT * FROM ".TABLE_PREFIX."tests WHERE test_id=$tid";
$result	= mysql_query($sql, $db); 
$row = mysql_fetch_assoc($result);
$num_questions = $row['num_questions'];	
$content_id = $row['content_id'];
$anonymous = $row['anonymous'];

echo '<h2>'.$row['title'].'</h2>';

if ($row['instructions']!='') {
	echo '<p><br /><strong>'._AT('special_instructions').'</strong>:  ';  
	echo $row['instructions'];
	echo '</p>';
}
if ($anonymous) {
	echo '<em><strong>'._AT('test_anonymous').'</strong></em>';
}

if ($row['random']) {
	/* Retrieve 'num_questions' question_id randomly choosed from those who are related to this test_id*/
	$sql    = "SELECT question_id FROM ".TABLE_PREFIX."tests_questions_assoc WHERE test_id=$tid";
	$result	= mysql_query($sql, $db); 
	$i = 0;
	$row2 = mysql_fetch_assoc($result);
	$num_questions--;
	/* Store all related question in cr_questions */
	while ($row2['question_id'] != '') {
		$cr_questions[$i] = $row2['question_id'];
		$row2 = mysql_fetch_array($result);
		$i++;
	}
	/* Randomly choose only 'num_question' question */
	$random_idx = rand(0, $i-1);
	$random_id_string = $cr_questions[$random_idx];
	$j = 0;
	$extracted[$j] = $random_idx;
	$j++;

	/* if we have less questions than we're asking for (ie 2 questions, but want to randomize out of 10) */
	$num_questions = min($num_questions, count($cr_questions)-1);

	while ($num_questions > 0) {
		$done = false;
	
		$k = 0;
		while (!$done && ($k<20)) {
			$random_idx = rand(0, $i-1);
			$done = true;
			if (in_array($random_idx, $extracted)) {
				$done = false;
			}
			$k++;
		}

		$extracted[$j] = $random_idx;
		$j++;
		$random_id_string = $random_id_string.','.$cr_questions[$random_idx];
		$num_questions--;
	}
	//$sql = "SELECT * FROM ".TABLE_PREFIX."tests_questions WHERE question_id IN ($random_id_string) ORDER BY ordering, question_id";

	$sql = "SELECT TQ.*, TQA.* FROM ".TABLE_PREFIX."tests_questions TQ INNER JOIN ".TABLE_PREFIX."tests_questions_assoc TQA USING (question_id) WHERE TQ.course_id=$_SESSION[course_id] AND TQA.test_id=$tid AND TQA.question_id IN ($random_id_string) ORDER BY TQA.ordering, TQA.question_id";
} else {
	//$sql = "SELECT * FROM ".TABLE_PREFIX."tests_questions WHERE course_id=$_SESSION[course_id] AND test_id=$tid ORDER BY ordering, question_id";
	$sql	= "SELECT TQ.*, TQA.* FROM ".TABLE_PREFIX."tests_questions TQ INNER JOIN ".TABLE_PREFIX."tests_questions_assoc TQA USING (question_id) WHERE TQ.course_id=$_SESSION[course_id] AND TQA.test_id=$tid ORDER BY TQA.ordering, TQA.question_id";
}

$result	= mysql_query($sql, $db);

$count = 1;
if ($row = @mysql_fetch_assoc($result)){
	echo '<table class="bodyline" width="90%"><tr><td>';
	echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'">';
	echo '<input type="hidden" name="tid" value="'.$tid.'" />';
	echo '<ol>';
	do {
		$count++;
		if ($row['properties'] == AT_TESTS_QPROP_ALIGN_VERT) {
			$spacer = '<br />';
		} else {
			$spacer = ', ';
		}

		switch ($row['type']) {
			case AT_TESTS_MC:
				echo '<li>';
				if ($row['weight']) {
					echo '('.$row['weight'].' '._AT('marks').')';
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
				echo '<input type="radio" name="answers['.$row['question_id'].']" value="-1" id="choice_'.$row['question_id'].'_x" checked="checked" /><label for="choice_'.$row['question_id'].'_x"><i>'._AT('leave_blank').'</i></label>';
				break;

			case AT_TESTS_TF:
				/* true or false question */
				echo '<li>';
				if ($row['weight']) {
					echo '('.$row['weight'].' '._AT('marks').')';
				}	

				echo '<p>'.AT_print($row['question'], 'tests_questions').'</p><p>';
				echo '<input type="radio" name="answers['.$row['question_id'].']" value="1" id="choice_'.$row['question_id'].'_0" /><label for="choice_'.$row['question_id'].'_0">'._AT('true').'</label>';

				echo $spacer;
				echo '<input type="radio" name="answers['.$row['question_id'].']" value="2" id="choice_'.$row['question_id'].'_1" /><label for="choice_'.$row['question_id'].'_1">'._AT('false').'</label>';

				echo $spacer;
				echo '<input type="radio" name="answers['.$row['question_id'].']" value="-1" id="choice_'.$row['question_id'].'_x" checked="checked" /><label for="choice_'.$row['question_id'].'_x"><i>'._AT('leave_blank').'</i></label>';
				break;

			case AT_TESTS_LONG:
				echo '<li>';
				if ($row['weight']) {
					echo '('.$row['weight'].' '._AT('marks').')';
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
				echo '<li>';
				if ($row['weight']) {
					echo '('.$row['weight'].' '._AT('marks').')';
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
				echo '<input type="radio" name="answers['.$row['question_id'].']" value="-1" id="choice_'.$row['question_id'].'_x" checked="checked" /><label for="choice_'.$row['question_id'].'_x"><i>'._AT('leave_blank').'</i></label>';
				break;					
		}
		echo '</p><hr /></li>';
	} while ($row = mysql_fetch_assoc($result));

	echo '</ol>';
	echo '<input type="submit" name="submit" value="'._AT('submit_test').' Alt-s" class="button" accesskey="s" />';
	echo '</form><br />';
	echo '</td></tr></table>';

} else {
	echo '<p>'._AT('no_questions').'</p>';
}

require(AT_INCLUDE_PATH.'footer.inc.php');
?>