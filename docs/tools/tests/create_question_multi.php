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

if (isset($_POST['cancel']) || isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: question_db.php');
	exit;
} else if (isset($_POST['submit'])) {
	$_POST['required'] = intval($_POST['required']);
	$_POST['feedback'] = trim($_POST['feedback']);
	$_POST['question'] = trim($_POST['question']);
	$_POST['category_id'] = intval($_POST['category_id']);
	$_POST['answer']      = intval($_POST['answer']);

	if ($_POST['question'] == ''){
		$msg->addError(array('EMPTY_FIELDS', _AT('question')));
	}
		
	if (!$msg->containsErrors()) {
		for ($i=0; $i<10; $i++) {
			$_POST['choice'][$i] = $addslashes(trim($_POST['choice'][$i]));
		}

		$answers = array_fill(0, 10, 0);
		$answers[$_POST['answer']] = 1;

		$_POST['feedback']   = $addslashes($_POST['feedback']);
		$_POST['question']   = $addslashes($_POST['question']);

		$sql	= "INSERT INTO ".TABLE_PREFIX."tests_questions VALUES (	NULL, 
				$_POST[category_id],
				$_SESSION[course_id],
				1,
				'$_POST[feedback]',
				'$_POST[question]',
				'{$_POST[choice][0]}',
				'{$_POST[choice][1]}',
				'{$_POST[choice][2]}',
				'{$_POST[choice][3]}',
				'{$_POST[choice][4]}',
				'{$_POST[choice][5]}',
				'{$_POST[choice][6]}',
				'{$_POST[choice][7]}',
				'{$_POST[choice][8]}',
				'{$_POST[choice][9]}',
				{$answers[0]},
				{$answers[1]},
				{$answers[2]},
				{$answers[3]},
				{$answers[4]},
				{$answers[5]},
				{$answers[6]},
				{$answers[7]},
				{$answers[8]},
				{$answers[9]}, 
				'',
				'',
				'',
				'',
				'',
				'',
				'',
				'',
				'',
				'',
				5,
				0)";
		$result	= mysql_query($sql, $db);

		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		header('Location: question_db.php');
		exit;
	}
} else {
	$_POST['answer'] = 0;
}

$onload = 'document.form.category_id.focus();';

require(AT_INCLUDE_PATH.'header.inc.php');

$msg->printConfirm();
?>
<form action="tools/tests/create_question_multi.php" method="post" name="form">
<input type="hidden" name="required" value="1" />
<div class="input-form">
	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="cats"><?php echo _AT('category'); ?></label><br />
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
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="question"><?php echo _AT('question'); ?></label>
		<?php print_VE('question'); ?>
		<textarea id="question" cols="50" rows="4" name="question" style="width:90%;"><?php 
		echo htmlspecialchars(stripslashes($_POST['question'])); ?></textarea>
	</div>

<?php
	for ($i=0; $i<10; $i++) {
?>
	<div class="row">
		<?php echo _AT('choice'); ?> <?php echo ($i+1); ?>
		
		<?php print_VE('choice_' . $i); ?>
		
		<br />

		<small><input type="radio" name="answer" id="answer_<?php echo $i; ?>" value="<?php echo $i; ?>" <?php if($_POST['answer'] == $i) { echo 'checked="checked"';} ?>><label for="answer_<?php echo $i; ?>"><?php echo _AT('correct_answer'); ?></label></small>			
		
		<textarea id="choice_<?php echo $i; ?>" cols="50" rows="2" name="choice[<?php echo $i; ?>]"><?php 
		echo htmlspecialchars(stripslashes($_POST['choice'][$i])); ?></textarea> 
	</div>
	<?php } ?>

	<div class="row buttons">
		<input type="submit" value="<?php echo _AT('save'); ?>" name="submit" accesskey="s" />
		<input type="submit" value="<?php echo _AT('cancel'); ?>" name="cancel" />
	</div>
</div>
</form>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>