<?php global $moduleFactory, $_pages;?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
	<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('groups_create_manual'); ?></legend>
		<div class="row">
			<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="type"><?php echo _AT('groups_type'); ?></label><br />
			<?php if ($this->types): ?>
				<?php echo _AT('existing_type'); ?>
				<select name="type" id="type">
				<?php foreach ($this->types as $type_id => $type_title): ?>
					<option value="<?php echo $type_id; ?>"><?php echo $type_title; ?></option>
				<?php endforeach; ?>
				</select>
				<strong><?php echo _AT('or'); ?></strong>
			<?php endif; ?>
			<label for="new"><?php echo _AT('new_type'); ?></label> <input type="text" name="new_type" value="<?php echo AT_print($_POST['new_type'], 'groups.type'); ?>" id="new" size="30" maxlength="40" />
		</div>

		<div class="row">
			<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="prefix"><?php echo _AT('title'); ?></label><br />
			<input type="text" name="prefix" id="prefix" value="<?php echo AT_print($_POST['prefix'], 'prefix'); ?>" size="20" maxlength="40" />
		</div>

		<div class="row">
			<label for="description"><?php echo _AT('description'); ?></label><br />
			<textarea name="description" id="description" cols="10" rows="2"><?php echo AT_print($_POST['description'], 'groups.description'); ?></textarea>
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