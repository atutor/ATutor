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
// $Id: preview.php 2661 2004-12-03 19:53:53Z shozubq $

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
echo '<br />';

$qid = intval($_GET['qid']);

$sql = "SELECT * FROM ".TABLE_PREFIX."tests_questions WHERE course_id=$_SESSION[course_id] AND question_id=$qid";

$result	= mysql_query($sql, $db);
$row = mysql_fetch_assoc($result);

echo '<h3>'._AT('preview_of').' '.$row['question'].'</h3>';

echo '<br />';

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
	echo '</td></tr></table>';
}

require(AT_INCLUDE_PATH.'footer.inc.php');
?>