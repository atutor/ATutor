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
$_include_path = '../include/';
require($_include_path.'vitals.inc.php');

if ($_POST['form_login'])
{
	if (($_POST['form_login'] == ADMIN_USERNAME) && ($_POST['form_password'] == ADMIN_PASSWORD)) {
		$_SESSION['s_is_super_admin'] = true;

		header('Location: admin/');
		exit;
	} else {
		$errors[] = AT_ERROR_INVALID_LOGIN;
	}
}

require($_include_path.'cc_html/header.inc.php'); 


?>
	<br /><br />
	<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<table cellspacing="1" cellpadding="0" border="0" class="bodyline" align="center" summary="">
	<tr>
		<td class="cat" colspan="2"><h4><?php echo _AT('sysadmin_login'); ?></h4></td>
	</tr>
	<tr>
		<td class="row1" align="right"><label for="login"><b><?php echo _AT('login'); ?>:</b></label></td>
		<td class="row1" align="left"><input type="text" class="formfield" name="form_login" id="login" /></td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1" align="right" valign="top"><label for="pass"><b><?php echo _AT('password'); ?>:</b></label></td>
		<td class="row1" align="left" valign="top"><input type="password" class="formfield" name="form_password" id="pass" /><br /><br /></td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td align="center" colspan="2" class="row1"><input type="submit" name="submit" class="button" value="<?php echo _AT('login'); ?>" /></td>
	</tr>
	</table> 
	<br /><br />
	</form>

<?php
require($_include_path.'cc_html/footer.inc.php'); 
?>