<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/

	$_include_path = '../../include/';
	require($_include_path.'vitals.inc.php');
	$_section[0][0] = _AT('tools');
	$_section[0][1] = 'tools/';
	$_section[1][0] = _AT('test_manager');
	$_section[1][1] = 'tools/tests';
	$_section[2][0] = _AT('results');

	if (!$_SESSION['is_admin']) {
		exit;
	}

	require($_include_path.'header.inc.php');

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

	echo '<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center">';
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
			echo '<td class="row1" align="right"><small><strong>'.$row['login'].'</strong></small></td>';
			echo '<td class="row1"><small>'.AT_date('%j/%n/%y %G:%i', $row['date_taken'], AT_DATE_MYSQL_DATETIME).'</small></td>';
			echo '<td class="row1" align="right"><small>'.$row['final_score'].'</small></td>';

			$total_score += $row['final_score'];

			$answers = array(); /* need this, because we dont know which order they were selected in */
			$sql = "SELECT question_id, score FROM ".TABLE_PREFIX."tests_answers WHERE result_id=$row[result_id] AND question_id IN ($q_sql)";
			$result2 = mysql_query($sql, $db);
			while ($row2 = mysql_fetch_array($result2)) {
				$answers[$row2['question_id']] = $row2['score'];
			}
			for($i = 0; $i < $num_questions; $i++) {
				$questions[$i]['score'] += $answers[$questions[$i]['question_id']];
				echo '<td class="row1" align="right"><small>'.$answers[$questions[$i]['question_id']].'</small></td>';
			}

			echo '</tr>';
			echo '<tr><td height="1" class="row2" colspan="'.(3+$num_questions).'"></td></tr>';
			$count++;
		} while ($row = mysql_fetch_array($result));

		echo '<tr><td height="1" class="row2" colspan="'.(3+$num_questions).'"></td></tr>';
		echo '<tr>';
		echo '<td colspan="2" class="row1" align="right"><small><strong>'._AT('average').':</strong></small></td>';
		echo '<td class="row1" align="right"><small><strong>'.number_format($total_score/$count, 1).'</strong></small></td>';

		for($i = 0; $i < $num_questions; $i++) {
			echo '<td class="row1" align="right"><small><strong>'.number_format($questions[$i]['score']/$count, 1).'</strong></small></td>';
		}
		echo '</tr>';
		echo '<tr><td height="1" class="row2" colspan="'.(3+$num_questions).'"></td></tr>';

		echo '<tr>';
		echo '<td colspan="2" class="row1"></td>';
		echo '<td class="row1" align="right"><small><strong>'.number_format($total_score/$count/$total_weight*100, 1).'%</strong></small></td>';

		for($i = 0; $i < $num_questions; $i++) {
			echo '<td class="row1" align="right"><small><strong>'.number_format($questions[$i]['score']/$count/$questions[$i]['weight']*100, 1).'%</strong></small></td>';
		}
		echo '</tr>';

	} else {
		echo '<tr><td colspan="'.(3+$num_questions).'" class="row1"><small><i>'._AT('no_results_available').'.</i></small></td></tr>';
	}

	echo '</table>';

	require($_include_path.'footer.inc.php');
?>
