hi I'm an administrator

<?php global $moduleFactory;?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<input type="hidden" name="course_id" value="<?php echo $this->course_id; ?>"/>
<div class="input-form">

	<div class="row">
		<h3><?php echo $this->student_row['login']; ?></h3>
	</div>

	<div class="row">
		<?php echo _AT('privileges'); ?><br />
		<table width="100%" border="0" cellspacing="5" cellpadding="0" summary="">
		<tr>
		<?php		
		$count =0;
		$this->student_row['privileges'] = intval($this->student_row['privileges']);
		$module_list = $moduleFactory->getModules(AT_MODULE_STATUS_ENABLED, 0, TRUE);
		$keys = array_keys($module_list);
		foreach ($keys as $module_name) {
			$module =& $module_list[$module_name];
			if (!($module->getPrivilege() > 1)) {
				continue;
			}
			$count++;
			echo '<td><label><input type="checkbox" name="privs['.$k.'][]" value="'.$module->getPrivilege().'" ';

			if (query_bit($this->student_row['privileges'], $module->getPrivilege())) { 
				echo 'checked="checked"';
			} 

			echo ' />'.$module->getName().'</label></td>';

			if (!($count % $this->num_cols)) {
				echo '</tr><tr>';
			}
		}
		if ($count % $this->num_cols) {
			echo '<td colspan="'.($this->num_cols-($count % $this->num_cols)).'">&nbsp;</td>';
		} else {
			echo '<td colspan="'.$this->num_cols.'">&nbsp;</td>';
		}
		?>
		</tr>
		</table>
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save');  ?>" accesskey="s" /> 
		<input type="submit" name="cancel" value="<?php echo _AT('cancel');  ?>" />
	</div>
</div>
</form>