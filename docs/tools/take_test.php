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
	$_section[0][0] = _AT('tools');
	$_section[0][1] = 'tools/';
	$_section[1][0] = _AT('take_test');

	/* check to make sure we can access this test: */
	// check goes here.

	if ($_POST['submit']) {
		$tid = intval($_POST['tid']);

		$sql	= "INSERT INTO ".TABLE_PREFIX."tests_results VALUES (0, $tid, $_SESSION[member_id], NOW(), '')";
		$result	= mysql_query($sql, $db);

		$result_id = mysql_insert_id($db);

		if (is_array($_POST['answers'])){
			$sql = '';
			foreach($_POST['answers'] as $q_id	=> $ans) {
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
		$sql	= 'SELECT automark FROM '.TABLE_PREFIX.'tests WHERE test_id='.$tid;
		$result	= mysql_query($sql, $db);
		$row = mysql_fetch_assoc($result);
		if ($row['automark'] == 1) {
			$count = 1;	
			$sql	= "SELECT * FROM ".TABLE_PREFIX."tests_questions WHERE course_id=$_SESSION[course_id] AND test_id=$tid ORDER BY ordering, question_id";
			$result	= mysql_query($sql, $db);	
//			echo '<link rel="stylesheet" href="/ATutor/stylesheet.css" type="text/css" /><link rel="stylesheet" href="/ATutor/css/stylesheet.css" type="text/css" /><link rel="stylesheet" type="text/css" href="/ATutor/print.css" media="print" /><link rel="shortcut icon" href="/ATutor/favicon.ico" type="image/x-icon" />';
//			echo '<table border="0" class="fbkbox" cellpadding="3" cellspacing="2" width="90%" summary="" align="center">';
//			echo '<tr class="fbkbox">';
//			echo '<td>';
//			echo '<h3><img src="/ATutor/images/feedback_x.gif" align="top" alt="Feedback" class="menuimage5" />Feedback</h3><hr /><ul>';
			if ($row = mysql_fetch_assoc($result)){
				do {
					/* get the results for this question */
					$sql		= "SELECT DISTINCT C.question_id as q,C.* FROM ".TABLE_PREFIX."tests_answers C WHERE C.result_id=$rid AND C.question_id=$row[question_id] group by question_id";
					$result_a	= mysql_query($sql, $db);
					$answer_row = mysql_fetch_assoc($result_a);
					$count++;
					switch ($row['type']) {
						case 1:
							/* multiple choice question */
/*							echo '<li>';
							echo "Question ";
							echo $count-1;
							echo ": (";
							echo $row['question'];
							echo ")";
							echo " Score "; */
							if ($row['answer_'.$answer_row['answer']]) {
								if ($answer_row['score'] == '') {
//									echo $row['weight'];
									$scores[$row['question_id']] = $row['weight'];
								}
								else {
//									echo $answer_row['score'];
									$scores[$row['question_id']] = $answer_row['score'];
								}
							}
							else {
//								echo '0';
								$scores[$row['question_id']] = 0;
							}
							// echo "/";
							// echo $row['weight'];
							// echo '</li>';
						break;
						case 2:
							/* true or false quastion */
/*							echo '<li>';
							echo "Question ";
							echo $count-1;
							echo ": (";
							echo $row['question'];
							echo ")";
							echo " Score "; */
							if ($answer_row['answer'] == $row['answer_0']) {
								if ($answer_row['score'] == '') {
//									echo $row['weight'];
									$scores[$row['question_id']] = $row['weight'];
								}
								else {
//									echo $answer_row['score'];
									$scores[$row['question_id']] = $answer_row['score'];
								}
							}
							else {
//								echo "0";
								$scores[$row['question_id']] = 0;
							}
//							echo "/";
//							echo $row['weight'];
//							echo '</li>';						
						break;
						case 3:
							/* open ended question */
							$scores[$row['question_id']] = 0;
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
					// echo $sql;
					$result	= mysql_query($sql, $db);
				}
			}
			$sql	= "UPDATE ".TABLE_PREFIX."tests_results SET final_score=$final_score WHERE result_id=$rid";
			$result	= mysql_query($sql, $db);
		
/*			echo "Final SCORE: ";
			echo $final_score;	
			echo '</ul>';
			echo '</td>';
			echo '</tr>';
			echo '</table>';			
			echo '<a href="my_tests.php">Go back</a>'; */
			header('Location: ../tools/view_results.php?tid='.$tid.'&rid='.$rid.'&tt='.$_SESSION[course_title]);
		}
		else {
			header('Location: ../tools/my_tests.php?f='.urlencode_feedback(AT_FEEDBACK_TEST_SAVED));
		}	
		exit;		
	}

	require(AT_INCLUDE_PATH.'header.inc.php');

	echo '<h2>'.$_GET['tt'].'</h2>';

	$tid	= intval($_GET['tid']);

	/* avman */
	
	/* Retrieve the content_id of this test */
	$sql = "SELECT content_id FROM ".TABLE_PREFIX."tests WHERE test_id=$tid";
	$result	= mysql_query($sql, $db); 
	$row = mysql_fetch_array($result);
	$content_id = $row['content_id'];
	$sql = "SELECT random, num_questions FROM ".TABLE_PREFIX."tests WHERE test_id=$tid";
	$result	= mysql_query($sql, $db); 
	$row = mysql_fetch_array($result);
	$num_questions = $row['num_questions'];	
	if ($row['random']) {
		/* Retrieve 'num_questions' question_id randomly choosed from  
		those who are related to this content_id*/
		$sql	= "SELECT question_id FROM ".TABLE_PREFIX."tests_questions WHERE content_id=$content_id";
		$result	= mysql_query($sql, $db); 
		$i = 0;
		$row2 = mysql_fetch_array($result);
		$num_questions--;
		/* Store all related question in cr_questions */
		while ($row2[question_id] != '') {
			$cr_questions[$i] = $row2[question_id];
			$row2 = mysql_fetch_array($result);
			$i++;
		}
		/* Randomly choose only 'num_question' question */
		$random_idx = rand(0, $i-1);
		$random_id_string = $cr_questions[$random_idx];
		$j = 0;
		$extracted[$j] = $random_idx;
		$j++;
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
		$sql = "SELECT * FROM ".TABLE_PREFIX."tests_questions WHERE question_id IN ($random_id_string) ORDER BY ordering, question_id";
	}
	else {
		$sql = "SELECT * FROM ".TABLE_PREFIX."tests_questions WHERE course_id=$_SESSION[course_id] AND test_id=$tid ORDER BY ordering, question_id";
	}
	
	$result	= mysql_query($sql, $db);
	echo '<table class="bodyline" width="90%"><tr><td>';
	$count = 1;
	if ($row = mysql_fetch_assoc($result)){
		echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'">';
		echo '<input type="hidden" name="tid" value="'.$tid.'" />';
		echo '<ol>';
		do {
			$count++;
			switch ($row['type']) {
				case 1:
					/* multiple choice question */
					echo '<li>('.$row['weight'].' '._AT('marks').')<p>'.AT_print($row['question'], 'tests_questions.question').'</p><p>';

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
					echo '</p></li>';
					break;

				case 2:
					/* true or false quastion */
					echo '<li>('.$row['weight'].' '._AT('marks').')<p>'.AT_print($row['question'], 'tests_questions').'</p><p>';

					echo '<input type="radio" name="answers['.$row['question_id'].']" value="1" id="choice_'.$row['question_id'].'_0" /><label for="choice_'.$row['question_id'].'_0">'._AT('true').'</label>';

					echo ', ';
					echo '<input type="radio" name="answers['.$row['question_id'].']" value="2" id="choice_'.$row['question_id'].'_1" /><label for="choice_'.$row['question_id'].'_1">'._AT('false').'</label>';

					echo '<br />';
					echo '<input type="radio" name="answers['.$row['question_id'].']" value="-1" id="choice_'.$row['question_id'].'_x" checked="checked" /><label for="choice_'.$row['question_id'].'_x"><i>'._AT('leave_blank').'</i></label>';

					echo '</p><br /></li>';
					break;

				case 3:
					/* long answer question */
					echo '<li>('.$row['weight'].' '._AT('marks').')<p>'.AT_print($row['question'], 'tests_questions').'</p><p>';
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

					echo '</p><br /></li>';
					break;
			}
			echo '<hr />';
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