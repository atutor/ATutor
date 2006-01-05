<?php
/*
This is the ATutor phpMyAdmin module page. It allows an admin user
to set or edit  the URL for the phpMyAdmin installation for ATutor, and
it includes the launcher, which opens phpMyAdmin in a new window

*/
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');


if (isset($_POST['submit'])) {
	$_POST['uri'] = trim($_POST['uri']);

	if (!$_POST['uri']){
		$msg->addError('PHPMYADMINURL_ADD_EMPTY');
	}
			
	if (!$msg->containsErrors()) {
		$_POST['uri'] = $addslashes($_POST['uri']);
		$sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES ('phpmyadmin', '$_POST[uri]')";
		mysql_query($sql, $db);
		$msg->addFeedback('PHPMYADMINURL_ADD_SAVED');

		header('Location: '.$_SERVER['PHP_SELF']);
		exit;
	}
}

require (AT_INCLUDE_PATH.'header.inc.php');

?>

<?php if ($_config['phpmyadmin']): ?>
	<div class="input-form">
		<div class="row">
			<p><?php echo _AT('phpmyadmin_text');  ?></p>
		</div>
		<div class="row buttons">
			<form action="" method="get">
				<input type="submit" value="<?php echo _AT('open'); ?>" onclick="window.open('<?php echo $_config['phpmyadmin']; ?>','mywindow','width=800,height=600,scrollbars=yes, resizable=yes', 'false');true;" />
			</form>
		</div>
	</div>
<?php else: ?>
	<div class="input-form">
		<div class="row">
			<p><?php echo _AT('phpmyadmin_missing_url');  ?></p>
		</div>
	</div>
<?php endif; ?>

<form action="<?php  $_SERVER['PHP_SELF']; ?>" method="post">
	<div class="input-form">
		<div class="row">
			<p><?php echo _AT('phpmyadmin_location'); ?></p>
	
			<input type="text" name="uri" value="<?php echo $_config['phpmyadmin']; ?>" size="80" style="min-width: 95%;" />
		</div>

		<div class="row buttons">
			<input type="submit" name="submit" value="<?php echo _AT('edit'); ?>"  />
		</div>
	</div>
</form>

<?php  require (AT_INCLUDE_PATH.'footer.inc.php'); ?>