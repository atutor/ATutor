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

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="<?php echo $tmpl_lang; ?>">
<head>
	<title><?php echo $tmpl_title; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $tmpl_charset; ?>" />
	<meta name="Generator" content="ATutor - Copyright 2004 by http://atutor.ca" />
	<link rel="stylesheet" href="basic_styles.css" type="text/css" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
	<?php echo $tmpl_rtl_css; ?>
</head>
<body <?php echo $tmpl_onload; ?> >
<br />
<table width="98%" align="center" cellpadding="0" cellspacing="0" class="bodyline" summary="">
	<tr>
		<td colspan="6" align="center"><table bgcolor="#FFFFFF" cellpadding="0" cellspacing="0" width="100%" style="background-image: url('<?php echo HEADER_IMAGE; ?>'); background-repeat: no-repeat; background-position: 0px 0px;" summary="">
			<tr>
				<td width="30%"></td>
				<td width="0" height="80" nowrap="nowrap" align="right" valign="top"><br />
				<?php if (HEADER_LOGO): ?>
					<img src="<?php echo HEADER_LOGO ?>" border="0" alt="<?php echo SITE_NAME ?>" />&nbsp;
				<?php endif; ?>
				<h4><?php echo stripslashes(SITE_NAME); ?>&nbsp;</h4></td>
			</tr>
			<tr><td colspan="2">
			<table width="100%" cellpadding="0" cellspacing="0" summary="">
			<tr>
				<td class="cyan" align="right" valign="middle">
<!-- the top navigation links -->
				<?php if ($tmpl_page == HOME_URL && HOME_URL !='') : ?>
					<u><?php echo _AT('home') ?></u> | 
				<?php elseif (HOME_URL!=''): ?>
					<a class="cyan" href="<?php echo HOME_URL ?>"><?php echo _AT('home') ?></a> | 
				<?php endif; ?>
				
				<?php if ($tmpl_page == 'register'): ?>
					<u><?php echo _AT('register') ?></u>
				<?php else: ?>
					<a class="cyan" href="registration.php"><?php echo _AT('register') ?></a>
				<?php endif; ?>
				|
				<?php if ($tmpl_page == 'browse'): ?>
					<u><?php echo _AT('browse_courses') ?></u>
				<?php else: ?>
					<a class="cyan" href="browse.php"><?php echo _AT('browse_courses') ?></a>
				<?php endif; ?>
				|
				<?php if ($tmpl_page == 'login'): ?>
					<u><?php echo _AT('login') ?></u>
				<?php else: ?>
					<a class="cyan" href="login.php"><?php echo _AT('login') ?></a>
				<?php endif; ?>
				|
				<?php if ($tmpl_page == 'password'): ?>
					<u><?php echo _AT('password_reminder') ?></u>
				<?php else: ?>
					<a class="cyan" href="password_reminder.php"><?php echo _AT('password_reminder') ?></a>
				<?php endif; ?>
<!-- /the top navigation links --></td>
			</tr>
			</table>
			</td>
		</tr>
		</table></td>
	</tr>
	<tr>
	<td valign="top"><table width="100%" summary="">
		<tr><td valign="top">