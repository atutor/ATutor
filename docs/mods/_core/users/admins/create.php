<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2010                                                  */
/* Inclusive Design Institute                                               */
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_ADMIN);

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php');
	exit;
} else if (isset($_POST['submit'])) {
	$missing_fields = array();

	/* login validation */
	if ($_POST['login'] == '') {
		$missing_fields[] = _AT('login_name');
	} else {
		/* check for special characters */
		if (!(preg_match("/^[a-zA-Z0-9_]([a-zA-Z0-9_])*$/i", $_POST['login']))) {
			$msg->addError('LOGIN_CHARS');
		} else {
			$result = mysql_query("SELECT * FROM ".TABLE_PREFIX."members WHERE login='$_POST[login]'",$db);
			if (mysql_num_rows($result) != 0) {
				$msg->addError('LOGIN_EXISTS');
			} 
						
			$result = mysql_query("SELECT * FROM ".TABLE_PREFIX."admins WHERE login='$_POST[login]'",$db);
			if (mysql_num_rows($result) != 0) {
				$msg->addError('LOGIN_EXISTS');
			}
		}
	}

	/* password check: password is verified front end by javascript. here is to handle the errors from javascript */
	if ($_POST['password_error'] <> "")
	{
		$pwd_errors = explode(",", $_POST['password_error']);

		foreach ($pwd_errors as $pwd_error)
		{
			if ($pwd_error == "missing_password")
				$missing_fields[] = _AT('password');
			else
				$msg->addError($pwd_error);
		}
	}

	/* email validation */
	if ($_POST['email'] == '') {
		$missing_fields[] = _AT('email');
	} else if (!preg_match("/^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,6}$/i", $_POST['email'])) {
		$msg->addError('EMAIL_INVALID');
	}
	$result = mysql_query("SELECT * FROM ".TABLE_PREFIX."members WHERE email LIKE '$_POST[email]'",$db);
	if (mysql_num_rows($result) != 0) {
		$valid = 'no';
		$msg->addError('EMAIL_EXISTS');
	}

	$priv = 0;
	if (isset($_POST['priv_admin'])) {
		// overrides all above.
		$priv = AT_ADMIN_PRIV_ADMIN;
	} else if (isset($_POST['privs'])) {
		foreach ($_POST['privs'] as $value) {
			$priv += intval($value);
		}
	}
	$_POST['privs'] = $priv;

	if ($missing_fields) {
		$missing_fields = implode(', ', $missing_fields);
		$msg->addError(array('EMPTY_FIELDS', $missing_fields));
	}

	if (!$msg->containsErrors()) {
		$_POST['login']     = $addslashes($_POST['login']);
		$password  = $addslashes($_POST['form_password_hidden']);
		$_POST['real_name'] = $addslashes($_POST['real_name']);
		$_POST['email']     = $addslashes($_POST['email']);

		$admin_lang = $_config['default_language']; 

		$sql    = "INSERT INTO ".TABLE_PREFIX."admins
		                 (login,
		                  password,
		                  real_name,
		                  email,
		                  language,
		                  `privileges`,
		                  last_login)
		          VALUES ('$_POST[login]', 
		                  '$password', 
		                  '$_POST[real_name]', 
		                  '$_POST[email]', 
		                  '$admin_lang', 
		                  $priv, 
		                  0)";
		$result = mysql_query($sql, $db) or die(mysql_error());

		$sql    = "INSERT INTO ".TABLE_PREFIX."admins
		                 (login,
		                  password,
		                  real_name,
		                  email,
		                  language,
		                  `privileges`,
		                  last_login)
		          VALUES ('$_POST[login]', 
		                  '********', 
		                  '$_POST[real_name]', 
		                  '$_POST[email]', 
		                  '$admin_lang', 
		                  $priv, 
		                  0)";
		                  
		write_to_log(AT_ADMIN_LOG_INSERT, 'admins', mysql_affected_rows($db), $sql);

		$msg->addFeedback('ADMIN_CREATED');
		header('Location: index.php');
		exit;
	}
	$_POST['login']             = $stripslashes($_POST['login']);
	$_POST['real_name']         = $stripslashes($_POST['real_name']);
	$_POST['email']             = $stripslashes($_POST['email']);
} 

$onload = 'document.form.login.focus();';
require(AT_INCLUDE_PATH.'header.inc.php'); 
?>
<script language="JavaScript" src="sha-1factory.js" type="text/javascript"></script>

<?php
	$module_list = $moduleFactory->getModules(AT_MODULE_STATUS_ENABLED, 0, TRUE);
	$keys = array_keys($module_list);
?>


<script language="JavaScript" src="sha-1factory.js" type="text/javascript"></script>

<script type="text/javascript">
function encrypt_password()
{
	document.form.password_error.value = "";

	err = verify_password(document.form.password.value, document.form.confirm_password.value);
	
	if (err.length > 0)
	{
		document.form.password_error.value = err;
	}
	else
	{
		document.form.form_password_hidden.value = hex_sha1(document.form.password.value);
		document.form.password.value = "";
		document.form.confirm_password.value = "";
		if (document.form.priv_admin.checked == true) 
		{
			return confirm('<?php echo _AT('confirm_admin_create'); ?>');
		} 
		else 
		{
			return true;
		}
	}
}
</script>

<?php 
$savant->assign('keys', $keys);
$savant->assign('module_list', $module_list);
$savant->display('admin/users/create.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php'); ?>
