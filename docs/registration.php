<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/

	$section = 'users';
	$page	 = 'register';
	$_public	= true;
define('AT_INCLUDE_PATH', 'include/');
	require (AT_INCLUDE_PATH.'vitals.inc.php');
	if (isset($_POST['cancel'])) {
		Header('Location: ./about.php');
		exit;
	}

	if (isset($_POST['submit'])) {
		/* email check */
		if ($_POST['email'] == '') {
			$errors[] = AT_ERROR_EMAIL_MISSING;
		} else if (!eregi("^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,3}$", $_POST['email'])) {
			$errors[] = AT_ERROR_EMAIL_INVALID;
		}
		$result = mysql_query("SELECT * FROM ".TABLE_PREFIX."members WHERE email LIKE '$_POST[email]'",$db);
		if (mysql_num_rows($result) != 0) {
			$valid = 'no';
			$errors[] = AT_ERROR_EMAIL_EXISTS;
		}

		/* login name check */
		if ($_POST['login'] == ''){
			$errors[] = AT_ERROR_LOGIN_NAME_MISSING;
		} else {
			/* check for special characters */
			if (!(eregi("^[a-zA-Z0-9_]([a-zA-Z0-9_])*$", $_POST['login']))){
				$errors[] = AT_ERROR_LOGIN_CHARS;
			} else {
				$result = mysql_query("SELECT * FROM ".TABLE_PREFIX."members WHERE login='$_POST[login]'",$db);
				if (mysql_num_rows($result) != 0) {
					$valid = 'no';
					$errors[] = AT_ERROR_LOGIN_EXISTS;
				} else if ($_POST['login'] == ADMIN_USERNAME) {
					$valid = 'no';			
					$errors[] = AT_ERROR_LOGIN_EXISTS;
				}
			}
		}

		/* password check:	*/
		if ($_POST['password'] == '') { 
			$errors[] = AT_ERROR_PASSWORD_MISSING;
		} else {
			// check for valid passwords
			if ($_POST['password'] != $_POST['password2']){
				$valid= 'no';
				$errors[] = AT_ERROR_PASSWORD_MISMATCH;
			}
		}
		
		$_POST['login'] = strtolower($_POST['login']);

		if (!$errors) {
			if (($_POST['website']) && (!ereg("://",$_POST['website']))) { 
				$_POST['website'] = "http://".$_POST['website']; 
			}
			
			if ($_POST['website'] == 'http://') { 
				$_POST['website'] = ''; 
			}
			$_POST['postal'] = strtoupper(trim($_POST['postal']));
			//figure out which defualt theme to apply, accessibility or ATutor default
			if($_POST['pref'] == 'access'){
				$sql = "SELECT * FROM ".TABLE_PREFIX."theme_settings where theme_id = '1'";
			}else{
				$sql = "SELECT * FROM ".TABLE_PREFIX."theme_settings where theme_id = '4'";
			}
			$result = mysql_query($sql, $db); 	
			while($row = mysql_fetch_array($result)){
				$start_prefs = $row['preferences'];
			}

			/* insert into the db. (the last 0 for status) */
			$sql = "INSERT INTO ".TABLE_PREFIX."members VALUES (0,'$_POST[login]','$_POST[password]','$_POST[email]','$_POST[website]','$_POST[first_name]','$_POST[last_name]', '$_POST[age]', '$_POST[gender]', '$_POST[address]','$_POST[postal]','$_POST[city]','$_POST[province]','$_POST[country]', '$_POST[phone]',0,'$start_prefs', NOW(),'$_SESSION[lang]')";
			$result = mysql_query($sql, $db);
			$m_id	= mysql_insert_id();
			if (!$result) {
				require(AT_INCLUDE_PATH.'basic_html/header.php');
				$error[] = AT_ERROR_DB_NOT_UPDATED;
				print_errors($errors);
				exit;
			}

			if ($_POST['pref'] == 'access') {
				$_SESSION['member_id'] = $m_id;
				save_prefs();
			}

			$feedback[]=AT_FEEDBACK_REG_THANKS;
			require(AT_INCLUDE_PATH.'basic_html/header.php');
			print_feedback($feedback);
			require(AT_INCLUDE_PATH.'basic_html/footer.php');
			exit;
		}
}

unset($_SESSION['member_id']);
unset($_SESSION['valid_user']);
unset($_SESSION['login']);
unset($_SESSION['is_admin']);
unset($_SESSION['course_id']);
unset($_SESSION['is_guest']);

$onload = 'onload="document.form.login.focus();"';
require(AT_INCLUDE_PATH.'basic_html/header.php');

?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">

<?php

print_errors($errors);

?>
<h3><?php echo _AT('registration');  ?></h3><br />
<table cellspacing="1" cellpadding="0" border="0" align="center" summary="">
<tr>
	<td class="row3" colspan="2"><h4><?php echo _AT('account_information'); ?> (<?php echo _AT('required'); ?>)</h4></td>
