<?php
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
	$this_course_fee = number_format($this_course_fee['course_fee'], 2);
} else {
	header('location: index.php');
	exit;
}

require (AT_INCLUDE_PATH.'header.inc.php');

///Check if a partial payment has already been made so the balance can be calculated
$sql4 = "SELECT SUM(amount) AS total_amount FROM ".TABLE_PREFIX."payments WHERE course_id='$_REQUEST[course_id]' AND approved=1 AND member_id=$_SESSION[member_id]";
$result4 = mysql_query($sql4, $db);
if ($row4 = mysql_fetch_assoc($result4)) {
	$amount_paid = number_format($row4['total_amount'],2);
} else {
	$amount_paid = 0.00;
}
$balance_course_fee = number_format($this_course_fee - $amount_paid, 2);
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
			<dd><?php echo $_config['ec_currency_symbol'].$balance_course_fee;?></dd>
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
		 * this payment form can be replaced by any other payment gateway.
		 * a paypal version will be added soon, which will submit the form to paypal.
		 * when the gateway sends back the response then it is authenticated and if
		 * the amounts match then the `payments` transaction is updated and approved.
		*/
	?>

	<div class="row buttons">
		<?php //paypal_print_form($payment_id, $balance_course_fee, $course_id); ?>
		<?php mirapay_print_form($payment_id, $balance_course_fee, $course_id); ?>
		<?php check_payment_print_form($payment_id, $balance_course_fee, $course_id); ?>

	</div>
</div>
		
<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>