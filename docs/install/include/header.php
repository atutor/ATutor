<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* http://atutor.ca                                                     */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
if (!defined('AT_INCLUDE_PATH')) { exit; }
error_reporting(E_ALL ^ E_NOTICE);

if ($step < 4) {
	error_reporting(0);
	include('../include/config.inc.php');
	error_reporting(E_ALL ^ E_NOTICE);
	if (defined('AT_INSTALL')) {
		include_once(AT_INCLUDE_PATH.'common.inc.php');
		echo print_meta_redirect();
		exit;
	}
}

$install_steps[0] = array('name' => 'Introduction');
$install_steps[1] = array('name' => 'Terms of Use');
$install_steps[2] = array('name' => 'Database');
$install_steps[3] = array('name' => 'Accounts &amp; Preferences');
$install_steps[4] = array('name' => 'Content Directory');
$install_steps[5] = array('name' => 'Save Configuration');
$install_steps[6] = array('name' => 'Anonymous Usage Collection');
$install_steps[7] = array('name' => 'Done!');

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="utf-8"> 
<head>
	<title>ATutor Installation</title>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
	<link rel="stylesheet" href="stylesheet.css" type="text/css" />
</head>
<body>
<div style="height: 70px; vertical-align: bottom; background-color: #354A81">
	<h1 id="header">ATutor <?php echo $new_version; ?> Installation</h1>
	<img src="../images/AT_Logo_1_sm.png"  alt="ATutor Logo" id="logo" />
</div>
<div class="content">