<?php if (count($this->tran_files) == 0) {
	echo '<div style="width:90%;" class="input-form"><p>'._AT('chat_none_found').'</p></div>';
} else {?>
	
<form name="form" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">

	<table class="data" rules="cols" summary="">
	<colgroup>
		<?php if ($this->col == 'name'): ?>
			<col />
			<col class="sort" />
			<col span="2" />
		<?php elseif($this->col == 'date'): ?>
			<col span="3" />
			<col class="sort" />
		<?php endif; ?>
	</colgroup>
	<thead>
	<tr>
		<th scope="col">&nbsp;</th>
		<th scope="col"><a href="mods/_standard/chat/index.php?<?php echo $this->orders[$this->order]; ?>=name"><?php echo _AT('chat_transcript');?></a></th>
		<th scope="col"><?php echo _AT('status'); ?></th>
		<th scope="col"><a href="mods/_standard/chat/index.php?<?php echo $this->orders[$this->order]; ?>=date"><?php echo _AT('date'); ?></a></th> 
		</th> 
	</tr>
	</thead>
	<?php

	if (($this->col == 'date') && ($this->order == 'asc')) {
		asort($this->tran_files);
	} else if (($this->col == 'date') && ($this->order == 'desc')) {
		arsort($this->tran_files);
	} else if (($this->col == 'name') && ($this->order == 'asc')) {
		ksort($this->tran_files);
	} else if (($this->col == 'name') && ($this->order == 'desc')) {
		krsort($this->tran_files);
	}
	reset ($this->tran_files);
	?>

	<tbody>
	<?php foreach ($this->tran_files as $file => $date) { ?>
		<tr onmousedown="document.form['<?php echo $file; ?>'].checked = true; rowselect(this);" id="r_<?php echo $file; ?>">
			<td><input type="radio" name="file" value="<?php echo $file; ?>" id="<?php echo $file; ?>" /></td>

			<td><label for="<?php echo $file; ?>"><?php echo $file; ?></label></td>
			<td>
				<?php if (($file.'.html' == $this->admin['tranFile']) && ($this->admin['produceTran'])) { 
					echo _AT('chat_currently_active');
				} else {
					echo _AT('chat_inactive');
				}?>
			</td>
	
			<td><?php echo AT_DATE(_AT('server_date_format'), $date); ?></td>
		</tr>
	<?php } ?>
	</tbody>

	<tfoot>
	<tr>
		<td colspan="4"><input type="submit" name="view" value="<?php echo _AT('view'); ?>" /> <input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" /></td>
	</tr>
	</tfoot>

	</table>
</form>
<?php
}?>