<?php require(AT_INCLUDE_PATH.'basic_html/header.php'); ?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
<?php print_errors($errors); ?>
<h3><?php echo _AT('registration');  ?></h3><br />
<table cellspacing="1" cellpadding="0" border="0" align="center" summary="">
<tr>
	<td class="row3" colspan="2"><h4><?php echo _AT('account_information'); ?> (<?php echo _AT('required'); ?>)</h4></td>
</tr>
<tr>
	<td class="row1" align="right" valign="top"><label for="login"><strong><?php echo _AT('login'); ?>:</strong></label></td>
	<td class="row1" align="left"><input id="login" class="formfield" name="login" type="text" maxlength="20" size="15" value="<?php echo stripslashes(htmlspecialchars($_POST['login'])); ?>" /><br />
	<small>&middot; <?php echo _AT('contain_only'); ?><br />
	&middot; <?php echo _AT('20_max_chars'); ?></small></td>
</tr>
<tr><td height="1" class="row1" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right" valign="top"><label for="password"><strong><?php echo _AT('password'); ?>:</strong></label></td>
	<td class="row1" align="left"><input id="password" class="formfield" name="password" type="password" size="15" maxlength="15" value="<?php echo stripslashes(htmlspecialchars($_POST['password'])); ?>" /><br />
	<small>&middot; <?php echo _AT('combination'); ?><br />
	&middot; <?php echo _AT('15_max_chars'); ?></small></td>
</tr>
<tr><td height="1" class="row1" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><label for="password2"><strong><?php echo _AT('password_again'); ?>:</strong></label></td>
	<td class="row1" align="left"><input id="password2" class="formfield" name="password2" type="password" size="15" maxlength="15" value="<?php echo stripslashes(htmlspecialchars($_POST['password2'])); ?>" /></td>
</tr>
<tr><td height="1" class="row1" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right" valign="top"><label for="email"><strong><?php echo _AT('email_address'); ?>:</strong></label></td>
	<td class="row1" align="left"><input id="email" class="formfield" name="email" type="text" size="30" maxlength="60" value="<?php echo stripslashes(htmlspecialchars($_POST['email'])); ?>" /></td>
</tr>
<tr><td height="1" class="row1" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right" valign="top"><label for="language"><strong><?php echo _AT('language'); ?>:</strong></label></td>
	<td class="row1" align="left"><select name="lang" id="language">
							<?php foreach ($tmpl_available_languages as $key => $val): ?>
								<?php if ($key == $_SESSION['lang']): ?>
									<option value="<?php echo $key; ?>" selected="selected"><?php echo $val[3]; ?></option>
								<?php else: ?>
									<option value="<?php echo $key; ?>"><?php echo $val[3]; ?></option>
								<?php endif; ?>
							<?php endforeach; ?>
							</select><br /><br /></td>
</tr>
<tr><td height="1" class="row1" colspan="2"></td></tr>
<tr>
	<td class="row3" colspan="2"><h4><?php echo _AT('personal_information').' ('._AT('optional').')'; ?> </h4></td>
</tr>
<tr>
	<td class="row1" align="right" colspan="2"><input type="checkbox" name="pref" value="access" id="access" <?php
		if ($_POST['pref'] == 'access') {
			echo ' checked="checked"';
		}
	?> /><label for="access"><?php echo _AT('enable_accessibility'); ?></label></td>
</tr>
<tr><td height="1" class="row1" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><label for="first_name"><strong><?php echo _AT('first_name'); ?>:</strong></label></td>
	<td class="row1" align="left"><input id="first_name" class="formfield" name="first_name" type="text" value="<?php echo stripslashes(htmlspecialchars($_POST['first_name'])); ?>" /></td>
</tr>
<tr><td height="1" class="row1" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><label for="last_name"><strong><?php echo _AT('last_name'); ?>:</strong></label></td>
	<td class="row1" align="left"><input id="last_name" class="formfield" name="last_name" type="text" value="<?php echo stripslashes(htmlspecialchars($_POST['last_name'])); ?>" /></td>
