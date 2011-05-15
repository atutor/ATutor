<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<div class="input-form">
	<div class="row">
		<label for="real_name"><?php echo _AT('real_name'); ?></label><br />
		<input type="text" name="real_name" id="real_name" size="30" value="<?php echo htmlspecialchars($_POST['real_name']); ?>" />
	</div>

	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="email"><?php echo _AT('email'); ?></label><br />
		<input type="text" name="email" id="email" size="30" value="<?php echo htmlspecialchars($_POST['email']); ?>" />
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
</div>
</form>
