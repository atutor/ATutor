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
require (AT_INCLUDE_PATH.'header.inc.php');
?>
<h3><?php echo _AT('ec_payment_failed'); ?></h3><br /><br />
<div class="payerrbox">
<?php $msg->printErrors('EC_PAYMENT_FAILED') ?>. 
</div><br /><br />
<ul>
<?php print_r($_GET['errorMessage']); ?>
</ul>
<?php

require (AT_INCLUDE_PATH.'footer.inc.php');
?>