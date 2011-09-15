
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="edit_news" value="true">
<input type="hidden" name="aid" value="<?php echo $this->row['news_id']; ?>">
<input type="submit" name="submit" style="display:none;"/>
<div class="input-form">
	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="title"><?php echo _AT('title'); ?></label><br />
		<input type="text" name="title" id="title" value="<?php echo AT_print($this->row['title'], 'input.text'); ?>" size="40">
	</div>

	<div class="row">
		<?php echo _AT('formatting'); ?><br />
		<input type="radio" name="formatting" value="0" id="text" <?php if ($_POST['formatting'] === 0) { echo 'checked="checked"'; } ?> onclick="javascript: document.form.setvisual.disabled=true;" <?php if ($_POST['setvisual'] && !$_POST['settext']) { echo 'disabled="disabled"'; } ?> /><label for="text"><?php echo _AT('plain_text'); ?></label>,

		<input type="radio" name="formatting" value="1" id="html" <?php if ($_POST['formatting'] == 1 || $_POST['setvisual']) { echo 'checked="checked"'; } ?> onclick="javascript: document.form.setvisual.disabled=false;"  /> <label for="html"><?php echo _AT('html'); ?></label>
		<?php
			if (($_POST['setvisual'] && !$_POST['settext']) || $_GET['setvisual']){
				echo '<input type="hidden" name="setvisual" value="'.$_POST['setvisual'].'" />';
				echo '<input type="submit" name="settext"   value="'._AT('switch_text').'" />';
			} else {
				echo '<input type="submit" name="setvisual" value="'._AT('switch_visual').'" ';
				if ($_POST['formatting']==0) { echo 'disabled="disabled"'; }
				echo '/>';
			} 
		?>
	</div>

	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="body_text"><?php echo _AT('body'); ?></label><br />
		<textarea name="body_text" cols="55" rows="15" id="body_text" wrap="wrap"><?php echo AT_print($this->row['body'], 'input.text'); ?></textarea>
	</div>

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?> " />
	</div>


</div>
</form>