<?php
$_user_location	= 'users';
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require('include/payments.lib.php');

mirapay_authenticate_user_response();

header('Location: index.php');
exit;
?>