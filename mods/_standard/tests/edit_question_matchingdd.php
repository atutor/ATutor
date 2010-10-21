<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$
define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'../mods/_standard/tests/lib/likert_presets.inc.php');

authenticate(AT_PRIV_TESTS);
require(AT_INCLUDE_PATH.'../mods/_standard/tests/lib/test_result_functions.inc.php');

// for matching test questions
$_letters = array(_AT('A'), _AT('B'), _AT('C'), _AT('D'), _AT('E'), _AT('F'), _AT('G'), _AT('H'), _AT('I'), _AT('J'));


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
	$_POST['tid']          = intval($_POST['tid']);
	$_POST['qid']          = intval($_POST['qid']);
	$_POST['feedback']     = trim($_POST['feedback']);
	$_POST['instructions'] = trim($_POST['instructions']);
	$_POST['category_id']  = intval($_POST['category_id']);

	for ($i = 0 ; $i < 10; $i++) {
		$_POST['question'][$i]        = $addslashes(trim($_POST['question'][$i]));
		$_POST['question_answer'][$i] = (int) $_POST['question_answer'][$i];
		$_POST['answer'][$i]          = $addslashes(trim($_POST['answer'][$i]));
	}

	if ($_POST['question'][0] == ''
		|| $_POST['question'][1] == ''
		|| $_POST['answer'][0] == ''
		|| $_POST['answer'][1] == '') {

		$msg->addError('QUESTION_EMPTY');
	}

	if (!$msg->containsErrors()) {
		$_POST['feedback']     = $addslashes($_POST['feedback']);
		$_POST['instructions'] = $addslashes($_POST['instructions']);
		
		$sql	= "UPDATE ".TABLE_PREFIX."tests_questions SET
			category_id=$_POST[category_id],
			feedback='$_POST[feedback]',
			question='$_POST[instructions]',
			choice_0='{$_POST[question][0]}',
			choice_1='{$_POST[question][1]}',
			choice_2='{$_POST[question][2]}',
			choice_3='{$_POST[question][3]}',
			choice_4='{$_POST[question][4]}',
			choice_5='{$_POST[question][5]}',
			choice_6='{$_POST[question][6]}',
			choice_7='{$_POST[question][7]}',
			choice_8='{$_POST[question][8]}',
			choice_9='{$_POST[question][9]}',
			answer_0={$_POST[question_answer][0]},
			answer_1={$_POST[question_answer][1]},
			answer_2={$_POST[question_answer][2]},
			answer_3={$_POST[question_answer][3]},
			answer_4={$_POST[question_answer][4]},
			answer_5={$_POST[question_answer][5]},
			answer_6={$_POST[question_answer][6]},
			answer_7={$_POST[question_answer][7]},
			answer_8={$_POST[question_answer][8]},
			answer_9={$_POST[question_answer][9]},
			option_0='{$_POST[answer][0]}',
			option_1='{$_POST[answer][1]}',
			option_2='{$_POST[answer][2]}',
			option_3='{$_POST[answer][3]}',
			option_4='{$_POST[answer][4]}',
			option_5='{$_POST[answer][5]}',
			option_6='{$_POST[answer][6]}',
			option_7='{$_POST[answer][7]}',
			option_8='{$_POST[answer][8]}',
			option_9='{$_POST[answer][9]}'

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
	$sql	= "SELECT * FROM ".TABLE_PREFIX."tests_questions WHERE question_id=$qid AND course_id=$_SESSION[course_id] AND type=8";
	$result	= mysql_query($sql, $db);

	if (!($row = mysql_fetch_assoc($result))){
		require(AT_INCLUDE_PATH.'header.inc.php');
		$msg->printErrors('ITEM_NOT_FOUND');
		require (AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}
	$_POST['feedback']		= $row['feedback'];
	$_POST['instructions']	= $row['question'];
	$_POST['category_id']	= $row['category_id'];

	for ($i=0; $i<10; $i++) {
		$_POST['question'][$i]        = $row['choice_'.$i];
		$_POST['question_answer'][$i] = $row['answer_'.$i];
		$_POST['answer'][$i]          = $row['option_'.$i];
	}
	
}

require(AT_INCLUDE_PATH.'header.inc.php');
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="qid" value="<?php echo $qid; ?>" />
<input type="hidden" name="tid" value="<?php echo $_REQUEST['tid']; ?>" />


<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('test_matchingdd'); ?></legend>
	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="cats"><?php echo _AT('category'); ?></label><br />
		<select name="category_id" id="cats">
			<?php print_question_cats($_POST['category_id']); ?>
		</select>
	</div>

	<div class="row">
		<label for="optional_feedback"><?php echo _AT('optional_feedback'); ?></label> 
		<?php print_VE('optional_feedback'); ?>

		<textarea id="optional_feedback" cols="50" rows="3" name="feedback"><?php 
		echo htmlspecialchars(stripslashes($_POST['feedback'])); ?></textarea>
	</div>

	<div class="row">
		<label for="instructions"><?php echo _AT('instructions'); ?></label> 
		<?php print_VE('instructions'); ?>
		<textarea id="instructions" cols="50" rows="3" name="instructions"><?php 
		echo htmlspecialchars(stripslashes($_POST['instructions'])); ?></textarea>
	</div>

	<div class="row">
		<h2><?php echo _AT('questions');?></h2>
	</div>
<?php for ($i=0; $i<10; $i++): ?>
	<div class="row">
		<?php if ($i < 2) :?>
			<span class="required" title="<?php echo _AT('required_field'); ?>">*</span>
		<?php endif; ?>
		<?php echo _AT('question'); ?> <?php echo ($i+1); ?>
		
		<?php print_VE('question_' . $i); ?>
		
		<br />

		<select name="question_answer[<?php echo $i; ?>]">
			<option value="-1">-</option>
			<?php foreach ($_letters as $key => $value): ?>
				<option value="<?php echo $key; ?>" <?php if ($key == $_POST['question_answer'][$i]) { echo 'selected="selected"'; }?>><?php echo $value; ?></option>
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
				<span class="required" title="<?php echo _AT('required_field'); ?>">*</span>
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
	</fieldset>
</div>
</form>

<?php require (AT_INCLUDE_PATH.'footer.inc.php');  ?>