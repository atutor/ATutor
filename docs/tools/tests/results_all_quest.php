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
$page = 'tests';
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_TESTS);


$tid = intval($_REQUEST['tid']);

$_pages['tools/tests/results_all_quest.php']['title_var']  = 'question_statistics';
$_pages['tools/tests/results_all_quest.php']['parent']  = 'tools/tests/index.php';
$_pages['tools/tests/results_all_quest.php']['children'] = array('tools/tests/results_all.php?tid='.$tid);

$_pages['tools/tests/results_all.php?tid='.$tid]['title_var']  = 'mark_statistics';
$_pages['tools/tests/results_all.php?tid='.$tid]['parent'] = 'tools/tests/results_all_quest.php';

function print_likert($q, $answers, $num_scale, $num_results) {
?>
	<br />
	<table class="data static" summary="" style="width: 95%" rules="cols">
	<thead>
	<tr>
		<th scope="col" width="40%"><?php echo _AT('question');	?></th>
		<th scope="col"><?php echo _AT('left_blank'); ?></th>
		<th scope="col"><?php echo _AT('average').' '._AT('answer'); ?></th>
		<?php for ($i=0; $i<=$num_scale; $i++) {
			echo '<th scope="col" title="'.$q['choice_'.$i].'">'.($i+1).'</th>';
		}?>
	</tr>
	</thead>
<?php
	echo '<tr>';
	echo '<td valign="top">'.$q['question'].'</td>';

	$num_blanks = intval($answers['-1']['count']);
	echo '<td align="center" width="70" valign="top">'.$num_blanks.'</td>';

	//avg choice 
	$sum = 0;
	for ($j=0; $j<=$num_scale; $j++) {
		$sum += ($j+1) * $answers[$j]['count'];
	}

	$num_results -= $num_blanks;

	//check if only blanks given
	echo '<td align="center" width="70" valign="top">';
	if ($num_results) {
		echo round($sum/$num_results, 1).'/'.($num_scale+1);
	} else  {
		echo '-';
	}
	echo '</td>';

	for ($j=0; $j<=$num_scale; $j++) {
		$percentage = $num_results ? round($answers[$j]['count']/$num_results*100) : 0;
		echo '<td align="center" valign="top">'.intval($answers[$j]['count']).'/'.$num_results.'<br />'.$percentage.'%</td>';		
	}
	echo '</tr>';
	echo '</table>';	

	return true;
}

