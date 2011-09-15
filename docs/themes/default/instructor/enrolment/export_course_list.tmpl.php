<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="selectform">
<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('export'); ?></legend>
	<div class="row">
		<label><input type="checkbox" name="enrolled" value="1" id="enrolled" /><?php echo _AT('enrolled_list_includes_assistants'); ?></label><br />
		<label><input type="checkbox" name="pending_enrollment" value="1" id="pending_enrollment" /><?php echo _AT('pending_enrollment'); ?></label><br />
		<label><input type="checkbox" name="alumni" value="1" id="alumni" /><?php echo _AT('alumni'); ?></label>
	</div>

	<div class="row buttons">
		<input type="submit" name="export" value="<?php echo _AT('export'); ?>" /> 
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
	</fieldset>
</div>
</form>