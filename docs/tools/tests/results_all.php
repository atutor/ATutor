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
	$page = 'tests';
	define('AT_INCLUDE_PATH', '../../include/');
	require(AT_INCLUDE_PATH.'vitals.inc.php');
	$_section[0][0] = _AT('tools');
	$_section[0][1] = 'tools/';
	$_section[1][0] = _AT('test_manager');
	$_section[1][1] = 'tools/tests';
	$_section[2][0] = _AT('results');

	authenticate(AT_PRIV_TEST_MARK);
	$tt = urldecode($_GET['tt']);
	if($tt == ''){
		$tt = $_POST['tt'];
	}

	$tid = intval($_GET['tid']);
	if ($tid == 0){
		$tid = intval($_POST['tid']);
	}

function print_likert($question, $answers, $num) {

	echo '<br />';
	echo '<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center" width="90%">';
	echo '<tr>';
	echo '<th scope="col" width="40%"><small>'._AT('question').'</small></th>';
	echo '<th scope="col"><small>'._AT('total').'</small></th>';
	echo '<th scope="col"><small>'._AT('average').'</small></th>';
	for ($i=1; $i<=$num+1; $i++) {
		echo '<th scope="col">'.$i.'</th>';
	}
	echo '</tr>';

	echo '<tr>';
	echo '<td>'.$question.'</td>';

	$total = 0;
	$sum = 0;
	for ($j=0; $j<=$num; $j++) {
		$total = $total + $answers[$j];
		if ($answers[$j] != '') {
			$sum += ($j+1) * $answers[$j];
		}
	}
	//total
	echo '<td align="center" width="70" valign="top">'.$total.'</td>';
	//avg 
	echo '<td align="center" width="70" valign="top">'.round($sum/$total, 1).'</td>';

	for ($j=0; $j<=$num; $j++) {
		echo '<td align="center" valign="top">'.$answers[$j].'</td>';		
	}
	echo '</tr>';
	echo '</table>';	

	return true;
}

function print_true_false($q, $answers) {
	echo '<br />';
	echo '<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center" width="90%">';
	echo '<tr>';
	echo '<th scope="col" width="40%"><small>'._AT('question').'</small></th>';	
	echo '<th scope="col"><small>'._AT('total').'</small></th>';	
	echo '<th scope="col"><small>'._AT('average').'</small></th>';

		if ($q['answer_0'] == 1) {		
			echo '<th scope="col">'._AT('true').'<font color="red">*</font></th>';
			echo '<th scope="col">'._AT('false').'</th>';
		} else {
			echo '<th scope="col">'._AT('true').'</th>';
			echo '<th scope="col">'._AT('false').'<font color="red">*</font></th>';
		}
	
	echo '</tr>';

	echo '<tr>';
	echo '<td>'.$q['question'].'</td>';
	//total
	echo '<td align="center" width="70" valign="top">'.($answers[0]+$answers[1]).'</td>';
	//avg
	echo '<td align="center" width="70" valign="top"> - </td>';	

	echo '<td align="center" width="20%" valign="top">'.$answers[1].'</td>';
	echo '<td align="center" width="20%" valign="top">'.$answers[0].'</td>';	

	echo '</tr>';
	echo '</table>';	

	return true;
}
function print_multiple_choice($q, $answers, $num) {

	echo '<br />';
	echo '<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center" width="90%">';
	echo '<tr>';
	echo '<th scope="col" width="40%"><small>'._AT('question').'</small></th>';
	echo '<th scope="col"><small>'._AT('total').'</small></th>';
	echo '<th scope="col"><small>'._AT('average').'</small></th>';
	for ($i=1; $i<=$num+1; $i++) {
		if ($q['answer_'.($i-1)]) {		
			echo '<th scope="col">'.$i.'<font color="red">*</font></th>';
		} else {
			echo '<th scope="col">'.$i.'</th>';
		}
	}
	echo '</tr>';

	echo '<tr>';
	echo '<td>'.$q['question'].'</td>';

	$total = 0;
	$sum = 0;
	for ($j=0; $j<=$num; $j++) {
		$total = $total + $answers[$j];
		if ($answers[$j] != '') {
			$sum += ($j+1) * $answers[$j];
		}
	}
	//total
	echo '<td align="center" width="70" valign="top">'.$total.'</td>';
	//avg 
	echo '<td align="center" width="70" valign="top">'.round($sum/$total).'</td>';

	for ($j=0; $j<=$num; $j++) {
		echo '<td align="center" valign="top">'.$answers[$j].'</td>';		
	}
	echo '</tr>';
	echo '</table>';	

	return true;
}

