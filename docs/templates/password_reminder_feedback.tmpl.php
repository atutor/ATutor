<?php require(AT_INCLUDE_PATH.'header.inc.php'); ?>

<h3><?php echo _AT('password_reminder');  ?></h3>
<?php
	$feedback = _AT('password_success');

	require_once(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

	global $savant;
	$msg =& new Message($savant);
	
	$msg->printAll();
?>


<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>