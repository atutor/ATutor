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

authenticate(AT_PRIV_TEST_CREATE);

$_pages['tools/tests/questions.php']['title']    = _AT('questions');
$_pages['tools/tests/questions.php']['parent']   = 'tools/tests/index.php';
$_pages['tools/tests/questions.php']['children'] = array('tools/tests/add_test_questions.php?tid='.$_GET['tid']);

$_pages['tools/tests/add_test_questions.php?tid='.$_GET['tid']]['title']    = _AT('add_questions');
$_pages['tools/tests/add_test_questions.php?tid='.$_GET['tid']]['parent']   = 'tools/tests/questions.php?tid='.$_GET['tid'];

$tid = intval($_REQUEST['tid']);

if (isset($_POST['done'])) {
	header('Location: index.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

if (isset($_POST['submit'])) {
	// check if we own this tid:
	$sql    = "SELECT test_id FROM ".TABLE_PREFIX."tests WHERE test_id=$tid AND course_id=$_SESSION[course_id]";
	$result = mysql_query($sql, $db);
	if ($row = mysql_fetch_assoc($result)) {

		//update the weights
		$total_weight = 0;
		foreach ($_POST['weight'] as $qid => $weight) {
			$weight = $addslashes($weight);
			$sql	= "UPDATE ".TABLE_PREFIX."tests_questions_assoc SET weight=$weight WHERE question_id=$qid AND test_id=".$tid;
			$result	= mysql_query($sql, $db);
			$total_weight += $weight;
		}

		$sql	= "UPDATE ".TABLE_PREFIX."tests SET out_of='$total_weight' WHERE test_id=$tid";
		$result	= mysql_query($sql, $db);
	}
	$total_weight = 0;
	$msg->addFeedback('QUESTION_WEIGHT_UPDATED');
}

$sql	= "SELECT title FROM ".TABLE_PREFIX."tests WHERE test_id=$tid";
$result	= mysql_query($sql, $db);
$row	= mysql_fetch_array($result);
echo '<h3>'._AT('questions_for').' '.AT_print($row['title'], 'tests.title').'</h3>';

$sql	= "SELECT count(*) as cnt FROM ".TABLE_PREFIX."tests_questions_assoc QA, ".TABLE_PREFIX."tests_questions Q WHERE QA.test_id=$tid AND QA.weight=0 AND QA.question_id=Q.question_id AND Q.type<>".AT_TESTS_LIKERT;
$result	= mysql_query($sql, $db);
$row = mysql_fetch_array($result);
if ($row['cnt']) {
	$msg->printWarnings('QUESTION_WEIGHT');
}

$msg->printAll();

$sql	= "SELECT * FROM ".TABLE_PREFIX."tests_questions Q, ".TABLE_PREFIX."tests_questions_assoc TQ WHERE Q.course_id=$_SESSION[course_id] AND Q.question_id=TQ.question_id AND TQ.test_id=$tid";
$result	= mysql_query($sql, $db);

?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="tid" value="<?php echo $tid; ?>" />
<table class="data static" summary="" rules="cols">
<thead>
<tr>
	<th scope="col"><?php echo _AT('num');      ?></th>
	<th scope="col"><?php echo _AT('weight');   ?></th>
	<th scope="col"><?php echo _AT('question'); ?></th>
	<th scope="col"><?php echo _AT('type');     ?></th>
	<th scope="col"><?php echo _AT('category'); ?></th>
	<th scope="col">&nbsp;</th>
</tr>
</thead>

<?php
if ($row = mysql_fetch_assoc($result)) {
	do {
		$total_weight += $row['weight'];
		$count++;
		echo '<tr>';
		echo '<td class="row1" align="center"><b>'.$count.'</b></td>';
		echo '<td class="row1" align="center">';
		
		if ($row['type'] == 4) {
			echo ''._AT('na').'';
			echo '<input type="hidden" value="0" name="weight['.$row['question_id'].']" />';
		} else {
			echo '<input type="text" value="'.$row['weight'].'" name="weight['.$row['question_id'].']" size="2" class="formfieldR" />';
		}
		echo '</td>';
		echo '<td class="row1">';
		if (strlen($row['question']) > 45) {
			echo AT_print(substr($row['question'], 0, 43), 'tests_questions.question') . '...';
		} else {
			echo AT_print(htmlspecialchars($row['question']), 'tests_questions.question');
		}
		echo '</td>';
		echo '<td nowrap="nowrap">';
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
				
		echo '</td>';
		
		$sql	= "SELECT title FROM ".TABLE_PREFIX."tests_questions_categories WHERE category_id=".$row['category_id']." AND course_id=".$_SESSION['course_id'];
		$cat_result	= mysql_query($sql, $db);

		if ($cat = mysql_fetch_array($cat_result)) {
			echo '<td align="center">'.$cat['title'].'</td>';
		} else {
			echo '<td align="center">'._AT('na').'</td>';
		}

		echo '<td nowrap="nowrap">';
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
		echo '<a href="tools/tests/question_remove.php?tid=' . $tid . SEP . 'qid=' . $row['question_id'] . '">' . _AT('remove') . '</a>';
		//echo '<a href="tools/tests/preview_question.php?qid='.$row['question_id'].'">'._AT('preview').'</a>';
		echo '</td>';

		echo '</tr>';
	} while ($row = mysql_fetch_assoc($result));

	//total weight
	echo '<tfoot>';
	echo '<tr>';
	echo '<td colspan="2" align="center" nowrap="nowrap"><strong>'._AT('total').':</strong> '.$total_weight.'</td>';
	echo '<td colspan="4" align="left" nowrap="nowrap">';
	echo '<input type="submit" value="'._AT('update').'" name="submit" /> <input type="submit"  value="'._AT('done').'" name="done" /></td>';
	echo '</tr>';
	echo '</tfoot>';
} else {
	echo '<tr><td colspan="6" class="row1"><i>'._AT('no_questions_avail').'</i></td></tr>';
}

echo '</table><br /></form>';

require(AT_INCLUDE_PATH.'footer.inc.php');
?>