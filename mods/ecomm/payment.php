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

if ($_POST['cancel']) {
	header('location: index.php');
	exit;
}

///Get the fee for the current course
$course_id = intval($_GET['course_id']);

$sql = "SELECT course_fee FROM ".TABLE_PREFIX."ec_course_fees WHERE course_id=$course_id";
$result = mysql_query($sql, $db);
if ($this_course_fee = mysql_fetch_assoc($result)) {
	$this_course_fee = $this_course_fee['course_fee'];
} else {
	header('location: index.php');
	exit;
}
$course_id = intval($_REQUEST['course_id']);
$member_id = intval($_SESSION['member_id']);
require (AT_INCLUDE_PATH.'header.inc.php');

///Check if a partial payment has already been made so the balance can be calculated
$sql4 = "SELECT SUM(amount) AS total_amount FROM ".TABLE_PREFIX."payments WHERE course_id='$course_id' AND approved=1 AND member_id=$member_id";
$result4 = mysql_query($sql4, $db);
while ($row4 = mysql_fetch_assoc($result4)) {
	if($row4['total_amount'] > 0){
		$amount_paid = $row4['total_amount'];
	} else {
		$amount_paid = 0.00;
	}
}
$balance_course_fee = $this_course_fee - $amount_paid;
$this_course_fee = $balance_course_fee;

$sql = "INSERT INTO ".TABLE_PREFIX."payments VALUES (NULL, NULL, 0, '', '{$_SESSION['member_id']}', '$course_id', '$balance_course_fee')";
$result = mysql_query($sql, $db);

$payment_id = mysql_insert_id($db);
?>
<div class="input-form">
	<div class="row">
		<h3><?php echo _AT('confirm'); ?></h3>

		<p><?php echo _AT('ec_confirm_info'); ?></p>

		<dl>
			<dt><?php echo _AT('ec_course');?></dt>
			<dd><?php echo $system_courses[$course_id]['title']; ?></dd>

			<dt><?php echo _AT('ec_this_course_fee');?></dt>
			<dd><?php echo $_config['ec_currency_symbol'].$this_course_fee.' '.$_config['ec_currency'];?></dd>

			<dt><?php echo _AT('ec_amount_recieved');?></dt>
			<dd><?php echo $_config['ec_currency_symbol'].$amount_paid;?></dd>

			<dt><?php echo _AT('ec_balance_due');?></dt>
			<dd><?php echo $_config['ec_currency_symbol'].number_format($balance_course_fee, 2).' '.$_config['ec_currency'];;?></dd>
		</dl>
			
		<h4><?php echo _AT('ec_requirements'); ?></h4>
		<ul>
			<li><?php echo _AT('ec_requirements_ssl'); ?></li>
			<li><?php echo _AT('ec_requirements_cookies'); ?></li>
			<li><?php echo _AT('ec_requirements_javascript'); ?></li>
			<li><?php echo _AT('ec_requirements_comments'); ?></li>
		</ul>
	</div>

	<?php
		/*
		 * these payment forms below can be replaced by any other payment gateway.
		 * when the gateway sends back the response then it is authenticated and if
		 * the amounts match then the `payments` transaction is updated and approved.
		*/
	?>

	<div class="row buttons">
		<?php beanstream_print_form($payment_id, $balance_course_fee, $course_id); ?>
		<?php paypal_print_form($payment_id, $balance_course_fee, $course_id); ?>
		<?php mirapay_print_form($payment_id, $balance_course_fee, $course_id); ?>
		<?php check_payment_print_form($payment_id, $balance_course_fee, $course_id); ?>

	</div>
</div>
		
<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>