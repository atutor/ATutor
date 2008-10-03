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
require(AT_INCLUDE_PATH.'classes/testQuestions.class.php');

authenticate(AT_PRIV_TESTS);

$tid = intval($_REQUEST['tid']);

if (isset($_POST['reset_filter'])) unset($_POST);

$_pages['tools/tests/results_all_quest.php']['title_var']  = 'question_statistics';
$_pages['tools/tests/results_all_quest.php']['parent']  = 'tools/tests/index.php';
$_pages['tools/tests/results_all_quest.php']['children'] = array('tools/tests/results_all.php?tid='.$tid);

$_pages['tools/tests/results_all.php?tid='.$tid]['title_var']  = 'mark_statistics';
$_pages['tools/tests/results_all.php?tid='.$tid]['parent'] = 'tools/tests/results_all_quest.php';

require(AT_INCLUDE_PATH.'header.inc.php');

$sql	= "SELECT TQ.*, TQA.* FROM ".TABLE_PREFIX."tests_questions TQ INNER JOIN ".TABLE_PREFIX."tests_questions_assoc TQA USING (question_id) WHERE TQ.course_id=$_SESSION[course_id] AND TQA.test_id=$tid ORDER BY TQA.ordering, TQA.question_id";
$result	= mysql_query($sql, $db);
$questions = array();
while ($row = mysql_fetch_array($result)) {
	$row['score']	= 0;
	$questions[]	= $row;
	$q_sql .= $row['question_id'].',';
}
$q_sql = substr($q_sql, 0, -1);
$num_questions = count($questions);

//check if survey
$sql	= "SELECT out_of, title, randomize_order FROM ".TABLE_PREFIX."tests WHERE test_id=$tid";
$result = mysql_query($sql, $db);
$row = mysql_fetch_assoc($result);
$tt = $row['title'];
$random = $row['randomize_order'];

echo '<h3>'.$row['title'].'</h3><br />';

//get all the questions in this test, store them
$sql	= "SELECT TQ.*, TQA.* FROM ".TABLE_PREFIX."tests_questions TQ INNER JOIN ".TABLE_PREFIX."tests_questions_assoc TQA USING (question_id) WHERE TQ.course_id=$_SESSION[course_id] AND TQA.test_id=$tid ORDER BY TQA.ordering, TQA.question_id";

$result = mysql_query($sql, $db);
$questions = array();	
while ($row = mysql_fetch_assoc($result)) {
	$questions[$row['question_id']] = $row;
}
$long_qs = substr($long_qs, 0, -1);

//get the answers:  count | q_id | answer
$sql = "SELECT count(*), A.question_id, A.answer, A.score
		FROM ".TABLE_PREFIX."tests_answers A, ".TABLE_PREFIX."tests_results R
		WHERE R.status=1 AND R.result_id=A.result_id AND R.final_score<>'' AND R.test_id=$tid";

if ($_POST["user_type"] == 1) $sql .= " AND R.member_id not like 'G_%' AND R.member_id > 0 ";
if ($_POST["user_type"] == 2) $sql .= " AND (R.member_id like 'G_%' OR R.member_id = 0) ";

$sql .=	" GROUP BY A.question_id, A.answer
		ORDER BY A.question_id, A.answer";

$result = mysql_query($sql, $db);
?>

<div class="input-form">
<form method="post" action="<?php echo $_SERVER['PHP_SELF'] . '?tid='.$tid; ?>">
	<div class="row">
		<?php echo _AT('user_type'); ?><br />
		<input type="radio" name="user_type" value="1" id="u0" <?php if ($_POST['user_type'] == 1) { echo 'checked="checked"'; } ?> /><label for="u0"><?php echo _AT('registered_members'); ?></label> 
		<input type="radio" name="user_type" value="2" id="u1" <?php if ($_POST['user_type'] == 2) { echo 'checked="checked"'; } ?> /><label for="u1"><?php echo _AT('guests'); ?></label> 
		<input type="radio" name="user_type" value="0" id="u2" <?php if (!isset($_POST['user_type']) || ($_POST['user_type'] != 1 && $_POST['user_type'] != 2)) { echo 'checked="checked"'; } ?> /><label for="u2"><?php echo _AT('all'); ?></label> 
	</div>

	<div class="row buttons">
		<input type="submit" name="filter" value="<?php echo _AT('filter'); ?>" />
		<input type="submit" name="reset_filter" value="<?php echo _AT('reset_filter'); ?>" />
		<input type="hidden" name="test_id" value="<?php echo $tid; ?>" />
	</div>
</form>
</div>

<?php
echo '<img src="images/checkmark.gif" alt="'._AT('correct_answer').'" />- '._AT('correct_answer').'<br /></p>';

$ans = array();	
while ($row = mysql_fetch_assoc($result)) {
	$ans[$row['question_id']][$row['answer']] = array('count'=>$row['count(*)'], 'score'=>$row['score']);
}

//print out rows
foreach ($questions as $q_id => $q) {
	/* for random: num_results is going to be specific to each question.
	 * This is a randomized test which means that it is possible each question has been answered a 
	 * different number of times.  Statistics are therefore based on the number of times each 
	 * question was answered, not the number of times the test has been taken.
	 */

	//catch random unanswered
	if($ans[$q_id]) {
		$obj = TestQuestions::getQuestion($q['type']);
		$obj->displayResultStatistics($q, $ans[$q_id]);
	}
}

require(AT_INCLUDE_PATH.'footer.inc.php');
?>