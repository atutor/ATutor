<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">

<div class="input-form" style="max-width: 400px">
	<div class="row">
		<p><?php echo _AT('send_confirmation'); ?></p>
	</div>

	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="email"><?php echo _AT('email'); ?></label><br />
		<input type="text" name="email" id="email" size="50" />
		<input type="hidden" name="en_id" id="en_id" value="<?php echo $_REQUEST['en_id']; ?>" size="50" />
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('send'); ?>" /> 
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
</div>
</form>