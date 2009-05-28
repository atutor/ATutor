<?php
	global $addslashes;

	//escape all strings
	$url		= htmlentities_utf8($this->url);
	$site_name	= htmlentities_utf8($this->site_name);
?>
<div class="headingbox"><h3><?php if($_GET['id']){echo _AT('edit_websites');}else{echo  _AT('add_new_website');}?></h3></div>
<div class="contentbox">
<form method="POST" action="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'edit_profile.php'); ?>">
	<dl id="public-profile">
	<dt><label for="url"><?php echo _AT('url'); ?></label></dt>
	<dd><input type="text" id="url" name="url" value="<?php echo $url; ?>" /></dd>

	<dt><label for="site_name"><?php echo _AT('site_name'); ?></label></dt>
	<dd><input type="text" id="site_name" name="site_name" value="<?php echo $site_name; ?>" /></dd>

	<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
		<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
		<?php if($_GET['id']){ ?>
		<input type="hidden" name="edit" value="websites" />
		<?php }else { ?>
		<input type="hidden" name="add" value="websites" />
		<?php } ?>
	<input type="submit" name="submit" class="button" value="<?php echo _AT('save'); ?>" />
	<input type="submit" name="cancel" class="button" value="<?php echo _AT('cancel'); ?>" />
	</dl>
</div>
</form>