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
define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/test_result_functions.inc.php'); // for print_result and print_score
require(AT_INCLUDE_PATH.'classes/testQuestions.class.php');
$_letters = array(_AT('A'), _AT('B'), _AT('C'), _AT('D'), _AT('E'), _AT('F'), _AT('G'), _AT('H'), _AT('I'), _AT('J'));

if (defined('AT_FORCE_GET_FILE') && AT_FORCE_GET_FILE) {
	$content_base_href = 'get.php/';
} else {
	$course_base_href = 'content/' . $_SESSION['course_id'] . '/';
}

require(AT_INCLUDE_PATH.'header.inc.php');

$tid = intval($_GET['tid']);
$rid = intval($_GET['rid']);

$sql	= "SELECT title, random, passfeedback, failfeedback, passscore, passpercent FROM ".TABLE_PREFIX."tests WHERE test_id=$tid AND course_id=$_SESSION[course_id]";
$result	= mysql_query($sql, $db);
$row	= mysql_fetch_array($result);
$test_title	= $row['title'];
$passfeedback	= $row['passfeedback'];
$failfeedback	= $row['failfeedback'];
$passscore	= $row['passscore'];
$passpercent	= $row['passpercent'];
$is_random  = $row['random'];

$mark_right = ' <img src="'.$_base_path.'images/checkmark.gif" alt="'._AT('correct_answer').'" title="'._AT('correct_answer').'" />';
$mark_wrong = ' <img src="'.$_base_path.'images/x.gif" alt="'._AT('wrong_answer').'" title="'._AT('wrong_answer').'" />';

$sql	= "SELECT * FROM ".TABLE_PREFIX."tests_results WHERE result_id=$rid AND member_id=$_SESSION[member_id]";
$result	= mysql_query($sql, $db); 
if (!$row = mysql_fetch_assoc($result)){
	$msg->printErrors('ITEM_NOT_FOUND');
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}
$final_score= $row['final_score'];

//make sure they're allowed to see results now
$sql	= "SELECT result_release, out_of FROM ".TABLE_PREFIX."tests WHERE test_id=$tid AND course_id=$_SESSION[course_id]";
$result	= mysql_query($sql, $db); 
$row = mysql_fetch_assoc($result);

if ( ($row['result_release']==AT_RELEASE_NEVER) || ($row['result_release']==AT_RELEASE_MARKED && $final_score=='') ) {
	$msg->printErrors('RESULTS_NOT_RELEASED');
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

$out_of = $row['out_of'];

/* Retrieve randomly choosed questions */
$sql	= "SELECT question_id FROM ".TABLE_PREFIX."tests_answers WHERE result_id=$rid";
$result	= mysql_query($sql, $db); 
$row = mysql_fetch_array($result);
$random_id_string = $row[question_id];
$row = mysql_fetch_array($result);	
while ($row['question_id'] != '') {
	$random_id_string = $random_id_string.','.$row['question_id'];
	$row = mysql_fetch_array($result);
}
if (!$random_id_string) {
	$random_id_string = 0;
}

if ($is_random) {
	$sql	= "SELECT TQ.*, TQA.* FROM ".TABLE_PREFIX."tests_questions TQ INNER JOIN ".TABLE_PREFIX."tests_questions_assoc TQA USING (question_id) WHERE TQA.test_id=$tid AND TQ.question_id IN ($random_id_string) ORDER BY TQ.question_id";
} else {
	$sql	= "SELECT TQ.*, TQA.* FROM ".TABLE_PREFIX."tests_questions TQ INNER JOIN ".TABLE_PREFIX."tests_questions_assoc TQA USING (question_id) WHERE TQA.test_id=$tid AND TQ.question_id IN ($random_id_string) ORDER BY TQA.ordering, TQ.question_id";
}
$result	= mysql_query($sql, $db); 

if (mysql_num_rows($result) == 0) {
	echo '<p>'._AT('no_questions').'</p>';
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

// calculate test/my total score to display pass/fail feedback
$sql_test_total = "SELECT sum(TQA.weight) test_total_score FROM ".TABLE_PREFIX."tests_questions TQ INNER JOIN ".TABLE_PREFIX."tests_questions_assoc TQA USING (question_id) WHERE TQA.test_id=$tid AND TQ.question_id IN ($random_id_string)";
$result_test_total	= mysql_query($sql_test_total, $db);
$row_test_total = mysql_fetch_array($result_test_total);
$test_total_score = $row_test_total["test_total_score"];

while ($row = mysql_fetch_assoc($result)) {
	$sql_this_score = "SELECT * FROM ".TABLE_PREFIX."tests_answers WHERE result_id=$rid AND question_id=$row[question_id] AND member_id=$_SESSION[member_id]";
	$result_this_score	= mysql_query($sql_this_score, $db); 
	$this_score = mysql_fetch_assoc($result_this_score);

	$my_score+=$this_score['score'];
	$this_total += $row['weight'];
}
?>
<form method="get" action="<?php echo AT_BASE_HREF; ?>tools/my_tests.php">
<div class="input-form">
	<div class="row">
		<h2><?php echo AT_print($test_title, 'tests.title'); ?></h2>
	</div>

	<div class="row">
		<h3 align="center">
			<?php 
				// don't display any feedback if test is created as "no pass score"
				if ($passscore == 0 && $passpercent == 0)
					echo '';
				// display pass feedback for passed students
				elseif (($passscore<>0 && $my_score>=$passscore) ||
				    ($passpercent<>0 && ($my_score/$this_total*100)>=$passpercent))
					echo '<font color="green">' . $passfeedback . '</font>';
				// otherwise, display fail feedback
				else
					echo '<font color="red">' . $failfeedback . '</font>'; 
			?>
		</h3>
	</div>

	<?php if ($row['instructions'] != ''): ?>
		<div style="background-color: #f3f3f3; padding: 5px 10px; margin: 0px; border-top: 1px solid">
			<strong><?php echo _AT('instructions'); ?></strong>
		</div>
		<div class="row" style="padding-bottom: 20px"><?php echo $row['instructions']; ?></div>
	<?php endif; ?>

	<?php
	// reset the result cursor to beginning
	mysql_data_seek ($result, 0);
	
	while ($row = mysql_fetch_assoc($result)) {
		$sql		= "SELECT * FROM ".TABLE_PREFIX."tests_answers WHERE result_id=$rid AND question_id=$row[question_id] AND member_id=$_SESSION[member_id]";
		$result_a	= mysql_query($sql, $db); 
		$answer_row = mysql_fetch_assoc($result_a);

		$obj = TestQuestions::getQuestion($row['type']);
		$obj->displayResult($row, $answer_row);

		if ($row['feedback']) {
			echo '<div class="row"><p><strong>'._AT('feedback').':</strong> ';
			echo nl2br($row['feedback']).'</p></div>';
		}
	}
	?>

	<?php if ($this_total): ?>
		<div style="background-color: #f3f3f3; padding: 5px 10px; margin: 0px; border-top: 1px solid">
			<strong>
				<span style="float: right"><?php echo $my_score .' / '.$this_total; ?> <?php echo _AT('points'); ?></span>
				<?php echo _AT('final_score'); ?>:
			</strong>
		</div>
	<?php else: ?>
		<div style="background-color: #f3f3f3; padding: 5px 10px; margin: 0px; border-top: 1px solid">
			<strong>
				<?php echo _AT('done'); ?>!
			</strong>
		</div>
	<?php endif; ?>

	<div class="row buttons">
		<input type="submit" value="<?php echo _AT('back'); ?>" name="back" />
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>