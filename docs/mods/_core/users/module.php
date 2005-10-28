<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }

define('AT_ADMIN_PRIV_USERS', $this->getAdminPrivilege());

// for admin
if (admin_authenticate(AT_ADMIN_PRIV_USERS, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {

	$_module_pages[AT_NAV_ADMIN] = array('admin/users.php');

	$_module_pages['admin/users.php']['title_var'] = 'users';
	$_module_pages['admin/users.php']['parent']    = AT_NAV_ADMIN;
	$_module_pages['admin/users.php']['guide']     = 'admin/?p=3.0.users.php';
	$_module_pages['admin/users.php']['children']  = array('admin/create_user.php', 'admin/instructor_requests.php', 'admin/master_list.php', 'admin/admin_email.php', 'admin/admins/index.php');

		$_module_pages['admin/admin_email.php']['title_var'] = 'admin_email';
		$_module_pages['admin/admin_email.php']['parent']    = 'admin/users.php';
		$_module_pages['admin/admin_email.php']['guide']     = 'admin/?p=3.3.email_users.php';

		$_module_pages['admin/create_user.php']['title_var'] = 'create_user';
		$_module_pages['admin/create_user.php']['parent']    = 'admin/users.php';

		$_module_pages['admin/password_user.php']['title_var'] = 'password';
		$_module_pages['admin/password_user.php']['parent']    = 'admin/users.php';


		$_module_pages['admin/instructor_requests.php']['title_var'] = 'instructor_requests';
		$_module_pages['admin/instructor_requests.php']['parent']    = 'admin/users.php';
		$_module_pages['admin/instructor_requests.php']['guide']     = 'admin/?p=3.1.instructor_requests.php';

			$_module_pages['admin/admin_deny.php']['title_var'] = 'deny_instructor_request';
			$_module_pages['admin/admin_deny.php']['parent']    = 'admin/instructor_requests.php';

		$_module_pages['admin/master_list.php']['title_var'] = 'master_student_list';
		$_module_pages['admin/master_list.php']['parent']    = 'admin/users.php';
		$_module_pages['admin/master_list.php']['guide']     = 'admin/?p=3.2.master_student_list.php';

			$_module_pages['admin/master_list_edit.php']['title_var'] = 'edit';
			$_module_pages['admin/master_list_edit.php']['parent']    = 'admin/master_list.php';

			$_module_pages['admin/master_list_delete.php']['title_var'] = 'delete';
			$_module_pages['admin/master_list_delete.php']['parent']    = 'admin/master_list.php';

		$_module_pages['admin/edit_user.php']['title_var'] = 'edit_user';
		$_module_pages['admin/edit_user.php']['parent']    = 'admin/users.php';

		$_module_pages['admin/admin_delete.php']['title_var'] = 'delete_user';
		$_module_pages['admin/admin_delete.php']['parent']    = 'admin/users.php';

		$_module_pages['admin/admins/index.php']['title_var'] = 'administrators';
		$_module_pages['admin/admins/index.php']['parent']    = 'admin/users.php';
		$_module_pages['admin/admins/index.php']['guide']     = 'admin/?p=3.4.administrators.php';
		$_module_pages['admin/admins/index.php']['children']  = array('admin/admins/create.php', 'admin/admins/log.php');

			$_module_pages['admin/admins/log.php']['title_var'] = 'admin_log';
			$_module_pages['admin/admins/log.php']['parent']    = 'admin/admins/index.php';
			$_module_pages['admin/admins/log.php']['children']  = array('admin/admins/reset_log.php');

				$_module_pages['admin/admins/reset_log.php']['title_var'] = 'reset_log';
				$_module_pages['admin/admins/reset_log.php']['parent']    = 'admin/admins/log.php';

				$_module_pages['admin/admins/detail_log.php']['title_var'] = 'details';
				$_module_pages['admin/admins/detail_log.php']['parent']    = 'admin/admins/log.php';

			$_module_pages['admin/admins/create.php']['title_var'] = 'create_admin';
			$_module_pages['admin/admins/create.php']['parent']    = 'admin/admins/index.php';

			$_module_pages['admin/admins/edit.php']['title_var'] = 'edit_admin';
			$_module_pages['admin/admins/edit.php']['parent']    = 'admin/admins/index.php';

			$_module_pages['admin/admins/delete.php']['title_var'] = 'delete_admin';
			$_module_pages['admin/admins/delete.php']['parent']    = 'admin/admins/index.php';

}
?>