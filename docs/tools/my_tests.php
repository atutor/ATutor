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

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/test_result_functions.inc.php');

require(AT_INCLUDE_PATH.'header.inc.php');

$sql	= "SELECT T.*, UNIX_TIMESTAMP(T.start_date) AS us, UNIX_TIMESTAMP(T.end_date) AS ue, COUNT(Q.weight) AS numquestions FROM ".TABLE_PREFIX."tests T, ".TABLE_PREFIX."tests_questions_assoc Q WHERE Q.test_id=T.test_id AND T.course_id=$_SESSION[course_id] GROUP BY T.test_id ORDER BY T.start_date, T.title";
$result	= mysql_query($sql, $db);

?>
	<table class="data static" summary="" rules="cols">
	<thead>
	<tr>
		<th scope="col"><?php echo _AT('title');      ?></th>
		<th scope="col"><?php echo _AT('status');     ?></th>
		<th scope="col"><?php echo _AT('start_date'); ?></th>
		<th scope="col"><?php echo _AT('end_date');   ?></th>
		<th scope="col"><?php echo _AT('attempts');   ?></th>
		<th scope="col"><?php echo _AT('questions');  ?></th>
		<th scope="col"><?php echo _AT('out_of');     ?></th>
	</tr>
	</thead>
	<tbody>

<?php

while (($row = mysql_fetch_assoc($result)) && authenticate_test($row['test_id'])) {
	$count++;
	echo '<tr>';

	echo '<td>';
	$sql		= "SELECT COUNT(test_id) AS cnt FROM ".TABLE_PREFIX."tests_results WHERE test_id=".$row['test_id']." AND member_id=".$_SESSION['member_id'];
	$takes_result= mysql_query($sql, $db);
	$takes = mysql_fetch_assoc($takes_result);
	if ( ($row['us'] <= time() && $row['ue'] >= time()) && 
	   ( ($row['num_takes'] == AT_TESTS_TAKE_UNLIMITED) || ($takes['cnt'] < $row['num_takes']) )  ) {
		echo '<strong><a href="tools/take_test.php?tid='.$row['test_id'].'">'.AT_print($row['title'], 'tests.title').'</a></strong>';
	} else {
		echo '<small class="bigspacer">'.AT_print($row['title'], 'tests.title').'';
	}
	echo '</td><td align="center">';
	if ( ($row['us'] <= time()) && ($row['ue'] >= time() ) ) {
		echo '<i><b>'._AT('ongoing').'</b></i>';
	} else if ($row['ue'] < time() ) {
		echo '<i>'._AT('expired').'</i>';
	} else if ($row['us'] > time() ) {
		echo '<i>'._AT('pending').'</i>';
	}
	echo '</td>';
	echo '<td align="center">'.substr($row['start_date'], 0, -3).'</td>';
	echo '<td align="center">'.substr($row['end_date'], 0, -3).'</td>';

	if ($row['num_takes'] == AT_TESTS_TAKE_UNLIMITED) {
		echo '<td align="center">'.$takes['cnt'].'/'._AT('unlimited').'</td>';
	} else  {
		echo '<td align="center">'.$takes['cnt'].'/'.$row['num_takes'].'</td>';
	}

	if ($row['random']) {
		echo '<td align="center">'.$row['num_questions'].'</td>';
		echo '<td align="center">-</td>';
	} else {
		echo '<td align="center">'.$row['numquestions'].'</td>';
		if ($row['out_of'] > 0) {
			echo '<td align="center">'.$row['out_of'].'</td>';
		} else {
			echo '<td align="center"><em>'._AT('na').'</em></td>';
		}
	}			

	echo '</tr>';

}
if (!$count) {
	echo '<tr><td colspan="7"><i>'._AT('no_tests').'</i></td></tr>';
}
echo '</tbody></table>';

echo '<br />';
?>
<h4><?php echo _AT('completed_tests'); ?></h4><br />
<?php

	$sql	= "SELECT T.*, R.* FROM ".TABLE_PREFIX."tests T, ".TABLE_PREFIX."tests_results R, ".TABLE_PREFIX."tests_questions_assoc Q WHERE Q.test_id=T.test_id AND R.member_id=$_SESSION[member_id] AND R.test_id=T.test_id AND T.course_id=$_SESSION[course_id] GROUP BY R.result_id ORDER BY R.date_taken";

	$result	= mysql_query($sql, $db);
	$num_results = mysql_num_rows($result);

	if ($row = mysql_fetch_assoc($result)) {
		$this_course_id=0;

		do {
			if ($this_course_id != $row['course_id']) {
				if ($this_course_id > 0) {
					echo '</table><br />';
				}
				echo '<h5>'.$system_courses[$row['course_id']]['title'].'</h5>';
				echo '<table class="data static" summary="" rules="cols">';
				echo '<thead>';
				echo '<tr>';
				echo '<th scope="col">'._AT('title').'</th>';
				echo '<th scope="col">'._AT('date_taken').'</th>';
				echo '<th scope="col">'._AT('mark').'</th>';
				echo '<th scope="col">'._AT('submissions').'</th>';
				echo '</tr>';
				echo '</thead>';
				echo '<tbody>';

				$this_course_id = $row['course_id'];
				$count =0;
			}
			echo '<tr>';
			echo '<td><b>'.AT_print($row['title'], 'tests.title').'</b></td>';
			echo '<td  align="center">'.$row['date_taken'].'</td>';
			echo '<td  align="center">';

			if ($row['out_of'] == 0) {
				echo '<em>'._AT('na').'</em>';
			} elseif ($row['final_score'] == '') {
				echo '<em>'._AT('unmarked').'</em>';
			} else {
				if ($row['random']) {
					echo '<strong>'.$row['final_score'].'</strong>/?';
				} else {
					echo '<strong>'.$row['final_score'].'</strong>/'.$row['out_of'];
				}
			}
			echo '</td>';

			echo '<td align="center">';

			if ( ($row['result_release']==AT_RELEASE_IMMEDIATE) || (($row['final_score'] != '') && ($row['result_release']==AT_RELEASE_MARKED)) ) {
				echo '<a href="tools/view_results.php?tid='.$row['test_id'].SEP.'rid='.$row['result_id'].'">'._AT('view').'</a>';
			} else {
				echo '<em>'._AT('no_results_yet').'</em>';
			}
			
			echo '</td>';
			echo '</tr>';
		} while ($row = mysql_fetch_assoc($result));
		echo '</tbody>';
		echo '</table>';
	} else {
		echo '<i>'._AT('no_results_available').'</i>';
	}

	echo '<br />';

	require(AT_INCLUDE_PATH.'footer.inc.php');
?>