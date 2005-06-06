<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

if (!defined('AT_INCLUDE_PATH')) { exit; }

define('AT_NAV_PUBLIC', 1);
define('AT_NAV_START',  2);
define('AT_NAV_COURSE', 3);
define('AT_NAV_HOME',   4);
define('AT_NAV_ADMIN',  5);

/*
	5 sections: public, my_start_page, course, admin, home
*/

$_pages[AT_NAV_ADMIN]  = array('admin/index.php',  'admin/users.php',   'admin/courses.php');
$_pages[AT_NAV_PUBLIC] = array('login.php', 'registration.php', 'browse.php', 'password_reminder.php');
$_pages[AT_NAV_START]  = array('users/index.php',  'users/profile.php', 'users/preferences.php');
$_pages[AT_NAV_COURSE] = array('index.php');
$_pages[AT_NAV_HOME]   = array();

if ($_SESSION['course_id']) {
	$main_links = $home_links = $side_menu = array();

	if ($system_courses[$_SESSION['course_id']]['main_links']) {
			$main_links = explode('|', $system_courses[$_SESSION['course_id']]['main_links']);
			$_pages[AT_NAV_COURSE] = array_merge($_pages[AT_NAV_COURSE], $main_links);
	}

	if ($system_courses[$_SESSION['course_id']]['home_links']) {
		$home_links = explode('|', $system_courses[$_SESSION['course_id']]['home_links']);
		$_pages[AT_NAV_HOME] = array_merge($_pages[AT_NAV_HOME], $home_links);
	}

//	debug($_pages[AT_NAV_HOME]);

	if (authenticate(AT_PRIV_ADMIN, AT_PRIV_RETURN) || $_SESSION['privileges']) {
		$_pages[AT_NAV_COURSE][] = 'tools/index.php';
	}
}

/* admin pages */

$_pages['admin/index.php']['title_var'] = 'configuration';
$_pages['admin/index.php']['parent']    = AT_NAV_ADMIN;
$_pages['admin/index.php']['guide']     = 'admin/?p=2.0.configuration.php';
$_pages['admin/index.php']['children']  = array('admin/admins/my_edit.php', 'admin/config_edit.php', 'admin/language.php', 'admin/themes/index.php', 'admin/error_logging.php');

	$_pages['admin/admins/my_edit.php']['title_var'] = 'my_account';
	$_pages['admin/admins/my_edit.php']['parent']    = 'admin/index.php';
	$_pages['admin/admins/my_edit.php']['guide']     = 'admin/?p=2.1.my_account.php';

	$_pages['admin/config_edit.php']['title_var'] = 'system_preferences';
	$_pages['admin/config_edit.php']['parent']    = 'admin/index.php';
	$_pages['admin/config_edit.php']['guide']     = 'admin/?p=2.2.system_preferences.php';

	$_pages['admin/fix_content.php']['title_var'] = 'fix_content_ordering';
	$_pages['admin/fix_content.php']['parent']    = 'admin/index.php';

	$_pages['admin/language.php']['title_var'] = 'languages';
	$_pages['admin/language.php']['parent']    = 'admin/index.php';
	$_pages['admin/language.php']['guide']     = 'admin/?p=2.3.languages.php';

		$_pages['admin/language_add.php']['title_var'] = 'add_language';
		$_pages['admin/language_add.php']['parent']    = 'admin/language.php';

		$_pages['admin/language_edit.php']['title_var'] = 'edit_language';
		$_pages['admin/language_edit.php']['parent']    = 'admin/language.php';

		$_pages['admin/language_delete.php']['title_var'] = 'delete_language';
		$_pages['admin/language_delete.php']['parent']    = 'admin/language.php';

	$_pages['admin/themes/index.php']['title_var'] = 'themes';
	$_pages['admin/themes/index.php']['parent']    = 'admin/index.php';
	$_pages['admin/themes/index.php']['guide']     = 'admin/?p=2.4.themes.php';

	$_pages['admin/themes/delete.php']['title_var'] = 'delete';
	$_pages['admin/themes/delete.php']['parent']    = 'admin/themes/index.php';

	$_pages['admin/error_logging.php']['title_var'] = 'error_logging';
	$_pages['admin/error_logging.php']['parent']    = 'admin/index.php';
	$_pages['admin/error_logging.php']['guide']     = 'admin/?p=2.5.error_logging.php';
	$_pages['admin/error_logging.php']['children']  = array('admin/error_logging_bundle.php', 'admin/error_logging_reset.php');

	$_pages['admin/error_logging_reset.php']['title_var'] = 'reset_log';
	$_pages['admin/error_logging_reset.php']['parent']    = 'admin/error_logging.php';

	$_pages['admin/error_logging_bundle.php']['title_var'] = 'report_errors';
	$_pages['admin/error_logging_bundle.php']['parent']    = 'admin/error_logging.php';

	$_pages['admin/error_logging_details.php']['title_var'] = 'viewing_profile_bugs';
	$_pages['admin/error_logging_details.php']['parent']    = 'admin/error_logging.php';

	$_pages['admin/error_logging_view.php']['title_var'] = 'viewing_errors';
	$_pages['admin/error_logging_view.php']['parent']    = 'admin/error_logging_details.php';

