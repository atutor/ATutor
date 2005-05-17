<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

if (!defined('AT_INCLUDE_PATH')) { exit; }

function write_config_file($filename, $comments) {

	global $config_template;

	$tokens = array('{USER}',
					'{PASSWORD}',
					'{HOST}',
					'{PORT}',
					'{DBNAME}',
					'{TABLE_PREFIX}',
					'{EMAIL}',
					'{SITE_NAME}',
					'{HEADER_IMG}',
					'{HEADER_LOGO}',
					'{HOME_URL}',
					'{GENERATED_COMMENTS}',
					'{CONTENT_DIR}',
					'{MAIL_USE_SMTP}',
					'{GET_FILE}'
				);

	if ($_POST['step1']['old_path'] != '') {
		$values = array(urldecode($_POST['step1']['db_login']),
					addslashes(urldecode($_POST['step1']['db_password'])),
					$_POST['step1']['db_host'],
					$_POST['step1']['db_port'],
					$_POST['step1']['db_name'],
					urldecode($_POST['step3']['email']),
					$_POST['step3']['email_notification'],
					$_POST['step3']['email_confirmation'],
					$_POST['step3']['allow_instructor_requests'],
					$_POST['step3']['auto_approve_instructors'],
					$_POST['step3']['max_file_size'],
					$_POST['step3']['max_course_size'],
					$_POST['step3']['max_course_float'],
					urldecode($_POST['step3']['ill_ext']),
					addslashes(urldecode($_POST['step3']['site_name'])),
					addslashes(urldecode($_POST['step1']['header_img'])),
					addslashes(urldecode($_POST['step1']['header_logo'])),
					addslashes(urldecode($_POST['step3']['home_url'])),
					$_POST['step1']['tb_prefix'],
					$comments,
					addslashes(urldecode($_POST['step3']['cache_dir'])),
					addslashes(urldecode($_POST['step4']['content_dir'])),
					$_POST['step3']['smtp'],
					$_POST['step3']['theme_categories'],
					$_POST['step3']['course_backups'],
					$_POST['step3']['get_file']
				);
	} else {
		$values = array(urldecode($_POST['step2']['db_login']),
					addslashes(urldecode($_POST['step2']['db_password'])),
					$_POST['step2']['db_host'],
					$_POST['step2']['db_port'],
					$_POST['step2']['db_name'],
					$_POST['step2']['tb_prefix'],
					urldecode($_POST['step3']['email']),
					addslashes(urldecode($_POST['step3']['site_name'])),
					'', //header image
					'', //header logo
					addslashes(urldecode($_POST['step3']['home_url'])),
					$comments,
					addslashes(urldecode($_POST['step4']['content_dir'])),
					$_POST['step3']['smtp'],
					$_POST['step4']['get_file']
				);
	}

	$config_template = str_replace($tokens, $values, $config_template);

	if (!$handle = @fopen($filename, 'wb')) {
         return false;
    }
	@ftruncate($handle,0);
    if (!@fwrite($handle, $config_template, strlen($config_template))) {
		return false;
    }
        
    @fclose($handle);
	return true;
}

$config_template = "<"."?php 
/************************************************************************/
/* ATutor                                                               */
/************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg, Heidi Hazelton */
/* http://atutor.ca                                                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
{GENERATED_COMMENTS}
/************************************************************************/
/************************************************************************/

/* the database user name                                               */
define('DB_USER',                      '{USER}');

/* the database password                                                */
define('DB_PASSWORD',                  '{PASSWORD}');

/* the database host                                                    */
define('DB_HOST',                      '{HOST}');

/* the database tcp/ip port                                             */
define('DB_PORT',                      '{PORT}');

/* the database name                                                    */
define('DB_NAME',                      '{DBNAME}');

/* The prefix to add to table names to avoid conflicts with existing    */
/* tables. Default: AT_                                                 */
define('TABLE_PREFIX',                 '{TABLE_PREFIX}');

/* The email that will be used as the return email when needed and when */
/* instructor account requests are made.                                */
define('EMAIL',                         '{EMAIL}');

/* do you want to receive emails when new instructor accounts           */
/* require approval                                                     */
define('EMAIL_NOTIFY',                 TRUE);

/* allow regular account users to request their account to be           */
/* upgraded to instructor accounts.                                     */
define('ALLOW_INSTRUCTOR_REQUESTS',    TRUE);

/* If ALLOW_INSTRUCTOR_REQUESTS is true then you can have the           */
/* requests approved instantly, otherwise each request will             */
/* have to be approved manually by the admin.                           */
define('AUTO_APPROVE_INSTRUCTORS',     FALSE);