function print_true_false($q, $answers, $num_results) {
	echo '<br />';
	echo '<table class="data static" summary="" style="width: 95%" rules="cols">';
	echo '<thead>';
	echo '<tr>';
	echo '<th scope="col" width="40%">'._AT('question').'</th>';	
	echo '<th scope="col" nowrap="nowrap">'._AT('left_blank').'</th>';	

	if ($q['answer_0'] == 1) {		
		echo '<th scope="col">'._AT('true').'<img src="images/checkmark.gif" alt="Correct checkmark" /></th>';
		echo '<th scope="col">'._AT('false').'</th>';
	} elseif ($q['answer_0'] == 2) {
		echo '<th scope="col">'._AT('true').'</th>';
		echo '<th scope="col">'._AT('false').'<img src="images/checkmark.gif" alt="Correct checkmark" /></th>';
	} else {
		echo '<th scope="col">'._AT('true').'</th>';
		echo '<th scope="col">'._AT('false').'</th>';
	}
	echo '</tr>';
	echo '</thead>';

	echo '<tr>';
	echo '<td valign="top">'.$q['question'].'</td>';

	$num_blanks = intval($answers['-1']['count']);
	//blank
	echo '<td align="center" width="70" valign="top">'.$num_blanks.'</td>';

	$num_results -= $num_blanks;

	$percentage1 = $num_results ? round($answers[1]['count']/$num_results*100) : 0;
	$percentage2 = $num_results ? round($answers[2]['count']/$num_results*100) : 0;

	echo '<td align="center" valign="top">'.intval($answers[1]['count']) .'/'.$num_results.'<br />'. $percentage1.'%</td>';
	echo '<td align="center" valign="top">'.intval($answers[2]['count']) .'/'.$num_results.'<br />'.$percentage2.'%</td>';	

	echo '</tr>';
	echo '</table>';	

	return true;
}
function print_multiple_choice($q, $answers, $num, $num_results) {

	echo '<br />';
	echo '<table class="data static" summary="" style="width: 95%" rules="cols">';
	echo '<thead>';
	echo '<tr>';
	echo '<th scope="col" width="40%">'._AT('question').'</th>';
	echo '<th scope="col" nowrap="nowrap">'._AT('left_blank').'</th>';

	for ($i=1; $i<=$num+1; $i++) {
		if(strlen($q['choice_'.($i-1)]) > 15) {
			$q['choice_'.($i-1)] = substr($q['choice_'.($i-1)], 0, 15).'...';
		}
		if ($q['answer_'.($i-1)]) {		
			echo '<th scope="col">'.htmlspecialchars($q['choice_'.($i-1)]).'<img src="images/checkmark.gif" alt="" /></th>';
		} else {
			echo '<th scope="col">'.htmlspecialchars($q['choice_'.($i-1)]).'</th>';
		}
	}
	echo '</tr>';
	echo '</thead>';

	echo '<tr>';
	echo '<td valign="top">'.$q['question'].'</td>';

	$sum = 0;
	for ($j=0; $j<=$num; $j++) {
		$sum += $answers[$j]['score'];
	}

	$num_blanks = intval($answers['-1']['count']);
	//blank
	echo '<td align="center" width="70" valign="top">'.$num_blanks.'</td>';

	$num_results -= $num_blanks;
	foreach ($answers as $key => $value) {
		$values = explode('|', $key);
		if (count($values) > 1) {
			for ($i=0; $i<count($values); $i++) {
				$answers[$values[$i]]['count']++;
			}
		}
	}

	for ($j=0; $j<=$num; $j++) {
		$percentage = $num_results ? round($answers[$j]['count']/$num_results*100) : 0;
		echo '<td align="center" valign="top">'.intval($answers[$j]['count']).'/'.$num_results.'<br />'.$percentage.'%</td>';		
	}
	echo '</tr>';
	echo '</table>';	

	return true;
}

function print_long($q, $answers) {
	global $tid, $tt;
	echo '<br />';
	echo '<table class="data static" summary="" style="width: 95%" rules="cols">';
	echo '<thead>';
	echo '<tr>';
	echo '<th scope="col">'._AT('question').'</th>';	
	echo '<th scope="col">'._AT('left_blank').'</th>';
	echo '<th scope="col">'._AT('results').'</th>';	
	echo '</tr>';
	echo '</thead>';

	echo '<tr>';
	echo '<td>'.$q['question'].'</td>';

	$num_blanks = intval($answers['']['count']);
	//blank
	echo '<td align="center" width="70" valign="top">'.$num_blanks.'</td>';
	
	echo '<td align="center" valign="top">';
	if ((count($answers)-$num_blanks) > 0) {
		echo '<a href="tools/tests/results_quest_long.php?tid='.$tid.SEP.'qid='.$q['question_id'].SEP.'q='.urlencode($q['question']).'">'._AT('view_responses').' ('.(count($answers)-$num_blanks).')'.'</a>';
	} else {
		echo _AT('none');
	}
	echo '</td>';
	echo '</tr>';
	echo '</table>';
}

require(AT_INCLUDE_PATH.'header.inc.php');

$sql	= "SELECT TQ.*, TQA.* FROM ".TABLE_PREFIX."tests_questions TQ INNER JOIN ".TABLE_PREFIX."tests_questions_assoc TQA USING (question_id) WHERE TQ.course_id=$_SESSION[course_id] AND TQA.test_id=$tid ORDER BY TQA.ordering, TQA.question_id";
$result	= mysql_query($sql, $db);
$questions = array();
while ($row = mysql_fetch_array($result)) {
	$row['score']	= 0;
	$questions[]	= $row;
	$q_sql .= $row['question_id'].',';
}
$q_sql = substr($q_sql, 0, -1);
$num_questions = count($questions);

