<?php

$_user_location	= 'users';
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
$_custom_css = $_base_path . 'mods/hello_world/module.css'; // use a custom stylesheet

$success_url = $_base_href . 'mods/ecomm/index_mystart.php?mid='.$_SESSION['member_id'].SEP.'cid='.$_REQUEST['course_id'];
$failed_url =$_base_href . 'mods/ecomm/failure.php';


if($_POST['cancel']){
	header('location:'.$failed_url);
	exit;
}

//echo $success_url;
//echo $_base_href;
///////////// Mira pay stuff
$password = $_config['ec_password'];
define(MERCHANT_ID, $_config['ec_vendor_id']);
/*
test
e911ca205ec7c2a9436af9102c497fd4
https://www3.eigendev.com/mirapay/secure_credit.php
*/
////////////////

require (AT_INCLUDE_PATH.'header.inc.php');

///Validate form fields
	if($_POST['amount1']){
		$amount = $_POST['amount1'];
	}else if($_POST['amount2']){
		$amount = $_POST['amount2'];
	}else if($_POST['amount']){
		$amount = $_POST['amount'];
	}else if($_GET['amount']){
		$amount = $_GET['amount'];
	}

	$member_id = intval($_POST['member_id']);

	if($_REQUEST['course_id'] != 0){
		$sql = "SELECT title from ".TABLE_PREFIX."courses WHERE course_id = '$_REQUEST[course_id]'";
		$result = mysql_query($sql, $db);
		while($row = mysql_fetch_assoc($result)){
			$course = $row['title'];
		}
	}else{
		$course = htmlspecialchars(addslashes($_REQUEST['course']));
	}
	$amount = floatval($amount);
	$firstname = htmlspecialchars(addslashes($_POST['firstname']));
	$lastname= htmlspecialchars(addslashes($_POST['lastname']));
	$email = htmlspecialchars(addslashes($_POST['EMail']));
	$organization = htmlspecialchars(addslashes($_POST['organization']));
	$address = htmlspecialchars(addslashes($_POST['address']));
	$postal = htmlspecialchars(addslashes($_POST['postal']));
	$telephone = htmlspecialchars(addslashes($_POST['telephone']));
	$country = htmlspecialchars(addslashes($_POST['country']));
	$comment = htmlspecialchars(addslashes($_POST['comment']));
	//$service  = htmlspecialchars(addslashes($_REQUEST['service']));
	$course_id = intval($_REQUEST['course_id']);
	//$invoice_id = htmlspecialchars(addslashes($_POST['invoice_id']));
	//$invoice = htmlspecialchars(addslashes($_REQUEST['invoice']));

	if($_GET['amount']){
		$tmp_amount = htmlspecialchars(addslashes($_GET['amount']));
	}else if($_POST['tmp_amount']){
		$tmp_amount = $_POST['tmp_amount'];
	}

	/// Generate error if no amount is entered
	if($_POST['next'] && $amount == ''){
		$error ='<li>'._AT('ec_amount').'</li>';
	}
	
	if($_POST['next'] && $invoice_id == '' && $invoice){
		$error.='<li>Invoice #</li>';
	}
	if($_POST['next']){
		if($firstname == ''){
			$error .= '<li>'._AT('ec_firstname').'</li>';
		}
		if($lastname == ''){
				$error .= '<li>'._AT('ec_lastname').'</li>';
		}
		if($_POST['amount2'] && !preg_match("/^\d{0,9}(\.\d{0,2})?$/", $_POST['amount2'])){
			$error .=  '<li>'._AT('ec_amount').'</li>';
		}
		if($email == ''){
			$error .= '<li>'._AT('ec_email').'</li>';
		}else if($_POST['next'] && !eregi("^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,6}$", $_POST['EMail'])){
			$error .=  '<li>'._AT('ec_email').'</li>';
		}
		if($address == ''){
			$error .= '<li>'._AT('ec_address').'</li>';
		}
		if($postal == ''){
			$error .= '<li>'._AT('ec_postal').'</li>';
		}
		if($telephone == ''){
			$error .= '<li>'._AT('ec_telephone').'</li>';
		}
		if($country == ''){
			$error .= '<li>'._AT('ec_country').'</li>';
		}
		//if($service && ($tmp_amount != $amount) && $invoice_id == ''){
		//	$error .= '<li>Invoice/Quote ID (required if amount has changed)</li>';
		//}
		if($error){
			$error = '<div style="border:thick red solid; width: 400px; align:centre; padding:2em;"><strong>'._AT('ec_invalid_fields').'</strong><br /><ul>'.$error.'</ul></div>';
		}
	}


