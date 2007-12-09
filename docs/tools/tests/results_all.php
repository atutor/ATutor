<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2007 by Greg Gay & Joel Kronenberg        */
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

$_pages['tools/tests/results_all.php']['title_var']  = 'mark_statistics';
$_pages['tools/tests/results_all.php']['parent'] = 'tools/tests/results_all_quest.php?tid='.$tid;

$_pages['tools/tests/results_all_quest.php?tid='.$tid]['title_var'] = 'question_statistics';
$_pages['tools/tests/results_all_quest.php?tid='.$tid]['parent'] = 'tools/tests/index.php';
$_pages['tools/tests/results_all_quest.php?tid='.$tid]['children'] = array('tools/tests/results_all.php');

if ($_POST['download']){
	$_POST['test_id']=intval($_POST['test_id']);
	header('Location: results_all_csv.php?tid='.$_POST['test_id']);
}

require(AT_INCLUDE_PATH.'lib/test_result_functions.inc.php');
require(AT_INCLUDE_PATH.'header.inc.php');

$sql	= "SELECT title, out_of, result_release, randomize_order FROM ".TABLE_PREFIX."tests WHERE test_id=$tid";
$result	= mysql_query($sql, $db);
$row = mysql_fetch_array($result);
$out_of = $row['out_of'];
$random = $row['randomize_order'];

echo '<h3>'.$row['title'].'</h3><br />';
?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<div class="input-form">
	<div class="row">
		<h3><?php echo _AT('download_test_csv'); ?></h3>
	</div>

	<div class="row buttons">
		<input type="submit" name="download" value="<?php echo _AT('download'); ?>" />
		<input type="hidden" name="test_id" value="<?php echo $tid; ?>" />
	</div>
</div>
</form>

<?php
$sql	= "SELECT TQ.*, TQA.* FROM ".TABLE_PREFIX."tests_questions TQ INNER JOIN ".TABLE_PREFIX."tests_questions_assoc TQA USING (question_id) WHERE TQ.course_id=$_SESSION[course_id] AND TQA.test_id=$tid ORDER BY TQA.ordering, TQA.question_id";

//$sql	= "SELECT * FROM ".TABLE_PREFIX."tests_questions Q WHERE Q.test_id=$tid AND Q.course_id=$_SESSION[course_id] ORDER BY ordering";
$result	= mysql_query($sql, $db);
$questions = array();
$total_weight = 0;
$i = 0;
while ($row = mysql_fetch_assoc($result)) {
	$row['score']	= 0;
	$questions[$i]	= $row;
	$questions[$i]['count'] = 0;
	$q_sql .= $row['question_id'].',';
	$total_weight += $row['weight'];
	$i++;
}
$q_sql = substr($q_sql, 0, -1);
$num_questions = count($questions);

//get all the marked tests for this test
$guest_text = '- '._AT('guest').' -';
$sql	= "SELECT R.*, M.login FROM ".TABLE_PREFIX."tests_results R LEFT JOIN ".TABLE_PREFIX."members M USING (member_id) WHERE R.status=1 AND R.test_id=$tid AND R.final_score<>'' ORDER BY M.login, R.date_taken";
$result = mysql_query($sql, $db);
$num_results = mysql_num_rows($result);
if ($row = mysql_fetch_assoc($result)) {
	$total_score = 0;

	echo '<table class="data static" summary="" style="width: 90%" rules="cols">';
	echo '<thead>';
	echo '<tr>';
	echo '<th scope="col">'._AT('login_name').'</th>';
	echo '<th scope="col">'._AT('date_taken').'</th>';
	echo '<th scope="col">'._AT('mark').'</th>';
	for($i = 0; $i< $num_questions; $i++) {
		echo '<th scope="col">Q'.($i+1).' /'.$questions[$i]['weight'].'</th>';
	}
	echo '</tr>';
	echo '</thead>';
	echo '<tbody>';
	
		$sql2	= "SELECT anonymous FROM ".TABLE_PREFIX."tests WHERE test_id=$tid AND course_id=$_SESSION[course_id]";
		$result2	= mysql_query($sql2, $db);
		while($row2 =mysql_fetch_array($result2)){
				$anonymous = $row2['anonymous'];
		}

	do {
		$row['login']     = $row['login']     ? $row['login']     : $guest_text;
		echo '<tr>';
			
		if($anonymous == 1){
				echo '<td align="center">'._AT('anonymous').'</td>';
		}else{
				echo '<td align="center">'.$row['login'].'</td>';
		}

		echo '<td align="center">'.AT_date('%j/%n/%y %G:%i', $row['date_taken'], AT_DATE_MYSQL_DATETIME).'</td>';
		if ($random) {
			$total_weight = get_random_outof($row['test_id'], $row['result_id']);
		}
		echo '<td align="center">'.$row['final_score'].'/'.$total_weight.'</td>';

		$total_score += $row['final_score'];

		$answers = array(); /* need this, because we dont know which order they were selected in */

		//get answers for this test result
		$sql = "SELECT question_id, score FROM ".TABLE_PREFIX."tests_answers WHERE result_id=$row[result_id] AND question_id IN ($q_sql)";
		$result2 = mysql_query($sql, $db);
		while ($row2 = mysql_fetch_assoc($result2)) {
			$answers[$row2['question_id']] = $row2['score'];
		}

		//print answers out for each question
		for($i = 0; $i < $num_questions; $i++) {
			$questions[$i]['score'] += $answers[$questions[$i]['question_id']];
			echo '<td align="center">';
			if ($answers[$questions[$i]['question_id']] == '') {
				echo '<span style="color:#ccc;">-</span>';
			} else {
				echo $answers[$questions[$i]['question_id']];
				if ($random) {
					$questions[$i]['count']++;
				}
			}
			echo '</td>';
		}

		echo '</tr>';
	} while ($row = mysql_fetch_assoc($result));
	echo '</tbody>';

	echo '<tfoot>';
	echo '<tr>';
	echo '<td colspan="2" align="right"><strong>'._AT('average').':</strong></td>';
	echo '<td align="center"><strong>'.number_format($total_score/$num_results, 1).'</strong></td>';

	for($i = 0; $i < $num_questions; $i++) {
		echo '<td class="row1" align="center"><strong>';
			if ($random) {
				$count = $questions[$i]['count'];
			}
			if ($questions[$i]['weight'] && $count) {
					echo number_format($questions[$i]['score']/$count, 1);
			} else {
				echo '0.0';
			}
			echo '</strong></td>';
	}
	echo '</tr>';

	echo '<tr>';
	echo '<td colspan="2">&nbsp;</td>';
	echo '<td align="center"><strong>';
	if ($total_weight) {
		echo number_format($total_score/$num_results/$total_weight*100, 1).'%';
	}
	echo '</strong></td>';

	for($i = 0; $i < $num_questions; $i++) {
		echo '<td align="center"><strong>';
			if ($random) {
				$count = $questions[$i]['count'];
			}

			if ($questions[$i]['weight'] && $count) {
				echo number_format($questions[$i]['score']/$count/$questions[$i]['weight']*100, 1).'%';
			} else {
				echo '00.0%';
			}
		echo '</strong></td>';
	}
	echo '</tr>';
	echo '</tfoot>';
} else {
	echo '<em>'._AT('no_results_available').'</em>';
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}
?>
</table>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>