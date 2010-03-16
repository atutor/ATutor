<fieldset>
<legend><strong><?php echo _AT("alt_to_text"); ?></strong> </legend>

	<div class="row">
		<label for="preferred_alt_to_text"><?php echo _AT("alt_to_text"); ?></label>
		<select id="preferred_alt_to_text" name="preferred_alt_to_text">
			<option selected="selected" value="audio">Audio</option>
			<option value="visual">Visual</option>
			<option value="sign_lang">Sign Language</option>
		</select>
	</div>

	<div class="row"><?php echo _AT('append_or_replace'); ?>
		<input type="radio" checked="checked" value="append" id="ar_append" name="alt_to_text_append_or_replace">
		<label for="ar_append">Append</label>
		<input type="radio" value="replace" id="ar_replace" name="alt_to_text_append_or_replace">
		<label for="ar_replace">Replace</label>
	</div>

	<div class="row">
		<label for="alt_text_prefer_lang"><?php echo _AT('prefer_lang'); ?></label>
		<select id="alt_text_prefer_lang" name="alt_text_prefer_lang">
			<option selected="selected" value="en">English - English</option>
		</select>
	</div>

</fieldset>
