<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$
$page = 'tests';
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

$msg =& new Message($savant);

authenticate(AT_PRIV_TEST_CREATE);

require(AT_INCLUDE_PATH.'lib/test_result_functions.inc.php');

$qid = intval($_GET['qid']);
if ($qid == 0){
	$qid = intval($_POST['qid']);
}

$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/';
$_section[1][0] = _AT('test_manager');
$_section[1][1] = 'tools/tests/';
$_section[2][0] = _AT('question_database');
$_section[2][1] = 'tools/tests/question_db.php';
$_section[3][0] = _AT('edit_question');

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

		$_POST['answer'] = $answer_new;
		$_POST['choice'] = $choice_new;
		$_POST['answer'] = array_pad($_POST['answer'], 10, 0);
		$_POST['choice'] = array_pad($_POST['choice'], 10, '');

		$_POST['feedback']  = $addslashes($_POST['feedback']);
		$_POST['question']  = $addslashes($_POST['question']);
		$_POST['properties'] = $addslashes($_POST['properties']);

		$sql	= "UPDATE ".TABLE_PREFIX."tests_questions SET
            category_id=$_POST[category_id],
		    feedback='$_POST[feedback]',
			question='$_POST[question]',
			properties='$_POST[properties]',
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
			answer_0={$_POST[answer][0]},
			answer_1={$_POST[answer][1]},
			answer_2={$_POST[answer][2]},
			answer_3={$_POST[answer][3]},
			answer_4={$_POST[answer][4]},
			answer_5={$_POST[answer][5]},
			answer_6={$_POST[answer][6]},
			answer_7={$_POST[answer][7]},
			answer_8={$_POST[answer][8]},
			answer_9={$_POST[answer][9]}

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
		$msg->printErrors('QUESTION_NOT_FOUND');
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

	$_POST['properties'] = $row['properties'];
	if ($_POST['properties'] == AT_TESTS_OPT_ALIGN_VERT) {
		$align_vert = ' checked="checked"';
	} else {
		$align_hor  = ' checked="checked"';
	}
}

require(AT_INCLUDE_PATH.'header.inc.php');

echo '<h2>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '<a href="tools/" class="hide"><img src="images/icons/default/square-large-tools.gif" class="menuimageh2" border="0" vspace="2" width="42" height="40" alt="" /></a>';
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
?>
<?php if (isset($_REQUEST['tid'])): ?>
	<h3><img src="images/clr.gif" height="1" width="54" alt="" /><a href="tools/tests/questions.php?tid=<?php echo $_REQUEST['tid']; ?>"><?php echo _AT('questions'); ?></a></h3>
<?php else: ?>
	<h3><img src="images/clr.gif" height="1" width="54" alt="" /><a href="tools/tests/question_db.php"><?php echo _AT('question_database'); ?></a></h3>
<?php endif; ?>
<?php

$msg->printErrors();
?>
<form action="tools/tests/edit_question_multi.php" method="post" name="form">
	<input type="hidden" name="tid" value="<?php echo $_REQUEST['tid']; ?>" />
	<input type="hidden" name="qid" value="<?php echo $qid; ?>" />
	<input type="hidden" name="required" value="1" />

	<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center">
	<tr>
		<th colspan="2" class="left"><?php echo _AT('edit_mc_question2'); ?></th>
	</tr>
	<tr>
		<td class="row1" align="right" valign="top"><label for="cats"><b><?php echo _AT('category'); ?>:</b></label></td>
		<td class="row1">
			<select name="category_id" id="cats">
			<?php print_question_cats($_POST['category_id']); ?>
			</select>
		</td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1" align="right" valign="top">
			<label for="feedback"><b><?php echo _AT('optional_feedback'); ?>:</b></label>
			<br />
			<a onclick="javascript:window.open('<?php echo $_base_href; ?>/tools/tests/form_editor.php?area=feedback','newWin1','toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,copyhistory=0,width=640,height=480')" style="cursor: pointer" ><?php echo _AT('use_visual_editor'); ?></a>		
		</td>
		<td class="row1"><textarea id="feedback" cols="50" rows="3" name="feedback" class="formfield"><?php 
			echo htmlspecialchars(stripslashes($_POST['feedback'])); ?></textarea></td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1" align="right" valign="top">
			<label for="question"><b><?php echo _AT('question'); ?>:</b></label>
			<br />
			<a onclick="javascript:window.open('<?php echo $_base_href; ?>/tools/tests/form_editor.php?area=question','newWin1','toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,copyhistory=0,width=640,height=480')" style="cursor: pointer" ><?php echo _AT('use_visual_editor'); ?></a>
		</td>
		<td class="row1"><textarea id="question" cols="50" rows="4" name="question" class="formfield"><?php 
			echo htmlspecialchars(stripslashes($_POST['question'])); ?></textarea></td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1" align="right" valign="top"><label for="cats"><b><?php echo _AT('option_alignment'); ?>:</b></label></td>
		<td class="row1">
			<label><input type="radio" name="properties" value="5"<?php echo $align_vert; ?> /><?php echo _AT('vertical'); ?></label>
			<label><input type="radio" name="properties" value="6" <?php echo $align_hor;  ?> /><?php echo _AT('horizontal'); ?></label>
		</td>
	</tr>
	<?php for ($i=0; $i<10; $i++) { ?>
<tr><td height="1" class="row2" colspan="2"></td></tr>
		<tr>
			<td class="row1" align="right" valign="top"><label for="choice_<?php echo $i; ?>"><b><?php echo _AT('choice'); ?> <?php
				echo ($i+1); ?>:</b></label>				
				<br />
				<small><input type="checkbox" name="answer[<?php echo $i; ?>]" id="answer_<?php echo $i; ?>" value="1" <?php if($_POST['answer'][$i]) { echo 'checked="checked"';} ?>><label for="answer_<?php echo $i; ?>"><?php echo _AT('correct_answer'); ?></label></small>
				<br />
				<a onclick="javascript:window.open('<?php echo $_base_href; ?>/tools/tests/form_editor.php?area=<?php echo 'choice_' . $i; ?>','newWin1','toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,copyhistory=0,width=640,height=480')" style="cursor: pointer" ><?php echo _AT('use_visual_editor'); ?></a>
			</td>
			<td class="row1"><textarea id="choice_<?php echo $i; ?>" cols="50" rows="2" name="choice[<?php echo $i; ?>]" class="formfield"><?php echo htmlspecialchars(stripslashes($_POST['choice'][$i])); ?></textarea></td>
		</tr>
	<?php } ?>

	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1" colspan="2" align="center"><input type="submit" value="<?php echo _AT('save'); ?> Alt-s" class="button" name="submit" accesskey="s" /> - <input type="submit" value="<?php echo _AT('cancel'); ?>" class="button" name="cancel" /></td>
	</tr>
	</table>
	<br />
	<br />
</form>
<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>