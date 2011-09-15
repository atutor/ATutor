<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
	<input type="hidden" name="login" value="<?php echo $this->row['login']; ?>" />
	<input type="hidden" name="form_password_hidden" value="" />
	<input type="hidden" name="password_error" value="" />

	<div class="input-form">
		<div class="row">
			<h3><?php echo htmlspecialchars($this->row['login']); ?></h3>
		</div>

		<div class="row">
			<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="form_password1"><?php echo _AT('password'); ?></label><br />
			<input type="password" title="password" name="password1" id="password1" size="15" />
		</div>

		<div class="row">
			<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="form_password2"><?php echo _AT('confirm_password'); ?></label><br />
			<input type="password" title="confirm password" name="confirm_password" id="confirm_password" size="15" />
		</div>

		<div class="row buttons">
			<input type="submit" name="submit" value="<?php echo _AT('submit'); ?>" accesskey="s" onclick="encrypt_password();" />
			<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
		</div>
	</div>
</form>