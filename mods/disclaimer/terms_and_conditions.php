<?php
/****************************************************************/
/* ATutor                                                       */
/****************************************************************/
/* Copyright (c) 2002-2008										*/
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca                                             */
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: terms_and_conditions.php 8319 2009-03-03 16:38:19Z hwong $
$_user_location	= 'public';
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

if (isset($_POST['agree'])) {
	$_SESSION['agree_terms_and_conditions'] = 1;
	header('Location: '.AT_BASE_HREF.'login.php');
	exit;
}

$savant->assign('site_name', $_config['site_name']);
$savant->assign('theme', $_SESSION['prefs']['PREF_THEME']);
$savant->assign('base_href', $_base_href);
$savant->assign('tac_link', $_config['tac_link']);
$savant->assign('body_text', $_config['tac_body']);
$savant->display('terms_and_conditions.tmpl.php');
?>