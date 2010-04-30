<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id: edit_question_ordering.php 8901 2009-11-11 19:10:19Z cindy $
define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'../mods/_standard/tests/lib/likert_presets.inc.php');

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
		$_POST['question'] = $addslashes($_POST['question']);
		$_POST['feedback'] = $addslashes($_POST['feedback']);

		$choice_new = array(); // stores the non-blank choices
		$answer_new = array(); // stores the non-blank answers
		$order = 0; // order count
		for ($i=0; $i<10; $i++) {
			/**
			 * Db defined it to be 255 length, chop strings off it it's less than that
			 * @harris
			 */
			$_POST['choice'][$i] = validate_length($_POST['choice'][$i], 255);
			$_POST['choice'][$i] = $addslashes(trim($_POST['choice'][$i]));

			if ($_POST['choice'][$i] != '') {
				/* filter out empty choices/ remove gaps */
				$choice_new[] = $_POST['choice'][$i];
				$answer_new[] = $order++;
			}
		}
		
		$_POST['choice']   = array_pad($choice_new, 10, '');
		$answer_new        = array_pad($answer_new, 10, 0);

		$sql	= "UPDATE ".TABLE_PREFIX."tests_questions SET
			category_id=$_POST[category_id],
			feedback='$_POST[feedback]',
			question='$_POST[question]',
			choice_0='{$_POST[choice][0]}',
			choice_1='{$_POST[choice][1]}',
			choice_2='{$_POST[choice][2]}',
			choice_3='{$_POST[choice][3]}',
			choice_4='{$_POST[choice][4]}',
			choice_5='{$_POST[choice][5]}',
			choice_6='{$_POST[choice][6]}',
			choice_7='{$_POST[choice][7]}',
			choice_8='{$_POST[choice][8]}',
			choice_9='{$_POST[choice][9]}',
			answer_0=$answer_new[0],
			answer_0=$answer_new[1],
			answer_0=$answer_new[2],
			answer_0=$answer_new[3],
			answer_0=$answer_new[4],
			answer_0=$answer_new[5],
			answer_0=$answer_new[6],
			answer_0=$answer_new[7],
			answer_0=$answer_new[8],
			answer_0=$answer_new[9]

			WHERE question_id=$_POST[qid] AND course_id=$_SESSION[course_id]";
		$result	= mysql_query($sql, $db);

		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		if ($_POST['tid']) {
			header('Location: questions.php?tid='.$_POST['tid']);			
		} else {
			header('Location: question_db.php');
		}
		exit;
	}
} else {
	$sql	= "SELECT * FROM ".TABLE_PREFIX."tests_questions WHERE question_id=$qid AND course_id=$_SESSION[course_id] AND type=6";
	$result	= mysql_query($sql, $db);

	if (!($row = mysql_fetch_assoc($result))){
		require(AT_INCLUDE_PATH.'header.inc.php');
		$msg->printErrors('ITEM_NOT_FOUND');
		require (AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}

	$_POST['required']		= $row['required'];
	$_POST['question']		= $row['question'];
	$_POST['category_id']	= $row['category_id'];
	$_POST['feedback']		= $row['feedback'];

	for ($i=0; $i<10; $i++) {
		$_POST['choice'][$i] = $row['choice_'.$i];
	}
}
require(AT_INCLUDE_PATH.'header.inc.php');
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="qid" value="<?php echo $qid; ?>" />
<input type="hidden" name="tid" value="<?php echo $_REQUEST['tid']; ?>" />

<div class="input-form">
	<div class="row">
		<label for="cats"><?php echo _AT('category'); ?></label><br />
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
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="question"><?php echo _AT('question'); ?></label> 
		
		<?php print_VE('question'); ?>
		
		<textarea id="question" cols="50" rows="6" name="question"><?php echo htmlspecialchars(stripslashes($_POST['question'])); ?></textarea>
	</div>

	<?php for ($i=0; $i<10; $i++): ?>
		<div class="row">
			<?php if ($i < 2): ?>
				<span class="required" title="<?php echo _AT('required_field'); ?>">*</span>
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

<?php require (AT_INCLUDE_PATH.'footer.inc.php');  ?>