
<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
<table class="data" style="width: 90%;" rules="cols">
<colgroup>
	<?php if ($this->sort == 'title'): ?>
		<col />
		<col class="sort" />
		<col span="5" />
	<?php elseif($this->sort == 'assign_to'): ?>
		<col span="2" />
		<col class="sort" />
		<col span="4" />
	<?php elseif($this->sort == 'date_due'): ?>
		<col span="3" />
		<col class="sort" />
		<col span="3" />
	<?php endif; ?>
</colgroup>
<thead>
<tr>
	<th>&nbsp;</th>
	<th scope="col"><a href="mods/_standard/assignments/index_instructor.php?sort=title<?php echo SEP; ?>order=<?php echo $orders[$order]; ?>"><?php echo _AT('title'); ?></a></th>
	<th scope="col"><?php echo _AT('assigned_to'); ?></th>
	<th scope="col"><a href="mods/_standard/assignments/index_instructor.php?sort=date_due<?php echo SEP; ?>order=<?php echo $orders[$order]; ?>"><?php echo _AT('due_date'); ?></a></th>
</tr>
</thead>
<?php if (($this->result != 0) && ($row = mysql_fetch_assoc($this->result))) : ?>
<tfoot>
<tr>
	<td colspan="4">
		<input type="submit" name="submissions" value="<?php echo _AT('submissions'); ?>" class="button"/> 
		<input type="submit" name="edit" value="<?php echo _AT('edit'); ?>"  class="button"/> 
		<input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" class="button" />
					
	</td>
</tr>
</tfoot>
<tbody>
	<?php do { ?>
		<tr onmousedown="document.form['a<?php echo $row['assignment_id']; ?>'].checked = true; rowselect(this);" id="a_<?php echo $row['assignment_id']; ?>_0">
		
		<td><input type="radio" id="a<?php echo $row['assignment_id']; ?>" name="assignment" value="<?php echo $row['assignment_id']; ?>" 

		<?php // set first item as checked if nothing selected
		if (isset($_GET['assignment_id'])){
			if ($_GET['assignment_id'] == $row['assignment_id']){ 
				echo ' checked="checked"'; 
			} 
		}
		else {
			echo ' checked="checked"';
			$_GET['assignment_id'] = $row['assignment_id'];
		}
		?>/></td>

		<td><label for="a<?php echo $row['assignment_id']; ?>"><?php echo AT_print($row['title'], 'assignment.title'); ?></label></td>

		<td><?php if($row['assign_to'] == '0'){echo _AT('all_students'); } else {
				
					$type_row = mysql_fetch_assoc($this->type_result);
					echo $type_row['title']; } ?></td>

		<td><?php  if ($row['date_due'] == '0000-00-00 00:00:00'){
			echo _AT('none');
		}else {
			echo AT_Date(_AT('forum_date_format'), $row['date_due'], AT_DATE_MYSQL_DATETIME);
		}?></td>
		</tr>
	<?php } while($row = mysql_fetch_assoc($this->result)); ?>
</tbody>
<?php else: ?>
	<tr>
		<td colspan="4"><strong><?php echo _AT('none_found'); ?></strong></td>
	</tr>
<?php endif; ?>
</table>
</form>