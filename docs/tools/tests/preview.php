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
require(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

$msg =& new Message($savant);
	
$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/';
$_section[1][0] = _AT('test_manager');
$_section[1][1] = 'tools/tests/';
$_section[2][0] = _AT('preview');

$course_base_href = 'get.php/';

require(AT_INCLUDE_PATH.'header.inc.php');

echo '<h2>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '<a href="tools/" class="hide"><img src="'.$_base_path.'images/icons/default/square-large-tools.gif"  class="menuimageh2" border="0" vspace="2" width="42" height="40" alt="" /></a>';
}
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
	echo ' <a href="tools/" class="hide">'._AT('tools').'</a>';
}
echo '</h2>';

echo '<h3>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '&nbsp;<img src="'.$_base_path.'images/icons/default/test-manager-large.gif"  class="menuimageh3" width="42" height="38" alt="" /> ';
}
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
	echo '<a href="tools/tests/">'._AT('test_manager').'</a>';
}
echo '</h3>';

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

echo '<h3>'._AT('preview_of').' '.$test_row['title'].'</h3>';

if ($row['instructions']!='') {
	echo '<p><br /><strong>'._AT('special_instructions').'</strong>:  ';  
	echo $row['instructions'];
	echo '</p>';
}

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
if (($row = mysql_fetch_assoc($result)) && !$rand_err) {
	echo '<table border="0" cellspacing="3" cellpadding="3" class="bodyline" width="90%"><tr><td>';
	do {
		echo '<b>'.$count.')</b> ';
		$count++;
	if ($row['properties'] == AT_TESTS_OPT_ALIGN_VERT) {
		switch ($row['type']) {
			case 1:
				/* multiple choice question */
				echo AT_print($row['question'], 'tests_questions.question').'<br /><p>';

				for ($i=0; $i < 10; $i++) {
					if ($row['choice_'.$i] != '') {
						if ($i > 0) {
							echo '<br />';
						}
						 
						echo '<input type="radio" name="question_'.$row['question_id'].'" value="'.$i.'" id="choice_'.$row['question_id'].'_'.$i.'" /><label for="choice_'.$row['question_id'].'_'.$i.'">'.AT_print($row['choice_'.$i], 'tests_answers.answer').'</label>';
					}
				}

				echo '<br />';
				echo '<input type="radio" name="question_'.$row['question_id'].'" value="-1" id="choice_'.$row['question_id'].'_x" checked="checked" /><label for="choice_'.$row['question_id'].'_x"><i>'._AT('leave_blank').'</i></label>';
				echo '</p>';
				break;
				
			case 2:
				/* true or false quastion */
				echo AT_print($row['question'], 'tests_questions.question').'<br />';

				echo '<input type="radio" name="question_'.$row['question_id'].'" value="1" id="choice_'.$row['question_id'].'_1" /><label for="choice_'.$row['question_id'].'_1">'._AT('true').'</label>';

				echo '<br />';
				echo '<input type="radio" name="question_'.$row['question_id'].'" value="0" id="choice_'.$row['question_id'].'_0" /><label for="choice_'.$row['question_id'].'_0">'._AT('false').'</label>';

				echo '<br />';
				echo '<input type="radio" name="question_'.$row['question_id'].'" value="-1" id="choice_'.$row['question_id'].'_x" checked="checked" /><label for="choice_'.$row['question_id'].'_x"><i>'._AT('leave_blank').'</i></label>';

				echo '<br />';
				break;

			case 3:
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

			case 4:
				/* Likert question */
				echo AT_print($row['question'], 'tests_questions.question').'<br /><p>';
 
				for ($i=0; $i < 10; $i++) {
					if ($row['choice_'.$i] != '') {
						if ($i > 0) {
							echo '<br />';
						}
						 
						echo '<input type="radio" name="question_'.$row['question_id'].'" value="'.$i.'" id="choice_'.$row['question_id'].'_'.$i.'" /><label for="choice_'.$row['question_id'].'_'.$i.'">'.AT_print($row['choice_'.$i], 'tests_answers.answer').'</label>';
					}
				}

				echo '<br />';
				echo '<input type="radio" name="question_'.$row['question_id'].'" value="-1" id="choice_'.$row['question_id'].'_x" checked="checked" /><label for="choice_'.$row['question_id'].'_x"><i>'._AT('leave_blank').'</i></label>';
				echo '</p>';
				break;
		}
		echo '<hr />';
	}
	else {
		switch ($row['type']) {
			case 1:
				/* multiple choice question */
				echo AT_print($row['question'], 'tests_questions.question').'<br /><p>';

				for ($i=0; $i < 10; $i++) {
					if ($row['choice_'.$i] != '') {
						if ($i > 0) {
							echo ', ';
						}
						 
						echo '<input type="radio" name="question_'.$row['question_id'].'" value="'.$i.'" id="choice_'.$row['question_id'].'_'.$i.'" /><label for="choice_'.$row['question_id'].'_'.$i.'">'.AT_print($row['choice_'.$i], 'tests_answers.answer').'</label>';
					}
				}

				echo ', ';
				echo '<input type="radio" name="question_'.$row['question_id'].'" value="-1" id="choice_'.$row['question_id'].'_x" checked="checked" /><label for="choice_'.$row['question_id'].'_x"><i>'._AT('leave_blank').'</i></label>';
				echo '</p>';
				break;
				
			case 2:
				/* true or false quastion */
				echo AT_print($row['question'], 'tests_questions.question').'<br />';

				echo '<input type="radio" name="question_'.$row['question_id'].'" value="1" id="choice_'.$row['question_id'].'_1" /><label for="choice_'.$row['question_id'].'_1">'._AT('true').'</label>';

				echo ', ';
				echo '<input type="radio" name="question_'.$row['question_id'].'" value="0" id="choice_'.$row['question_id'].'_0" /><label for="choice_'.$row['question_id'].'_0">'._AT('false').'</label>';

				echo ', ';
				echo '<input type="radio" name="question_'.$row['question_id'].'" value="-1" id="choice_'.$row['question_id'].'_x" checked="checked" /><label for="choice_'.$row['question_id'].'_x"><i>'._AT('leave_blank').'</i></label>';

				echo '<br />';
				break;

			case 4:
				/* Likert question */
				echo AT_print($row['question'], 'tests_questions.question').'<br /><p>';
 
				for ($i=0; $i < 10; $i++) {
					if ($row['choice_'.$i] != '') {
						if ($i > 0) {
							echo ', ';
						}
						 
						echo '<input type="radio" name="question_'.$row['question_id'].'" value="'.$i.'" id="choice_'.$row['question_id'].'_'.$i.'" /><label for="choice_'.$row['question_id'].'_'.$i.'">'.AT_print($row['choice_'.$i], 'tests_answers.answer').'</label>';
					}
				}

				echo ', ';
				echo '<input type="radio" name="question_'.$row['question_id'].'" value="-1" id="choice_'.$row['question_id'].'_x" checked="checked" /><label for="choice_'.$row['question_id'].'_x"><i>'._AT('leave_blank').'</i></label>';
				echo '</p>';
				break;
		}
		echo '<hr />';
	}
	} while ($row = mysql_fetch_assoc($result));
	echo '</td></tr></table>';
} else {
	$msg->printErrors('NO_QUESTIONS');
}


require(AT_INCLUDE_PATH.'footer.inc.php');
?>