<form name="form" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">

<table class="data" summary="" rules="cols">
<thead>
<tr>
	<th scope="col">&nbsp;</th>
	<th scope="col"><?php echo _AT('title'); ?></th>
	<th scope="col"><?php echo _AT('description'); ?></th>
	<th scope="col"><?php echo _AT('allow_editing'); ?></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="4"><input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" /> <input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" /></td>
</tr>
</tfoot>
<tbody>
<?php if ($this->all_forums['nonshared']): ?>
	<?php foreach($this->all_forums['nonshared'] as $row): ?>
		<tr onmousedown="document.form['f<?php echo $row['forum_id']; ?>'].checked = true; rowselect(this);" id="r_<?php echo $row['forum_id']; ?>">
			<td width="10"><input type="radio" name="id" value="<?php echo $row['forum_id']; ?>" id="f<?php echo $row['forum_id']; ?>" /></td>
			<td><label for="f<?php echo $row['forum_id']; ?>"><?php echo AT_print($row['title'], 'forums.title'); ?></label></td>
			<td><?php echo AT_print($row['description'], 'forums.description'); ?></td>
			<td>
				<?php if (!$row['mins_to_edit']): ?>
					<?php echo _AT('no'); ?>
				<?php else: ?>
					<?php echo  _AT('minutes', $row['mins_to_edit']); ?>
				<?php endif; ?>
			</td>
		</tr>
	<?php endforeach; ?>
<?php else: ?>
	<tr>
		<td colspan="4"><?php echo _AT('none_found'); ?></td>
	</tr>
<?php endif; ?>
</tbody>
</table>
</form>