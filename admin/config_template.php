<?php 
/************************************************************************/
/* ATutor                                                               */
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg, Heidi Hazelton */
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
define('EMAIL_NOTIFY',                 {EMAIL_NOTIFY});

/* allow regular account users to request their account to be           */
/* upgraded to instructor accounts.                                     */
define('ALLOW_INSTRUCTOR_REQUESTS',    {INSTRUCTOR_REQUESTS});

/* If ALLOW_INSTRUCTOR_REQUESTS is true then you can have the           */
/* requests approved instantly, otherwise each request will             */
/* have to be approved manually by the admin.                           */
define('AUTO_APPROVE_INSTRUCTORS',     {APPROVE_INSTRUCTORS});

/************************************************************************/
/* File manager options:                                                */

/* Default maximum allowable file size in Bytes to upload:              */
/* Will not override the upload_max_filesize in php.ini                 */
$MaxFileSize   =   {MAX_FILE_SIZE}; /* 1 MB */

/* Default total maximum allowable course size in Bytes:                */
/* When this number is exceeded, no more uploads will be allowed        */
$MaxCourseSize =  {MAX_COURSE_SIZE}; /* 10 MB */

/* Soft limit threshold:                                                */
/* How much a course can be over, while still allowing the              */
/* upload to finish.                                                    */
/* Therefore the real course limit is                                   */
/* \$MaxCourseSize + \$MaxCourseFloat, but when the float gets          */
/* used then no more uploads will be allowed.                           */
$MaxCourseFloat =  {MAX_COURSE_FLOAT}; /* 2 MB */

/* Illegal file types, by extension. Include any extensions             */
/* you do not want to allow for uploading. (Just the extention          */
/* without the leading dot.)                                            */
$IllegalExtentions = array({ILL_EXT});

/* The name of your course website.                                     */
/* Example: Acme University's Course Server                             */
/* Single quotes will have to be escaped with a slash: \'.              */
define('SITE_NAME', '{SITE_NAME}');

/* link for the 'home' menu item.  Will not show if empty */
define('HOME_URL', '{HOME_URL}');

/* Default language to use, if not browser-defined or                   */
/* user-defined. 'en' is always available. Any other language           */
/* specified must already exist in the database.                        */
/* Default language: en                                                 */
define('DEFAULT_LANGUAGE',             '{DEFAULT_LANGUAGE}');

/* Where the cache directory should be created. On a Windows            */
/* machine the path should look like C:\Windows\temp\. Path             */
/* must end in a slash. The directory must already exist.               */
/* Make empty or comment out to disable cacheing.                       */
/* Back slashes must be escaped if at the end: ex: ..tmp\\');           */
define('CACHE_DIR', '{CACHE_DIR}');

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
define('AT_ENABLE_CATEGORY_THEMES',      {THEME_CATEGORIES});

/* How many backup files can be stored per course.                      */
define('AT_COURSE_BACKUPS', {COURSE_BACKUPS});

/* Whether or not to require email confirmation to validate accounts    */
define('AT_EMAIL_CONFIRMATION', {EMAIL_CONFIRMATION});

/* Whether or not to enable master list authentication.                 */
/* If enabled, only new accounts that validate against the master list  */
/* will be created. The master list is flexible and can be used for any */
/* fields.                                                              */
define('AT_MASTER_LIST', {MASTER_LIST});

/* Whether or not to show the ongoing tests box on the home page.       */
/* Default: TRUE (on)                                                   */
define('AT_SHOW_TEST_BOX', TRUE);

/* Whether or not to use the AT_CONTENT_DIR as a protected directory.   */
/* The if set to FALSE then the content directory will be hard coded    */
/* to ATutor_install_dir/content/ and AT_CONTENT_DIR will be ignored.   */
/* This option is used for compatability with IIS and Apache 2.         */
define('AT_FORCE_GET_FILE', {GET_FILE});

/* Whether or not to allow user notes in the handbook.                  */
define('AT_ENABLE_HANDBOOK_NOTES', {USER_NOTES});

/* DO NOT ALTER THIS LAST LINE                                          */
define('AT_INSTALL', true);

?>