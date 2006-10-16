<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
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
require(AT_INCLUDE_PATH.'lib/likert_presets.inc.php');

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
	$_POST['required']    = intval($_POST['required']);
	$_POST['question']    = trim($_POST['question']);
	$_POST['category_id'] = intval($_POST['category_id']);
	$_POST['alignment']   = intval($_POST['alignment']);

	if ($_POST['question'] == ''){
		$msg->addError('QUESTION_EMPTY');
	}
	if (($_POST['choice'][0] == '') || ($_POST['choice'][1] == '')){
		$msg->addError('CHOICES_EMPTY');
	}
	if (!$msg->containsErrors()) {
		$_POST['question'] = $addslashes($_POST['question']);

		for ($i=0; $i<10; $i++) {
			$_POST['choice'][$i] = trim($_POST['choice'][$i]);
			$_POST['answer'][$i] = intval($_POST['answer'][$i]);

			if ($_POST['choice'][$i] == '') {
				/* an empty option can't be correct */
				$_POST['answer'][$i] = 0;
			}
		}		
		$sql	= "UPDATE ".TABLE_PREFIX."tests_questions SET
			category_id=$_POST[category_id],
			feedback='',
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

		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		if ($_POST['tid']) {
			header('Location: questions.php?tid='.$_POST['tid']);			
		} else {
			header('Location: question_db.php');
		}
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
	$sql	= "SELECT * FROM ".TABLE_PREFIX."tests_questions WHERE question_id=$qid AND course_id=$_SESSION[course_id] AND type=4";
	$result	= mysql_query($sql, $db);

	if (!($row = mysql_fetch_array($result))){
		require(AT_INCLUDE_PATH.'header.inc.php');
		$msg->printErrors('ITEM_NOT_FOUND');
		require (AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}
	$_POST['required']		= $row['required'];
	$_POST['question']		= $row['question'];
	$_POST['category_id']	= $row['category_id'];

	for ($i=0; $i<10; $i++) {
		$_POST['choice'][$i] = $row['choice_'.$i];
	}
	
	$_POST['properties'] = $row['properties'];
	
	if ($_POST['properties'] == AT_TESTS_QPROP_ALIGN_VERT) {
		$align_vert = ' checked="checked"';
	} else {
		$align_hor  = ' checked="checked"';
	}
}

require(AT_INCLUDE_PATH.'header.inc.php');
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="qid" value="<?php echo $qid; ?>" />
<input type="hidden" name="tid" value="<?php echo $_REQUEST['tid']; ?>" />
<input type="hidden" name="required" value="1" />

<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center">
<tr>
	<th align="left"><?php echo _AT('preset_scales'); ?> </th>
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

<div class="input-form">
	<div class="row">
		<label for="cats"><?php echo _AT('category'); ?></label><br />
		<select name="category_id" id="cats">
			<?php print_question_cats($_POST['category_id']); ?>
		</select>
	</div>

	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="question"><?php echo _AT('question'); ?></label> 
		
		<?php print_VE('question'); ?>
		
		<textarea id="question" cols="50" rows="6" name="question"><?php 
		echo htmlspecialchars(stripslashes($_POST['question'])); ?></textarea>
	</div>

	<div class="row">
		<label for="properties"><?php echo _AT('option_alignment'); ?></label><br />
		<label for="prop_5"><input type="radio" name="properties" id="prop_5" value="5" <?php echo $align_vert; ?> /><?php echo _AT('vertical'); ?></label>
		<label for="prop_6"><input type="radio" name="properties" id="prop_6" value="6" <?php echo $align_hor;  ?> /><?php echo _AT('horizontal'); ?></label>
	</div>

<?php
	for ($i=0; $i<10; $i++) { ?>
		<div class="row">
			<?php if ($i==0 || $i==1) { ?>
				<div class="required" title="<?php echo _AT('required_field'); ?>">*</div>
			<?php } ?>

			<label for="choice_<?php echo $i; ?>">
			<?php echo _AT('choice'); ?> <?php echo ($i+1); ?></label><br />
			<input type="text" id="choice_<?php echo $i; ?>" size="40" name="choice[<?php echo $i; ?>]" value="<?php echo htmlspecialchars(stripslashes($_POST['choice'][$i])); ?>" />
		</div>
<?php } ?>

	<div class="row buttons">
		<input type="submit" value="<?php echo _AT('save'); ?>"   name="submit" accesskey="s" />
		<input type="submit" value="<?php echo _AT('cancel'); ?>" name="cancel" />
	</div>
</div>
</form>

<?php require (AT_INCLUDE_PATH.'footer.inc.php');  ?>