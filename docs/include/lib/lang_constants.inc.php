<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg & Heidi Hazelton	*/
/* Adaptive Technology Resource Centre / University of Toronto				*/
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/
// $Id$
// This file contains the constants for the ATutor feedback system
// FEEDBACK TYPES:
// AT_ERROR, AT_HELP, AT_WARNING, AT_FEEDBACK, AT_INFOS
// THIS FILE SHOULD ONLY BE EDITED WHEN NEW FEEDBACK MESSAGES ARE ADDED 
if (!defined('AT_INCLUDE_PATH')) { exit; }

/* the error codes counter. we're reserving 0 for some reason. */
$i=1;

/********************************************************/
/* ERROR DEFINITIONS									*/

define('AT_ERROR_GENERAL',                $i); $i++;
define('AT_ERROR_UNKNOWN',                $i); $i++;
define('AT_ERROR_NO_SUCH_COURSE',         $i); $i++;
define('AT_ERROR_NO_TITLE',               $i); $i++;
define('AT_ERROR_BAD_DATE',               $i); $i++;
define('AT_ERROR_ID_ZERO',                $i); $i++;
define('AT_ERROR_PAGE_NOT_FOUND',         $i); $i++;
define('AT_ERROR_BAD_FILE_TYPE',          $i); $i++;
define('AT_ERROR_ANN_BOTH_EMPTY',         $i); $i++;
define('AT_ERROR_ANN_ID_ZERO',            $i); $i++;
define('AT_ERROR_ANN_NOT_FOUND',          $i); $i++;
define('AT_ERROR_TERM_EMPTY',             $i); $i++;
define('AT_ERROR_DEFINITION_EMPTY',       $i); $i++;
define('AT_ERROR_TERM_EXISTS',            $i); $i++;
define('AT_ERROR_GLOS_ID_MISSING',        $i); $i++;
define('AT_ERROR_TERM_NOT_FOUND',         $i); $i++;
define('AT_ERROR_FORUM_TITLE_EMPTY',      $i); $i++;
define('AT_ERROR_POST_ID_ZERO',           $i); $i++;
define('AT_ERROR_POST_NOT_FOUND',         $i); $i++;
define('AT_ERROR_FORUM_NOT_FOUND',        $i); $i++;
define('AT_ERROR_ACCESS_DENIED',          $i); $i++;
define('AT_ERROR_LOGIN_TO_POST',          $i); $i++;
define('AT_ERROR_ALREADY_SUB',            $i); $i++;
define('AT_ERROR_ACCESS_INSTRUCTOR',      $i); $i++;
define('AT_ERROR_STUD_INFO_NOT_FOUND',    $i); $i++;
define('AT_ERROR_ADMIN_INFO_NOT_FOUND',   $i); $i++;
define('AT_ERROR_MSG_SUBJECT_EMPTY',      $i); $i++;
define('AT_ERROR_MSG_BODY_EMPTY',         $i); $i++;
define('AT_ERROR_MSG_TO_INSTRUCTOR',      $i); $i++;
define('AT_ERROR_INST_INFO_NOT_FOUND',    $i); $i++;
define('AT_ERROR_CHOOSE_ONE_SECTION',     $i); $i++;
define('AT_ERROR_NO_COURSE_CONTENT',      $i); $i++;
define('AT_ERROR_NO_DB_CONNECT',          $i); $i++;
define('AT_ERROR_PREFS_NO_ACCESS',        $i); $i++;
define('AT_ERROR_NO_USER_PREFS',          $i); $i++;
define('AT_ERROR_INVALID_LOGIN',          $i); $i++;
define('AT_ERROR_EMAIL_NOT_FOUND',        $i); $i++;
define('AT_ERROR_EMAIL_MISSING',          $i); $i++;
define('AT_ERROR_EMAIL_INVALID',          $i); $i++;
define('AT_ERROR_EMAIL_EXISTS',           $i); $i++;
define('AT_ERROR_LOGIN_NAME_MISSING',     $i); $i++;
define('AT_ERROR_LOGIN_CHARS',            $i); $i++;
define('AT_ERROR_LOGIN_EXISTS',           $i); $i++;
define('AT_ERROR_PASSWORD_MISSING',       $i); $i++;
define('AT_ERROR_PASSWORD_MISMATCH',      $i); $i++;
define('AT_ERROR_DB_NOT_UPDATED',         $i); $i++;
define('AT_ERROR_NO_RECIPIENT',           $i); $i++;
define('AT_ERROR_SEND_ENROL',             $i); $i++;
define('AT_ERROR_SEND_MEMBERS',           $i); $i++;
define('AT_ERROR_FILE_ILLEGAL',           $i); $i++;
define('AT_ERROR_FILE_NOT_SAVED',         $i); $i++;
define('AT_ERROR_FILE_TOO_BIG',           $i); $i++;
define('AT_ERROR_MAX_STORAGE_EXCEEDED',   $i); $i++;
define('AT_ERROR_FILE_NOT_SELECTED',      $i); $i++;
define('AT_ERROR_FOLDER_NOT_CREATED',     $i); $i++;
define('AT_ERROR_DIR_NOT_OPENED',         $i); $i++;
define('AT_ERROR_DIR_NOT_DELETED',        $i); $i++;
define('AT_ERROR_DIR_NOT_EMPTY',          $i); $i++; // no longer used (remove next major version)
define('AT_ERROR_DIR_NO_PERMISSION',      $i); $i++;
define('AT_ERROR_NOT_RELEASED',           $i); $i++;
define('AT_ERROR_UNSUPPORTED_FILE',       $i); $i++;
define('AT_ERROR_CSS_ONLY',               $i); $i++;
define('AT_ERROR_RESULT_NOT_FOUND',       $i); $i++;
define('AT_ERROR_SUPPLY_TITLE',           $i); $i++;
define('AT_ERROR_CREATE_NOPERM',          $i); $i++;
define('AT_ERROR_NO_STUDENTS',            $i); $i++;
define('AT_ERROR_ALREADY_OWNED',          $i); $i++;
define('AT_ERROR_ALREADY_ENROLED',        $i); $i++;
define('AT_ERROR_LOGIN_ENROL',            $i); $i++;
define('AT_ERROR_REMOVE_COURSE',          $i); $i++;
define('AT_ERROR_DESC_REQUIRED',          $i); $i++;
define('AT_ERROR_START_DATE_INVALID',     $i); $i++;
define('AT_ERROR_END_DATE_INVALID',       $i); $i++;
define('AT_ERRORS_QUESTION_EMPTY',        $i); $i++; /* why is this ERRORS */
define('AT_ERROR_QUESTION_NOT_FOUND',     $i); $i++;
define('AT_ERROR_TEST_NOT_FOUND',         $i); $i++;
define('AT_ERROR_FIELD_TITLE_EMPTY',      $i); $i++;
define('AT_ERROR_FIELD_SIZE_EMPTY',       $i); $i++;
define('AT_ERROR_FIELD_SIZE_EMPTY_MULTI', $i); $i++;
define('AT_ERROR_FIELD_TYPE_EMPTY',       $i); $i++;
define('AT_ERROR_SIZE_TEXTAREA_BOTH',     $i); $i++;
define('AT_ERROR_OPTION_MISSING',         $i); $i++;
define('AT_ERROR_VALUE_MISSING',          $i); $i++;
define('AT_ERROR_TEST_EMAIL_MISSING',     $i); $i++;
define('AT_ERROR_TEST_EMAIL_INVALID',     $i); $i++;
define('AT_ERROR_TEST_THANKYOU',          $i); $i++;
define('AT_ERROR_TEST_HOSTUSER_MISSING',  $i); $i++;
define('AT_ERROR_TEST_TABLENAME_MISSING', $i); $i++;
define('AT_ERROR_TEST_COLNAME_MISSING',   $i); $i++;
define('AT_ERROR_TEST_COL_NOSPACE',       $i); $i++;
define('AT_ERROR_CHOOSE_YESNO',           $i); $i++;
define('AT_ERROR_DB_NOT_CONNECTED',       $i); $i++;
define('AT_ERROR_DB_NOT_ACCESSED',        $i); $i++;
define('AT_ERROR_TABLE_NOT_CREATED',      $i); $i++;
define('AT_ERROR_NOT_OWNER',              $i); $i++;
define('AT_ERROR_CSV_FAILED',             $i); $i++;
define('AT_ERROR_EXPORTDIR_FAILED',       $i); $i++;
define('AT_ERROR_IMPORTDIR_FAILED',       $i); $i++;
define('AT_ERROR_IMPORTFILE_EMPTY',       $i); $i++;
define('AT_ERROR_NO_QUESTIONS',           $i); $i++;
define('AT_ERROR_NODELETE_USER',          $i); $i++;
define('AT_ERRORS_TRACKING_NOT_DELETED',  $i); $i++;
define('AT_ERROR_CPREFS_NOT_FOUND',       $i); $i++;
define('AT_ERROR_THEME_NOT_FOUND',        $i); $i++;
define('AT_ERROR_CANNOT_OPEN_DIR',        $i); $i++;
define('AT_ERROR_CANNOT_CREATE_DIR',      $i); $i++;
define('AT_ERROR_NO_SPACE_LEFT',          $i); $i++;
define('AT_ERROR_CANNOT_OVERWRITE_FILE',  $i); $i++;
define('AT_ERROR_LINK_CAT_NOT_EMPTY',     $i); $i++;
define('AT_ERROR_NO_CONTENT_SPACE',       $i); $i++;
define('AT_ERROR_NO_LANGUAGE',            $i); $i++;
define('AT_ERROR_CACHE_DIR_BAD',          $i); $i++;
define('AT_ERROR_TRAN_NOT_FOUND',         $i); $i++;
define('AT_ERROR_CHAT_TRAN_REJECTED',     $i); $i++;
define('AT_ERROR_LANG_MISSING',           $i); $i++;
define('AT_ERROR_LANG_IMPORT_FAILED',     $i); $i++;
define('AT_ERROR_SEARCH_TERM_REQUIRED',   $i); $i++;
define('AT_ERROR_LIST_IMPORT_FAILED',     $i); $i++;
define('AT_ERROR_CAT_NOT_INSERTED',       $i); $i++;
define('AT_ERROR_CAT_NO_NAME',            $i); $i++;
define('AT_ERROR_CAT_HAS_SUBS',           $i); $i++;
define('AT_ERROR_CAT_DELETE_FAILED',      $i); $i++;
define('AT_ERROR_CAT_UPDATE_FAILED',      $i); $i++;
define('AT_ERROR_NO_IMSMANIFEST',         $i); $i++;
define('AT_ERROR_IMPORTDIR_NOTVALID',     $i); $i++;
define('AT_ERROR_NO_IMS_BACKUP',          $i); $i++;
define('AT_ERROR_TITLE_EMPTY',            $i); $i++;
define('AT_ERROR_INCORRECT_FILE_FORMAT',  $i); $i++;
define('AT_ERROR_FILE_MAX_SIZE',          $i); $i++;
define('AT_ERRORS_TILE_UNAVAILABLE',      $i); $i++;
define('AT_ERROR_POLL_QUESTION_EMPTY',    $i); $i++;
define('AT_ERROR_POLL_NOT_FOUND',         $i); $i++;
define('AT_ERROR_DOB_INVALID',            $i); $i++;
define('AT_ERROR_CANNOT_RENAME',          $i); $i++;
define('AT_ERROR_MAX_ATTEMPTS',           $i); $i++;

