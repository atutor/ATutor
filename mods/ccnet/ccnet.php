<?php
/*
This is the ATutor CCNet admin module page. It allows an admin user
to set or edit  the URL for the CCNet installation for ATutor, and
it includes the launcher, which opens CCNet in a new window

*/
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');


if (isset($_POST['submit'])) {
	$_POST['uri'] = trim($_POST['uri']);

	if (!$_POST['uri']){
		$msg->addError('CCNETURL_ADD_EMPTY');
	}
			
	if (!$msg->containsErrors()) {
		$_POST['uri'] = $addslashes($_POST['uri']);
		$sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES ('ccnet', '$_POST[uri]')";
		mysql_query($sql, $db);
		$msg->addFeedback('CCNETURL_ADD_SAVED');

		header('Location: '.$_SERVER['PHP_SELF']);
		exit;
	}
}

require (AT_INCLUDE_PATH.'header.inc.php');

?>

<?php if ($_config['ccnet']): ?>
	<div class="input-form">
		<div class="row">
			<p><?php echo _AT('ccnet_text');  ?></p>
		</div>
		<div class="row buttons">
			<form action="" method="get">
				<input type="submit" value="<?php echo _AT('ccnet_open'); ?>" onclick="window.open('<?php echo $_config['ccnet']; ?>','mywindow','width=800,height=600,scrollbars=yes, resizable=yes', 'false'); return false;" />
			</form>
		</div>
	</div>
<?php else: ?>
	<div class="input-form">
		<div class="row">
			<p><?php echo _AT('ccnet_missing_url');  ?></p>
		</div>
	</div>
<?php endif; ?>

<form action="<?php  $_SERVER['PHP_SELF']; ?>" method="post">
	<div class="input-form">
		<div class="row">
			<p><label for="uri"><?php echo _AT('ccnet_location'); ?></label></p>
	
			<input type="text" name="uri" value="<?php echo $_config['ccnet']; ?>" id="uri" size="80" style="min-width: 95%;" />
		</div>

		<div class="row buttons">
			<input type="submit" name="submit" value="<?php echo _AT('save'); ?>"  />
		</div>
	</div>
</form>

<?php  require (AT_INCLUDE_PATH.'footer.inc.php'); ?>