$_pages['admin/users.php']['title_var'] = 'users';
$_pages['admin/users.php']['parent']    = AT_NAV_ADMIN;
$_pages['admin/users.php']['guide']     = 'admin/?p=3.0.users.php';
$_pages['admin/users.php']['children']  = array('admin/create_user.php', 'admin/instructor_requests.php', 'admin/master_list.php', 'admin/admin_email.php', 'admin/admins/index.php');

	$_pages['admin/admin_email.php']['title_var'] = 'admin_email';
	$_pages['admin/admin_email.php']['parent']    = 'admin/users.php';
	$_pages['admin/admin_email.php']['guide']     = 'admin/?p=3.3.email_users.php';

	$_pages['admin/create_user.php']['title_var'] = 'create_user';
	$_pages['admin/create_user.php']['parent']    = 'admin/users.php';

	$_pages['admin/instructor_requests.php']['title_var'] = 'instructor_requests';
	$_pages['admin/instructor_requests.php']['parent']    = 'admin/users.php';
	$_pages['admin/instructor_requests.php']['guide']     = 'admin/?p=3.1.instructor_requests.php';

	$_pages['admin/master_list.php']['title_var'] = 'master_student_list';
	$_pages['admin/master_list.php']['parent']    = 'admin/users.php';
	$_pages['admin/master_list.php']['guide']     = 'admin/?p=3.2.master_student_list.php';

		$_pages['admin/master_list_edit.php']['title_var'] = 'edit';
		$_pages['admin/master_list_edit.php']['parent']    = 'admin/master_list.php';

		$_pages['admin/master_list_delete.php']['title_var'] = 'delete';
		$_pages['admin/master_list_delete.php']['parent']    = 'admin/master_list.php';

	$_pages['admin/edit_user.php']['title_var'] = 'edit_user';
	$_pages['admin/edit_user.php']['parent']    = 'admin/users.php';

	$_pages['admin/admin_delete.php']['title_var'] = 'delete_user';
	$_pages['admin/admin_delete.php']['parent']    = 'admin/users.php';

	$_pages['admin/admins/index.php']['title_var'] = 'administrators';
	$_pages['admin/admins/index.php']['parent']    = 'admin/users.php';
	$_pages['admin/admins/index.php']['guide']     = 'admin/?p=3.4.administrators.php';
	$_pages['admin/admins/index.php']['children']  = array('admin/admins/create.php', 'admin/admins/log.php');

		$_pages['admin/admins/log.php']['title_var'] = 'admin_log';
		$_pages['admin/admins/log.php']['parent']    = 'admin/admins/index.php';
		$_pages['admin/admins/log.php']['children']  = array('admin/admins/reset_log.php');

			$_pages['admin/admins/reset_log.php']['title_var'] = 'reset_log';
			$_pages['admin/admins/reset_log.php']['parent']    = 'admin/admins/log.php';

			$_pages['admin/admins/detail_log.php']['title_var'] = 'details';
			$_pages['admin/admins/detail_log.php']['parent']    = 'admin/admins/log.php';

		$_pages['admin/admins/create.php']['title_var'] = 'create_admin';
		$_pages['admin/admins/create.php']['parent']    = 'admin/admins/index.php';

		$_pages['admin/admins/edit.php']['title_var'] = 'edit_admin';
		$_pages['admin/admins/edit.php']['parent']    = 'admin/admins/index.php';

		$_pages['admin/admins/delete.php']['title_var'] = 'delete_admin';
		$_pages['admin/admins/delete.php']['parent']    = 'admin/admins/index.php';


$_pages['admin/courses.php']['title_var'] = 'courses';
$_pages['admin/courses.php']['parent']    = AT_NAV_ADMIN;
$_pages['admin/courses.php']['guide']     = 'admin/?p=4.0.courses.php';
$_pages['admin/courses.php']['children']  = array('admin/create_course.php', 'admin/backup/index.php', 'admin/forums.php', 'admin/course_categories.php');

	$_pages['admin/delete_course.php']['title_var'] = 'delete_course';
	$_pages['admin/delete_course.php']['parent']    = 'admin/courses.php';

	$_pages['admin/instructor_login.php']['title_var'] = 'view';
	$_pages['admin/instructor_login.php']['parent']    = 'admin/courses.php';

	$_pages['admin/edit_course.php']['title_var'] = 'course_properties';
	$_pages['admin/edit_course.php']['parent']    = 'admin/courses.php';

	$_pages['admin/create_course.php']['title_var'] = 'create_course';
	$_pages['admin/create_course.php']['parent']    = 'admin/courses.php';

	$_pages['admin/backup/index.php']['title_var'] = 'backups';
	$_pages['admin/backup/index.php']['parent']    = 'admin/courses.php';
	$_pages['admin/backup/index.php']['guide']     = 'admin/?p=4.1.backups.php';
	$_pages['admin/backup/index.php']['children']  = array('admin/backup/create.php');

		$_pages['admin/backup/create.php']['title_var'] = 'create_backup';
		$_pages['admin/backup/create.php']['parent']    = 'admin/backup/index.php';
		$_pages['admin/backup/create.php']['guide']     = 'admin/?p=4.1.backups.php';
	
		// this item is a bit iffy:
		$_pages['admin/backup/restore.php']['title_var'] = 'restore';
		$_pages['admin/backup/restore.php']['parent']    = 'admin/backup/index.php';
		$_pages['admin/backup/restore.php']['guide']     = 'admin/?p=4.1.backups.php';

		$_pages['admin/backup/delete.php']['title_var'] = 'delete';
		$_pages['admin/backup/delete.php']['parent']    = 'admin/backup/index.php';

		$_pages['admin/backup/edit.php']['title_var'] = 'edit';
		$_pages['admin/backup/edit.php']['parent']    = 'admin/backup/index.php';


	$_pages['admin/forums.php']['title_var'] = 'forums';
	$_pages['admin/forums.php']['parent']    = 'admin/courses.php';
	$_pages['admin/forums.php']['guide']     = 'admin/?p=4.2.forums.php';
	$_pages['admin/forums.php']['children']  = array('admin/forum_add.php');

		$_pages['admin/forum_add.php']['title_var'] = 'create_forum';
		$_pages['admin/forum_add.php']['parent']    = 'admin/forums.php';

		$_pages['admin/forum_edit.php']['title_var'] = 'edit_forum';
		$_pages['admin/forum_edit.php']['parent']    = 'admin/forums.php';

		$_pages['admin/forum_delete.php']['title_var'] = 'delete_forum';
		$_pages['admin/forum_delete.php']['parent']    = 'admin/forums.php';

	$_pages['admin/course_categories.php']['title_var'] = 'cats_categories';
	$_pages['admin/course_categories.php']['parent']    = 'admin/courses.php';
	$_pages['admin/course_categories.php']['guide']     = 'admin/?p=4.3.categories.php';
	$_pages['admin/course_categories.php']['children']  = array('admin/create_category.php');

		$_pages['admin/create_category.php']['title_var'] = 'create_category';
		$_pages['admin/create_category.php']['parent']    = 'admin/course_categories.php';

		$_pages['admin/edit_category.php']['title_var'] = 'edit_category';
		$_pages['admin/edit_category.php']['parent']    = 'admin/course_categories.php';

		$_pages['admin/delete_category.php']['title_var'] = 'delete_category';
		$_pages['admin/delete_category.php']['parent']    = 'admin/course_categories.php';

