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

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="cat2" summary="">
	<tr>
		<td class="catb" valign="top">
			<?php print_popup_help($tmpl_popup_help); ?>
			<?php echo $tmpl_menu_url; ?>
			<a class="white" href="<?php echo $tmpl_open_url; ?>" accesskey="<?php echo $tmpl_access_key; ?>" title="<?php echo $tmpl_dropdown_open; ?> : Alt-<?php echo $tmpl_access_key; ?>">
			<?php echo $tmpl_dropdown_open; ?></a>
		</td>
	</tr>
</table>
