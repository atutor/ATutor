<?php
	global $addslashes;

	//escape all strings
	$rep_name  	 = htmlentities_utf8($this->rep_name);
	$rep_title	 = htmlentities_utf8($this->rep_title);
	$rep_phone	 = htmlentities_utf8($this->rep_phone);
	$rep_email	 = htmlentities_utf8($this->rep_email);
	$rep_address	 = htmlentities_utf8($this->rep_address);

?>

<div class="headingbox"><h3><?php if($_GET['id']){echo _AT('edit_representation');}else{echo  _AT('add_new_representation');}?></h3></div>
<div class="contentbox">
<form method="post" action="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'edit_profile.php'); ?>">
	<dl id="public-profile">
		<div class="row">
		<dt><label for="rep_name"><?php echo _AT('name'); ?></label></dt>
		<dd><input type="text" id="rep_name" name="rep_name" value="<?php echo $rep_name; ?>" /></dd>
		</div>
		<div class="row">
		<dt><label for="rep_title"><?php echo _AT('title'); ?></label></dt>
		<dd><input type="text" id="rep_title"  name="rep_title" value="<?php echo $rep_title; ?>" /></dd>
		</div>
		<div class="row">
		<dt><label for="rep_phone"><?php echo _AT('phone'); ?></label></dt>
		<dd><input type="text" id="rep_phone"  name="rep_phone" value="<?php echo $rep_phone; ?>" /></dd>
		</div>
		<div class="row">
		<dt><label for="rep_email"><?php echo _AT('email'); ?></label></dt>
		<dd><input type="text" id="rep_email"  name="rep_email" value="<?php echo $rep_email; ?>" /></dd>
		</div>
		
		<dt><label for="rep_address"><?php echo _AT('street_address'); ?></label></dt>	
		<dd><textarea name="rep_address" id="rep_address" cols="40" rows="5"><?php echo $rep_address; ?></textarea></dd>
		</dl>
		
		<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
		<?php if($_GET['id']){ ?>
		<input type="hidden" name="edit" value="representation" />
		<?php }else { ?>
		<input type="hidden" name="add" value="representation" />
		<?php } ?>
	
		<input type="submit" name="submit" class="button" value="<?php echo _AT('save'); ?>" />
		<input type="submit" name="cancel" class="button" value="<?php echo _AT('cancel'); ?>" />
	
</form>
</div>