/* public pages */
$_pages['registration.php']['title_var'] = 'register';
$_pages['registration.php']['parent']    = AT_NAV_PUBLIC;

$_pages['browse.php']['title_var'] = 'browse_courses';
$_pages['browse.php']['parent']    = AT_NAV_PUBLIC;

$_pages['login.php']['title_var'] = 'login';
$_pages['login.php']['parent']    = AT_NAV_PUBLIC;

$_pages['confirm.php']['title_var'] = 'confirm';
$_pages['confirm.php']['parent']    = AT_NAV_PUBLIC;

$_pages['password_reminder.php']['title_var'] = 'password_reminder';
$_pages['password_reminder.php']['parent']    = AT_NAV_PUBLIC;

$_pages['logout.php']['title_var'] = 'logout';
$_pages['logout.php']['parent']    = AT_NAV_PUBLIC;

/* my start page pages */
$_pages['users/index.php']['title_var'] = 'my_courses';
$_pages['users/index.php']['parent']    = AT_NAV_START;
$_pages['users/index.php']['children']  = array('users/browse.php', 'users/create_course.php');
	
	$_pages['users/browse.php']['title_var'] = 'browse_courses';
	$_pages['users/browse.php']['parent']    = 'users/index.php';
	
	$_pages['users/create_course.php']['title_var'] = 'create_course';
	$_pages['users/create_course.php']['parent']    = 'users/index.php';

	$_pages['users/private_enroll.php']['title_var'] = 'enroll';
	$_pages['users/private_enroll.php']['parent']    = 'users/index.php';

	$_pages['users/remove_course.php']['title_var'] = 'unenroll';
	$_pages['users/remove_course.php']['parent']    = 'users/index.php';

$_pages['users/profile.php']['title_var']    = 'profile';
$_pages['users/profile.php']['parent']   = AT_NAV_START;
	
$_pages['users/preferences.php']['title_var']  = 'preferences';
$_pages['users/preferences.php']['parent'] = AT_NAV_START;


/* course pages */
$_pages['index.php']['title_var']  = 'home';
$_pages['index.php']['parent'] = AT_NAV_COURSE;

$_pages['enroll.php']['title_var']  = 'enroll';
$_pages['enroll.php']['parent'] = AT_NAV_COURSE;

