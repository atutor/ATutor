<script language="JavaScript" src="sha-1factory.js" type="text/javascript"></script>
<script type="text/javascript">
/* 
 * Encrypt login password with sha1
 */
function encrypt_password() {
	document.form.form_password_hidden.value = hex_sha1(hex_sha1(document.form.form_password.value) + "<?php echo $_SESSION['token']; ?>");
	document.form.form_password.value = "";
	return true;
}
</script>
<div class="jb_login">
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
	<input type="hidden" name="form_password_hidden" value="" />
		<fieldset><legend><?php echo _AT('jb_login') ;?></legend>
			<p><?php echo _AT('jb_login_text') ;?></p>
			<label for="login"><?php echo _AT('jb_login_name'); ?></label><br />
			<input type="text" name="form_login" size="50" style="max-width: 80%; width: 80%;" id="login" /><br />

			<label for="pass"><?php echo _AT('password'); ?></label><br />
			<input type="password" class="formfield" name="form_password" style="max-width: 80%; width: 80%;" id="pass" />
			<br /><br />
			<input type="submit" name="submit" value="<?php echo _AT('login'); ?>" class="button" onclick="return encrypt_password();" /> 
		</fieldset>	
	</form>
</div>