define('AT_ERROR_LANG_CODE_MISSING',      $i); $i++;
define('AT_ERROR_LANG_CHARSET_MISSING',   $i); $i++;
define('AT_ERROR_LANG_REGEX_MISSING',     $i); $i++;
define('AT_ERROR_LANG_NNAME_MISSING',     $i); $i++;
define('AT_ERROR_LANG_ENAME_MISSING',     $i); $i++;
define('AT_ERROR_LAST_LANGUAGE',          $i); $i++;
define('AT_ERROR_LANG_EXISTS',            $i); $i++;
define('AT_ERROR_LANG_EMPTY',             $i); $i++;
define('AT_ERROR_RESTORE_MATERIAL',       $i); $i++;
define('AT_ERROR_RESTORE_TOO_BIG',        $i); $i++;

define('AT_ERROR_NO_STUDENT_SELECTED',    $i); $i++;
define('AT_ERROR_INCOMPLETE',             $i); $i++;

define('AT_ERROR_THEME_IMPORT_FAILED',    $i); $i++;
define('AT_ERROR_THEME_INFO_ABSENT',      $i); $i++;
define('AT_ERROR_IMPORT_ERROR_IN_ZIP',    $i); $i++;
define('AT_ERROR_CANNOT_OPEN_FILE',       $i); $i++;
define('AT_ERROR_CANNOT_WRITE_FILE',      $i); $i++;

