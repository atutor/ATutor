<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay & Joel Kronenberg        */
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

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: question_db.php');
	exit;
} else if ($_POST['submit']) {
	$missing_fields = array();

	$_POST['feedback']    = trim($_POST['feedback']);
	$_POST['question']    = trim($_POST['question']);
	$_POST['category_id'] = intval($_POST['category_id']);

	if ($_POST['question'] == ''){
		$missing_fields[] = _AT('question');
	}

	if (trim($_POST['choice'][0]) == '') {
		$missing_fields[] = _AT('item').' 1';
	}
	if (trim($_POST['choice'][1]) == '') {
		$missing_fields[] = _AT('item').' 2';
	}

	if ($missing_fields) {
		$missing_fields = implode(', ', $missing_fields);
		$msg->addError(array('EMPTY_FIELDS', $missing_fields));
	}

	if (!$msg->containsErrors()) {
		$choice_new = array(); // stores the non-blank choices
		$answer_new = array(); // stores the non-blank answers
		$order = 0; // order count
		for ($i=0; $i<10; $i++) {
			$_POST['choice'][$i] = $addslashes(trim($_POST['choice'][$i]));

			if ($_POST['choice'][$i] != '') {
				/* filter out empty choices/ remove gaps */
				$choice_new[] = $_POST['choice'][$i];
				$answer_new[] = $order++;
			}
		}

		$_POST['choice']   = array_pad($choice_new, 10, '');
		$answer_new        = array_pad($answer_new, 10, 0);
		$_POST['feedback'] = $addslashes($_POST['feedback']);
		$_POST['question'] = $addslashes($_POST['question']);
	
		$sql	= "INSERT INTO ".TABLE_PREFIX."tests_questions VALUES (	NULL,
			$_POST[category_id],
			$_SESSION[course_id],
			".AT_TESTS_ORDERING.",
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
			$answer_new[0],
			$answer_new[1],
			$answer_new[2],
			$answer_new[3],
			$answer_new[4],
			$answer_new[5],
			$answer_new[6],
			$answer_new[7],
			$answer_new[8],
			$answer_new[9],
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

if (!isset($_POST['properties'])) {
	$_POST['properties'] = 1;
}

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
		<label for="feedback"><?php echo _AT('optional_feedback'); ?></label> 
		<?php print_VE('feedback'); ?>

		<textarea id="feedback" cols="50" rows="3" name="feedback"><?php 
		echo htmlspecialchars(stripslashes($_POST['feedback'])); ?></textarea>
	</div>

	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="question"><?php echo _AT('question'); ?></label> 
		<?php print_VE('question'); ?>
		<textarea id="question" cols="50" rows="6" name="question" style="width:90%;"><?php 
		echo htmlspecialchars(stripslashes($_POST['question'])); ?></textarea>
	</div>
	
	<?php for ($i=0; $i<10; $i++): ?>
		<div class="row">
			<?php if ($i < 2): ?>
				<div class="required" title="<?php echo _AT('required_field'); ?>">*</div>
			<?php endif; ?> <?php echo _AT('item'); ?> <?php echo ($i+1); ?>
			
			<?php print_VE('choice_' . $i); ?>
			
			<br />
	
			<textarea id="choice_<?php echo $i; ?>" cols="50" rows="2" name="choice[<?php echo $i; ?>]"><?php 
			echo htmlspecialchars(stripslashes($_POST['choice'][$i])); ?></textarea> 
		</div>
	<?php endfor; ?>
	
	<div class="row buttons">
		<input type="submit" value="<?php echo _AT('save'); ?>"   name="submit" accesskey="s" />
		<input type="submit" value="<?php echo _AT('cancel'); ?>" name="cancel" />
	</div>
</div>
</form>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>