<?php global $moduleFactory; ?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
<input type="hidden" name="backup_id" value="<?php echo $_REQUEST['backup_id']; ?>" />

<div class="input-form">
<fieldset class="group_form"><legend class="group_form"><?php echo _AT('restore'); ?></legend>
	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><?php echo _AT('material'); ?><br />

		<input type="checkbox" value="1" name="all" id="all" onclick="javascript:selectAll();" /><label for="all"><?php echo _AT('material_select_all'); ?></label><br /><br />

		<input type="checkbox" value="1" name='material[properties]' id='m0' /><label for='m0'><?php echo _AT('banner'); ?></label><br />
		<?php
		$i=0;
		$modules = $moduleFactory->getModules(AT_MODULE_STATUS_ENABLED, 0, TRUE);
		$keys = array_keys($modules);
		?>
		<?php foreach($keys as $module_name): ?>
			<?php $module =& $modules[$module_name]; ?>
			<?php if ($module->isBackupable()): ?>
				<input type="checkbox" value="1" name="material[<?php echo $module_name; ?>]" id="m<?php echo ++$i; ?>" /><label for="m<?php echo $i; ?>"><?php echo $module->getName(); ?></label><br />
			<?php endif; ?>
		<?php endforeach; ?>
	</div>

	<div class="row">
		<?php echo _AT('action'); ?><br />
		<input type="radio" checked="checked" name="action" value="append" id="append" /><label for="append"><?php echo _AT('append_content'); ?></label><br />
		
		<input type="radio" name="action" value="overwrite" id="overwrite" /><label for="overwrite"><?php echo _AT('overwite_content'); ?></label><br />
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('restore'); ?>"  class="button"/> 
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>"  class="button"/>
	</div>
</fieldset>
</div>
</form>
<?php $i=0; ?>
<script language="javascript" type="text/javascript">
	
	function selectAll() {
		if (document.form.all.checked == true) {
			document.form.m0.checked = true;
			<?php foreach($keys as $module_name): $module =& $modules[$module_name]; if ($module->isBackupable()): ?>
				document.form.m<?php echo ++$i; ?>.checked = true;
			<?php endif; endforeach; ?>
		} else {
			document.form.m0.checked = false;
			<?php $i=0;?>
			<?php foreach($keys as $module_name): $module =& $modules[$module_name]; if ($module->isBackupable()): ?>
				document.form.m<?php echo ++$i; ?>.checked = false;
			<?php endif; endforeach; ?>

		}
	}
</script>
