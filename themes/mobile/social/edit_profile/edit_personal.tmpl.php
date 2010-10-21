<?php
	global $addslashes;

	//escape all strings
	$per_weight	 = htmlentities_utf8($this->per_weight);
	$per_height	 = htmlentities_utf8($this->per_height);
	$per_hair	 = htmlentities_utf8($this->per_hair);
	$per_eyes	 = htmlentities_utf8($this->per_eyes);
	$per_ethnicity	 = htmlentities_utf8($this->per_ethnicity);
	$per_languages	 = htmlentities_utf8($this->per_languages);
	$per_disabilities = htmlentities_utf8($this->per_disabilities);

?>

<div class="headingbox"><h3><?php if($_GET['id']){echo _AT('edit_personal');}else{echo  _AT('add_new_personal');}?></h3></div>
<div class="contentbox">
<form method="post" action="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'edit_profile.php'); ?>">
	<dl id="public-profile">
		<div class="row"> 
		<dt><label for="per_weight"><?php echo _AT('per_weight'); ?></label></dt>
		<dd><input type="text" id="per_weight"  name="per_weight" value="<?php echo $per_weight; ?>" /></dd>
		</div>	
		
		<div class="row">
		<dt><label for="per_height"><?php echo _AT('per_height'); ?></label></dt>
		<dd><input type="text" id="per_height"  name="per_height" value="<?php echo $per_height; ?>" /></dd>
		</div>		
		
		<div class="row">
		<dt><label for="per_hair"><?php echo _AT('per_hair'); ?></label></dt>
		<dd><input type="text" id="per_hair"  name="per_hair" value="<?php echo $per_hair; ?>" /></dd>
		</div>

		<div class="row">
		<dt><label for="per_eyes"><?php echo _AT('per_eyes'); ?></label></dt>
		<dd><input type="text" id="per_eyes"  name="per_eyes" value="<?php echo $per_eyes; ?>" /></dd>
		</div>
	
		<div class="row">
		<dt><label for="per_ethnicity"><?php echo _AT('per_ethnicity'); ?></label></dt>
		<dd><input type="text" id="per_ethnicity"  name="per_ethnicity" value="<?php echo $per_ethnicity; ?>" /></dd>
		</div>

		<div class="row">
		<dt><label for="per_languages"><?php echo _AT('per_languages'); ?></label></dt>
		<dd><input type="text" id="per_languages"  name="per_languages" value="<?php echo $per_languages; ?>" /></dd>
		</div>

		<div class="row">
		<dt><label for="per_disabilities"><?php echo _AT('per_disabilities'); ?></label></dt>
		<dd><input type="text" id="per_disabilities"  name="per_disabilities" value="<?php echo $per_disabilities; ?>" /></dd>
		</div>

		</dl>
		<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
		<?php if($_GET['id']){ ?>
		<input type="hidden" name="edit" value="personal" />
		<?php }else { ?>
		<input type="hidden" name="add" value="personal" />
		<?php } ?>
	
		<input type="submit" name="submit" class="button" value="<?php echo _AT('save'); ?>" />
		<input type="submit" name="cancel" class="button" value="<?php echo _AT('cancel'); ?>" />
	
</form>
</div>