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
	$_section[1][1] = 'tools/tests/';
	$_section[2][0] = _AT('questions');

	require(AT_INCLUDE_PATH.'header.inc.php');

	$_GET['tt'] = urldecode($_GET['tt']);
	$tt = $_GET['tt'];
	
	if($tt == ''){
		$tt = urldecode($_POST['tt']);
	}

	if($_GET['tid']){
		$tid = intval($_GET['tid']);
	}else{
		$tid = intval($_POST['tid']);
	}

	echo '<h2>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '<a href="tools/" class="hide"><img src="images/icons/default/square-large-tools.gif"  class="menuimageh2" border="0" vspace="2" width="42" height="40" alt="" /></a>';
	}
	echo ' <a href="tools/" class="hide">'._AT('tools').'</a>';
	echo '</h2>';

	echo '<h3>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '&nbsp;<img src="images/icons/default/test-manager-large.gif"  class="menuimageh3" width="42" height="38" alt="" /> ';
	}
	echo '<a href="tools/tests/">'._AT('test_manager').'</a>';
	echo '</h3>';

	echo '<h3>'._AT('questions_for').' '.$tt.'</h3>';

	$help[] = AT_HELP_ADD_QUESTIONS2;
	print_help($help);
	
	echo '<h4>'._AT('add_questions').'</h4>';
	/* avman */
	$sql = "SELECT automark FROM ".TABLE_PREFIX."tests WHERE test_id=$tid";
	$result	= mysql_query($sql, $db);
	$automatic_test = mysql_fetch_array($result);
	if ($automatic_test[0] == AT_MARK_SELF || $automatic_test[0] == AT_MARK_SELF_UNCOUNTED) {
		echo '<p><a href="tools/tests/add_question_multi.php?tid='.$tid.SEP.'tt='.urlencode($_GET['tt']).'">'._AT('add_mc_questions').'</a><br />';
		echo '<a href="tools/tests/add_question_tf.php?tid='.$tid.SEP.'tt='.urlencode($_GET['tt']).'">'._AT('add_tf_questions').'</a><br />';
	}
	else {
		echo '<p><a href="tools/tests/add_question_multi.php?tid='.$tid.SEP.'tt='.urlencode($_GET['tt']).'">'._AT('add_mc_questions').'</a><br />';
		echo '<a href="tools/tests/add_question_tf.php?tid='.$tid.SEP.'tt='.urlencode($_GET['tt']).'">'._AT('add_tf_questions').'</a><br />';
		echo '<a href="tools/tests/add_question_long.php?tid='.$tid.SEP.'tt='.urlencode($_GET['tt']).'">'._AT('add_open_questions').'</a><br />';
		echo '<a href="tools/tests/add_question_likert.php?tid='.$tid.SEP.'tt='.urlencode($_GET['tt']).'">'._AT('add_likert_questions').'</a></p>';
	}
	echo '<br />';
	echo '<br />';

	$sql	= "SELECT * FROM ".TABLE_PREFIX."tests_questions WHERE course_id=$_SESSION[course_id] AND test_id=$tid ORDER BY ordering, question_id";
	$result	= mysql_query($sql, $db);
	$num_qs = mysql_num_rows($result);
	if($num_qs){
		echo '<p>(<a href="tools/tests/preview.php?tid='.$tid.SEP.'tt='.$_GET['tt'].'">'._AT('preview_test').'</a>)</p>';
	}
	echo '<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center" width="90%">';
	echo '<tr>';
	echo '<th scope="col"><small>'._AT('num').'</small></th>';
	echo '<th scope="col"><small>'._AT('question').'</small></th>';
	echo '<th scope="col"><small>'._AT('type').'</small></th>';
	echo '<th scope="col"><small>'._AT('weight').'</small></th>';
	echo '<th scope="col"><small>'._AT('required').'</small></th>';
	echo '<th scope="col"><small>'._AT('edit').'</small></th>';
	echo '<th scope="col"><small>'._AT('delete').'</small></th>';
	echo '</tr>';

	if ($row = mysql_fetch_array($result)) {
		do {
			$total_weight += $row['weight'];
			$count++;
			echo '<tr>';
			echo '<td class="row1" align="center"><small><b>'.$count.'</b></small></td>';
			echo '<td class="row1"><small>';
			if (strlen($row['question']) > 45) {
				echo AT_print(substr($row['question'], 0, 43), 'tests_questions.question') . '...';

			} else {
				echo $row['question'];
			}
			echo '</small></td>';
			echo '<td class="row1"><small>';
			switch ($row['type']) {
				case 1:
					echo _AT('test_mc');
					break;
				
				case 2:
					echo _AT('test_tf');
					break;
			
				case 3:
					echo _AT('test_open');
					break;
				case 4:
					echo _AT('test_lk');
					break;
			}
				
			echo '</small></td>';
			echo '<td class="row1" align="center"><small>'.$row['weight'].'</small></td>';
			echo '<td class="row1" align="center"><small>';
			switch ($row['required']) {
				case 0:
					echo _AT('no1');
					break;
				
				case 1:
					echo _AT('yes1');
					break;
			}
				
			echo '</small></td>';
			echo '<td class="row1"><small>';
			
			switch ($row['type']) {
				case 1:
					echo '<a href="tools/tests/edit_question_multi.php?tid='.$tid.SEP.'qid='.$row['question_id'].SEP.'tt='.$_GET['tt'].'">';
					break;
				
				case 2:
					echo '<a href="tools/tests/edit_question_tf.php?tid='.$tid.SEP.'qid='.$row['question_id'].SEP.'tt='.$_GET['tt'].'">';
					break;
			
				case 3:
					echo '<a href="tools/tests/edit_question_long.php?tid='.$tid.SEP.'qid='.$row['question_id'].SEP.'tt='.$_GET['tt'].'">';
					break;
				case 4:
					echo '<a href="tools/tests/edit_question_likert.php?tid='.$tid.SEP.'qid='.$row['question_id'].SEP.'tt='.$_GET['tt'].'">';
					break;
			}

			echo _AT('edit').'</a></small></td>';
			echo '<td class="row1"><small><a href="tools/tests/delete_question.php?tid='.$tid.SEP.'tt='.$_GET['tt'].SEP.'qid='.$row['question_id'].'">'._AT('delete').'</a></small></td>';
			echo '</tr>';
			
			echo '<tr><td height="1" class="row2" colspan="7"></td></tr>';
		} while ($row = mysql_fetch_array($result));
		echo '<tr><td height="1" class="row2" colspan="7"></td></tr>';
		echo '<tr>';
		echo '<td class="row1"></td>';
		echo '<td class="row1"></td>';
		echo '<td class="row1" align="right"><small><b>'._AT('total').':</b></small></td>';
		echo '<td class="row1" align="center"><small>'.$total_weight.'</small></td>';
		echo '<td class="row1"></td>';
		echo '<td class="row1"></td>';
		echo '<td class="row1"></td>';
		echo '</tr>';
	} else {
		echo '<tr><td colspan="7" class="row1"><small><i>'._AT('no_questions_avail').'</i></small></td></tr>';
	}

	echo '</table><br />';

	require(AT_INCLUDE_PATH.'footer.inc.php');
?>
