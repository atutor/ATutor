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
require(AT_INCLUDE_PATH.'lib/test_result_functions.inc.php');

$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/index.php';
$_section[1][0] = _AT('my_tests');
$_section[1][1] = 'tools/my_tests.php';
$_section[2][0] = _AT('test_results');

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
		echo ' <a href="tools/my_tests.php?g=11">'._AT('my_tests').'</a>';

}
echo '</h3>';
$tid = intval($_GET['tid']);
$rid = intval($_GET['rid']);

$sql	= "SELECT title FROM ".TABLE_PREFIX."tests WHERE test_id=$tid AND course_id=$_SESSION[course_id]";
$result	= mysql_query($sql, $db);
$row	= mysql_fetch_array($result);

echo '<h3>'._AT('submissions_for', AT_print($row['title'], 'tests.title')).'</h3>';

$mark_right = '<span style="font-family: Wingdings; color: green; font-weight: bold; font-size: 1.6 em; vertical-align: middle;" title="correct answer"></span>';
$mark_wrong = '<span style="font-family: Wingdings; color: red; font-weight: bold; font-size: 1.6 em; vertical-align: middle;" title="incorrect answer"></span>';

$sql	= "SELECT * FROM ".TABLE_PREFIX."tests_results WHERE result_id=$rid AND member_id=$_SESSION[member_id] AND final_score<>''";
$result	= mysql_query($sql, $db); 
if (!$row = mysql_fetch_assoc($result)){
	$errors[]=AT_ERROR_RESULT_NOT_FOUND;
	print_errors($errors);
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

// $sql	= "SELECT * FROM ".TABLE_PREFIX."tests_questions WHERE course_id=$_SESSION[course_id] AND test_id=$tid ORDER BY ordering, question_id";

/* Retrieve randomly choosed questions */
$sql	= "SELECT question_id FROM ".TABLE_PREFIX."tests_answers WHERE result_id=$rid";
$result	= mysql_query($sql, $db); 
$row = mysql_fetch_array($result);
$random_id_string = $row[question_id];
$row = mysql_fetch_array($result);	
while ($row[question_id] != '') {
	$random_id_string = $random_id_string.','.$row[question_id];
	$row = mysql_fetch_array($result);
}
$sql	= "SELECT * FROM ".TABLE_PREFIX."tests_questions WHERE question_id IN ($random_id_string) ORDER BY ordering, question_id";	
$result	= mysql_query($sql, $db); 
		
//	$sql	= "SELECT * FROM ".TABLE_PREFIX."tests_questions WHERE course_id=$_SESSION[course_id] AND test_id=$tid ORDER BY ordering, question_id";	
//	$result	= mysql_query($sql, $db); 

$count = 1;
echo '<form>';

if ($row = mysql_fetch_assoc($result)){
	echo '<table border="0" cellspacing="3" cellpadding="3" class="bodyline" width="90%" align="center">';

	do {
		/* get the results for this question */
		$sql		= "SELECT * FROM ".TABLE_PREFIX."tests_answers WHERE result_id=$rid AND question_id=$row[question_id] AND member_id=$_SESSION[member_id]";
		$result_a	= mysql_query($sql, $db); 
		$answer_row = mysql_fetch_assoc($result_a);

		echo '<tr>';
		echo '<td valign="top">';
		echo '<b>'.$count.'</b><br />';
		
		$count++;

		switch ($row['type']) {
			case 1:
				/* multiple choice question */

				if ($row['weight']) {
					print_score($row['answer_'.$answer_row['answer']], $row['weight'], $row['question_id'], $answer_row['score'], false, true);
				}

				echo '</td>';
				echo '<td>';

				echo AT_print($row['question'], 'tests_questions.question').'<br /><p>';

				/* for each non-empty choice: */
				for ($i=0; ($i < 10) && ($row['choice_'.$i] != ''); $i++) {
					if ($i > 0) {
						echo '<br />';
					}
					print_result($row['choice_'.$i], $row['answer_'.$i], $i, AT_print($answer_row['answer'], 'tests_answers.answer'), $row['answer_'.$answer_row['answer']]);
				}

				echo '<br />';

				print_result('<em>'._AT('left_blank').'</em>', -1, -1, AT_print($answer_row['answer'], 'tests_answers.answer'), false);
				echo '</p>';
				$my_score=($my_score+$answer_row['score']);
				$this_total=($this_total+$row['weight']);
				break;

			case 2:
				/* true or false question */
				if ($row['weight']) {
					print_score($row['answer_'.$answer_row['answer']], $row['weight'], $row['question_id'], $answer_row['score'], false, true);
				}

				echo '</td>';
				echo '<td>';

				echo AT_print($row['question'], 'tests_questions.question').'<br /><p>';

				/* avman */
				print_result(_AT('true'), $row['answer_0'], 1, AT_print($answer_row['answer'], 'tests_answers.answer'),
							$row['answer_'.$answer_row['answer']]);

				print_result(_AT('false'), $row['answer_1'], 2, AT_print($answer_row['answer'], 'tests_answers.answer'),
							$row['answer_'.$answer_row['answer']]);

				echo '<br />';
				print_result('<em>'._AT('left_blank').'</em>', -1, -1, AT_print($answer_row['answer'], 'tests_answers.answer'), false);
				$my_score=($my_score+$answer_row['score']);
				$this_total=($this_total+$row['weight']);
				echo '</p>';
				break;

			case 3:
				/* long answer question */

				if ($row['weight']) {
					print_score($row['answer_'.$answer_row['answer']], $row['weight'], $row['question_id'], $answer_row['score'], false, true);
				}

				echo '</td>';
				echo '<td>';

				echo AT_print($row['question'], 'tests_questions.question').'<br /><p>';
				switch ($row['answer_size']) {
					case 1:
							/* one word */
							echo '<input type="text" value="'.$answer_row['answer'].'" class="formfield" size="15" />';
						break;

					case 2:
							/* sentence */
							echo '<input type="text" name value="'.$answer_row['answer'].'" class="formfield" size="45" />';
						break;
				 
					case 3:
							/* paragraph */
							echo '<textarea cols="55" rows="5" class="formfield">'.$answer_row['answer'].'</textarea>';
						break;

					case 4:
							/* page */
							echo '<textarea cols="55" rows="25" class="formfield">'.$answer_row['answer'].'</textarea>';
						break;
				}
				$my_score=($my_score+$answer_row['score']);
				$this_total=($this_total+$row['weight']);
				echo '</p><br />';
				break;
			case 4:
				/* Likert question */

				//print_score($row['answer_'.$answer_row['answer']], $row['weight'], $row['question_id'], $answer_row['score'], false, true);

				echo '</td>';
				echo '<td>';

				echo AT_print($row['question'], 'tests_questions.question').'<br /><p>';

				/* for each non-empty choice: */
				for ($i=0; ($i < 10) && ($row['choice_'.$i] != ''); $i++) {
					if ($i > 0) {
						echo '<br />';
					}
					print_result($row['choice_'.$i], $row['answer_'.$i], $i, AT_print($answer_row['answer'], 'tests_answers.answer'), $row['answer_'.$answer_row['answer']]);
				}

				echo '<br />';

				print_result('<em>'._AT('left_blank').'</em>', -1, -1, AT_print($answer_row['answer'], 'tests_answers.answer'), false);
				echo '</p>';
				$my_score=($my_score+$answer_row['score']);
				$this_total=($this_total+$row['weight']);
				break;
		}


		if ($row['feedback'] == '') {
			//echo '<em>'._AT('none').'</em>.';
		} else {
			echo '<p><strong>'._AT('feedback').':</strong> ';
			echo nl2br($row['feedback']).'</p>';
		}

		//echo '</p>';
		echo '</td></tr>';
		echo '<tr><td colspan="2"><hr /></td></tr>';
	} while ($row = mysql_fetch_array($result));

	if ($this_total) {
		echo '<tr><td colspan="2"><strong>'.$my_score.'/'.$this_total.'</strong></td></tr>';
	}
	echo '</table>';
} else {
	echo '<p>'._AT('no_questions').'</p>';
}
echo '</form>';

require(AT_INCLUDE_PATH.'footer.inc.php');
?>