/* instructor pages: */
$_pages['tools/index.php']['title_var']    = 'manage';
$_pages['tools/index.php']['parent']   = AT_NAV_COURSE;

	$_pages['tools/polls/index.php']['title_var']  = 'polls';
	$_pages['tools/polls/index.php']['parent'] = 'tools/index.php';
	$_pages['tools/polls/index.php']['children'] = array('tools/polls/add.php');

		$_pages['tools/polls/add.php']['title_var']  = 'add_poll';
		$_pages['tools/polls/add.php']['parent'] = 'tools/polls/index.php';

		$_pages['tools/polls/edit.php']['title_var']  = 'edit_poll';
		$_pages['tools/polls/edit.php']['parent'] = 'tools/polls/index.php';

		$_pages['tools/polls/delete.php']['title_var']  = 'delete_poll';
		$_pages['tools/polls/delete.php']['parent'] = 'tools/polls/index.php';

	$_pages['tools/filemanager/index.php']['title_var'] = 'file_manager';
	$_pages['tools/filemanager/index.php']['parent']    = 'tools/index.php';
	$_pages['tools/filemanager/index.php']['guide']     = 'instructor/?p=7.0.file_manager.php';
	$_pages['tools/filemanager/index.php']['children'] = array('tools/filemanager/new.php');

		$_pages['tools/filemanager/new.php']['title_var']  = 'create_new_file';
		$_pages['tools/filemanager/new.php']['parent'] = 'tools/filemanager/index.php';

		$_pages['tools/filemanager/zip.php']['title_var']  = 'zip_file_manager';
		$_pages['tools/filemanager/zip.php']['parent'] = 'tools/filemanager/index.php';

		$_pages['tools/filemanager/rename.php']['title_var']  = 'rename';
		$_pages['tools/filemanager/rename.php']['parent'] = 'tools/filemanager/index.php';

		$_pages['tools/filemanager/move.php']['title_var']  = 'move';
		$_pages['tools/filemanager/move.php']['parent'] = 'tools/filemanager/index.php';

		$_pages['tools/filemanager/edit.php']['title_var']  = 'edit';
		$_pages['tools/filemanager/edit.php']['parent'] = 'tools/filemanager/index.php';

		$_pages['tools/filemanager/delete.php']['title_var']  = 'delete';
		$_pages['tools/filemanager/delete.php']['parent'] = 'tools/filemanager/index.php';

	$_pages['tools/course_stats.php']['title_var']  = 'statistics';
	$_pages['tools/course_stats.php']['parent'] = 'tools/index.php';

	$_pages['tools/course_properties.php']['title_var']    = 'properties';
	$_pages['tools/course_properties.php']['parent']   = 'tools/index.php';
	$_pages['tools/course_properties.php']['children'] = array('tools/delete_course.php');

		$_pages['tools/delete_course.php']['title_var']  = 'delete_course';
		$_pages['tools/delete_course.php']['parent'] = 'tools/course_properties.php';

	$_pages['tools/modules.php']['title_var']  = 'student_tools';
	$_pages['tools/modules.php']['parent'] = 'tools/index.php';
	$_pages['tools/modules.php']['children'] = array('tools/side_menu.php');

		$_pages['tools/side_menu.php']['title_var']  = 'side_menu';
		$_pages['tools/side_menu.php']['parent'] = 'tools/modules.php';

	$_pages['tools/course_email.php']['title_var']  = 'course_email';
	$_pages['tools/course_email.php']['parent'] = 'tools/index.php';
	$_pages['tools/course_email.php']['guide']     = 'instructor/?p=5.0.course_email.php';

	$_pages['tools/content/index.php']['title_var'] = 'content';
	$_pages['tools/content/index.php']['parent']    = 'tools/index.php';
	$_pages['tools/content/index.php']['guide']     = 'instructor/?p=4.0.content.php';
	$_pages['tools/content/index.php']['children'] = array('editor/add_content.php', 'tools/ims/index.php', 'tools/tracker/index.php', 'tools/tile/index.php');

		$_pages['editor/add_content.php']['title_var']    = 'add_content';
		$_pages['editor/add_content.php']['parent']   = 'tools/content/index.php';
		$_pages['editor/add_content.php']['guide']     = 'instructor/?p=4.1.creating_editing_content.php';

		$_pages['editor/edit_content.php']['title_var']  = 'edit_content';
		$_pages['editor/edit_content.php']['parent'] = 'tools/content/index.php';
		$_pages['editor/edit_content.php']['guide']     = 'instructor/?p=4.1.creating_editing_content.php';

		$_pages['editor/delete_content.php']['title_var']    = 'delete_content';
		$_pages['editor/delete_content.php']['parent']   = 'tools/content/index.php';

		$_pages['tools/tracker/index.php']['title_var']  = 'content_usage';
		$_pages['tools/tracker/index.php']['parent'] = 'tools/content/index.php';
		$_pages['tools/tracker/index.php']['children'] = array('tools/tracker/student_usage.php', 'tools/tracker/reset.php');
		$_pages['tools/tracker/index.php']['guide']     = 'instructor/?p=4.3.content_usage.php';		

			$_pages['tools/tracker/student_usage.php']['title_var']  = 'member_stats';
			$_pages['tools/tracker/student_usage.php']['parent'] = 'tools/tracker/index.php';

			//$_pages['tools/tracker/page_student_stats.php']['title_var']  = _AT('page_student_stats');
			//$_pages['tools/tracker/page_student_stats.php']['parent'] = 'tools/tracker/index.php';

			$_pages['tools/tracker/reset.php']['title_var']  = 'reset';
			$_pages['tools/tracker/reset.php']['parent'] = 'tools/tracker/index.php';

		$_pages['tools/ims/index.php']['title_var']    = 'content_packaging';
		$_pages['tools/ims/index.php']['parent']   = 'tools/content/index.php';
		$_pages['tools/ims/index.php']['guide'] = 'instructor/?p=4.2.content_packages.php';

		$_pages['tools/tile/index.php']['title_var']  = 'tile_search';
		$_pages['tools/tile/index.php']['parent'] = 'tools/content/index.php';
		$_pages['tools/tile/index.php']['guide'] = 'instructor/?p=4.4.tile_repository.php';

			$_pages['tools/tile/import.php']['title_var']    = 'import_content_package';
			$_pages['tools/tile/import.php']['parent']   = 'tools/tile/index.php';

	$_pages['tools/enrollment/index.php']['title_var'] = 'enrolment';
	$_pages['tools/enrollment/index.php']['parent']    = 'tools/index.php';
	$_pages['tools/enrollment/index.php']['guide']     = 'instructor/?p=6.0.enrollment.php';
	$_pages['tools/enrollment/index.php']['children'] = array('tools/enrollment/export_course_list.php', 'tools/enrollment/import_course_list.php', 'tools/enrollment/create_course_list.php', 'tools/enrollment/groups.php');

		$_pages['tools/enrollment/export_course_list.php']['title_var']    = 'list_export_course_list';
		$_pages['tools/enrollment/export_course_list.php']['parent']   = 'tools/enrollment/index.php';

		$_pages['tools/enrollment/import_course_list.php']['title_var']    = 'list_import_course_list';
		$_pages['tools/enrollment/import_course_list.php']['parent']   = 'tools/enrollment/index.php';

		$_pages['tools/enrollment/create_course_list.php']['title_var']    = 'list_create_course_list';
		$_pages['tools/enrollment/create_course_list.php']['parent']   = 'tools/enrollment/index.php';

		$_pages['tools/enrollment/verify_list.php']['title_var']  = 'course_list';
		$_pages['tools/enrollment/verify_list.php']['parent'] = 'tools/enrollment/index.php';

		$_pages['tools/enrollment/groups.php']['title_var']    = 'groups';
		$_pages['tools/enrollment/groups.php']['parent']   = 'tools/enrollment/index.php';
		$_pages['tools/enrollment/groups.php']['children'] = array('tools/enrollment/groups_manage.php');

			$_pages['tools/enrollment/groups_manage.php']['title_var']  = 'create_group';
			$_pages['tools/enrollment/groups_manage.php']['parent'] = 'tools/enrollment/groups.php';

		$_pages['tools/enrollment/privileges.php']['title_var']  = 'roles_privileges';
		$_pages['tools/enrollment/privileges.php']['parent'] = 'tools/enrollment/index.php';
		$_pages['tools/enrollment/privileges.php']['guide']     = 'instructor/?p=6.1.roles_privileges.php';
		
		$_pages['tools/enrollment/enroll_edit.php']['title_var']    = 'edit';
		$_pages['tools/enrollment/enroll_edit.php']['parent']   = 'tools/enrollment/index.php';

	$_pages['tools/course_email.php']['title_var']  = 'course_email';
	$_pages['tools/course_email.php']['parent'] = 'tools/index.php';

	$_pages['tools/backup/index.php']['title_var'] = 'backups';
	$_pages['tools/backup/index.php']['parent']    = 'tools/index.php';
	$_pages['tools/backup/index.php']['guide']     = 'instructor/?p=2.0.backups.php';
	$_pages['tools/backup/index.php']['children'] = array('tools/backup/create.php', 'tools/backup/upload.php');

		$_pages['tools/backup/create.php']['title_var']  = 'create';
		$_pages['tools/backup/create.php']['parent'] = 'tools/backup/index.php';
		$_pages['tools/backup/create.php']['guide'] = 'instructor/?p=2.1.creating_restoring.php';

		$_pages['tools/backup/upload.php']['title_var']  = 'upload';
		$_pages['tools/backup/upload.php']['parent'] = 'tools/backup/index.php';
		$_pages['tools/backup/upload.php']['guide'] = 'instructor/?p=2.2.downloading_uploading.php';

		$_pages['tools/backup/restore.php']['title_var']  = 'restore';
		$_pages['tools/backup/restore.php']['parent'] = 'tools/backup/index.php';
		$_pages['tools/backup/restore.php']['guide'] = 'instructor/?p=2.1.creating_restoring.php';

		$_pages['tools/backup/edit.php']['title_var']  = 'edit';
		$_pages['tools/backup/edit.php']['parent'] = 'tools/backup/index.php';
		$_pages['tools/backup/edit.php']['guide'] = 'instructor/?p=2.3.editing_deleting.php';

		$_pages['tools/backup/delete.php']['title_var']  = 'delete';
		$_pages['tools/backup/delete.php']['parent'] = 'tools/backup/index.php';				$_pages['tools/backup/delete.php']['guide'] = 'instructor/?p=2.3.editing_deleting.php';

	$_pages['tools/news/index.php']['title_var']  = 'announcements';
	$_pages['tools/news/index.php']['guide']     = 'instructor/?p=1.0.announcements.php';
	$_pages['tools/news/index.php']['parent'] = 'tools/index.php';
	$_pages['tools/news/index.php']['children'] = array('editor/add_news.php');

		$_pages['editor/add_news.php']['title_var']  = 'add_announcement';
		$_pages['editor/add_news.php']['parent'] = 'tools/news/index.php';

		$_pages['editor/edit_news.php']['title_var']  = 'edit_announcement';
		$_pages['editor/edit_news.php']['parent'] = 'tools/news/index.php';

		$_pages['editor/delete_news.php']['title_var']  = 'delete_announcement';
		$_pages['editor/delete_news.php']['parent'] = 'tools/news/index.php';

	$_pages['tools/glossary/index.php']['title_var']  = 'glossary';
	$_pages['tools/glossary/index.php']['parent'] = 'tools/index.php';
	$_pages['tools/glossary/index.php']['children'] = array('tools/glossary/add.php');

		$_pages['tools/glossary/add.php']['title_var']  = 'add_glossary';
		$_pages['tools/glossary/add.php']['parent'] = 'tools/glossary/index.php';

		$_pages['tools/glossary/edit.php']['title_var']  = 'edit_glossary';
		$_pages['tools/glossary/edit.php']['parent'] = 'tools/glossary/index.php';

		$_pages['tools/glossary/delete.php']['title_var']  = 'delete_glossary';
		$_pages['tools/glossary/delete.php']['parent'] = 'tools/glossary/index.php';

	$_pages['tools/forums/index.php']['title_var'] = 'forums';
	$_pages['tools/forums/index.php']['parent']    = 'tools/index.php';
	$_pages['tools/forums/index.php']['guide']     = 'instructor/?p=3.0.forums.php';
	$_pages['tools/forums/index.php']['children']  = array('editor/add_forum.php');

		$_pages['editor/add_forum.php']['title_var']  = 'create_forum';
		$_pages['editor/add_forum.php']['parent'] = 'tools/forums/index.php';

		$_pages['editor/delete_forum.php']['title_var']  = 'delete_forum';
		$_pages['editor/delete_forum.php']['parent'] = 'tools/forums/index.php';

		$_pages['editor/edit_forum.php']['title_var']  = 'edit_forum';
		$_pages['editor/edit_forum.php']['parent'] = 'tools/forums/index.php';

	$_pages['tools/links/index.php']['title_var']  = 'links';
	$_pages['tools/links/index.php']['parent'] = 'tools/index.php';
	$_pages['tools/links/index.php']['children'] = array('tools/links/add.php', 'tools/links/categories.php', 'tools/links/categories_create.php');

		$_pages['tools/links/add.php']['title_var']  = 'add_link';
		$_pages['tools/links/add.php']['parent'] = 'tools/links/index.php';

		$_pages['tools/links/edit.php']['title_var']  = 'edit_link';
		$_pages['tools/links/edit.php']['parent'] = 'tools/links/index.php';

		$_pages['tools/links/delete.php']['title_var']  = 'delete_link';
		$_pages['tools/links/delete.php']['parent'] = 'tools/links/index.php';

		$_pages['tools/links/categories.php']['title_var']  = 'categories';
		$_pages['tools/links/categories.php']['parent'] = 'tools/links/index.php';

		$_pages['tools/links/categories_create.php']['title_var']  = 'create_category';
		$_pages['tools/links/categories_create.php']['parent'] = 'tools/links/index.php';

		$_pages['tools/links/categories_edit.php']['title_var']  = 'edit_category';
		$_pages['tools/links/categories_edit.php']['parent'] = 'tools/links/categories.php';

		$_pages['tools/links/categories_delete.php']['title_var']  = 'delete_category';
		$_pages['tools/links/categories_delete.php']['parent'] = 'tools/links/categories.php';


	// tests
	$_pages['tools/tests/index.php']['title_var'] = 'tests';
	$_pages['tools/tests/index.php']['parent']    = 'tools/index.php';
	$_pages['tools/tests/index.php']['guide']     = 'instructor/?p=7.0.tests_surveys.php';
	$_pages['tools/tests/index.php']['children']  = array('tools/tests/create_test.php', 'tools/tests/question_db.php', 'tools/tests/question_cats.php');

	$_pages['tools/tests/create_test.php']['title_var']  = 'create_test';
	$_pages['tools/tests/create_test.php']['parent'] = 'tools/tests/index.php';

	$_pages['tools/tests/question_db.php']['title_var']  = 'question_database';
	$_pages['tools/tests/question_db.php']['parent'] = 'tools/tests/index.php';

		$_pages['tools/tests/create_question_multi.php']['title_var']  = 'create_question_multi';
		$_pages['tools/tests/create_question_multi.php']['parent'] = 'tools/tests/question_db.php';

	$_pages['tools/tests/question_cats.php']['title_var']  = 'question_categories';
	$_pages['tools/tests/question_cats.php']['parent'] = 'tools/tests/index.php';
	$_pages['tools/tests/question_cats.php']['children'] = array('tools/tests/question_cats_manage.php');

	$_pages['tools/tests/question_cats_manage.php']['title_var']  = 'create_category';
	$_pages['tools/tests/question_cats_manage.php']['parent'] = 'tools/tests/question_cats.php';

	$_pages['tools/tests/question_cats_delete.php']['title_var']  = 'delete_category';
	$_pages['tools/tests/question_cats_delete.php']['parent'] = 'tools/tests/question_cats.php';

	$_pages['tools/tests/edit_test.php']['title_var']  = 'edit_test';
	$_pages['tools/tests/edit_test.php']['parent'] = 'tools/tests/index.php';

	$_pages['tools/tests/preview.php']['title_var']  = 'preview';
	$_pages['tools/tests/preview.php']['parent'] = 'tools/tests/index.php';

	$_pages['tools/tests/preview_question.php']['title_var']  = 'preview';
	$_pages['tools/tests/preview_question.php']['parent'] = 'tools/tests/question_db.php';

	$_pages['tools/tests/results.php']['title_var']  = 'submissions';
	$_pages['tools/tests/results.php']['parent'] = 'tools/tests/index.php';

	//$_pages['tools/tests/results_all_quest.php']['title_var']  = _AT('statistics');
	//$_pages['tools/tests/results_all_quest.php']['parent'] = 'tools/tests/index.php';

	$_pages['tools/tests/delete_test.php']['title_var']  = 'delete_test';
	$_pages['tools/tests/delete_test.php']['parent'] = 'tools/tests/index.php';

	$_pages['tools/view_results.php']['title_var']  = 'view_results';
	$_pages['tools/view_results.php']['parent'] = 'tools/my_tests.php';

		// test questions
		$_pages['tools/tests/create_question_tf.php']['title_var']  = 'create_new_question';
		$_pages['tools/tests/create_question_tf.php']['parent'] = 'tools/tests/question_db.php';
		
		$_pages['tools/tests/create_question_multi.php']['title_var']  = 'create_new_question';
		$_pages['tools/tests/create_question_multi.php']['parent'] = 'tools/tests/question_db.php';

		$_pages['tools/tests/create_question_long.php']['title_var']  = 'create_new_question';
		$_pages['tools/tests/create_question_long.php']['parent'] = 'tools/tests/question_db.php';

		$_pages['tools/tests/create_question_likert.php']['title_var']  = 'create_new_question';
		$_pages['tools/tests/create_question_likert.php']['parent'] = 'tools/tests/question_db.php';

		$_pages['tools/tests/edit_question_tf.php']['title_var']  = 'edit_question';
		$_pages['tools/tests/edit_question_tf.php']['parent'] = 'tools/tests/question_db.php';
		
		$_pages['tools/tests/edit_question_multi.php']['title_var']  = 'edit_question';
		$_pages['tools/tests/edit_question_multi.php']['parent'] = 'tools/tests/question_db.php';

		$_pages['tools/tests/edit_question_long.php']['title_var']  = 'edit_question';
		$_pages['tools/tests/edit_question_long.php']['parent'] = 'tools/tests/question_db.php';

		$_pages['tools/tests/edit_question_likert.php']['title_var']  = 'edit_question';
		$_pages['tools/tests/edit_question_likert.php']['parent'] = 'tools/tests/question_db.php';

	$_pages['tools/take_test.php']['title_var']  = 'take_test';
	$_pages['tools/take_test.php']['parent'] = 'tools/my_tests.php';

	$_pages['tools/chat/index.php']['title_var']  = 'chat';
	$_pages['tools/chat/index.php']['parent'] = 'tools/index.php';
	$_pages['tools/chat/index.php']['children'] = array('tools/chat/start_transcript.php');
	$_pages['tools/chat/index.php']['guide']    = 'instructor/?p=3.0.chat.php';

		$_pages['tools/chat/start_transcript.php']['title_var']  = 'chat_start_transcript';
		$_pages['tools/chat/start_transcript.php']['parent'] = 'tools/chat/index.php';

		$_pages['tools/chat/delete_transcript.php']['title_var']  = 'chat_delete_transcript';
		$_pages['tools/chat/delete_transcript.php']['parent'] = 'tools/chat/index.php';

		$_pages['tools/chat/view_transcript.php']['title_var']  = 'chat_transcript';
		$_pages['tools/chat/view_transcript.php']['parent'] = 'tools/chat/index.php';

	$_pages['tools/packages/index.php']['title_var'] = 'packages';
	$_pages['tools/packages/index.php']['parent']    = 'tools/index.php';
	$_pages['tools/packages/index.php']['children']  = array('tools/packages/import.php', 'tools/packages/delete.php', 'tools/packages/settings.php');
	$_pages['tools/packages/index.php']['guide']    = 'instructor/?p=4.5.scorm_packages.php';
		
		$_pages['tools/packages/import.php']['title_var'] = 'import_package';
		$_pages['tools/packages/import.php']['parent']    = 'tools/packages/index.php';
		
		$_pages['tools/packages/delete.php']['title_var'] = 'delete_package';
		$_pages['tools/packages/delete.php']['parent']    = 'tools/packages/index.php';
		
		$_pages['tools/packages/settings.php']['title_var'] = 'package_settings';
		$_pages['tools/packages/settings.php']['parent']    = 'tools/packages/index.php';

