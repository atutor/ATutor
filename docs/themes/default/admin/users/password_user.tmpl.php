
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
	<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
	<input type="hidden" name="form_password_hidden" value="" />
	<input type="hidden" name="password_error" value="" />

	<div class="input-form">
		<div class="row">
			<h3><?php echo htmlspecialchars($row['login']); ?></h3>
		</div>

		<div class="row">
			<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="password"><?php echo _AT('password'); ?></label><br />
			<input type="password" name="password" id="password" value="" size="30" />
		</div>

		<div class="row">
			<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="password2"><?php echo _AT('confirm_password'); ?></label><br />
			<input type="password" name="password2" id="password2" value="" size="30" />
		</div>

		<div class="row buttons">
			<input type="submit" name="submit" value="<?php echo _AT('submit'); ?>" onclick="encrypt_password()" />
			<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
		</div>
	</div>
</form>
