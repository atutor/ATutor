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
require(AT_INCLUDE_PATH.'lib/test_question_queries.inc.php');

authenticate(AT_PRIV_TESTS);
require(AT_INCLUDE_PATH.'lib/test_result_functions.inc.php');

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: question_db.php');
	exit;
} else if ($_POST['submit']) {
	$_POST['required']     = 1; //intval($_POST['required']);
	$_POST['feedback']     = trim($_POST['feedback']);
	$_POST['question']     = trim($_POST['question']);
	$_POST['category_id']  = intval($_POST['category_id']);
	$_POST['answer']       = intval($_POST['answer']);

	if ($_POST['question'] == ''){
		$msg->addError(array('EMPTY_FIELDS', _AT('statement')));
	}

	if (!$msg->containsErrors()) {
		$_POST['feedback'] = $addslashes($_POST['feedback']);
		$_POST['question'] = $addslashes($_POST['question']);

		/*
		$sql = 'SELECT content_id FROM '.TABLE_PREFIX."tests WHERE test_id=$_POST[tid]";
		$result = mysql_query($sql, $db);			
		$row = mysql_fetch_assoc($result);
		*/

		$sql_params = array(	$_POST['category_id'], 
								$_SESSION['course_id'],
								$_POST['feedback'], 
								$_POST['question'], 
								$_POST['answer']);

		$sql = vsprintf(AT_SQL_QUESTION_TRUEFALSE, $sql_params);
		$result	= mysql_query($sql, $db);
		
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		header('Location: question_db.php');
	}
}

$onload = 'document.form.category_id.focus();';
require(AT_INCLUDE_PATH.'header.inc.php');

?>
<form action="tools/tests/create_question_tf.php" method="post" name="form">
<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('test_tf'); ?></legend>
	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="cats"><?php echo _AT('category'); ?></label><br />
		<select name="category_id" id="cats">
			<?php print_question_cats($_POST['category_id']); ?>
		</select>
	</div>

	<div class="row">
		<label for="optional_feedback"><?php echo _AT('optional_feedback'); ?></label>		
		<?php print_VE('optional_feedback'); ?>
		<br />
	
		<textarea id="optional_feedback" cols="50" rows="3" name="feedback"><?php echo htmlspecialchars($stripslashes($_POST['feedback'])); ?></textarea>
	</div>
	
	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="question"><?php echo _AT('statement'); ?></label>
		<?php print_VE('question'); ?>
		<br />

		<textarea id="question" cols="50" rows="6" name="question" style="width:90%;"><?php echo htmlspecialchars($stripslashes($_POST['question'])); ?></textarea>
	</div>
	
	<div class="row">
		<?php echo _AT('answer'); ?><br />
		<input type="radio" name="answer" value="1" id="answer1" /><label for="answer1"><?php echo _AT('true'); ?></label>, 
		<input type="radio" name="answer" value="2" id="answer2" checked="checked" /><label for="answer2"><?php echo _AT('false'); ?></label>
	</div>

	<div class="row buttons">
		<input type="submit" value="<?php echo _AT('save'); ?>"   name="submit" accesskey="s" />
		<input type="submit" value="<?php echo _AT('cancel'); ?>" name="cancel" />
	</div>
	</fieldset>
</div>
</form>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>