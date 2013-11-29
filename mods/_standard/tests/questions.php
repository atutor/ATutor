<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$

$page = 'tests';
define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'../mods/_standard/tests/classes/testQuestions.class.php');

authenticate(AT_PRIV_TESTS);

$_pages['mods/_standard/tests/questions.php']['title_var']    = 'questions';
$_pages['mods/_standard/tests/questions.php']['parent']   = 'mods/_standard/tests/index.php';
$_pages['mods/_standard/tests/questions.php']['children'] = array('mods/_standard/tests/add_test_questions.php?tid='.$_GET['tid']);

$_pages['mods/_standard/tests/add_test_questions.php?tid='.$_GET['tid']]['title_var']    = 'add_questions';
$_pages['mods/_standard/tests/add_test_questions.php?tid='.$_GET['tid']]['parent']   = 'mods/_standard/tests/questions.php?tid='.$_GET['tid'];

$_pages['mods/_standard/tests/questions.php']['guide']    = 'instructor/?p=add_questions.php';

$tid = intval($_REQUEST['tid']);

if (isset($_POST['submit'])) {
	
	$sql    = "SELECT test_id, random, num_questions FROM %stests WHERE test_id=%d AND course_id=%d";
	$row = queryDB($sql, array(TABLE_PREFIX, $tid, $_SESSION['course_id']), TRUE);
	if (count($row) == 0) { exit; }

	// #1760
	// for each question that isn't required
	if ($row['random']) {
		foreach ($_POST['weight'] as $qid => $weight) {
			if ($_POST['required'][$qid]) { continue; }
			if (!$current_weight) { $current_weight = $weight; }

			if ($current_weight != $weight) {
				// the weights aren't the same.
				$msg->addError('RAND_TEST_Q_WEIGHT');
				break;
			}
		}
	}

	if (!$msg->containsErrors()) {
		//update the weights & order
		$total_weight = 0;
		$total_required_weight = 0;
		$total_required_num = 0;
		$optional_weight = 0;
		$count = 1;
		foreach ($_POST['weight'] as $qid => $weight) {
			$qid    = intval($qid);
			$weight = intval($weight);
			if ($_POST['required'][$qid]) {
				$required = 1;
			} else {
				$required = 0;
			}

			if ($row['random']) {
				if ($required) {
					$total_required_weight += $weight;
					$total_required_num++;
				} else {
					$optional_weight = $weight; // what each optional question weights.
				}
			} else {
				$total_weight += $weight; // not random, so just sum the weights
			}
				
			if (!$row['random']) {
				$orders = $_POST['ordering'];
				asort($orders);
				$orders = array_keys($orders);

				foreach ($orders as $k => $id)
					$orders[$k] = intval($id);
					
				$orders = array_flip($orders);
				$next_qid = ($orders[$qid]+1);
				
				$sql	= "UPDATE %stests_questions_assoc SET weight=%d, required=%d, ordering=%d WHERE question_id=%d AND test_id=%d";
    		    $result	= queryDB($sql,array(TABLE_PREFIX, $weight, $required, $next_qid, $qid, $tid));

			} else {
				$sql	= "UPDATE %stests_questions_assoc SET weight=%d, required=%d, ordering=%d WHERE question_id=%d AND test_id=%d";
		        $result	= queryDB($sql, array(TABLE_PREFIX, $weight, $required, $count, $qid, $tid));

			}

			$count++;
		}

		$num_questions_sql = '';
		if ($row['random']) {
			$row['num_questions'] -= $total_required_num;
			if ($row['num_questions'] > 0) {
				// how much do the optional questions add up to: (assume they all weight the same)
				$total_weight = $total_required_weight + $optional_weight * $row['num_questions'];
			} else {
				$total_weight = $total_required_weight; // there are no more optional questions
				$num_questions_sql = ', num_questions='.$total_required_num;
			}
		}
		
		$sql	= "UPDATE %stests SET out_of='%s' %s WHERE test_id=%d";
		$result	= queryDB($sql, array(TABLE_PREFIX, $total_weight, $num_questions_sql, $tid));

		$total_weight = 0;
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		header('Location: '.$_SERVER['PHP_SELF'] .'?tid='.$tid);
		exit;
	}
}

require(AT_INCLUDE_PATH.'header.inc.php');

$sql	= "SELECT title, random FROM %stests WHERE test_id=%d";
$row	= queryDB($sql, array(TABLE_PREFIX, $tid), TRUE);


