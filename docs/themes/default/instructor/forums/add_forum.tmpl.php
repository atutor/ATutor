<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="add_forum" value="true">

<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('create_forum'); ?></legend>
	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="title"><?php echo _AT('title'); ?></label><br />
		<input type="text" name="title" size="40" id="title" />
	</div>
	<div class="row">
		<label for="body"><?php echo _AT('description'); ?></label><br />
		<textarea name="body" cols="45" rows="2" id="body" wrap="wrap"></textarea>
	</div>
	<div class="row">
		<label for="edit"><?php echo _AT('allow_editing'); ?></label><br />
		<input type="text" name="edit" size="3" id="edit" value="<?php echo intval($row['mins_to_edit']); ?>" /> <?php echo _AT('in_minutes'); ?>
	</div>
	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s" /> 
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
	</fieldset>
</div>
</form>