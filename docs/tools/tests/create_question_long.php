<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

$page = 'tests';
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_TEST_CREATE);
require(AT_INCLUDE_PATH.'lib/test_result_functions.inc.php');

$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/';
$_section[1][0] = _AT('test_manager');
$_section[1][1] = 'tools/tests/';
$_section[2][0] = _AT('question_database');
$_section[2][1] = 'tools/tests/question_db.php';
$_section[3][0] = _AT('new_open_question');

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: question_db.php');
	exit;
} else if ($_POST['submit']) {
	$_POST['feedback']    = trim($_POST['feedback']);
	$_POST['question']    = trim($_POST['question']);
	$_POST['category_id'] = intval($_POST['category_id']);
	$_POST['properties']  = intval($_POST['properties']);

	if ($_POST['question'] == ''){
		$msg->addError('QUESTION_EMPTY');
	}

	if (!$msg->containsErrors()) {
		$_POST['feedback'] = $addslashes($_POST['feedback']);
		$_POST['question'] = $addslashes($_POST['question']);
	
		$sql	= "INSERT INTO ".TABLE_PREFIX."tests_questions VALUES (	0,
			$_POST[category_id],
			$_SESSION[course_id],
			3,
			'$_POST[feedback]',
			'$_POST[question]',
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
			0,
			0,
			0,
			0,
			0,
			0,
			0,
			0,
			0,
			$_POST[properties],
			0)";
		$result	= mysql_query($sql, $db);

		$msg->addFeedback('QUESTION_ADDED');
		header('Location: question_db.php');
		exit;
	}
} 

require(AT_INCLUDE_PATH.'header.inc.php');

if (!isset($_POST['properties'])) {
	$_POST['properties'] = 1;
}

$msg->addHelp('QUESTION_LONG');
$msg->printAll(); 

?>
<form action="tools/tests/create_question_long.php" method="post" name="form">
<input type="hidden" name="required" value="1" />
<div class="input-form">
	<div class="row">
		<label for="cats"><?php echo _AT('category'); ?></label><br />
		<select name="category_id" id="cats">
			<?php print_question_cats($_POST['category_id']); ?>
		</select>
	</div>

	<div class="row">
		<label for="feedback"><?php echo _AT('optional_feedback'); ?></label><br />
		<a onclick="javascript:window.open('<?php echo $_base_href; ?>/tools/tests/form_editor.php?area=feedback','newWin1','toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,copyhistory=0,width=640,height=480')" style="cursor: pointer" ><?php echo _AT('use_visual_editor'); ?></a>
		<textarea id="feedback" cols="50" rows="3" name="feedback"><?php 
		echo htmlspecialchars(stripslashes($_POST['feedback'])); ?></textarea>
	</div>

	<div class="row">
		<label for="question"><?php echo _AT('question'); ?></label><br />
		<a onclick="javascript:window.open('<?php echo $_base_href; ?>/tools/tests/form_editor.php?area=question','newWin1','toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,copyhistory=0,width=640,height=480')" style="cursor: pointer" ><?php echo _AT('use_visual_editor'); ?></a>
		
		<textarea id="question" cols="50" rows="6" name="question"><?php 
		echo htmlspecialchars(stripslashes($_POST['question'])); ?></textarea>
	</div>
	
	<div class="row">
		<?php echo _AT('answer_size'); ?><br />
		<input type="radio" name="properties" value="1" id="az1" <?php if ($_POST['properties'] == 1) { echo 'checked="checked"'; } ?> /><label for="az1"><?php echo _AT('one_word'); ?></label><br />
		
		<input type="radio" name="properties" value="2" id="az2" <?php if ($_POST['properties'] == 2) { echo 'checked="checked"'; } ?> /><label for="az2"><?php echo _AT('one_sentence'); ?></label><br />
		
		<input type="radio" name="properties" value="3" id="az3" <?php if ($_POST['properties'] == 3) { echo 'checked="checked"'; } ?> /><label for="az3"><?php echo _AT('short_paragraph'); ?></label><br />
		
		<input type="radio" name="properties" value="4" id="az4" <?php if ($_POST['properties'] == 4) { echo 'checked="checked"'; } ?> /><label for="az4"><?php echo _AT('one_page'); ?></label>
	</div>
	
	<div class="row buttons">
		<input type="submit" value="<?php echo _AT('save'); ?>"   name="submit" accesskey="s" />
		<input type="submit" value="<?php echo _AT('cancel'); ?>" name="cancel" />
	</div>
</div>
</form>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>