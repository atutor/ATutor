<?php
/*
This is the ATutor Elluminate module page. It allows an admin user
to set or edit  the URL for the Elluminate installation for ATutor, and define an optional guest password.

*/
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_ELLUMINATE);

if (isset($_POST['submit'])) {
	$_POST['uri'] = trim($_POST['uri']);

	if (!$_POST['uri']){
		$msg->addError('ELLUMINATEURL_ADD_EMPTY');
	}
			
	if (!$msg->containsErrors()) {
		$_POST['uri'] = $addslashes($_POST['uri']);
		$sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES ('elluminate', '$_POST[uri]')";
		mysql_query($sql, $db);
		$_POST['pw'] = $addslashes($_POST['pw']);
		$sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES ('elluminate_pw', '$_POST[pw]')";
		mysql_query($sql, $db);
		$msg->addFeedback('ELLUMINATEURL_ADD_SAVED');

		header('Location: '.$_SERVER['PHP_SELF']);
		exit;
	}
}

require (AT_INCLUDE_PATH.'header.inc.php');

?>

<?php if ($_config['elluminate']): ?>
	<div class="input-form">
		<div class="row">
			<p><?php echo _AT('elluminate_text'); ?></p>
		</div>
		<div class="row buttons">
			<form action="<?php echo $_config['elluminate']; ?>" method="get">
				<input type="submit" value="<?php echo _AT('elluminate_open'); ?>" onclick="window.open('<?php echo $_config['elluminate']; ?>','mywindow','width=800,height=600,scrollbars=yes, resizable=yes', 'false');true;" />
				<input type="hidden" name="username" value="admin">
				<input type="hidden" name="password" value="<?php echo $_config['elluminate_pw']; ?>">
			</form>
		</div>
	</div>
<?php else: ?>
	<div class="input-form">
		<div class="row">
			<p><?php echo _AT('elluminate_missing_url');  ?></p>
		</div>
	</div>
<?php endif; ?>

<form action="<?php  $_SERVER['PHP_SELF']; ?>" method="post">
	<div class="input-form">
		<div class="row">
			<p><label for="uri"><?php echo _AT('elluminate_location'); ?></label></p>
	
			<input type="text" name="uri" value="<?php echo $_config['elluminate']; ?>" id="uri" size="80" style="min-width: 95%;" />
		</div>
		<div class="row">
			<p><label for="pw"><?php echo _AT('elluminate_pw'); ?></label></p>
	
			<input type="text" name="pw" value="<?php echo $_config['elluminate_pw']; ?>" id="pw" size="20" style="min-width: 55%;" />
		</div>
		<div class="row buttons">
			<input type="submit" name="submit" value="<?php echo _AT('save'); ?>"  />
		</div>
	</div>
</form>

<?php  require (AT_INCLUDE_PATH.'footer.inc.php'); ?>