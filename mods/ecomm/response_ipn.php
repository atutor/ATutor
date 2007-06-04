<?php
$_user_location	= 'public';
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require('include/payments.lib.php');
//require(AT_INCLUDE_PATH.'header.inc.php');

//echo "success";
//paypal_authenticate_ipn();

paypal_authenticate_user_response();
//mirapay_authenticate_user_response();
//print_r($_GET);
//exit;
header('Location: index.php');
exit;



?>