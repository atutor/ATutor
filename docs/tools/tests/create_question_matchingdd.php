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
// $Id: create_question_matching.php 6706 2007-02-01 16:28:49Z joel $

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_TESTS);
require(AT_INCLUDE_PATH.'lib/test_result_functions.inc.php');

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: question_db.php');
	exit;
} else if ($_POST['submit']) {
	$_POST['feedback']    = trim($_POST['feedback']);
	$_POST['instructions'] = trim($_POST['instructions']);
	$_POST['category_id'] = intval($_POST['category_id']);

	for ($i = 0 ; $i < 10; $i++) {
		$_POST['question'][$i]        = $addslashes(trim($_POST['question'][$i]));
		$_POST['question_answer'][$i] = (int) $_POST['question_answer'][$i];
		$_POST['answer'][$i]          = $addslashes(trim($_POST['answer'][$i]));
	}

	if (!$_POST['question'][0] 
		|| !$_POST['question'][1] 
		|| !$_POST['answer'][0] 
		|| !$_POST['answer'][1]) {

		$msg->addError('QUESTION_EMPTY');
	}
	

	if (!$msg->containsErrors()) {
		$_POST['feedback']     = $addslashes($_POST['feedback']);
		$_POST['instructions'] = $addslashes($_POST['instructions']);
	
		$sql	= "INSERT INTO ".TABLE_PREFIX."tests_questions VALUES (	NULL,
			$_POST[category_id],
			$_SESSION[course_id],
			8,
			'$_POST[feedback]',
			'$_POST[instructions]',
			'{$_POST[question][0]}',
			'{$_POST[question][1]}',
			'{$_POST[question][2]}',
			'{$_POST[question][3]}',
			'{$_POST[question][4]}',
			'{$_POST[question][5]}',
			'{$_POST[question][6]}',
			'{$_POST[question][7]}',
			'{$_POST[question][8]}',
			'{$_POST[question][9]}',
			{$_POST[question_answer][0]},
			{$_POST[question_answer][1]},
			{$_POST[question_answer][2]},
			{$_POST[question_answer][3]},
			{$_POST[question_answer][4]},
			{$_POST[question_answer][5]},
			{$_POST[question_answer][6]},
			{$_POST[question_answer][7]},
			{$_POST[question_answer][8]},
			{$_POST[question_answer][9]},
			'{$_POST[answer][0]}',
			'{$_POST[answer][1]}',
			'{$_POST[answer][2]}',
			'{$_POST[answer][3]}',
			'{$_POST[answer][4]}',
			'{$_POST[answer][5]}',
			'{$_POST[answer][6]}',
			'{$_POST[answer][7]}',
			'{$_POST[answer][8]}',
			'{$_POST[answer][9]}',
			0,
			0)";

		$result	= mysql_query($sql, $db);

		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		header('Location: question_db.php');
		exit;
	}
}

$onload = 'document.form.category_id.focus();';

require(AT_INCLUDE_PATH.'header.inc.php');

// for matching test questions
$_letters = array(_AT('A'), _AT('B'), _AT('C'), _AT('D'), _AT('E'), _AT('F'), _AT('G'), _AT('H'), _AT('I'), _AT('J'));

?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
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
		<label for="instructions"><?php echo _AT('instructions'); ?></label> 
		<?php print_VE('instructions'); ?>
		<textarea id="instructions" cols="50" rows="3" name="instructions"><?php echo htmlspecialchars(stripslashes($_POST['instructions'])); ?></textarea>
	</div>

	<div class="row">
		<h2><?php echo _AT('questions');?></h2>
	</div>
<?php for ($i=0; $i<10; $i++): ?>
	<div class="row">
		<?php if ($i < 2) :?>
			<div class="required" title="<?php echo _AT('required_field'); ?>">*</div>
		<?php endif; ?>
		<?php echo _AT('question'); ?> <?php echo ($i+1); ?>
		
		<?php print_VE('question_' . $i); ?>
		
		<br />

		<select name="question_answer[<?php echo $i; ?>]">
			<option value="-1">-</option>
			<?php foreach ($_letters as $key => $value): ?>
				<option value="<?php echo $key; ?>"><?php echo $value; ?></option>
			<?php endforeach; ?>
		</select>
		
		<textarea id="question_<?php echo $i; ?>" cols="50" rows="2" name="question[<?php echo $i; ?>]"><?php 
		echo htmlspecialchars(stripslashes($_POST['question'][$i])); ?></textarea> 
	</div>
<?php endfor; ?>
	
	<div class="row">
		<h2><?php echo _AT('answers');?></h2>
	</div>
	<?php for ($i=0; $i<10; $i++): ?>
		<div class="row">
			<?php if ($i < 2) :?>
				<div class="required" title="<?php echo _AT('required_field'); ?>">*</div>
			<?php endif; ?>
			<?php echo _AT('answer'); ?> <?php echo $_letters[$i]; ?>
			<?php print_VE('answer_' . $i); ?>
			<br />
			<textarea id="answer_<?php echo $i; ?>" cols="50" rows="2" name="answer[<?php echo $i; ?>]"><?php 
			echo htmlspecialchars(stripslashes($_POST['answer'][$i])); ?></textarea>
		</div>
	<?php endfor; ?>

	<div class="row buttons">
		<input type="submit" value="<?php echo _AT('save'); ?>"   name="submit" accesskey="s" />
		<input type="submit" value="<?php echo _AT('cancel'); ?>" name="cancel" />
	</div>
</div>
</form>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>