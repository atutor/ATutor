<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'../mods/_standard/tests/lib/test_result_functions.inc.php');

require(AT_INCLUDE_PATH.'header.inc.php');

$sql	= "SELECT T.*, UNIX_TIMESTAMP(T.start_date) AS us, UNIX_TIMESTAMP(T.end_date) AS ue, COUNT(Q.weight) AS numquestions FROM %stests T, %stests_questions_assoc Q WHERE Q.test_id=T.test_id AND T.course_id=%d GROUP BY T.test_id ORDER BY T.start_date, T.title";
$rows_tests	= queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, $_SESSION['course_id']));

?>
<table class="data static" summary="">
<thead>
<tr>
	<th scope="col"><?php echo _AT('title');      ?></th>
	<th scope="col"><?php echo _AT('status');     ?></th>
	<th scope="col"><?php echo _AT('start_date'); ?></th>
	<th scope="col" class="hidecol480"><?php echo _AT('end_date');   ?></th>
	<th scope="col" class="hidecol480"><?php echo _AT('attempts');   ?></th>
</tr>
</thead>
<tbody>

<?php
$count = 0;
foreach($rows_tests as $row){
	// this code hides tests from the user if they are not enrolled.
	if (!$row['guests'] && !authenticate_test($row['test_id'])) {
		continue;
	}

	$count++;
	echo '<tr>';
	echo '<td>';

	$sql = "SELECT COUNT(test_id) AS cnt FROM %stests_results WHERE status=1 AND test_id=%d AND member_id=%d";
	$takes= queryDB($sql, array(TABLE_PREFIX, $row['test_id'], $_SESSION['member_id']), TRUE);

	if ( ($row['us'] <= time() && $row['ue'] >= time()) && 
	   ( ($row['num_takes'] == AT_TESTS_TAKE_UNLIMITED) || ($takes['cnt'] < $row['num_takes']) )  ) {
		//echo '<strong><a href="'.url_rewrite('mods/_standard/tests/test_intro.php?tid='.$row['test_id']).'">'.AT_print($row['title'], 'tests.title').'</a></strong>';
		echo '<strong><a href="mods/_standard/tests/test_intro.php?tid='.$row['test_id'].'">'.AT_print($row['title'], 'tests.title').'</a></strong>';
	} else {
		echo '<small class="bigspacer">'.AT_print($row['title'], 'tests.title').'';
	}
	echo '</td><td>';
	if ( ($row['us'] <= time()) && ($row['ue'] >= time() ) ) {
		echo '<strong>'._AT('ongoing').'</strong>';
	} else if ($row['ue'] < time() ) {
		echo '<strong>'._AT('expired').'</strong>';
	} else if ($row['us'] > time() ) {
		echo '<strong>'._AT('pending').'</strong>';
	}
	
	$startend_date_long_format=_AT('startend_date_long_format');
	echo '</td>';

	echo '<td>'.AT_date($startend_date_long_format, $row['start_date']).'</td>';
	echo '<td class="hidecol480">'.AT_date($startend_date_long_format, $row['end_date']).'</td>';

	if ($row['num_takes'] == AT_TESTS_TAKE_UNLIMITED) {
		echo '<td class="hidecol480">'.$takes['cnt'].'/'._AT('unlimited').'</td>';
	} else  {
		echo '<td class="hidecol480">'.$takes['cnt'].'/'.$row['num_takes'].'</td>';
	}

/*
	if ($row['random']) {
		echo '<td>'.$row['num_questions'].'</td>';
		echo '<td>'.$row['out_of'].'</td>';
	} else {
		echo '<td>'.$row['numquestions'].'</td>';
		if ($row['out_of'] > 0) {
			echo '<td>'.$row['out_of'].'</td>';
		} else {
			echo '<td><em>'._AT('na').'</em></td>';
		}
	}			
*/
	echo '</tr>';

}
if (!$count) {
	echo '<tr><td colspan="7">'._AT('none_found').'</td></tr>';
}
?>
	</tbody>
</table>
<br />

<?php if (!$_SESSION['enroll']): ?>
	<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>
	<?php exit; ?>
<?php endif; ?>
<h4><?php echo _AT('completed_tests'); ?></h4><br />
<table class="data static" summary="">
<thead>
<tr>
	<th scope="col"><?php echo _AT('title');      ?></th>
	<th scope="col"><?php echo _AT('date_taken'); ?></th>
	<th scope="col" class="hidecol480"><?php echo _AT('time_spent'); ?></th>
	<th scope="col"><?php echo _AT('mark');       ?></th>
	<th scope="col" class="hidecol480"><?php echo _AT('submission'); ?></th>
</tr>
</thead>
<tbody>
<?php

$sql	= "SELECT T.*, R.*, (UNIX_TIMESTAMP(R.end_time) - UNIX_TIMESTAMP(R.date_taken)) AS diff FROM %stests T, %stests_results R, %stests_questions_assoc Q WHERE R.status=1 AND Q.test_id=T.test_id AND R.member_id=%d AND R.test_id=T.test_id AND T.course_id=%d GROUP BY R.result_id ORDER BY R.date_taken DESC";
$rows_results	= queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, TABLE_PREFIX, $_SESSION['member_id'], $_SESSION['course_id']));
$num_results = count($rows_results);

if($num_results > 0){
	$this_course_id=0;
    foreach($rows_results as $row){
		echo '<tr>';
		echo '<td><strong>'.AT_print($row['title'], 'tests.title').'</strong></td>';
		echo '<td>'.AT_date($startend_date_long_format, $row['date_taken']).'</td>';
		echo '<td class="hidecol480">'.get_human_time($row['diff']).'</td>';
		echo '<td>';

		if ($row['out_of'] == 0) {
			echo _AT('na');
		} elseif ($row['final_score'] == '') {
			echo _AT('unmarked');
		} elseif (($row['final_score'] != '') && ($row['result_release']==AT_RELEASE_NEVER)) {
			echo _AT('unreleased');
		} else {
			if ($row['random']) {
				$out_of = get_random_outof($row['test_id'], $row['result_id']);
			} else {
				$out_of = $row['out_of'];
			}

			echo '<strong>'.$row['final_score'].'</strong>/'.$out_of;
		}
		echo '</td>';

		echo '<td class="hidecol480">';

		if ( ($row['result_release']==AT_RELEASE_IMMEDIATE) || (($row['final_score'] != '') && ($row['result_release']==AT_RELEASE_MARKED)) ) {
			echo '<a href="mods/_standard/tests/view_results.php?tid='.$row['test_id'].SEP.'rid='.$row['result_id'].'">'._AT('view_results').'</a>';
		} else {
			echo '<strong>'._AT('no_results_yet').'</strong>';
		}
		
		echo '</td>';
		echo '</tr>';
	} 
} else {
	echo '<tr><td colspan="4">'._AT('none_found').'</td></tr>';
}
?>
</tbody>
</table>

<?php 
	$_SESSION['last_visited_page'] = $server_protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	require(AT_INCLUDE_PATH.'footer.inc.php'); 
?>
