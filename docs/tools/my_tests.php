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
require(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/index.php';
$_section[1][0] = _AT('my_tests');

require(AT_INCLUDE_PATH.'header.inc.php');

echo '<h2>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '<a href="tools/index.php?g=11"><img src="images/icons/default/square-large-tools.gif"  class="menuimageh2" width="42" border="0" vspace="2" height="40" alt="" /></a>';
}
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
	echo ' <a href="tools/index.php?g=11">'._AT('tools').'</a>';
}
echo '</h2>';

echo '<h3>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '&nbsp;<img src="images/icons/default/my-tests-large.gif" vspace="2"  class="menuimageh3" width="42" height="38" alt="" /> ';
}
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
	echo _AT('my_tests');
}
echo '</h3>';

global $savant;
$msg =& new Message($savant);
$msg->printAll();

$sql	= "SELECT T.*, UNIX_TIMESTAMP(T.start_date) AS us, UNIX_TIMESTAMP(T.end_date) AS ue, SUM(Q.weight) AS outof, COUNT(Q.weight) AS numquestions FROM ".TABLE_PREFIX."tests T, ".TABLE_PREFIX."tests_questions Q WHERE Q.test_id=T.test_id AND T.course_id=$_SESSION[course_id] GROUP BY T.test_id ORDER BY T.start_date, T.title";
$result	= mysql_query($sql, $db);
$num_tests = mysql_num_rows($result);

echo '<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary=""  width="90%" align="center">';
echo '<tr>';
echo '<th scope="col"><small>'._AT('title').'</small></th>';
echo '<th scope="col"><small>'._AT('status').'</small></th>';
echo '<th scope="col"><small>'._AT('start_date').'</small></th>';
echo '<th scope="col"><small>'._AT('end_date').'</small></th>';
echo '<th scope="col"><small>'._AT('attempts').'</small></th>';
echo '<th scope="col"><small>'._AT('questions').'</small></th>';
echo '<th scope="col"><small>'._AT('out_of').'</small></th>';
echo '</tr>';

if ($row = mysql_fetch_assoc($result)) {
	do {
		$count++;
		echo '<tr>';

		echo '<td class="row1">';
		$sql		= "SELECT COUNT(test_id) AS cnt FROM ".TABLE_PREFIX."tests_results WHERE test_id=".$row['test_id']." AND member_id=".$_SESSION['member_id'];
		$takes_result= mysql_query($sql, $db);
		$takes = mysql_fetch_assoc($takes_result);
		if ( ($row['us'] <= time() && $row['ue'] >= time()) && 
		   ( ($row['num_takes'] == AT_TESTS_TAKE_UNLIMITED) || ($takes['cnt'] < $row['num_takes']) )  ) {
			echo '<small><strong><a href="tools/take_test.php?tid='.$row['test_id'].'">'.AT_print($row['title'], 'tests.title').'</a></strong>';
		} else {
			echo '<small class="bigspacer">'.AT_print($row['title'], 'tests.title').'';
		}
		echo '</small></td><td class="row1" align="center"><small>';
		if ( ($row['us'] <= time()) && ($row['ue'] >= time() ) ) {
			echo '<i><b>'._AT('ongoing').'</b></i>';
		} else if ($row['ue'] < time() ) {
			echo '<i>'._AT('expired').'</i>';
		} else if ($row['us'] > time() ) {
			echo '<i>'._AT('pending').'</i>';
		}
		echo '</small></td>';
		echo '<td class="row1" align="center"><small>'.substr($row['start_date'], 0, -3).'</small></td>';
		echo '<td class="row1" align="center"><small>'.substr($row['end_date'], 0, -3).'</small></td>';

		if ($row['num_takes'] == AT_TESTS_TAKE_UNLIMITED) {
			echo '<td class="row1" align="center"><small>'.$takes['cnt'].'/'._AT('unlimited').'</small></td>';
		} else  {
			echo '<td class="row1" align="center"><small>'.$takes['cnt'].'/'.$row['num_takes'].'</small></td>';
		}

		if ($row['random']) {
			echo '<td class="row1" align="center"><small>'.$row['num_questions'].'</small></td>';
			echo '<td class="row1" align="center"><small>-</small></td>';
		} else {
			echo '<td class="row1" align="center"><small>'.$row['numquestions'].'</small></td>';
			if ($row['outof'] > 0) {
				echo '<td class="row1" align="center"><small>'.$row['outof'].'</small></td>';
			} else {
				echo '<td class="row1" align="center"><small><em>'._AT('na').'</em></small></td>';
			}
		}			

		echo '</tr>';

		if ($count < $num_tests) {
			echo '<tr><td height="1" class="row2" colspan="9"></td></tr>';
		}
	} while ($row = mysql_fetch_assoc($result));
} else {
	echo '<tr><td colspan="9" class="row1"><small><i>'._AT('no_tests').'</i></small></td></tr>';
}

