<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

	$page = 'tests';
	define('AT_INCLUDE_PATH', '../../include/');
	require(AT_INCLUDE_PATH.'vitals.inc.php');
	require(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

	global $savant;
	$msg =& new Message($savant);
	
	$_section[0][0] = _AT('tools');
	$_section[0][1] = 'tools/index.php';
	$_section[1][0] = _AT('test_manager');
	$_section[1][1] = 'tools/tests/index.php';
	$_section[2][0] = _AT('questions');

	require(AT_INCLUDE_PATH.'header.inc.php');

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

	$msg->printHelps('ADD_QUESTIONS2');
	
	echo '<h4>'._AT('add_questions').'</h4>';
	/* avman */
	$sql		= "SELECT automark, title FROM ".TABLE_PREFIX."tests WHERE test_id=$tid";
	$result		= mysql_query($sql, $db);
	$row		= mysql_fetch_array($result);
	$automark	= $row['automark'];
	echo '<h3>'._AT('questions_for').' '.AT_print($row['title'], 'tests.title').'</h3>';

	$msg->printAll();

	if ($automark == AT_MARK_SELF) {
		unset($editors);
		$editors[] = array('priv' => AT_PRIV_TEST_CREATE, 'title' => _AT('add_mc_questions'), 'url' => 'tools/tests/add_question_multi.php?tid='.$tid);
		$editors[] = array('priv' => AT_PRIV_TEST_CREATE, 'title' => _AT('add_tf_questions'), 'url' => 'tools/tests/add_question_tf.php?tid='.$tid);
		$editors[] = array('priv' => AT_PRIV_TEST_CREATE, 'title' => _AT('add_likert_questions'), 'url' => 'tools/tests/add_question_likert.php?tid='.$tid);
		print_editor($editors , $large = false);
	} else {
		unset($editors);
		$editors[] = array('priv' => AT_PRIV_TEST_CREATE, 'title' => _AT('add_mc_questions'), 'url' => 'tools/tests/add_question_multi.php?tid='.$tid);
		$editors[] = array('priv' => AT_PRIV_TEST_CREATE, 'title' => _AT('add_tf_questions'), 'url' => 'tools/tests/add_question_tf.php?tid='.$tid);
		$editors[] = array('priv' => AT_PRIV_TEST_CREATE, 'title' => _AT('add_likert_questions'), 'url' => 'tools/tests/add_question_likert.php?tid='.$tid);
		$editors[] = array('priv' => AT_PRIV_TEST_CREATE, 'title' => _AT('add_open_questions'), 'url' => 'tools/tests/add_question_long.php?tid='.$tid);
		print_editor($editors , $large = false);	
	}
	echo '<br /><br />';

	$sql	= "SELECT * FROM ".TABLE_PREFIX."tests_questions WHERE course_id=$_SESSION[course_id] AND test_id=$tid ORDER BY ordering, question_id";
	$result	= mysql_query($sql, $db);
	$num_qs = mysql_num_rows($result);
	if($num_qs){
		echo '<p>(<a href="tools/tests/preview.php?tid='.$tid.'">'._AT('preview_test').'</a>)</p>';
	}
	echo '<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center" width="90%">';
	echo '<tr>';
	echo '<th scope="col"><small>'._AT('num').'</small></th>';
	echo '<th scope="col"><small>'._AT('question').'</small></th>';
	echo '<th scope="col"><small>'._AT('type').'</small></th>';
	$num_cols = 7;
	if ($automark != AT_MARK_UNMARKED) {
		$num_cols--;
		echo '<th scope="col"><small>'._AT('weight').'</small></th>';
	}
	//echo '<th scope="col"><small>'._AT('required').'</small></th>';
	echo '<th scope="col"><small>'._AT('edit').' &amp; '._AT('delete').'</small></th>';
	echo '</tr>';

if ($row = mysql_fetch_assoc($result)) {
	do {
		$total_weight += $row['weight'];
		$count++;
		echo '<tr>';
		echo '<td class="row1" align="center"><small><b>'.$count.'</b></small></td>';
		echo '<td class="row1"><small>';
		if (strlen($row['question']) > 45) {
			echo AT_print(substr($row['question'], 0, 43), 'tests_questions.question') . '...';
		} else {
			echo AT_print($row['question'], 'tests_questions.question');
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
		
		if ($automark != AT_MARK_UNMARKED) {
			if ($row['type'] == AT_TESTS_LIKERT) {
				$row['weight'] = _AT('na');
			}
			echo '<td class="row1" align="center"><small>'.$row['weight'].'</small></td>';
		}

		echo '<td class="row1"><small>&middot;';
			
		switch ($row['type']) {
			case 1:
				echo '<a href="tools/tests/edit_question_multi.php?tid='.$tid.SEP.'qid='.$row['question_id'].'">';
				break;
				
			case 2:
				echo '<a href="tools/tests/edit_question_tf.php?tid='.$tid.SEP.'qid='.$row['question_id'].'">';
				break;
			
			case 3:
				echo '<a href="tools/tests/edit_question_long.php?tid='.$tid.SEP.'qid='.$row['question_id'].'">';
				break;
			case 4:
				echo '<a href="tools/tests/edit_question_likert.php?tid='.$tid.SEP.'qid='.$row['question_id'].'">';
				break;
		}

		echo _AT('edit').'</a><br />';
		echo '&middot;<a href="tools/tests/delete_question.php?tid='.$tid.SEP.'qid='.$row['question_id'].'">'._AT('delete').'</a></small></td>';
		echo '</tr>';
		if($count != mysql_num_rows($result)) {
			echo '<tr><td height="1" class="row2" colspan="'.$num_cols.'"></td></tr>';
		}
	} while ($row = mysql_fetch_assoc($result));

	if ($total_weight > 0) {
		echo '<tr><td height="1" class="row2" colspan="7"></td></tr>';
		echo '<tr><td height="1" class="row2" colspan="7"></td></tr>';
		echo '<tr>';
		echo '<td class="row1"></td>';
		echo '<td class="row1"></td>';
		echo '<td class="row1" align="right"><small><b>'._AT('total').':</b></small></td>';
		echo '<td class="row1" align="center"><small>'.$total_weight.'</small></td>';
		echo '<td class="row1"></td>';
		echo '</tr>';
	}
} else {
	echo '<tr><td colspan="7" class="row1"><small><i>'._AT('no_questions_avail').'</i></small></td></tr>';
}

echo '</table><br />';

require(AT_INCLUDE_PATH.'footer.inc.php');
?>