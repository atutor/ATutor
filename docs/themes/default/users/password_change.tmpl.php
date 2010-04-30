<?php 
global $onload;
$onload = 'document.form.old_password.focus();';

require(AT_INCLUDE_PATH.'header.inc.php'); 
?>

<script language="JavaScript" src="sha-1factory.js" type="text/javascript"></script>

<script type="text/javascript">
function encrypt_password()
{
	document.form.password_error.value = "";

	document.form.form_old_password_hidden.value = hex_sha1(document.form.old_password.value);
	document.form.old_password.value = "";

	// verify new password
	err = verify_password(document.form.password.value, document.form.password2.value);
	
	if (err.length > 0)
	{
		document.form.password_error.value = err;
	}
	else
	{
		document.form.form_password_hidden.value = hex_sha1(document.form.password.value);
		document.form.password.value = "";
		document.form.password2.value = "";
	}
}
</script>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
	<input type="hidden" name="form_change" value="true" />
	<input name="password_error" type="hidden" />
	<input type="hidden" name="form_old_password_hidden" value="" />
	<input type="hidden" name="form_password_hidden" value="" />

	<div class="input-form" style="width:90%;">

		<div class="row">
			<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="old_password"><?php echo _AT('password_old'); ?></label><br />
			<input id="old_password" name="old_password" type="password" size="15" maxlength="15" /><br />
		</div>

		<div class="row">
			<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="password"><?php echo _AT('password'); ?></label><br />
			<input id="password" name="password" type="password" size="15" maxlength="15" /><br />
			<small>&middot; <?php echo _AT('combination'); ?><br />
				   &middot; <?php echo _AT('15_max_chars'); ?></small>
		</div>

		<div class="row">
			<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="password2"><?php echo _AT('password_again'); ?></label><br />
			<input id="password2" name="password2" type="password" size="15" maxlength="15" />
		</div>

		<div class="row buttons">
			<input type="submit" name="submit" value="<?php echo _AT('submit'); ?>" onClick="encrypt_password()" /> 
			<input type="submit" name="cancel" value=" <?php echo _AT('cancel'); ?> " />
		</div>
	</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>