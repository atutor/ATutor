<?php require(AT_INCLUDE_PATH.'header.inc.php'); 


?>

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
<div class="input-form">
	<div class="column" style="margin-top:0;">
		<form action="<?php echo AT_print($_SERVER['PHP_SELF'], 'url.self'); ?>" method="post" name="form">
		<input type="hidden" name="form_login_action" value="true" />
		<input type="hidden" name="form_course_id" value="<?php echo $this->form_course_id; ?>" />
		<input type="hidden" name="form_password_hidden" value="" />
		<input type="hidden" name="p" value="<?php if(isset($_GET['p'])){echo urlencode($_GET['p']);}?>" />

			<fieldset class="group_form"><legend class="group_form"><?php echo _AT('returning_user') ;?></legend>
			<p><?php echo _AT('login_text') ;?></p>
				<?php if ($_GET['course']): ?>
					<div class="row">
						<h3><?php echo _AT('login'). ' ' . $this->title; ?></h3>
					</div>
				<?php endif;?>

				<label for="login"><?php echo _AT('login_name_or_email'); ?></label><br />
				<input type="text" name="form_login" size="50" style="max-width: 80%; width: 80%;" id="login" /><br />

				<label for="pass"><?php echo _AT('password'); ?></label><br />
				<input type="password" class="formfield" name="form_password" style="max-width: 80%; width: 80%;" id="pass" />
				<br /><br />
				<input type="submit" name="submit" value="<?php echo _AT('login'); ?>" class="button" onclick="return encrypt_password();" /> 
			</fieldset>			
		</form>
	</div>

<?php
if($_config['allow_registration'] ==1){
?>
	<div class="column" style="margin-top:0;">
		<form action="registration.php" method="post">

			<fieldset class="group_form"><legend class="group_form"><?php echo _AT('new_user') ;?></legend>
			<p><?php echo _AT('registration_text'); ?></p>

			<?php if (defined('AT_EMAIL_CONFIRMATION') && AT_EMAIL_CONFIRMATION): ?>
				<p><?php echo _AT('confirm_account_text'); ?></p>
			<?php endif; ?>
			<div style="width: 20%;margin-left:auto; margin-right:auto;margin-bottom:.6em;padding:.5em;">
			<br /><br />
			<input type="submit" name="register" value="<?php echo _AT('register'); ?>" class="button" />
			</div>
			</fieldset>
		</form>
	</div>

<?php } ?>

<br style="clear:both;" />
</div>
<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>