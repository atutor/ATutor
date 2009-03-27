<form method="POST" action="<?php echo url_rewrite('mods/social/edit_profile.php'); ?>">
<div>
	<div>
		<label for="university"><?php echo _AT('university'); ?></label>
		<input type="text" id="university" name="university" value="<?php echo $university; ?>" />
	</div>

	<div>
		<label for="country"><?php echo _AT('country'); ?></label>
		<input type="text" name="country" value="<?php echo $country; ?>" />
	</div>

	<div>
		<label for="province"><?php echo _AT('province'); ?></label>
		<input type="text" name="province" value="<?php echo $province; ?>" />
	</div>
	
	<div>
		<label for="degree"><?php echo _AT('degree'); ?></label>
		<input type="text" name="degree" value="<?php echo $degree; ?>" />
	</div>

	<div>
		<label for="field"><?php echo _AT('field'); ?></label>
		<input type="text" name="field" value="<?php echo $field; ?>" />
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

	<input type="hidden" name="add" value="education" />
	<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" />
	<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
</div>
</form>