define('AT_ERROR_FORUM_DENIED',			  $i); $i++;
define('AT_ERROR_NO_FILE_SELECT',		  $i); $i++;
define('AT_ERROR_SELECT_ONE_FILE',        $i); $i++;

define('AT_ERROR_NO_COURSE_SELECTED',     $i); $i++;
define('AT_ERROR_INVALID_URL',			  $i); $i++;

/********************************************************/
/* HELP DEFINITIONS										*/

//missing def's:
define('AT_HELP_FILEMANAGER2',            $i); $i++;
define('AT_HELP_FILEMANAGER3',            $i); $i++;
define('AT_HELP_FILEMANAGER4',            $i); $i++;
define('AT_HELP_MARK_RESULTS',            $i); $i++;
define('AT_HELP_TEXTICON_OPTIONS',        $i); $i++;
define('AT_HELP_THEME_OPTIONS',           $i); $i++;
define('AT_HELP_HIDE_HELP',               $i); $i++;
define('AT_HELP_NO_HELP',                 $i); $i++;
define('AT_HELP_ENABLE_EDITOR',           $i); $i++;
define('AT_HELP_DISABLE_EDITOR',          $i); $i++;
define('AT_HELP_EDITOR',                  $i); $i++;
define('AT_HELP_EDIT_CONTENT',            $i); $i++;
define('AT_HELP_DELETE_CONTENT',          $i); $i++;
define('AT_HELP_SUB_CONTENT',             $i); $i++;
define('AT_HELP_BROWSER_PRINT_BUTTON',    $i); $i++;
define('AT_HELP_EDIT_STYLES',             $i); $i++;
define('AT_HELP_EDIT_STYLES_MINI',        $i); $i++;
define('AT_HELP_ADD_ANNOUNCEMENT',        $i); $i++;
define('AT_HELP_ADD_ANNOUNCEMENT2',       $i); $i++;
define('AT_HELP_ADD_TOP_PAGE',            $i); $i++;
define('AT_HELP_CREATE_LINKS',            $i); $i++;
define('AT_HELP_CREATE_LINKS1',           $i); $i++;
define('AT_HELP_CREATE_FORUMS',           $i); $i++;
define('AT_HELP_FILE_LINKING',            $i); $i++;
define('AT_HELP_FILE_EXPORTABLE',         $i); $i++;
define('AT_HELP_EMBED_GLOSSARY',          $i); $i++;
define('AT_HELP_CONTENT_PATH',            $i); $i++;
define('AT_HELP_CONTENT_BACKWARDS',       $i); $i++;
define('AT_HELP_FORUM_STICKY',            $i); $i++; 
define('AT_HELP_FORUM_LOCK',              $i); $i++;
define('AT_HELP_TRACKING',                $i); $i++;
define('AT_HELP_TRACKING1',               $i); $i++;
define('AT_HELP_NOT_RELEASED',            $i); $i++;
define('AT_HELP_FORMATTING',              $i); $i++;
define('AT_HELP_PASTE_FILE',              $i); $i++;
define('AT_HELP_PASTE_FILE1',             $i); $i++;
define('AT_HELP_BODY',                    $i); $i++;
define('AT_HELP_KEYWORDS',                $i); $i++;
define('AT_HELP_ADD_CODES',               $i); $i++;
define('AT_HELP_ADD_CODES1',              $i); $i++;
define('AT_HELP_INSERT',                  $i); $i++;
define('AT_HELP_RELATED',                 $i); $i++;
define('AT_HELP_ANNOUNCEMENT',            $i); $i++;
define('AT_HELP_FILEMANAGER',             $i); $i++;
define('AT_HELP_FILEMANAGER1',            $i); $i++;
define('AT_HELP_CUSTOM_HEADER',           $i); $i++;
define('AT_HELP_CREATE_HEADER',           $i); $i++;
define('AT_HELP_DEMO_HELP',               $i); $i++;
define('AT_HELP_DEMO_HELP2',              $i); $i++;
define('AT_HELP_ADD_RESOURCE',            $i); $i++;
define('AT_HELP_ADD_RESOURCE1',           $i); $i++;
define('AT_HELP_ADD_RESOURCE_MINI',       $i); $i++;
define('AT_HELP_ADD_FORUM_MINI',          $i); $i++;
define('AT_HELP_GLOSSARY_MINI',           $i); $i++;
define('AT_HELP_GLOSSARY_MENU',           $i); $i++;
define('AT_HELP_USERS_MENU',              $i); $i++;
define('AT_HELP_RELATED_MENU',            $i); $i++;
define('AT_HELP_GLOBAL_MENU',             $i); $i++;
define('AT_HELP_LOCAL_MENU',              $i); $i++;
define('AT_HELP_MAIN_MENU',               $i); $i++;
define('AT_HELP_ADD_MC_QUESTION',         $i); $i++;
define('AT_HELP_ADD_TF_QUESTION',         $i); $i++;
define('AT_HELP_ADD_OPEN_QUESTION',       $i); $i++;
define('AT_HELP_ADD_TEST',                $i); $i++;
define('AT_HELP_POSITION_OPTIONS',        $i); $i++;
define('AT_HELP_DISPLAY_OPTIONS',         $i); $i++;
define('AT_HELP_MENU_OPTIONS',            $i); $i++;
define('AT_HELP_PREFERENCES',             $i); $i++;
define('AT_HELP_PREFERENCES1',            $i); $i++;
define('AT_HELP_PREFERENCES2',            $i); $i++;
define('AT_HELP_ADD_QUESTIONS',           $i); $i++;
define('AT_HELP_ADD_QUESTIONS2',          $i); $i++;
define('AT_HELP_PREVEIW_QUESTIONS',       $i); $i++;
define('AT_HELP_NETSCAPE4',               $i); $i++;
define('AT_HELP_CONTROL_CENTER1',         $i); $i++;
define('AT_HELP_CONTROL_CENTER2',         $i); $i++;
define('AT_HELP_CONTROL_PROFILE',         $i); $i++;
define('AT_HELP_IMPORT_EXPORT',           $i); $i++;
define('AT_HELP_IMPORT_EXPORT1',          $i); $i++;
define('AT_HELP_COURSE_EMAIL',            $i); $i++;
define('AT_HELP_COURSE_QUOTA',            $i); $i++;
define('AT_HELP_ENROLMENT',               $i); $i++;
define('AT_HELP_ENROLMENT2',              $i); $i++;
define('AT_HELP_PRINT_COMPILER',          $i); $i++;
define('AT_HELP_PRINT_COMPILER2',         $i); $i++;
define('AT_HELP_LINK_FILES',              $i); $i++;
define('AT_HELP_PRESET',                  $i); $i++;
define('AT_HELP_SEARCH',                  $i); $i++;
define('AT_HELP_SEARCH_MENU',             $i); $i++;
define('AT_HELP_BANNER_TEXT',             $i); $i++;
define('AT_HELP_COURSE_PREF',             $i); $i++;
define('AT_HELP_COURSE_PREF2',            $i); $i++;
define('AT_HELP_POLL_MENU',               $i); $i++;

