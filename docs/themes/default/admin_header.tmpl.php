<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
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
	<base href="<?php echo $tmpl_content_base_href; ?>" />
	<link rel="shortcut icon" href="<?php echo $tmpl_base_path; ?>favicon.ico" type="image/x-icon" />
	<link rel="stylesheet" href="<?php echo $tmpl_base_path; ?>print.css" type="text/css" media="print" />
	<link rel="stylesheet" href="<?php echo $tmpl_base_path.'themes/'.$tmpl_theme; ?>/styles.css" type="text/css" />
	<?php echo $tmpl_rtl_css; ?>
	<style type="text/css"><?php echo $tmpl_banner_style; ?></style>
</head>
<body <?php echo $tmpl_onload; ?>><div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
<script language="JavaScript" src="<?php echo $tmpl_base_path; ?>overlib.js" type="text/javascript"><!-- overLIB (c) Erik Bosrup --></script>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" id="maintable" summary="">
<tr>
	<td style="background-image: url('<?php echo $tmpl_base_path . HEADER_IMAGE; ?>'); background-repeat: no-repeat; background-position: 0px 0px;" nowrap="nowrap" align="right" valign="top">
		<table border="0" align="right" cellpadding="0" cellspacing="0" summary="">
			<tr>
				<td align="right"><?php echo $tmpl_bypass_links; ?><br /><br />
					<?php if (HEADER_LOGO): ?>
						<img src="<?php echo $tmpl_base_path.HEADER_LOGO; ?>" border="0" alt="<?php echo SITE_NAME; ?>" />&nbsp;
					<?php endif; ?>
					<br /><h4><?php echo stripslashes(SITE_NAME); ?>&nbsp;</h4><br />
				</td>
				<td align="left" class="login-box">
					» <small><?php echo _AT('logged_in_as'); ?>: <?php echo $tmpl_user_name; ?>&nbsp;<br /></small>
					» <small><?php echo $tmpl_log_link; ?></small>
				</td>
			</tr>	
		</table>
	</td>
</tr>
<tr>
	<td class="cyan">
	<!-- page top navigation links: -->
	<table border="0" cellspacing="0" cellpadding="0" align="right" class="navmenu">
		<tr>			
			<?php foreach ($tmpl_user_nav as $page => $link): ?>
				<?php if ($page == 'jump_menu'): ?>
					
					<!-- course select drop down -->
					<td align="right" valign="middle" class="navmenu"><form method="post" action="<?php echo $tmpl_base_path; ?>bounce.php" target="_top"><label for="jumpmenu" accesskey="j"></label>
						<select name="course" id="jumpmenu" title="<?php echo _AT('jump'); ?>:  ALT-j">
							<option value="0"><?php echo _AT('my_courses'); ?></option>
							<optgroup label="<?php echo _AT('courses_below'); ?>">
								<?php foreach ($tmpl_nav_courses as $course): ?>
									<?php if ($course['course_id'] == $_SESSION['course_id']): ?>
										<option value="<?php echo $course['course_id']; ?>" selected="selected"><?php echo $course['title']; ?></option>
									<?php else: ?>
										<option value="<?php echo $course['course_id']; ?>"><?php echo $course['title']; ?></option>
									<?php endif; ?>
								<?php endforeach; ?>
							</optgroup>
						</select>&nbsp;<input type="submit" name="jump" value="<?php echo _AT('jump'); ?>" id="jump-button" /><input type="hidden" name="g" value="22" /></form></td>
					<!-- end course select drop down -->

				<?php else: ?>

					<!-- regular menu item -->			

					<?php if ($tmpl_page == $page): ?>
						<td valign="middle" class="navmenu selected"  onclick="window.location.href='<?php echo $link['url'];?>'">
						<?php if (!$tmpl_main_text_only && $link['image']): ?>
							<a href="<?php echo $link['url']; ?>" <?php echo $link['attributes']; ?>><img src="<?php echo $link['image']; ?>" alt="<?php echo $link['name']; ?>" title="<?php echo $link['name']; ?>" class="menuimage17" border="0" /></a>
						<?php endif; ?>
						<?php if (!$tmpl_main_icons_only): ?>
							<small><a href="<?php echo $link['url'] ?>" <?php echo $link['attributes']; ?>><?php echo $link['name'] ?></a></small>
						<?php endif; ?>	
						
						</td>

					<?php else: ?>
						<td valign="middle" class="navmenu" onmouseover="this.className='navmenu selected';" onmouseout="this.className='navmenu';"  onclick="window.location.href='<?php echo $link['url'];?>'">
						<?php if (!$tmpl_main_text_only && $link['image']): ?>
							<a href="<?php echo $link['url']; ?>" <?php echo $link['attributes']; ?>><img src="<?php echo $link['image'] ?>" alt="<?php echo $link['name']; ?>" title="<?php echo $link['name']; ?>" class="menuimage17" border="0" /></a>
						<?php endif; ?>
						<?php if (!$tmpl_main_icons_only): ?>
							<small><a href="<?php echo $link['url'] ?>" <?php echo $link['attributes']; ?>><?php echo $link['name'] ?></a></small>
						<?php endif; ?>	
						</td>
					<?php endif; ?>

					<!-- end regular menu item -->

				<?php endif; ?>
			<?php endforeach; ?>
		</tr>
		</table></td>
</tr>
<tr>
	<td><a name="content"></a>