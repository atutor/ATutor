<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (isset($cat_id)) {
	echo '<p><a href="'.$_SERVER['PHP_SELF'].'?pcat_id='.$cat_id.'">'._AT('cats_add_subcategory').'</a></p>';
}
?>
<form action ="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" name="form">
<input type="hidden" name="cat_id" value="<?php echo $cat_id; ?>" />
<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center">
<tr>
	<th colspan="2"><?php 
		if (isset($cat_id)) {
			echo _AT('cats_edit_categories'); 
		} else {
			echo _AT('cats_add_categories'); 
		}?></th>
</tr>
<tr>
	<td height="1" class="row2" colspan="2"></td>
</tr>
<tr>
	<td class="row1"><label for="category_name"><?php echo _AT('cats_category_name'); ?></label>:</td>
	<td class="row1"><input type="text" id="category_name" name="cat_name" value="<?php echo stripslashes(htmlspecialchars($categories[$cat_id]['cat_name'])); ?>" class="formfield" /></td>
</tr>
<tr>
	<td height="1" class="row2" colspan="2"></td>
</tr>
<tr>
	<td class="row1"><label for="category_parent"><?php echo _AT('cats_parent_category'); ?></label>:</td>
	<td class="row1"><select name="cat_parent_id"  id="category_parent"><?php

				echo '<option value="0">'._AT('cats_none').'</option>';
				echo '<option value="0">-----------</option>';
				if ($pcat_id) {
					$current_cat_id = $pcat_id;
				} else {
					$current_cat_id = $cat_id;
				}
				select_categories($categories, 0, $current_cat_id);
			?></select><br /><br /></td>
</tr>
<tr>
	<td height="1" class="row2" colspan="2"></td>
</tr>
<tr>
	<td height="1" class="row2" colspan="2"></td>
</tr>
<tr>
	<td colspan="2" class="row1" align="right"><input type="submit" name="submit" value=" <?php 
		if (isset($cat_id)) { 
			echo _AT('edit'); 
		} else {
			echo _AT('create');	
		} ?> " class="button" accesskey="s" />
	<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" class="button" /></td>
</tr>
</table>
</form>