/********************************************************/
/* WARNING DEFINITIONS									*/

// missing def's:
define('AT_WARNING_SURE_DELETE_USER',     $i); $i++;
define('AT_WARNING_RAM_SIZE',             $i); $i++;
define('AT_WARNING_THREAD_DELETE',        $i); $i++;
define('AT_WARNING_DELETE_FORUM',         $i); $i++;
define('AT_WARNING_CONFIRM_FILE_DELETE',  $i); $i++;
define('AT_WARNING_CONFIRM_DIR_DELETE',   $i); $i++;
define('AT_WARNING_SURE_DELETE_COURSE1',  $i); $i++;
define('AT_WARNING_SURE_DELETE_COURSE2',  $i); $i++;
define('AT_WARNING_REMOVE_COURSE',        $i); $i++;
define('AT_WARNING_DELETE_USER',          $i); $i++;
define('AT_WARNING_SUB_CONTENT_DELETE',   $i); $i++;
define('AT_WARNING_GLOSSARY_REMAINS',     $i); $i++;
define('AT_WARNING_GLOSSARY_REMAINS2',    $i); $i++;
define('AT_WARNING_GLOSSARY_DELETE',      $i); $i++;
define('AT_WARNING_DELETE_CONTENT',       $i); $i++;
define('AT_WARNING_DELETE_NEWS',          $i); $i++;
define('AT_WARNING_SAVE_YOUR_WORK',       $i); $i++;
define('AT_WARNING_DELETE_THREAD',        $i); $i++;
define('AT_WARNING_DELETE_MESSAGE',       $i); $i++;
define('AT_WARNING_LINK_WINDOWS',         $i); $i++;
define('AT_WARNING_AUTO_LOGIN',           $i); $i++;
define('AT_WARNING_SAVE_TEMPLATE',        $i); $i++;
define('AT_WARNING_EXPERIMENTAL11',       $i); $i++;
define('AT_WARNING_DELETE_TRACKING',      $i); $i++;
define('AT_WARNING_DELETE_TEST',          $i); $i++;
define('AT_WARNING_DELETE_RESULTS',       $i); $i++;
define('AT_WARNING_DELETE_QUESTION',      $i); $i++;
define('AT_WARNING_DELETE_CATEGORY',      $i); $i++;
define('AT_WARNING_CHAT_TRAN_EXISTS',     $i); $i++;
define('AT_WARNING_DELETE_LANG',          $i); $i++;
define('AT_WARNING_LANG_EXISTS',          $i); $i++;
define('AT_WARNING_DELETE_CAT_CATEGORY',  $i); $i++;
define('AT_WARNING_LOGIN_INSTRUCTOR',     $i); $i++;
define('AT_WARNING_DELETE_POLL',          $i); $i++;

