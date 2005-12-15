<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay, Joel Kronenberg & Heidi Hazelton	*/
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
	} else if (!eregi("^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,6}$", $_POST['email'])) {
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

		if (isset($_POST['priv_admin'])) {
			// overrides all above.
			$priv = AT_ADMIN_PRIV_ADMIN;
		} else if (isset($_POST['privs'])) {
			foreach ($_POST['privs'] as $value) {
				$priv += intval($value);
			}
		}

		$sql    = "UPDATE ".TABLE_PREFIX."admins SET password='$_POST[password]', real_name='$_POST[real_name]', email='$_POST[email]', `privileges`=$priv WHERE login='$_POST[login]'";
		$result = mysql_query($sql, $db);

		$sql    = "UPDATE ".TABLE_PREFIX."admins SET password='*****', real_name='$_POST[real_name]', email='$_POST[email]', `privileges`=$priv WHERE login='$_POST[login]'";

		write_to_log(AT_ADMIN_LOG_UPDATE, 'admins', mysql_affected_rows($db), $sql);

		$msg->addFeedback('ADMIN_EDITED');
		header('Location: index.php');
		exit;
	}
} 

require(AT_INCLUDE_PATH.'header.inc.php'); 

$_GET['login'] = $addslashes($_REQUEST['login']);

$sql = "SELECT * FROM ".TABLE_PREFIX."admins WHERE login='$_GET[login]'";
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
	$_POST['privs'] = intval($row['privileges']);
}

?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
<input type="hidden" name="login" value="<?php echo $row['login']; ?>" />
<div class="input-form">
	<div class="row">
		<h3><?php echo $row['login']; ?></h3>
	</div>

	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="password"><?php echo _AT('password'); ?></label><br />
		<input type="password" name="password" id="password" size="25" value="<?php echo $_POST['password']; ?>" />
	</div>

	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="password2"><?php echo _AT('confirm_password'); ?></label><br />
		<input type="password" name="confirm_password" id="password2" size="25" value="<?php echo $_POST['confirm_password']; ?>"  />
	</div>

	<div class="row">
		<label for="real_name"><?php echo _AT('real_name'); ?></label><br />
		<input type="text" name="real_name" id="real_name" size="30" value="<?php echo $_POST['real_name']; ?>" />
	</div>

	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="email"><?php echo _AT('email'); ?></label><br />
		<input type="text" name="email" id="email" size="30" value="<?php echo $_POST['email']; ?>" />
	</div>

	<div class="row">
		<?php echo _AT('privileges'); ?><br />
		<input type="checkbox" name="priv_admin" value="1" id="priv_admin" <?php if ($_POST['priv_admin']) { echo 'checked="checked"'; } ?> /><label for="priv_admin"><?php echo _AT('priv_admin_super'); ?></label><br /><br />

		<?php
			$module_list = $moduleFactory->getModules(AT_MODULE_STATUS_ENABLED, 0, TRUE);
			$keys = array_keys($module_list);
		?>

		<?php foreach ($keys as $module_name): ?>
			<?php $module =& $module_list[$module_name]; ?>
			<?php if (!($module->getAdminPrivilege() > 1)) { continue; } ?>
				<input type="checkbox" name="privs[]" value="<?php echo $module->getAdminPrivilege(); ?>" id="priv_<?php echo $module->getAdminPrivilege(); ?>" <?php if (query_bit($_POST['privs'], $module->getAdminPrivilege())) { echo 'checked="checked"'; }  ?> /><label for="priv_<?php echo $module->getAdminPrivilege(); ?>"><?php echo $module->getName(); ?></label><br />
		<?php endforeach; ?>
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s" <?php if ($_POST['priv_admin'] != 1) { echo 'onClick="return checkAdmin();"'; } ?> />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
</div>
</form>

<script language="javascript">

function checkAdmin() {
	if (document.form.priv_admin.checked == true) {
		return confirm('<?php echo _AT('confirm_admin_create'); ?>');
	} else {
		return true;
	}
}

</script>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>