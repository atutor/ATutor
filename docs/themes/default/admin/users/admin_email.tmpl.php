
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
<input type="hidden" name="admin" value="admin" />

<div class="input-form">
	<div class="row">
	<fieldset>
<legend><span class="required" title="<?php echo _AT('required_field'); ?>">*</span><?php echo  _AT('to'); ?></legend>
		<input type="radio" name="to" value="3" checked="checked" id="all" /><label for="all"><?php echo _AT('all_users'); ?></label>  
	  <input type="radio" name="to" value="1" id="inst" <?php if ($_POST['to'] == AT_STATUS_INSTRUCTOR) { echo 'checked="checked"'; } ?> /><label for="inst"><?php echo  _AT('instructors'); ?></label>
	  <input type="radio" name="to" value="2" id="stud" <?php if ($_POST['to'] == AT_STATUS_STUDENT) { echo 'checked="checked"'; } ?> /><label for="stud"><?php echo  _AT('students'); ?></label>
	</fieldset>
	</div>

	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="subject"><?php echo _AT('subject'); ?></label><br />
		<input type="text" name="subject" size="40" id="subject" value="<?php echo $_POST['subject']; ?>" />
	</div>

	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="body"><?php echo _AT('body'); ?></label><br />
		<textarea cols="55" rows="18" name="body" id="body"><?php echo $_POST['body']; ?></textarea>
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('send'); ?>" accesskey="s" /> 
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
</div>
</form>