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
		$_POST['required'] = intval($_POST['required']);
		$_POST['feedback'] = trim($_POST['feedback']);
		$_POST['question'] = trim($_POST['question']);
		$_POST['tid']	   = intval($_POST['tid']);
		$_POST['weight']   = intval($_POST['weight']);

		if ($_POST['question'] == ''){
			$msg->addError('QUESTION_EMPTY');
		}
			
		if (!$msg->containsErrors()) {
			$choice_new = array(); // stores the non-blank choices
			$answer_new = array(); // stores the associated "answer" for the choices
			for ($i=0; $i<10; $i++) {
				$_POST['choice'][$i] = $addslashes(trim($_POST['choice'][$i]));
				$_POST['answer'][$i] = intval($_POST['answer'][$i]);

				if ($_POST['choice'][$i] == '') {
					/* an empty option can't be correct */
					$_POST['answer'][$i] = 0;
				} else {
					/* filter out empty choices/ remove gaps */
					$choice_new[] = $_POST['choice'][$i];
					$answer_new[] = $_POST['answer'][$i];
				}
			}
			
			//add slahes throughout - does that fix it?
			$_POST['answer'] = $answer_new;
			$_POST['choice'] = $choice_new;
			$_POST['answer'] = array_pad($_POST['answer'], 10, 0);
			$_POST['choice'] = array_pad($_POST['choice'], 10, '');
			
			$_POST['feedback'] = $addslashes($_POST['feedback']);
			$_POST['question'] = $addslashes($_POST['question']);

			$sql	= "INSERT INTO ".TABLE_PREFIX."tests_questions VALUES (	0, 
				$_POST[tid],
				$_SESSION[course_id],
				0,
				1,
				$_POST[weight],
				$_POST[required],
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
				{$_POST[answer][0]},
				{$_POST[answer][1]},
				{$_POST[answer][2]},
				{$_POST[answer][3]},
				{$_POST[answer][4]},
				{$_POST[answer][5]},
				{$_POST[answer][6]},
				{$_POST[answer][7]},
				{$_POST[answer][8]},
				{$_POST[answer][9]},
				0,
				0)";

			$result	= mysql_query($sql, $db);

			$msg->addFeedback('QUESTION_ADDED');
			Header('Location: questions.php?tid='.$_POST['tid']);
			exit;
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

echo '<h3><img src="/images/clr.gif" height="1" width="54" alt="" /><a href="tools/tests/questions.php?tid='.$tid.'">'._AT('questions_for').' '.$test_title.'</a></h3>';
?>
<?php $msg->printErrors(); ?>

<form action="tools/tests/add_question_multi.php" method="post" name="form">
	<input type="hidden" name="tid" value="<?php echo $tid; ?>" />
	<input type="hidden" name="automark" value="<?php echo $_POST['automark']; ?>" />
	<input type="hidden" name="required" value="1" />

	<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center">
	<tr>
		<th colspan="2" class="left"><?php print_popup_help('ADD_MC_QUESTION');  ?><?php echo _AT('new_mc_question'); ?> </th>
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
	<?php } ?>

	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1" align="right" valign="top"><label for="ques"><b><?php echo _AT('question'); ?>:</b></label></td>
		<td class="row1"><textarea id="ques" cols="50" rows="6" name="question" class="formfield"><?php 
			echo htmlspecialchars(stripslashes($_POST['question'])); ?></textarea></td>
	</tr>
	<?php for ($i=0; $i<10; $i++) { ?>
		<tr><td height="1" class="row2" colspan="2"></td></tr>
		<tr>
			<td class="row1" align="right" valign="top"><label for="choice_<?php echo $i; ?>"><b><?php echo _AT('choice'); ?> <?php
				echo ($i+1); ?>:</b></label>
				<?php if ($automark != AT_MARK_UNMARKED) { ?>
					<br />
					<select name="answer[<?php echo $i; ?>]">
						<option value="0"><?php echo _AT('wrong_answer'); ?></option>
						<option value="1"><?php echo _AT('correct_answer'); ?></option>
					</select>
				<?php } ?>

			</td>
			<td class="row1"><textarea id="choice_<?php echo $i; ?>" cols="50" rows="4" name="choice[<?php echo $i; ?>]" class="formfield"><?php echo htmlspecialchars(stripslashes($_POST['choice'][$i])); ?></textarea></td>
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