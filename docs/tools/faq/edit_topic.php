<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_FAQ);


if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index_instructor.php');
	exit;
} 

if (isset($_GET['id'])) {
	$id = intval($_GET['id']);
} else {
	$id = intval($_POST['id']);
}

if (isset($_POST['submit'])) {
	if (trim($_POST['name']) == '') {
		$msg->addError('NAME_EMPTY');
	}

	if (!$msg->containsErrors()) {
		$_POST['name'] = $addslashes($_POST['name']);

		$sql	= "UPDATE ".TABLE_PREFIX."faq_topics SET name='$_POST[name]' WHERE topic_id=$id AND course_id=$_SESSION[course_id]";
		$result = mysql_query($sql,$db);

		$msg->addFeedback('TOPIC_UPDATED');
		header('Location: index_instructor.php');
		exit;
	}
}

require(AT_INCLUDE_PATH.'header.inc.php');

if ($id == 0) {
	$msg->printErrors('TOPIC_NOT_FOUND');
	require (AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

$sql	= "SELECT name FROM ".TABLE_PREFIX."faq_topics WHERE course_id=$_SESSION[course_id] AND topic_id=$id ORDER BY name";
$result = mysql_query($sql, $db);
if (!$row = mysql_fetch_assoc($result)) {
	$msg->printErrorS('TOPIC_NOT_FOUND');
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
} else if (!$_POST['name']) {
	$_POST['name'] = $row['name'];
}

?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="id" value="<?php echo $id; ?>" />

<div class="input-form">
	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="name"><?php  echo _AT('name'); ?></label><br />
		<input type="text" name="name" size="50" id="name" value="<?php if (isset($_POST['name'])) echo stripslashes($_POST['name']);  ?>" />
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?> " />
	</div>

</div>
</form>
<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>