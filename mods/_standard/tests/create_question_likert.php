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
require(AT_INCLUDE_PATH.'../mods/_standard/tests/lib/test_question_queries.inc.php');

authenticate(AT_PRIV_TESTS);
require(AT_INCLUDE_PATH.'../mods/_standard/tests/lib/test_result_functions.inc.php');

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: question_db.php');
	exit;
} else if (isset($_POST['submit'])) {
	$_POST['required']    = intval($_POST['required']);
	$_POST['question']    = trim($_POST['question']);
	$_POST['category_id'] = intval($_POST['category_id']);

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
		$_POST['feedback']   = '';
		$_POST['question']   = $addslashes($_POST['question']);

		for ($i=0; $i<10; $i++) {
			$_POST['choice'][$i] = $addslashes(trim($_POST['choice'][$i]));
			$_POST['answer'][$i] = intval($_POST['answer'][$i]);

			if ($_POST['choice'][$i] == '') {
				/* an empty option can't be correct */
				$_POST['answer'][$i] = 0;
			}
		}
		
		$sql_params = array(	$_POST['category_id'], 
								$_SESSION['course_id'],
								$_POST['feedback'], 
								$_POST['question'], 
								$_POST['choice'][0], 
								$_POST['choice'][1], 
								$_POST['choice'][2], 
								$_POST['choice'][3], 
								$_POST['choice'][4], 
								$_POST['choice'][5], 
								$_POST['choice'][6], 
								$_POST['choice'][7], 
								$_POST['choice'][8], 
								$_POST['choice'][9], 
								$_POST['answer'][0], 
								$_POST['answer'][1], 
								$_POST['answer'][2], 
								$_POST['answer'][3], 
								$_POST['answer'][4], 
								$_POST['answer'][5], 
								$_POST['answer'][6], 
								$_POST['answer'][7], 
								$_POST['answer'][8], 
								$_POST['answer'][9]);

		$sql = vsprintf(AT_SQL_QUESTION_LIKERT, $sql_params);
        $result    = queryDB($sql, array());
		
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		header('Location: question_db.php');
		exit;
	}
} else if (isset($_POST['preset'])) {
	// load preset
	$_POST['preset_num'] = intval($_POST['preset_num']);

	if (isset($_likert_preset[$_POST['preset_num']])) {
		$_POST['choice'] = $_likert_preset[$_POST['preset_num']];
	} else if ($_POST['preset_num']) {
		$sql	= "SELECT * FROM %d=stests_questions WHERE question_id=%d AND course_id=%d";
		$row	= queryDB($sql, array(TABLE_PREFIX, $_POST['preset_num'], $_SESSION['course_id']), TRUE);
        if(count($row) > 0){
			for ($i=0; $i<10; $i++) {
				$_POST['choice'][$i] = $row['choice_' . $i];
			}
		}
	}

}

$onload = 'document.form.category_id.focus();';

require(AT_INCLUDE_PATH.'header.inc.php');

?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="required" value="1" />

<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('preset_scales'); ?></legend>

	<div class="row">
		<select name="preset_num">
			<optgroup label="<?php echo _AT('presets'); ?>">
		<?php
			//presets
			foreach ($_likert_preset as $val=>$preset) {
				echo '<option value="'.$val.'">'.$preset[0].' - '.$preset[count($preset)-1].'</option>';
			}
			echo '</optgroup>';
			//previously used
			$sql = "SELECT * FROM %stests_questions WHERE course_id=%d AND type=4";
			$rows_questions = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id']));
			
            if(count($rows_questions) > 0){
				echo '<optgroup label="'. _AT('prev_used').'">';
				$used_choices = array();
				foreach($rows_questions as $row){
				//do {
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
	</div>

	<div class="row buttons">
		<input type="submit" name="preset" value="<?php echo _AT('set_preset'); ?>" class="button" />
	</div>
	</fieldset>
</div>

<br />
<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('test_lk'); ?></legend>
	<div class="row">
		<label for="cats"><?php echo _AT('category'); ?></label><br />
		<select name="category_id" id="cats">
			<?php print_question_cats($_POST['category_id']); ?>
		</select>
	</div>

	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="question"><?php echo _AT('question'); ?></label> 
		<?php print_VE('question'); ?>
		<textarea id="question" cols="50" rows="6" name="question" style="width:90%;"><?php 
		echo htmlspecialchars(stripslashes($_POST['question'])); ?></textarea>
	</div>


<?php for ($i=0; $i<10; $i++) { ?>
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