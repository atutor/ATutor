<form action="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'settings.php');?>" method="POST">
<div class="input-form">
	<h4><?php echo _AT('account_settings'); ?></h4>
	<div class="row"><?php echo _AT('account_control_blurb'); ?> </div>
	<div class="row">
		<input type="hidden" name="n" value="account_settings" />
		<input class="button" type="submit" name="submit" value="<?php echo _AT('save'); ?>"/>
	</div>
</div>
</form>