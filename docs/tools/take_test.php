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

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'classes/Message/Message.class.php');
$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/';
$_section[1][0] = _AT('my_tests');
$_section[1][1] = 'tools/my_tests.php';
$_section[2][0] = _AT('take_test');

global $savant;
$msg =& new Message($savant);

/* check to make sure we can access this test: */
if (!$_SESSION['enroll']) {
	require(AT_INCLUDE_PATH.'header.inc.php');
	$msg->printFeedbacks('NOT_ENROLLED');

	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

$tid = intval($_REQUEST['tid']);

//make sure max attempts not reached, and still on going
$sql		= "SELECT UNIX_TIMESTAMP(start_date) as start_date, UNIX_TIMESTAMP(end_date) as end_date, num_takes FROM ".TABLE_PREFIX."tests WHERE test_id=".$tid." AND course_id=".$_SESSION['course_id'];
$result= mysql_query($sql, $db);

$row = mysql_fetch_assoc($result);

$sql		= "SELECT COUNT(test_id) AS cnt FROM ".TABLE_PREFIX."tests_results WHERE test_id=".$tid." AND member_id=".$_SESSION['member_id'];
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
	//insert
	$sql	= "INSERT INTO ".TABLE_PREFIX."tests_results VALUES (0, $tid, $_SESSION[member_id], NOW(), '')";
	$result	= mysql_query($sql, $db);
	$result_id = mysql_insert_id($db);

	if (is_array($_POST['answers'])){
		$sql = '';
		foreach($_POST['answers'] as $q_id	=> $ans) {
			$ans = $addslashes($ans);
			if ($sql != '') {
				$sql .= ', ';	
			}

			$sql .= "($result_id, $q_id, $_SESSION[member_id], '$ans', '', '')";
		}
		$sql	= 'INSERT INTO '.TABLE_PREFIX.'tests_answers VALUES '.$sql;
		$result	= mysql_query($sql, $db);
	}
	
	/* avman */	
	$rid = $result_id;
	if ($_POST['automark'] == AT_MARK_SELF) {
		$count	= 1;	
		$sql	= "SELECT * FROM ".TABLE_PREFIX."tests_questions WHERE course_id=$_SESSION[course_id] AND test_id=$tid ORDER BY ordering, question_id";
		$result	= mysql_query($sql, $db);	
		if ($row = mysql_fetch_assoc($result)){
			do {
				/* get the results for this question */
				$sql		= "SELECT DISTINCT C.question_id as q,C.* FROM ".TABLE_PREFIX."tests_answers C WHERE C.result_id=$rid AND C.question_id=$row[question_id] group by question_id";
				$result_a	= mysql_query($sql, $db);
				$answer_row = mysql_fetch_assoc($result_a);
				$count++;
				switch ($row['type']) {
					case AT_TESTS_MC:
						if ($row['answer_'.$answer_row['answer']]) {
							if ($answer_row['score'] == '') {
								$scores[$row['question_id']] = $row['weight'];
							} else {
								$scores[$row['question_id']] = $answer_row['score'];
							}
						} else {
							$scores[$row['question_id']] = 0;
						}
					break;
					case AT_TESTS_TF:
						if ($answer_row['answer'] == $row['answer_0']) {
							if ($answer_row['score'] == '') {
								$scores[$row['question_id']] = $row['weight'];
							} else {
								$scores[$row['question_id']] = $answer_row['score'];
							}
						} else {
							$scores[$row['question_id']] = 0;
						}
					break;
					case AT_TESTS_LONG:							
						$scores[$row['question_id']] = 0;
					break;
					case AT_TESTS_LIKERT:							
						if ($row['answer_'.$answer_row['answer']]) {
							if ($answer_row['score'] == '') {
								$scores[$row['question_id']] = $row['weight'];
							} else {
								$scores[$row['question_id']] = $answer_row['score'];
							}
						} else {
							$scores[$row['question_id']] = 0;
						}
					break;
				}
			} while ($row = mysql_fetch_assoc($result));
		}
	
		$final_score = 0;
		
		if (is_array($scores)) {
			foreach ($scores as $qid => $score) {
				$score		  = intval($score);
				$final_score += $score;
				$sql	= "UPDATE ".TABLE_PREFIX."tests_answers SET score=$score WHERE result_id=$rid AND question_id=$qid";
				$result	= mysql_query($sql, $db);
			}
		}
		$sql	= "UPDATE ".TABLE_PREFIX."tests_results SET final_score=$final_score WHERE result_id=$rid";
		$result	= mysql_query($sql, $db);
	
		header('Location: ../tools/view_results.php?tid='.$tid.'&rid='.$rid.'&tt='.$_SESSION['course_title']);

	} else if ($_POST['automark'] == AT_MARK_UNMARKED) {
		$sql	= "UPDATE ".TABLE_PREFIX."tests_results SET final_score=0 WHERE result_id=$rid";
		$result	= mysql_query($sql, $db);

		$sql	= "UPDATE ".TABLE_PREFIX."tests_answers SET score=0 WHERE result_id=$rid AND question_id=$qid";
		$result	= mysql_query($sql, $db);
		$msg->addFeedback('TEST_SAVED');
		header('Location: ../tools/my_tests.php');
	} else {
		$msg->addFeedback('TEST_SAVED');
		header('Location: ../tools/my_tests.php');
	}	
	exit;		
}

