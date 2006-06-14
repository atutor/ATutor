<?php
define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_ADMIN);

//$key = $_config['gsearch'];

if (isset($_POST['submit'])) {
	$_POST['key'] = trim($_POST['key']);
}

if (!isset($_config['cron_key'])) {
	$_config['cron_key'] = strtoupper(substr(str_replace(array('l','o','0','i'), array(), md5(time())), 0, 6));
	$sql = "INSERT INTO ".TABLE_PREFIX."config VALUES ('cron_key', '{$_config['cron_key']}')";
	mysql_query($sql, $db);
}

require(AT_INCLUDE_PATH.'header.inc.php');
?>

<div class="input-form">
	<div class="row">
		<p><?php echo _AT('cron_secret_key_usage'); ?></p>
	</div>
	<div class="row">
		<label for="key"><?php echo _AT('cron_secret_key'); ?></label><br />
		<input type="text" name="secret_key" size="7" value="<?php echo $_config['cron_key']; ?>" id="key" />
	</div>
</div>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>