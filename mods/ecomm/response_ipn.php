<?php
$_user_location	= 'public';
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require('include/payments.lib.php');

paypal_authenticate_user_response();

header('Location: index.php');
exit;



?>