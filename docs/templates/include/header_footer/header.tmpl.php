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
	<base href="<?php echo $tmpl_base_href; ?>" />
	<link rel="stylesheet" href="basic_styles.css" type="text/css" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
	<?php echo $tmpl_rtl_css; ?>
</head>
<body <?php echo $tmpl_onload; ?> >
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="bodyline" summary="">
<tr>
	<td style="background-image: url('<?php echo HEADER_IMAGE; ?>'); background-repeat: no-repeat; background-position: 0px 0px;" nowrap="nowrap" align="right" valign="top"><br />
			<?php if (HEADER_LOGO): ?>
				<img src="<?php echo HEADER_LOGO ?>" border="0" alt="<?php echo SITE_NAME ?>" />&nbsp;
			<?php endif; ?>
			<h4><?php echo stripslashes(SITE_NAME); ?>&nbsp;</h4><br /></td>
</tr>
<tr>
	<td>