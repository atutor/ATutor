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
// $Id: take_test.php 9034 2009-12-14 19:47:30Z cindy $
define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'../mods/_standard/tests/lib/test_result_functions.inc.php');
require(AT_INCLUDE_PATH.'../mods/_standard/tests/classes/testQuestions.class.php');

$tid = intval($_REQUEST['tid']);
if (isset($_REQUEST['gid'])) $gid = $addslashes($_REQUEST['gid']);
if (isset($_REQUEST['cid']))
{
	$cid = $addslashes($_REQUEST['cid']);
	$cid_url = SEP.'cid='.$cid;
}

//make sure max attempts not reached, and still on going
$sql		= "SELECT *, UNIX_TIMESTAMP(start_date) AS start_date, UNIX_TIMESTAMP(end_date) AS end_date FROM ".TABLE_PREFIX."tests WHERE test_id=".$tid." AND course_id=".$_SESSION['course_id'];
$result= mysql_query($sql, $db);
$test_row = mysql_fetch_assoc($result);
/* check to make sure we can access this test: */
if (!$test_row['guests'] && ($_SESSION['enroll'] == AT_ENROLL_NO || $_SESSION['enroll'] == AT_ENROLL_ALUMNUS)) {
	require(AT_INCLUDE_PATH.'header.inc.php');
	$msg->printInfos('NOT_ENROLLED');

	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

if (!$test_row['guests'] && !authenticate_test($tid)) {
	header('Location: '.url_rewrite('mods/_standard/tests/my_tests.php', AT_PRETTY_URL_IS_HEADER));
	exit;
}

// checks one/all questions per page, and forward user to the correct one
if ($test_row['display']) {
	header('Location: '.url_rewrite('mods/_standard/tests/take_test_q.php?tid='.$tid.$cid_url, AT_PRETTY_URL_IS_HEADER));
} 

$out_of = $test_row['out_of'];

$sql		= "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."tests_results WHERE status=1 AND test_id=".$tid." AND member_id='".$_SESSION['member_id']."'";
$takes_result= mysql_query($sql, $db);
$takes = mysql_fetch_assoc($takes_result);	

if ( (($test_row['start_date'] > time()) || ($test_row['end_date'] < time())) || 
   ( ($test_row['num_takes'] != AT_TESTS_TAKE_UNLIMITED) && ($takes['cnt'] >= $test_row['num_takes']) )  ) {
	require(AT_INCLUDE_PATH.'header.inc.php');
	$msg->printInfos('MAX_ATTEMPTS');
	
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

if (isset($_POST['submit'])) {
	// insert
	if (!isset($_POST['gid'])) {
		$sql	= "SELECT result_id FROM ".TABLE_PREFIX."tests_results WHERE test_id=$tid AND member_id='$_SESSION[member_id]' AND status=0";
		$result	= mysql_query($sql, $db);
		$row    = mysql_fetch_assoc($result);
		$result_id = $row['result_id'];
	} else {
		$sql	= "INSERT INTO ".TABLE_PREFIX."tests_results VALUES (NULL, $tid, '".$_POST["gid"]."', NOW(), '', 0, NOW(), 0)";
		$result = mysql_query($sql, $db);
		$result_id = mysql_insert_id($db);
	}

	$final_score     = 0;
	$set_final_score = TRUE; // whether or not to save the final score in the results table.

	$sql	= "SELECT TQA.weight, TQA.question_id, TQ.type, TQ.answer_0, TQ.answer_1, TQ.answer_2, TQ.answer_3, TQ.answer_4, TQ.answer_5, TQ.answer_6, TQ.answer_7, TQ.answer_8, TQ.answer_9 FROM ".TABLE_PREFIX."tests_questions_assoc TQA INNER JOIN ".TABLE_PREFIX."tests_questions TQ USING (question_id) WHERE TQA.test_id=$tid ORDER BY TQA.ordering, TQ.question_id";
	$result	= mysql_query($sql, $db);
	while ($row = mysql_fetch_assoc($result)) {
		if (isset($_POST['answers'][$row['question_id']])) {
			$obj = TestQuestions::getQuestion($row['type']);
			$score = $obj->mark($row);

			if (!isset($_POST["gid"])) {
				$sql	= "UPDATE ".TABLE_PREFIX."tests_answers SET answer='{$_POST[answers][$row[question_id]]}', score='$score' WHERE result_id=$result_id AND question_id=$row[question_id]";
			} else {
				$sql	= "INSERT INTO ".TABLE_PREFIX."tests_answers VALUES ($result_id, $row[question_id], 0, '{$_POST[answers][$row[question_id]]}', '$score', '')";
			}
			mysql_query($sql, $db);

			// don't set final score if there is any unmarked answers and release option is set to "after all answers are marked"
			if (is_null($score))
			{
				if ($test_row['result_release']==AT_RELEASE_MARKED)
					$set_empty_final_score = true;
			}
			else
				$final_score += $score;
		}
	}

	// update the final score
	// update status to complate to fix refresh test issue.
	if ($set_empty_final_score)
		$sql	= "UPDATE ".TABLE_PREFIX."tests_results SET final_score=NULL, date_taken=date_taken, status=1, end_time=NOW() WHERE result_id=$result_id";
	else
		$sql	= "UPDATE ".TABLE_PREFIX."tests_results SET final_score=$final_score, date_taken=date_taken, status=1, end_time=NOW() WHERE result_id=$result_id";
	$result	= mysql_query($sql, $db);

	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	if ((!$_SESSION['enroll'] && !isset($cid)) || $test_row['result_release']==AT_RELEASE_IMMEDIATE) {
		header('Location: '.url_rewrite('mods/_standard/tests/view_results.php?tid='.$tid.SEP.'rid='.$result_id.$cid_url, AT_PRETTY_URL_IS_HEADER));
		exit;
	}
	
	if (isset($cid)) header('Location: '.url_rewrite('content.php?cid='.$cid, AT_PRETTY_URL_IS_HEADER));
	else header('Location: '.url_rewrite('mods/_standard/tests/my_tests.php', AT_PRETTY_URL_IS_HEADER));
	exit;
}

if (defined('AT_FORCE_GET_FILE') && AT_FORCE_GET_FILE) {
	$content_base_href = 'get.php/';
} else {
	$course_base_href = 'content/' . $_SESSION['course_id'] . '/';
}

require(AT_INCLUDE_PATH.'header.inc.php');

/* Retrieve the content_id of this test */
$num_questions = $test_row['num_questions'];
$content_id = $test_row['content_id'];
$anonymous = $test_row['anonymous'];
$instructions = $test_row['instructions'];
$title = $test_row['title'];

$_letters = array(_AT('A'), _AT('B'), _AT('C'), _AT('D'), _AT('E'), _AT('F'), _AT('G'), _AT('H'), _AT('I'), _AT('J'));

// first check if there's an 'in progress' test.
// this is the only place in the code that makes sure there is only ONE 'in progress' test going on.
$in_progress = false;
$sql = "SELECT result_id FROM ".TABLE_PREFIX."tests_results WHERE member_id='{$_SESSION['member_id']}' AND test_id=$tid AND status=0";

$result  = mysql_query($sql);
if ($row = mysql_fetch_assoc($result)) {
	$result_id = $row['result_id'];
	$in_progress = true;

	// retrieve the test questions that were saved to `tests_answers`

	$sql	= "SELECT R.*, A.*, Q.* FROM ".TABLE_PREFIX."tests_answers R INNER JOIN ".TABLE_PREFIX."tests_questions_assoc A USING (question_id) INNER JOIN ".TABLE_PREFIX."tests_questions Q USING (question_id) WHERE R.result_id=$result_id AND A.test_id=$tid ORDER BY Q.question_id";
	
} else if ($test_row['random']) {
	/* Retrieve 'num_questions' question_id randomly choosed from those who are related to this test_id*/

	$non_required_questions = array();
	$required_questions     = array();

	$sql    = "SELECT question_id, required FROM ".TABLE_PREFIX."tests_questions_assoc WHERE test_id=$tid";
	$result	= mysql_query($sql, $db);
	
	while ($row = mysql_fetch_assoc($result)) {
		if ($row['required'] == 1) {
			$required_questions[] = $row['question_id'];
		} else {
			$non_required_questions[] = $row['question_id'];
		}
	}
	
	$num_required = count($required_questions);
	if ($num_required < max(1, $num_questions)) {
		shuffle($non_required_questions);
		$required_questions = array_merge($required_questions, array_slice($non_required_questions, 0, $num_questions - $num_required));
	}

	$random_id_string = implode(',', $required_questions);

	$sql = "SELECT TQ.*, TQA.* FROM ".TABLE_PREFIX."tests_questions TQ INNER JOIN ".TABLE_PREFIX."tests_questions_assoc TQA USING (question_id) WHERE TQ.course_id=$_SESSION[course_id] AND TQA.test_id=$tid AND TQA.question_id IN ($random_id_string) ORDER BY TQ.question_id";
} else {
	$sql	= "SELECT TQ.*, TQA.* FROM ".TABLE_PREFIX."tests_questions TQ INNER JOIN ".TABLE_PREFIX."tests_questions_assoc TQA USING (question_id) WHERE TQ.course_id=$_SESSION[course_id] AND TQA.test_id=$tid ORDER BY TQA.ordering, TQA.question_id";
}

$result	= mysql_query($sql, $db);

$questions = array();
while ($row = mysql_fetch_assoc($result)) {
	$questions[] = $row;
}

if (!$result || !$questions) {
	echo '<p>'._AT('no_questions').'</p>';
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

// save $questions with no response, and set status to 'in progress' in test_results <---
if (!isset($_REQUEST['gid']) && !$in_progress) {
	$sql	= "INSERT INTO ".TABLE_PREFIX."tests_results VALUES (NULL, $tid, '$_SESSION[member_id]', NOW(), '', 0, NOW(), 0)";
	$result = mysql_query($sql, $db);
	$result_id = mysql_insert_id($db);
}
?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="tid" value="<?php echo $tid; ?>" />
<?php if (isset($_REQUEST['gid'])): ?> <input type="hidden" name="gid" value="<?php echo $gid; ?>" /> <?php endif; ?>
<?php if (isset($_REQUEST['cid'])): ?> <input type="hidden" name="cid" value="<?php echo $cid; ?>" /> <?php endif; ?>

<div class="input-form" style="width:95%">
	<fieldset class="group_form"><legend class="group_form"><?php echo $title ?></legend>


	<?php if ($instructions!=''): ?>
		<div style="background-color: #f3f3f3; padding: 5px 10px; margin: 0px; border-top: 1px solid">
			<strong><?php echo _AT('instructions'); ?></strong>
		</div>
		<div class="row" style="padding-bottom: 20px"><?php echo $instructions; ?></div>
	<?php endif; ?>

	<?php if ($anonymous): ?>
		<div class="row"><em><strong><?php echo _AT('test_anonymous'); ?></strong></em></div>
	<?php endif; ?>

	<?php
	foreach ($questions as $row) {
		if (!isset($_POST["gid"]) && !$in_progress) {
			$sql	= "INSERT INTO ".TABLE_PREFIX."tests_answers VALUES ($result_id, $row[question_id], $_SESSION[member_id], '', '', '')";
			mysql_query($sql, $db);
		}

		$obj = TestQuestions::getQuestion($row['type']);
		$obj->display($row);
	}
	?>
	<div style="background-color: #f3f3f3; padding: 5px 10px; margin: 0px; border-top: 1px solid">
		<strong><?php echo _AT('done'); ?>!</strong>
	</div>
	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('submit'); ?>" accesskey="s" onclick="confirmSubmit(this, '<?php echo $addslashes(_AT("test_confirm_submit")); ?>'); return false;"/>
	</div>
</div>
</form>
<script type="text/javascript" src="<?php echo $_base_href;?>/mods/_standard/tests/lib/take_test.js">
<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>