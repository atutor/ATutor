<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

define('AT_ADMIN_PRIV_USERS', $this->getAdminPrivilege());

// for admin
if (admin_authenticate(AT_ADMIN_PRIV_USERS, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {

	$this->_pages[AT_NAV_ADMIN] = array('admin/users.php');

	$this->_pages['admin/users.php']['title_var'] = 'users';
	$this->_pages['admin/users.php']['parent']    = AT_NAV_ADMIN;
	$this->_pages['admin/users.php']['guide']     = 'admin/?p=users.php';
	$this->_pages['admin/users.php']['children']  = array('admin/create_user.php', 'admin/instructor_requests.php', 'admin/master_list.php', 'admin/admin_email.php');

	if (admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
		$this->_pages['admin/users.php']['children'][]  = 'admin/admins/index.php';

		$this->_pages['admin/admins/index.php']['title_var'] = 'administrators';
		$this->_pages['admin/admins/index.php']['parent']    = 'admin/users.php';
		$this->_pages['admin/admins/index.php']['guide']     = 'admin/?p=administrators.php';
		$this->_pages['admin/admins/index.php']['children']  = array('admin/admins/create.php', 'admin/admins/log.php');

			$this->_pages['admin/admins/log.php']['title_var'] = 'admin_log';
			$this->_pages['admin/admins/log.php']['parent']    = 'admin/admins/index.php';
			$this->_pages['admin/admins/log.php']['children']  = array('admin/admins/reset_log.php');

				$this->_pages['admin/admins/reset_log.php']['title_var'] = 'reset_log';
				$this->_pages['admin/admins/reset_log.php']['parent']    = 'admin/admins/log.php';

				$this->_pages['admin/admins/detail_log.php']['title_var'] = 'details';
				$this->_pages['admin/admins/detail_log.php']['parent']    = 'admin/admins/log.php';

			$this->_pages['admin/admins/create.php']['title_var'] = 'create_admin';
			$this->_pages['admin/admins/create.php']['parent']    = 'admin/admins/index.php';

			$this->_pages['admin/admins/edit.php']['title_var'] = 'edit_admin';
			$this->_pages['admin/admins/edit.php']['parent']    = 'admin/admins/index.php';

			$this->_pages['admin/admins/password.php']['title_var'] = 'password';
			$this->_pages['admin/admins/password.php']['parent']    = 'admin/admins/index.php';

			$this->_pages['admin/admins/delete.php']['title_var'] = 'delete_admin';
			$this->_pages['admin/admins/delete.php']['parent']    = 'admin/admins/index.php';
	}

		$this->_pages['admin/admin_email.php']['title_var'] = 'admin_email';
		$this->_pages['admin/admin_email.php']['parent']    = 'admin/users.php';
		$this->_pages['admin/admin_email.php']['guide']     = 'admin/?p=email_users.php';

		$this->_pages['admin/create_user.php']['title_var'] = 'create_user';
		$this->_pages['admin/create_user.php']['parent']    = 'admin/users.php';

		$this->_pages['admin/default_preferences.php']['title_var'] = 'default_preferences';
		$this->_pages['admin/default_preferences.php']['parent']    = 'admin/config_edit.php';
		$this->_pages['admin/default_preferences.php']['guide']     = 'admin/?p=default_preferences.php';
		$this->_pages['admin/config_edit.php']['children'] = array('admin/default_preferences.php');

		$this->_pages['admin/password_user.php']['title_var'] = 'password';
		$this->_pages['admin/password_user.php']['parent']    = 'admin/users.php';

		$this->_pages['admin/instructor_requests.php']['title_var'] = 'instructor_requests';
		$this->_pages['admin/instructor_requests.php']['parent']    = 'admin/users.php';
		$this->_pages['admin/instructor_requests.php']['guide']     = 'admin/?p=instructor_requests.php';

			$this->_pages['admin/admin_deny.php']['title_var'] = 'deny_instructor_request';
			$this->_pages['admin/admin_deny.php']['parent']    = 'admin/instructor_requests.php';

		$this->_pages['admin/master_list.php']['title_var'] = 'master_student_list';
		$this->_pages['admin/master_list.php']['parent']    = 'admin/users.php';
		$this->_pages['admin/master_list.php']['guide']     = 'admin/?p=master_student_list.php';

			$this->_pages['admin/master_list_edit.php']['title_var'] = 'edit';
			$this->_pages['admin/master_list_edit.php']['parent']    = 'admin/master_list.php';

			$this->_pages['admin/master_list_delete.php']['title_var'] = 'delete';
			$this->_pages['admin/master_list_delete.php']['parent']    = 'admin/master_list.php';

		$this->_pages['admin/edit_user.php']['title_var'] = 'edit_user';
		$this->_pages['admin/edit_user.php']['parent']    = 'admin/users.php';

	$this->_pages['admin/admin_delete.php']['title_var'] = 'delete_user';
	$this->_pages['admin/admin_delete.php']['parent']    = 'admin/users.php';

	$this->_pages['admin/user_status.php']['title_var'] = 'status';
	$this->_pages['admin/user_status.php']['parent']    = 'admin/users.php';

}
?>