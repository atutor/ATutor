<?php
	global $addslashes;

	//escape all strings
	$university  = $addslashes($this->university);
	$country	 = $addslashes($this->country);
	$province	 = $addslashes($this->province);
	$degree		 = $addslashes($this->degree);
	$field		 = $addslashes($this->field);
	$from		 = intval($this->from);
	$to			 = intval($this->to);
	$description = $addslashes($this->description);
?>
<div class="headingbox"><h3><?php echo _AT('edit_education'); ?></h3></a></div>
<div class="contentbox">
<form method="POST" action="<?php echo url_rewrite('mods/social/edit_profile.php'); ?>">
	<dl id="public-profile">
		<dt><label for="university"><?php echo _AT('university'); ?></label></dt>
		<dd><input type="text" id="university" name="university" value="<?php echo $university; ?>" /></dd>
	
		<dt><label for="country"><?php echo _AT('country'); ?></label></dt>
		<dd><input type="text" id="country"  name="country" value="<?php echo $country; ?>" /></dd>
	
		<dt><label for="province"><?php echo _AT('province'); ?></label></dt>
		<dd><input type="text" id="province"  name="province" value="<?php echo $province; ?>" /></dd>
		
		<dt><label for="degree"><?php echo _AT('degree'); ?></label></dt>
		<dd><input type="text" id="degree"  name="degree" value="<?php echo $degree; ?>" /></dd>
	
		<dt><label for="field"><?php echo _AT('field'); ?></label></dt>
		<dd><input type="text" id="field"  name="field" value="<?php echo $field; ?>" /></dd>
		
		<dt><label for="from"><?php echo _AT('from'); ?></label></dt>
		<dd><input type="text" id="from"  name="from" value="<?php echo $from; ?>" /></dd>
	
		<dt><label for="to"><?php echo _AT('to'); ?></label></dt>	
		<dd><input type="text" id="to"  name="to" value="<?php echo $to; ?>" /></dd>
	
		<dt><label for="description"><?php echo _AT('description'); ?></label></dt>	
		<dd><textarea name="description" id="description" cols="40" rows="5" ><?php echo $description; ?></textarea></dd>
	
		<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
		<input type="hidden" name="edit" value="education" />
		<input type="submit" name="submit" class="button" value="<?php echo _AT('save'); ?>" />
		<input type="submit" name="cancel" class="button" value="<?php echo _AT('cancel'); ?>" />
	</dl>
</div>
</form>