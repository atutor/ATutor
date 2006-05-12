<?php require(AT_INCLUDE_PATH.'header.inc.php'); ?>

<script language="JavaScript" src="sha-1factory.js"></script>
<script language="JavaScript" type="text/javascript">
//<!--
  function crypt_sha1() {
  	document.form.password_hidden.value = hex_sha1(document.form.password.value + "<?php echo $_SESSION['token']; ?>");
  	document.form.password.value = "";
  	return true;
  }
 //-->
</script>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
	<input type="hidden" name="password_hidden" value="" />

	<div class="input-form" style="max-width: 400px;">

		<div class="row">
			<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="password"><?php echo _AT('password'); ?></label><br />
			<input id="password" name="password" type="password" size="15" maxlength="15" value="" /><br />
		</div>

		<div class="row">
			<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="email"><?php echo _AT('email_address'); ?></label><br />
			<input id="email" name="email" type="text" size="50" maxlength="50" value="<?php echo stripslashes(htmlspecialchars($_POST['email'])); ?>" />
			</label>
		</div>

		<div class="row buttons">
			<input type="submit" name="submit" value="<?php echo _AT('submit'); ?>" /> <input type="submit" name="cancel" value=" <?php echo _AT('cancel'); ?> " />
		</div>
	</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>