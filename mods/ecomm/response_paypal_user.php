<?php
$_user_location	= 'users';
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require('include/payments.lib.php');

if ($_GET['payment_status'] == "Completed"){
	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');

} else {
	$msg->addError('EC_PAYMENT_FAILED');
}

header('Location: index.php');
exit;
?>