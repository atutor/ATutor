<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
	$page = 'tests';
	define('AT_INCLUDE_PATH', '../../include/');
	require(AT_INCLUDE_PATH.'vitals.inc.php');
	require(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

	global $savant;
	$msg =& new Message($savant);

	authenticate(AT_PRIV_TEST_CREATE);

	$tid = intval($_GET['tid']);
	if ($tid == 0){
		$tid = intval($_POST['tid']);
	}

	$_section[0][0] = _AT('tools');
	$_section[0][1] = 'tools/';
	$_section[1][0] = _AT('test_manager');
	$_section[1][1] = 'tools/tests/';
	$_section[2][0] = _AT('questions');
	$_section[2][1] = 'tools/tests/questions.php?tid='.$tid;
	$_section[3][0] = _AT('add_question');

	if (isset($_POST['cancel'])) {
		$msg->addFeedback('CANCELLED');
		header('Location: questions.php?tid='.$tid);
		exit;
	} else if ($_POST['submit']) {
		$_POST['required'] = 1; //intval($_POST['required']);
		$_POST['feedback'] = trim($_POST['feedback']);
		$_POST['question'] = trim($_POST['question']);
		$_POST['tid']	   = intval($_POST['tid']);
		$_POST['weight']   = intval($_POST['weight']);
		$_POST['answer']   = intval($_POST['answer']);

		if ($_POST['question'] == ''){
			$msg->addError('QUESTION_EMPTY');
		}

		if (!$msg->containsErrors()) {
			$_POST['feedback'] = $addslashes($_POST['feedback']);
			$_POST['question'] = $addslashes($_POST['question']);

			/*
			$sql = 'SELECT content_id FROM '.TABLE_PREFIX."tests WHERE test_id=$_POST[tid]";
            $result = mysql_query($sql, $db);			
			$row = mysql_fetch_assoc($result);
			*/

			$sql = "INSERT INTO ".TABLE_PREFIX."tests_questions VALUES (	0,
				$_POST[tid],
				$_SESSION[course_id],
				0,
				2,
				$_POST[weight],
				$_POST[required],
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
				$_POST[answer],
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
				0)";
			$result	= mysql_query($sql, $db);
			
			$msg->addFeedback('QUESTION_ADDED');
			header('Location: questions.php?tid='.$tid);
		}
	}

$sql	= "SELECT * FROM ".TABLE_PREFIX."tests WHERE test_id=$tid AND course_id=$_SESSION[course_id]";
$result	= mysql_query($sql, $db);
if (!($row = mysql_fetch_array($result))){
	$msg->printErrors('TEST_NOT_FOUND');
	require (AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

$test_title = $row['title'];
$automark   = $row['automark'];


	require(AT_INCLUDE_PATH.'header.inc.php');

echo '<h2>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '<a href="tools/index.php" class="hide"><img src="images/icons/default/square-large-tools.gif"  class="menuimageh2" border="0" vspace="2" width="42" height="40" alt="" /></a>';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo ' <a href="tools/" class="hide">'._AT('tools').'</a>';
	}
echo '</h2>';

echo '<h3>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '&nbsp;<a href="tools/tests"><img src="images/icons/default/test-manager-large.gif" class="menuimageh3" width="42" height="38" alt="" border="0" /></a> ';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo '<a href="tools/tests/">'._AT('test_manager').'</a>';
	}
echo '</h3>';

echo '<h3><img src="/images/clr.gif" height="1" width="54" alt="" /><a href="tools/tests/questions.php?tid='.$tid.'">'._AT('questions_for').' '.AT_print($test_title, 'tests.title').'</a></h3>';

?>

<?php $msg->printErrors(); ?>

<form action="tools/tests/add_question_tf.php" method="post" name="form">
	<input type="hidden" name="tid" value="<?php echo $tid; ?>" />
	<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center">
	<tr>
		<th colspan="2" class="left"><?php print_popup_help('ADD_TF_QUESTION');  ?> <?php echo _AT('new_tf_question'); ?></th>
	</tr>
	<?php if ($automark != AT_MARK_UNMARKED) { ?>
	<tr>
		<td class="row1" align="right"><label for="weight"><b><?php echo _AT('weight'); ?>:</b></label></td>
		<td class="row1"><input type="text" value="5" name="weight" id="weight" class="formfieldR" size="2" maxlength="2" /></td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1" align="right" valign="top"><label for="feedback"><b><?php echo _AT('feedback'); ?>:</b></label></td>
		<td class="row1"><textarea id="feedback" cols="50" rows="3" name="feedback" class="formfield"><?php 
			echo htmlspecialchars(stripslashes($_POST['feedback'])); ?></textarea></td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<?php } ?>

	<tr>
		<td class="row1" align="right" valign="top"><label for="ques"><b><?php echo _AT('statement'); ?>:</b></label></td>
		<td class="row1"><textarea id="ques" cols="50" rows="6" name="question" class="formfield"><?php 
			echo htmlspecialchars(stripslashes($_POST['question'])); ?></textarea></td>
	</tr>

	<?php if ($automark != AT_MARK_UNMARKED) { ?>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1" align="right"><b><?php echo _AT('answer'); ?>:</b></td>
		<td class="row1"><input type="radio" name="answer" value="1" id="answer1" /><label for="answer1"><?php echo _AT('true'); ?></label>, <input type="radio" name="answer" value="2" id="answer2" checked="checked" /><label for="answer2"><?php echo _AT('false'); ?></label></td>
	</tr>
	<?php } ?>

	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1" colspan="2" align="center"><input type="submit" value="<?php echo _AT('save_test_question'); ?> Alt-s" class="button" name="submit" accesskey="s" /> - <input type="submit" value="<?php echo _AT('cancel'); ?>" class="button" name="cancel" /></td>
	</tr>
	</table>
	<br />
	<br />
</form>

<?php
	require (AT_INCLUDE_PATH.'footer.inc.php');
?>