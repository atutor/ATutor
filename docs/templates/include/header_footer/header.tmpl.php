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
	<link rel="shortcut icon" href="<?php echo $tmpl_base_path; ?>favicon.ico" type="image/x-icon" />

	<link rel="stylesheet" href="<?php echo $tmpl_base_path; ?>stylesheet.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $tmpl_base_path; ?>print.css" type="text/css" media="print" />
	<link rel="stylesheet" href="<?php echo $tmpl_base_path; ?>basic_styles.css" type="text/css" />
	<?php echo $tmpl_rtl_css; ?>
</head>
<body <?php echo $tmpl_onload; ?> ><a href="#content" accesskey="c"><img src="images/clr.gif" height="1" width="1" border="0" alt="<?php echo _AT('goto_content'); ?>: ALT-c" /></a><table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" id="maintable" summary="">
<tr>
	<td style="background-image: url('<?php echo $tmpl_base_path . HEADER_IMAGE; ?>'); background-repeat: no-repeat; background-position: 0px 0px;" nowrap="nowrap" align="right" valign="top"><br />
			<?php if (HEADER_LOGO): ?>
				<img src="<?php echo $tmpl_base_path . HEADER_LOGO ?>" border="0" alt="<?php echo SITE_NAME ?>" />&nbsp;
			<?php endif; ?>
			<h4><?php echo stripslashes(SITE_NAME); ?>&nbsp;</h4><br /></td>
</tr>
<tr>
	<td class="cyan">
	<!-- page top navigation links: -->
	<table border="0" cellspacing="0" cellpadding="0" align="right" class="navmenu">
		<tr>
			<?php foreach ($tmpl_nav as $link): ?>
				<?php if ($link['name'] == 'jump_menu'): ?>
					<td align="right" valign="middle" class="navmenu"><form method="post" action="bounce.php" target="_top"><label for="jumpmenu" accesskey="j"></label>
						<select name="course" id="jumpmenu" title="Jump:  ALT-j">
							<option value="0"><?php echo _AT('my_control_centre'); ?></option>

						</select>&nbsp;<input type="submit" name="jump" value="<?php echo _AT('jump'); ?>" id="jump-button" /><input type="hidden" name="g" value="22" /></form></td>				
				<?php else: ?>
					<?php if ($tmpl_page == $link['page']): ?>
						<td align="right" valign="middle" class="navmenu selected"><a href="<?php echo $link['url'] ?>" id="<?php echo $link['id']; ?>"><?php echo $link['name'] ?></a></td>
					<?php else: ?>
						<td align="right" valign="middle" class="navmenu"><a href="<?php echo $link['url'] ?>" id="<?php echo $link['id']; ?>"><?php echo $link['name'] ?></a></td>
					<?php endif; ?>
				<?php endif; ?>
			<?php endforeach; ?>
		</tr>
		</table></td>
</tr>
<?php if ($tmpl_user_nav): ?>
	<tr>
		<td id="user-bar" align="right">
		<table border="0" cellspacing="0" cellpadding="0" align="right">
			<tr>
				<?php foreach ($tmpl_user_nav as $link): ?>
					<td align="right" valign="middle" class="usernavmenu"><a href="<?php echo $link['url'] ?>" id="<?php echo $link['id']; ?>"><?php echo $link['name'] ?></a></td>
				<?php endforeach; ?>
			</tr>
			</table></td>
	</tr>
<?php endif; ?>
<tr>
	<td><a name="content"></a><?php if ($tmpl_section): ?>
								<h2><?php echo $tmpl_section; ?></h2>
							  <?php endif; ?>