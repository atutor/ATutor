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
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_ECOMM);

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: payments_admin.php');
	exit;
} else if (isset($_POST['submit'])) {

	$_POST['ec_uri']               = trim($_POST['ec_uri']);
	$_POST['ec_vendor_id']         = trim($_POST['ec_vendor_id']);
	$_POST['ec_password']          = trim($_POST['ec_password']);
	$_POST['ec_contact_email']     = trim($_POST['ec_contact_email']);
	$_POST['ec_contact_address']   = trim($_POST['ec_contact_address']);
	$_POST['ec_allow_instructors'] = intval($_POST['ec_allow_instructors']);
	$_POST['ec_email_admin']       = intval($_POST['ec_email_admin']);
	$_POST['ec_email_admin']       = intval($_POST['ec_email_admin']);
	$_POST['ec_log_file']       = $addslashes($_POST['ec_log_file']);
	$_POST['ec_store_log']       = intval($_POST['ec_store_log']);


	if (!$_POST['ec_uri']){
		$msg->addError('EC_URL_EMPTY');
	}
	if (!$_POST['ec_vendor_id']){
		$msg->addError('EC_ID_EMPTY');
	}
	//if (!$_POST['ec_password']){
		//$msg->addError('EC_PASSWORD_EMPTY');
	//}		
	if (!$msg->containsErrors()) {
		$_POST['ec_gateway'] = $addslashes($_POST['ec_gateway']);
		$sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES ('ec_gateway', '$_POST[ec_gateway]')";
		mysql_query($sql, $db);

		$_POST['ec_uri'] = $addslashes($_POST['ec_uri']);
		$sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES ('ec_uri', '$_POST[ec_uri]')";
		mysql_query($sql, $db);

		$_POST['ec_vendor_id'] = $addslashes($_POST['ec_vendor_id']);
		$sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES ('ec_vendor_id', '$_POST[ec_vendor_id]')";
		mysql_query($sql, $db);

		$_POST['ec_password'] = $addslashes($_POST['ec_password']);
		$sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES ('ec_password', '$_POST[ec_password]')";
		mysql_query($sql, $db);

		$_POST['ec_currency'] = $addslashes($_POST['ec_currency']);
		$sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES ('ec_currency', '$_POST[ec_currency]')";
		mysql_query($sql, $db);

		$_POST['ec_currency_other'] = $addslashes($_POST['ec_currency_other']);
		$sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES ('ec_currency_other', '$_POST[ec_currency_other]')";
		mysql_query($sql, $db);

		if($_POST['ec_currency_other']){
			$sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES ('ec_currency', '')";
			mysql_query($sql, $db);
		}

		$_POST['ec_currency_symbol'] = $_POST['ec_currency_symbol'];
		$sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES ('ec_currency_symbol', '$_POST[ec_currency_symbol]')";
		mysql_query($sql, $db);

		$sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES ('ec_allow_instructors', '{$_POST['ec_allow_instructors']}')";
		mysql_query($sql, $db);

		$sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES ('ec_email_admin', '{$_POST['ec_email_admin']}')";
		mysql_query($sql, $db);

		$sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES ('ec_store_log', '{$_POST['ec_store_log']}')";
		mysql_query($sql, $db);

		$sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES ('ec_log_file', '{$_POST['ec_log_file']}')";
		mysql_query($sql, $db);

		$_POST['ec_contact_email'] = $addslashes($_POST['ec_contact_email']);
		$sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES ('ec_contact_email', '$_POST[ec_contact_email]')";
		mysql_query($sql, $db);

		$_POST['ec_contact_address'] = $addslashes($_POST['ec_contact_address']);
		$sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES ('ec_contact_address', '$_POST[ec_contact_address]')";
		mysql_query($sql, $db);

		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');

		header('Location: '.$_SERVER['PHP_SELF']);
		exit;
	}
}

$_config['ec_allow_instructors'] = isset($_config['ec_allow_instructors']) ? $_config['ec_allow_instructors'] : 0;
$_config['ec_email_admin']       = isset($_config['ec_email_admin'])       ? $_config['ec_email_admin']       : 0;
$_config['ec_uri']               = isset($_config['ec_uri'])               ? $_config['ec_uri']               : '';
$_config['ec_currency']          = isset($_config['ec_currency'])          ? $_config['ec_currency']          : 'USD';
$_config['ec_currency_symbol']   = isset($_config['ec_currency_symbol'])   ? $_config['ec_currency_symbol']   : '$';

require (AT_INCLUDE_PATH.'header.inc.php');

