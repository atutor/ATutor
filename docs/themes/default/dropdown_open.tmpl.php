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
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" summary="" class="dropdown">
	<tr>
		<td><img src="<?php echo $this->tmpl_base_path; ?>images/clr.gif" height="4" width="4" alt="" /></td>
	</tr>
	<tr>
		<td valign="top" class="dropdown-heading">
			<?php print_popup_help($this->tmpl_popup_help); ?>
			<?php echo $this->tmpl_menu_url; ?>
			<small> <a href="<?php echo $this->tmpl_close_url; ?>" accesskey="<?php echo $this->tmpl_access_key; ?>" title="<?php echo $this->tmpl_dropdown_close; ?> <?php if ($this->tmpl_access_key): echo 'ALT-'.$this->tmpl_access_key; endif; ?>">
			<?php echo $this->tmpl_dropdown_close; ?></a> </small>
		</td>
	</tr>
	<?php echo $this->tmpl_dropdown_contents; ?>
</table>