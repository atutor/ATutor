<?php global $languageManager;?>
<form name="form1" method="post" action="mods/_core/languages/language_import.php" enctype="multipart/form-data" onsubmit="openWindow('<?php echo AT_BASE_HREF; ?>tools/prog.php');">
<div class="input-form">
	<div class="row">
		<p><?php echo _AT('import_lang_howto'); ?></p>
	</div>
	
	<div class="row">
		<label for="file"><?php echo _AT('import_a_new_lang'); ?></label><br />
		<input type="file" name="file" id="file" />
	</div>
	
	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('import'); ?>" />
	</div>
</div>
</form>
<form name="form1" method="post" action="mods/_core/languages/language_import.php">
<div class="input-form">
	<div class="row">
		<?php echo _AT('import_remote_language'); ?>
	</div>

	<div class="row">
		<?php
			
			$remoteLanguageManager = new RemoteLanguageManager();
			if ($remoteLanguageManager->getNumLanguages()) {
				$found = false;
				foreach ($remoteLanguageManager->getAvailableLanguages() as $codes){
					$language = current($codes);
					if (!$languageManager->exists($language->getCode()) && ($language->getStatus() == AT_LANG_STATUS_PUBLISHED)) {
						if (!$found) {
							echo '<select name="language">';
							$found = true;
						}
						echo '<option value="'.$language->getCode().'">'.$language->getEnglishName().' - '.$language->getNativeName().'</option>';
					}
				}
				if ($found) {
					echo '</select></div>';
					echo '<div class="row buttons"><input type="submit" name="submit_import" value="' . _AT('import') . '" class="button" /></div>';
				} else {
					echo _AT('none_found');
					echo '</div>';
				}
			} else {
				echo _AT('cannot_find_remote_languages');
				echo '</div>';
			}
		?>
</div>
</form>