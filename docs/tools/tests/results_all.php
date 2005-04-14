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

authenticate(AT_PRIV_TEST_MARK);

$tid = intval($_REQUEST['tid']);

$_pages['tools/tests/results_all.php']['title_var']  = 'mark_statistics';
$_pages['tools/tests/results_all.php']['parent'] = 'tools/tests/results_all_quest.php?tid='.$tid;

$_pages['tools/tests/results_all_quest.php?tid='.$tid]['title_var'] = 'question_statistics';
$_pages['tools/tests/results_all_quest.php?tid='.$tid]['parent'] = 'tools/tests/index.php';
$_pages['tools/tests/results_all_quest.php?tid='.$tid]['children'] = array('tools/tests/results_all.php');


require(AT_INCLUDE_PATH.'header.inc.php');

$sql	= "SELECT title, out_of, result_release FROM ".TABLE_PREFIX."tests WHERE test_id=$tid";
$result	= mysql_query($sql, $db);
$row = mysql_fetch_array($result);
$out_of = $row['out_of'];

$sql	= "SELECT TQ.*, TQA.* FROM ".TABLE_PREFIX."tests_questions TQ INNER JOIN ".TABLE_PREFIX."tests_questions_assoc TQA USING (question_id) WHERE TQ.course_id=$_SESSION[course_id] AND TQA.test_id=$tid ORDER BY TQA.ordering, TQA.question_id";

//$sql	= "SELECT * FROM ".TABLE_PREFIX."tests_questions Q WHERE Q.test_id=$tid AND Q.course_id=$_SESSION[course_id] ORDER BY ordering";
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

$sql	= "SELECT R.*, M.login FROM ".TABLE_PREFIX."tests_results R, ".TABLE_PREFIX."members M WHERE R.test_id=$tid AND R.final_score<>'' AND R.member_id=M.member_id ORDER BY M.login, R.date_taken";
$result = mysql_query($sql, $db);
if ($row = mysql_fetch_assoc($result)) {
	$count		 = 0;
	$total_score = 0;

	echo '<table class="data static" summary="" style="width: 90%" rules="cols">';
	echo '<thead>';
	echo '<tr>';
	echo '<th scope="col">'._AT('login_name').'</th>';
	echo '<th scope="col">'._AT('date_taken').'</th>';
	echo '<th scope="col">'._AT('mark').'/'.$total_weight.'</th>';
	for($i = 0; $i< $num_questions; $i++) {
		echo '<th scope="col">Q'.($i+1).' /'.$questions[$i]['weight'].'</th>';
	}
	echo '</tr>';
	echo '</thead>';

	do {
		echo '<tr>';
		echo '<td class="row1" align="center">'.$row['login'].'</td>';
		echo '<td class="row1" align="center">'.AT_date('%j/%n/%y %G:%i', $row['date_taken'], AT_DATE_MYSQL_DATETIME).'</td>';
		echo '<td class="row1" align="center">'.$row['final_score'].'</td>';

		$total_score += $row['final_score'];

		$answers = array(); /* need this, because we dont know which order they were selected in */
		$sql = "SELECT question_id, score FROM ".TABLE_PREFIX."tests_answers WHERE result_id=$row[result_id] AND question_id IN ($q_sql)";
		$result2 = mysql_query($sql, $db);
		while ($row2 = mysql_fetch_assoc($result2)) {
			$answers[$row2['question_id']] = $row2['score'];
		}
		for($i = 0; $i < $num_questions; $i++) {
			$questions[$i]['score'] += $answers[$questions[$i]['question_id']];
			echo '<td class="row1" align="center">'.$answers[$questions[$i]['question_id']].'</td>';
		}

		echo '</tr>';
		$count++;
	} while ($row = mysql_fetch_assoc($result));

	echo '<tr>';
	echo '<td colspan="2" class="row1" align="right"><strong>'._AT('average').':</strong></td>';
	echo '<td class="row1" align="center"><strong>'.number_format($total_score/$count, 1).'</strong></td>';

	for($i = 0; $i < $num_questions; $i++) {
		echo '<td class="row1" align="center"><strong>';
			if ($questions[$i]['weight']) {
				echo number_format($questions[$i]['score']/$count, 1);
			} else {
				echo '-';
			}
			echo '</strong></td>';
	}
	echo '</tr>';

	echo '<tr>';
	echo '<td colspan="2" class="row1"></td>';
	echo '<td class="row1" align="center"><strong>';
	if ($total_weight) {
		echo number_format($total_score/$count/$total_weight*100, 1).'%';
	}
	echo '</strong></td>';

	for($i = 0; $i < $num_questions; $i++) {
		echo '<td class="row1" align="center"><strong>';
			if ($questions[$i]['weight']) {
				echo number_format($questions[$i]['score']/$count/$questions[$i]['weight']*100, 1).'%';
			} else {
				echo '-';
			}
		echo '</strong></td>';
	}
	echo '</tr>';

} else {
	echo '<em>'._AT('no_results_available').'</em>';
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

echo '</table>';
?>

<br /><p align="center"><a href="tools/tests/results_all_csv.php?tid=<?php echo $tid; ?>"><?php echo _AT('download_test_csv'); ?></a></p>

<?php 
require(AT_INCLUDE_PATH.'footer.inc.php');
?>