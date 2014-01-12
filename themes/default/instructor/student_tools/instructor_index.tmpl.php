<?php global $_pages;?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<table class="data" summary="">
<thead>
<tr>
	<th scope="cols"><?php echo _AT('section'); ?></th>
	<th><?php echo _AT('order'); ?></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="2"><input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s" /></td>
</tr>
</tfoot>
<tbody>

<?php foreach ($this->_current_modules as $module): ?>
<?php if ($module == 'mods/_standard/student_tools/index.php') { continue; } ?>
<?php  ?>
<tr>
	<td>
		<?php if (in_array($module, $this->fha_student_tools)): ?>
			<input type="checkbox" name="main[]" value="<?php echo $module; ?>" id="m<?php echo $count; ?>" checked="checked" />
		<?php else: ?>
			<input type="checkbox" name="main[]" value="<?php echo $module; ?>" id="m<?php echo $count; ?>" />
		<?php endif; ?>
		<label for="m<?php echo $count; ?>"><?php 
			if (isset($_pages[$module]['title'])) {
				echo $_pages[$module]['title'];
			} else {
				echo _AT($_pages[$module]['title_var']);
		} ?></label>
	</td>

	<td align="right">
		<?php if (!in_array($module, $this->fha_student_tools)): ?>
			&nbsp;
		<?php else: ?>
			<?php if (($count != $num_main+1) && ($count > 1)): ?>
				<input type="submit" name="up[<?php echo $module; ?>]" value="<?php echo _AT('move_up'); ?>" title="<?php echo _AT('move_up'); ?>" style="background-color: white; border: 1px solid; padding: 0px;" />
			<?php else: ?>
				<img src="images/clr.gif" alt="" width="12" />
			<?php endif; ?>
			<?php if (($count != $num_main) && ($count < $this->num_modules)): ?>
				<input type="submit" name="down[<?php echo $module; ?>]" value="<?php echo _AT('move_down'); ?>" title="<?php echo _AT('move_down'); ?>" style="background-color: white; border: 1px solid; padding: 0px;"/>
			<?php else: ?>
				<img src="images/clr.gif" alt="" width="12" />
			<?php endif; ?>
		<?php endif; ?>
	</td>
</tr>
<?php 
$count++;
endforeach; ?>
</tbody>
</table>
</form>