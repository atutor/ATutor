<?php

$_user_location	= 'users';
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
$_custom_css = $_base_path . 'mods/hello_world/module.css'; // use a custom stylesheet

require (AT_INCLUDE_PATH.'header.inc.php');

?>

<h3><?php echo _AT('ec_payment_failed'); ?></h3><br />

<?php $msg->printErrors('EC_PAYMENT_FAILED'); ?>

<p align="center"><a href="mods/ecomm/index_mystart.php"><?php echo _AT('ec_return_to_payments'); ?></a>
<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>
