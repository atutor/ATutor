<form name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="backup_id" value="<?php echo $_GET['backup_id']; ?>" />
<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('edit'); ?></legend>
	<div class="row">
		<label for="description"><?php echo _AT('optional_description'); ?></label>
		<textarea cols="30" rows="2" id="description" name="new_description"><?php echo AT_print($this->row['description'], 'backups.description'); ?></textarea>
	</div>

	<div class="row buttons">
		<input type="submit" name="edit" value="<?php echo _AT('save'); ?>" accesskey="s" /> 
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
	</fieldset>
</div>
</form>