/* student pages: */
$_pages['sitemap.php']['title_var'] = 'sitemap';
$_pages['sitemap.php']['parent']    = 'index.php';
$_pages['sitemap.php']['img']       = 'images/home-site_map.gif';

$_pages['forum/list.php']['title_var']  = 'forums';
$_pages['forum/list.php']['img']        = 'images/home-forums.gif';

$_pages['glossary/index.php']['title_var'] = 'glossary';
$_pages['glossary/index.php']['img']       = 'images/home-glossary.gif';

$_pages['links/index.php']['title_var'] = 'links';
$_pages['links/index.php']['children']  = array('links/add.php');
$_pages['links/index.php']['img']       = 'images/home-links.gif';

	$_pages['links/add.php']['title_var'] = 'suggest_link';
	$_pages['links/add.php']['parent']    = 'links/index.php';

$_pages['chat/index.php']['title_var'] = 'chat';
$_pages['chat/index.php']['img']       = 'images/home-chat.gif';

	$_pages['chat/chat_frame.php']['title_var'] = 'chat';
	$_pages['chat/chat_frame.php']['parent']    = 'chat/index.php';

	$_pages['chat/view_transcript.php']['title_var'] = 'chat_transcript';
	$_pages['chat/view_transcript']['parent']        = 'chat/index.php';
	

