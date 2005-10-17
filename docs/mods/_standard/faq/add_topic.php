<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_FAQ);

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index_instructor.php');
	exit;
} else if (isset($_POST['submit'])) {
	if (trim($_POST['name']) == '') {
		$msg->addError('NAME_EMPTY');
	}

	if (!$msg->containsErrors()) {
		$_POST['name'] = $addslashes($_POST['name']);

		$sql	= "INSERT INTO ".TABLE_PREFIX."faq_topics VALUES (0, $_SESSION[course_id], '$_POST[name]')";
		$result = mysql_query($sql,$db);
		
		$msg->addFeedback('TOPIC_ADDED');
		header('Location: index_instructor.php');
		exit;
	}
}

require(AT_INCLUDE_PATH.'header.inc.php');

?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">

<div class="input-form">	
	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="name"><?php  echo _AT('name'); ?></label><br />
		<input type="text" name="name" size="50" id="name" value="<?php if (isset($_POST['name'])) echo stripslashes($_POST['name']);  ?>" />
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>