//check if survey
$sql	= "SELECT out_of, title, randomize_order FROM ".TABLE_PREFIX."tests WHERE test_id=$tid";
$result = mysql_query($sql, $db);
$row = mysql_fetch_assoc($result);
$tt = $row['title'];
$random = $row['randomize_order'];

echo '<h3>'.$row['title'].'</h3><br />';

//get all the questions in this test, store them
$sql	= "SELECT TQ.*, TQA.* FROM ".TABLE_PREFIX."tests_questions TQ INNER JOIN ".TABLE_PREFIX."tests_questions_assoc TQA USING (question_id) WHERE TQ.course_id=$_SESSION[course_id] AND TQA.test_id=$tid ORDER BY TQA.ordering, TQA.question_id";

$result = mysql_query($sql, $db);
$questions = array();	
while ($row = mysql_fetch_assoc($result)) {
	$questions[$row['question_id']] = $row;
}
$long_qs = substr($long_qs, 0, -1);

//get the answers:  count | q_id | answer
$sql = "SELECT count(*), A.question_id, A.answer, A.score
		FROM ".TABLE_PREFIX."tests_answers A, ".TABLE_PREFIX."tests_results R
		WHERE R.result_id=A.result_id AND R.final_score<>'' AND R.test_id=$tid
		GROUP BY A.question_id, A.answer
		ORDER BY A.question_id, A.answer";
$result = mysql_query($sql, $db);
$num_results = mysql_num_rows($result);

if (!$num_results) {
	echo '<p><em>'._AT('no_results_available').'</em></p>';
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

echo '<p>'._AT('total').' '._AT('results').': '.$num_results.'<br />';
echo '<img src="images/checkmark.gif" alt="Correct checkmark" />- '._AT('correct_answer').'<br /></p>';

// This is to prevent division by zero in cases where the test has not been taken but an average is calculated (i.e. 0/0)
if (!$num_results) {
	$num_results = 1;
}


$ans = array();	
while ($row = mysql_fetch_assoc($result)) {
	$ans[$row['question_id']][$row['answer']] = array('count'=>$row['count(*)'], 'score'=>$row['score']);
}

//print out rows
foreach ($questions as $q_id => $q) {
	//for random: num_results is going to be specific to each question.
	//This is a randomized test which means that it is possible each question has been answered a different number of times.  Statistics are therefore based on the number of times each question was answered, not the number of times the test has been taken.

	//catch random unanswered
	if($ans[$q_id]) {
		switch ($q['type']) {
			case AT_TESTS_MC:
				for ($i=0; $i<=10; $i++) {
					if ($q['choice_'.$i] == '') {
						$i--;
						break;
					}
				}
				if ($random) {
					$num_results = 0;		
					foreach ($ans[$q_id] as $answer) {
						$num_results += $answer['count'];
					}
				}
				
				print_multiple_choice($q, $ans[$q_id], $i, $num_results);
				break;

			case AT_TESTS_TF:
				if ($random) {		
					$num_results = 0;		
					foreach ($ans[$q_id] as $answer) {
						$num_results += $answer['count'];
					}
				}

				print_true_false($q, $ans[$q_id], $num_results);
				break;

			case AT_TESTS_LONG:

				//get score of answers
				print_long($q, $ans[$q_id]);
				break;

			case AT_TESTS_LIKERT:
				if ($random) {		
					$num_results = 0;		
					foreach ($ans[$q_id] as $answer) {
						$num_results += $answer['count'];
					}
				}
				for ($i=0; $i<=10; $i++) {
					if ($q['choice_'.$i] == '') {
						$i--;
						break;
					}
				}
				print_likert($q, $ans[$q_id], $i, $num_results);
				break;
		}
	}
}

require(AT_INCLUDE_PATH.'footer.inc.php');
?>