$_pages['tile.php']['title_var'] = 'tile_search';
$_pages['tile.php']['img']       = 'images/home-tile_search.gif';

$_pages['my_stats.php']['title_var'] = 'my_tracker';
$_pages['my_stats.php']['img']       = 'images/home-tracker.gif';

$_pages['tools/my_tests.php']['title_var'] = 'my_tests';
$_pages['tools/my_tests.php']['img']       = 'images/home-tests.gif';

$_pages['polls/index.php']['title_var'] = 'polls';
$_pages['polls/index.php']['img']       = 'images/home-polls.gif';

$_pages['inbox/index.php']['title_var'] = 'inbox';
$_pages['inbox/index.php']['children']  = array('inbox/send_message.php');

	$_pages['inbox/send_message.php']['title_var'] = 'send_message';
	$_pages['inbox/send_message.php']['parent']    = 'inbox/index.php';

$_pages['acollab/bounce.php']['title_var'] = 'acollab';
$_pages['acollab/bounce.php']['img']       = 'images/home-acollab.gif';

	//$_pages['acollab/index.php']['title_var'] = 'acollab';
	//$_pages['acollab/index.php']['img'] = 'images/home-acollab.gif';

$_pages['export.php']['title_var'] = 'export_content';
$_pages['export.php']['img']       = 'images/home-export_content.gif';

