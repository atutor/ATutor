<?php require(AT_INCLUDE_PATH.'header.inc.php'); ?>

<script language="JavaScript" src="sha-1factory.js"></script>

<script language="JavaScript" type="text/javascript">
//<!--
  function crypt_sha1() {
  	document.form.form_password_hidden.value = hex_sha1(document.form.form_password.value + "<?php echo $_SESSION['token']; ?>");
  	document.form.form_password.value = "";
  	return true;
  }
 //-->
</script>

<div id="container">
	<div class="column">
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
		<input type="hidden" name="form_login_action" value="true" />
		<input type="hidden" name="form_course_id" value="<?php echo $this->course_id; ?>" />
		<input type="hidden" name="form_password_hidden" value="" />

		<h3 style="border-bottom: 1px solid #e0e0e0; background-color:#fafafa; text-align:center; padding:5px;"><?php echo _AT('login'); ?></h3>
		<div class="insidecol"><p><?php echo _AT('login_text') ;?></p>
			<div class="input-form" style="border:0px; margin-bottom: 10px; width: 90%; text-align: center; line-height:180%;">

				<?php if ($_GET['course']): ?>
					<div class="row">
						<h3><?php echo _AT('login'). ' ' . $this->title; ?></h3>
					</div>
				<?php endif;?>

				<label for="login"><?php echo _AT('login_name_or_email'); ?></label><br />
				<input type="text" name="form_login" size="50" style="max-width: 100%; width: 100%;" id="login" /><br />

				<label for="pass"><?php echo _AT('password'); ?></label><br />
				<input type="password" class="formfield" name="form_password" style="max-width: 100%; width: 100%;" id="pass" />
			</div>
		</div>
		<div style="background-color:#fafafa; text-align:center; padding:5px; border-top: 1px solid #e0e0e0; ">
			<input type="submit" name="submit" value="<?php echo _AT('login'); ?>" class="button" onclick="return crypt_sha1();" />
		</div>
		</form>
	</div>
		
	<div class="column">
		<form action="registration.php" method="get">
		<h3 style="border-bottom: 1px solid #e0e0e0; background-color:#fafafa; text-align:center; padding:5px;"><?php echo _AT('new_user');?></h3>
		<div class="insidecol"><p><?php echo _AT('registration_text'); ?></p>

		<?php if (defined('AT_EMAIL_CONFIRMATION') && AT_EMAIL_CONFIRMATION): ?>
			<p><?php echo _AT('confirm_account_text'); ?></p>
		<?php endif; ?>
		</div>

		<div style="background-color:#fafafa; text-align:center; padding:5px; border-top: 1px solid #e0e0e0;">
			<input type="submit" name="register" value="<?php echo _AT('register'); ?>" class="button" />
		</div>
		</form>
	</div>
</div>
<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>