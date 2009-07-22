<?php 
global $onload;
$onload = 'document.form.form_password.focus();';
require(AT_INCLUDE_PATH.'header.inc.php'); 
?>

<script language="JavaScript" type="text/javascript" src="sha-1factory.js"></script>

<script type="text/javascript">
function encrypt_password()
{
	document.form.form_password_hidden.value = hex_sha1(document.form.form_password.value);
	document.form.form_password.value = "";
}
</script>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
	<input type="hidden" name="form_password_hidden" value="" />

	<div class="input-form" style="max-width: 400px;">

		<div class="row">
			<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="form_password"><?php echo _AT('password'); ?></label><br />
			<input id="form_password" name="form_password" type="password" size="15" maxlength="15" value="" /><br />
		</div>

		<div class="row">
			<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="email"><?php echo _AT('email_address'); ?></label><br />
			<input id="email" name="email" type="text" size="50" maxlength="50" value="<?php echo stripslashes(htmlspecialchars($_POST['email'])); ?>" />
		</div>

		<div class="row buttons">
			<input type="submit" name="submit" value="<?php echo _AT('submit'); ?>" onClick="encrypt_password()" />
			<input type="submit" name="cancel" value=" <?php echo _AT('cancel'); ?> " />
		</div>
	</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>