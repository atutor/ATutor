<?php require(AT_INCLUDE_PATH.'header.inc.php'); ?>

<h3><?php echo _AT('password_reminder');  ?></h3>
<?php
	$feedback = _AT('password_success');
	print_feedback($feedback);
?>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>