$_pages['directory.php']['title_var'] = 'directory';
$_pages['directory.php']['img']       = 'images/home-directory.gif';

$_pages['profile.php']['title_var'] = 'profile';
$_pages['profile.php']['parent']    = 'index.php';

$_pages['packages/index.php']['title_var'] = 'packages';
$_pages['packages/index.php']['img']       = 'images/content_pkg.gif';
$_pages['packages/index.php']['children']  = array ('packages/preferences.php');

	$_pages['packages/preferences.php']['title_var'] = 'package_preferences';
	$_pages['packages/preferences.php']['parent']    = 'packages/index.php';

	$_pages['packages/cmidata.php']['title_var'] = 'cmi_data';
	$_pages['packages/cmidata.php']['parent']    = 'packages/index.php';


if (($_SESSION['course_id'] > 0) && isset($_modules)) {
	foreach ($_modules as $module) {
		if (in_array($module, $_pages[AT_NAV_COURSE])) {
			$_pages[$module]['parent'] = AT_NAV_COURSE;
		} else {
			$_pages[$module]['parent'] = 'index.php';
		}
	}
} else if ($_SESSION['course_id'] == -1) {
	/* administrator section: */
	/* authenticate user for the sections they have access to. */
	if (!admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
		$_pages[AT_NAV_ADMIN]  = array('admin/index.php');
		if (admin_authenticate(AT_ADMIN_PRIV_USERS, TRUE)) {
			$_pages[AT_NAV_ADMIN][] = 'admin/users.php';
			$_pages['admin/users.php']['parent'] = AT_NAV_ADMIN;
		}
		if (admin_authenticate(AT_ADMIN_PRIV_COURSES, TRUE)) {
			$_pages[AT_NAV_ADMIN][] = 'admin/courses.php';
			$_pages['admin/courses.php']['children'] = array('admin/create_course.php');
			$_pages['admin/courses.php']['parent'] = AT_NAV_ADMIN;
		}
		if (admin_authenticate(AT_ADMIN_PRIV_BACKUPS, TRUE)) {
			$_pages[AT_NAV_ADMIN][] = 'admin/backup/index.php';
			$_pages['admin/backup/index.php']['parent'] = AT_NAV_ADMIN;
		}
		if (admin_authenticate(AT_ADMIN_PRIV_FORUMS, TRUE)) {
			$_pages[AT_NAV_ADMIN][] = 'admin/forums.php';
			$_pages['admin/forums.php']['parent'] = AT_NAV_ADMIN;
		}
		if (admin_authenticate(AT_ADMIN_PRIV_CATEGORIES, TRUE)) {
			$_pages[AT_NAV_ADMIN][] = 'admin/course_categories.php';
			$_pages['admin/course_categories.php']['parent'] = AT_NAV_ADMIN;
		}
		if (admin_authenticate(AT_ADMIN_PRIV_LANGUAGES, TRUE)) {
			$_pages[AT_NAV_ADMIN][] = 'admin/language.php';
			$_pages['admin/language.php']['parent'] = AT_NAV_ADMIN;
		}
		if (admin_authenticate(AT_ADMIN_PRIV_THEMES, TRUE)) {
			$_pages[AT_NAV_ADMIN][] = 'admin/themes/index.php';
			$_pages['admin/themes/index.php']['parent'] = AT_NAV_ADMIN;
		}
	}
}