define('AT_WARNING_REMOVE_STUDENT',       $i); $i++;
define('AT_WARNING_ENROLL_STUDENT',       $i); $i++;
define('AT_WARNING_UNENROLL_STUDENT',     $i); $i++;
define('AT_WARNING_UNENROLL_PRIV',        $i); $i++;
define('AT_WARNING_UNENROLL_PRIV',        $i); $i++;
define('AT_WARNING_ALUMNI',				  $i); $i++;

define('AT_WARNING_DELETE_THEME',         $i); $i++;
define('AT_WARNING_THEME_VERSION_DIFF',   $i); $i++;

define('AT_WARNING_DELETE_BACKUP',        $i); $i++;
define('AT_WARNING_CONFIRM_FILE_MOVE',	  $i); $i++;


//////////////////////////////////////
// FEEDBACK DEFINITIONS
define('AT_FEEDBACK_SUCCESS',               $i); $i++;
define('AT_FEEDBACK_FORUM_ADDED',           $i); $i++;
define('AT_FEEDBACK_CANCELLED',             $i); $i++;
define('AT_FEEDBACK_GLOS_UPDATED',          $i); $i++;
define('AT_FEEDBACK_GLOSSARY_DELETE2',      $i); $i++;
define('AT_FEEDBACK_REG_THANKS',            $i); $i++;
define('AT_FEEDBACK_MSG_SENT',              $i); $i++;
define('AT_FEEDBACK_MSG_DELETED',           $i); $i++;
define('AT_FEEDBACK_FILE_UPLOADED',         $i); $i++;
define('AT_FEEDBACK_FILE_UPLOADED_ZIP',     $i); $i++;
define('AT_FEEDBACK_FILE_DELETED',          $i); $i++;
define('AT_FEEDBACK_ARCHIVE_EXTRACTED',     $i); $i++;
define('AT_FEEDBACK_DIR_DELETED',           $i); $i++;
define('AT_FEEDBACK_THREAD_DELETED',        $i); $i++;
define('AT_FEEDBACK_THREAD_STARTED',        $i); $i++;
define('AT_FEEDBACK_THREAD_SUBCRIBED',      $i); $i++;
define('AT_FEEDBACK_THREAD_UNSUBCRIBED',    $i); $i++;
define('AT_FEEDBACK_THREAD_LOCKED',         $i); $i++;
define('AT_FEEDBACK_THREAD_UNLOCKED',       $i); $i++;
define('AT_FEEDBACK_STICKY_UPDATED',        $i); $i++;
define('AT_FEEDBACK_FORUM_DELETED',         $i); $i++;
define('AT_FEEDBACK_THREAD_REPLY',          $i); $i++;
define('AT_FEEDBACK_POST_EDITED',           $i); $i++;
define('AT_FEEDBACK_MESSAGE_DELETED',       $i); $i++;
define('AT_FEEDBACK_DELETE_SUCCESSFUL',     $i); $i++;
define('AT_FEEDBACK_COPYRIGHT_UPDATED',     $i); $i++;
define('AT_FEEDBACK_BANNER_UPDATED',        $i); $i++;
define('AT_FEEDBACK_DEFAULT_WRAP_TEMPLATE', $i); $i++;
define('AT_FEEDBACK_DEFAULT_CSS_LOADED',    $i); $i++;
define('AT_FEEDBACK_STYLES_UPDATED',        $i); $i++;
define('AT_FEEDBACK_COURSE_PREFS_SAVED',    $i); $i++;
define('AT_FEEDBACK_COURSE_DELETED',        $i); $i++;
define('AT_FEEDBACK_NOW_ENROLLED',          $i); $i++;
define('AT_FEEDBACK_APPROVAL_PENDING',      $i); $i++;
define('AT_FEEDBACK_COURSE_REMOVED',        $i); $i++;
define('AT_FEEDBACK_ACCOUNT_PENDING',       $i); $i++;
define('AT_FEEDBACK_TABLE_CREATED',         $i); $i++;
define('AT_FEEDBACK_USER_DELETED',          $i); $i++;
define('AT_FEEDBACK_FORUM_UPDATED',         $i); $i++;
define('AT_FEEDBACK_NEWS_UPDATED',          $i); $i++;
define('AT_FEEDBACK_NEWS_ADDED',            $i); $i++;
define('AT_FEEDBACK_CONTENT_ADDED',         $i); $i++;
define('AT_FEEDBACK_CONTENT_UPDATED',       $i); $i++;
define('AT_FEEDBACK_CONTENT_DELETED',       $i); $i++;
define('AT_FEEDBACK_NEWS_DELETED',          $i); $i++;
define('AT_FEEDBACK_COURSE_CREATED',        $i); $i++;
define('AT_FEEDBACK_COURSE_PROPERTIES',     $i); $i++;
define('AT_FEEDBACK_COURSE_DEFAULT_CSIZE',  $i); $i++;
define('AT_FEEDBACK_COURSE_DEFAULT_FSIZE',  $i); $i++;
define('AT_FEEDBACK_ENROLMENT_UPDATED',     $i); $i++;
define('AT_FEEDBACK_IMPORT_SUCCESS',        $i); $i++;
define('AT_FEEDBACK_AUTO_DISABLED',         $i); $i++;
define('AT_FEEDBACK_AUTO_ENABLED',          $i); $i++;
define('AT_FEEDBACK_PROFILE_UPDATED',       $i); $i++;
define('AT_FEEDBACK_COURSE_UPDATED',        $i); $i++;
define('AT_FEEDBACK_TRACKING_DELETED',      $i); $i++;
define('AT_FEEDBACK_TEST_DELETED',          $i); $i++;
define('AT_FEEDBACK_RESULT_DELETED',        $i); $i++;
define('AT_FEEDBACK_TEST_SAVED',            $i); $i++;
define('AT_FEEDBACK_TEST_UPDATED',          $i); $i++;
define('AT_FEEDBACK_QUESTION_UPDATED',      $i); $i++;
define('AT_FEEDBACK_QUESTION_DELETED',      $i); $i++;
define('AT_FEEDBACK_QUESTION_ADDED',        $i); $i++;
define('AT_FEEDBACK_RESULTS_UPDATED',       $i); $i++;
define('AT_FEEDBACK_TEST_ADDED',            $i); $i++;
define('AT_FEEDBACK_PREFS_CHANGED',         $i); $i++;
define('AT_FEEDBACK_APPLY_PREFS',           $i); $i++;
define('AT_FEEDBACK_APPLY_PREFS2',          $i); $i++;
define('AT_FEEDBACK_PREFS_LOGIN',           $i); $i++;
define('AT_FEEDBACK_PREFS_SAVED1',          $i); $i++;
define('AT_FEEDBACK_PREFS_SAVED2',          $i); $i++;
define('AT_FEEDBACK_PREFS_RESTORED',        $i); $i++;
define('AT_FEEDBACK_TEST_NODATA',           $i); $i++;
define('AT_FEEDBACK_CSS_UPDATED',           $i); $i++;
define('AT_FEEDBACK_CSS_PREVIEW',           $i); $i++;
define('AT_FEEDBACK_FILE_PASTED',           $i); $i++;
define('AT_FEEDBACK_FILE_EXISTS',           $i); $i++;
define('AT_FEEDBACK_FILE_OVERWRITE',        $i); $i++;
define('AT_FEEDBACK_LINK_CAT_DELETED',      $i); $i++;
define('AT_FEEDBACK_LINK_CAT_EDITED',       $i); $i++;
define('AT_FEEDBACK_EXPORT_CANCELLED',      $i); $i++;
define('AT_FEEDBACK_IMPORT_CANCELLED',      $i); $i++;
define('AT_FEEDBACK_TRAN_DELETED',          $i); $i++;
define('AT_FEEDBACK_IMPORT_LANG_SUCCESS',   $i); $i++;
define('AT_FEEDBACK_LANG_DELETED',          $i); $i++;
define('AT_FEEDBACK_CAT_DELETED',           $i); $i++;
define('AT_FEEDBACK_CAT_UPDATE_SUCCESSFUL', $i); $i++;
define('AT_FEEDBACK_CLOSED',                $i); $i++;
define('AT_FEEDBACK_CAT_ADDED',             $i); $i++;
define('AT_FEEDBACK_CONTENT_DIR_CREATED',   $i); $i++;
define('AT_FEEDBACK_LOGOUT',                $i); $i++;
define('AT_INFOS_ACCOUNT_APPROVED',         $i); $i++;
define('AT_FEEDBACK_PRIVS_CHANGED',         $i); $i++;
define('AT_FEEDBACK_POLL_ADDED',            $i); $i++;
define('AT_FEEDBACK_POLL_UPDATED',          $i); $i++;
define('AT_FEEDBACK_POLL_DELETED',          $i); $i++;
define('AT_FEEDBACK_RENAMED',               $i); $i++;

