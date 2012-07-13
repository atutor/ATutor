<div class="input-form">
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" name="form">
<fieldset class="group_form" margin:auto;"><legend class="group_form"><?php echo _AT('groups'); ?></legend>
<table class="data" summary="" rules="cols" style="width: 80%">
<tfoot>
<tr>
	<td>
		<input type="submit" name="edit"    value="<?php echo _AT('edit'); ?>" />
		<input type="submit" name="members" value="<?php echo _AT('members'); ?>" />
		<input type="submit" name="delete"  value="<?php echo _AT('delete'); ?>" />
	</td>
</tr>
</tfoot>
<tbody>

<?php if (!empty($this->group_type_rows)): ?>

		<?php foreach ($this->group_type_rows as $type_id => $row): ?>
		<tr onmousedown="document.form['g<?php echo $row['type_id']; ?>'].checked = true; rowselect(this);" id="r_<?php echo $row['type_id']; ?>">
			<th>
				<input type="radio" id="g<?php echo $row['type_id']; ?>" name="id" value="<?php echo $row['type_id']; ?>" />
				<label for="g<?php echo $row['type_id']; ?>"><?php echo AT_print($row['title'], 'groups.title'); ?></label> (<?php echo $this->num_groups.' '._AT('groups'); ?>)</td>
			</th>
		</tr>
		<?php endforeach; ?>
		<?php if ($num_groups) : ?>
			<?php while ($group_row = mysql_fetch_assoc($group_result)): ?>
				<?php
					$sql = "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."groups_members WHERE group_id=$group_row[group_id]";
					$group_cnt_result = mysql_query($sql, $db);
					$group_cnt = mysql_fetch_assoc($group_cnt_result);
				?>
				<tr onmousedown="document.form['g<?php echo $row['type_id'].'_'.$group_row['group_id']; ?>'].checked = true; rowselect(this);" id="r_<?php echo $row['type_id'].'_'.$group_row['group_id']; ?>">
					<td class="indent"><input type="radio" id="g<?php echo $row['type_id'].'_'.$group_row['group_id']; ?>" name="id" value="<?php echo $row['type_id'].'_'.$group_row['group_id']; ?>" /> <label for="g<?php echo $row['type_id'].'_'.$group_row['group_id']; ?>"><?php echo AT_print($group_row['title'], 'groups.title'); ?></label> (<?php echo $group_cnt['cnt'].' '._AT('members'); ?>)</td>
				</tr>
			<?php endwhile; ?>
		<?php else: ?>
			<tr>
				<td class="indent"><strong><?php echo _AT('none_found'); ?></strong></td>
			</tr>
		<?php endif; ?>

<?php endif;?>
<?php if (empty($this->group_type_rows)): ?>
<?php else: ?>
	<tr>
		<td><strong><?php echo _AT('none_found'); ?></strong></td>
	</tr>
<?php endif;?>


</tbody>
</table>
</fieldset>
</form><br />
</div>