<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_ADMIN);

if (isset($_POST['submit'])) {
	$_POST['server']   = trim($_POST['server']);
	$_POST['username'] = trim($_POST['username']);
	$_POST['secret']   = trim($_POST['secret']);

	if (!$_POST['server']){
		$msg->addError('TLE_SERVER_EMPTY');
	}

	if (!$msg->containsErrors()) {
		$_POST['server']   = $addslashes($_POST['server']);
		$_POST['username'] = $addslashes($_POST['username']);
		$_POST['secret']   = $addslashes($_POST['secret']);

		$sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES('tle_server','$_POST[server]'), ('tle_username', '$_POST[username]'), ('tle_secret', '$_POST[secret]')";
		$result = mysql_query($sql, $db);

		$msg->addFeedback('TLE_UPDATE_SUCCESSFULL');

		header('Location: '.$_SERVER['PHP_SELF']);
		exit;
	}
}

require (AT_INCLUDE_PATH.'header.inc.php');
?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<div class="input-form">

	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="server"><?php echo _AT('tle_server'); ?></label><br />
		<input type="text" name="server" value="<?php echo $_config['tle_server']; ?>" id="server" size="50" />
	</div>

	<div class="row">
		<label for="username"><?php echo _AT('username'); ?></label><br />
		<input type="text" name="username" value="<?php echo $_config['tle_username']; ?>" id="username" size="20" />
	</div>

	<div class="row">
		<label for="secret"><?php echo _AT('tle_shared_secret'); ?></label><br />
		<input type="text" name="secret" value="<?php echo $_config['tle_secret']; ?>" id="secret" size="20" />
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" />
	</div>
</div>
</form>


<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>