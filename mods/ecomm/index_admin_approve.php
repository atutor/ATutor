<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
require('include/payments.lib.php');
admin_authenticate(AT_ADMIN_PRIV_ECOMM);

$invoice_row = false;
if (isset($_GET['id'], $_GET['submit'])) {
	$_GET['id'] = intval($_GET['id']);
	$sql = "SELECT * FROM ".TABLE_PREFIX."payments WHERE payment_id={$_GET['id']}";
	$result = mysql_query($sql, $db);
	$invoice_row = mysql_fetch_assoc($result);
	if (!$invoice_row) {
		// can't be found.
		$msg->addError('EC_INVOICE_NOT_FOUND');
	} else if ($invoice_row['approved']) {
		// already approved
		$msg->addError('EC_INVOICE_APPROVED');
		$invoice_row = false;
	}
} else if (isset($_POST['id'], $_POST['submit'])) {
	$_POST['id']   = intval($_POST['id']);
	$_POST['txid'] = $addslashes($_POST['txid']);
	approve_payment($_POST['id'], $_POST['txid']);

	$msg->deleteFeedback('EC_PAYMENT_CONFIRMED_AUTO');
	$msg->deleteFeedback('EC_PAYMENT_CONFIRMED_MANUAL');
	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: payments_admin.php');
	exit;
} else if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.$_SERVER['PHP_SELF']);
	exit;
}


require (AT_INCLUDE_PATH.'header.inc.php');
?>

<?php if (!$invoice_row): ?>
	<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<div class="input-form">
		<div class="row">
			<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="invoice"><?php echo _AT('ec_invoice'); ?>#</label><br/>
			<input type="text" id="invoice" name="id" value="" size="10"/>
		</div>

		<div class="row buttons">
			<input type="submit" name="submit" value="<?php echo _AT('search'); ?>"/>
		</div>
	</div>
	</form>
<?php else: ?>
	<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<input type="hidden" name="id" value="<?php echo $invoice_row['payment_id']; ?>"/>
	<div class="input-form">
		<div class="row">
			<?php echo _AT('ec_invoice'); ?>#<br/>
			<?php echo $invoice_row['payment_id']; ?>
		</div>

		<div class="row">
			<?php echo _AT('date'); ?><br/>
			<?php echo $invoice_row['timestamp']; ?>
		</div>

		<div class="row">
			<?php echo _AT('login_name'); ?><br/>
			<?php echo get_login($invoice_row['member_id']); ?>
		</div>

		<div class="row">
			<?php echo _AT('course'); ?><br/>
			<?php echo $system_courses[$invoice_row['course_id']]['title']; ?>
		</div>

		<div class="row">
			<?php echo _AT('ec_amount'); ?><br/>
			<?php echo $_config['ec_currency_symbol']; ?><?php echo $invoice_row['amount']; ?> <?php echo $_config['ec_currency']; ?>
		</div>

		<div class="row">
			<label for="txid"><?php echo _AT('ec_transaction_id'); ?></label><br/>
			<input type="text" id="txid" name="txid" value="" size="30"/>
		</div>


		<div class="row buttons">
			<input type="submit" name="submit" value="<?php echo _AT('approve'); ?>"/>
			<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>"/>
		</div>
	</div>
	</form>
<?php endif; ?>
<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>