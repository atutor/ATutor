<?php global $stripslashes;?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" name="form">
	<input type="hidden" name="fid" value="<?php echo $this->feed_id; ?>" />
	<div class="input-form" style="width:95%">
		<div class="row">
			<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="title"><?php echo _AT('title'); ?></label><br />
			<input id="title" name="title" type="text" size="60" maxlength="255" value="<?php echo $stripslashes(htmlspecialchars($_GET['title'])); ?>" /><br />
		</div>

		<div class="row">
			<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="url"><?php echo _AT('url'); ?></label><br />
			<input id="url" name="url" type="text" size="60" maxlength="255" value="<?php echo $stripslashes(htmlspecialchars($_GET['url'])); ?>" /><br />
		</div>

		<div class="row buttons">
			<input type="submit" name="submit" value=" <?php echo _AT('save'); ?> " accesskey="s" />
			<input type="submit" name="cancel" value=" <?php echo _AT('cancel'); ?> " />
		</div>
	</div>
</form>