/* global pages */
$_pages['about.php']['title_var']  = 'about_atutor';

$_pages['404.php']['title_var']  = '404';

$_pages['help/index.php']['title_var']  = 'help';
$_pages['help/index.php']['children'] = array('help/accessibility.php', 'help/contact_support.php');

	$_pages['help/accessibility.php']['title_var']  = 'accessibility';
	$_pages['help/accessibility.php']['parent'] = 'help/index.php';

	$_pages['help/contact_support.php']['title_var']  = 'contact_support';
	$_pages['help/contact_support.php']['parent'] = 'help/index.php';

$_pages['search.php']['title_var']      = 'search';

$current_page = substr($_SERVER['PHP_SELF'], strlen($_base_path));

function get_num_new_messages() {
	global $db;
	static $num_messages;

	if (isset($num_messages)) {
		return $num_messages;
	}
	$sql    = "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."messages WHERE to_member_id=$_SESSION[member_id] AND new=1";
	$result = mysql_query($sql, $db);
	$row    = mysql_fetch_assoc($result);
	$num_messages = $row['cnt'];

	return $num_messages;
}

function get_main_navigation($current_page) {
	global $_pages, $_base_path;

	$parent_page = $_pages[$current_page]['parent'];
	$_top_level_pages = array();

	if (isset($parent_page) && is_numeric($parent_page)) {
		foreach($_pages[$parent_page] as $page) {
			$_page_title = _AT($_pages[$page]['title_var']);
			
			$_top_level_pages[] = array('url' => $_base_path . $page, 'title' => $_page_title);
		}
	} else if (isset($parent_page)) {
		return get_main_navigation($parent_page);
	}

	return $_top_level_pages;
}

function get_current_main_page($current_page) {
	global $_pages, $_base_path;

	$parent_page = $_pages[$current_page]['parent'];

	if (isset($parent_page) && is_numeric($parent_page)) {
		return $_base_path . $current_page;
	} else if (isset($parent_page)) {
		return get_current_main_page($parent_page);
	}
}

function get_sub_navigation($current_page) {
	global $_pages, $_base_path;

	if (isset($current_page) && is_numeric($current_page)) {
		// reached the top
		return array();
	} else if (isset($_pages[$current_page]['children'])) {
		if (isset($_pages[$current_page]['title'])) {
			$_page_title = $_pages[$current_page]['title'];
		} else {
			$_page_title = _AT($_pages[$current_page]['title_var']);
		}

		$_sub_level_pages[] = array('url' => $_base_path . $current_page, 'title' => $_page_title);
		foreach ($_pages[$current_page]['children'] as $child) {

			if (isset($_pages[$child]['title'])) {
				$_page_title = $_pages[$child]['title'];
			} else {
				$_page_title = _AT($_pages[$child]['title_var']);
			}

			$_sub_level_pages[] = array('url' => $_base_path . $child, 'title' => $_page_title);
		}
	} else if (isset($_pages[$current_page]['parent'])) {
		// no children

		$parent_page = $_pages[$current_page]['parent'];
		return get_sub_navigation($parent_page);
	}

	return $_sub_level_pages;
}

function get_current_sub_navigation_page($current_page) {
	global $_pages, $_base_path;

	$parent_page = $_pages[$current_page]['parent'];

	if (isset($parent_page) && is_numeric($parent_page)) {
		return $_base_path . $current_page;
	} else {
		return $_base_path . $current_page;
	}
}

function get_path($current_page) {
	global $_pages, $_base_path;

	$parent_page = $_pages[$current_page]['parent'];

	if (isset($_pages[$current_page]['title'])) {
		$_page_title = $_pages[$current_page]['title'];
	} else {
		$_page_title = _AT($_pages[$current_page]['title_var']);
	}

	if (isset($parent_page) && is_numeric($parent_page)) {
		$path[] = array('url' => $_base_path . $current_page, 'title' => $_page_title);
		return $path;
	} else if (isset($parent_page)) {
		$path[] = array('url' => $_base_path . $current_page, 'title' => $_page_title);
		$path = array_merge($path, get_path($parent_page));
	} else {
		$path[] = array('url' => $_base_path . $current_page, 'title' => $_page_title);
	}
	
	return $path;
}

function get_home_navigation() {
	global $_pages, $_base_path;

	$home_links = array();
	foreach ($_pages[AT_NAV_HOME] as $child) {
		$home_links[] = array('url' => $_base_path . $child, 'title' => _AT($_pages[$child]['title_var']), 'img' => $_base_path.$_pages[$child]['img']);
	}

	return $home_links;
}
?>
