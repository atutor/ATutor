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

?>
<form action ="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" name="form">
<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center">
<tr>
	<th colspan="2"><?php echo _AT('cats_edit_categories').' '.$current_cats[$_GET['current_cat']]; ?></th>
</tr>
<tr>
	<td height="1" class="row2" colspan="2"></td>
</tr>
<tr>
	<td class="row1"><label for="category_name"><?php echo _AT('cats_category_name'); ?></label>:</td>
	<td class="row1"><input type="text" id="category_name" name="category_name" value="<?php echo $categories[$cat_id]['cat_name']; ?>" class="formfield" /></td>
</tr>
<tr>
	<td height="1" class="row2" colspan="2"></td>
</tr>
<tr>
	<td class="row1"><label for="category_parent"><?php echo _AT('cats_parent_category'); ?></label>:</td>
	<td class="row1"><select name="category_parent"  id="category_parent"><?php

				echo '<option value="0"> - '._AT('cats_none').' - </option>';
				select_categories($categories, 0, $categories[$cat_id]['cat_parent']);
			?></select></td>
</tr>
<tr>
	<td height="1" class="row2" colspan="2"></td>
</tr>
<tr>
	<td colspan="2" class="row1" align="right"><input type="submit" name="submit" value=" <?php echo _AT('edit'); ?> " class="button" accesskey="s" />
	<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" class="button" /></td>
</tr>
</table>
</form>