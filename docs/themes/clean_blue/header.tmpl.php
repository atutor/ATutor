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
	<td>

<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" summary="">

<tr class="header-bg">
	<td align="left"><?php echo $tmpl_bypass_links; ?>
		<?php if (HEADER_LOGO): ?>
			&nbsp;<img src="<?php echo $tmpl_base_path.HEADER_LOGO; ?>" class="img" border="0" alt="<?php echo SITE_NAME; ?>" />&nbsp;
		<?php endif; ?>
			
		<strong><?php echo stripslashes(SITE_NAME);
		if ($tmpl_section !='') { echo ' - '.$tmpl_section; } 
		?></strong>
	</td>
	<td valign="bottom" align="right">
		<small>
		<?php echo $tmpl_current_date; ?>&nbsp;<br />
		<?php echo _AT('logged_in_as'); ?>: <?php echo $tmpl_user_name; ?>&nbsp; / <?php echo $tmpl_log_link; ?></small>&nbsp;<br />
	</td>
</tr>
<?php if ($tmpl_user_nav): ?>
<tr class="header-bg">
	<td colspan="2">
	<!-- page top navigation links: -->
	<table border="0" cellspacing="0" cellpadding="0" align="left" width="100%">
		<tr>			
			<?php foreach ($tmpl_user_nav as $page => $link): ?>
				<?php if ($page == 'jump_menu'): ?>
					
					<!-- course select drop down -->
					<td align="right" valign="middle" class="navmenu" width="100%"
					<?php if (!$tmpl_course_nav): ?>
						style="border-bottom: 1px #98AAB1 solid">
					<?php else: ?>
						> 
					<?php endif; ?>
					<form method="post" action="<?php echo $tmpl_base_path; ?>bounce.php?p=<?php echo urlencode($tmpl_rel_url); ?>" target="_top"><label for="jumpmenu" accesskey="j"></label>
						<select name="course" id="jumpmenu" title="<?php echo _AT('jump'); ?>:  ALT-j">
							<option value="0"><?php echo _AT('my_courses'); ?></option>
							<?php if ($_SESSION['valid_user']): ?>								
								<optgroup label="<?php echo _AT('courses_below'); ?>">
									<?php foreach ($tmpl_nav_courses as $this_course_id => $this_course_title): ?>
										<?php if ($this_course_id == $_SESSION['course_id']): ?>
											<option value="<?php echo $this_course_id; ?>" selected="selected"><?php echo $this_course_title; ?></option>
										<?php else: ?>
											<option value="<?php echo $this_course_id; ?>"><?php echo $this_course_title; ?></option>
										<?php endif; ?>
									<?php endforeach; ?>
								</optgroup>
							<?php endif; ?>
						</select>&nbsp;<input type="submit" name="jump" value="<?php echo _AT('jump'); ?>" id="jump-button" /><input type="hidden" name="g" value="22" /></form></td>
					<!-- end course select drop down -->

				<?php else: ?>
					<!-- regular menu item 	-->		
					<?php if ($tmpl_page == $page): ?>
						<td valign="top" class="navmenu selected" nowrap="nowrap" 
					<?php else: ?>
						<td valign="top" class="navmenu"  nowrap="nowrap" 
					<?php endif; ?>

					<?php if (!$tmpl_course_nav): ?>
						style="border-bottom: 1px #98AAB1 solid;">
					<?php else: ?>
						>
					<?php endif; ?>
					
					<?php if (!$tmpl_main_text_only && $link['image']): ?>
						<a href="<?php echo $link['url']; ?>" <?php echo $link['attributes']; ?>><img src="<?php echo $link['image']; ?>" alt="<?php echo $link['name']; ?>" title="<?php echo $link['name']; ?>" class="img" border="0" /></a>
					<?php endif; ?>
					<?php if (!$tmpl_main_icons_only): ?>
						<small><a href="<?php echo $link['url'] ?>" <?php echo $link['attributes']; ?>><?php echo $link['name'] ?></a></small>
					<?php endif; ?>	
					
					</td>							
					<!-- end regular menu item -->
				<?php endif; ?>
			<?php endforeach; ?>

			<?php if (!$tmpl_course_nav): ?>
				<td align="right" valign="middle" class="navmenu" width="100%" style="border-bottom: 1px #98AAB1 solid">&nbsp;</td>
			<?php endif; ?>
		</tr>
		</table>
	</td>
</tr>
<?php endif; ?>

<!-- admin navigation -->
<?php if ($tmpl_admin_nav): ?>
	<tr class="header-bg">
		<td colspan="2"><a name="navigation"></a><br />
		<table border="0" cellspacing="0" cellpadding="0">
			<tr>			
				<td class="course-nav-between">&nbsp;</td>
				<?php foreach ($tmpl_admin_nav as $page => $link): ?>
					<!-- regular menu item -->					
					<?php if ($tmpl_page == $page): ?>
						<td valign="top" nowrap="nowrap" class="admin-nav-item selected" width="80">
					<?php else: ?>
						<td valign="top" nowrap="nowrap" class="admin-nav-item" width="80">				
					<?php endif; ?>
						<a href="<?php echo $link['url']; ?>" <?php echo $link['attribs']; ?> title="<?php echo $link['title']; ?>" ><?php echo $link['name'] ?></a>
					
					</td>
					<td class="course-nav-between">&nbsp;</td>
					<!-- end regular menu item -->					
				<?php endforeach; ?>
				<td align="right" class="course-nav-between" width="100%">&nbsp;</td>
			</tr>
		</table>
		</td>
	</tr>
<?php endif; ?>
<!-- end admin navigation -->

<!-- course navigation elements: ( course nav links ) -->
<?php if ($tmpl_course_nav): ?>
	<tr>
		<td  colspan="2"><a name="navigation"></a>
		<!-- course navigation links: -->
		<table border="0" cellspacing="0" cellpadding="0">
			<tr>			
				<td class="course-nav-between">&nbsp;</td>
				<?php foreach ($tmpl_course_nav as $page => $link): ?>
					<!-- regular menu item -->					
					<?php if ($tmpl_page == $page): ?>
						<td valign="top" nowrap="nowrap" class="course-nav-item selected" width="80">
					<?php else: ?>
						<td valign="top" nowrap="nowrap" class="course-nav-item" width="80">				
					<?php endif; ?>
						<a href="<?php echo $link['url']; ?>" <?php echo $link['attribs']; ?> title="<?php echo $link['title']; ?>" ><?php echo $link['name'] ?></a>
					
					</td>
					<td class="course-nav-between">&nbsp;</td>
					<!-- end regular menu item -->					
				<?php endforeach; ?>
				<td align="right" class="course-nav-between" width="100%">&nbsp;</td>
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
	<td valign="middle" class="breadcrumbs"  colspan="2">
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

</table>
</td></tr>
<tr>
	<td colspan="2"><a name="content"></a>