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
// $Id: header.tmpl.php,v 1.10 2004/04/15 15:18:40 joel Exp $

if (!defined('AT_INCLUDE_PATH')) { exit; }
global $_base_path;


?>
<!-- content table -->
<table border="0" cellpadding="0" cellspacing="0" width="100%" summary="" id="content">
<tr>
	<?php if ($tmpl_menu_open): ?>
		<td id="menu" width="25%" valign="top" rowspan="2" style="padding-top: 1px;">
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="cat2" summary="">
			<tr>
				<td class="cata" valign="top">
					<?php print_popup_help(AT_HELP_MAIN_MENU); ?>
					<a name="menu"></a><a class="white" href="<?php echo $tmpl_close_menu_url; ?>" accesskey="6" title="<?php echo _AT('close_menu')?>: Alt-6"><?php echo _AT('close_menus'); ?></a>
				</td>
			</tr>
			</table>
	
			<!-- dropdown menus -->
			<?php require(AT_INCLUDE_PATH.'html/dropdowns.inc.php'); ?>
			<!-- end dropdown menus -->
		</td>
	<?php endif; ?>

	<td width="3"><img src="<?php echo $tmpl_base_path?>images/clr.gif" width="3" height="3" alt="" /></td>
	<td valign="top" width="<?php echo $tmpl_width; ?>">
		<table border="0" cellspacing="0" cellpadding="0" width="100%" summary="">
		<tr>
			<?php if ($tmpl_menu_closed && $tmpl_menu_left): ?>
				<td width="25%" valign="top" class="hide">								
					<table width="100%" border="0" cellspacing="0" cellpadding="0" class="cat2" summary="">
					<tr>
						<td class="cata" valign="top">
							<?php print_popup_help(AT_HELP_MAIN_MENU); ?>
							<a name="menu"></a><a class="white" href="<?php echo $tmpl_open_menu_url; ?>" accesskey="6" title="<?php echo _AT('open_menus'); ?> ALT-6"><?php echo _AT('open_menus'); ?></a>
						</td>
					</tr>
					</table>
				</td>
			<?php endif; ?>

			<td width="75%" valign="top"></td>

			<?php if ($tmpl_menu_closed && !$tmpl_menu_left): ?>
				<td width="25%" valign="top" class="hide">
					<table width="100%" border="0" cellspacing="0" cellpadding="0" class="cat2" summary="">
					<tr>
						<td class="cata" valign="top">
							<?php print_popup_help(AT_HELP_MAIN_MENU);?>
							<a name="menu"></a><a class="white" href="<?php echo $tmpl_open_menu_url; ?>" accesskey="6" title="<?php echo _AT('open_menus'); ?> ALT-6"><?php echo _AT('open_menus'); ?></a>
						</td>
					</tr>
					</table>
				</td>
			<?php endif; ?>	
		</tr>
		</table>

<a name="content"></a>