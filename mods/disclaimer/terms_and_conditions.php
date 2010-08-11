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
require('common.inc.php');

global $_config;

if (isset($_POST['agree'])) {
	if (isset($_POST['inreg'])) {
		header ('Location: '.AT_BASE_HREF.'registration.php?agreed_disclaimer=1');
		exit;
	}
	if (!isset($_SESSION['login']) || $_SESSION['login'] == '') {
		header ('Location: '.AT_BASE_HREF.'index.php');
		exit;
	}
	
	save_agreed_login($_SESSION['login']);
	
	if ($_SESSION['course_id'] == -1) { // admin login
		header ('Location: '.AT_BASE_HREF.'admin/index.php');
		exit;
	} else { // regular user login
		// if page variable is set, bring them there.
		if (isset($_POST['p']) && $_POST['p']!=''){
			header ('Location: '.urldecode($_POST['p']));
			exit;
		}
		
		$msg->addFeedback('LOGIN_SUCCESS');
	    header('Location: '.AT_BASE_HREF.'bounce.php?course='.$_POST['form_course_id']);
		exit;
	}
} else if (isset($_POST['disagree'])) {
	$_SESSION = array();   // destroy all session vars set in login.php
	header('Location: '.$_config['tac_link']);
	exit;
}

$savant->assign('site_name', $_config['site_name']);
$savant->assign('theme', $_SESSION['prefs']['PREF_THEME']);
$savant->assign('base_href', $_base_href);
$savant->assign('body_text', $_config['tac_body']);
$savant->display('terms_and_conditions.tmpl.php');
?>