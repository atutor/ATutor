<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/

$section = 'users';
$page	 = 'login';
$_public	= true;
define('AT_INCLUDE_PATH', 'include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

if (isset($_POST['cancel'])) {
	Header('Location: about.php');
	exit;
}

// check if we have a cookie
if (isset($_COOKIE['ATLogin'])) {
	$cookie_login = $_COOKIE['ATLogin'];
}
if (isset($_COOKIE['ATPass'])) {
	$cookie_pass  = $_COOKIE['ATPass'];
}

if (isset($cookie_login, $cookie_pass) && !isset($_POST['submit'])) {
	/* auto login */
	$this_login		= $cookie_login;
	$this_password	= $cookie_pass;
	$auto_login		= 1;
	$used_cookie	= true;
	
} else if (isset($_POST['submit'])) {
	/* form post login */
	$this_login		= $_POST['form_login'];
	$this_password  = $_POST['form_password'];
	$auto_login		= intval($_POST['auto']);
	$used_cookie	= false;

}

if (isset($this_login, $this_password)) {
	if (($this_login == ADMIN_USERNAME) && (stripslashes($this_password) == stripslashes(ADMIN_PASSWORD))) {
		$_SESSION['login']		= $this_login;
		$_SESSION['valid_user'] = true;
		$_SESSION['course_id']  = -1;
		header('Location: admin/index.php');
		exit;
	}

	if ($_GET['course'] != '') {
		$_POST['form_course_id'] = intval($_GET['course']);
	} else {
		$_POST['form_course_id'] = intval($_POST['form_course_id']);
	}

	if ($used_cookie) {
		// check if that cookie is valid
		$sql = "SELECT member_id, login, preferences, PASSWORD(password) AS pass, language FROM ".TABLE_PREFIX."members WHERE login='$this_login' AND PASSWORD(password)='$this_password'";

	} else {
		$sql = "SELECT member_id, login, preferences, PASSWORD(password) AS pass, language FROM ".TABLE_PREFIX."members WHERE login='$this_login' AND PASSWORD(password)=PASSWORD('$this_password')";
	}

	$result = mysql_query($sql, $db);
	if ($row = mysql_fetch_assoc($result)) {
		$_SESSION['login']		= $row['login'];
		$_SESSION['valid_user'] = true;
		$_SESSION['member_id']	= intval($row['member_id']);
		assign_session_prefs(unserialize(stripslashes($row['preferences'])));
		$_SESSION['is_guest']	= 0;
		$_SESSION['lang']		= $row['language'];

		if ($auto_login == 1) {
			$parts = parse_url($_base_href);
			// update the cookie.. increment to another 2 days
			$cookie_expire = time()+172800;
			setcookie('ATLogin', $this_login, $cookie_expire, $parts['path'], $parts['host'], 0);
			setcookie('ATPass',  $row['pass'],  $cookie_expire, $parts['path'], $parts['host'], 0);
		}
		header('Location: bounce.php?course='.$_POST['form_course_id']);
		exit;
	} else {
		$errors[] = AT_ERROR_INVALID_LOGIN;
	}
}

if (isset($_SESSION['member_id'])) {
	$sql = "DELETE FROM ".TABLE_PREFIX."users_online WHERE member_id=$_SESSION[member_id]";
	$result = @mysql_query($sql, $db);
}

//session_destroy();
unset($_SESSION['member_id']);
unset($_SESSION['valid_user']);
unset($_SESSION['login']);
unset($_SESSION['is_admin']);
unset($_SESSION['course_id']);
unset($_SESSION['is_guest']);

$onload = 'onload="document.form.form_login.focus()"';

require(AT_INCLUDE_PATH.'basic_html/header.php');

?>
<h3><?php echo _AT('login'); ?></h3>

<?php 
if ($_GET['f']) {
	$f = intval($_GET['f']);
	print_feedback($f);		
}
if (isset($errors)) {
	print_errors($errors);
}
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="form_login_action" value="true" />
<input type="hidden" name="form_course_id" value="<?php echo $_GET['course']; ?>" />

<table cellspacing="5" cellpadding="0" border="0" align="center">
<tr>
	<td class="row3" colspan="4" align="center"><h4><?php echo _AT('login'); ?><?php
	
	if (isset($_GET['course'])) {
		echo ' '._AT('to1').' '.$system_courses[$_GET['course']]['title'];
	} else {
		echo ' '._AT('to_control');
	}
	?></h4></td>
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
	<td class="row1" colspan="4" align="center" valign="top"><input type="checkbox" name="auto" value="1" id="auto" /><label for="auto"><?php echo _AT('auto_login2'); ?></label>
	</td>
</tr>
</table>

<p align="center"><br /><input type="submit" name="submit" class="button" value="<?php echo _AT('login'); ?>" />	- <input type="submit" name="cancel" class="button" value=" <?php echo _AT('cancel'); ?> " /></p>
	
<br /><p align="center">&middot; <a href="password_reminder.php"><?php echo _AT('forgot'); ?></a><br />
	&middot; <?php echo _AT('no_account'); ?> <a href="registration.php"><?php echo _AT('free_account'); ?></a>.</p>

</form>
<br /><br />
<?php
	require(AT_INCLUDE_PATH.'basic_html/footer.php');
?>