<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* http://atutor.ca                                                     */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$

if (!defined('AT_INSTALLER_INCLUDE_PATH') || !defined('AT_INCLUDE_PATH')) { exit; }

include(AT_INCLUDE_PATH . 'install/install.inc.php');

if(isset($_POST['submit']) && ($_POST['action'] == 'process')) {
	install_step_accounts($_POST['admin_username'], $_POST['form_admin_password_hidden'], $_POST['admin_email'], $_POST['site_name'],
                               $_POST['email'], $_POST['account_username'], $_POST['form_account_password_hidden'],
                               $_POST['account_fname'], $_POST['account_lname'], $_POST['account_email'],
                               $_POST['just_social'], $_POST['home_url'], get_atutor_installation_path(AT_INSTALLER_INCLUDE_PATH),
                               $_POST['step2']['db_host'], $_POST['step2']['db_port'], $_POST['step2']['db_login'], 
                               $_POST['step2']['db_password'], $_POST['step2']['db_name'], $_POST['step2']['tb_prefix'], true);
	
	if (!isset($errors)) {
		unset($_POST['admin_username']);
		unset($_POST['form_admin_password_hidden']);
		unset($_POST['admin_email']);
		unset($_POST['account_username']);
		unset($_POST['form_account_password_hidden']);
		unset($_POST['account_email']);
		unset($_POST['home_url']);
		unset($_POST['email']);
		unset($_POST['site_name']);
		unset($_POST['just_social']);
	
		unset($errors);
		unset($_POST['submit']);
		unset($action);
		store_steps($step);
		$step++;
		return;
	}
}	

print_progress($step);

if (isset($errors)) {
	print_errors($errors);
}

if (isset($_POST['step1']['old_version']) && $_POST['upgrade_action']) {
	$defaults['admin_username'] = urldecode($_POST['step1']['admin_username']);
	$defaults['admin_email']    = urldecode($_POST['step1']['admin_email']);

	$defaults['site_name']   = urldecode($_POST['step1']['site_name']);
	$defaults['header_img']  = urldecode($_POST['step1']['header_img']);
	$defaults['header_logo'] = urldecode($_POST['step1']['header_logo']);
	$defaults['home_url']    = urldecode($_POST['step1']['home_url']);
} else {
	$defaults = $_defaults;
}

?>
<script language="JavaScript" src="<?php echo AT_INSTALLER_INCLUDE_PATH; ?>../../sha-1factory.js" type="text/javascript"></script>

