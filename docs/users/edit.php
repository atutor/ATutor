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
define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');


if (isset($_POST['cancel'])) {
	Header('Location: index.php?f='.AT_FEEDBACK_CANCELLED);
	exit;
}

if ($_POST['submit']){
		$error = '';

		// email check
		if ($_POST['email'] == '') {
			$errors[]=AT_ERROR_EMAIL_MISSING;
		} else {
			if(!eregi("^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,3}$", $_POST['email']))
			{
				$errors[]=AT_ERROR_EMAIL_INVALID;
			}
			$result = mysql_query("SELECT * FROM ".TABLE_PREFIX."members WHERE email='$_POST[email]' AND member_id<>$_SESSION[member_id]",$db);
			if(mysql_num_rows($result) != 0) {
				$errors[]=AT_ERROR_EMAIL_EXISTS;
			}
		}

		// password check
		if ($_POST['password'] == '') { 
			$errors[] = AT_ERROR_PASSWORD_MISSING;
		}
		// check for valid passwords
		if ($_POST['password'] != $_POST['password2']){
			$errors[] = AT_ERROR_PASSWORD_MISMATCH;
		}
		
		$login = strtolower($_POST['login']);
		if (!$errors) {

			if (($_POST['web_site']) && (!ereg('://',$_POST['web_site']))) { $_POST['web_site'] = 'http://'.$_POST['web_site']; }
			if ($_POST['web_site'] == 'http://') { $_POST['web_site'] = ''; }

			// insert into the db.
			$sql = "UPDATE ".TABLE_PREFIX."members SET password='$_POST[password]', email='$_POST[email]', website='$_POST[website]', first_name='$_POST[first_name]', last_name='$_POST[last_name]', age='$_POST[age]', gender='$_POST[gender]', address='$_POST[address]', postal='$_POST[postal]', city='$_POST[city]', province='$_POST[province]', country='$_POST[country]', phone='$_POST[phone]', language='$_SESSION[lang]' WHERE member_id=$_SESSION[member_id]";

			$result = mysql_query($sql,$db);
			if (!$result) {
				$errors[]=AT_ERROR_DB_NOT_UPDATED;
				print_errors($errors);
				exit;
			}

			Header('Location: ./index.php?f='.urlencode_feedback(AT_FEEDBACK_PROFILE_UPDATED));
			exit;
		}
}


require(AT_INCLUDE_PATH.'cc_html/header.inc.php');

?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<h2><?php   echo _AT('edit_profile'); ?></h2>
<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="">
<?php
	$sql	= 'SELECT * FROM '.TABLE_PREFIX.'members WHERE member_id='.$_SESSION['member_id'];
	$result = mysql_query($sql,$db);
	$row = mysql_fetch_array($result);

	if ($_POST['submit']){
		$row['password']	= $_POST['password'];
		$row['email']		= $_POST['email'];
		$row['first_name']	= $_POST['first_name'];
		$row['last_name']	= $_POST['last_name'];
		$row['age']			= $_POST['age'];
		$row['address']		= $_POST['address'];
		$row['postal']		= $_POST['postal'];
		$row['city']		= $_POST['city'];
		$row['province']	= $_POST['province'];
		$row['country']		= $_POST['country'];
		$row['phone']		= $_POST['phone'];
		$row['website']		= $_POST['website'];
	}
?>
<tr>
	<td colspan="2" class="cat"><h4><?php   echo _AT('account_information'); ?></h4></td>
</tr>
<tr>
	<td class="row1" align="right"><?php   echo _AT('login'); ?>:</td>
	<td class="row1"><?php echo $row['login'];?></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right" valign="top"><label for="password"><?php   echo _AT('password'); ?>:</label></td>
	<td class="row1" valign="top"><input id="password" class="formfield" name="password" type="password"  size="15" maxlength="15" value="<?php echo stripslashes(htmlspecialchars($row['password'])); ?>" /><br />
	<small class="spacer">&middot; <?php echo _AT('combination'); ?>.<br />
	&middot; <?php echo _AT('15_max_chars'); ?>.</small></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><label for="password2"><?php echo _AT('password_again'); ?>:</label></td>
	<td class="row1"><input id="password2" class="formfield" name="password2" type="password" size="15" maxlength="15" value="<?php if ($_POST['submit']){ echo stripslashes(htmlspecialchars($_POST['password2'])); } else { echo stripslashes(htmlspecialchars($row['password'])); }?>" /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right" valign="top"><label for="email"><?php   echo _AT('email_address'); ?>:</label></td>
	<td class="row1"><input id="email" class="formfield" name="email" type="text" size="30" maxlength="60"  value="<?php echo stripslashes(htmlspecialchars($row['email']));?>" /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right" valign="top"><label for="language"><?php echo _AT('language'); ?>:</label></td>
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
	<td colspan="2" class="cat"><h4><?php   echo _AT('personal_information'); ?></h4></td> 
