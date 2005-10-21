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

$page = 'tests';
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_TESTS);

if ($_POST['back']) {
	header('Location: index.php');
	exit;
} 

if (defined('AT_FORCE_GET_FILE') && AT_FORCE_GET_FILE) {
	$content_base_href = 'get.php/';
} else {
	$content_base_href = 'content/' . $_SESSION['course_id'] . '/';
}

require(AT_INCLUDE_PATH.'header.inc.php');

$tid = intval($_GET['tid']);

/* Retrieve the content_id of this test */
$sql = "SELECT title, random, num_questions, instructions FROM ".TABLE_PREFIX."tests WHERE test_id=$tid";
$result	= mysql_query($sql, $db); 
if (!($test_row = mysql_fetch_assoc($result))) {
	$msg->printErrors('TEST_NOT_FOUND');
	require (AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}
$num_questions = $test_row['num_questions'];
$rand_err = false;

if ($row['random']) {
	/* !NOTE! this is a really awful way of randomizing questions !NOTE! */

	/* Retrieve 'num_questions' question_id randomly choosed from  
	those who are related to this content_id*/
	$sql	= "SELECT question_id FROM ".TABLE_PREFIX."tests_questions_assoc WHERE test_id=$tid";
	$result	= mysql_query($sql, $db); 
	$i = 0;
	$row2 = mysql_fetch_assoc($result);
	/* Store all related question in cr_questions */
	while ($row2['question_id'] != '') {
		$cr_questions[$i] = $row2['question_id'];
		$row2 = mysql_fetch_assoc($result);
		$i++;
	}
	if ($i < $num_questions) {
		/* this if-statement is misleading. */
		/* one should still be able to preview a test before all its questions have been added. */
		/* ie. preview as questions are added. */
		/* bug # 0000615 */
		$rand_err = true;
	} else {
		/* Randomly choose only 'num_question' question */
		$random_idx = rand(0, $i-1);
		$random_id_string = $cr_questions[$random_idx];
		$j = 0;
		$extracted[$j] = $random_idx;
		$j++;
		$num_questions--;
		while ($num_questions > 0) {
			$done = false;
			while (!$done) {
				$random_idx = rand(0, $i-1);
				$done = true;
				for ($k=0;$k<$j;$k++) {
					if ($extracted[$k]== $random_idx) {
						$done = false;
						break;
					}
				}
			}
			$extracted[$j] = $random_idx;
			$j++;
			$random_id_string = $random_id_string.','.$cr_questions[$random_idx];
			$num_questions--;
		}
		//$sql = "SELECT * FROM ".TABLE_PREFIX."tests_questions WHERE question_id IN ($random_id_string)";

		$sql = "SELECT TQ.*, TQA.* FROM ".TABLE_PREFIX."tests_questions TQ INNER JOIN ".TABLE_PREFIX."tests_questions_assoc TQA USING (question_id) WHERE TQ.course_id=$_SESSION[course_id] AND TQA.test_id=$tid AND TQA.question_id IN ($random_id_string) ORDER BY TQA.ordering, TQA.question_id";
	}
} else {
	$sql	= "SELECT TQ.*, TQA.* FROM ".TABLE_PREFIX."tests_questions TQ INNER JOIN ".TABLE_PREFIX."tests_questions_assoc TQA USING (question_id) WHERE TQ.course_id=$_SESSION[course_id] AND TQA.test_id=$tid ORDER BY TQA.ordering, TQA.question_id";
}
$result	= mysql_query($sql, $db);
$count = 1;
echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'">';

if (($row = mysql_fetch_assoc($result)) && !$rand_err) {
	echo '<div class="input-form">';
	echo '<div class="row">';
	echo '<h2>'.$test_row['title'].'</h2>';

	if ($test_row['instructions']!='') {
		echo '<p><br /><strong>'._AT('instructions').'</strong>:  '. $test_row['instructions'] .'</p>';
	}
	echo '</div>';

	do {
		echo '<div class="row"><h3>'.$count.')</h3> ';
		$count++;
		
		if ($row['properties'] == AT_TESTS_QPROP_ALIGN_VERT) {
			$spacer = '<br />';
		} else {
			$spacer = ', ';
		}

		switch ($row['type']) {
			case AT_TESTS_MC:
				/* multiple choice question */
				echo AT_print($row['question'], 'tests_questions.question').'<br /><p>';

				if (array_sum(array_slice($row, 16, -6)) > 1) {
					for ($i=0; $i < 10; $i++) {
						if ($row['choice_'.$i] != '') {
							if ($i > 0) {
								echo $spacer;
							}
							 
							echo '<input type="checkbox" name="question_'.$row['question_id'].'" value="'.$i.'" id="choice_'.$row['question_id'].'_'.$i.'" /><label for="choice_'.$row['question_id'].'_'.$i.'">'.AT_print($row['choice_'.$i], 'tests_questions.choice_'.$i).'</label>';
						}
					}
				} else {
					for ($i=0; $i < 10; $i++) {
						if ($row['choice_'.$i] != '') {
							if ($i > 0) {
								echo $spacer;
							}
							 
							echo '<input type="radio" name="question_'.$row['question_id'].'" value="'.$i.'" id="choice_'.$row['question_id'].'_'.$i.'" /><label for="choice_'.$row['question_id'].'_'.$i.'">'.AT_print($row['choice_'.$i], 'tests_questions.choice_'.$i).'</label>';
						}
					}

					echo $spacer;
					echo '<input type="radio" name="question_'.$row['question_id'].'" value="-1" id="choice_'.$row['question_id'].'_x" checked="checked" /><label for="choice_'.$row['question_id'].'_x"><em>'._AT('leave_blank').'</em></label>';
				}
				echo '</p>';
				break;
				
			case AT_TESTS_TF:
				/* true or false question */
				echo AT_print($row['question'], 'tests_questions.question').'<br />';

				echo '<input type="radio" name="question_'.$row['question_id'].'" value="1" id="choice_'.$row['question_id'].'_1" /><label for="choice_'.$row['question_id'].'_1">'._AT('true').'</label>';

				echo $spacer;
				echo '<input type="radio" name="question_'.$row['question_id'].'" value="0" id="choice_'.$row['question_id'].'_0" /><label for="choice_'.$row['question_id'].'_0">'._AT('false').'</label>';

				echo $spacer;
				echo '<input type="radio" name="question_'.$row['question_id'].'" value="-1" id="choice_'.$row['question_id'].'_x" checked="checked" /><label for="choice_'.$row['question_id'].'_x"><em>'._AT('leave_blank').'</em></label>';

				echo $spacer;
				break;

			case AT_TESTS_LONG:
				/* long answer question */
				echo AT_print($row['question'], 'tests_questions.question').'<br /><p>';
				switch ($row['properties']) {
					case 1:
							/* one word */
							echo '<input type="text" name="question_'.$row['question_id'].'" class="formfield" size="15" />';
						break;

					case 2:
							/* sentence */
							echo '<input type="text" name="question_'.$row['question_id'].'" class="formfield" size="45" />';
						break;
					
					case 3:
							/* paragraph */
							echo '<textarea cols="55" rows="5" name="question_'.$row['question_id'].'" class="formfield"></textarea>';
						break;

					case 4:
							/* page */
							echo '<textarea cols="55" rows="25" name="question_'.$row['question_id'].'" class="formfield"></textarea>';
						break;
				}

				echo '</p><br />';
				break;

			case AT_TESTS_LIKERT:
				/* Likert question */
				echo AT_print($row['question'], 'tests_questions.question').'<br /><p>';
 
				for ($i=0; $i < 10; $i++) {
					if ($row['choice_'.$i] != '') {
						if ($i > 0) {
							echo $spacer;
						}
						 
						echo '<input type="radio" name="question_'.$row['question_id'].'" value="'.$i.'" id="choice_'.$row['question_id'].'_'.$i.'" /><label for="choice_'.$row['question_id'].'_'.$i.'">'.AT_print($row['choice_'.$i], 'tests_answers.answer').'</label>';
					}
				}

				echo $spacer;
				echo '<input type="radio" name="question_'.$row['question_id'].'" value="-1" id="choice_'.$row['question_id'].'_x" checked="checked" /><label for="choice_'.$row['question_id'].'_x"><em>'._AT('leave_blank').'</em></label>';
				echo '</p>';
				break;
		}
		echo '</div>';
	} while ($row = mysql_fetch_assoc($result));

	echo '<div class="row buttons">';
		echo '<input type="submit" value="'._AT('back').'" name="back" />';
	echo '</div>';

	echo '</div>';
	echo '</form>';
} else {
	$msg->printErrors('NO_QUESTIONS');
}


require(AT_INCLUDE_PATH.'footer.inc.php');
?>