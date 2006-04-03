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

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
	<div class="input-form">
		<div class="row">
			ATutor operates best with the help of an automated event scheduler, commonly known as a <em>cron job</em>. The cron internval should be between 5 minutes and 30 minutes.
		</div>

		<div class="row">
			Since the cron simply loads an ATutor web page, the cron can run on any machine which has Internet access or access to your ATutor installation.
		</div>

		<div class="row">
			<h3>Unix Setup</h3>
			<ol>
				<li>Enter your hosts cron utility, either using an existing web interface or from the shell with <code>crontab -e</code>.</li>
				<li>Decide whether you want to use <code>wget</code> or <code>lynx</code> to execute the file remotely.</li>
				<li>To run the cron every 5 minutes enter<br />
					<code>*/5 * * * * wget -q -O  <?php echo $_base_href; ?>admin/cron.php?k=<?php echo $_config['cron_key']; ?></code><br />
					Or<br /><code>*/5 * * * * lynx -dump <?php echo $_base_href; ?>admin/cron.php?k=<?php echo $_config['cron_key']; ?> > /dev/null</code></li>
			</ol>
		</div>

		<div class="row">
			- user atutor server
		</div>

		<div class="row buttons">
			<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s" />
		</div>
	</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>