/************************************************************************/
/* File manager options:                                                */

/* Default maximum allowable file size in Bytes to upload:              */
/* Will not override the upload_max_filesize in php.ini                 */
\$MaxFileSize   =   1048576; /* 1 MB */

/* Default total maximum allowable course size in Bytes:                */
/* When this number is exceeded, no more uploads will be allowed        */
\$MaxCourseSize =  10485760; /* 10 MB */

/* Soft limit threshold:                                                */
/* How much a course can be over, while still allowing the              */
/* upload to finish.                                                    */
/* Therefore the real course limit is                                   */
/* \$MaxCourseSize + \$MaxCourseFloat, but when the float gets          */
/* used then no more uploads will be allowed.                           */
\$MaxCourseFloat =  2097152; /* 2 MB */

/* Illegal file types, by extension. Include any extensions             */
/* you do not want to allow for uploading. (Just the extention          */
/* without the leading dot.)                                            */
\$IllegalExtentions = array('exe','asp','php','php3','bat','cgi','pl','com','vbs','reg','pcd','pif','scr','bas','inf','vb','vbe','wsc','wsf','wsh');

/* The name of your course website.                                     */
/* Example: Acme University's Course Server                             */
/* Single quotes will have to be escaped with a slash: \'.              */
define('SITE_NAME', '{SITE_NAME}');

/* Public area variables.                                               */
/* Top left header image  - approximately w:230 x h:90                  */
/* Default: images/pub_default.jpg                                      */	
define('HEADER_IMAGE', '{HEADER_IMG}');

/* Top right logo default: images/at-logo.gif                           */
define('HEADER_LOGO', '{HEADER_LOGO}');

/* link for the 'home' menu item.  Will not show if empty               */
define('HOME_URL', '{HOME_URL}');

/* Default language to use, if not browser-defined or                   */
/* user-defined. 'en' is always available. Any other language           */
/* specified must already exist in the database.                        */
/* Default language: en                                                 */
define('DEFAULT_LANGUAGE', 'en');

/* Where the cache directory should be created. On a Windows            */
/* machine the path should look like C:\Windows\\temp\. Path             */
/* must end in a slash. The directory must already exist.               */
/* Make empty or comment out to disable cacheing.                       */
/* Back slashes must be escaped if at the end: ex: ..tmp\\');            */
define('CACHE_DIR', '');

/* Where the course content files are located.  This includes all file  */
/* manager and imported files.  If security is a concern, it is         */
/* recommended that the content directory be moved outside of the web	*/
/* accessible area.														*/
define('AT_CONTENT_DIR', '{CONTENT_DIR}');

/* Whether or not to use the default php.ini SMTP settings.             */
/* If false, then mail will try to be sent using sendmail.              */
define('MAIL_USE_SMTP', {MAIL_USE_SMTP});

/* Whether or not to enable theme specific categories and disable the   */
/* personal theme preference.                                           */
define('AT_ENABLE_CATEGORY_THEMES',      FALSE);

/* How many backup files can be stored per course.                      */
define('AT_COURSE_BACKUPS', 5);

/* Whether or not to require email confirmation to validate accounts    */
define('AT_EMAIL_CONFIRMATION', FALSE);

/* Whether or not to enable master list authentication.                 */
/* If enabled, only new accounts that validate against the master list  */
/* will be created. The master list is flexible and can be used for any */
/* fields.                                                              */
define('AT_MASTER_LIST', FALSE);


/* Whether or not to show the ongoing tests box on the home page.       */
/* Default: TRUE (on)                                                   */
define('AT_SHOW_TEST_BOX', FALSE);

/* Whether or not to use the AT_CONTENT_DIR as a protected directory.   */
/* If set to FALSE then the content directory will be hard coded        */
/* to ATutor_install_dir/content/ and AT_CONTENT_DIR will be ignored.   */
/* This option is used for compatability with IIS and Apache 2.         */
define('AT_FORCE_GET_FILE', {GET_FILE});

/* SCORM 1.2 RTE. The 'sco' directory must exist inside the ATutor      */
/* installation path for this feature to be enabled.                    */
define('AT_ENABLE_SCO', FALSE);

/* ACollab integration constants.                                       */
/* Follow the instructions in ACollab's administration section under    */
/* Integrate ATutor.                                                    */
define('AC_PATH', '');
define('AC_TABLE_PREFIX', '');


/* DO NOT ALTER THIS LAST LINE                                          */
define('AT_INSTALL', TRUE);

?".">";

?>