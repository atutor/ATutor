<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

$_user_location	= 'users';
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require('include/payments.lib.php');

$amount = floatval($_GET['trnAmount']);
$id = intval($_GET['ref2']);
$approved = intval($_GET['trnOrderNumber']);
$ordernumber = intval($_GET['trnOrderNumber']);
$trans_id = intval($_GET['trnId']);

if ($_config['ec_contact_email']){
	$contact_admin_email = $_config['ec_contact_email'];
} else {
	$contact_admin_email = $_config['contact_email'];
}
//$contact_admin_email .= ",ineher@ocad.ca"; //uncomment and add comma separated list, starting with a comma, of additional email addresses that should receive notification of payments made

if($_GET['trnApproved'] == 1 && $_GET['trnId']){
	approve_payment($ordernumber,$trans_id);
}

require (AT_INCLUDE_PATH.'header.inc.php');
require (AT_INCLUDE_PATH.'footer.inc.php');
?>
