<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('create_groups'); ?></legend>
	<div class="row">
		<input type="radio" name="create" value="automatic" id="automatic" checked="checked" /><label for="automatic"><?php echo _AT('groups_create_automatic'); ?></label>
	</div>

	<div class="row">
		<input type="radio" name="create" value="manual" id="manual" /><label for="manual"><?php echo _AT('groups_create_manual'); ?></label>
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('continue'); ?>" />
	</div>
	</fieldset>
</div>
</form>