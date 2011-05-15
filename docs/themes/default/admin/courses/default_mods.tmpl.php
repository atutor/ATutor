
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<table class="data static" rules="rows" summary="">
<thead>
<tr>
	<th scope="cols"><?php echo _AT('section'); ?></th>
	<th><?php echo _AT('location'); ?></th>
	<th><?php echo _AT('order'); ?></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="3" style="text-align:right;">		
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s"  />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>"  />	
	</td>
</tr>
</tfoot>
<tbody>
<?php 


foreach ($this->current_modules as $tool) :
	$count++; 
?>
	<tr>
		<td><?php 
		if (isset($this->pages[$tool]['title'])) {
			echo $this->pages[$tool]['title'];
		} else {
			echo _AT($this->pages[$tool]['title_var']);
		} ?></td>
		<td align="center">
			<?php if (in_array($tool, $this->main_defaults)): ?>
				<input type="checkbox" name="main[]" value="<?php echo $tool; ?>" id="m<?php echo $tool; ?>" checked="checked" /><label for="m<?php echo $tool; ?>"><?php echo _AT('main_navigation'); ?></label>
			<?php else: ?>
				<input type="checkbox" name="main[]" value="<?php echo $tool; ?>" id="m<?php echo $tool; ?>" /><label for="m<?php echo $tool; ?>"><?php echo _AT('main_navigation'); ?></label>
			<?php endif; ?>

			<?php if (in_array($tool, $this->home_defaults)): ?>
				<input type="checkbox" name="home[]" value="<?php echo $tool; ?>" id="h<?php echo $tool; ?>" checked="checked" /><label for="h<?php echo $tool; ?>"><?php echo _AT('home'); ?></label>
			<?php else: ?>
				<input type="checkbox" name="home[]" value="<?php echo $tool; ?>" id="h<?php echo $tool; ?>" /><label for="h<?php echo $tool; ?>"><?php echo _AT('home'); ?></label>
			<?php endif; ?>
		</td>
		<td align="right">
			<?php if (!in_array($tool, $this->home_defaults) && !in_array($tool, $this->main_defaults)): ?>
				&nbsp;
			<?php else: ?>
				<?php if (($count != $this->num_main+1) && ($count > 1)): ?>
					<input type="submit" name="up[<?php echo $tool; ?>]" value="<?php echo _AT('move_up'); ?>" title="<?php echo _AT('move_up'); ?>" style="background-color: white; border: 1px solid; padding: 0px;" />
				<?php else: ?>
					<img src="images/clr.gif" alt="" width="12" />
				<?php endif; ?>
				<?php if (($count != $this->num_main) && ($count < $this->num_modules)): ?>
					<input type="submit" name="down[<?php echo $tool; ?>]" value="<?php echo _AT('move_down'); ?>" title="<?php echo _AT('move_down'); ?>" style="background-color: white; border: 1px solid; padding: 0px;"/>
				<?php else: ?>
					<img src="images/clr.gif" alt="" width="12" />
				<?php endif; ?>
			<?php endif; ?>
		</td>
	</tr>
<?php 
endforeach; ?>
</tbody>
</table>
</form>