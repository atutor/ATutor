<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
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
require(AT_INCLUDE_PATH.'lib/likert_presets.inc.php');
require(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

global $savant;
$msg =& new Message($savant);

authenticate(AT_PRIV_TEST_CREATE);

$tt = urldecode($_GET['tt']);
if($tt == ''){
	$tt = $_POST['tt'];
}

$tid = intval($_GET['tid']);
if ($tid == 0){
	$tid = intval($_POST['tid']);
}

$qid = intval($_GET['qid']);
if ($qid == 0){
	$qid = intval($_POST['qid']);
}

$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/index.php';
$_section[1][0] = _AT('test_manager');
$_section[1][1] = 'tools/tests/index.php';
$_section[2][0] = _AT('questions');
$_section[2][1] = 'tools/tests/questions.php?tid='.$tid;
$_section[3][0] = _AT('add_question');

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: questions.php?tid='.$tid);
	exit;
} else if (isset($_POST['submit'])) {
	$_POST['required'] = intval($_POST['required']);
	$_POST['question'] = trim($_POST['question']);
	$_POST['tid']	   = intval($_POST['tid']);

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
		/* avman */
		$sql = "SELECT content_id FROM ".TABLE_PREFIX."tests WHERE test_id= $_POST[tid]";
		$result = mysql_query($sql, $db);
		
		$content_id = mysql_fetch_array($result);
		
			$sql	= "UPDATE ".TABLE_PREFIX."tests_questions SET
				weight=0,
				required=$_POST[required],
				feedback='',
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

				WHERE question_id=$_POST[qid] AND test_id=$_POST[tid] AND course_id=$_SESSION[course_id]";
		$result	= mysql_query($sql, $db);

		$msg->addFeedback('QUESTION_ADDED');
		Header('Location: questions.php?tid='.$_POST['tid']):
		exit;
	}
} else if (isset($_POST['preset'])) {
	// load preset
	$_POST['preset_num'] = intval($_POST['preset_num']);

	if (isset($_likert_preset[$_POST['preset_num']])) {
		$_POST['choice'] = $_likert_preset[$_POST['preset_num']];
	} else if ($_POST['preset_num']) {
		$sql	= "SELECT * FROM ".TABLE_PREFIX."tests_questions WHERE question_id=$_POST[preset_num] AND course_id=$_SESSION[course_id]";
		$result	= mysql_query($sql, $db);
		if ($row = mysql_fetch_assoc($result)){
			for ($i=0; $i<10; $i++) {
				$_POST['choice'][$i] = $row['choice_' . $i];
			}
		}
	}

} else {
	$sql	= "SELECT * FROM ".TABLE_PREFIX."tests_questions WHERE question_id=$qid AND test_id=$tid AND course_id=$_SESSION[course_id] AND type=4";
	$result	= mysql_query($sql, $db);

	if (!($row = mysql_fetch_array($result))){
		$msg->printErrors('QUESTION_NOT_FOUND');
		require (AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}
	$_POST['required']	= $row['required'];
	$_POST['question']	= $row['question'];

	for ($i=0; $i<10; $i++) {
		$_POST['choice'][$i] = $row['choice_'.$i];
	}
}

/* get the test title: */
	$sql	= "SELECT title FROM ".TABLE_PREFIX."tests WHERE test_id=$tid AND course_id=$_SESSION[course_id]";
	$result	= mysql_query($sql, $db);

	if (!($row = mysql_fetch_assoc($result))){
		require(AT_INCLUDE_PATH.'header.inc.php');
		$msg->printErrors('TEST_NOT_FOUND');
		require (AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}
	
	$test_title = $row['title'];

require(AT_INCLUDE_PATH.'header.inc.php');

echo '<h2>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '<a href="tools/index.php" class="hide"><img src="images/icons/default/square-large-tools.gif"  class="menuimageh2" border="0" vspace="2" width="42" height="40" alt="" /></a>';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo ' <a href="tools/index.php" class="hide">'._AT('tools').'</a>';
	}
echo '</h2>';

echo '<h3>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '&nbsp;<a href="tools/tests/index.php"><img src="images/icons/default/test-manager-large.gif"  class="menuimageh3" width="42" height="38" alt="" border="0" /></a> ';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo '<a href="tools/tests/index.php">'._AT('test_manager').'</a>';
	}
echo '</h3>';

echo '<h3><img src="/images/clr.gif" height="1" width="54" alt="" /><a href="tools/tests/questions.php?tid='.$tid.'">'._AT('questions_for').' '.htmlspecialchars($test_title).'</a></h3>';
?>

<?php
$msg->printErrors(); ?>

<form action="tools/tests/edit_question_likert.php" method="post" name="form">
<input type="hidden" name="tid" value="<?php echo $tid; ?>" />
<input type="hidden" name="qid" value="<?php echo $qid; ?>" />
<input type="hidden" name="required" value="1" />
<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center">
<tr>
	<th class="left"><?php echo _AT('preset_scales'); ?> </th>
</tr>
<tr>
	<td class="row1" nowrap="nowrap">
		<select name="preset_num">
				<option value="0"></option>
			<optgroup label="<?php echo _AT('presets'); ?>"><?php
				// presets
				foreach ($_likert_preset as $val => $preset) {
					echo '<option value="'.$val.'">'.$preset[0].' - '.$preset[count($preset)-1].'</option>';
				}
			//previously used
			echo '</optgroup>';

			$sql = "SELECT * FROM ".TABLE_PREFIX."tests_questions WHERE course_id=$_SESSION[course_id] AND type=4";
			$result = mysql_query($sql, $db);
			if ($row = mysql_fetch_assoc($result)) {
				echo '<optgroup label="'. _AT('prev_used').'">';
				$used_choices = array();
				do {
					$choices = array_slice($row, 9, 10);
					if (in_array($choices, $used_choices)) {
						continue;
					}

					$used_choices[] = $choices;

					for ($i=0; $i<=10; $i++) {
						if ($row['choice_'.$i] == '') {
							$i--;
							break;
						}
					}
					echo '<option value="'.$row['question_id'].'">'.$row['choice_0'].' - '.$row['choice_'.$i].'</option>';
				} while ($row = mysql_fetch_assoc($result));
				echo '</optgroup>';
			}
		?>
		</select> 
		<input type="submit" name="preset" value="<?php echo _AT('set_preset'); ?>" class="button" />
	</td>
</tr>
</table>
<br />
<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center">
<tr>
	<th colspan="2" class="left"><?php print_popup_help(AT_HELP_ADD_LK_QUESTION);  ?><?php echo _AT('edit_lk_question'); ?> </th>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right" valign="top"><label for="ques"><b><?php echo _AT('question'); ?>:</b></label></td>
	<td class="row1"><textarea id="ques" cols="50" rows="6" name="question" class="formfield"><?php 
		echo htmlspecialchars(stripslashes($_POST['question'])); ?></textarea></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<?php for ($i=0; $i<10; $i++) { ?>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1" align="right" valign="top"><label for="choice_<?php echo $i; ?>"><b><?php echo _AT('choice'); ?> <?php
			echo ($i+1); ?>:</b></label></td>
		<td class="row1"><textarea id="choice_<?php echo $i; ?>" cols="50" rows="2" name="choice[<?php echo $i; ?>]" class="formfield"><?php echo htmlspecialchars(stripslashes($_POST['choice'][$i])); ?></textarea></td>
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

<?php require (AT_INCLUDE_PATH.'footer.inc.php');  ?>