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
// $Id$
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_TESTS);

require(AT_INCLUDE_PATH.'lib/test_result_functions.inc.php');

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
	$_POST['required'] = intval($_POST['required']);
	$_POST['feedback'] = trim($_POST['feedback']);
	$_POST['question'] = trim($_POST['question']);
	$_POST['tid']	   = intval($_POST['tid']);
	$_POST['qid']	   = intval($_POST['qid']);
	$_POST['weight']   = intval($_POST['weight']);
	$_POST['answer']   = intval($_POST['answer']);

	if ($_POST['question'] == ''){
		$msg->addError(array('EMPTY_FIELDS', _AT('question')));
	}

	if (!$msg->containsErrors()) {
		$answers    = array_fill(0, 10, 0);
		$answers[$_POST['answer']] = 1;

		for ($i=0; $i<10; $i++) {
			$_POST['choice'][$i] = $addslashes(trim($_POST['choice'][$i]));
		}

		$_POST['feedback']   = $addslashes($_POST['feedback']);
		$_POST['question']   = $addslashes($_POST['question']);

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
			answer_0={$answers[0]},
			answer_1={$answers[1]},
			answer_2={$answers[2]},
			answer_3={$answers[3]},
			answer_4={$answers[4]},
			answer_5={$answers[5]},
			answer_6={$answers[6]},
			answer_7={$answers[7]},
			answer_8={$answers[8]},
			answer_9={$answers[9]}

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

if (!isset($_POST['submit'])) {
	$sql	= "SELECT * FROM ".TABLE_PREFIX."tests_questions WHERE question_id=$qid AND course_id=$_SESSION[course_id] AND type=1";
	$result	= mysql_query($sql, $db);

	if (!($row = mysql_fetch_array($result))){
		require(AT_INCLUDE_PATH.'header.inc.php');
		$msg->printErrors('ITEM_NOT_FOUND');
		require (AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}
	$_POST['category_id'] = $row['category_id'];
	$_POST['feedback']	  = $row['feedback'];
	$_POST['required']	  = $row['required'];
	$_POST['weight']	  = $row['weight'];
	$_POST['question']	  = $row['question'];

	for ($i=0; $i<10; $i++) {
		$_POST['choice'][$i] = $row['choice_'.$i];
		$_POST['answer'][$i] = $row['answer_'.$i];
	}
}

require(AT_INCLUDE_PATH.'header.inc.php');

?>
<form action="tools/tests/edit_question_multi.php" method="post" name="form">
<input type="hidden" name="tid" value="<?php echo $_REQUEST['tid']; ?>" />
<input type="hidden" name="qid" value="<?php echo $qid; ?>" />
<input type="hidden" name="required" value="1" />

<div class="input-form">
	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="cats"><?php echo _AT('category'); ?></label>
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
		<textarea id="question" cols="50" rows="4" name="question"><?php 
			echo htmlspecialchars(stripslashes($_POST['question'])); ?></textarea>
	</div>

	<?php 
	for ($i=0; $i<10; $i++) { ?>
		<div class="row">
			<label for="choice_<?php echo $i; ?>"><?php echo _AT('choice'); ?> <?php echo ($i+1); ?></label> 
			<?php print_VE('choice_' . $i); ?>
			<br />
			<small><input type="radio" name="answer" id="answer_<?php echo $i; ?>" value="<?php echo $i; ?>" <?php if($_POST['answer'][$i]) { echo 'checked="checked"';} ?>><label for="answer_<?php echo $i; ?>"><?php echo _AT('correct_answer'); ?></label></small>
			

			<textarea id="choice_<?php echo $i; ?>" cols="50" rows="2" name="choice[<?php echo $i; ?>]" class="formfield"><?php echo htmlspecialchars(stripslashes($_POST['choice'][$i])); ?></textarea>
		</div>
	<?php } ?>

	<div class="row buttons">
		<input type="submit" value="<?php echo _AT('save'); ?>"   name="submit" accesskey="s" />
		<input type="submit" value="<?php echo _AT('cancel'); ?>" name="cancel" />
	</div>
</div>
</form>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>