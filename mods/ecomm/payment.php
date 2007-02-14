<?php

$_user_location	= 'users';
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
$_custom_css = $_base_path . 'mods/hello_world/module.css'; // use a custom stylesheet

$success_url = $_base_href . 'mods/ecomm/success.php?mid='.$_SESSION['member_id'].SEP.'cid='.$_REQUEST['course_id'];
$failed_url =$_base_href . 'mods/ecomm/failed.php?mid='.$_SESSION['member_id'].SEP.'cid='.$_REQUEST['course_id'];

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


	//$course = htmlspecialchars(addslashes($_REQUEST['project']));
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
			$error .= '<li>'._AT('ec_amount').'</li>';
		}
		//if($service && ($tmp_amount != $amount) && $invoice_id == ''){
		//	$error .= '<li>Invoice/Quote ID (required if amount has changed)</li>';
		//}
		if($error){
			$error = '<div style="border:thick red solid; width: 400px; align:centre; padding:2em;"><strong>'._AT('ec_invalid_fields').'</strong><br /><ul>'.$error.'</ul></div>';
		}
	}


?>
<h2><?php echo _AT('ec_payments_gateway'); ?></h2>
<div style="border:3px solid rgb(112, 161, 202); background-color: rgb(235, 244, 249); padding: 1em;  margin-left: auto; margin-right: auto; width: 70%;" >



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
	
	<h4><?php echo _AT('ec_requirements'); ?>Requirements to procede</h4>
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
		<input type="hidden"  name="service" value="<?php echo $service; ?>">
		<input type="hidden"  name="invoice_id" value="<?php echo $invoice_id; ?>">
		<input type="hidden"  name="invoice" value="<?php echo $invoice; ?>">
		<!-- Info sent to mirapay -->
		<input type="hidden" name="MTID" value="<?php echo $_SESSION['MTID']; ?>">
		<input type="hidden" name="Merchant_ID" value="<?php echo MERCHANT_ID; ?>">
		<input type="hidden"  name="MKEY" value="<?php echo $mkey; ?>">
		<input type="hidden"  name="amount" value="<?php echo $amount; ?>">
		<input type="hidden"  name="SuccessURL" value="http://www.atutor.ca/payment/success.php">
		<input type="hidden"  name="FailURL" value="http://www.atutor.ca/payment/failure.php">
		<input type="hidden"  name="EMail" value="<?php echo $email; ?>">
		<input type="submit" name="confirm" value="<?php echo _AT('ec_confirm'); ?>"><input type="submit" name="modify" value="<?php echo _AT('ec_modify'); ?>">
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
		project='$course', 
		comments = '$comment', 
		course_id='$course_id', 
		invoice_id = '$invoice_id'";
	
	if($result = mysql_query($sql, $db)){
		$update = 1;
	}else{
		$update = 2;
	}
	
	$mtid = mysql_insert_id($db);

		$successurl = "http://www.atutor.ca/shop/success.php";
		$failurl = "http://www.atutor.ca/shop/fail.php";
		$mkey = md5($mtid.$amount.$password);

		//if($invoice){
		//	$contribinfo = '<dt><strong>Invoice #</strong>: '.$invoice_id.' </dt>';	
		//}

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
		<input type="submit" name="confirm" value="<?php echo _AT('ec_paybycredit'); ?>"> &nbsp;<img src="<?php echo $_base_path; ?>mods/ecomm/images/visa_42x27.gif" title="<?php echo _AT('ec_acceptvisa'); ?>" alt="<?php echo _AT('ec_acceptvisa'); ?>" align="middle" /> <img src="<?php echo $_base_path; ?>mods/ecomm/images/mc_42x27.gif" title="<?php echo _AT('ec_acceptmastercard'); ?>" alt="<?php echo _AT('ec_acceptmastercard'); ?>" align="middle" />
	</form><br/><br />

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
/*	if($_REQUEST['service']){?>
			<!--h3>Service Purchase: <?php echo $service; ?></h3>
			<ul>
			<li>Replace the amount below with an Invoice or Quote amount if you have been provided with one. </li>
			
	
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
			<input type="hidden" name="service" value="<?php echo $service;  ?>" />
			<input type="hidden" name="tmp_amount" value="<?php echo $tmp_amount;  ?>" />
			Amount $ <input type="text" size="8" maxlength="8" name="amount2" class="input" value="<?php echo $amount; ?>" /> CAD
			 <img src="<?php echo $_base_path; ?>mods/ecomm/images/visa_42x27.gif" alt="Accepting Visa" align="middle"/> <img src="<?php echo $_base_path; ?>mods/ecomm/images/mc_42x27.gif" alt="Accepting Master Card"  align="middle"/><br /><br />
			Invoice/Quote ID <input type="text" size="8" maxlength="8" name="invoice_id" class="input" value="<?php echo $invoice_id; ?>" /> <strong><small>(required if amount above changes)</small></strong>

		<br /><br /></li></ul>
		<?php 
 }else if($invoice)	{ ?>
			<h3>Invoice Payment</h3>
			<ul>
			<li>Enter the Total Amount listed on the invoice into the Amount field below, and enter the invoice number into the Invoice # field.  </li>
			
	
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
			<input type="hidden" name="invoice" value="<?php echo $invoice;  ?>" />
			Amount $ <input type="text" size="8" maxlength="8" name="amount2" class="input" value="<?php echo $amount; ?>" /> CAD
			<br /><br />
			Invoice # &nbsp;<input type="text" size="12" maxlength="12" name="invoice_id" class="input" value="<?php echo $invoice_id; ?>" /> <br /><br />
			Payment Methods:  <img src="<?php echo $_base_path; ?>mods/ecommimages/visa_42x27.gif" alt="Accepting Visa" align="middle"/> <img src="<?php echo $_base_path; ?>mods/ecomm/images/mc_42x27.gif" alt="Accepting Master Card"  align="middle"/>

		<br /><br /></li></ul -->
	
<?php 
  }else{
		/// Contributor Shop Page
		?>
		<h3><?php echo _AT('ec_payfeesfor'); ?>: <?php echo $course; ?></h3>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
		<input type="hidden" name="course" value="<?php echo $course  ?>" />
		<ol>
		<li>Choose an amount from the selector, or type the amount you wish to contribute to this project:</li>
		<li>Amount is calculated in Canadian Dollars.</li>
		</ol>
		<strong><?php echo _AT('ec_amount'); ?></strong>:  $<select name="amount1" class="input" ><option></option>
		<option>10</option>
		<option>25</option>
		<option>50</option>
		<option>100</option>
		<option>500</option>
		<option>1000</option>
		<option>5000</option>
		
		</select>  or <input type="text" size="8" maxlength="8" name="amount2" class="input" value="<?php echo $amount; ?>" /> CAD
		 <img src="<?php echo $_base_path; ?>mods/ecomm/images/visa_42x27.gif" alt="<?php echo _AT('ec_acceptvisa'); ?>" align="middle"/> <img src="<?php echo $_base_path; ?>mods/ecomm/images/mc_42x27.gif" alt="<?php echo _AT('ec_acceptmastercard'); ?>"  align="middle"/><br /><br />  
<?php }  */ ?>

		<?php
		$sql = "SELECT course_fee from ".TABLE_PREFIX."ec_course_fees WHERE course_id = '$course_id'; ";
		$result = mysql_query($sql, $db);
		$this_course_fee = mysql_result($result, $row['0']);
		?>
		<h3><?php echo _AT('ec_payfeesfor'); ?>: </h3><br /><br />
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
		<input type="hidden" name="course" value="<?php echo $course  ?>" />
		<input type="hidden" name="amount1" value="<?php echo $this_course_fee  ?>" />
		<div style="margin-left: 2em;"><br />
		<?php
		 echo '<strong>'.$course.'</strong><br />'; 
		 echo '<strong>'. _AT('ec_this_course_fee').'</strong>: '.$_config['ec_currency_symbol'].' '.$this_course_fee.' '.$_config['ec_currency'];

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
	<span style="color:red; font-size:15pt;">*</span><span><?php echo _AT('ec_required'); ?></span>
		<input type="hidden" name="member_id" value="<?php  echo $_SESSION['member_id']; ?>" />
		<input type="hidden" name="course_id" value="<?php echo $course_id;  ?>" />
		<table>
			<?php
			if($service){ ?>
			<tr><td><label for="service">ATutor Service</label></td><td><input type="text" id="service" name="service" value="<?php echo $service; ?>" size="30" class="input" /></td></tr>
			<?php }else if(!$invoice && !$service){?>
			<tr><td><label for="project"><?php echo _AT('ec_course'); ?></label></td><td><input type="text" id="project" name="course" value="<?php echo $course; ?>" size="30" class="input" /></td></tr>
			<?php } ?>
			<tr><td><span style="color:red; font-size:15pt;">*</span><label for="firstname"><?php echo _AT('ec_firstname'); ?></label>:</td><td><input type="text" id="firstname" name="firstname" value="<?php echo $firstname; ?>" size="30"  class="input"/></td></tr>
			<tr><td><span style="color:red; font-size:15pt;">*</span><label for="lastname"><?php echo _AT('ec_lastname'); ?></label>:</td><td><input type="text" id="lastname" name="lastname" value="<?php echo $lastname; ?>" size="30" class="input" /></td></tr>
			<tr><td><span style="color:red; font-size:15pt;">*</span><label for="email"><?php echo _AT('ec_lastname'); ?>:</label> </td><td><input type="text" id="email" name="EMail" value="<?php echo $email; ?>" size="40" class="input"/></td></tr>
			<tr><td><label for="org"><?php echo _AT('ec_organization'); ?></label>:</td><td><input type="text" id="org" name="organization" value="<?php echo $organization; ?>" size="30"  class="input"/></td></tr>
			<tr><td><span style="color:red; font-size:15pt;">*</span><label for="address"><?php echo _AT('ec_address'); ?></label>: </td><td><textarea type="text" id="address" name="address" cols="40" rows="5" class="input"><?php echo $address; ?></textarea></td></tr>
			<tr><td><span style="color:red; font-size:15pt;">*</span><label for="postal"><?php echo _AT('ec_postal'); ?></label></td><td><input type="text" id="postal" name="postal" value="<?php echo $postal; ?>" size="10" class="input" /></td></tr>
			<tr><td><span style="color:red; font-size:15pt;">*</span><label for="tele"><?php echo _AT('ec_telephone'); ?></label>: </td><td><input type="text" id="tele" name="telephone" value="<?php echo $telephone; ?>"  class="input" /></td></tr>
			<tr><td><span style="color:red; font-size:15pt;">*</span><label for="country"><?php echo _AT('ec_country'); ?></label>: </td><td><input type="text" id="country" name="country" value="<?php echo $country; ?>" class="input" /></td></tr> 
			<tr><td><label for="comment"><?php echo _AT('ec_comments'); ?></label>: </td><td><textarea type="text" id="comment" name="comment" cols="40" rows="5" class="input"><?php echo $comment; ?></textarea></td></tr>
		</table>
		<input type="hidden" name="amount" value="<?php echo $amount; ?>">
		<input type="submit" name="next" value="<?php echo _AT('ec_next_step'); ?>">
	</form>
	
<?php
}

//////// End of screen 1
?>

</div>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>