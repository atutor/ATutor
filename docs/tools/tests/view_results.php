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

	define('AT_INCLUDE_PATH', '../../include/');
	require(AT_INCLUDE_PATH.'vitals.inc.php');
	require(AT_INCLUDE_PATH.'lib/test_result_functions.inc.php');
	require(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

	global $savant;
	$msg =& new Message($savant);

	$_section[0][0] = _AT('tools');
	$_section[0][1] = 'tools/';
	$_section[1][0] = _AT('test_manager');
	$_section[1][1] = 'tools/tests/';
	$_section[2][0] = _AT('results');
	$_section[2][1] = 'tools/tests/results.php?tid='.$_GET['tid'];
	$_section[3][0] = _AT('test_results');

	authenticate(AT_PRIV_TEST_MARK);
	$tid = intval($_GET['tid']);
	if ($tid == 0){
		$tid = intval($_POST['tid']);
	}

	if ($_POST['submit']) {
		$tid = intval($_POST['tid']);
		$rid = intval($_POST['rid']);
		
		$final_score = 0;
		if (is_array($_POST['scores'])) {
			foreach ($_POST['scores'] as $qid => $score) {
				$score		  = intval($score);
				$final_score += $score;

				$sql	= "UPDATE ".TABLE_PREFIX."tests_answers SET score=$score WHERE result_id=$rid AND question_id=$qid";
				$result	= mysql_query($sql, $db);
			}
		}

		$sql	= "UPDATE ".TABLE_PREFIX."tests_results SET final_score=$final_score WHERE result_id=$rid";
		$result	= mysql_query($sql, $db);

		$msg->addFeedback('RESULTS_UPDATED');
		header('Location: results.php?tid='.$tid);
		exit;
	}

	require(AT_INCLUDE_PATH.'header.inc.php');

echo '<h2>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '<a href="tools/" class="hide"><img src="images/icons/default/square-large-tools.gif"  class="menuimageh2" border="0" vspace="2" width="42" height="40" alt="" /></a>';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo ' <a href="tools/" class="hide">'._AT('tools').'</a>';
	}
echo '</h2>';

echo '<h3>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '&nbsp;<img src="images/icons/default/test-manager-large.gif"  class="menuimageh3" width="42" height="38" alt="" /> ';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo '<a href="tools/tests/">'._AT('test_manager').'</a>';
	}
echo '</h3>';
	
	$sql	= "SELECT * FROM ".TABLE_PREFIX."tests WHERE test_id=$tid AND course_id=$_SESSION[course_id]";
	$result	= mysql_query($sql, $db);

	if (!($row = mysql_fetch_array($result))){
		$msg->printErrors('TEST_NOT_FOUND');
		require (AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}
	$test_title = $row['title'];
	$automark   = $row['automark'];

	echo '<h3>'._AT('submissions_for', AT_print($test_title, 'tests.title')).'</h3>';

	$tid = intval($_GET['tid']);
	$rid = intval($_GET['rid']);

	$mark_right = '<span style="font-family: Wingdings; color: green; font-weight: bold; font-size: 1.6 em; vertical-align: middle;" title="correct answer"></span>';
	$mark_wrong = '<span style="font-family: Wingdings; color: red; font-weight: bold; font-size: 1.6 em; vertical-align: middle;" title="incorrect answer"></span>';

	/* avman */
	$sql	= "SELECT * FROM ".TABLE_PREFIX."tests_questions WHERE course_id=$_SESSION[course_id] AND test_id=$tid ORDER BY ordering, question_id";
	$result	= mysql_query($sql, $db);

	$count = 1;
	echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'">';
	echo '<input type="hidden" name="tid" value="'.$tid.'">';
	echo '<input type="hidden" name="rid" value="'.$rid.'">';

	if ($row = mysql_fetch_assoc($result)){
		echo '<table border="0" cellspacing="3" cellpadding="3" class="bodyline" width="90%">';

		do {
			/* get the results for this question */
			$sql		= "SELECT DISTINCT C.question_id as q,C.* FROM ".TABLE_PREFIX."tests_answers C WHERE C.result_id=$rid AND C.question_id=$row[question_id] group by question_id";
			$result_a	= mysql_query($sql, $db);
			$answer_row = mysql_fetch_assoc($result_a);
			
			if ($answer_row != '') {
				echo '<tr>';
				echo '<td valign="top">';
				echo '<b>'.$count.'</b><br />';

				$count++;			
				switch ($row['type']) {
					case AT_TESTS_MC:
						/* multiple choice question */
						if ($row['weight']) {
							print_score($row['answer_'.$answer_row['answer']], $row['weight'], $row['question_id'], $answer_row['score']);
						}
						echo '</td>';
						echo '<td>';
	
						echo AT_print($row['question'], 'tests_questions.question').'<br /><p>';
	
						/* for each non-empty choice: */
						for ($i=0; ($i < 10) && ($row['choice_'.$i] != ''); $i++) {
							if ($i > 0) {
								echo '<br />';
							}
							print_result($row['choice_'.$i], $row['answer_'.$i], $i, $answer_row['answer'], $row['answer_'.$answer_row['answer']]);
						}
	
						echo '<br />';
	
						print_result('<em>'._AT('left_blank').'</em>', -1, -1, $answer_row['answer'], false);
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
						}
						echo '</td>';
						echo '<td>';
	
						echo AT_print($row['question'], 'tests_questions.question').'<br /><p>';
	
						print_result(_AT('true'), $row['answer_0'], 1, $answer_row['answer'],
									$correct);
	
						print_result(_AT('false'), $row['answer_0'], 2, $answer_row['answer'],
									$correct);
	
						echo '<br />';
						print_result('<em>'._AT('left_blank').'</em>', -1, -1, $answer_row['answer'], false);
	
						echo '</p>';
						break;
	
					case AT_TESTS_LONG:
						if ($row['weight']) {
							print_score($row['answer_'.$answer_row['answer']], $row['weight'], $row['question_id'], $answer_row['score'], false);
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
	
						echo '</p><br />';
						break;
					case AT_TESTS_LIKERT:
	
						echo '</td>';
						echo '<td>';
	
						echo AT_print($row['question'], 'tests_questions.question').'<br /><p>';
	
						/* for each non-empty choice: */
						for ($i=0; ($i < 10) && ($row['choice_'.$i] != ''); $i++) {
							if ($i > 0) {
								echo '<br />';
							}
							print_result($row['choice_'.$i], $row['answer_'.$i], $i, $answer_row['answer'], $row['answer_'.$answer_row['answer']]);
						}
	
						echo '<br />';
	
						print_result('<em>'._AT('left_blank').'</em>', -1, -1, $answer_row['answer'], false);
						echo '</p>';
						break;
				}
			echo '</td></tr>';
			echo '<tr><td colspan="2"><hr /></td></tr>';
			}			
		} while ($row = mysql_fetch_assoc($result));

		if ($automark != AT_MARK_UNMARKED) {
			echo '<tr>';
			echo '<td align="center" colspan="2">';

			echo '<input type="submit" class="button" value="'._AT('submit_test_results').' Alt-s" name="submit" accesskey="s" />';
			echo '</td>';

			echo '</tr>';
		}
		echo '</table>';
	} else {
		echo '<p>'._AT('no_questions').'</p>';
	}
	echo '</form>';

	require(AT_INCLUDE_PATH.'footer.inc.php');
?>