</tr>
<tr><td height="1" class="row1" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><label for="age"><strong><?php echo _AT('age'); ?>:</strong></label></td>
	<td class="row1" align="left"><input id="age" class="formfield" name="age" type="text" size="2" maxlength="2" value="<?php echo stripslashes(htmlspecialchars($_POST['age'])); ?>" /></td>
</tr>
<tr><td height="1" class="row1" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><strong><?php echo _AT('gender'); ?>:</strong></td>
	<td class="row1" align="left"><input type="radio" name="gender" id="m" value="m" <?php if ($_POST['gender'] == 'm') { echo 'checked="checked"'; } ?> /><label for="m"><?php echo _AT('male'); ?></label> <input type="radio" value="f" name="gender" id="f" <?php if ($_POST['gender'] == 'f') { echo 'checked="checked"'; } ?> /><label for="f"><?php echo _AT('female'); ?></label>  <input type="radio" value="ns" name="gender" id="ns" <?php if (($_POST['gender'] == 'ns') || ($_POST['gender'] == '')) { echo 'checked="checked"'; } ?> /><label for="ns"><?php echo _AT('not_specified'); ?></label></td>
</tr>
<tr><td height="1" class="row1" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><label for="address"><strong><?php echo _AT('street_address'); ?>:</strong></label></td>
	<td class="row1" align="left"><input id="address" class="formfield" name="address" size="40" type="text" value="<?php echo stripslashes(htmlspecialchars($_POST['address'])); ?>" /></td>
</tr>
<tr><td height="1" class="row1" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><label for="postal"><strong><?php echo _AT('postal_code'); ?>:</strong></label></td>
	<td class="row1" align="left"><input id="postal" class="formfield" name="postal" size="7" type="text" value="<?php echo stripslashes(htmlspecialchars($_POST['postal'])); ?>" /></td>
</tr>
<tr><td height="1" class="row1" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><label for="city"><strong><?php echo _AT('city'); ?>:</strong></label></td>
	<td class="row1" align="left"><input id="city" class="formfield" name="city" type="text" value="<?php echo stripslashes(htmlspecialchars($_POST['city'])); ?>" /></td>
</tr>
<tr><td height="1" class="row1" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><label for="province"><strong><?php echo _AT('province'); ?>:</strong></label></td>
	<td class="row1" align="left"><input id="province" class="formfield" name="province" type="text" value="<?php echo stripslashes(htmlspecialchars($_POST['province'])); ?>" /></td>
</tr>
<tr><td height="1" class="row1" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><label for="country"><strong><?php echo _AT('country'); ?>:</strong></label></td>
	<td class="row1" align="left"><input id="country" class="formfield" name="country" type="text" value="<?php echo stripslashes(htmlspecialchars($_POST['country'])); ?>" /></td>
</tr>
<tr><td height="1" class="row1" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right" valign="top"><label for="phone"><strong><?php echo _AT('phone'); ?>:</strong></label></td>
	<td class="row1" align="left"><input class="formfield" size="11" name="phone" type="text" value="<?php echo stripslashes(htmlspecialchars($_POST['phone'])); ?>" id="phone" /> <small>123-456-7890</small></td>
</tr>
<tr><td height="1" class="row1" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right" valign="top"><label for="website"><strong><?php echo _AT('web_site'); ?>:</strong></label></td>
	<td class="row1" align="left"><input id="website" class="formfield" name="website" size="40" type="text" value="<?php if ($_POST['website'] == '') { echo 'http://'; } else { echo stripslashes(htmlspecialchars($_POST['website'])); } ?>" /><br /><br /></td>
</tr>
<tr><td height="1" class="row1" colspan="2"></td></tr>
<tr><td height="1" class="row1" colspan="2"></td></tr>
<tr>
	<td class="row1" colspan="2" align="center"><input type="submit" class="button" value=" <?php echo _AT('submit'); ?>" name="submit" /> - <input type="submit" name="cancel" class="button" value=" <?php echo _AT('cancel'); ?> " /></td>
</tr>
</table>
</form>
<br />
<?php require(AT_INCLUDE_PATH.'basic_html/footer.php'); ?>