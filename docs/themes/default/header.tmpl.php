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
	<td id="top-heading" style="background-image: url('<?php echo $tmpl_base_path . HEADER_IMAGE; ?>'); background-repeat: no-repeat; background-position: 0px 0px;" nowrap="nowrap" align="right" valign="top">
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
					<td align="right" valign="middle" class="navmenu"><form method="post" action="<?php echo $tmpl_base_path; ?>bounce.php?p=<?php echo urlencode($tmpl_rel_url); ?>" target="_top"><label for="jumpmenu" accesskey="j"></label>
						<select name="course" id="jumpmenu" title="<?php echo _AT('jump'); ?>:  ALT-j">
							<option value="0"><?php echo _AT('my_courses'); ?></option>
							<optgroup label="<?php echo _AT('courses_below'); ?>">
								<?php foreach ($tmpl_nav_courses as $this_course_id => $this_course_title): ?>
									<?php if ($this_course_id == $_SESSION['course_id']): ?>
										<option value="<?php echo $this_course_id; ?>" selected="selected"><?php echo $this_course_title; ?></option>
									<?php else: ?>
										<option value="<?php echo $this_course_id; ?>"><?php echo $this_course_title; ?></option>
									<?php endif; ?>
								<?php endforeach; ?>
							</optgroup>
						</select>&nbsp;<input type="submit" name="jump" value="<?php echo _AT('jump'); ?>" id="jump-button" /><input type="hidden" name="g" value="22" /></form></td>
					<!-- end course select drop down -->

				<?php else: ?>

					<!-- regular menu item -->			

					<?php if ($tmpl_page == $page): ?>
						<td valign="middle" class="navmenu selected">
						<?php if (!$tmpl_main_text_only && $link['image']): ?>
							<a href="<?php echo $link['url']; ?>" <?php echo $link['attributes']; ?>><img src="<?php echo $link['image']; ?>" alt="<?php echo $link['name']; ?>" title="<?php echo $link['name']; ?>" class="menuimage17" border="0" /></a>
						<?php endif; ?>
						<?php if (!$tmpl_main_icons_only): ?>
							<small><a href="<?php echo $link['url'] ?>" <?php echo $link['attributes']; ?>><?php echo $link['name'] ?></a></small>
						<?php endif; ?>	
						
						</td>

					<?php else: ?>
						<td valign="middle" class="navmenu" onmouseover="this.className='navmenu selected';" onmouseout="this.className='navmenu';">
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
<!-- the course banner (or section title) -->
	<tr> 
		<td id="course-banner"><?php echo $tmpl_section; ?></td>
	</tr>
<!-- end course banner -->

<!-- course navigation elements: ( course nav links, instructor nav links) -->
<?php if ($tmpl_course_nav): ?>
	<tr>
		<td id="course-nav"><a name="navigation"></a>
		<!-- course navigation links: -->
		<table border="0" cellspacing="0" cellpadding="0" align="center">
			<tr>			
				<?php foreach ($tmpl_course_nav as $page => $link): ?>
					<!-- regular menu item -->					
					<td class="cat2" valign="top" nowrap="nowrap">				
					<?php if (!$tmpl_course_text_only): ?>
						<a href="<?php echo $link['url']; ?>" <?php echo $link['attribs']; ?>><img src="<?php echo $link['image'] ?>" alt="<?php echo $link['title']; ?>" title="<?php echo $link['title']; ?>" class="menuimage" border="0" /></a>
					<?php endif; ?>
					<?php if (!$tmpl_course_icons_only): ?>
						<small><a href="<?php echo $link['url']; ?>" <?php echo $link['attribs']; ?> title="<?php echo $link['title']; ?>" ><?php echo $link['name'] ?></a></small>
					<?php endif; ?>
					</td>
					<td width="10"></td>
					<!-- end regular menu item -->
				<?php endforeach; ?>
			</tr>
		</table>
		<!-- end course navigation links -->
		</td>
	</tr>
<?php endif; ?>
<!-- end course navigation elements -->
<!-- the breadcrumb navigation -->
<?php if ($tmpl_breadcrumbs): ?>
	<tr>
		<td valign="middle" class="breadcrumbs">
				<?php foreach($tmpl_breadcrumbs as $item): ?>
					<?php if ($item['link']): ?>
						<a href="<?php echo $item['link']; ?>" class="breadcrumbs"><?php echo $item['title']; ?></a> » 
					<?php else: ?>
						<!-- the last item in the list is not a link. current location -->
						<?php echo $item['title']; ?>
					<?php endif; ?>
				<?php endforeach; ?>
		</td>
	</tr>
<?php endif; ?>
<!-- end the breadcrumb navigation -->
<tr>
	<td><a name="content"></a>