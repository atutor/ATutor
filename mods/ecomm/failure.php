<?php

$_user_location	= 'users';
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
$_custom_css = $_base_path . 'mods/hello_world/module.css'; // use a custom stylesheet

require (AT_INCLUDE_PATH.'header.inc.php');

?>

<h2><?php echo _AT('ec_payments'); ?></h2>

<h3>Payment Failed/Cancelled</h3><br /><br />
<div style="border:3px solid red; padding: 1em;  margin-left: auto; margin-right: auto; width: 80%;background-color: #FBF4ED;" >
The credit card processing service returned an error. The payment failed or was cancelled. 
</div><br /><br />
<p align="center"><a href="/payment">Return to ATutor Payment</a></p>


<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>
