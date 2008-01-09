<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_TESTS);
require(AT_INCLUDE_PATH.'lib/test_result_functions.inc.php');
require(AT_INCLUDE_PATH.'classes/testQuestions.class.php');
$_letters = array(_AT('A'), _AT('B'), _AT('C'), _AT('D'), _AT('E'), _AT('F'), _AT('G'), _AT('H'), _AT('I'), _AT('J'));

$tid = intval($_GET['tid']);
if ($tid == 0){
	$tid = intval($_POST['tid']);
}

$_pages['tools/tests/view_results.php']['title_var']  = 'view_results';
$_pages['tools/tests/view_results.php']['parent'] = 'tools/tests/results.php?tid='.$tid;

$_pages['tools/tests/results.php?tid='.$tid]['title_var'] = 'submissions';
$_pages['tools/tests/results.php?tid='.$tid]['parent'] = 'tools/tests/index.php';


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
			$score		  = floatval($score);
			$final_score += $score;

			$sql	= "UPDATE ".TABLE_PREFIX."tests_answers SET score='$score' WHERE result_id=$rid AND question_id=$qid";
			$result	= mysql_query($sql, $db);
		}
	}

	$sql	= "UPDATE ".TABLE_PREFIX."tests_results SET final_score='$final_score', date_taken=date_taken, end_time=end_time WHERE result_id=$rid AND status=1";
	$result	= mysql_query($sql, $db);

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
	
$sql	= "SELECT * FROM ".TABLE_PREFIX."tests WHERE test_id=$tid AND course_id=$_SESSION[course_id]";
$result	= mysql_query($sql, $db);

if (!($row = mysql_fetch_array($result))){
	$msg->printErrors('ITEM_NOT_FOUND');
	require (AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}
$test_title = $row['title'];
$out_of		= $row['out_of'];

$tid = intval($_GET['tid']);
$rid = intval($_GET['rid']);

$sql	= "SELECT TQ.*, TQA.* FROM ".TABLE_PREFIX."tests_questions TQ INNER JOIN ".TABLE_PREFIX."tests_questions_assoc TQA USING (question_id) WHERE TQ.course_id=$_SESSION[course_id] AND TQA.test_id=$tid ORDER BY TQA.ordering";
$result	= mysql_query($sql, $db);

if (mysql_num_rows($result) == 0) {
	echo '<p>'._AT('no_questions').'</p>';
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}
?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="tid" value="<?php echo $tid; ?>">
<input type="hidden" name="rid" value="<?php echo $rid; ?>">

<div class="input-form">
	<div class="row">
		<h2><?php echo AT_print($test_title, 'tests.title'); ?></h2>
	</div>
	<?php while ($row = mysql_fetch_assoc($result)) {
		/* get the results for this question */
		$sql		= "SELECT C.* FROM ".TABLE_PREFIX."tests_answers C WHERE C.result_id=$rid AND C.question_id=$row[question_id]";
		$result_a	= mysql_query($sql, $db);
		if ($answer_row = mysql_fetch_assoc($result_a)) {
			$obj = TestQuestions::getQuestion($row['type']);
			$obj->displayResult($row, $answer_row, TRUE);

			if ($row['feedback']) {
				echo '<div class="row"><p><strong>'._AT('feedback').':</strong> ';
				echo nl2br($row['feedback']).'</p></div>';
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

</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>