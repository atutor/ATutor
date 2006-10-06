<?php
/*
This is the ATutor CMAP module page. It allows an admin user
to set or edit  the URL for the CMAP installation for ATutor.

*/
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');


if (isset($_POST['submit'])) {
	$_POST['uri'] = trim($_POST['uri']);

	if (!$_POST['uri']){
		$msg->addError('CMAPURL_ADD_EMPTY');
	}
			
	if (!$msg->containsErrors()) {
		$_POST['uri'] = $addslashes($_POST['uri']);
		$sql = "REPLACE INTO ".TABLE_PREFIX."config VALUES ('cmap', '$_POST[uri]')";
		mysql_query($sql, $db);
		$msg->addFeedback('CMAPURL_ADD_SAVED');

		header('Location: '.$_SERVER['PHP_SELF']);
		exit;
	}
}

require (AT_INCLUDE_PATH.'header.inc.php');

?>

<?php if ($_config['cmap']): ?>
	<div class="input-form">
		<div class="row">
			<p><?php  echo _AT('cmap_admin_text'); ?></p>
		</div>

	</div>
<?php else: ?>
	<div class="input-form">
		<div class="row">
			<p><?php echo _AT('cmap_missing_url');  ?></p>
		</div>
	</div>
<?php endif; ?>

<form action="<?php  $_SERVER['PHP_SELF']; ?>" method="post">
	<div class="input-form">
		<div class="row">
			<p><label for="uri"><?php echo _AT('cmap_location'); ?></label></p>
	
			<input type="text" name="uri" value="<?php echo $_config['cmap']; ?>" id="uri" size="80" style="min-width: 95%;" />
		</div>

		<div class="row buttons">
			<input type="submit" name="submit" value="<?php echo _AT('save'); ?>"  />
		</div>
	</div>
</form>
<?php if ($_config['cmap']): ?>

<div class="input-form">
| <a href="<?php echo $_SERVER['PHP_SELF']; ?>" onclick="window.open('<?php echo $_config['cmap']; ?>','cmapwin','width=800,height=720,scrollbars=yes, resizable=yes'); return false"><?php echo  _AT('cmap_own_window'); ?></a> 
| <a href="<?php echo $_SERVER['PHP_SELF']; ?>" onclick="window.open('http://cmap.ihmc.us/Support/Help/','cmapwin','width=800,height=720,scrollbars=yes, resizable=yes'); return false"><?php echo  _AT('cmap_help_window'); ?></a> 
| <br /><br />
</div>
<iframe name="cmap" id="cmap" title="ePresence" scrolling="yes" src="<?php echo $_config['cmap']; ?>" height="800" width="90%" align="center" style="border:thin white solid; align:center;"></iframe>
<?php else: ?>
		<div class="row">
			<p><?php echo _AT('cmap_missing_url');  ?></p>
		</div>

<?php endif; ?>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>