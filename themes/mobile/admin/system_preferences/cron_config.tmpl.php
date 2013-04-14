<?php global $_config; ?>
<div class="input-form">
	<div class="row">
		<p><?php echo _AT('cron_url_usage'); ?></p>
	<p>
		<?php echo _AT('cron_url'); ?><br />
		<code><wbr><?php echo AT_BASE_HREF; ?>admin/cron.php?k=<?php echo $_config['cron_key']; ?></wbr></code>
	</p>
	</div>
	
	
</div>