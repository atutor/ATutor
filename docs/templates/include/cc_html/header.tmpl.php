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
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $available_languages[$_SESSION['lang']][1]; ?>" />
	<meta name="Generator" content="ATutor - Copyright 2004 by http://atutor.ca" />
	<title><?php echo stripslashes(SITE_NAME); ?></title>
	<base href="<?php echo $tmpl_base_href; ?>" />
	<link rel="stylesheet" href="stylesheet.css" type="text/css" />
	<link rel="shortcut icon" href="<?php echo $_base_path; ?>favicon.ico" type="image/x-icon" />
	<?php echo $tmpl_css; ?>
	<?php echo $tmpl_rtl_css; ?>
</head>
<body <?php echo (isset($errors) ? '' : $onload); ?> <?php echo (isset($onload) ? $online : ''); ?> ><div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div><script language="JavaScript" src="overlib.js" type="text/javascript"><!-- overLIB (c) Erik Bosrup --></script>
<table width="98%" align="center" cellpadding="0" cellspacing="0" class="bodyline" summary="">
<tr>
	<td colspan="6"><table width="100%" border="0" cellspacing="0" cellpadding="0" summary="">
	<?php require(AT_INCLUDE_PATH.'html/user_bar.inc.php'); ?>
<tr><td colspan="2" class="row3" height="1"><img src="images/clr.gif" height="1" width="1" alt="" /></td></tr>
</table>
<table width="98%" align="center" cellpadding="5" cellspacing="0" summary="">
<tr>
	<td valign="bottom" nowrap="nowrap"><h2><?php echo _AT('control_centre');  ?></h2></td>
	<td valign="bottom"><h3><?php echo $title ?></h3></td>
</tr>
<tr>
	<td valign="top"><table width="100%" class="bodyline" summary="">
		<tr>
			<td valign="top" class="cc_menu"><a name="menu"></a><img src="images/home.jpg" height="15" width="16" class="menuimage17" alt="<?php echo _AT('home'); ?>" /> <a href="users/index.php"><?php echo _AT('home'); ?></a><br />
			<img src="images/profile.jpg" class="menuimage17" height="15" width="16" alt="<?php echo _AT('profile'); ?>" /> <a href="users/edit.php"><?php echo _AT('profile'); ?></a><br />
			<img src="images/browse.gif" height="14" width="16" style="	height:1.10em;width:1.26em;" alt="<?php echo _AT('browse_courses'); ?>" /> <a href="users/browse.php"><?php echo _AT('browse_courses'); ?></a><br />
			<hr />

			<?php if ($tmpl_is_instructor): ?>
				<img src="images/create.jpg" height="15" width="16" class="menuimage17" alt="<?php echo _AT('create_course'); ?>" /> <a href="users/create_course.php"><?php echo _AT('create_course'); ?></a>
				<br />
				<hr />
			<?php endif; ?>
			<img src="images/logout.gif" alt="<?php echo _AT('logout'); ?>" height="15" width="16" class="menuimage17" /> <a href="logout.php"><?php echo _AT('logout'); ?></a><br /></td>
		</tr>
	</table>
	</td>
	<td width="100%" valign="top">