<?php global $moduleFactory, $_pages;?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
	<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('groups_create_automatic'); ?></legend>
		<div class="row">
			<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="type"><?php echo _AT('groups_type'); ?></label><br />
			<input type="text" name="type_title" id="type" value="<?php echo AT_print($_POST['type_title'], 'groups.type'); ?>" size="30" maxlength="60" />
		</div>

		<div class="row">
			<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="prefix"><?php echo _AT('group_prefix'); ?></label><br />
			<input type="text" name="prefix" id="prefix" value="<?php echo AT_print($_POST['prefix'], 'groups.prefix'); ?>" size="20" maxlength="40" />
		</div>

		<div class="row">
			<label for="description"><?php echo _AT('default_description'); ?></label><br />
			<textarea name="description" id="description" cols="10" rows="2"><?php echo AT_print($_POST['description'], 'groups.description'); ?></textarea>
		</div>

		<div class="row">
			<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><?php echo _AT('number_of_groups'); ?><br />
		
			<p><?php echo _AT('num_students_currently_enrolled', $this->row['cnt']-1); ?></p>

			<input type="radio" name="num_g" value="1" id="num1" checked="checked" onclick="javascript:changer('num_groups', 'num_students');" /><label for="num1"><?php echo _AT('number_of_students_per_group'); ?></label> <input type="text" name="num_students" size="3" style="text-align: right" maxlength="4" />
			<br />
			<input type="radio" name="num_g" value="2" id="num2" onclick="javascript:changer('num_students', 'num_groups');" /><label for="num2"><?php echo _AT('number_of_groups'); ?></label> <input type="text" name="num_groups" size="3" style="text-align: right" maxlength="4" value="-" />
		</div>

		<div class="row">
			<?php echo _AT('fill_groups'); ?><br />
			<input type="checkbox" name="fill" value="1" id="fill_r" checked="checked" /><label for="fill_r"><?php echo _AT('fill_groups_randomly'); ?></label>
		</div>

		<div class="row">
			<?php echo _AT('tools'); ?><br />
				<?php
				$modules = $moduleFactory->getModules(AT_MODULE_STATUS_ENABLED, 0, TRUE);
				$keys = array_keys($modules);
				$i=0;
				
				?>
				<?php foreach($keys as $module_name): ?>
					<?php $module =& $modules[$module_name]; ?>
					
					<?php if ($module->getGroupTool() && (in_array($module->getGroupTool(),$_pages[AT_NAV_HOME]) || in_array($module->getGroupTool(),$_pages[AT_NAV_COURSE])) ): ?>
						<input type="checkbox" value="<?php echo $module_name; ?>" name="modules[]" id="m<?php echo ++$i; ?>" /><label for="m<?php echo $i; ?>"><?php echo $module->getName(); ?></label><br />
					
					<?php endif; ?>
				<?php endforeach; ?>
		</div>

		<div class="row buttons">
			<input type="submit" name="submit" value="<?php echo _AT('create'); ?>" accesskey="s" />
			<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
		</div>
		</fieldset>
	</div>
</form>
