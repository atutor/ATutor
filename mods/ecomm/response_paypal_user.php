<?php
$_user_location	= 'users';
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require('include/payments.lib.php');

//print_r($_GET);
//exit;

if($_GET['st'] == "Completed"){
	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');

}else {
	$msg->addError('EC_PAYMENT_FAILED');
}

paypal_authenticate_user_response();

header('Location: index.php');
exit;
?>