<h3><?php echo _AT('login'); ?></h3>
	<form action="<?php echo $_SEVER['PHP_SELF']; ?>" method="post" name="form">
	<input type="hidden" name="form_login_action" value="true" />
	<input type="hidden" name="form_course_id" value="<?php $tmpl_course_id; ?>" />

	<table cellspacing="5" cellpadding="0" border="0" align="center">
	<tr>
		<td class="row3" colspan="4" align="center"><h4><?php echo _AT('login'); ?> <?php echo $tmpl_title; ?></h4></td>
	</tr>
	<tr>
		<td class="row1" colspan="2" align="right"><label for="login"><strong><?php echo _AT('login'); ?>:</strong></label></td>
		<td class="row1" colspan="2" align="left"><input type="text" class="formfield" name="form_login" id="login" /></td>
	</tr>
	<tr>
		<td class="row1" colspan="2" align="right" valign="top"><label for="pass"><strong><?php echo _AT('password'); ?>:</strong></label></td>
		<td class="row1" colspan="2" align="left" valign="top"><input type="password" class="formfield" name="form_password" id="pass" /></td>
	</tr>
	<tr>
		<td class="row1" colspan="4" align="center" valign="top"><input type="checkbox" name="auto" value="1" id="auto" /><label for="auto"><?php echo _AT('auto_login2'); ?></label></td>
	</tr>
	</table>

	<p align="center"><br /><input type="submit" name="submit" class="button" value="<?php echo _AT('login'); ?>" /> - <input type="submit" name="cancel" class="button" value="<?php echo _AT('cancel'); ?>" /></p>
		
	<br /><p align="center">&middot; <a href="password_reminder.php"><?php echo _AT('forgot'); ?></a><br />
		&middot; <?php echo _AT('no_account'); ?> <a href="registration.php"><?php echo _AT('free_account'); ?></a></p>

	</form>
	<br /><br />
