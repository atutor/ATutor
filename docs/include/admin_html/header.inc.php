<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
if (!defined('AT_INCLUDE_PATH')) { exit; }

global $available_languages;
global $_rtl_languages;
global $page;
global $savant;
global $onload;

$savant->assign('tmpl_lang',	$available_languages[$_SESSION['lang']][2]);
$savant->assign('tmpl_title',	stripslashes(SITE_NAME).' '._AT('administration'));
$savant->assign('tmpl_charset', $available_languages[$_SESSION['lang']][1]);
$savant->assign('tmpl_base_href', $_base_href);

if (in_array($_SESSION['lang'], $_rtl_languages)) {
	$savant->assign('tmpl_rtl_css', '<link rel="stylesheet" href="'.$_base_path.'rtl.css" type="text/css" />');
} else {
	$savant->assign('tmpl_rtl_css', '');
}

if (!isset($errors) && $onload) {
	$savant->assign('tmpl_onload', $onload);
}

$savant->assign('tmpl_page', $page);

header('Content-Type: text/html; charset='.$available_languages[$_SESSION['lang']][1]);
$savant->display('include/admin_html/header.tmpl.php');

?>