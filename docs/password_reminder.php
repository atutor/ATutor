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
$page	 = 'password';
$_public	= true;
define('AT_INCLUDE_PATH', 'include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
require (AT_INCLUDE_PATH.'lib/atutor_mail.inc.php');

	if ($_POST['cancel']) {
		Header('Location: about.php');
		exit;
	}

if ($_POST['form_password_reminder'])
{
	$sql	= "SELECT login, password, email FROM ".TABLE_PREFIX."members WHERE email='$_POST[form_email]'";
	$result = mysql_query($sql,$db);
	if (mysql_num_rows($result) == 0) {
		$errors[]=AT_ERROR_EMAIL_NOT_FOUND;
	} else {
		$row = mysql_fetch_array($result);
		$r_login = $row['login'];	
		$r_passwd= $row['password'];
		$r_email = $row['email'];

		$message = _AT('hello').','."\n"._AT('password_request2')."\n".$HTTP_SERVER_VARS["REMOTE_ADDR"].'.'."\n";
		$message .= _AT('login').': '.$r_login."\n"._AT('password').': '.$r_passwd."\n";
		atutor_mail($r_email, 'ATutor '._AT('password_reminder'), $message, ADMIN_EMAIL);
		$success = true;
	}
}

require(AT_INCLUDE_PATH.'basic_html/header.php');
?>
<h2><?php echo _AT('password_reminder');  ?></h2>
<?php
		if ($errors && !$success) {
			print_errors($errors);
		}
?>
<form action="<?php echo $PHP_SELF; ?>" method="post">
<input type="hidden" name="form_password_reminder" value="true" />

<table cellspacing="1" cellpadding="0" border="0" class="bodyline" align="center" width="60%" summary="">
<tr>
	<td class="cat" colspan="2"><h4><?php echo _AT('password_reminder'); ?></h4></td>
</tr>
<?php
	if (!$success) {
?>
<tr>
	<td class="row1" align="left" colspan="2"><?php echo _AT('password_blurb'); ?><br /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td valign="top" align="right" class="row1"><label for="email"><b><?php echo _AT('email_address'); ?></b></label></td>
	<td valign="top" align="left" class="row1"><input type="text" class="formfield" name="form_email" id="email" /><br /><br /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td align="center" colspan="2" class="row1"><input type="submit" name="submit" class="button" value="<?php echo _AT('submit'); ?>" /> - <input type="submit" name="cancel" class="button" value=" <?php echo _AT('cancel'); ?> " /></td>
</tr>
<?php
	} else {
?>
<tr>
	<td align="center" colspan="2"><?php echo _AT('password_success'); ?></td>
</tr>
<?php
	}
?>
</table>
</form>
<?php
	require(AT_INCLUDE_PATH.'basic_html/footer.php');
?>
