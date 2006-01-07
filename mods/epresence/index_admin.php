<?php
/*
This is the ATutor admin ePresence module page. 
*/
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');


if (isset($_POST['submit'])) {
	$_POST['uri'] = trim($_POST['uri']);

	if (!$_POST['uri']){
		$msg->addError('EPRESENCEURL_ADD_EMPTY');
	}
			
	if (!$msg->containsErrors()) {
		$_POST['uri'] = $addslashes($_POST['uri']);
		$sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES ('epresence', '$_POST[uri]')";
		mysql_query($sql, $db);
		$msg->addFeedback('EPRESENCEURL_ADD_SAVED');

		header('Location: '.$_SERVER['PHP_SELF']);
		exit;
	}
}

require (AT_INCLUDE_PATH.'header.inc.php');

?>

<?php if ($_config['epresence']): ?>

<?php else: ?>
	<div class="input-form">
		<div class="row">
			<p><?php echo _AT('epresence_missing_url');  ?></p>
		</div>
	</div>
<?php endif; ?>

<form action="<?php  $_SERVER['PHP_SELF']; ?>" method="post">
	<div class="input-form">
		<div class="row">
			<p><label for="uri"><?php echo _AT('epresence_location'); ?></label></p>
	
			<input type="text" name="uri" value="<?php echo $_config['epresence']; ?>" id="uri" size="80" style="min-width: 95%;" />
		</div>

		<div class="row buttons">
			<input type="submit" name="submit" value="<?php echo _AT('save'); ?>"  />
		</div>
	</div>
</form>
<a href="<?php echo $_SERVER['PHP_SELF']; ?>" onclick="window.open('<?php echo $_config['epresence']; ?>','epresencewin','width=800,height=720,scrollbars=yes, resizable=yes'); return false"><?php echo  _AT('epresence_own_window'); ?></a> </li>

<iframe name="epresence" id="epresence" title="ePresence" scrolling="yes" src="<?php echo $_config['epresence']; ?>" height="800" width="90%" align="center" style="border:thin white solid; align:center;"></iframe>

<?php  require (AT_INCLUDE_PATH.'footer.inc.php'); ?>