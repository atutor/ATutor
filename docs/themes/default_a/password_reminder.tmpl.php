<?php require(AT_INCLUDE_PATH.'header.inc.php'); ?>

<?php
global $msg;
	
$msg->printAll();
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
	<input type="hidden" name="form_password_reminder" value="true" />

	<div class="input-form" style="max-width: 400px;">
		<div class="row">
			<?php echo _AT('password_blurb'); ?>
		</div>

		<div class="row">
			<label for="email"><?php echo _AT('email_address'); ?></label><br />
			<input type="text" name="form_email" id="email" />
		</div>

		<div class="row buttons">
			<input type="submit" name="submit" value="<?php echo _AT('submit'); ?>" /> <input type="submit" name="cancel" value=" <?php echo _AT('cancel'); ?> " />
		</div>
	</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>