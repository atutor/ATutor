<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<div class="input-form">
	<div class="row">
		<h3><label for="ctid"><?php echo _AT('select_parent_topic'); ?></label></h3>
	</div>

	<div class="row">
		<select name="ctid" id="ctid">
			<option value="0"><?php echo _AT('top_level'); ?></option>
			<?php
				print_select(0, 1);
			?>
		</select>
	</div>

	<div class="row buttons">
		<input type="submit" name="sub_content" value="<?php echo _AT('view_sub_topics'); ?>" />
	</div>
</div>
</form>

<form name="form" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">

<table class="data" summary="" rules="cols" style="width: 95%;">
<thead>
<tr>
	<th scope="col">&nbsp;</th>
	<th scope="col">#</th>
	<th scope="col"><?php echo _AT('title'); ?></th>
	<th scope="col"><?php echo _AT('num_pages'); ?></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="5">
		<input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" /> 
		<input type="submit" name="view" value="<?php echo _AT('view'); ?>" />
		<input type="submit" name="usage" value="<?php echo _AT('usage'); ?>" />
		<input type="submit" name="sub_content" value="<?php echo _AT('sub_topics'); ?>" />
		<input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" />
	</td>
</tr>
</tfoot>
<tbody>
	<?php if (!empty($this->content)): ?>
		<?php foreach ($this->content as $row): ?>
			<tr onmousedown="document.form['c<?php echo $row['content_id']; ?>'].checked = true; rowselect(this);" id="r_<?php echo $row['content_id']; ?>">
				<td><input type="radio" name="ctid" value="<?php echo $row['content_id']; ?>" id="c<?php echo $row['content_id']; ?>" /></td>
				<td><?php echo $row['ordering']; ?></td>
				<td><label for="c<?php echo $row['content_id']; ?>"><?php echo AT_print($row['title'], 'content.title'); ?></label></td>
				<td><?php echo count($this->all_content[$row['content_id']]); ?></td>
			</tr>
		<?php endforeach; ?>
	<?php else: ?>
		<tr>
			<td colspan="5"><?php echo _AT('none_found'); ?></td>
		</tr>
	<?php endif; ?>
</tbody>
</table>
</form>