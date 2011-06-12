<form name="form" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<table class="data" summary="" rules="cols">
<colgroup>
	<?php if ($this->col == 'title'): ?>
		<col />
		<col class="sort" />
		<col />
	<?php elseif($this->col == 'date'): ?>
		<col span="2" />
		<col class="sort" />
	<?php endif; ?>
</colgroup>
<thead>
<tr>
	<th scope="col">&nbsp;</th>
	<th scope="col"><a href="mods/_standard/announcements/index.php?<?php echo $this->orders[$this->order]; ?>=title"><?php echo _AT('title'); ?></a></th>
	<th scope="col"><a href="mods/_standard/announcements/index.php?<?php echo $this->orders[$this->order]; ?>=date"><?php echo _AT('date'); ?></a></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="3"><input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" class="button"/> <input type="submit" name="delete" value="<?php echo _AT('delete'); ?>"  class="button"/></td>
</tr>
</tfoot>
<tbody>
	<?php if ($row = mysql_fetch_assoc($this->result)): ?>
		<?php do { ?>
			<tr onmousedown="document.form['n<?php echo $row['news_id']; ?>'].checked = true; rowselect(this);" id="r_<?php echo $row['news_id']; ?>">
			
				<td><input type="radio" name="aid" value="<?php echo $row['news_id']; ?>" id="n<?php echo $row['news_id']; ?>" /></td>
				
				<td><label for="n<?php echo $row['news_id']; ?>"><?php echo AT_print($row['title'], 'news.title'); ?></label></td>
				<td><?php echo AT_date(_AT('announcement_date_format'), $row['date'], AT_DATE_MYSQL_DATETIME); ?></td>
			</tr>
		<?php } while ($row = mysql_fetch_assoc($this->result)); ?>
	<?php else: ?>
		<tr>
			<td colspan="3"><?php echo _AT('none_found'); ?></td>
		</tr>
	<?php endif; ?>
</tbody>
</table>
</form>