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
$count = 10;
while ($row = mysql_fetch_assoc($result)) {
	// this code hides tests from the user if they are not enrolled.
	if (!$row['guests'] && !authenticate_test($row['test_id'])) {
		continue;
	}

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
	echo '</td><td>';
	if ( ($row['us'] <= time()) && ($row['ue'] >= time() ) ) {
		echo '<i><b>'._AT('ongoing').'</b></i>';
	} else if ($row['ue'] < time() ) {
		echo '<i>'._AT('expired').'</i>';
	} else if ($row['us'] > time() ) {
		echo '<i>'._AT('pending').'</i>';
	}
	echo '</td>';
	echo '<td>'.substr($row['start_date'], 0, -3).'</td>';
	echo '<td>'.substr($row['end_date'], 0, -3).'</td>';

	if ($row['num_takes'] == AT_TESTS_TAKE_UNLIMITED) {
		echo '<td>'.$takes['cnt'].'/'._AT('unlimited').'</td>';
	} else  {
		echo '<td>'.$takes['cnt'].'/'.$row['num_takes'].'</td>';
	}

	if ($row['random']) {
		echo '<td>'.$row['num_questions'].'</td>';
		echo '<td>-</td>';
	} else {
		echo '<td>'.$row['numquestions'].'</td>';
		if ($row['out_of'] > 0) {
			echo '<td>'.$row['out_of'].'</td>';
		} else {
			echo '<td><em>'._AT('na').'</em></td>';
		}
	}			

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
<table class="data static" summary="" rules="cols">
<thead>
<tr>
	<th scope="col"><?php echo _AT('title');      ?></th>
	<th scope="col"><?php echo _AT('date_taken'); ?></th>
	<th scope="col"><?php echo _AT('mark');       ?></th>
	<th scope="col"><?php echo _AT('submission'); ?></th>
</tr>
</thead>
<tbody>
<?php
$sql	= "SELECT T.*, R.* FROM ".TABLE_PREFIX."tests T, ".TABLE_PREFIX."tests_results R, ".TABLE_PREFIX."tests_questions_assoc Q WHERE Q.test_id=T.test_id AND R.member_id=$_SESSION[member_id] AND R.test_id=T.test_id AND T.course_id=$_SESSION[course_id] GROUP BY R.result_id ORDER BY R.date_taken DESC";

$result	= mysql_query($sql, $db);
$num_results = mysql_num_rows($result);

if ($row = mysql_fetch_assoc($result)) {
	$this_course_id=0;

	do {
		echo '<tr>';
		echo '<td><strong>'.AT_print($row['title'], 'tests.title').'</strong></td>';
		echo '<td>'.$row['date_taken'].'</td>';
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

		echo '<td>';

		if ( ($row['result_release']==AT_RELEASE_IMMEDIATE) || (($row['final_score'] != '') && ($row['result_release']==AT_RELEASE_MARKED)) ) {
			echo '<a href="tools/view_results.php?tid='.$row['test_id'].SEP.'rid='.$row['result_id'].'">'._AT('view_results').'</a>';
		} else {
			echo '<em>'._AT('no_results_yet').'</em>';
		}
		
		echo '</td>';
		echo '</tr>';
	} while ($row = mysql_fetch_assoc($result));
} else {
	echo '<tr><td colspan="4">'._AT('none_found').'</td></tr>';
}
?>
</tbody>
</table>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>