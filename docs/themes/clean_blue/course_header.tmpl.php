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
// $Id$

if (!defined('AT_INCLUDE_PATH')) { exit; }
?>
<!-- content table -->
<table border="0" cellpadding="0" cellspacing="0" width="100%" summary="" id="content">
<tr>
	<?php if ($tmpl_menu_open && $tmpl_menu_left): ?>
		<td id="menu" width="20%" valign="top" rowspan="2" style="padding:5px">
			<table width="100%" border="0" cellspacing="0" cellpadding="0" summary="">
			<tr>
				<td class="dropdown-heading closed" valign="top">
					<?php print_popup_help('MAIN_MENU'); ?>
					<a name="menu"></a><a href="<?php echo $tmpl_close_menu_url; ?>" accesskey="6" title="<?php echo _AT('close_menus')?>: Alt-6"><?php echo _AT('close_menus'); ?></a>
				</td>
			</tr>
			<?php if(show_pen()): ?>
				<tr><td height="5"></td></tr>
				<tr>
					<td class="pen" valign="top">
						<?php print_popup_help('EDITOR'); ?><img src="<?php echo $tmpl_pen_image; ?>" alt="<?php echo _AT('editor'); ?>" title="<?php echo _AT('editor'); ?>" /> <?php echo $tmpl_pen_link; ?>
					</td>
				</tr>
			<?php endif; ?>
			</table>

			<!-- dropdown menus -->
			<?php require(AT_INCLUDE_PATH.'html/dropdowns.inc.php'); ?>
			<!-- end dropdown menus -->
		</td>
	<?php endif; ?>

	<td width="3"><img src="<?php echo $tmpl_base_path; ?>images/clr.gif" width="3" height="3" alt="" /></td>

	<td valign="top" width="<?php echo $tmpl_width; ?>">
		<table border="0" cellspacing="0" cellpadding="0" width="100%" summary="">
		<tr>
			<?php if ($tmpl_menu_closed && $tmpl_menu_left): ?>
				<td width="20%" valign="top" style="padding-top:5px">
					<table width="100%" border="0" cellspacing="0" cellpadding="0" summary="">
					<tr>
						<td class="dropdown-heading closed" valign="top">
							<?php print_popup_help('MAIN_MENU'); ?>
							<a name="menu"></a><a href="<?php echo $tmpl_open_menu_url; ?>" accesskey="6" title="<?php echo _AT('open_menus'); ?> ALT-6"><?php echo _AT('open_menus'); ?></a>
						</td>
					</tr>
					<?php if(show_pen()): ?>
						<tr><td height="5"></td></tr>
						<tr>
							<td class="pen" valign="top">
								<?php print_popup_help('EDITOR'); ?><img src="<?php echo $tmpl_pen_image; ?>" alt="<?php echo _AT('editor'); ?>" title="<?php echo _AT('editor'); ?>" /><?php echo $tmpl_pen_link; ?>
							</td>
						</tr>
					<?php endif; ?>
					</table>
				</td>
			<?php endif; ?>

			<td width="80%" valign="top"></td>
			<?php if ($tmpl_menu_closed && !$tmpl_menu_left): ?>
				<td width="20%" valign="top" style="padding:5px">
					<table width="100%" border="0" cellspacing="0" cellpadding="0" summary="">
					<tr>
						<td class="dropdown-heading closed" valign="top">
							<?php print_popup_help('MAIN_MENU');?>
							<a name="menu"></a><a href="<?php echo $tmpl_open_menu_url; ?>" accesskey="6" title="<?php echo _AT('open_menus'); ?> ALT-6"><?php echo _AT('open_menus'); ?></a>
						</td>
					</tr>
					<?php if(show_pen()): ?>
						<tr><td height="5"></td></tr>
						<tr>
							<td class="pen" valign="top">
								<?php print_popup_help('EDITOR'); ?><img src="<?php echo $tmpl_pen_image; ?>" alt="<?php echo _AT('editor'); ?>" title="<?php echo _AT('editor'); ?>" /> <?php echo $tmpl_pen_link; ?>
							</td>
						</tr>
					<?php endif; ?>
					</table>
				</td>
			<?php endif; ?>	
		</tr>
		</table>
<a name="course-content"></a>