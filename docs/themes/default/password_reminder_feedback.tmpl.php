<?php 

	require(AT_INCLUDE_PATH.'header.inc.php'); 

	global $msg;

	$msg->addFeedback('PASSWORD_SUCCESS');
	
	$msg->printAll();

	require(AT_INCLUDE_PATH.'footer.inc.php'); 
?>