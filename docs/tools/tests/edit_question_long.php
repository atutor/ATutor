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

$qid = intval($_GET['qid']);
if ($qid == 0){
	$qid = intval($_POST['qid']);
}


$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/';
$_section[1][0] = _AT('test_manager');
$_section[1][1] = 'tools/tests/';
$_section[2][0] = _AT('questions');
$_section[2][1] = 'tools/tests/questions.php?tid='.$tid;
$_section[3][0] = _AT('edit_question');

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: questions.php?tid='.$tid);
	exit;
} else if (isset($_POST['submit'])) {
	$_POST['required'] = intval($_POST['required']);
	$_POST['feedback'] = trim($_POST['feedback']);
	$_POST['question'] = trim($_POST['question']);
	$_POST['tid']	   = intval($_POST['tid']);
	$_POST['weight']   = intval($_POST['weight']);
	$_POST['answer_size'] = intval($_POST['answer_size']);

	if ($_POST['question'] == ''){
		$msg->addError('QUESTION_EMPTY');
	}

	if (!$msg->containsErrors()) {

		for ($i=0; $i<10; $i++) {
			$_POST['choice'][$i] = trim($_POST['choice'][$i]);
			$_POST['answer'][$i] = intval($_POST['answer'][$i]);

			if ($_POST['choice'][$i] == '') {
				/* an empty option can't be correct */
				$_POST['answer'][$i] = 0;
			}
		}

		$sql = "UPDATE ".TABLE_PREFIX."tests_questions SET	weight=$_POST[weight],
			required=$_POST[required],
			feedback='$_POST[feedback]',
			question='$_POST[question]',
			answer_size=$_POST[answer_size]
		WHERE question_id=$_POST[qid] AND test_id=$_POST[tid] AND course_id=$_SESSION[course_id]";

		$result	= mysql_query($sql, $db);

		$msg->addFeedback('QUESTION_UPDATED');
		Header('Location: questions.php?tid='.$_POST['tid'].SEP.'tt='.$_POST['tt']);
		exit;
	}
}

require(AT_INCLUDE_PATH.'header.inc.php');

/* get the test title */
$sql	= "SELECT * FROM ".TABLE_PREFIX."tests WHERE test_id=$tid AND course_id=$_SESSION[course_id]";
$result	= mysql_query($sql, $db);

if (!($row = mysql_fetch_assoc($result))){
	$msg->printErrors('TEST_NOT_FOUND');
	require (AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}
$test_title = $row['title'];
$automark   = $row['automark'];


if (!isset($_POST['submit'])) {
	$sql	= "SELECT * FROM ".TABLE_PREFIX."tests_questions WHERE question_id=$qid AND test_id=$tid AND course_id=$_SESSION[course_id] AND type=3";
	$result	= mysql_query($sql, $db);
	if (!($row = mysql_fetch_assoc($result))){
		$msg->printErrors('QUESTION_NOT_FOUND');
		require (AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}

	$_POST	= $row;
}

echo '<h2>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '<a href="tools/" class="hide"><img src="images/icons/default/square-large-tools.gif"  class="menuimageh2" border="0" vspace="2" width="42" height="40" alt="" /></a>';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo ' <a href="tools/" class="hide">'._AT('tools').'</a>';
	}
echo '</h2>';

echo '<h3>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '&nbsp;<img src="images/icons/default/test-manager-large.gif"  class="menuimageh3" width="42" height="38" alt="" /> ';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo '<a href="tools/tests/">'._AT('test_manager').'</a>';
	}
echo '</h3>';

echo '<h3><img src="/images/clr.gif" height="1" width="54" alt="" /><a href="tools/tests/questions.php?tid='.$_GET['tid'].'">'._AT('questions_for').' '.$test_title.'</a></h3>';

?>
<?php
	if ($_POST['required'] == 1) {
		$req_yes = ' checked="checked"';
	} else {
		$req_no  = ' checked="checked"';
	}

$msg->printErrors();
?>
<form action="tools/tests/edit_question_long.php" method="post" name="form">
	<input type="hidden" name="tid" value="<?php echo $tid; ?>" />
	<input type="hidden" name="qid" value="<?php echo $qid; ?>" />
	<input type="hidden" name="required" value="1" />

	<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center">
	<tr>
		<th colspan="2" class="left"><?php echo _AT('edit_open_question1'); ?></th>
	</tr>

	<?php if ($automark != AT_MARK_UNMARKED) { ?>
	<tr>
		<td class="row1" align="right"><label for="weight"><b><?php echo _AT('weight'); ?>:</b></label></td>
		<td class="row1"><input type="text" name="weight" id="weight" class="formfieldR" size="2" maxlength="2" value="<?php echo $_POST['weight']; ?>" /></td>
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
		<td class="row1" align="right" valign="top"><label for="ques"><b><?php echo _AT('question'); ?>:</b></label></td>
		<td class="row1"><textarea id="ques" cols="50" rows="6" name="question" class="formfield"><?php 
			echo htmlspecialchars(stripslashes($_POST['question'])); ?></textarea></td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1" align="right"><b><?php echo _AT('answer_size'); ?>:</b></td>
		<td class="row1"><input type="radio" name="answer_size" value="1" id="az1" <?php if ($_POST['answer_size'] == 1) { echo 'checked="checked"'; } ?> /><label for="az1"><?php echo _AT('one_word'); ?></label><br />
						<input type="radio" name="answer_size" value="2" id="az2" <?php if ($_POST['answer_size'] == 2) { echo 'checked="checked"'; } ?> /><label for="az2"><?php echo _AT('one_sentence'); ?></label><br />
						<input type="radio" name="answer_size" value="3" id="az3" <?php if ($_POST['answer_size'] == 3) { echo 'checked="checked"'; } ?> /><label for="az3"><?php echo _AT('short_paragraph'); ?></label><br />
						<input type="radio" name="answer_size" value="4" id="az4" <?php if ($_POST['answer_size'] == 4) { echo 'checked="checked"'; } ?> /><label for="az4"><?php echo _AT('one_page'); ?></label></td>
	</tr>
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