<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }

function paypal_print_form($payment_id, $amount, $course_id) {
	global $_config, $system_courses;
?>
	<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
	<input type="hidden" name="add" value="1">
	<input type="hidden" name="cmd" value="_cart"/>
	<input type="hidden" name="business" value="<?php echo $_config['ec_email']; ?>"/>
	<input type="hidden" name="item_number" value="<?php echo $payment_id; ?>"/>
	<input type="hidden" name="amount" value="<?php echo $amount; ?>"/>
	<input type="hidden" name="item_name" value="<?php echo htmlspecialchars($system_courses[$course_id]['title']); ?>"/>
	<input type="hidden" name="page_style" value="PayPal"/>
	<input type="hidden" name="no_shipping" value="1"/>
	<input type="hidden" name="return" value="<?php echo AT_BASE_HREF; ?>mods/ecomm/response_user.php"/>
	<input type="hidden" name="currency_code" value="CAD"/>
	<input type="hidden" name="lc" value="CA"/>
	<input type="hidden" name="bn" value="PP-ShopCartBF"/>
	<input type="hidden" name="no_note" value="0"/>
	<input type="hidden" name="quantity"" value="1"/>
	<input type="hidden" name="undefined_quantity" value="1"/>

	<input type="submit" name="confirm" class="button" value="<?php echo _AT('ec_paypal'); ?>"/>

	</form>
<?php
}

function paypal_authenticate_ipn() {
	// real authentication goes here
}

function paypal_authenticate_user_response() {
	// nothing to do but set the feedback
	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
}

function mirapay_print_form($payment_id, $amount, $course_id) {
	global $_config;
	$mkey = md5($payment_id.$amount.$_config['ec_password']);
?>
	<form method="post" action="<?php echo $_config['ec_uri']; ?>">
		<input type="hidden" name="MTID"        value="<?php echo $payment_id; ?>"/>
		<input type="hidden" name="Merchant_ID" value="<?php echo $_config['ec_vendor_id']; ?>"/>
		<input type="hidden" name="MKEY"        value="<?php echo $mkey; ?>"/>
		<input type="hidden" name="Amount1"     value="<?php echo $amount; ?>"/>
		<input type="hidden" name="SuccessURL"  value="<?php echo AT_BASE_HREF; ?>mods/ecomm/response_user.php"/>
		<input type="hidden" name="FailURL"     value="<?php echo AT_BASE_HREF; ?>mods/ecomm/response_user.php"/>
		<input type="hidden" name="Currency"    value="<?php if ($_config['ec_currency'] == 'CAD') { echo 'CA'; } else { echo 'US'; } ?>"/>

		<input type="submit" name="confirm" class="button" value="<?php echo _AT('ec_paybycredit'); ?>"/> 

		<img src="<?php echo $_base_path; ?>mods/ecomm/images/visa_42x27.gif" title="<?php echo _AT('ec_acceptvisa'); ?>" alt="<?php echo _AT('ec_acceptvisa'); ?>" align="middle" /> <img src="<?php echo $_base_path; ?>mods/ecomm/images/mc_42x27.gif" title="<?php echo _AT('ec_acceptmastercard'); ?>" alt="<?php echo _AT('ec_acceptmastercard'); ?>" align="middle" />
	</form>
<?php
}

function mirapay_authenticate_ipn() {
	// nothing to do
}

function mirapay_authenticate_user_response( ) {
	if (isset($_GET['MTID'], $_GET['Amount1'], $_GET['MiraID'], $_GET['Response'])) {
		global $_config, $msg;
		$response_hash = md5($_GET['MTID'] . $_GET['Amount1'] . $_GET['MiraID'] . $_GET['Response'] . $_config['ec_password']);
		if ($response_hash == $_GET['MKEY'] && !strcasecmp($_GET['Response'], 'APPROVED')) {
			approve_payment($_GET['MTID'], $_GET['MiraID']);
			$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		} else {
			$msg->addError('EC_PAYMENT_FAILED');
		}
	}
}

function approve_payment($payment_id, $transaction_id) {
	global $db, $system_courses, $_config;

	$sql = "UPDATE ".TABLE_PREFIX."payments SET transaction_id='$transaction_id', approved=1 WHERE payment_id=$payment_id";
	$result = mysql_query($sql, $db);

	// get the course_id for this transaction
	$sql = "SELECT course_id, member_id FROM ".TABLE_PREFIX."payments WHERE payment_id=$payment_id";
	$result = mysql_query($sql, $db);
	$row = mysql_fetch_assoc($result);
	$course_id = $row['course_id'];
	$member_id = $row['member_id'];

	$sql = "SELECT * FROM ".TABLE_PREFIX."ec_course_fees WHERE course_id=$course_id";
	$result = mysql_query($sql,$db);
	$course_fee_row = mysql_fetch_assoc($result);

	$sql = "UPDATE ".TABLE_PREFIX."course_enrollment SET approved='y' WHERE member_id=$member_id AND course_id=$course_id";
	mysql_query($sql, $db);

	/// Get the course title
	$course_title  = $system_courses[$course_id]['title'];

	require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');
	// If auto email when payment is made, send an email to the instructor (maybe this should be an admin option)
	if ($course_fee_row['auto_email']) {

		/// Get the instructor's email address
		$sql = "SELECT email FROM ".TABLE_PREFIX."members WHERE member_id=".$system_courses[$course_id]['member_id'];
		$result = mysql_query($sql,$db);
		$row = mysql_fetch_assoc($result);
		$instructor_email = $row['email'];	
			
		$mail = new ATutorMailer;
		$mail->From     = $_config['contact_email'];
		$mail->AddAddress($instructor_email);
		$mail->Subject = _AT('ec_payment_made'); 
		$mail->Body    = _AT('ec_payment_mail_instruction', $course_title);
			
		if(!$mail->Send()) {
			$msg->printErrors('SENDING_ERROR');
			exit;
		}
		$mail->ClearAddresses();
	}

	if ($_config['ec_email_admin']) {
		/// Email Administrator  if set
		if ($_config['ec_email_admin']){
			if ($_config['ec_contact_email']){
				$contact_admin_email = $_config['ec_contact_email'];
			} else {
				$contact_admin_email = $_config['contact_email'];
			}
			$mail = new ATutorMailer;
			$mail->From     = $_config['contact_email'];
			$mail->AddAddress($contact_admin_email);
			$mail->Subject = _AT('ec_payment_made'); 
			$mail->Body    = _AT('ec_admin_payment_mail_instruction', $course_title);
			
			if (!$mail->Send()) {
				$msg->printErrors('SENDING_ERROR');
				exit;
			}
		}
	}
}

function check_payment_print_form($payment_id, $amount, $course_id){
global $db, $system_courses, $_config;

if($_config['ec_contact_address'] != ''){ 
?>

	<form  method="GET">
		<input type="hidden"  name="Amount1" value="<?php echo $amount; ?>">
		<input type="hidden"  name="mtid" value="<?php echo $mtid; ?>">
		<input class="button" type="submit" name="bycheque" value="<?php echo _AT('ec_paybycheque'); ?>" onclick="window.open('mods/ecomm/invoice.php?mtid=<?php echo $mtid.SEP; ?>amount=<?php echo $amount; ?>','invwindow','height=425px, width=520px'); return false" /> 
	</form><br/><br />
<?php }
}
?>