?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
	<div class="input-form">

		<div class="row">
			<p><?php echo _AT('ec_location_text'); ?></p>
		</div>
		<div class="row">
			<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="uri"><?php echo _AT('ec_gateway'); ?></label><br/>
			<select name="ec_gateway">
				<option value="BeanStream"<?php if($_config['ec_gateway']  == "BeanStream"){ echo ' selected="selected"';} ?>>BeanStream</option>
				<option value="PayPal" <?php if($_config['ec_gateway']  == "PayPal"){ echo ' selected="selected"';} ?>>PayPal</option>
				<option value="MiraPay"<?php if($_config['ec_gateway']  == "MiraPay"){ echo ' selected="selected"';} ?>>MiraPay</option>
			</select>
		</div>

		<div class="row">
			<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="uri"><?php echo _AT('ec_location'); ?></label><br/>
			<input type="text" name="ec_uri" value="<?php echo htmlspecialchars($_config['ec_uri']); ?>" id="uri" size="80"  />
		</div>
		<div class="row">
			<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="ec_vendor_id"><?php echo _AT('ec_vendor_id'); ?></label><br/>
			<input type="text" name="ec_vendor_id" value="<?php echo htmlspecialchars($_config['ec_vendor_id']); ?>" id="ec_vendor_id" size="40"/>
		</div>
		<div class="row">
			<label for="ec_password"><?php echo _AT('ec_password'); ?></label><br/>
				<input type="password" name="ec_password" value="<?php echo htmlspecialchars($_config['ec_password']); ?>" id="ec_password" size="20" />
		</div>
		<div class="row">
			<?php echo _AT('ec_currency'); ?><br/>
			<input type="radio" name="ec_currency" value="USD" id="currusd" <?php if ($_config['ec_currency'] == 'USD') { echo 'checked="checked"'; } ?>><label for="currusd">USD</label>
			<input type="radio" name="ec_currency" value="CAD" id="currcad" <?php if ($_config['ec_currency'] == 'CAD') { echo 'checked="checked"'; } ?>><label for="currcad">CAD</label>
			<input type="radio" name="ec_currency" value="EUR" id="curreur" <?php if ($_config['ec_currency'] == 'EUR') { echo 'checked="checked"'; } ?>><label for="curreur">EUR</label>&nbsp;&nbsp;

			<?php echo _AT('or'); ?>

			<label for="ec_currency_other"><?php echo _AT('ec_currency_other'); ?></label>
			<input type="text" name="ec_currency_other" size="3" value="<?php echo $_config['ec_currency_other']; ?>" id="ec_currency_other" size="3" />
		</div>
		<div class="row">
			<label for="ec_currency_symbol"><?php echo _AT('ec_currency_symbol'); ?></label><br/>
			<input type="text" name="ec_currency_symbol" size="3" value="<?php echo $_config['ec_currency_symbol']; ?>" id="ec_currency_symbol" size="3" />
		</div>
		<div class="row">
			<label for="ec_contact_email"><?php echo _AT('ec_contact_email'); ?></label><br/>
			<input type="text" name="ec_contact_email" size="50" value="<?php echo htmlspecialchars($_config['ec_contact_email']); ?>" id="ec_contact_email" size="20" />
		</div>

		<div class="row">
			<label for="ec_contact_address"><?php echo _AT('ec_contact_address'); ?></label><br/>
			<textarea  name="ec_contact_address" id="ec_contact_address"  cols="20" rows="5" class="input"/><?php echo htmlspecialchars($_config['ec_contact_address']); ?></textarea>
		</div>

		<div class="row">
			<?php echo _AT('ec_allow_instructors'); ?><br/>
			<input type="radio" name="ec_allow_instructors" value="1" id="allow1" <?php if ($_config['ec_allow_instructors']){ echo 'checked="checked"'; } ?>/><label for="allow1"><?php echo _AT('enable'); ?></label>

			<input type="radio" name="ec_allow_instructors" value="0" id="allow0" <?php if (!$_config['ec_allow_instructors']){ echo 'checked="checked"'; } ?>/><label for="allow0"><?php echo _AT('disable'); ?></label>
		</div> 
		<div class="row">
			<?php echo _AT('ec_email_admin'); ?><br/>
			<input type="radio" name="ec_email_admin" value="1" id="email1" <?php if ($_config['ec_email_admin']){ echo 'checked="checked"'; } ?>/><label for="email1"><?php echo _AT('enable'); ?></label>

			<input type="radio" name="ec_email_admin" value="0" id="email0" <?php if (!$_config['ec_email_admin']){ echo 'checked="checked"'; } ?>/><label for="email0"><?php echo _AT('disable'); ?></label>
		</div>
		<div class="row">
			<?php echo _AT('ec_store_log'); ?><br/>
			<input type="radio" name="ec_store_log" value="1" id="ipn1" <?php if ($_config['ec_store_log']){ echo 'checked="checked"'; } ?>/><label for="ipn1"><?php echo _AT('enable'); ?></label>

			<input type="radio" name="ec_store_log" value="0" id="ipn0" <?php if (!$_config['ec_store_log']){ echo 'checked="checked"'; } ?>/><label for="ipn0"><?php echo _AT('disable'); ?></label>
		</div> 
		<div class="row">
			<label for="ec_log_file"><?php echo _AT('ec_log_file'); ?></label><br/>
			<input type="text" name="ec_log_file" value="<?php echo htmlspecialchars($_config['ec_log_file']); ?>" id="ec_log_file" size="60"/>
		</div>
		<div class="row buttons">
			<input type="submit" name="submit" value="<?php echo _AT('save'); ?>"  class="button" accesskey="s" />
			<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>"  class="button" />
		</div>
	</div>
</form>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>