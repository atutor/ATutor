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
// $Id: edit_question_tf.php 7482 2008-05-06 17:44:49Z greg $

$page = 'tests';
define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_TESTS);
require(AT_INCLUDE_PATH.'../mods/_standard/tests/lib/test_result_functions.inc.php');

$qid = intval($_GET['qid']);
if ($qid == 0){
	$qid = intval($_POST['qid']);
}

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	if ($_POST['tid']) {
		header('Location: questions.php?tid='.$_POST['tid']);			
	} else {
		header('Location: question_db.php');
	}
	exit;
} else if (isset($_POST['submit'])) {

	$_POST['question'] = trim($_POST['question']);

	if ($_POST['question'] == ''){
		$msg->addError(array('EMPTY_FIELDS', _AT('statement')));
	}

	if (!$msg->containsErrors()) {
		$_POST['feedback']    = $addslashes(trim($_POST['feedback']));
		$_POST['question']    = $addslashes($_POST['question']);
		$_POST['qid']	      = intval($_POST['qid']);
		$_POST['category_id'] = intval($_POST['category_id']);
		$_POST['answer']      = intval($_POST['answer']);

		$sql	= "UPDATE ".TABLE_PREFIX."tests_questions SET	category_id=$_POST[category_id],
			feedback='$_POST[feedback]',
			question='$_POST[question]',
			answer_0={$_POST[answer]}
			WHERE question_id=$_POST[qid] AND course_id=$_SESSION[course_id]";

		$result	= mysql_query($sql, $db);
		
		$msg->addFeedback('QUESTION_UPDATED');
		if ($_POST['tid']) {
			header('Location: questions.php?tid='.$_POST['tid']);			
		} else {
			header('Location: question_db.php');
		}
		exit;
	}
}
require(AT_INCLUDE_PATH.'header.inc.php'); 

if (!$_POST['submit']) {
	$sql	= "SELECT * FROM ".TABLE_PREFIX."tests_questions WHERE question_id=$qid AND course_id=$_SESSION[course_id] AND type=2";
	$result	= mysql_query($sql, $db);

	if (!($row = mysql_fetch_array($result))){
		$msg->printErrors('ITEM_NOT_FOUND');
		require (AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}

	$_POST	= $row;
}

if ($_POST['required'] == 1) {
	$req_yes = ' checked="checked"';
} else {
	$req_no  = ' checked="checked"';
}

if ($_POST['answer'] == '') {
	if ($_POST['answer_0'] == 1) {
		$ans_yes = ' checked="checked"';
	} else if ($_POST['answer_0'] == 2){
		$ans_no  = ' checked="checked"';
	} else if ($_POST['answer_0'] == 3) {
		$ans_yes1 = ' checked="checked"';
	} else {
		$ans_no1  = ' checked="checked"';
	}
} else {
	if ($_POST['answer'] == 1) {
		$ans_yes = ' checked="checked"';
	} else if($_POST['answer'] == 2){
		$ans_no  = ' checked="checked"';
	} else if ($_POST['answer'] == 3) {
		$ans_yes1 = ' checked="checked"';
	} else {
		$ans_no1  = ' checked="checked"';
	}
}

?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="qid" value="<?php echo $qid; ?>" />
<input type="hidden" name="tid" value="<?php echo $_REQUEST['tid']; ?>" />
<input type="hidden" name="required" value="1" />

<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('test_tf'); ?></legend>
	<div class="row">
		<label for="cats"><?php echo _AT('category'); ?></label>
		<select name="category_id" id="cats">
			<?php print_question_cats($_POST['category_id']); ?>
		</select>
	</div>

	<div class="row">
		<label for="optional_feedback"><?php echo _AT('optional_feedback'); ?></label> 
		<?php print_VE('optional_feedback'); ?>
		<textarea id="optional_feedback" cols="50" rows="3" name="feedback"><?php echo htmlspecialchars(stripslashes($_POST['feedback'])); ?></textarea>
	</div>

	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="question"><?php echo _AT('statement'); ?></label> 
		<?php print_VE('question'); ?>	
		<textarea id="question" cols="50" rows="6" name="question"><?php 
			echo htmlspecialchars(stripslashes($_POST['question'])); ?></textarea>
	</div>

	<div class="row">
		<?php echo _AT('answer'); ?><br />
		<input type="radio" name="answer" value="1" id="answer1"<?php echo $ans_yes; ?> /><label for="answer1"><?php echo _AT('true'); ?></label>, <input type="radio" name="answer" value="2" id="answer2"<?php echo $ans_no; ?> /><label for="answer2"><?php echo _AT('false'); ?></label>
	</div>

	<div class="row buttons">
		<input type="submit" value="<?php echo _AT('save'); ?>"   name="submit" accesskey="s"/>
		<input type="submit" value="<?php echo _AT('cancel'); ?>" name="cancel" />
	</div>
	</fieldset>
</div>
</form>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>