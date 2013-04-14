<?php global $_config; ?>
<div class="input-form">
	<div class="row">
		<p><?php echo _AT('cron_url_usage'); ?></p>
	</div>
	<div class="row">
		<?php echo _AT('cron_url'); ?><br />
		<code><?php echo AT_BASE_HREF; ?>admin/cron.php?k=<?php echo $_config['cron_key']; ?></code>
	</div>
</div>