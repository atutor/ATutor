<?php
$_user_location	= 'public';
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require('include/payments.lib.php');

// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-validate';

foreach ($_POST as $key => $value) {
	$value = urlencode($stripslashes($value));
	$req .= "&$key=$value";
}

$host = parse_url($_config['ec_uri']);
$host = $host['host']; // either www.sandbox.paypal.com or just www.paypal.com
if (strcasecmp($host, 'www.sandbox.paypal.com') && strcasecmp($host, 'www.paypal.com')) {
	// don't want to post this to the wrong URI
	exit;
}

// post back to PayPal system to validate
$header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
$fp = fsockopen($host, 80, $errno, $errstr, 30);
if (!$fp) { exit; }

$result = '';
fputs($fp, $header . $req);
while (!feof($fp)) { 
	$result .= fgets($fp, 1024);
}

if (strpos($result, 'VERIFIED') === FALSE) {
	// Error: not VERIFIED by PayPal
	log_paypal_ipn_requests('INVALID (1)' . $result);
	return;
} else if (strcasecmp($_POST['payment_status'], 'Completed')) {
	// Error: not completed
	log_paypal_ipn_requests('INCOMPLETE (2)');
	return;
}

$error = false;
$_POST['item_number'] = $addslashes($_POST['item_number']);
$_POST['txn_id']      = $addslashes($_POST['txn_id']);

// check that txn_id has not been previously processed
$sql = "SELECT transaction_id, amount FROM ".TABLE_PREFIX."payments WHERE payment_id='$_POST[item_number]'";
$result = mysql_query($sql, $db);
if (!($row = mysql_fetch_assoc($result))) {
	// Error: no valid payment_id
	$error = 3;
} else if ($row['transaction_id']) {
	// Error: this transaction has already been processed
	$error = 4;
} else if ($row['amount'] != $_POST['mc_gross']) {
	// Error: wrong amount sent
	$error = 5;
} else if ($_config['ec_currency'] != $_POST['mc_currency']) {
	// Error: wrong currency
	$error = 6;
}

if (!$error) {
	approve_payment($_POST['item_number'], $_POST['txn_id']);
	$status = 'VALID';
} else {
	$status = "INVALID ($error)";
}
log_paypal_ipn_requests($status);
?>