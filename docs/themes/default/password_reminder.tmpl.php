<?php 
global $onload;
$onload = 'document.form.form_email.focus();';

require(AT_INCLUDE_PATH.'header.inc.php'); 
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
	<input type="hidden" name="form_password_reminder" value="true" />
	<div class="input-form" style="width: 90%;">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('password_reminder'); ?></legend>
		<div class="row">
			<?php echo _AT('password_blurb'); ?>
		</div>

		<div class="row">
			<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="email"><?php echo _AT('email_address'); ?></label><br />
			<input type="text" name="form_email" id="email" size="50" />
		</div>

		<div class="row buttons">
			<input type="submit" name="submit" value="<?php echo _AT('submit'); ?>" /> <input type="submit" name="cancel" value=" <?php echo _AT('cancel'); ?> " />
		</div>
	</fieldset>
	</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>