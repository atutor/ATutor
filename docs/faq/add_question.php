<?php
define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_FAQ);

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index_instructor.php');
	exit;
} else if (isset($_POST['submit'])) {
	if (trim($_POST['question']) == '') {
		$msg->addError('QUESTION_EMPTY');
	}

	if (trim($_POST['answer']) == '') {
		$msg->addError('ANSWER_EMPTY');
	}

	if (!$msg->containsErrors()) {
		$_POST['question'] = $addslashes($_POST['question']);
		$_POST['answer']   = $addslashes($_POST['answer']);
		$_POST['topic_id'] = intval($_POST['topic_id']);

		// check that this topic_id belongs to this course:
		$sql    = "SELECT topic_id FROM ".TABLE_PREFIX."faq_topics WHERE topic_id=$_POST[topic_id] AND course_id=$_SESSION[course_id]";
		$result = mysql_query($sql, $db);
		if ($row = mysql_fetch_assoc($result)) {
			$sql	= "INSERT INTO ".TABLE_PREFIX."faq_entries VALUES (0, $_POST[topic_id], NOW(), 1, '$_POST[question]', '$_POST[answer]')";
			$result = mysql_query($sql,$db);
		}
		
		$msg->addFeedback('QUESTION_ADDED');
		header('Location: index_instructor.php');
		exit;
	}
}

require(AT_INCLUDE_PATH.'header.inc.php');

	$sql	= "SELECT name, topic_id FROM ".TABLE_PREFIX."faq_topics WHERE course_id=$_SESSION[course_id] ORDER BY name";
	$result = mysql_query($sql, $db);
	$num_topics = mysql_num_rows($result);
	if (!$num_topics) {
		$msg->printErrors('NO_FAQ_TOPICS');
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">

<div class="input-form">
	<div class="row">

		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="topic"><?php  echo _AT('topic'); ?></label><br />
		<select name="topic_id" id="topic">
			<?php while ($row = mysql_fetch_assoc($result)): ?>
				<option value="<?php echo $row['topic_id']; ?>"<?php if (isset($_POST['topic_id']) && ($row['topic_id'] == $_POST['topic_id'])) { echo ' selected="selected"'; } ?>><?php echo htmlspecialchars($row['name']); ?></option>
			<?php endwhile; ?>
		</select>
	</div>
	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="question"><?php  echo _AT('question'); ?></label><br />
		<input type="text" name="question" size="50" id="question" value="<?php if (isset($_POST['question'])) echo stripslashes($_POST['question']);  ?>" />

	</div>
	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="answer"><?php  echo _AT('answer'); ?></label><br />
		<textarea name="answer" cols="45" rows="3" id="answer" style="width:90%;"><?php if (isset ($_POST['answer'])) echo stripslashes($_POST['answer']);  ?></textarea>
	</div>


	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>