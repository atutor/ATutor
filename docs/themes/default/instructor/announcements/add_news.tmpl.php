	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
	<input type="hidden" name="add_news" value="true" />
	<input type="submit" name="submit" style="display:none;"/>
	<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('add_announcement'); ?></legend>
		<div class="row">
			<label for="title"><?php echo _AT('title'); ?></label><br />
			<input type="text" name="title" size="40" id="title" value="<?php echo $_POST['title']; ?>" />
		</div>

		<div class="row">
			<?php echo _AT('formatting'); ?><br />
			<input type="radio" name="formatting" value="0" id="text" <?php if ($_POST['formatting'] == 0) { echo 'checked="checked"'; } ?> onclick="javascript: document.form.setvisual.disabled=true;" <?php if ($_POST['setvisual'] && !$_POST['settext']) { echo 'disabled="disabled"'; } ?> />

			<label for="text"><?php echo _AT('plain_text'); ?></label>
			<input type="radio" name="formatting" value="1" id="html" <?php if ($_POST['formatting'] == 1 || $_POST['setvisual']) { echo 'checked="checked"'; } ?> onclick="javascript: document.form.setvisual.disabled=false;"/>

			<label for="html"><?php echo _AT('html'); ?></label>
			<?php   //Button for enabling/disabling visual editor
				if (($_POST['setvisual'] && !$_POST['settext']) || $_GET['setvisual']){
					echo '<input type="hidden" name="setvisual" value="'.$_POST['setvisual'].'" />';
					echo '<input type="submit" name="settext" value="'._AT('switch_text').'" class="button"/>';
				} else {
					echo '<input type="submit" name="setvisual" value="'._AT('switch_visual').'"  ';
					if ($_POST['formatting']==0) { echo 'disabled="disabled"'; }
					echo ' class="button" />';
				}
			?>
		</div>

		<div class="row">
			<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="body_text"><?php echo _AT('body'); ?></label><br />
			<textarea name="body_text" cols="40" rows="15" id="body_text"><?php echo $_POST['body_text']; ?></textarea>
		</div>
		
		<div class="row buttons">
			<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s"  class="button"/>
			<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?> "  class="button"/>
		</div>
	</fieldset>
	</div>
	</form>