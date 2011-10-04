<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$
define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_USERS);

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.AT_BASE_HREF.'mods/_core/users/admins/index.php');
	exit;
} else if (isset($_POST['submit'])) {
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

	if (!$msg->containsErrors()) {
		$password     = $addslashes($_POST['form_password_hidden']);

		$sql    = "UPDATE ".TABLE_PREFIX."admins SET password='$password', last_login=last_login WHERE login='$_POST[login]'";
		$result = mysql_query($sql, $db);

		$sql    = "UPDATE ".TABLE_PREFIX."admins SET password='********' WHERE login='$_POST[login]'";
		write_to_log(AT_ADMIN_LOG_UPDATE, 'admins', mysql_affected_rows($db), $sql);

		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		header('Location: '.AT_BASE_HREF.'mods/_core/users/admins/index.php');
		exit;
	}
	$_POST['login'] = $stripslashes($_POST['login']);
}


$_GET['login'] = $addslashes($_REQUEST['login']);

$sql = "SELECT login FROM ".TABLE_PREFIX."admins WHERE login='$_GET[login]'";
$result = mysql_query($sql, $db);
if (!($row = mysql_fetch_assoc($result))) {
	$msg->addError('USER_NOT_FOUND');
	$msg->printErrors();
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}
if (!isset($_POST['submit'])) {
	$_POST = $row;

	if (query_bit($row['privileges'], AT_ADMIN_PRIV_ADMIN)) {
		$_POST['priv_admin'] = 1;
	}
	$_POST['privs'] = intval($row['privileges']);
}

$onload = 'document.form.password1.focus();';
require(AT_INCLUDE_PATH.'header.inc.php');

?>
<script language="JavaScript" src="sha-1factory.js" type="text/javascript"></script>

<script type="text/javascript">
function encrypt_password()
{
	document.form.password_error.value = "";

	err = verify_password(document.form.password1.value, document.form.confirm_password.value);
	
	if (err.length > 0)
	{
		document.form.password_error.value = err;
	}
	else
	{
		document.form.form_password_hidden.value = hex_sha1(document.form.password1.value);
		document.form.password1.value = "";
		document.form.confirm_password.value = "";
	}
}
</script>



<?php 
$savant->assign('row', $row);
$savant->display('admin/users/password.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php'); ?>