function print_long($q) {
	echo '<br />';
	echo '<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center" width="90%">';
	echo '<tr>';
	echo '<th scope="col" width="40%"><small>'._AT('question').'</small></th>';	
	echo '<th scope="col"><small>'._AT('total').'</small></th>';
	echo '<th scope="col"><small>'._AT('average').'</small></th>';
	echo '<th scope="col"><small>'._AT('results').'</small></th>';	
	echo '</tr>';

	echo '<tr>';
	echo '<td>'.$q['question'].'</td>';
	//total
	echo '<td align="center" width="70" valign="top">'.($answers[0]+$answers[1]).'</td>';

	//avg
	echo '<td align="center" width="70" valign="top"> - </td>';	
	echo '<td align="center" valign="top"><a href="">compilation of answers</a></td>';


	echo '</tr>';
	echo '</table>';
}

	require(AT_INCLUDE_PATH.'header.inc.php');
echo '<h2>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '<a href="tools/" class="hide"><img src="images/icons/default/square-large-tools.gif"  class="menuimage" border="0" vspace="2" width="42" height="40" alt="" /></a>';
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


	echo '<h3>'._AT('results_for').' '.$_GET['tt'].'</h3>';

	$tid = intval($_GET['tid']);

	$sql	= "SELECT * FROM ".TABLE_PREFIX."tests_questions Q WHERE Q.test_id=$tid AND Q.course_id=$_SESSION[course_id] ORDER BY ordering";
 	$result	= mysql_query($sql, $db);
	$questions = array();
	$total_weight = 0;
	while ($row = mysql_fetch_array($result)) {
		$row['score']	= 0;
		$questions[]	= $row;
		$q_sql .= $row['question_id'].',';
		$total_weight += $row['weight'];
	}
	$q_sql = substr($q_sql, 0, -1);
	$num_questions = count($questions);

	echo '<p><a href="tools/tests/results_all_csv.php?tid='.$tid.SEP.'tt='.$_GET['tt'].'">' . _AT('download_test_csv') . '</a></p>';

	echo '<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center" width="90%">';
	echo '<tr>';
	echo '<th scope="col"><small>'._AT('username').'</small></th>';
	echo '<th scope="col"><small>'._AT('date_taken').'</small></th>';
	echo '<th scope="col"><small>'._AT('mark').'/'.$total_weight.'</small></th>';
	for($i = 0; $i< $num_questions; $i++) {
		echo '<th scope="col"><small>Q'.($i+1).'/'.$questions[$i]['weight'].'</small></th>';
	}
	echo '</tr>';

	$sql	= "SELECT R.*, M.login FROM ".TABLE_PREFIX."tests_results R, ".TABLE_PREFIX."members M WHERE R.test_id=$tid AND R.final_score<>'' AND R.member_id=M.member_id ORDER BY M.login, R.date_taken";
	$result = mysql_query($sql, $db);
	if ($row = mysql_fetch_array($result)) {
		$count		 = 0;
		$total_score = 0;
		do {
			echo '<tr>';
			echo '<td class="row1" align="center"><small><strong>'.$row['login'].'</strong></small></td>';
			echo '<td class="row1" align="center"><small>'.AT_date('%j/%n/%y %G:%i', $row['date_taken'], AT_DATE_MYSQL_DATETIME).'</small></td>';
			echo '<td class="row1" align="center"><small>'.$row['final_score'].'</small></td>';

			$total_score += $row['final_score'];

			$answers = array(); /* need this, because we dont know which order they were selected in */
			$sql = "SELECT question_id, score FROM ".TABLE_PREFIX."tests_answers WHERE result_id=$row[result_id] AND question_id IN ($q_sql)";
			$result2 = mysql_query($sql, $db);
			while ($row2 = mysql_fetch_array($result2)) {
				$answers[$row2['question_id']] = $row2['score'];
			}
			for($i = 0; $i < $num_questions; $i++) {
				$questions[$i]['score'] += $answers[$questions[$i]['question_id']];
				echo '<td class="row1" align="center"><small>'.$answers[$questions[$i]['question_id']].'</small></td>';
			}

			echo '</tr>';
			echo '<tr><td height="1" class="row2" colspan="'.(3+$num_questions).'"></td></tr>';
			$count++;
		} while ($row = mysql_fetch_array($result));

		echo '<tr><td height="1" class="row2" colspan="'.(3+$num_questions).'"></td></tr>';
		echo '<tr>';
		echo '<td colspan="2" class="row1" align="right"><small><strong>'._AT('average').':</strong></small></td>';
		echo '<td class="row1" align="center"><small><strong>'.number_format($total_score/$count, 1).'</strong></small></td>';

		for($i = 0; $i < $num_questions; $i++) {
			echo '<td class="row1" align="center"><small><strong>';
				if ($questions[$i]['weight']) {
					echo number_format($questions[$i]['score']/$count, 1);
				} else {
					echo '-';
				}
				echo '</strong></small></td>';
		}
		echo '</tr>';
		echo '<tr><td height="1" class="row2" colspan="'.(3+$num_questions).'"></td></tr>';

		echo '<tr>';
		echo '<td colspan="2" class="row1"></td>';
		echo '<td class="row1" align="center"><small><strong>'.number_format($total_score/$count/$total_weight*100, 1).'%</strong></small></td>';

		for($i = 0; $i < $num_questions; $i++) {
			echo '<td class="row1" align="center"><small><strong>';
				if ($questions[$i]['weight']) {
					echo number_format($questions[$i]['score']/$count/$questions[$i]['weight']*100, 1).'%';
				} else {
					echo '-';
				}
			echo '</strong></small></td>';
		}
		echo '</tr>';

	} else {
		echo '<tr><td colspan="'.(3+$num_questions).'" class="row1"><small><i>'._AT('no_results_available').'.</i></small></td></tr>';
		echo '</table>';
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}

	echo '</table>';

