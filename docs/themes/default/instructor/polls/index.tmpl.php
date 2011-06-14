<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<table class="data" summary="" rules="cols">
<colgroup>
	<?php if ($this->col == 'question'): ?>
		<col />
		<col class="sort" />
		<col span="2" />
	<?php elseif($this->col == 'created_date'): ?>
		<col span="2" />
		<col class="sort" />
		<col />
	<?php elseif($this->col == 'total'): ?>
		<col span="3" />
		<col class="sort" />
	<?php endif; ?>
</colgroup>
<thead>
<tr>
	<th scope="col">&nbsp;</th>
	<th scope="col"><a href="mods/_standard/polls/tools/index.php?<?php echo $this->orders[$this->order]; ?>=question"><?php echo _AT('question'); ?></a></th>
	<th scope="col"><a href="mods/_standard/polls/tools/index.php?<?php echo $this->orders[$this->order]; ?>=created_date"><?php echo _AT('created'); ?></a></th>
	<th scope="col"><a href="mods/_standard/polls/tools/index.php?<?php echo $this->orders[$this->order]; ?>=total"><?php echo _AT('total_votes'); ?></a></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="4">
		<input type="submit" name="edit"   value="<?php echo _AT('edit'); ?>" />
		<input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" />
	</td>
</tr>
</tfoot>
<tbody>
<?php if ($row = mysql_fetch_assoc($this->result)) : ?>
	<?php do { ?>
		<tr onmousedown="document.form['p_<?php echo $row['poll_id']; ?>'].checked = true; rowselect(this);" id="r_<?php echo $row['poll_id']; ?>">
			<td><input type="radio" id="p_<?php echo $row['poll_id']; ?>" name="poll" value="<?php echo $row['poll_id']; ?>" /></td>
			<td><label for="p_<?php echo $row['poll_id']; ?>"><?php echo AT_print($row['question'], 'polls.question'); ?></label></td>
			<td><?php echo AT_DATE(_AT("server_date_format"), $row['created_date']); ?></td>
			<td><?php echo $row['total']; ?></td>
		</tr>
	<?php } while($row = mysql_fetch_assoc($this->result)); ?>
<?php else: ?>
	<tr>
		<td colspan="4"><?php echo _AT('none_found'); ?></td>
	</tr>
<?php endif; ?>
</tbody>
</table>
</form>