require(AT_INCLUDE_PATH.'header.inc.php'); 

echo '<h2>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '<a href="tools/index.php?g=11"><img src="images/icons/default/square-large-tools.gif"  class="menuimageh2" width="42" border="0" vspace="2" height="40" alt="" /></a>';
}
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
	echo ' <a href="tools/index.php?g=11">'._AT('tools').'</a>';
}
echo '</h2>';

echo '<h3>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '&nbsp;<img src="images/icons/default/my-tests-large.gif" vspace="2"  class="menuimageh3" width="42" height="38" alt="" /> ';
}
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
	echo '<a href="tools/my_tests.php?g=11">'._AT('my_tests').'</a>';
}
echo '</h3>';

$tid = intval($_GET['tid']);
/* Retrieve the content_id of this test */
$sql = "SELECT * FROM ".TABLE_PREFIX."tests WHERE test_id=$tid";
$result	= mysql_query($sql, $db); 
$row = mysql_fetch_assoc($result);
$automark = $row['automark'];
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
	$sql    = "SELECT question_id FROM ".TABLE_PREFIX."tests_questions WHERE test_id=$tid";
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
	$sql = "SELECT * FROM ".TABLE_PREFIX."tests_questions WHERE question_id IN ($random_id_string) ORDER BY ordering, question_id";
} else {
	$sql = "SELECT * FROM ".TABLE_PREFIX."tests_questions WHERE course_id=$_SESSION[course_id] AND test_id=$tid ORDER BY ordering, question_id";
}

$result	= mysql_query($sql, $db);
$count = 1;
if ($row = @mysql_fetch_assoc($result)){
	echo '<table class="bodyline" width="90%"><tr><td>';
	echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'">';
	echo '<input type="hidden" name="tid" value="'.$tid.'" />';
	echo '<input type="hidden" name="automark" value="'.$automark.'" />';
	echo '<ol>';
	do {
		$count++;
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
							echo '<br />';
						}

						echo '<input type="radio" name="answers['.$row['question_id'].']" value="'.$i.'" id="choice_'.$row['question_id'].'_'.$i.'" /><label for="choice_'.$row['question_id'].'_'.$i.'">'.AT_print($row['choice_'.$i], 'tests_answers.answer').'</label>';
					}
				}

				echo '<br />';
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

				echo ', ';
				echo '<input type="radio" name="answers['.$row['question_id'].']" value="2" id="choice_'.$row['question_id'].'_1" /><label for="choice_'.$row['question_id'].'_1">'._AT('false').'</label>';

				echo '<br />';
				echo '<input type="radio" name="answers['.$row['question_id'].']" value="-1" id="choice_'.$row['question_id'].'_x" checked="checked" /><label for="choice_'.$row['question_id'].'_x"><i>'._AT('leave_blank').'</i></label>';
				break;

			case AT_TESTS_LONG:
				echo '<li>';
				if ($row['weight']) {
					echo '('.$row['weight'].' '._AT('marks').')';
				}
				echo '<p>'.AT_print($row['question'], 'tests_questions').'</p><p>';
				switch ($row['answer_size']) {
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
							echo '<br />';
						}

						echo '<input type="radio" name="answers['.$row['question_id'].']" value="'.$i.'" id="choice_'.$row['question_id'].'_'.$i.'" /><label for="choice_'.$row['question_id'].'_'.$i.'">'.AT_print($row['choice_'.$i], 'tests_answers.answer').'</label>';
					}
				}

				echo '<br />';
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