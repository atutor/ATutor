<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay, Joel Kronenberg, Heidi Hazelton	*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/

if ($step != 6) {
	require('../include/config.inc.php');
	if (defined('AT_INSTALL') && false) {
		echo 'ATutor appear to have been installed already.';
		exit;
	}
}

$install_steps[0] = array(	'name' => 'Introduction',
							'file' => 'index.php');

$install_steps[1] = array(	'name' => 'Terms of Use',
							'file' => 'step1.php');

$install_steps[2] = array(	'name' => 'Database',
							'file' => 'step2.php');

$install_steps[3] = array(	'name' => 'Preferences');

$install_steps[4] = array(	'name' => 'Administrator Account',
							'file' => 'step4.php');

$install_steps[5] = array(	'name' => 'Directories');

//$install_steps[6] = array(	'name' => 'Languages');

$install_steps[6] = array(	'name' => 'Done!',
							'file' => 'step5.php');


?><!doctype html public "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title> Atutor Install </title>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<link rel="stylesheet" href="stylesheet.css" type="text/css" />
</head>

<body>
<table height="100%" width="100%" cellpadding="0" cellspacing="0" border="0">
<tr height="50" bgcolor="#354A81">
	<td><h2 class="header">ATutor <small>v</small><?php echo $new_version; ?> Installation</h2></td>
	<td align="right" valign="middle"><img src="../images/logo.gif">&nbsp;&nbsp;&nbsp;</td>
</tr>
<tr>
	<td colspan="2" valign="top" class="content">