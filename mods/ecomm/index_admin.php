<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_ECOMM);


if (isset($_POST['submit'])) {
	$_POST['ec_uri'] = trim($_POST['ec_uri']); 

	if (!$_POST['ec_uri']){
		$msg->addError('EC_URL_EMPTY');
	}
	if (!$_POST['ec_vendor_id']){
		$msg->addError('EC_ID_EMPTY');
	}
	if (!$_POST['ec_password']){
		$msg->addError('EC_PASSWORD_EMPTY');
	}		
	if (!$msg->containsErrors()) {
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

		$_POST['ec_currency_symbol'] = $addslashes($_POST['ec_currency_symbol']);
		$sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES ('ec_currency_symbol', '$_POST[ec_currency_symbol]')";
		mysql_query($sql, $db);

		$msg->addFeedback('EC_ADD_SAVED');

		header('Location: '.$_SERVER['PHP_SELF']);
		exit;
	}
}

require (AT_INCLUDE_PATH.'header.inc.php'); ?>


<?php if ($_config['ec_uri']): ?>
	<div class="input-form">
		<div class="row">
			<p><?php echo _AT('ec_location_text'); ?></p>
		</div>
	</div>
<?php else: ?>
	<div class="input-form">
		<div class="row">
			<p><?php echo _AT('ec_location_text');  ?></p>
		</div>
	</div>
<?php endif; ?>

<form action="<?php  $_SERVER['PHP_SELF']; ?>" method="post">
	<div class="input-form">
		<div class="row">
			<p><label for="uri"><?php echo _AT('ec_location'); ?></label></p>
	
			<input type="text" name="ec_uri" value="<?php echo $_config['ec_uri']; ?>" id="ec_uri" size="80"  />
		</div>
		<div class="row">
			<p><label for="ec_vendor_id"><?php echo _AT('ec_vendor_id'); ?></label></p>
	
			<input type="text" name="ec_vendor_id" value="<?php echo $_config['ec_vendor_id']; ?>" id="ec_vendor_id" size="40"/>
		</div>
		<div class="row">
			<p><label for="ec_password"><?php echo _AT('ec_password'); ?></label></p>
	
			<input type="password" name="ec_password" value="<?php echo $_config['ec_password']; ?>" id="ec_password" size="20" />
		</div>
		<div class="row">
			<p><label for="ec_currency"><?php echo _AT('ec_currency'); ?></label></p>
	
			<input type="text" name="ec_currency" value="<?php echo $_config['ec_currency']; ?>" id="ec_currency" size="20"  />
		</div>
		<div class="row">
			<p><label for="ec_currency_symbol"><?php echo _AT('ec_currency_symbol'); ?></label></p>
	
			<input type="text" name="ec_currency_symbol" value="<?php echo $_config['ec_currency_symbol']; ?>" id="ec_currency_symbol" size="20"" />
		</div>

		<div class="row buttons">
			<input type="submit" name="submit" value="<?php echo _AT('save'); ?>"  class="button"  />
		</div>
	</div>
</form>


<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>