echo '</table>';
echo '<br />';
?>
<h3><?php echo _AT('completed_tests'); ?></h3>
<?php

	$sql	= "SELECT T.random, T.automark, T.title, T.course_id, R.*, SUM(Q.weight) AS outof FROM ".TABLE_PREFIX."tests T, ".TABLE_PREFIX."tests_results R, ".TABLE_PREFIX."tests_questions Q WHERE Q.test_id=T.test_id AND R.member_id=$_SESSION[member_id] AND R.test_id=T.test_id AND T.course_id=$_SESSION[course_id] GROUP BY R.result_id ORDER BY R.date_taken";
	$result	= mysql_query($sql, $db);
	$num_results = mysql_num_rows($result);

	if ($row = mysql_fetch_assoc($result)) {
		$this_course_id=0;

		do {
			if ($this_course_id != $row['course_id']) {
				if ($this_course_id > 0) {
					echo '</table><br />';
				}
				echo '<h4>'.$system_courses[$row['course_id']]['title'].'</h4>';
				echo '<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" width="90%" align="center">';
				echo '<tr>';
				echo '<th scope="col"><small>'._AT('title').'</small></th>';
				echo '<th scope="col"><small>'._AT('date_taken').'</small></th>';
				echo '<th scope="col"><small>'._AT('mark').'</small></th>';
				echo '<th scope="col"><small>'._AT('submissions').'</small></th>';
				// echo '<th scope="col"><small>'._AT('delete').'</small></th>';
				echo '</tr>';

				$this_course_id = $row['course_id'];
				$count =0;
			}

			if ($count > 0){
				echo '<tr><td height="1" class="row2" colspan="5"></td></tr>';
			}

			$count++;
			echo '<tr>';
			echo '<td class="row1"><small><b>'.AT_print($row['title'], 'tests.title').'</b></small></td>';
			echo '<td class="row1"  align="center"><small>'.$row['date_taken'].'</small></td>';
			echo '<td class="row1"  align="center"><small>';
			if ($row['outof'] == 0) {
				echo '<em>'._AT('na').'</em>';
			} elseif ($row['final_score'] == '') {
				echo '<em>'._AT('unmarked').'</em>';
			} else {
				if ($row['random']) {
					echo '<strong>'.$row['final_score'];
				} else {
					echo '<strong>'.$row['final_score'].'</strong>/'.$row['outof'];
				}
			}
			echo '</small></td>';

			echo '<td class="row1" align="center"><small>';

			if ($row['final_score'] != '') {
				echo '<a href="tools/view_results.php?tid='.$row['test_id'].SEP.'rid='.$row['result_id'].'">'._AT('view').'</a>';
			} else {
				echo '<em>'._AT('no_results_yet').'</em>';
			}
			
			/* // Delete col removed.
			if ($row['automark'] == AT_MARK_SELF) {
				echo '<td class="row1" align="center"><small><a href="tools/tests/delete_result.php?tid='.$row['test_id'].SEP.'rid='.$row['result_id'].SEP.'tt=Automatic'.SEP.'auto=1">'._AT('delete').'</a></small></td>';
			}
			else {
				echo '<td class="row1" align="center"><small>-</small></td>';
			}
			**/

			echo '</small></td>';
			echo '</tr>';
		} while ($row = mysql_fetch_assoc($result));
		echo '</table>';
	} else {
		echo '<i>'._AT('no_results_available').'</i>';
	}

	echo '<br />';

	require(AT_INCLUDE_PATH.'footer.inc.php');
?>