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

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="<?php echo $tmpl_lang; ?>">
<head>
	<title><?php echo $tmpl_title; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $tmpl_charset; ?>" />
	<meta name="Generator" content="ATutor - Copyright 2004 by http://atutor.ca" />
	<base href="<?php echo $tmpl_base_href; ?>" />
	<link rel="stylesheet" href="stylesheet.css" type="text/css" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
	<?php echo $tmpl_rtl_css; ?>
</head>
<body <?php echo $tmpl_onload; ?> >

<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
<script language="JavaScript" src="overlib.js" type="text/javascript"><!-- overLIB (c) Erik Bosrup --></script>
<table width="98%" align="center" cellpadding="0" cellspacing="0" class="bodyline" summary="">
	<tr>
	<td colspan="6">
<table width="100%" border="0" cellspacing="0" cellpadding="0" summary="">
<tr>
	<td colspan="2" class="topbar" valign="middle">
	<strong><?php echo $tmpl_title; ?></strong></td>
</tr>
<tr><td colspan="2" class="row3" height="1"><img src="images/clr.gif" height="1" width="1" alt="" /></td></tr>
</table>

<table width="98%" align="center" cellpadding="5" cellspacing="0" summary="">
<tr>
	<td valign="top">
	<br /><table width="100%" class="bodyline">
		<tr><td valign="top" class="cc_menu" nowrap="nowrap">
			<a href="admin/"><?php echo _AT('home'); ?></a><br />
			<a href="admin/users.php"><?php echo _AT('users'); ?></a> <br />
			<a href="admin/courses.php"><?php echo _AT('courses'); ?></a><br />
			<a href="admin/course_categories.php"><?php echo _AT('cats_course_categories');?></a><br />
			<a href="admin/language.php"><?php echo _AT('language'); ?></a><br />
			<a href="admin/config_info.php"><?php echo _AT('server_configuration'); ?></a><br />
			<hr />
			<img src="images/logout.gif" alt="<?php echo _AT('logout'); ?>" height="15" width="16" class="menuimage17" /> <a href="logout.php"><?php echo _AT('logout'); ?></a> <br />
			</td>
		</tr>
	</table>	
	</td>
	<td valign="top" width="100%"><a name="content"></a>
