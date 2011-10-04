
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
<input type="hidden" name="id" value="<?php echo $_REQUEST['id']; ?>" />
<div class="input-form">
	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="student_id"><?php echo _AT('student_id'); ?></label><br />
		<input type="text" name="public_field" id="student_id" size="25" value="<?php echo $_POST['public_field']; ?>" />
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
</div>
</form>