echo '<h3>'._AT('questions_for').' '.AT_print($row['title'], 'tests.title').'</h3>';
$random = $row['random'];

$sql	= "SELECT count(*) as cnt FROM %stests_questions_assoc QA, %stests_questions Q WHERE QA.test_id=%d AND QA.weight=0 AND QA.question_id=Q.question_id AND Q.type<>4";
$row	= queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, $tid), TRUE);

if ($row['cnt']) {
	$msg->printWarnings('QUESTION_WEIGHT');
}

$msg->printAll();

$sql	= "SELECT * FROM %stests_questions Q, %stests_questions_assoc TQ WHERE Q.course_id=%d AND Q.question_id=TQ.question_id AND TQ.test_id=%d ORDER BY TQ.ordering";
$rows_questions	= queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, $_SESSION['course_id'], $tid));

?>
<script type="text/javascript">
//<!--
function setAllWeights() {
    for (var i=0; i<document.form.elements.length;i++) {
        var e = document.form.elements[i];
        if ((e.type == 'text') && (e.name.substring(0, 7) == 'weight[')) {
            e.value = document.form.all_weights.value;
        }
    }
}
//-->
</script>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>?tid=<?php echo $tid; ?>" method="post" name="form">
<div class="input-form">
	<div class="row">
		<?php echo _AT('set_all_weights'); ?>
	</div>
	<div class="row">
		<label for="all_weights"><?php echo _AT('points') . ':'; ?></label>
		<input type="text" id="all_weights" name="all_weights" size="2">
	</div>
	<div class="row">
		<input type="button" class="button" name="set_all_weights" value="<?php echo _AT('set'); ?>" onclick="setAllWeights()">
	</div>
</div>

<input type="hidden" name="tid" value="<?php echo $tid; ?>" />
<table class="data static" summary="" rules="rows">
<thead>
<tr>
	<th scope="col"><?php echo _AT('num');      ?></th>
	<th scope="col"><?php echo _AT('points');   ?></th>
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

if(count($rows_questions) > 0){

	$sql	= "SELECT title, category_id FROM %stests_questions_categories WHERE course_id=%d";
	$cats_rows	= queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id']));
	
	$cats    = array();
	$cats[0] = _AT('cats_uncategorized');
	foreach($cats_rows as $cat_row){
		$cats[$cat_row['category_id']] = $cat_row['title'];
	}
    foreach($rows_questions as $row){

		$count++;
		echo '<tr>';
		echo '<td class="row1" align="center"><strong>'.$count.'</strong></td>';
		echo '<td class="row1" align="center">';
		
		if (isset($_POST['submit'])) {
			$row['weight'] = $_POST['weight'][$row['question_id']];
			$row['required'] = (isset($_POST['required'][$row['question_id']]) ? 1 : 0);
		}

		if ($row['type'] == 4) {
			echo ''._AT('na').'';
			echo '<input type="hidden" value="0" name="weight['.$row['question_id'].']" />';
		} else {
			echo '<input type="text" value="'.$row['weight'].'" name="weight['.$row['question_id'].']" size="2" />';
		}
		echo '</td>';

		if ($random) {
			echo '<td class="row1" align="center">'._AT('na').'</td>';
		} else {
			echo '<td class="row1" align="center"><input type="text" name="ordering['.$row['question_id'].']" value="'.$row['ordering'].'" size="2" /></td>';
		}

		echo '<td class="row1">';
        echo AT_print(validate_length($row['question'], 45, VALIDATE_LENGTH_FOR_DISPLAY), 'tests_questions.question');

		echo '</td>';
		echo '<td nowrap="nowrap">';
		$o = TestQuestions::getQuestion($row['type']);
		echo $o->printName();
		echo '</td>';

		$link = 'mods/_standard/tests/edit_question_'.$o->getPrefix().'.php?tid='.$tid.SEP.'qid='.$row['question_id'];

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
		echo '<a href="mods/_standard/tests/question_remove.php?tid=' . $tid . SEP . 'qid=' . $row['question_id'] . '">' . _AT('remove') . '</a>';
		echo '</td>';

		echo '</tr>';
	} 

	//total weight
	echo '<tfoot>';
	echo '<tr><td>&nbsp;</td>';
	echo '<td colspan="';
	if ($random) {
		echo 7;
	} else {
		echo 6;
	}

	echo '" align="left" nowrap="nowrap">';
	echo '<input type="submit" value="'._AT('save').'" name="submit" /> </td>';
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