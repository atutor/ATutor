<?php
	global $addslashes;

	//escape all strings
	$con_name       = AT_print($this->con_name, 'input.text');
	$con_phone      = AT_print($this->con_phone, 'input.text');
	$con_email      = AT_print($this->con_email, 'input.text');
	$con_address    = AT_print($this->con_address, 'input.text');

?>

<div class="headingbox"><h3><?php if($_GET['id']){echo _AT('edit_contact');}else{echo  _AT('add_new_contact');}?></h3></div>
<div class="contentbox">
<form method="post" action="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'edit_profile.php'); ?>">
	<dl id="public-profile">
		<dt><label for="con_name"><?php echo _AT('name'); ?></label></dt>
		<dd><input type="text" id="con_name" name="con_name" value="<?php echo $con_name; ?>" /></dd>

		<dt><label for="con_phone"><?php echo _AT('phone'); ?></label></dt>
		<dd><input type="text" id="con_phone"  name="con_phone" value="<?php echo $con_phone; ?>" /></dd>
		
		<dt><label for="con_email"><?php echo _AT('email'); ?></label></dt>
		<dd><input type="text" id="con_email"  name="con_email" value="<?php echo $con_email; ?>" /></dd>

		<dt><label for="con_address"><?php echo _AT('street_address'); ?></label></dt>	
		<dd><textarea name="con_address" id="con_address" cols="40" rows="5"><?php echo $con_address; ?></textarea></dd>
		</dl>
		<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
		<?php if($_GET['id']){ ?>
		<input type="hidden" name="edit" value="contact" />
		<?php }else { ?>
		<input type="hidden" name="add" value="contact" />
		<?php } ?>
	
		<input type="submit" name="submit" class="button" value="<?php echo _AT('save'); ?>" />
		<input type="submit" name="cancel" class="button" value="<?php echo _AT('cancel'); ?>" />
	
</form>
</div>