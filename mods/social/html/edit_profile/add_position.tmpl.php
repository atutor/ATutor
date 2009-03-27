<form method="POST" action="<?php echo url_rewrite('mods/social/edit_profile.php'); ?>">
<div>
	<div>
		<label for="company"><?php echo _AT('company'); ?></label>
		<input type="text" id="company" name="company" value="<?php echo $company; ?>" />
	</div>

	<div>
		<label for="title"><?php echo _AT('title'); ?></label>
		<input type="text" name="title" value="<?php echo $title; ?>" />
	</div>

	<div>
		<label for="from"><?php echo _AT('from'); ?></label>
		<input type="text" name="from" value="<?php echo $from; ?>" />
	</div>

	<div>
		<label for="to"><?php echo _AT('to'); ?></label>	
		<input type="text" name="to" value="<?php echo $to; ?>" />
	</div>

	<div>
		<label for="description"><?php echo _AT('description'); ?></label>	
		<textarea name="description"><?php echo $description; ?></textarea>
	</div>
	<input type="hidden" name="add" value="position" />
	<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" />
	<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
</div>
</form>