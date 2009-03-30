<div class="headingbox"><h3><?php echo _AT('add_new_position'); ?></h3></a></div>
<div class="contentbox">
<form method="POST" action="<?php echo url_rewrite('mods/social/edit_profile.php'); ?>">
<div style="width:60%;">

	<dl id="public-profile">
		<dt><label for="company"><?php echo _AT('company'); ?>:</label></dt>
		<dd><input type="text" id="company" name="company" value="<?php echo $company; ?>" /></dd>
	      
		<dt><label for="title"><?php echo _AT('title'); ?>:</label></dt>
		<dd><input type="text" id="title" name="title" value="<?php echo $title; ?>" /></dd>

		<dt><label for="from"><?php echo _AT('from'); ?>:</label></dt>
		<dd><input type="text" id="from" name="from" value="<?php echo $from; ?>" /></dd>

		<dt><label for="to"><?php echo _AT('to'); ?></label>	</dt>
		<dd><input type="text" id="to" name="to" value="<?php echo $to; ?>" /></dd>


		<dt><label for="description"><?php echo _AT('description'); ?></label>	</dt>
		<dd><textarea name="description" id="description" cols="40" rows="4"><?php echo $description; ?></textarea></dd>
	</dl>

	<input type="hidden" name="add" value="position" />
	<input type="submit" name="submit" class="button" value="<?php echo _AT('save'); ?>" />
	<input type="submit" name="cancel"  class="button" value="<?php echo _AT('cancel'); ?>" />
</div>
</form>
</div>