define('AT_FEEDBACK_LANG_UPDATED',          $i); $i++;
define('AT_FEEDBACK_THEME_DELETED',			$i); $i++;

define('AT_FEEDBACK_ENROLLED',			    $i); $i++;
define('AT_FEEDBACK_ALREADY_ENROLLED',      $i); $i++;
define('AT_FEEDBACK_MEMBERS_REMOVED',     $i); $i++;
define('AT_FEEDBACK_MEMBERS_ENROLLED',    $i); $i++;
define('AT_FEEDBACK_MEMBERS_UNENROLLED',  $i); $i++;


define('AT_FEEDBACK_THEME_IMPORT_SUCCESS',  $i); $i++;
define('AT_FEEDBACK_THEME_ENABLED',         $i); $i++;
define('AT_FEEDBACK_THEME_DISABLED',        $i); $i++;
define('AT_FEEDBACK_THEME_DEFAULT',         $i); $i++;
define('AT_FEEDBACK_BACKUP_DELETED',        $i); $i++;
define('AT_FEEDBACK_BACKUP_CREATED',        $i); $i++;
define('AT_FEEDBACK_BACKUP_EDIT',           $i); $i++;
define('AT_FEEDBACK_BACKUP_UPLOADED',       $i); $i++;
define('AT_FEEDBACK_FILE_SAVED',			$i); $i++;
define('AT_FEEDBACK_ACCOUNT_APPROVED',		$i); $i++;

