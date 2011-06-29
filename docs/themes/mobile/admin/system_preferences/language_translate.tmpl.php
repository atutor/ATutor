
<form method="get">
<div class="input-form">
	<div class="row">
		<h3><?php echo _AT('translate'); ?></h3>
	</div>

	<div class="row">
		<p><?php echo _AT('translate_lang_howto'); ?></p>
	</div>

	<div class="row buttons">
		<input type="button" onclick="javascript:window.open('<?php echo AT_BASE_HREF; ?>mods/_core/languages/translate_atutor.php', 'newWin1', 'toolbar=0, location=0, directories=0, status=0, menubar=0, scrollbars=1, resizable=1, copyhistory=0, width=640, height=480')" value="<?php echo _AT('translate'); ?>" <?php echo $button_state; ?> />
	</div>
</div>
</form>

<?php if (defined('AT_DEVEL_TRANSLATE') && AT_DEVEL_TRANSLATE): ?>
	<form name="form1" method="post" action="mods/_core/languages/language_translate.php">
		<div class="input-form">
			<div class="row">
				Import partial language from the <strong>live ATutor language database</strong> to your local installation for translating.
			</div>
			<div class="row">
				<?php
					require_once(AT_INCLUDE_PATH.'../mods/_core/languages/classes/RemoteLanguageManager.class.php');
					$remoteLanguageManager = new RemoteLanguageManager();
					$remoteLanguageManager->printDropdown($_SESSION['lang'], 'import_lang', 'import_lang');
				?>
			</div>

			<div class="row buttons">
				<input type="submit" name="svn_submit" value="<?php echo _AT('import'); ?>" />
			</div>
		</div>
	</form>
<?php endif; ?>
