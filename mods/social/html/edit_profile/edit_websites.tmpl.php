<?php
	global $addslashes;

	//escape all strings
	$url		= $addslashes($this->url);
	$site_name	= $addslashes($this->site_name);
?>
<div class="headingbox"><h3><?php echo _AT('edit_website'); ?></h3></a></div>
<div class="contentbox">
<form method="POST" action="<?php echo url_rewrite('mods/social/edit_profile.php'); ?>">
	<dl id="public-profile">
	<dt><label for="url"><?php echo _AT('url'); ?></label></dt>
	<dd><input type="text" id="url" name="url" value="<?php echo $url; ?>" /></dd>

	<dt><label for="site_name"><?php echo _AT('site_name'); ?></label></dt>
	<dd><input type="text" id="site_name" name="site_name" value="<?php echo $site_name; ?>" /></dd>

	<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
	<input type="hidden" name="edit" value="websites" />
	<input type="submit" name="submit" class="button" value="<?php echo _AT('save'); ?>" />
	<input type="submit" name="cancel" class="button" value="<?php echo _AT('cancel'); ?>" />
	</dl>
</div>
</form>