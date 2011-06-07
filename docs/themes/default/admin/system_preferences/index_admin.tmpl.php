<?php global $_config; ?>

<div style="width:95%;margin-left:auto;margin-right:auto;">
	<div class="headingbox"><h3><?php echo _AT('admin_social'); ?></h3></a></div>
	<div class="contentbox">
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
		<p><?php echo _AT('shindig_blurb'); ?></p>

				<label for="shindig"><?php echo _AT('shindig_url'); ?></label>
				<input type="text" id="shindig" name="shindig_url" size="60" value="<?php echo $_config['shindig_url']; ?>" />

		<input type="submit" name="save" value="<?php echo _AT('save'); ?>" />
	</form>
	</div>
</div>
