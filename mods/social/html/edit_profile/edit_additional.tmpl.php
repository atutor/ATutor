<?php
	global $addslashes;
	//escape all strings
	$title			= htmlentities_utf8($this->title);
	$interests		= htmlentities_utf8($this->interests, false);
	$associations	= htmlentities_utf8($this->associations, false);
	$awards			= htmlentities_utf8($this->awards, false);
?>
<form method="POST" action="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'edit_profile.php'); ?>">
<div>
	
		<label for="<?php echo $title;?>"><?php echo _AT($title); ?></label>
<div>
		<textarea rows="4" cols="60" id="<?php echo $title;?>" name="<?php echo $title;?>" /><?php echo $$title; ?></textarea>
	</div>
	
	<?php if (isset($this->id)): ?>
	<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
	<input type="hidden" name="edit" value="<?php echo $title; ?>" />
	<?php else: ?>	
	<input type="hidden" name="add" value="<?php echo $title; ?>" />
	<?php endif; ?>
	<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" class="button"/>
	<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" class="button"/>
</div>
</form>