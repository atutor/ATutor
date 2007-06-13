<?php

$_user_location	= 'public';
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require('include/payments.lib.php');

//file_put_contents('/tmp/ipn.txt', print_r($_POST, TRUE));

//fwrite(AT_CONTENT_DIR'/tmp/ipn.txt', print_r($_POST, TRUE));

// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-validate';

foreach ($_POST as $key => $value) {
$value = urlencode(stripslashes($value));
$req .= "&$key=$value"."test";
}

// post back to PayPal system to validate
$header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
$fp = fsockopen ('www.sandbox.paypal.com', 80, $errno, $errstr, 30);

if (!$fp) {
// HTTP ERROR
} else {
	fputs ($fp, $header . $req);
	while (!feof($fp)) {
		$res = fgets ($fp, 1024);
	
		if (strcmp ($res, "VERIFIED") == 0) {
		// check that the payment_status = Completed
		if($_POST['receiver_email'] == "Completed"){
			$error[] = 'AT_ERROR_EC_PAYMENT_FAILED';
		}


		// check that txn_id has not been previously processed
		$sql = "SELECT transaction_id from ".TABLE_PREFIX."payments WHERE payment_id = '$_POST[item_number]' ";
		$result = mysql_query($sql, $db);
		$this_transaction = mysql_result($result,0);
		if($this_transaction != ''){
				$error[] = 'AT_ERROR_EC_PAYMENT_FAILED';
		}
		// check that receiver_email is your Primary PayPal email
		if($_config['ec_vendor_id'] != $_POST['receiver_email']){
			$error[] = 'AT_ERROR_EC_PAYMENT_FAILED';
		}
		// check that payment amount are correct
		$sql = "SELECT amount from ".TABLE_PREFIX."payments WHERE payment_id = '$_POST[item_number]' ";
		$result = mysql_query($sql, $db);
		$this_amount = mysql_result($result,0);
		if($this_amount != $_POST['mc_gross']){
				$error[] = 'AT_ERROR_EC_PAYMENT_FAILED';
		}

		// check that payment_currency are correct
		if($_config['ec_currency'] != $_POST['mc_currency']){
				$error[] = 'AT_ERROR_EC_PAYMENT_FAILED';
		}
		// process payment

		if(!$error){
			approve_payment($_POST['item_number'], $_POST['txn_id']);
			if($_config['ec_store_log']){
				$fpn = fopen($_config['ec_log_file'], "a+");
				$results = print_r($_POST, TRUE);
				$results .= "Successful Transaction \n".$results;
				fwrite($fpn, $results);
			}
		}else{
			$msg->addError($error);
		}
	
		fclose ($fp);

		} else if (strcmp ($res, "INVALID") == 0) {
				// log for manual investigation
			$msg->addError($error);
			if($_config['ec_store_log']){
				$fpn = fopen($_config['ec_log_file'], "a+");
				$results = print_r($_POST, TRUE);
				$results .= "Failed Transaction \n".$results;
				fwrite($fpn, $results);
			}
		}
	}
}
exit;

?>