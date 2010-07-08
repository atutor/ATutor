<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: language_import.php 8901 2009-11-11 19:10:19Z cindy $

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_LANGUAGES);

require(AT_INCLUDE_PATH.'classes/pclzip.lib.php');
require_once(AT_INCLUDE_PATH.'../mods/_core/languages/classes/LanguageEditor.class.php');
require_once(AT_INCLUDE_PATH.'../mods/_core/languages/classes/LanguagesParser.class.php');

/* to avoid timing out on large files */
@set_time_limit(0);

$_SESSION['done'] = 1;

if (isset($_POST['submit_import'])){
	require_once(AT_INCLUDE_PATH.'../mods/_core/languages/classes/RemoteLanguageManager.class.php');
	$remoteLanguageManager = new RemoteLanguageManager();
	$remoteLanguageManager->import($_POST['language']);
	header('Location: language_import.php');
	exit;
} else if (isset($_POST['submit']) && (!is_uploaded_file($_FILES['file']['tmp_name']) || !$_FILES['file']['size'])) {
	$msg->addError('LANG_IMPORT_FAILED');
} else if (isset($_POST['submit']) && !$_FILES['file']['name']) {
	$msg->addError('IMPORTFILE_EMPTY');
} else if (isset($_POST['submit']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
	$languageManager->import($_FILES['file']['tmp_name']);
	header('Location: ./language_import.php');
	exit;
}

?>
<?php require(AT_INCLUDE_PATH.'header.inc.php'); ?>

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
			require_once(AT_INCLUDE_PATH.'../mods/_core/languages/classes/RemoteLanguageManager.class.php');
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


<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>