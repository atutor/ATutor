<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton	*/
/* Adaptive Technology Resource Centre / University of Toronto				*/
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_ADMIN);

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php');
	exit;
} else if (isset($_POST['submit'])) {
	/* password validation */
	if ($_POST['password'] == '') { 
		$msg->addError('PASSWORD_MISSING');
	} else {
		// check for valid passwords
		if ($_POST['password'] != $_POST['confirm_password']){
			$msg->addError('PASSWORD_MISMATCH');
		}
	}

	/* email validation */
	if ($_POST['email'] == '') {
		$msg->addError('EMAIL_MISSING');
	} else if (!eregi("^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,3}$", $_POST['email'])) {
		$msg->addError('EMAIL_INVALID');
	}
	$result = mysql_query("SELECT * FROM ".TABLE_PREFIX."members WHERE email LIKE '$_POST[email]'",$db);
	if (mysql_num_rows($result) != 0) {
		$valid = 'no';
		$msg->addError('EMAIL_EXISTS');
	}

	if (!$msg->containsErrors()) {
		$_POST['login']     = $addslashes($_POST['login']);
		$_POST['password']  = $addslashes($_POST['password']);
		$_POST['real_name'] = $addslashes($_POST['real_name']);
		$_POST['email']     = $addslashes($_POST['email']);

		$priv = 0;
		if (isset($_POST['priv_users'])) {
			$priv += AT_ADMIN_PRIV_USERS;
		}

		if (isset($_POST['priv_courses'])) {
			$priv += AT_ADMIN_PRIV_COURSES;
		}

		if (isset($_POST['priv_backups'])) {
			$priv += AT_ADMIN_PRIV_BACKUPS;
		}

		if (isset($_POST['priv_forums'])) {
			$priv += AT_ADMIN_PRIV_FORUMS;
		}

		if (isset($_POST['priv_categories'])) {
			$priv += AT_ADMIN_PRIV_CATEGORIES;
		}

		if (isset($_POST['priv_languages'])) {
			$priv += AT_ADMIN_PRIV_LANGUAGES;
		}

		if (isset($_POST['priv_themes'])) {
			$priv += AT_ADMIN_PRIV_THEMES;
		}

		if (isset($_POST['priv_admin'])) {
			// overrides all above.
			$priv = AT_ADMIN_PRIV_ADMIN;
		}

		$sql    = "UPDATE ".TABLE_PREFIX."admins SET password='$_POST[password]', real_name='$_POST[real_name]', email='$_POST[email]', `privileges`=$priv WHERE login='$_POST[login]'";
		$result = mysql_query($sql, $db);

		write_to_log(AT_ADMIN_LOG_UPDATE, 'admins', mysql_affected_rows($db), $sql);

		$msg->addFeedback('ADMIN_EDITED');
		header('Location: index.php');
		exit;
	}
} 

require(AT_INCLUDE_PATH.'header.inc.php'); 

$_GET['alogin'] = $addslashes($_GET['alogin']);

$sql = "SELECT * FROM ".TABLE_PREFIX."admins WHERE login='$_GET[alogin]'";
$result = mysql_query($sql, $db);
if (!($row = mysql_fetch_assoc($result))) {
	$msg->addError('USER_NOT_FOUND');
	$msg->printErrors();
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}
if (!isset($_POST['submit'])) {
	$_POST = $row;
	$_POST['confirm_password'] = $_POST['password'];
	if (query_bit($row['privileges'], AT_ADMIN_PRIV_ADMIN)) {
		$_POST['priv_admin'] = 1;
	}
	if (query_bit($row['privileges'], AT_ADMIN_PRIV_USERS)) {
		$_POST['priv_users'] = 1;
	}
	if (query_bit($row['privileges'], AT_ADMIN_PRIV_COURSES)) {
		$_POST['priv_courses'] = 1;
	}
	if (query_bit($row['privileges'], AT_ADMIN_PRIV_BACKUPS)) {
		$_POST['priv_backups'] = 1;
	}
	if (query_bit($row['privileges'], AT_ADMIN_PRIV_FORUMS)) {
		$_POST['priv_forums'] = 1;
	}
	if (query_bit($row['privileges'], AT_ADMIN_PRIV_CATEGORIES)) {
		$_POST['priv_categories'] = 1;
	}
	if (query_bit($row['privileges'], AT_ADMIN_PRIV_LANGUAGES)) {
		$_POST['priv_languages'] = 1;
	}
	if (query_bit($row['privileges'], AT_ADMIN_PRIV_THEMES)) {
		$_POST['priv_themes'] = 1;
	}

}

?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="login" value="<?php echo $row['login']; ?>" />
<div class="input-form">
	<div class="row">
		<h3><?php echo $row['login']; ?></h3>
	</div>

	<div class="row">
		<label for="password"><?php echo _AT('password'); ?></label><br />
		<input type="password" name="password" id="password" size="25" value="<?php echo $_POST['password']; ?>" />
	</div>

	<div class="row">
		<label for="password2"><?php echo _AT('confirm_password'); ?></label><br />
		<input type="password" name="confirm_password" id="password2" size="25" value="<?php echo $_POST['confirm_password']; ?>"  />
	</div>

	<div class="row">
		<label for="real_name"><?php echo _AT('real_name'); ?></label><br />
		<input type="text" name="real_name" id="real_name" size="30" value="<?php echo $_POST['real_name']; ?>" />
	</div>

	<div class="row">
		<label for="email"><?php echo _AT('email'); ?></label><br />
		<input type="text" name="email" id="email" size="30" value="<?php echo $_POST['email']; ?>" />
	</div>

	<div class="row">
		<?php echo _AT('privileges'); ?><br />
		<input type="checkbox" name="priv_admin" value="1" id="priv_admin" <?php if ($_POST['priv_admin']) { echo 'checked="checked"'; } ?> /><label for="priv_admin"><?php echo _AT('priv_admin'); ?></label><br /><br />

		<input type="checkbox" name="priv_users" value="1" id="priv_users" <?php if ($_POST['priv_users']) { echo 'checked="checked"'; } ?> /><label for="priv_users"><?php echo _AT('priv_users'); ?></label><br />
		<input type="checkbox" name="priv_courses" value="1" id="priv_courses" <?php if ($_POST['priv_courses']) { echo 'checked="checked"'; } ?> /><label for="priv_courses"><?php echo _AT('priv_courses'); ?></label><br />
		<input type="checkbox" name="priv_backups" value="1" id="priv_backups" <?php if ($_POST['priv_backups']) { echo 'checked="checked"'; } ?> /><label for="priv_backups"><?php echo _AT('priv_backups'); ?></label><br />
		<input type="checkbox" name="priv_forums" value="1" id="priv_forums" <?php if ($_POST['priv_forums']) { echo 'checked="checked"'; } ?> /><label for="priv_forums"><?php echo _AT('priv_forums'); ?></label><br />
		<input type="checkbox" name="priv_categories" value="1" id="priv_categories" <?php if ($_POST['priv_categories']) { echo 'checked="checked"'; } ?> /><label for="priv_categories"><?php echo _AT('priv_categories'); ?></label><br />
		<input type="checkbox" name="priv_languages" value="1" id="priv_languages" <?php if ($_POST['priv_languages']) { echo 'checked="checked"'; } ?> /><label for="priv_languages"><?php echo _AT('priv_languages'); ?></label><br />
		<input type="checkbox" name="priv_themes" value="1" id="priv_themes" <?php if ($_POST['priv_themes']) { echo 'checked="checked"'; } ?> /><label for="priv_themes"><?php echo _AT('priv_themes'); ?></label>
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>