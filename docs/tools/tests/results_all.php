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
	$_section[0][1] = 'tools/index.php';
	$_section[1][0] = _AT('test_manager');
	$_section[1][1] = 'tools/tests/index.php';
	$_section[2][0] = _AT('results');

	authenticate(AT_PRIV_TEST_MARK);

	$tid = intval($_GET['tid']);
	if ($tid == 0){
		$tid = intval($_POST['tid']);
	}

	require(AT_INCLUDE_PATH.'header.inc.php');
echo '<h2>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '<a href="tools/index.php" class="hide"><img src="images/icons/default/square-large-tools.gif"  class="menuimage" border="0" vspace="2" width="42" height="40" alt="" /></a>';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo ' <a href="tools/index.php" class="hide">'._AT('tools').'</a>';
	}
echo '</h2>';

echo '<h3>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '&nbsp;<img src="images/icons/default/test-manager-large.gif"  class="menuimageh3" width="42" height="38" alt="" /> ';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo '<a href="tools/tests/index.php">'._AT('test_manager').'</a>';
	}
echo '</h3>';

	$sql	= "SELECT title, automark FROM ".TABLE_PREFIX."tests WHERE test_id=$tid";
 	$result	= mysql_query($sql, $db);
	$row = mysql_fetch_array($result);
	echo '<h3>'._AT('results_for', AT_print($row['title'], 'tests.title')).'</h3>';
	$automark = $row['automark'];

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

	echo '<p><br /><a href="tools/tests/results_all_quest.php?tid='.$tid.'">' . _AT('question_statistics').'</a> | <strong>'. _AT('mark_statistics').'</strong>';
	//echo ' | <a href="tools/tests/results_all_csv.php?tid='.$tid.'">' . _AT('download_test_csv') . '</a>';
	echo '</p>';

if ($automark == AT_MARK_UNMARKED) {
	echo '<em>'._AT('marks_unavailable').'</em>';
	require (AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}
	echo '<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center" width="90%">';
	echo '<tr>';
	echo '<th scope="col"><small>'._AT('username').'</small></th>';
	echo '<th scope="col"><small>'._AT('date_taken').'</small></th>';
	echo '<th scope="col"><small>'._AT('mark').'/'.$total_weight.'</small></th>';
	for($i = 0; $i< $num_questions; $i++) {
		echo '<th scope="col"><small>Q'.($i+1).' /'.$questions[$i]['weight'].'</small></th>';
	}
	echo '</tr>';

	$sql	= "SELECT R.*, M.login FROM ".TABLE_PREFIX."tests_results R, ".TABLE_PREFIX."members M WHERE R.test_id=$tid AND R.final_score<>'' AND R.member_id=M.member_id ORDER BY M.login, R.date_taken";
	$result = mysql_query($sql, $db);
	if ($row = mysql_fetch_assoc($result)) {
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
			while ($row2 = mysql_fetch_assoc($result2)) {
				$answers[$row2['question_id']] = $row2['score'];
			}
			for($i = 0; $i < $num_questions; $i++) {
				$questions[$i]['score'] += $answers[$questions[$i]['question_id']];
				echo '<td class="row1" align="center"><small>'.$answers[$questions[$i]['question_id']].'</small></td>';
			}

			echo '</tr>';
			echo '<tr><td height="1" class="row2" colspan="'.(3+$num_questions).'"></td></tr>';
			$count++;
		} while ($row = mysql_fetch_assoc($result));

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
		echo '<td class="row1" align="center"><small><strong>';
		if ($total_weight) {
			echo number_format($total_score/$count/$total_weight*100, 1).'%';
		}
		echo '</strong></small></td>';

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

	require(AT_INCLUDE_PATH.'footer.inc.php');
?>