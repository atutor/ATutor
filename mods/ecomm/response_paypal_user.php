<?php
$_user_location	= 'users';
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require('include/payments.lib.php');

if ($_GET['st'] == "Completed"){
	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');

} else {
	$msg->addError('EC_PAYMENT_FAILED');
}

//print_r($_GET);
header('Location: index.php');
exit;
?>