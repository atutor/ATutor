
<form name="form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">

<table class="data" summary="" rules="cols">
<thead>
<tr>
	<th><?php echo _AT('file_name');    ?></th>
	<th><?php echo _AT('date_created'); ?></th>
	<th><?php echo _AT('file_size');    ?></th>
	<th><?php echo _AT('description');  ?></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="6"><input type="submit" name="restore" value="<?php echo _AT('restore'); ?>"  class="button"/> 
				  <input type="submit" name="download" value="<?php echo _AT('download'); ?>"  class="button"/> 
				  <input type="submit" name="delete" value="<?php echo _AT('delete'); ?>"  class="button"/> 
				  <input type="submit" name="edit" value="<?php echo _AT('edit'); ?>"  class="button"/></td>
</tr>
</tfoot>
<tbody>
<?php

	if (!$this->list) {
		?>
	<tr>
		<td colspan="4"><?php echo _AT('none_found'); ?></td>
	</tr>
	<?php
	} else {
		foreach ($this->list as $row) {
			echo '<tr onmousedown="document.form[\'b'.$row['backup_id'].'\'].checked = true; rowselect(this);" id="r_'.$row['backup_id'].'">';
			echo '<td class="row1"><label><input type="radio" value="'.$row['backup_id'].'" name="backup_id" id="b'.$row['backup_id'].'" />';
			echo $row['file_name'].'</label></td>';
			echo '<td>'.AT_date(_AT('filemanager_date_format'), $row['date'], AT_DATE_MYSQL_DATETIME).'</td>';
			echo '<td align="right">'.get_human_size($row['file_size']).'</td>';
			echo '<td>'.AT_print($row['description'], 'backups.description').'</td>';
			echo '</tr>';
		}
?>
	<?php } ?>
</tbody>
</table>
</form>