</tr>
<tr>
	<td class="row1" align="right"><label for="first_name"><?php   echo _AT('first_name'); ?>:</label></td>
	<td class="row1"><input id="first_name" class="formfield" name="first_name" type="text" value="<?php echo  stripslashes(htmlspecialchars($row['first_name']));?>" /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><label for="last_name"><?php   echo _AT('last_name'); ?>:</label></td>
	<td class="row1"><input id="last_name" class="formfield" name="last_name" type="text"  value="<?php echo stripslashes(htmlspecialchars($row['last_name']));?>" /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><label for="age"><?php   echo _AT('age'); ?>:</label></td>
	<td class="row1"><input id="age" class="formfield" name="age" type="text" size="2" maxlength="2" value="<?php echo stripslashes(htmlspecialchars($row['age']));?>" /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><?php   echo _AT('gender'); ?>:</td>
	<td class="row1"><?php
	if ($row['gender'] == 'm'){
		$m = ' checked="checked"';
	}

	if ($row['gender'] == 'f'){
		$f = ' checked="checked"';
	}

	?><input type="radio" name="gender" id="m" <?php echo $m;?> value="m" /><label for="m"><?php   echo _AT('male'); ?></label> <input type="radio" value="f" name="gender" id="f" <?php echo $f;?>  size="2" maxlength="2" /><label for="f"><?php   echo _AT('female'); ?></label></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><label for="address"><?php   echo _AT('street_address'); ?>:</label></td>
	<td class="row1"><input id="address" class="formfield" name="address" size="40" type="text"   value="<?php echo stripslashes(htmlspecialchars($row['address']));?>" /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><label for="postal"><?php   echo _AT('postal_code'); ?>:</label></td>
	<td class="row1"><input id="postal" class="formfield" name="postal" size="7" type="text"   value="<?php echo stripslashes(htmlspecialchars($row['postal']));?>" /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><label for="city"><?php   echo _AT('city'); ?>:</label></td>
	<td class="row1"><input id="city" class="formfield" name="city" type="text" value="<?php echo stripslashes(htmlspecialchars($row['city'])); ?>" /><br /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><label for="province"><?php   echo _AT('province'); ?>:</label></td>
	<td class="row1"><input id="province" class="formfield" name="province" type="text"   value="<?php echo stripslashes(htmlspecialchars($row['province']));?>" /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><label for="country"><?php   echo _AT('country'); ?>:</label></td>
	<td class="row1"><input id="country" class="formfield" name="country" type="text"   value="<?php echo stripslashes(htmlspecialchars($row['country']));?>" /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right" valign="top"><label for="phone"><?php   echo _AT('phone'); ?>:</label></td>
	<td class="row1"><input class="formfield" size="11" name="phone" id="phone" type="text" value="<?php echo stripslashes(htmlspecialchars($row['phone']));?>" /><br />
	<small class="spacer">&middot; Eg. 123-456-7890</small></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right" valign="top"><label for="website"><?php   echo _AT('web_site'); ?>:</label></td>
	<td class="row1"><input id="website" class="formfield" name="website" size="40" type="text" value="<?php echo stripslashes(htmlspecialchars($row['website']));?>" /><br /><br /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="center" colspan="2"><input type="submit" class="button" value=" <?php   echo _AT('update_profile'); ?> [Alt-s]" name="submit" accesskey="s" /> <input type="submit" name="cancel" class="button" value=" <?php echo  _AT('cancel'); ?>" /></td>
</tr>
</table>
</form>
<?php
	require (AT_INCLUDE_PATH.'cc_html/footer.inc.php'); 
?>
