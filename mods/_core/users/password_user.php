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

$_user_location = 'admin';

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_USERS);

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.AT_BASE_HREF.'mods/_core/users/users.php');
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
		$_POST['id'] = intval($_POST['id']);

		$sql = "UPDATE ".TABLE_PREFIX."members SET password= '$_POST[form_password_hidden]', creation_date=creation_date, last_login=last_login WHERE member_id=$_POST[id]";
		$result = mysql_query($sql, $db);

		$sql	= "SELECT login, email FROM ".TABLE_PREFIX."members WHERE member_id=$_POST[id]";
		$result = mysql_query($sql,$db);
		if ($row = mysql_fetch_assoc($result)) {
			$r_login = $row['login'];	
			$r_email = $row['email'];

			$tmp_message  = _AT('password_change_msg')."\n\n";
			$tmp_message .= _AT('web_site').' : '.AT_BASE_HREF."\n";
			$tmp_message .= _AT('login_name').' : '.$r_login."\n";

			require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');

			$mail = new ATutorMailer;

			$mail->From     = $_config['contact_email'];
			$mail->AddAddress($r_email);
			$mail->Subject = $_config['site_name'] . ': ' . _AT('password_changed');
			$mail->Body    = $tmp_message;

			if(!$mail->Send()) {
			   $msg->printErrors('SENDING_ERROR');
			   exit;
			}

		}

		$msg->addFeedback('PROFILE_UPDATED_ADMIN');
		header('Location: '.AT_BASE_HREF.'mods/_core/users/users.php');
		exit;
	}
	$_GET['id'] = $_POST['id'];
}


$onload = 'document.form.password.focus();';

require(AT_INCLUDE_PATH.'header.inc.php');

$id = intval($_GET['id']);

$sql	= "SELECT login FROM ".TABLE_PREFIX."members WHERE member_id=$id";
$result = mysql_query($sql, $db);

if (!$row = mysql_fetch_assoc($result)) {
	$msg->printErrors('USER_NOT_FOUND');
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

?>
<script language="JavaScript" src="sha-1factory.js" type="text/javascript"></script>

<script type="text/javascript">
function encrypt_password()
{
	document.form.password_error.value = "";

	err = verify_password(document.form.password.value, document.form.password2.value);
	
	if (err.length > 0)
	{
		document.form.password_error.value = err;
	}
	else
	{
		document.form.form_password_hidden.value = hex_sha1(document.form.password.value);
		document.form.password.value = "";
		document.form.password2.value = "";
	}
}
</script>

<?php 
$savant->assign('id', $id);
$savant->display('admin/users/password_user.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php'); ?>