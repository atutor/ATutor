<?php require(AT_INCLUDE_PATH.'header.inc.php'); ?>

<h3><?php echo _AT('password_reminder');  ?></h3>
<?php
	global $msg;

	$msg->addFeedback('PASSWORD_SUCCESS');
	
	$msg->printAll();
?>
<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>