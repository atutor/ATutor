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
authenticate(AT_PRIV_TESTS);
require(AT_INCLUDE_PATH.'../mods/_standard/tests/lib/test_result_functions.inc.php');
require(AT_INCLUDE_PATH.'../mods/_standard/tests/classes/testQuestions.class.php');
$_letters = array(_AT('A'), _AT('B'), _AT('C'), _AT('D'), _AT('E'), _AT('F'), _AT('G'), _AT('H'), _AT('I'), _AT('J'));

$tid = intval($_GET['tid']);
if ($tid == 0){
	$tid = intval($_POST['tid']);
}

$_pages['mods/_standard/tests/view_results_manage.php']['title_var']  = 'view_results';
$_pages['mods/_standard/tests/view_results_manage.php']['parent'] = 'mods/_standard/tests/results.php?tid='.$tid;

$_pages['mods/_standard/tests/results.php?tid='.$tid]['title_var'] = 'submissions';
$_pages['mods/_standard/tests/results.php?tid='.$tid]['parent'] = 'mods/_standard/tests/index.php';

$sql	= "SELECT * FROM %stests WHERE test_id=%d AND course_id=%d";
$row	= queryDB($sql, array(TABLE_PREFIX, $tid, $_SESSION['course_id']), TRUE);

if(count($row) == 0){
	require(AT_INCLUDE_PATH.'header.inc.php');
	$msg->printErrors('ITEM_NOT_FOUND');
	require (AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}
$test_title = $row['title'];
$out_of		= $row['out_of'];

if ($_POST['cancel']) {
	$msg->addFeedback('CANCELLED');
	header('Location: results.php?tid='.$tid);
	exit;
} else if ($_POST['back']) {
	header('Location: results.php?tid='.$tid);
	exit;
} else if ($_POST['submit']) {
	$tid = intval($_POST['tid']);
	$rid = intval($_POST['rid']);
	
	$final_score = 0;
	if (is_array($_POST['scores'])) {
		foreach ($_POST['scores'] as $qid => $score) {
			$qid          = intval($qid);
			if ($score == '')
			{
				if ($row['result_release']==AT_RELEASE_MARKED)
					$set_empty_final_score = true;
			}
			else
			{
				$score		  = floatval($score);
				$final_score += $score;
			}

			$sql	= "UPDATE %stests_answers SET score='%s' WHERE result_id=%d AND question_id=%d";
			$result	= queryDB($sql, array(TABLE_PREFIX, $score, $rid, $qid));
		}
	}

	if ($set_empty_final_score){
		$sql	= "UPDATE %stests_results SET final_score=NULL, date_taken=date_taken, end_time=end_time WHERE result_id=%d AND status=1";
	    $result	= queryDB($sql, array(TABLE_PREFIX, $rid));
	} else {
		$sql	= "UPDATE %stests_results SET final_score='%s', date_taken=date_taken, end_time=end_time WHERE result_id=%d AND status=1";
	    $result	= queryDB($sql, array(TABLE_PREFIX, $final_score, $rid));

    }

	$msg->addFeedback('RESULTS_UPDATED');
	header('Location: results.php?tid='.$tid);
	exit;
}

if (defined('AT_FORCE_GET_FILE') && AT_FORCE_GET_FILE) {
	$content_base_href = 'get.php/';
} else {
	$course_base_href = 'content/' . $_SESSION['course_id'] . '/';
}

require(AT_INCLUDE_PATH.'header.inc.php');

$tid = intval($_GET['tid']);
$rid = intval($_GET['rid']);

$sql	= "SELECT TQ.*, TQA.* FROM %stests_questions TQ INNER JOIN %stests_questions_assoc TQA USING (question_id) WHERE TQ.course_id=%d AND TQA.test_id=%d ORDER BY TQA.ordering";
$rows_questions	= queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, $_SESSION['course_id'], $tid));

if(count($rows_questions) == 0){
	echo '<p>'._AT('no_questions').'</p>';
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}
?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="tid" value="<?php echo $tid; ?>" />
<input type="hidden" name="rid" value="<?php echo $rid; ?>" />

<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo AT_print($test_title, 'tests.title'); ?></legend>

	<?php 
	foreach($rows_questions as $row){
		/* get the results for this question */
		$sql		= "SELECT C.* FROM %stests_answers C WHERE C.result_id=%d AND C.question_id=%d";
		$answer_row	= queryDB($sql, array(TABLE_PREFIX, $rid, $row['question_id']), TRUE);
		
		if(count($answer_row) > 0){
			$obj = TestQuestions::getQuestion($row['type']);
			$obj->displayResult($row, $answer_row, TRUE);

			if ($row['feedback']) {
				echo '<div class="row"><p><strong>'._AT('feedback').':</strong> ';
				echo AT_print(nl2br($row['feedback']), 'tests_questions.feedback').'</p></div>';
			}
		}
	}
	?>

	<div class="row buttons">
	<?php if ($out_of): ?>
		<input type="submit" value="<?php echo _AT('save'); ?>" name="submit" accesskey="s" /> <input type="submit" value="<?php echo _AT('cancel'); ?>" name="cancel" />
	<?php else: ?>
		<input type="submit" value="<?php echo _AT('back'); ?>" name="back" />
	<?php endif; ?>
	</div>
	</fieldset>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>