//----------------results--------

echo '<br /><strong>Results Per Question</strong><br />';

	//get all the questions in this test, store them
	$sql = "SELECT *
			FROM ".TABLE_PREFIX."tests_questions  
			WHERE test_id=$tid
			ORDER BY question_id";

	$result = mysql_query($sql, $db);
	$questions = array();	
	while ($row = mysql_fetch_assoc($result)) {
		$questions[$row['question_id']] = $row;

		if ($row['type'] == AT_TESTS_LONG) {
			$long_qs .= $row['question_id'].",";
		}
	}
	$long_qs = substr($long_qs, 0, -1);

	//get the answers:  count | q_id | answer
	$sql = "SELECT count(*), question_id, answer
			FROM ".TABLE_PREFIX."tests_answers ";
		if($long_qs != '') { 	
			"WHERE question_id NOT IN (".$long_qs.")";
		}
	$sql .="GROUP BY question_id, answer
			ORDER BY question_id, answer";
	$result = mysql_query($sql, $db);
	$ans = array();	
	while ($row = mysql_fetch_assoc($result)) {
		$ans[$row['question_id']][$row['answer']] = $row['count(*)'];
	}

	//print out rows
	foreach ($questions as $q_id => $q) {

		switch ($q['type']) {
			case AT_TESTS_MC:
				for ($i=0; $i<=10; $i++) {
					if ($q['choice_'.$i] == '') {
						$i--;
						break;
					}
				}
				print_multiple_choice($q, $ans[$q_id], $i);
				break;

			case AT_TESTS_TF:
				print_true_false($q, $ans[$q_id]);
				break;

			case AT_TESTS_LONG:
				print_long($q, $ans[$q_id]);
				break;

			case AT_TESTS_LIKERT:
				for ($i=0; $i<=10; $i++) {
					if ($q['choice_'.$i] == '') {
						$i--;
						break;
					}
				}
				print_likert($q['question'], $ans[$q_id], $i);
				break;
		}

	}

	require(AT_INCLUDE_PATH.'footer.inc.php');
?>