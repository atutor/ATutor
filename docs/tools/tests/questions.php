<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
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

authenticate(AT_PRIV_TESTS);

$_pages['tools/tests/questions.php']['title_var']    = 'questions';
$_pages['tools/tests/questions.php']['parent']   = 'tools/tests/index.php';
$_pages['tools/tests/questions.php']['children'] = array('tools/tests/add_test_questions.php?tid='.$_GET['tid']);

$_pages['tools/tests/add_test_questions.php?tid='.$_GET['tid']]['title_var']    = 'add_questions';
$_pages['tools/tests/add_test_questions.php?tid='.$_GET['tid']]['parent']   = 'tools/tests/questions.php?tid='.$_GET['tid'];

$_pages['tools/tests/questions.php']['guide']    = 'instructor/?p=15.6.add_questions.php';


$tid = intval($_REQUEST['tid']);

if (isset($_POST['submit'])) {
	// check if we own this tid:
	$sql    = "SELECT test_id FROM ".TABLE_PREFIX."tests WHERE test_id=$tid AND course_id=$_SESSION[course_id]";
	$result = mysql_query($sql, $db);
	if ($row = mysql_fetch_assoc($result)) {
		/*
		// For #1760
		//check that randomized questions are all same weight.
		$randomized_question_weight = -1;
		foreach ($_POST['weight'] as $qid => $weight) {
			$weight = $addslashes($weight);
			if ($_POST['required'][$qid]) {
				// do nothing
			} else {
				if ($randomized_question_weight == -1) {
					// if first time through this loop.
					$randomized_question_weight = $weight;
				} else if ($randomized_question_weight != $weight) {
					// The values of two non-required questions are not equal.
					$msg->addError ("NON_REQUIRED_QUESTION_WEIGHT");
					header('Location: '.$_SERVER['PHP_SELF'] .'?tid='.$tid);
					exit;
				} else {
					// The values of non-required questions are equal so far.
				}
			}
		}
		*/

		//update the weights & order
		$total_weight = 0;
		foreach ($_POST['weight'] as $qid => $weight) {
			$weight = $addslashes($weight);
			if ($_POST['required'][$qid]) {
				$required = 1;
			} else {
				$required = 0;
			}
			
			$orders = $_POST['ordering'];
			$orders = array_keys($orders);
			$orders = array_flip($orders);

			$sql	= "UPDATE ".TABLE_PREFIX."tests_questions_assoc SET weight=$weight, required=$required, ordering=".($orders[$qid]+1)." WHERE question_id=$qid AND test_id=".$tid;
			$result	= mysql_query($sql, $db);
			$total_weight += $weight;
		}

		$sql	= "UPDATE ".TABLE_PREFIX."tests SET out_of='$total_weight' WHERE test_id=$tid";
		$result	= mysql_query($sql, $db);
	}
	$total_weight = 0;
	$msg->addFeedback('QUESTION_WEIGHT_UPDATED');
	header('Location: '.$_SERVER['PHP_SELF'] .'?tid='.$tid);
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

$sql	= "SELECT title, random FROM ".TABLE_PREFIX."tests WHERE test_id=$tid";
$result	= mysql_query($sql, $db);
$row	= mysql_fetch_assoc($result);
echo '<h3>'._AT('questions_for').' '.AT_print($row['title'], 'tests.title').'</h3>';
$random = $row['random'];

$sql	= "SELECT count(*) as cnt FROM ".TABLE_PREFIX."tests_questions_assoc QA, ".TABLE_PREFIX."tests_questions Q WHERE QA.test_id=$tid AND QA.weight=0 AND QA.question_id=Q.question_id AND Q.type<>".AT_TESTS_LIKERT;
$result	= mysql_query($sql, $db);
$row = mysql_fetch_array($result);
if ($row['cnt']) {
	$msg->printWarnings('QUESTION_WEIGHT');
}

$msg->printAll();

$sql	= "SELECT * FROM ".TABLE_PREFIX."tests_questions Q, ".TABLE_PREFIX."tests_questions_assoc TQ WHERE Q.course_id=$_SESSION[course_id] AND Q.question_id=TQ.question_id AND TQ.test_id=$tid ORDER BY TQ.ordering";
$result	= mysql_query($sql, $db);

?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="tid" value="<?php echo $tid; ?>" />
<table class="data static" summary="" rules="cols">
<thead>
<tr>
	<th scope="col"><?php echo _AT('num');      ?></th>
	<th scope="col"><?php echo _AT('weight');   ?></th>
	<th scope="col"><?php echo _AT('order'); ?></th>
	<th scope="col"><?php echo _AT('question'); ?></th>
	<th scope="col"><?php echo _AT('type');     ?></th>
	<th scope="col"><?php echo _AT('category'); ?></th>
	<?php if ($random): ?>
		<th scope="col"><?php echo _AT('required'); ?></th>
	<?php endif; ?>
	<th scope="col">&nbsp;</th>
</tr>
</thead>
<?php
if ($row = mysql_fetch_assoc($result)) {
	$sql	= "SELECT title, category_id FROM ".TABLE_PREFIX."tests_questions_categories WHERE course_id=".$_SESSION['course_id'];
	$cat_result	= mysql_query($sql, $db);
	$cats    = array();
	$cats[0] = _AT('cats_uncategorized');
	while ($cat_row = mysql_fetch_assoc($cat_result)) {
		$cats[$cat_row['category_id']] = $cat_row['title'];
	}

	do {
		$total_weight += $row['weight'];
		$count++;
		echo '<tr>';
		echo '<td class="row1" align="center"><strong>'.$count.'</strong></td>';
		echo '<td class="row1" align="center">';
		
		if ($row['type'] == AT_TESTS_LIKERT) {
			echo ''._AT('na').'';
			echo '<input type="hidden" value="0" name="weight['.$row['question_id'].']" />';
		} else {
			echo '<input type="text" value="'.$row['weight'].'" name="weight['.$row['question_id'].']" size="2" />';
		}
		echo '</td>';

		echo '<td class="row1" align="center"><input type="text" name="ordering['.$row['question_id'].']" value="'.$row['ordering'].'" size="2" /></td>';

		echo '<td class="row1">';
		if (strlen($row['question']) > 45) {
			echo htmlspecialchars(AT_print(substr($row['question'], 0, 43), 'tests_questions.question')) . '...';
		} else {
			echo AT_print(htmlspecialchars($row['question']), 'tests_questions.question');
		}

		echo '</td>';
		echo '<td nowrap="nowrap">';
		$link = '';
		switch ($row['type']) {
			case AT_TESTS_MC:
				echo _AT('test_mc');
				$link = 'tools/tests/edit_question_multi.php?tid='.$tid.SEP.'qid='.$row['question_id'];
				break;
			case AT_TESTS_TF:
				echo _AT('test_tf');
				$link = 'tools/tests/edit_question_tf.php?tid='.$tid.SEP.'qid='.$row['question_id'];
				break;
			case AT_TESTS_LONG:
				echo _AT('test_open');
				$link = 'tools/tests/edit_question_long.php?tid='.$tid.SEP.'qid='.$row['question_id'];
				break;
			case AT_TESTS_LIKERT:
				echo _AT('test_lk');
				$link = 'tools/tests/edit_question_likert.php?tid='.$tid.SEP.'qid='.$row['question_id'];
				break;
		}
		echo '</td>';
		
		echo '<td align="center">'.$cats[$row['category_id']].'</td>';

		if ($random) {
			echo '<td align="center" nowrap="nowrap"><input type="checkbox" name="required['.$row['question_id'].']" value="1"';
			if ($row['required']) {
				echo ' checked="checked"';
			}
			echo ' id="q'.$row['question_id'].'" /><label for="q'.$row['question_id'].'">'._AT('required').'</label></td>';
		}

		echo '<td nowrap="nowrap">';
		echo '<a href="' . $link . '">' . _AT('edit').'</a> | ';
		echo '<a href="tools/tests/question_remove.php?tid=' . $tid . SEP . 'qid=' . $row['question_id'] . '">' . _AT('remove') . '</a>';
		echo '</td>';

		echo '</tr>';
	} while ($row = mysql_fetch_assoc($result));

	//total weight
	echo '<tfoot>';
	echo '<tr><td>&nbsp;</td>';
	echo '<td align="center" nowrap="nowrap"><strong>'._AT('total').':</strong> '.$total_weight.'</td>';
	echo '<td colspan="';
	if ($random) {
		echo 5;
	} else {
		echo 4;
	}

	echo '" align="left" nowrap="nowrap">';
	echo '<input type="submit" value="'._AT('update').'" name="submit" /> </td>';
	echo '</tr>';
	echo '</tfoot>';
} else {
	echo '<tr><td colspan="';
	if ($random) {
		echo 7;
	} else {
		echo 6;
	}

	echo '" >'._AT('none_found').'</td></tr>';
}

echo '</table><br /></form>';

require(AT_INCLUDE_PATH.'footer.inc.php');
?>