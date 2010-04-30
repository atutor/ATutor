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
define('AT_INCLUDE_PATH', '../../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_POLLS);

if ($_POST['cancel']) {
	$msg->addFeedback('CANCELLED');
	Header('Location: index.php');
	exit;
}

if ($_POST['add_poll'] && (authenticate(AT_PRIV_POLLS, AT_PRIV_RETURN))) {
	if (trim($_POST['question']) == '') {
		$msg->addError(array('EMPTY_FIELDS', _AT('question')));
	}

	if ((trim($_POST['c1']) == '') || (trim($_POST['c2']) == '')) {
		$msg->addError('POLL_QUESTION_MINIMUM');
	}

	if (!$msg->containsErrors()) {
		$_POST['question'] = $addslashes($_POST['question']);
		//Check if the question has exceeded the words amount - 100, decided in the db
		if ($strlen($_POST['question']) > 100){
			$_POST['question'] = $substr($_POST['question'], 0, 100);
		}

		for ($i=1; $i<= AT_NUM_POLL_CHOICES; $i++) {
			$trimmed_word = $addslashes($_POST['c' . $i]);
			if ($strlen($trimmed_word) > 100){
				$trimmed_word = $substr($trimmed_word, 0, 100);
			}
			$choices .= "'" . $trimmed_word . "',0,";
		}
		$choices = substr($choices, 0, -1);	//Remove the last comma.

		$sql	= "INSERT INTO ".TABLE_PREFIX."polls VALUES (NULL, $_SESSION[course_id], '$_POST[question]', NOW(), 0, $choices)";
		$result = mysql_query($sql,$db);
		
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		header('Location: index.php');
		exit;
	}
	for ($i=1; $i<= AT_NUM_POLL_CHOICES; $i++) {
		$_POST['c' . $i] = $stripslashes($_POST['c' . $i]);
	}
	$_POST['question'] = $stripslashes($_POST['question']);
}

require(AT_INCLUDE_PATH.'header.inc.php');

?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="add_poll" value="true" />

<div class="input-form">	
<fieldset class="group_form"><legend class="group_form"><?php echo _AT('add_poll'); ?></legend>
	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="question"><?php  echo _AT('question'); ?></label><br />
		<textarea name="question" cols="45" rows="3" id="question" style="width:90%;"><?php if (isset ($_POST['question'])) echo htmlspecialchars($_POST['question']);  ?></textarea>
	</div>

<?php for ($i=1; $i<= AT_NUM_POLL_CHOICES; $i++): ?>
	<div class="row">
		<?php if (($i==1) || ($i==2)) { ?>
			<span class="required" title="<?php echo _AT('required_field'); ?>">*</span>
		<?php } ?>
		<label for="c<?php echo $i; ?>"><?php echo _AT('choice'); ?> <?php echo $i; ?></label><br />
		<input type="text" name="c<?php echo $i; ?>" value="<?php if (isset($_POST['c' . $i])) echo htmlspecialchars($_POST['c' . $i]);  ?>" size="50" id="c<?php echo $i; ?>" />
	</div>
<?php endfor; ?>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
	</fieldset>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>