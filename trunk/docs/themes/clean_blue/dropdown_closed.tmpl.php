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

?>

<table width="100%" border="0" cellspacing="0" cellpadding="0" summary="">
	<tr>
		<td><img src="<?php echo $tmpl_base_path; ?>images/clr.gif" height="4" width="4" alt="" /></td>
	</tr>
	<tr>
		<td class="dropdown-heading closed" valign="top">
			<?php print_popup_help($tmpl_popup_help); ?>
			<?php echo $tmpl_menu_url; ?>
			<a href="<?php echo $tmpl_open_url; ?>" accesskey="<?php echo $tmpl_access_key; ?>" title="<?php echo $tmpl_dropdown_open; ?> <?php if ($tmpl_access_key): echo 'ALT-'.$tmpl_access_key; endif; ?>"><?php echo $tmpl_dropdown_open; ?></a>
		</td>
	</tr>
</table>