/********************************************************/
/* INFOS DEFINITIONS									*/
define('AT_INFOS_REQUEST_ACCOUNT',          $i); $i++;
define('AT_INFOS_PRIVATE_ENROL',            $i); $i++;
define('AT_INFOS_CHOOSE_NUMBERS',           $i); $i++;
define('AT_INFOS_NO_MORE_FIELDS',           $i); $i++;
define('AT_INFOS_NO_POSTS_FOUND',           $i); $i++;
define('AT_INFOS_INBOX_EMPTY',              $i); $i++;
define('AT_INFOS_APPROVAL_PENDING',         $i); $i++;
define('AT_INFOS_ACCOUNT_PENDING',          $i); $i++;
define('AT_INFOS_NO_ENROLLMENTS',           $i); $i++;
define('AT_INFOS_GLOSSARY_REMAINS',         $i); $i++;
define('AT_INFOS_NO_TERMS',                 $i); $i++;
define('AT_INFOS_TRACKING_OFFST',           $i); $i++;
define('AT_INFOS_TRACKING_OFFIN',           $i); $i++;
define('AT_INFOS_TRACKING_NO_INST',         $i); $i++;
define('AT_INFOS_TRACKING_NO_INST1',        $i); $i++;
define('AT_INFOS_NO_CONTENT',               $i); $i++;
define('AT_INFOS_NO_PERMISSION',            $i); $i++;
define('AT_INFOS_NOT_RELEASED',             $i); $i++;
define('AT_INFOS_NO_PAGE_CONTENT',          $i); $i++;
define('AT_INFOS_NO_SEARCH_RESULTS',        $i); $i++;
define('AT_INFOS_NO_CATEGORIES',            $i); $i++;
define('AT_INFOS_OVER_QUOTA',               $i); $i++;
define('AT_INFOS_MSG_SEND_LOGIN',           $i); $i++;
define('AT_INFOS_INVALID_USER',             $i); $i++;
define('AT_INFOS_CSS_DEPRECATED',           $i); $i++;
define('AT_INFOS_CSS_DEPRECATED_DL',        $i); $i++;
define('AT_INFOS_HEADFOOT_DEPRECATED',      $i); $i++;
define('AT_INFOS_HEADFOOT_DEPRECATED_DL_H', $i); $i++;
define('AT_INFOS_HEADFOOT_DEPRECATED_DL_F', $i); $i++;
define('AT_INFOS_NOT_ENROLLED',             $i); $i++;
define('AT_INFOS_SERVICE_UNAVAILABLE',      $i); $i++;
define('AT_INFOS_INVALID_URL',              $i); $i++;
define('AT_INFOS_SAVE_CONTENT',             $i); $i++;
define('AT_INFOS_DECISION_REVERSED',        $i); $i++;
define('AT_INFOS_DECISION_NOT_REVERSED',    $i); $i++;
define('AT_INFOS_DECISION_NOT_SAVED',       $i); $i++;

define('AT_INFOS_NO_INSTRUCTORS',			$i); $i++;
define('AT_INFOS_NO_ACOLLAB',			$i); $i++;
define('AT_INFOS_404_BLURB',				$i); $i++;
define('AT_INFOS_MSG_TO_INSTRUCTOR',		$i); $i++;
define('AT_INFOS_ALREADY_ENROLLED',			$i); $i++;
define('AT_INFOS_LOGIN_TO_POST',			$i); $i++;
?>
