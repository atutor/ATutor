<?php
$_user_location	= 'users';
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require('include/payments.lib.php');
$transaction_id = $addslashes($_REQUEST['tx']);
$payment_id = intval($_REQUEST['item_number']);

if ($_GET['st'] == "Completed"){

	approve_payment($payment_id, $transaction_id);
	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');

} else if ($_GET['st'] == "Pending"){
	approve_payment($payment_id, $transaction_id);
	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	$msg->addFeedback('ACTION_PENDING_CC_CONFIRM');

}else {
	$msg->addError('EC_PAYMENT_FAILED');
}

//print_r($_GET);
header('Location: index.php');
exit;
?>