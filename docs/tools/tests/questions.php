<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
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

if(isset($_GET['tid'])) {
	$tid = intval($_GET['tid']);
} else {
	$tid = intval($_POST['tid']);
}

if (isset($_POST['submit'])) {
	//update the weights
	foreach ($_POST['weight'] as $qid=>$weight) {
		$sql	= "UPDATE ".TABLE_PREFIX."tests_questions_assoc SET weight=$weight WHERE question_id=$qid AND test_id=".$tid;  //tests - course_id too?
		$result	= mysql_query($sql, $db);
	}
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


$sql	= "SELECT title FROM ".TABLE_PREFIX."tests WHERE test_id=$tid";
$result	= mysql_query($sql, $db);
$row	= mysql_fetch_array($result);
echo '<h3>'._AT('questions_for').' '.AT_print($row['title'], 'tests.title').'</h3>';

$msg->printHelps('ADD_QUESTIONS2');
$msg->printAll();

$sql	= "SELECT * FROM ".TABLE_PREFIX."tests_questions Q, ".TABLE_PREFIX."tests_questions_assoc TQ WHERE Q.course_id=$_SESSION[course_id] AND Q.question_id=TQ.question_id AND TQ.test_id=$tid";
$result	= mysql_query($sql, $db);

echo '<p align="center">';
//echo '<a href="tools/tests/index.php">'._AT('tests').'</a> |';
echo '<a href="tools/tests/add_test_questions.php?tid='.$tid.'">'._AT('add_questions!').'</a>';
//echo '| <a href="tools/tests/question_cats.php">'._AT('question_categories').'</a>';
echo '</p>';

echo '</p>';
echo '<form action="'.$_SERVER['PHP_SELF'].'" method="post" name="form">';
echo '<input type="hidden" name="tid" value="'.$tid.'" />';
echo '<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center" width="90%">';
echo '<tr>';
echo '<th scope="col"><small>'._AT('num').'</small></th>';
echo '<th scope="col"><small>'._AT('weight').'</small></th>';
echo '<th scope="col"><small>'._AT('question').'</small></th>';
echo '<th scope="col"><small>'._AT('type').'</small></th>';
echo '<th scope="col"></th>';
$num_cols = 5;
echo '</tr>';

if ($row = mysql_fetch_assoc($result)) {
	do {
		$total_weight += $row['weight'];
		$count++;
		echo '<tr>';
		echo '<td class="row1" align="center"><small><b>'.$count.'</b></small></td>';
		echo '<td class="row1" align="center"><input type="text" value="'.$row['weight'].'" name="weight['.$row['question_id'].']" size="2" /></td>';
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
		
		echo '<td class="row1" nowrap="nowrap"><small>';
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

		echo _AT('edit').'</a> | ';
		echo '<a href="tools/tests/question_remove.php?tid='.$tid.SEP.'qid='.$row['question_id'].'">'._AT('remove').'</a></small></td>';

		echo '</tr>';
		if($count != mysql_num_rows($result)) {
			echo '<tr><td height="1" class="row2" colspan="6"></td></tr>';
		}
	} while ($row = mysql_fetch_assoc($result));

	//total weight
	echo '<tr><td height="1" class="row2" colspan="7"></td></tr>';
	echo '<tr><td height="1" class="row2" colspan="7"></td></tr>';
	echo '<tr>';
	echo '<td class="row1" align="right"></td>';
	echo '<td class="row1" align="center"><small><b>'._AT('total').':</b> '.$total_weight.'</small><br />';
	echo '<input type="submit" value="'._AT('update').'" name="submit" class="button" /></td>';
	echo '<td class="row1"></td>';
	echo '<td class="row1"></td>';
	echo '<td class="row1" colspan="2"></td>';
	echo '</tr>';
} else {
	echo '<tr><td colspan="6" class="row1"><small><i>'._AT('no_questions_avail').'</i></small></td></tr>';
}

echo '</table><br /></form>';

require(AT_INCLUDE_PATH.'footer.inc.php');
?>