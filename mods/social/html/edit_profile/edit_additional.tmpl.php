<?php
	global $addslashes;
	//escape all strings
	$title  = $addslashes($this->title);
	$interests	 = $addslashes($this->interests);
	$associations = $addslashes($this->associations);
	$awards		 = $addslashes($this->awards);
?>
<form method="POST" action="<?php echo url_rewrite('mods/social/edit_profile.php'); ?>">
<div>
	<div>
		<label for="<?php echo $title;?>"><?php echo _AT($title); ?></label>
		<input type="text" size="100" id="<?php echo $title;?>" name="<?php echo $title;?>" value="<?php echo $$title; ?>" />
	</div>
	
	<?php if (isset($this->id)): ?>
	<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
	<input type="hidden" name="edit" value="<?php echo $title; ?>" />
	<?php else: ?>	
	<input type="hidden" name="add" value="<?php echo $title; ?>" />
	<?php endif; ?>
	<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" />
	<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
</div>
</form>