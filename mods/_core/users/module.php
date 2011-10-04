<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

define('AT_ADMIN_PRIV_USERS', $this->getAdminPrivilege());

// for admin
if (admin_authenticate(AT_ADMIN_PRIV_USERS, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {

	$this->_pages[AT_NAV_ADMIN] = array('mods/_core/users/users.php');

	$this->_pages['mods/_core/users/users.php']['title_var'] = 'users';
	$this->_pages['mods/_core/users/users.php']['parent']    = AT_NAV_ADMIN;
	$this->_pages['mods/_core/users/users.php']['guide']     = 'admin/?p=users.php';
	$this->_pages['mods/_core/users/users.php']['children']  = array('mods/_core/users/create_user.php', 'mods/_core/users/instructor_requests.php', 'mods/_core/users/master_list.php', 'mods/_core/users/admin_email.php');

	if (admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
		$this->_pages['mods/_core/users/users.php']['children'][]  = 'mods/_core/users/admins/index.php';

		$this->_pages['mods/_core/users/admins/index.php']['title_var'] = 'administrators';
		$this->_pages['mods/_core/users/admins/index.php']['parent']    = 'mods/_core/users/users.php';
		$this->_pages['mods/_core/users/admins/index.php']['guide']     = 'admin/?p=administrators.php';
		$this->_pages['mods/_core/users/admins/index.php']['children']  = array('mods/_core/users/admins/create.php', 'mods/_core/users/admins/log.php');

		$this->_pages['mods/_core/users/admins/log.php']['title_var'] = 'admin_log';
		$this->_pages['mods/_core/users/admins/log.php']['parent']    = 'mods/_core/users/admins/index.php';
		$this->_pages['mods/_core/users/admins/log.php']['children']  = array('mods/_core/users/admins/reset_log.php');

		$this->_pages['mods/_core/users/admins/reset_log.php']['title_var'] = 'reset_log';
		$this->_pages['mods/_core/users/admins/reset_log.php']['parent']    = 'mods/_core/users/admins/log.php';

		$this->_pages['mods/_core/users/admins/detail_log.php']['title_var'] = 'details';
		$this->_pages['mods/_core/users/admins/detail_log.php']['parent']    = 'mods/_core/users/admins/log.php';

		$this->_pages['mods/_core/users/admins/create.php']['title_var'] = 'create_admin';
		$this->_pages['mods/_core/users/admins/create.php']['parent']    = 'mods/_core/users/admins/index.php';

		$this->_pages['mods/_core/users/admins/edit.php']['title_var'] = 'edit_admin';
		$this->_pages['mods/_core/users/admins/edit.php']['parent']    = 'mods/_core/users/admins/index.php';

		$this->_pages['mods/_core/users/admins/password.php']['title_var'] = 'password';
		$this->_pages['mods/_core/users/admins/password.php']['parent']    = 'mods/_core/users/admins/index.php';

		$this->_pages['mods/_core/users/admins/delete.php']['title_var'] = 'delete_admin';
		$this->_pages['mods/_core/users/admins/delete.php']['parent']    = 'mods/_core/users/admins/index.php';
	}

		$this->_pages['mods/_core/users/admin_email.php']['title_var'] = 'admin_email';
		$this->_pages['mods/_core/users/admin_email.php']['parent']    = 'mods/_core/users/users.php';
		$this->_pages['mods/_core/users/admin_email.php']['guide']     = 'admin/?p=email_users.php';

		$this->_pages['mods/_core/users/create_user.php']['title_var'] = 'create_user';
		$this->_pages['mods/_core/users/create_user.php']['parent']    = 'mods/_core/users/users.php';

		$this->_pages['mods/_core/users/default_preferences.php']['title_var'] = 'default_preferences';
		$this->_pages['mods/_core/users/default_preferences.php']['parent']    = 'admin/config_edit.php';
		$this->_pages['mods/_core/users/default_preferences.php']['guide']     = 'admin/?p=default_preferences.php';
		$this->_pages['admin/config_edit.php']['children'] = array('mods/_core/users/default_preferences.php');

		$this->_pages['mods/_core/users/password_user.php']['title_var'] = 'password';
		$this->_pages['mods/_core/users/password_user.php']['parent']    = 'mods/_core/users/users.php';

		$this->_pages['mods/_core/users/instructor_requests.php']['title_var'] = 'instructor_requests';
		$this->_pages['mods/_core/users/instructor_requests.php']['parent']    = 'mods/_core/users/users.php';
		$this->_pages['mods/_core/users/instructor_requests.php']['guide']     = 'admin/?p=instructor_requests.php';

			$this->_pages['mods/_core/users/admin_deny.php']['title_var'] = 'deny_instructor_request';
			$this->_pages['mods/_core/users/admin_deny.php']['parent']    = 'mods/_core/users/instructor_requests.php';

		$this->_pages['mods/_core/users/master_list.php']['title_var'] = 'master_student_list';
		$this->_pages['mods/_core/users/master_list.php']['parent']    = 'mods/_core/users/users.php';
		$this->_pages['mods/_core/users/master_list.php']['guide']     = 'admin/?p=master_student_list.php';

			$this->_pages['mods/_core/users/master_list_edit.php']['title_var'] = 'edit';
			$this->_pages['mods/_core/users/master_list_edit.php']['parent']    = 'mods/_core/users/master_list.php';

			$this->_pages['mods/_core/users/master_list_delete.php']['title_var'] = 'delete';
			$this->_pages['mods/_core/users/master_list_delete.php']['parent']    = 'mods/_core/users/master_list.php';

		$this->_pages['mods/_core/users/edit_user.php']['title_var'] = 'edit_user';
		$this->_pages['mods/_core/users/edit_user.php']['parent']    = 'mods/_core/users/users.php';

	$this->_pages['mods/_core/users/admin_delete.php']['title_var'] = 'delete_user';
	$this->_pages['mods/_core/users/admin_delete.php']['parent']    = 'mods/_core/users/users.php';

	$this->_pages['mods/_core/users/user_status.php']['title_var'] = 'status';
	$this->_pages['mods/_core/users/user_status.php']['parent']    = 'mods/_core/users/users.php';

}
?>