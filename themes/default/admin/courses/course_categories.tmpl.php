<?php
/**
 * Accept parameter:
 * 
 * $this->categories: An array of course categories created by admin
 */ 
?>
<form name="form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<table summary="" class="data" rules="cols" align="center" style="width: 95%;">

<thead>
<tr>
	<th scope="col">&nbsp;</th>
	<th scope="col"><?php echo _AT('name'); ?></th>
	<th scope="col"><?php echo _AT('parent'); ?></th>
<?php if (defined('AT_ENABLE_CATEGORY_THEMES') && AT_ENABLE_CATEGORY_THEMES) : ?>
	<th scope="col"><?php echo _AT('theme'); ?></th>
<?php endif; ?>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="4">
		<div class="row buttons">
		<input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" /> <input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" /> 
		</div>
	</td>
</tr>
</tfoot>
<tbody>
<?php if (count($this->categories) == 0) { ?>
	<tr>
		<td colspan="3"><?php echo _AT('none_found'); ?></td>
	</tr>
<?php
} else { 
	foreach ($this->categories as $category) { 
?>
	<tr onmousedown="document.form['m<?php echo $category['cat_id']; ?>'].checked = true; rowselect(this);" id="r_<?php echo $category['cat_id']; ?>">
		<td width="10"><input type="radio" name="cat_id" value="<?php echo $category['cat_id']; ?>" id="m<?php echo $category['cat_id']; ?>" /></td>
		<td><label for="m<?php echo $category['cat_id']; ?>"><?php echo AT_print($category['cat_name'], 'course_cats.cat_name'); ?></label></td>
		<td><?php echo AT_print($category['parent_cat_name'], 'course_cats.cat_name'); ?></td>
		<?php if (defined('AT_ENABLE_CATEGORY_THEMES') && AT_ENABLE_CATEGORY_THEMES) : ?>
		<td><?php echo AT_print(get_theme_name($category['theme']), 'themes.title'); ?></td>
		<?php endif; ?>

	</tr>
<?php 
	} // end of foreach 
} // end of else
?>
</tbody>
</table>

</form>