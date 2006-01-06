<?php
/*
This is the main ATutor ccnet module page. It allows users to access
the UofT CCNet installation through courses that have CCNet enabled
*/
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

require (AT_INCLUDE_PATH.'header.inc.php');
?>

<?php if (!$_config['ccnet']): ?>
	<?php $msg->printInfos('CCNET_URL_ADD_REQUIRED'); ?>
<?php else: ?>
	<div class="input-form">
		<div class="row">
			<p><?php echo _AT('ccnet_text'); ?></p>
		</div>

		<div class="row buttons">
			<form action="" method="get">
				<input type="submit" name="submit" value="<?php echo _AT('ccnet_open'); ?>" onclick="window.open('<?php echo $_config['ccnet']; ?>','mywindow','width=800,height=600,scrollbars=yes, resizable=yes'); return false;" />
			</form>
		</div>
	</div>
<?php endif; ?>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>