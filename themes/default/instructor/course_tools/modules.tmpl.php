<?php $count = 0;
global $_pages;
?>

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
	<td colspan="3" style="text-align:right;"><input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s" /></td>
</tr>
</tfoot>
<tbody>
<?php foreach ($this->current_modules as $module): ?>
<?php $count++; ?>
<tr>
	<td><?php 
		if (isset($_pages[$module]['title'])) {
			echo $_pages[$module]['title'];
		} else {
			echo _AT($_pages[$module]['title_var']);
		} ?></td>
	<td>
		<?php if (in_array($module, $_pages[AT_NAV_COURSE])): ?>
			<input type="checkbox" name="main[]" value="<?php echo $module; ?>" id="m<?php echo $count; ?>" checked="checked" /><label for="m<?php echo $count; ?>"><?php echo _AT('main_navigation'); ?></label>
		<?php else: ?>
			<input type="checkbox" name="main[]" value="<?php echo $module; ?>" id="m<?php echo $count; ?>" /><label for="m<?php echo $count; ?>"><?php echo _AT('main_navigation'); ?></label>
		<?php endif; ?>

		<?php if (in_array($module, $_pages[AT_NAV_HOME])): ?>
			<input type="checkbox" name="home[]" value="<?php echo $module; ?>" id="h<?php echo $count; ?>" checked="checked" /><label for="h<?php echo $count; ?>"><?php echo _AT('home'); ?></label>
		<?php else: ?>
			<input type="checkbox" name="home[]" value="<?php echo $module; ?>" id="h<?php echo $count; ?>" /><label for="h<?php echo $count; ?>"><?php echo _AT('home'); ?></label>
		<?php endif; ?>
	</td>
	<td align="right">
		<?php if (!in_array($module, $_pages[AT_NAV_HOME]) && !in_array($module, $_pages[AT_NAV_COURSE])): ?>
			&nbsp;
		<?php else: ?>
			<?php if (($count != $this->num_main+1) && ($count > 1)): ?>
				<input type="submit" name="up[<?php echo $module; ?>]" value="<?php echo _AT('move_up'); ?>" title="<?php echo _AT('move_up'); ?>" style="background-color: white; border: 1px solid; padding: 0px;" />
			<?php else: ?>
				<img src="images/clr.gif" alt="" width="12" />
			<?php endif; ?>
			<?php if (($count != $this->num_main) && ($count < $this->num_modules)): ?>
				<input type="submit" name="down[<?php echo $module; ?>]" value="<?php echo _AT('move_down'); ?>" title="<?php echo _AT('move_down'); ?>" style="background-color: white; border: 1px solid; padding: 0px;"/>
			<?php else: ?>
				<img src="images/clr.gif" alt="" width="12" />
			<?php endif; ?>
		<?php endif; ?>
	</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</form>