<script type="text/javascript">
function encrypt_password()
{
	document.form.form_admin_password_hidden.value = hex_sha1(document.form.admin_password.value);
	document.form.form_account_password_hidden.value = hex_sha1(document.form.account_password.value);
	document.form.admin_password.value = "";
	document.form.account_password.value = "";
}
</script>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
	<input type="hidden" name="action" value="process" />
	<input type="hidden" name="form_admin_password_hidden" value="" />
	<input type="hidden" name="form_account_password_hidden" value="" />
	<input type="hidden" name="step" value="<?php echo $step; ?>" />
	<?php print_hidden($step); ?>

	<?php
		/* detect mail settings. if sendmail_path is empty then use SMTP. */
		if (@ini_get('sendmail_path') == '') { 
			echo '<input type="hidden" name="smtp" value="true" />';
		} else {
			echo '<input type="hidden" name="smtp" value="false" />';
		}
	?>
	<br />
		<table width="70%" class="tableborder" cellspacing="0" cellpadding="1" align="center">
		<tr>
			<th colspan="2">Super Administrator Account</th>
		</tr>
		<tr>
			<td colspan="2" class="row1">The Super Administrator account is used for managing ATutor. The Super Administrator can also create additional Administrators each with their own privileges and roles. Administrator accounts cannot enroll in courses.</td>
		</tr>
		<tr>
			<td class="row1"><span class="required" title="Required Field">*</span><b><label for="username">Administrator Username:</label></b><br />
			May contain only letters, numbers, or underscores.</td>
			<td class="row1"><input type="text" name="admin_username" id="username" maxlength="20" size="20" value="<?php if (!empty($_POST['admin_username'])) { echo stripslashes(htmlspecialchars($_POST['admin_username'])); } else { echo $defaults['admin_username']; } ?>" class="formfield" /></td>
		</tr>
		<tr>
			<td class="row1"><span class="required" title="Required Field">*</span><b><label for="password">Administrator Password:</label></b></td>
			<td class="row1"><input type="text" name="admin_password" id="password" maxlength="15" size="15" class="formfield" /></td>
		</tr>
		<tr>
			<td class="row1"><span class="required" title="Required Field">*</span><b><label for="email">Administrator Email:</label></b></td>
			<td class="row1"><input type="text" name="admin_email" id="email" size="40" value="<?php if (!empty($_POST['admin_email'])) { echo stripslashes(htmlspecialchars($_POST['admin_email'])); } else { echo $defaults['admin_email']; } ?>" class="formfield" /></td>
		</tr>
		</table>

	<br />

		<table width="70%" class="tableborder" cellspacing="0" cellpadding="1" align="center">
		<tr>
			<th colspan="2">System Preferences</th>
		</tr>
		<tr>
			<td class="row1"><span class="required" title="Required Field">*</span><b><label for="sitename">Site Name:</label></b><br />
			The name of your course server website.<br />Default: <kbd><?php echo $defaults['site_name']; ?></kbd></td>
			<td class="row1"><input type="text" name="site_name" size="28" maxlength="60" id="sitename" value="<?php if (!empty($_POST['site_name'])) { echo stripslashes(htmlspecialchars($_POST['site_name'])); } else { echo $defaults['site_name']; } ?>" class="formfield" /></td>
		</tr>
		<tr>
			<td class="row1"><span class="required" title="Required Field">*</span><b><label for="cemail">Contact Email:</label></b><br />
			The email that will be used as the return email when needed.</td>
			<td class="row1"><input type="text" name="email" id="cemail" size="40" value="<?php if (!empty($_POST['email'])) { echo stripslashes(htmlspecialchars($_POST['email'])); } else { echo $defaults['email']; } ?>" class="formfield" /></td>
		</tr>
		<tr>			
			<td class="row1"><span class="required" title="Required Field">*</span><b><label for="just_social">Just Social:</label></b><br />
			Deploy ATutor as just a Social Networking platform? (without LMS)</td>
			<td class="row1">
				<label for="social_y">Just Social</label><input type="radio" name="just_social" id="social_y" value="1" class="formfield" <?php echo ($_POST['just_social']==1)?' checked="checked"':''; ?>/>
				<label for="social_n">Social and LMS</label><input type="radio" name="just_social" id="social_n" value="0" class="formfield" <?php echo ($_POST['just_social']==0 || !isset($_POST['just_social']))?' checked="checked"':''; ?>/>
			</td>
		</tr>
		<tr>
			<td class="row1"><div class="optional" title="Optional Field">?</div><b><label for="home_url">Optional 'Home' URL:</label></b><br />
			This will be the URL for the 'Home' link in the Public Area. Leave empty to have this link not appear. <br /></td>
			<td class="row1"><input type="text" name="home_url" size="28" maxlength="60" id="home_url" value="<?php if (!empty($_POST['home_url'])) { echo stripslashes(htmlspecialchars($_POST['home_url'])); } else { echo $defaults['home_url']; } ?>" class="formfield" /></td>
		</tr>
		</table>

	<br />

		<table width="70%" class="tableborder" cellspacing="0" cellpadding="1" align="center">
		<tr>
			<th colspan="2">Personal Account</th>
		</tr>
		<tr>
			<td colspan="2" class="row1">You will need a personal account to view and create courses.</td>
		</tr>
		<tr>
			<td class="row1"><span class="required" title="Required Field">*</span><b><label for="account_username">Username:</label></b><br />
			May contain only letters, numbers, and underscores.</td>
			<td class="row1"><input type="text" name="account_username" id="account_username" maxlength="20" size="20" value="<?php if (!empty($_POST['account_username'])) { echo stripslashes(htmlspecialchars($_POST['account_username'])); } ?>" class="formfield" /></td>
		</tr>
		<tr>
			<td class="row1"><span class="required" title="Required Field">*</span><b><label for="account_password">Password:</label></b></td>
			<td class="row1"><input type="text" name="account_password" id="account_password" maxlength="15" size="15" class="formfield" /></td>
		</tr>
		<tr>
			<td class="row1"><span class="required" title="Required Field">*</span><b><label for="account_email">Email:</label></b></td>
			<td class="row1"><input type="text" name="account_email" id="account_email" size="40" maxlength="60" value="<?php if (!empty($_POST['account_email'])) { echo stripslashes(htmlspecialchars($_POST['account_email'])); } ?>" class="formfield" /></td>
		</tr>
		<tr>
			<td class="row1"><span class="required" title="Required Field">*</span><b><label for="account_fname">First Name:</label></b></td>
			<td class="row1"><input type="text" name="account_fname" id="account_fname" size="40" maxlength="60" value="<?php if (!empty($_POST['account_fname'])) { echo stripslashes(htmlspecialchars($_POST['account_fname'])); } ?>" class="formfield" /></td>
		</tr>
		<tr>
			<td class="row1"><span class="required" title="Required Field">*</span><b><label for="account_lname">Last Name:</label></b></td>
			<td class="row1"><input type="text" name="account_lname" id="account_lname" size="40" maxlength="60" value="<?php if (!empty($_POST['account_lname'])) { echo stripslashes(htmlspecialchars($_POST['account_lname'])); } ?>" class="formfield" /></td>
		</tr>
		</table>
	<br />
	<br />
	<div align="center"><input type="submit" class="button" value=" Next &raquo;" name="submit" onclick="return encrypt_password();" /></div>
</form>