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

$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/';
$_section[1][0] = _AT('test_manager');
$_section[1][1] = 'tools/tests/index.php';
$_section[2][0] = _AT('question_bank');

$msg =& new Message($savant);

authenticate(AT_PRIV_TEST_CREATE);

if (isset($_POST['submit_add'])) {
	$tid = intval($_POST['tid']);
	$sql = "INSERT INTO ".TABLE_PREFIX."tests_questions_assoc VALUES ";
	foreach ($_POST['questions'] as $question) {
		$question = intval($question);
		$sql .= '('.$tid.', '.$question.'),';
	}
	$sql = substr($sql, 0, -1);
	$result = mysql_query($sql, $db);

	$msg->addFeedback('');
	header('Location: questions.php?tid='.$tid);
	exit;
} else if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: question_bank.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

debug($_POST);

$questions = addslashes(implode(',',$_POST['add_questions']));
$sql = "SELECT question FROM ".TABLE_PREFIX."tests_questions WHERE question_id IN ($questions) AND course_id=$_SESSION[course_id] ORDER BY question";
$result = mysql_query($sql, $db);
$questions = '';
while ($row = mysql_fetch_assoc($result)) {
	$questions .= '<li>'.$row['question'].'</li>';
}
echo 'The following questions will be added to the test:<ul>'.$questions.'</ul>';
?>

<form method="post" action="">
	<input type="hidden" name="tid" value="<?php echo $_POST['test_id']; ?>" />
	<?php foreach ($_POST['add_questions'] as $question): ?>
		<input type="hidden" name="questions[]" value="<?php echo $question; ?>" />
	<?php endforeach; ?>
	<input type="submit" name="submit_add" value="Add" class="button" /> - <input type="submit" name="cancel" value="Cancel" class="button" />
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>