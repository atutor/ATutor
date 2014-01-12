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

if ($_GET['login'] == $_SESSION['login']) {
	$msg->addError('ADMIN_EDIT_OWN_ACCOUNT');
	header('Location: index.php');
	exit;
}

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php');
	exit;
} else if (isset($_POST['submit'])) {
	$missing_fields = array();

	/* email validation */
	if ($_POST['email'] == '') {
		$missing_fields[] = _AT('email');
	} else if (!preg_match("/^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,6}$/i", $_POST['email'])) {
		$msg->addError('EMAIL_INVALID');
	}
	//check if admin email already exists.
	if($_POST['email'] != $_POST['hide_email']){

        $sql = "SELECT * FROM %sadmins WHERE email LIKE '%s'";
        $rows_admin_email = queryDB($sql,array(TABLE_PREFIX, $_POST['email']), TRUE);
        
        if(count(rows_admin_email) != 0){
            $valid = 'no';
            $msg->addError('EMAIL_EXISTS');
        }

         $sql = "SELECT * FROM %smembers WHERE email LIKE '%s'";
         $rows_members_email = queryDB($sql, array(TABLE_PREFIX, $_POST['email']), TRUE);
         
         if(count($rows_members_email) != 0){
            $valid = 'no';
            $msg->addError('EMAIL_EXISTS');
        }
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
		$_POST['real_name'] = $addslashes($_POST['real_name']);
		$_POST['email']     = $addslashes($_POST['email']);

		$sql    = "UPDATE %sadmins SET real_name='%s', email='%s', privileges=%d, last_login=last_login WHERE login='%s'";
		$result = queryDB($sql, array(TABLE_PREFIX, $_POST['real_name'], $_POST['email'], $priv, $_POST['login']));

        global $sqlout;
		write_to_log(AT_ADMIN_LOG_UPDATE, 'admins', $result, $sqlout);

		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		header('Location: index.php');
		exit;
	}
	$_POST['login']             = $stripslashes($_POST['login']);
	$_POST['real_name']         = $stripslashes($_POST['real_name']);
	$_POST['email']             = $stripslashes($_POST['email']);
} 

require(AT_INCLUDE_PATH.'header.inc.php'); 

$_GET['login'] = $addslashes($_REQUEST['login']);

$sql = "SELECT * FROM %sadmins WHERE login='%s'";
$row_admin = queryDB($sql, array(TABLE_PREFIX, $_GET['login']), TRUE);

if(count($row_admin) == 0){
	$msg->addError('USER_NOT_FOUND');
	$msg->printErrors();
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}
if (!isset($_POST['submit'])) {
	$_POST = $row_admin;
	if (query_bit($row_admin['privileges'], AT_ADMIN_PRIV_ADMIN)) {
		$_POST['priv_admin'] = 1;
	}
	$_POST['privs'] = intval($row_admin['privileges']);
}


	$module_list = $moduleFactory->getModules(AT_MODULE_STATUS_ENABLED, 0, TRUE);
	$keys = array_keys($module_list);

?>

<script type="text/javascript">
// <!--
function checkAdmin() {
	if (document.form.priv_admin.checked == true) {
		return confirm('<?php echo _AT('confirm_admin_create'); ?>');
	} else {
		return true;
	}
}
// -->
</script>

<?php 
if(!isset($_POST['hide_email'])){
    $_POST['hide_email'] = $_POST['email'];
}
$savant->assign('hide_email', $_POST['hide_email']);
$savant->assign('login', $_POST['login']);
$savant->assign('keys', $keys);
$savant->assign('module_list', $module_list);
$savant->display('admin/users/edit.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php'); ?>