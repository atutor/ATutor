
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="id" value="<?php echo $id; ?>" />

<div class="input-form">
	<div class="row">
		<?php echo _AT('to'); ?><br />
		<?php echo $instructor_name; ?>
	</div>

	<div class="row">
		<label for="from"><?php echo _AT('from_name'); ?></label><br />
		<input type="text" class="formfield" name="from" id="from" size="40" value="<?php echo $student_name;?>" />
	</div>

	<div class="row">
		<label for="from_email"><?php echo _AT('from_email'); ?></label><br />
		<input type="text" class="formfield" name="from_email" id="from_email" size="40" value="<?php echo $student_email;?>" />
	</div>

	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="subject"><?php echo _AT('subject'); ?></label><br />
		<input type="text" class="formfield" name="subject" id="subject" size="40" value="<?php echo $_POST['subject']; ?>" />
	</div>

	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="body"><?php echo _AT('body'); ?></label><br />
		<textarea class="formfield" cols="55" rows="15" id="body" name="body" wrap="wrap"><?php echo $_POST['body']; ?></textarea>
	</div>

	<div class="buttons row">
		<input type="submit" name="submit" class="button" value="<?php echo _AT('send_message'); ?>" accesskey="s" />  
		<input type="submit" name="cancel" class="button" value="<?php echo _AT('cancel'); ?>" />
	</div>
</div>
</form>
