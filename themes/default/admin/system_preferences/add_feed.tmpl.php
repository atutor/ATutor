<?php
global $stripslashes;

if (!isset($_POST['confirm'])) {
?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
		<input type="hidden" name="confirm" value="1" />

		<div class="input-form" style="width: 95%x">
			<div class="row">
				<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="title"><?php echo _AT('title'); ?></label><br />
				<input id="title" name="title" type="text" size="60" maxlength="255" value="<?php echo $stripslashes(htmlspecialchars($_POST['title'])); ?>" /><br />
			</div>

			<div class="row">
				<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="url"><?php echo _AT('url'); ?></label><br />
				<input id="url" name="url" type="text" size="60" maxlength="255" value="<?php echo $stripslashes(htmlspecialchars($_POST['url'])); ?>" /><br />
			</div>

			<div class="row buttons">
				<input type="submit" name="submit" value=" <?php echo _AT('save'); ?> " accesskey="s" />
				<input type="submit" name="cancel" value=" <?php echo _AT('cancel'); ?> " />
			</div>
		</div>
	</form>
<?php 
} else { ?>

	<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<input type="hidden" name="new" value="<?php echo $_POST['new']; ?>" />

	<div class="input-form">
		<div class="row">
			<h3><?php if (file_exists($this->title_file)) { 
					readfile($this->title_file); 
				} else {
					echo $_POST['title'];
				}?>
			</h3>
		</div>

		<div class="row">
			<?php echo $this->output; ?>
		</div>
	</div>
	</form>

	<?php
		$this->msg->printConfirm();
}
?>