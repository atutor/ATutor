<?php
/***********************************************************************/
/* ATutor                                                              */
/***********************************************************************/
/* Copyright (c) 2002-2010                                             */
/* Inclusive Design Institute                                          */
/* http://atutor.ca                                                    */
/*                                                                     */
/* This program is free software. You can redistribute it and/or       */
/* modify it under the terms of the GNU General Public License         */
/* as published by the Free Software Foundation.                       */
/***********************************************************************/
// $Id$

$_user_location    = 'public';
define('AT_INCLUDE_PATH', 'include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
include(AT_INCLUDE_PATH.'login_functions.inc.php');

unset($_SESSION['login']);
unset($_SESSION['valid_user']);
unset($_SESSION['member_id']);
unset($_SESSION['is_admin']);
unset($_SESSION['course_id']);
unset($_SESSION['is_super_admin']);
unset($_SESSION['dd_question_ids']);

$_SESSION['prefs']['PREF_FORM_FOCUS'] = 1;

/*****************************/
/* template starts down here */

$onload = 'document.form.form_login.focus();';

$savant->assign('form_course_id', $_GET['course']);

if (isset($_GET['course']) && $_GET['course']) {
    $savant->assign('title',  ' '._AT('to1').' '.$system_courses[$_GET['course']]['title']);
} else {
    $savant->assign('title',  ' ');
}

header('P3P: CP="IDC DSP COR CURa ADMa OUR IND PHY ONL COM STA"');
$savant->display('login.tmpl.php');
?>
