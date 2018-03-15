
<form method="get">
<div class="input-form">
	<div class="row">
		<h3><?php echo _AT('translate'); ?></h3>
	</div>

	<div class="row">
		<p><?php echo _AT('translate_lang_howto'); ?></p>
	</div>

	<div class="row buttons">
		<input type="button" onclick="javascript:window.open('<?php echo AT_BASE_HREF; ?>mods/_core/languages/translate_atutor.php', 'newWin1', 'toolbar=0, location=0, directories=0, status=0, menubar=0, scrollbars=1, resizable=1, copyhistory=0, width=640, height=480')" value="<?php echo _AT('translate'); ?>" <?php echo $this->button_state; ?> />
	</div>
</div>
</form>