?>

<div style="border:1px solid rgb(112, 161, 202); background-color: rgb(235, 244, 249); padding: 1em;  margin-left: auto; margin-right: auto; width: 70%;" >



<?php

//////////
/// Screen 2
/// Confirm the information entered
///////////////
if($_POST['next'] && !$error){

	echo _AT('ec_confirm_info');
	if($course){
		$contribinfo = '<dt><strong>'._AT('ec_course').'</strong>: '.$course.' </dt>';
	}
	$contribinfo .= '<dt><strong>'._AT('ec_amount').'</strong>: $'.$amount.' </dt>';
	
	if($firstname){
		$contribinfo .= '<dt><strong>'._AT('ec_firstname').'</strong>: '.$firstname.'</dt>';
	}
	if($lastname){
		$contribinfo .= '<dt><strong>'._AT('ec_lastname').'</strong>: '.$lastname.'</dt>';
	}
	if($organization){
		$contribinfo .= '<dt><strong>'._AT('ec_organization').'</strong>: '.$organization.'</dt>';
	}
	if($email){
		$contribinfo .= '<dt><strong>'._AT('ec_email').'</strong>: '.$email.'</dt>';
	}
	if($address){
		$contribinfo .= '<dt><strong>'._AT('ec_address').'</strong>: '.$address.'</dt>';
	}
	if($postal){
		$contribinfo .= '<dt><strong>'._AT('ec_postal').'</strong>: '.$postal.'</dt>';
	}
	if($telephone){
		$contribinfo .= '<dt><strong>'._AT('ec_telephone').'</strong>: '.$telephone.'</dt>';
	}
	if($country){
		$contribinfo .= '<dt><strong>'._AT('ec_country').'</strong>: '.$country.'</dt>';
	}
	if($comment){
		$contribinfo .= '<dt><strong>'._AT('ec_comments').'</strong>: '.$comment.'</dt>';
	}
	
	$contribinfo = '<dl>'.$contribinfo.'</dl>';

	echo $contribinfo;
?>

	<br />
	
	<h4><?php echo _AT('ec_requirements'); ?></h4>
	<ul>
	<li><?php echo _AT('ec_requirements_ssl'); ?></li>
	<li><?php echo _AT('ec_requirements_cookies'); ?></li>
	<li><?php echo _AT('ec_requirements_javascript'); ?></li>
	<p><strong><?php echo _AT('ec_requirements_comments'); ?></strong><p>
	</ul>
	
	<form name="purchase_form" method="post"
	action="<?php  echo $_SERVER['PHP_SELF']; ?>">
		<!-- stored  for history -->
		<input type="hidden"  name="member_id" value="<?php echo $_SESSION['member_id']; ?>">
		<input type="hidden"  name="firstname" value="<?php echo $firstname; ?>">
		<input type="hidden"  name="lastname" value="<?php echo $lastname; ?>">
		<input type="hidden"  name="organization" value="<?php echo $organization; ?>">
		<input type="hidden"  name="address" value="<?php echo $address; ?>">
		<input type="hidden"  name="postal" value="<?php echo $postal; ?>">
		<input type="hidden"  name="telephone" value="<?php echo $telephone; ?>">
		<input type="hidden"  name="country" value="<?php echo $country; ?>">
		<input type="hidden"  name="receipt" value="<?php echo $receipt; ?>">
		<input type="hidden"  name="course" value="<?php echo $course; ?>">
		<input type="hidden"  name="comment" value="<?php echo $comment; ?>">
		<input type="hidden"  name="course_id" value="<?php echo $course_id; ?>">

		<!-- Info sent to mirapay -->
		<input type="hidden" name="MTID" value="<?php echo $_SESSION['MTID']; ?>">
		<input type="hidden" name="Merchant_ID" value="<?php echo MERCHANT_ID; ?>">
		<input type="hidden"  name="MKEY" value="<?php echo $mkey; ?>">
		<input type="hidden"  name="amount" value="<?php echo $amount; ?>">
		<input type="hidden"  name="SuccessURL" value="<?php echo $success_url; ?>">
		<input type="hidden"  name="FailURL" value="<?php echo $failed_url; ?>">
		<input type="hidden"  name="EMail" value="<?php echo $email; ?>">
		<input type="submit" class="button" name="confirm" value="<?php echo _AT('ec_confirm'); ?>"><input class="button" type="submit" name="modify" value="<?php echo _AT('ec_modify'); ?>"><input type="submit" class="button" name="cancel" value="<?php echo _AT('ec_cancel'); ?>">
	</form>
	
	<?php
	if($result = mysql_query($sql, $db)){
		$update = 1;
	}else{
		$update = 1;
	}


}else if($_POST['confirm']){ 

	/////////
	/// Screen 3
	/// Advance to payment mediator screen
	///////////////
	echo  _AT('ec_select_creditcard');

	//if($service){
	//$course = $service;
	//}
	$sql = "INSERT into ".TABLE_PREFIX."ec_shop  set 
		shopid='', 
		member_id = '$_SESSION[member_id]', 
		firstname='$firstname', 
		lastname='$lastname', 
		email='$email', 
		organization='$organization', 
		address='$address', 
		postal='$postal', 
		telephone='$telephone', 	
		country='$country', 
		receipt='$receipt', 
		miraid='', 
		date=NOW(), 
		approval='', 
		course_name='$course', 
		comments = '$comment', 
		course_id='$course_id'";
	
		$result = mysql_query($sql, $db);

	
		$mtid = mysql_insert_id($db);
		$mkey = md5($mtid.$amount.$password);

		if($course){
			$contribinfo = '<dt><strong>'._AT('ec_course').'</strong>: '.$course.' </dt>';
		}
		$contribinfo .= '<dt><strong>'._AT('ec_amount').'</strong>: $'.$amount.' </dt>';
		
		if($firstname){
			$contribinfo .= '<dt><strong>'._AT('ec_firstname').'</strong>: '.$firstname.'</dt>';
		}
		if($lastname){
			$contribinfo .= '<dt><strong>'._AT('ec_lastname').'</strong>: '.$lastname.'</dt>';
		}
		if($organization){
			$contribinfo .= '<dt><strong>'._AT('ec_organization').'</strong>: '.$organization.'</dt>';
		}
		if($email){
			$contribinfo .= '<dt><strong>'._AT('ec_email').'</strong>: '.$email.'</dt>';
		}
		if($address){
			$contribinfo .= '<dt><strong>'._AT('ec_address').'</strong>: '.$address.'</dt>';
		}
		if($postal){
			$contribinfo .= '<dt><strong>'._AT('ec_postal').'</strong>: '.$postal.'</dt>';
		}
		if($telephone){
			$contribinfo .= '<dt><strong>'._AT('ec_telephone').'</strong>: '.$telephone.'</dt>';
		}
		if($country){
			$contribinfo .= '<dt><strong>'._AT('ec_country').'</strong>: '.$country.'</dt>';
		}
		if($comment){
			$contribinfo .= '<dt><strong>'._AT('ec_comments').'</strong>: '.$comment.'</dt>';
		}	
		$contribinfo = '<dl>'.$contribinfo.'</dl>';
		echo $contribinfo;

	?>

	<form name="purchase_form" method="post"
	action="<?php echo $_config['ec_uri']; ?>">
		<!-- stored  for history -->
		<input type="hidden"  name="firstname" value="<?php echo $firstname; ?>">
		<input type="hidden"  name="lastname" value="<?php echo $lastname; ?>">
		<input type="hidden"  name="organization" value="<?php echo $organization; ?>">
		<input type="hidden"  name="address" value="<?php echo $address; ?>">
		<input type="hidden"  name="postal" value="<?php echo $postal; ?>">
		<input type="hidden"  name="telephone" value="<?php echo $telephone; ?>">
		<input type="hidden"  name="country" value="<?php echo $country; ?>">
		<input type="hidden"  name="receipt" value="<?php echo $receipt; ?>">
		<input type="hidden"  name="course" value="<?php echo $course; ?>">
		<input type="hidden"  name="comment" value="<?php echo $comment; ?>">
		<input type="hidden"  name="tmp_amount" value="<?php echo $tmp_amount; ?>">
		<input type="hidden"  name="invoice_id" value="<?php echo $invoice_id; ?>">
		<input type="hidden"  name="invoice" value="<?php echo $invoice; ?>">
		<!-- Info sent to mirapay -->
		<input type="hidden" name="MTID" value="<?php echo $mtid; ?>">
		<input type="hidden" name="Merchant_ID" value="<?php echo MERCHANT_ID; ?>">
		<input type="hidden"  name="MKEY" value="<?php echo $mkey; ?>">
		<input type="hidden"  name="Amount1" value="<?php echo $amount; ?>">
		<input type="hidden"  name="SuccessURL" value="<?php echo $success_url; ?>">
		<input type="hidden"  name="FailURL" value="<?php echo $failed_url; ?>">
		<input type="hidden"  name="EMail" value="<?php echo $email; ?>">
		<input class="button" type="submit" name="confirm" value="<?php echo _AT('ec_paybycredit'); ?>"> &nbsp;<img src="<?php echo $_base_path; ?>mods/ecomm/images/visa_42x27.gif" title="<?php echo _AT('ec_acceptvisa'); ?>" alt="<?php echo _AT('ec_acceptvisa'); ?>" align="middle" /> <img src="<?php echo $_base_path; ?>mods/ecomm/images/mc_42x27.gif" title="<?php echo _AT('ec_acceptmastercard'); ?>" alt="<?php echo _AT('ec_acceptmastercard'); ?>" align="middle" />
	</form><?php echo _AT('or'); ?> 

	<form onclick="window.open('mods/ecomm/invoice.php', null, 'height=350,width=350,status=yes,toolbar=no,menubar=no,location=no'); return false;" method="post">

		<input type="hidden"  name="firstname" value="<?php echo $firstname; ?>">
		<input type="hidden"  name="lastname" value="<?php echo $lastname; ?>">
		<input type="hidden"  name="organization" value="<?php echo $organization; ?>">
		<input type="hidden"  name="address" value="<?php echo $address; ?>">
		<input type="hidden"  name="postal" value="<?php echo $postal; ?>">
		<input type="hidden"  name="telephone" value="<?php echo $telephone; ?>">
		<input type="hidden"  name="country" value="<?php echo $country; ?>">
		<input type="hidden"  name="receipt" value="<?php echo $receipt; ?>">
		<input type="hidden"  name="course" value="<?php echo $course; ?>">
		<input type="hidden"  name="comment" value="<?php echo $comment; ?>">
		<input type="hidden"  name="tmp_amount" value="<?php echo $tmp_amount; ?>">
		<input type="hidden"  name="invoice_id" value="<?php echo $invoice_id; ?>">
		<input type="hidden"  name="invoice" value="<?php echo $invoice; ?>">
		<input type="hidden"  name="Amount1" value="<?php echo $amount; ?>">
		<input type="hidden"  name="EMail" value="<?php echo $email; ?>">
		<input class="button" type="submit" name="bycheque" value="<?php echo _AT('ec_paybycheque'); ?>" > &nbsp;</form><br/><br />

	<?php

	/// End of screen 3


}else{
	
	//////////
	/// Screen 1
	/// User information form
	///////////////
	/// Gather member information if it exists to populate contributor form
	if($_SESSION['member_id']){
		$sql = "SELECT * from  ".TABLE_PREFIX."members WHERE member_id = '$_SESSION[member_id]'";
		$result = mysql_query($sql, $db);
		while($row = mysql_fetch_assoc($result)){
			$member['firstname'] = $row['first_name'];
			$member['lastname'] = $row['last_name'];
			$member['email'] = $row['email'];
			$member['organization'] = $row['organization'];
			$member['address'] = $row['address'];
			$member['postal'] = $row['postal'];
			$member['telephone'] = $row['telephone'];
			$member['country'] = $row['country'];
		}
	}
	/// If Payee is logged in, fill in form with info from member table 

	if($_SESSION['member_id']){
		$member_id = intval($_SESSION['member_id']);
	}else{
		$member_id = intval($_POST['member_id']);
	}

	if($member['firstname']){
		$firstname = $member['firstname'];
	}
	if($member['lastname']){
		$lastname = $member['lastname'];
	}
	if($member['email']){
		$email = $member['email'];
	}
	if($member['organization']){
		$organization = $member['organization'];
	}
	if($member['address']){
		$address = $member['address'];
	}
	if($member['postal']){
		$postal = $member['postal'];
	}
	if($member['telephone']){
		$telephone = $member['telephone'];
	}
	if($member['country']){
		$country = $member['country'];
	}
	echo $error;
	///Get the fee for the current course
	$sql = "SELECT course_fee from ".TABLE_PREFIX."ec_course_fees WHERE course_id = '$course_id'; ";
	$result = mysql_query($sql, $db);
	$this_course_fee = mysql_result($result, $row['0']);

	///Check if a partial payment has already been made so the balance can be calculated
	$sql4 = "SELECT amount from ".TABLE_PREFIX."ec_shop WHERE course_id = '$_REQUEST[course_id]' AND member_id = '$_SESSION[member_id]'";
	if($result4 = mysql_query($sql4,$db)){
		$amount_paid = '';
		while($row4 = mysql_fetch_array($result4)){
			$amount_paid = $amount_paid+$row4['0'];
		}
	}
	$balance_course_fee = ($this_course_fee - $amount_paid);
	?>
	<h3><?php echo _AT('ec_payfeesfor'); ?>: </h3>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
		<input type="hidden" name="course" value="<?php echo $course  ?>" />


		<div style="margin-left: 2em;"><br />
		<?php
		echo '<strong>'. _AT('ec_course_name').'</strong> '.$course.'</strong><br />'; 
		if($amount_paid > 0){
			echo '<strong>'. _AT('ec_this_course_fee').'</strong> '.$_config['ec_currency_symbol'].$this_course_fee.' '.$_config['ec_currency'].'<br />';
			echo '<strong>'. _AT('ec_amount_recieved').'</strong> '.$_config['ec_currency_symbol'].$amount_paid.'</strong><br />';
			echo '<strong>'. _AT('ec_balance_due').'</strong> '.$_config['ec_currency_symbol'].$balance_course_fee.'</strong><br />';
			$this_course_fee = $balance_course_fee;
		}else if($amount){
			echo '<strong>'. _AT('ec_this_course_fee').'</strong> '.$_config['ec_currency_symbol'].$this_course_fee.' '.$_config['ec_currency'].'<br />';
			echo '<strong>'. _AT('ec_amount_recieved').'</strong> '.$_config['ec_currency_symbol'].$amount_paid.'</strong><br />';
			echo '<strong>'. _AT('ec_balance_due').'</strong> '.$_config['ec_currency_symbol'].$amount.'</strong><br />';
			$this_course_fee = $amount;
		}else{
			echo '<strong>'. _AT('ec_this_course_fee').'</strong> '.$_config['ec_currency_symbol'].$this_course_fee.' '.$_config['ec_currency'];
		}

		?>
		<?php echo $_config['currency']; ?>
		<br /><br /><img src="<?php echo $_base_path; ?>mods/ecomm/images/visa_42x27.gif" alt="<?php echo _AT('ec_acceptvisa'); ?>" align="middle"/> <img src="<?php echo $_base_path; ?>mods/ecomm/images/mc_42x27.gif" alt="<?php echo _AT('ec_acceptmastercard'); ?>"  align="middle"/>  
		</div>
		<br />
		<ul>
			<li><?php echo _AT('ec_complete_thisinfo'); ?></li>
			<li><?php echo _AT('ec_next_toproceed'); ?></li>
		</ul>
		<br /><h3><?php echo _AT('ec_purchaser_info'); ?></h3>
		<input type="hidden" name="amount1" value="<?php echo $this_course_fee;  ?>" />
		<span style="color:red; font-size:15pt;">*</span><span><?php echo _AT('ec_required'); ?></span>
		<input type="hidden" name="member_id" value="<?php  echo $_SESSION['member_id']; ?>" />
		<input type="hidden" name="course_id" value="<?php echo $course_id;  ?>" />

		<table>

			<tr><td><span style="color:red; font-size:15pt;">*</span><label for="firstname"><?php echo _AT('ec_firstname'); ?></label>:</td><td><input type="text" id="firstname" name="firstname" value="<?php echo $firstname; ?>" size="30"  class="input"/></td></tr>
			<tr><td><span style="color:red; font-size:15pt;">*</span><label for="lastname"><?php echo _AT('ec_lastname'); ?></label>:</td><td><input type="text" id="lastname" name="lastname" value="<?php echo $lastname; ?>" size="30" class="input" /></td></tr>
			<tr><td><span style="color:red; font-size:15pt;">*</span><label for="email"><?php echo _AT('ec_email'); ?>:</label> </td><td><input type="text" id="email" name="EMail" value="<?php echo $email; ?>" size="40" class="input"/></td></tr>
			<tr><td><label for="org"><?php echo _AT('ec_organization'); ?></label>:</td><td><input type="text" id="org" name="organization" value="<?php echo $organization; ?>" size="30"  class="input"/></td></tr>
			<tr><td><span style="color:red; font-size:15pt;">*</span><label for="address"><?php echo _AT('ec_address'); ?></label>: </td><td><textarea type="text" id="address" name="address" cols="40" rows="5" class="input"><?php echo $address; ?></textarea></td></tr>
			<tr><td><span style="color:red; font-size:15pt;">*</span><label for="postal"><?php echo _AT('ec_postal'); ?></label></td><td><input type="text" id="postal" name="postal" value="<?php echo $postal; ?>" size="10" class="input" /></td></tr>
			<tr><td><span style="color:red; font-size:15pt;">*</span><label for="tele"><?php echo _AT('ec_telephone'); ?></label>: </td><td><input type="text" id="tele" name="telephone" value="<?php echo $telephone; ?>"  class="input" /></td></tr>
			<tr><td><span style="color:red; font-size:15pt;">*</span><label for="country"><?php echo _AT('ec_country'); ?></label>: </td><td><input type="text" id="country" name="country" value="<?php echo $country; ?>" class="input" /></td></tr> 
			<tr><td><label for="comment"><?php echo _AT('ec_comments'); ?></label>: </td><td><textarea type="text" id="comment" name="comment" cols="40" rows="5" class="input"><?php echo $comment; ?></textarea></td></tr>
		</table>
		<input type="hidden" name="amount" value="<?php echo $amount; ?>">
		<input class="button" type="submit" name="next" value="<?php echo _AT('ec_next_step'); ?>">
	</form>
	
<?php
}
//debug($_POST);
//////// End of screen 1
?>

</div>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>