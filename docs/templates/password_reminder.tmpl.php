<?php require(AT_INCLUDE_PATH.'header.inc.php'); ?>

<h3><?php echo _AT('password_reminder');  ?></h3>
<?php
	if ($errors && !$success) {
		print_errors($errors);
	}
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
	<input type="hidden" name="form_password_reminder" value="true" />
	<br />
	<table cellspacing="1" cellpadding="0" border="0" align="center" width="60%" summary="">
	<tr>
		<td class="row1" align="left" colspan="2"><?php echo _AT('password_blurb'); ?><br /></td>
	</tr>
	<tr><td height="1" class="row1" colspan="2"></td></tr>
	<tr>
		<td valign="top" align="right" class="row1"><label for="email"><strong><?php echo _AT('email_address'); ?>: </strong></label></td>
		<td valign="top" align="left" class="row1"><input type="text" class="formfield" name="form_email" id="email" /><br /><br /></td>
	</tr>
	<tr><td height="1" class="row1" colspan="2"></td></tr>
	<tr><td height="1" class="row1" colspan="2"></td></tr>
	<tr>
		<td align="center" colspan="2" class="row1"><input type="submit" name="submit" class="button" value="<?php echo _AT('submit'); ?>" /> - <input type="submit" name="cancel" class="button" value=" <?php echo _AT('cancel'); ?> " /></td>
	</tr>
	</table>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>