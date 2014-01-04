<form action="<?php echo $_SERVER['PHP_SELF']; ?>?fid=<?php echo $this->fid; ?>&pid=<?php echo $this->pid; ?>" method="post" name="form">
<input type="hidden" name="edit_post" value="true" />
<input type="hidden" name="pid" value="<?php echo $this->pid; ?>" />
<input type="hidden" name="ppid" value="<?php echo $this->ppid; ?>" />
<input type="hidden" name="fid" value="<?php echo $this->forumid; ?>" />

<div class="input-form">
	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="subject"><?php echo _AT('subject'); ?></label><br />
		<input type="text" maxlength="80" name="subject" size="36" value="<?php echo stripslashes(htmlspecialchars($this->subject)); ?>" id="subject" />
	</div>
	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="body"><?php echo _AT('body'); ?></label><br />
		<textarea cols="65" name="body" rows="10" id="body"><?php echo AT_print($this->body, 'text.input'); ?></textarea>
	</div>
	<div class="row">
		<small>&middot; <?php echo _AT('forum_links'); ?><br />
		&middot; <?php echo _AT('forum_email_links'); ?><br />
		&middot; <?php echo _AT('forum_html_disabled'); ?></small>	
		<a href="<?php echo htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES); ?>#jumpcodes" title="<?php echo _AT('jump_codes'); ?>"><img src="images/clr.gif" height="1" width="1" alt="<?php echo _AT('jump_codes'); ?>" border="0" /></a>
		<?php require(AT_INCLUDE_PATH.'html/code_picker.inc.php'); ?>
		<a name="jumpcodes"></a>
    </div>
	<div class="row buttons">
		<input name="submit" type="submit" value="  <?php echo _AT('save'); ?>" accesskey="s" />
		<input type="submit" name="cancel" value=" <?php echo _AT('cancel'); ?> " />
	</div>
</div>
</form>