</tr>
<tr>
	<td class="row1" align="right" valign="top"><label for="login"><b><?php echo _AT('login'); ?>:</b></label></td>
	<td class="row1" align="left"><input id="login" class="formfield" name="login" type="text" maxlength="20" size="15" value="<?php echo stripslashes(htmlspecialchars($_POST['login'])); ?>" /><br />
	<small>&middot; <?php echo _AT('contain_only'); ?><br />
	&middot; <?php echo _AT('20_max_chars'); ?></small></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right" valign="top"><label for="password"><b><?php echo _AT('password'); ?>:</b></label></td>
	<td class="row1" align="left"><input id="password" class="formfield" name="password" type="password" size="15" maxlength="15" value="<?php echo stripslashes(htmlspecialchars($_POST['password'])); ?>" /><br />
	<small>&middot; <?php echo _AT('combination'); ?><br />
	&middot; <?php echo _AT('15_max_chars'); ?></small></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><label for="password2"><b><?php echo _AT('password_again'); ?>:</b></label></td>
	<td class="row1" align="left"><input id="password2" class="formfield" name="password2" type="password" size="15" maxlength="15" value="<?php echo stripslashes(htmlspecialchars($_POST['password2'])); ?>" /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right" valign="top"><label for="email"><b><?php echo _AT('email_address'); ?>:</b></label></td>
	<td class="row1" align="left"><input id="email" class="formfield" name="email" type="text" size="30" maxlength="60" value="<?php echo stripslashes(htmlspecialchars($_POST['email'])); ?>" /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right" valign="top"><label for="language"><b><?php echo _AT('language'); ?>:</b></label></td>
	<td class="row1" align="left"><select name="lang" id="language">
							<?php
							foreach ($available_languages as $key => $val) {
								echo '<option value="'.$key.'"';
								if ($key == $_SESSION['lang']) {
									echo ' selected="selected"';
								}
								echo '>'.$val[3].'</option>';
							}
						
							?>
								</select><br /><br /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
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
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><label for="first_name"><b><?php echo _AT('first_name'); ?>:</b></label></td>
	<td class="row1" align="left"><input id="first_name" class="formfield" name="first_name" type="text" value="<?php echo stripslashes(htmlspecialchars($_POST['first_name'])); ?>" /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><label for="last_name"><b><?php echo _AT('last_name'); ?>:</b></label></td>
	<td class="row1" align="left"><input id="last_name" class="formfield" name="last_name" type="text" value="<?php echo stripslashes(htmlspecialchars($_POST['last_name'])); ?>" /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><label for="age"><b><?php echo _AT('age'); ?>:</b></label></td>
	<td class="row1" align="left"><input id="age" class="formfield" name="age" type="text" size="2" maxlength="2" value="<?php echo stripslashes(htmlspecialchars($_POST['age'])); ?>" /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><b><?php echo _AT('gender'); ?>:</b></td>
	<td class="row1" align="left"><input type="radio" name="gender" id="m" value="m" <?php if ($_POST['gender'] == 'm') { echo 'checked="checked"'; } ?> /><label for="m"><?php echo _AT('male'); ?></label> <input type="radio" value="f" name="gender" id="f" <?php if ($_POST['gender'] == 'f') { echo 'checked="checked"'; } ?> /><label for="f"><?php echo _AT('female'); ?></label>  <input type="radio" value="ns" name="gender" id="ns" <?php if (($_POST['gender'] == 'ns') || ($_POST['gender'] == '')) { echo 'checked="checked"'; } ?> /><label for="ns"><?php echo _AT('not_specified'); ?></label></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><label for="address"><b><?php echo _AT('street_address'); ?>:</b></label></td>
	<td class="row1" align="left"><input id="address" class="formfield" name="address" size="40" type="text" value="<?php echo stripslashes(htmlspecialchars($_POST['address'])); ?>" /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><label for="postal"><b><?php echo _AT('postal_code'); ?>:</b></label></td>
	<td class="row1" align="left"><input id="postal" class="formfield" name="postal" size="7" type="text" value="<?php echo stripslashes(htmlspecialchars($_POST['postal'])); ?>" /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><label for="city"><b><?php echo _AT('city'); ?>:</b></label></td>
	<td class="row1" align="left"><input id="city" class="formfield" name="city" type="text" value="<?php echo stripslashes(htmlspecialchars($_POST['city'])); ?>" /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><label for="province"><b><?php echo _AT('province'); ?>:</b></label></td>
	<td class="row1" align="left"><input id="province" class="formfield" name="province" type="text" value="<?php echo stripslashes(htmlspecialchars($_POST['province'])); ?>" /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><label for="country"><b><?php echo _AT('country'); ?>:</b></label></td>
	<td class="row1" align="left"><input id="country" class="formfield" name="country" type="text" value="<?php echo stripslashes(htmlspecialchars($_POST['country'])); ?>" /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right" valign="top"><label for="phone"><b><?php echo _AT('phone'); ?>:</b></label></td>
	<td class="row1" align="left"><input class="formfield" size="11" name="phone" type="text" value="<?php echo stripslashes(htmlspecialchars($_POST['phone'])); ?>" id="phone" /> <small>123-456-7890</small></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right" valign="top"><label for="website"><b><?php echo _AT('web_site'); ?>:</b></label></td>
	<td class="row1" align="left"><input id="website" class="formfield" name="website" size="40" type="text" value="<?php if ($_POST['website'] == '') { echo 'http://'; } else { echo stripslashes(htmlspecialchars($_POST['website'])); } ?>" /><br /><br /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" colspan="2" align="center"><input type="submit" class="button" value=" <?php echo _AT('submit'); ?>" name="submit" /> - <input type="submit" name="cancel" class="button" value=" <?php echo _AT('cancel'); ?> " /></td>
</tr>
</table>
</form><br />

<?php
	require(AT_INCLUDE_PATH.'basic_html/footer.php');
?>