<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_POLLS);


if ($_POST['cancel']) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php');
	exit;
}

if (isset($_GET['poll_id'])) {
	$poll_id = intval($_GET['poll_id']);
} else {
	$poll_id = intval($_POST['poll_id']);
}

if ($_POST['edit_poll']) {
	if (trim($_POST['question']) == '') {
		$msg->addError('POLL_QUESTION_EMPTY');
	}

	if ((trim($_POST['c1']) == '') || (trim($_POST['c2']) == '')) {
		$msg->addError('POLL_QUESTION_MINIMUM');
	}

	if (!$msg->containsErrors()) {
		$_POST['question'] = $addslashes($_POST['question']);

		for ($i=1; $i<= AT_NUM_POLL_CHOICES; $i++) {
			$choices .= "choice$i = '" . $addslashes($_POST['c' . $i]) . "',";
		}
		$choices = substr($choices, 0, -1);

		$sql = "UPDATE ".TABLE_PREFIX."polls SET question='$_POST[question]', $choices WHERE poll_id=$poll_id AND course_id=$_SESSION[course_id]";
		$result = mysql_query($sql,$db);

		$msg->addFeedback('POLL_UPDATED');
		Header('Location: index.php');
		exit;
	}
	for ($i=1; $i<= AT_NUM_POLL_CHOICES; $i++) {
		$_POST['c' . $i] = stripslashes($addslashes($_POST['c' . $i]));
	}
	$_POST['question'] = stripslashes($addslashes($_POST['question']));
}

require(AT_INCLUDE_PATH.'header.inc.php');

	if ($poll_id == 0) {
		$msg->printErrors('POLL_NOT_FOUND');
		require (AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}
	
	$sql = "SELECT * FROM ".TABLE_PREFIX."polls WHERE poll_id=$poll_id AND course_id=$_SESSION[course_id]";
	$result = mysql_query($sql,$db);
	if (!($row = mysql_fetch_assoc($result))) {
		$msg->printErrors('POLL_NOT_FOUND');
		require (AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}

?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="edit_poll" value="true" />
<input type="hidden" name="poll_id" value="<?php echo $row['poll_id']; ?>" />

<div class="input-form">
	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="question"><?php echo _AT('question'); ?>:</label><br />
		<textarea name="question" cols="55" rows="3" id="question"><?php if (isset ($_POST['question'])) { echo htmlspecialchars($_POST['question']); } else { echo htmlspecialchars($row['question']); } ?></textarea>
	</div>

<?php
	for ($i=1; $i<= AT_NUM_POLL_CHOICES; $i++): ?>
		<div class="row">
			<?php if (($i==1) || ($i==2)) { ?>
				<div class="required" title="<?php echo _AT('required_field'); ?>">*</div>
			<?php } ?>
			<label for="c<?php echo $i; ?>"><?php echo _AT('choice'); ?> <?php echo $i; ?>:</label><br />
			<input type="text" name="c<?php echo $i; ?>" id="c<?php echo $i; ?>" value="<?php if (isset ($_POST['c' . $i])) { echo htmlspecialchars($_POST['c' . $i]); } else { echo htmlspecialchars($row['choice' . $i]); }?>" size="40" />
		</div>

<?php endfor; ?>
	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?> " />
	</div>

</div>
</form>
<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>