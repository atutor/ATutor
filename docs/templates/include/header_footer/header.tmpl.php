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

	<!-- link rel="stylesheet" href="<?php echo $tmpl_base_path; ?>stylesheet.css" type="text/css" / -->
	<link rel="stylesheet" href="<?php echo $tmpl_base_path; ?>print.css" type="text/css" media="print" />
	<link rel="stylesheet" href="<?php echo $tmpl_base_path; ?>basic_styles.css" type="text/css" />
	<?php echo $tmpl_rtl_css; ?>
	<?php echo $tmpl_nav_images_css; ?>
	<style type="text/css">
		<?php echo $tmpl_banner_style; ?>
	</style>
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
			<td align="right" valign="middle" class="navmenu borderless"><small><?php echo _AT('logged_in_as'); ?>: <?php echo $tmpl_user_name; ?> </small></td>
			<?php foreach ($tmpl_nav as $link): ?>
				<?php if ($link['name'] == 'jump_menu'): ?>
					
					<!-- course select drop down -->

					<td align="right" valign="middle" class="navmenu"><form method="post" action="bounce.php" target="_top"><label for="jumpmenu" accesskey="j"></label>
						<select name="course" id="jumpmenu" title="Jump:  ALT-j">
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
				
					<?php if ($tmpl_page == $link['page']): ?>
						<td align="right" valign="middle" class="navmenu selected"><small><a href="<?php echo $link['url'] ?>" id="<?php echo $link['id']; ?>"><?php echo $link['name'] ?></a></small></td>
					<?php else: ?>
						<td align="right" valign="middle" class="navmenu" onmouseover="this.className='navmenu selected';" onmouseout="this.className='navmenu';"><small><a href="<?php echo $link['url'] ?>" id="<?php echo $link['id']; ?>"><?php echo $link['name'] ?></a></small></td>
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
		<td class="cat2c">
		<!-- course navigation links: -->
		<table border="0" cellspacing="0" cellpadding="0" align="right" width="100%">
			<tr>
				<?php foreach ($tmpl_course_nav as $link): ?>
						<!-- regular menu item -->
					
						<?php if ($tmpl_page == $link['page']): ?>
							<td align="right" valign="middle" class="cat2c selected"><a href="<?php echo $link['url'] ?>" id="<?php echo $link['id']; ?>"><?php echo $link['name'] ?></a></td>
						<?php else: ?>
							<td align="right" valign="middle" class="cat2c"><a href="<?php echo $link['url'] ?>" id="<?php echo $link['id']; ?>"><?php echo $link['name'] ?></a></td>
						<?php endif; ?>

						<!-- end regular menu item -->

				<?php endforeach; ?>
			</tr>
			</table></td>
	</tr>
	<?php if ($tmpl_instructor_nav): ?>
	<tr>
		<td class="cyan">
		<!-- instructor navigation links: -->
		<table border="0" cellspacing="0" cellpadding="0" align="right" class="instmenu">
			<tr>
				<td align="right" valign="middle" class="instmenu borderless">Instructor Tools:</td>
				<?php foreach ($tmpl_instructor_nav as $link): ?>
						<!-- regular menu item -->
					
						<?php if ($tmpl_page == $link['page']): ?>
							<td align="right" valign="middle" class="instmenu selected"><a href="<?php echo $link['url'] ?>" id="<?php echo $link['id']; ?>"><?php echo $link['name'] ?></a></td>
						<?php else: ?>
							<td align="right" valign="middle" class="instmenu"><a href="<?php echo $link['url'] ?>" id="<?php echo $link['id']; ?>"><?php echo $link['name'] ?></a></td>
						<?php endif; ?>

						<!-- end regular menu item -->

				<?php endforeach; ?>
			</tr>
			</table></td>
	</tr>
	<?php endif; ?>
	<tr>
		<td colspan="2" class="row3" height="1"><img src="<?php echo $tmpl_base_path; ?>images/clr.gif" height="1" width="1" alt="" /></td>
	</tr>
<?php endif; ?>
<!-- end course navigation elements -->

<!-- the breadcrumb navigation -->
<?php if ($tmpl_breadcrumbs_actual): ?>
	<tr>
		<td valign="middle" class="breadcrumbs">
				<?php foreach($tmpl_breadcrumbs_actual as $item): ?>
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