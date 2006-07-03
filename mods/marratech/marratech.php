<?php
/*
This is the ATutor Marratech module page. It allows an admin user
to set or edit  the URL for the Marratech installation for ATutor, and define an optional guest password.

*/
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_MARRATECH);

if (isset($_POST['submit'])) {
	$_POST['uri'] = trim($_POST['uri']);

	if (!$_POST['uri']){
		$msg->addError('MARRATECHURL_ADD_EMPTY');
	}
			
	if (!$msg->containsErrors()) {
		$_POST['uri'] = $addslashes($_POST['uri']);
		$sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES ('marratech', '$_POST[uri]')";
		mysql_query($sql, $db);
		$msg->addFeedback('MARRATECHURL_ADD_SAVED');

		header('Location: '.$_SERVER['PHP_SELF']);
		exit;
	}
}

require (AT_INCLUDE_PATH.'header.inc.php');

?>

<?php if ($_config['marratech']): ?>
	<div class="input-form">
		<div class="row">
			<p><?php echo _AT('marratech_text'); ?></p>
		</div>
		<div class="row buttons">
			<form  action="" method="get">
				<input type="submit" value="<?php echo _AT('marratech_open'); ?>" onclick="window.open('<?php echo $_config['marratech']; ?>/admin/','mywindow','width=800,height=600,scrollbars=yes, resizable=yes', 'false');false;" />
			</form>
		</div>
	</div>
<?php else: ?>
	<div class="input-form">
		<div class="row">
			<p><?php echo _AT('marratech_missing_url');  ?></p>
		</div>
	</div>
<?php endif; ?>

<form action="<?php  $_SERVER['PHP_SELF']; ?>" method="post">
	<div class="input-form">
		<div class="row">
			<p><label for="uri"><?php echo _AT('marratech_location'); ?></label></p>
	
			<input type="text" name="uri" value="<?php echo $_config['marratech']; ?>" id="uri" size="80" style="min-width: 95%;" />
		</div>
		<div class="row buttons">
			<input type="submit" name="submit" value="<?php echo _AT('save'); ?>"  />
		</div>
	</div>
</form>
<div>
<a href="<?php echo $_SERVER['PHP_SELF']; ?>" onclick="window.open('<?php echo $_config['marratech']; ?>','marratechwin','width=800,height=720,scrollbars=yes, resizable=yes'); return false"><?php echo  _AT('marratech_own_window'); ?></a> </li>

<iframe name="marratech" id="marratech" title="Marratech" frameborder="1" scrolling="auto" src="<?php echo $_config['marratech']; ?>/index.jsp" height="500" width="90%" align="center" style="border:thin white solid; align:center;" allowautotransparency="true"></iframe>

</div>
<?php  require (AT_INCLUDE_PATH.'footer.inc.php'); ?>