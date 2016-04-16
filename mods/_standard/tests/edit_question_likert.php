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

	$empty_fields = array();
	if ($_POST['question'] == ''){
		$empty_fields[] = _AT('question');
	}
	if ($_POST['choice'][0] == '') {
		$empty_fields[] = _AT('choice').' 1';
	}

	if ($_POST['choice'][1] == '') {
		$empty_fields[] = _AT('choice').' 2';
	}

	if (!empty($empty_fields)) {
		$msg->addError(array('EMPTY_FIELDS', implode(', ', $empty_fields)));
	}

	if (!$msg->containsErrors()) {
		$_POST['question'] = $addslashes($_POST['question']);

		for ($i=0; $i<10; $i++) {
			$_POST['choice'][$i] = $addslashes(trim($_POST['choice'][$i]));
			$_POST['answer'][$i] = intval($_POST['answer'][$i]);

			if ($_POST['choice'][$i] == '') {
				/* an empty option can't be correct */
				$_POST['answer'][$i] = 0;
			}
		}	

        $sql    = "UPDATE %stests_questions SET
            category_id=%d,
            feedback='%s',
            question='%s',
            choice_0='%s',
            choice_1='%s',
            choice_2='%s',
            choice_3='%s',
            choice_4='%s',
            choice_5='%s',
            choice_6='%s',
            choice_7='%s',
            choice_8='%s',
            choice_9='%s',
            answer_0=%d,
            answer_1=%d,
            answer_2=%d,
            answer_3=%d,
            answer_4=%d,
            answer_5=%d,
            answer_6=%d,
            answer_7=%d,
            answer_8=%d,
            answer_9=%d,
            remedial_content='%s'
            WHERE question_id=%d AND course_id=%d";

        $result    = queryDB($sql, array(
                        TABLE_PREFIX,
                        $_POST['category_id'],
                        $_POST['feedback'],
                        $_POST['question'],
                        $_POST['choice']['0'],
                        $_POST['choice']['1'],
                        $_POST['choice']['2'],
                        $_POST['choice']['3'],
                        $_POST['choice']['4'],
                        $_POST['choice']['5'],
                        $_POST['choice']['6'],
                        $_POST['choice']['7'],
                        $_POST['choice']['8'],
                        $_POST['choice']['9'],
                        $_POST['answer']['0'],
                        $_POST['answer']['1'],
                        $_POST['answer']['2'],
                        $_POST['answer']['3'],
                        $_POST['answer']['4'],
                        $_POST['answer']['5'],
                        $_POST['answer']['6'],
                        $_POST['answer']['7'],
                        $_POST['answer']['8'],
                        $_POST['answer']['9'],
                        $_POST['remedial_content'],
                        $_POST['qid'],
                        $_SESSION['course_id'] ));
        
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

		$sql	= "SELECT * FROM %stests_questions WHERE question_id=%d AND course_id=%d";
		$rows_questions	= queryDB($sql, array(TABLE_PREFIX, $_POST['preset_num'], $_SESSION['course_id']));
		
		if(count($rows_questions) > 0){
			for ($i=0; $i<10; $i++) {
				$_POST['choice'][$i] = $rows_questions['choice_' . $i];
			}
		}
	}

} else {
	$sql	= "SELECT * FROM %stests_questions WHERE question_id=%d AND course_id=%d AND type=4";
	$row	= queryDB($sql, array(TABLE_PREFIX, $qid, $_SESSION['course_id']), TRUE);
    if(count($row) == 0){
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
}

require(AT_INCLUDE_PATH.'header.inc.php');
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="qid" value="<?php echo $qid; ?>" />
<input type="hidden" name="tid" value="<?php echo intval($_REQUEST['tid']); ?>" />
<input type="hidden" name="required" value="1" />
	<fieldset class="group_form" style="width:88%;margin-left:auto;margin-right:auto;"><legend class="group_form"><?php echo _AT('preset_scales'); ?></legend>
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

			$sql = "SELECT * FROM %stests_questions WHERE course_id=%d AND type=4";
			$rows_questions = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id']));
			
			if(count($rows_questions) > 0){
				echo '<optgroup label="'. _AT('prev_used').'">';
				$used_choices = array();
				foreach($rows_questions as $row){
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
				} 
				echo '</optgroup>';
			}
		?>
		</select> 
		<input type="submit" name="preset" value="<?php echo _AT('set_preset'); ?>" class="button" />
	</td>
</tr>
</table>
</fieldset>
<br />

<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('question'); ?></legend>
	<div class="row">
		<label for="cats"><?php echo _AT('category'); ?></label><br />
		<select name="category_id" id="cats">
			<?php print_question_cats($_POST['category_id']); ?>
		</select>
	</div>

	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="question"><?php echo _AT('question'); ?></label> 
		
		<?php print_VE('question'); ?>
		
		<textarea id="question" cols="50" rows="6" name="question"><?php echo stripslashes($_POST['question']); ?></textarea>
	</div>

<?php
	for ($i=0; $i<10; $i++) { ?>
		<div class="row">
			<?php if ($i==0 || $i==1) { ?>
				<span class="required" title="<?php echo _AT('required_field'); ?>">*</span>
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
	</fieldset>
</div>
</form>

<?php require (AT_INCLUDE_PATH.'footer.inc.php');  ?>