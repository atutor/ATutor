<?php global $stripslashes;?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">

<div class="input-form">	
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('add_topic'); ?></legend>
	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="name"><?php  echo _AT('name'); ?></label><br />
		<input type="text" name="name" size="50" id="name" value="<?php if (isset($_POST['name'])) echo $stripslashes($_POST['name']);  ?>" />
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
	</fieldset>
</div>
</form>