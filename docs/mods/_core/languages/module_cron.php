<?php

// this cron checks for new available languages and installs them if found.
function languages_cron() {
	global $languageManager;

	require(AT_INCLUDE_PATH.'classes/Language/RemoteLanguageManager.class.php');
	require(AT_INCLUDE_PATH.'classes/pclzip.lib.php');

	$remoteLanguageManager =& new RemoteLanguageManager();

	$languages = $remoteLanguageManager->getAvailableLanguages();

	foreach ($languages as $codes) {
		$language = current($codes);
		if (($language->getStatus() == AT_LANG_STATUS_PUBLISHED) && !$languageManager->exists($language->getCode())) {
			// language does not exist

			$filename = AT_CONTENT_DIR . 'import/ATutor_language_file.zip';
			$remoteLanguageManager->fetchLanguage($language->getCode(), $filename);

			$import_path = AT_CONTENT_DIR . 'import/';
			$archive = new PclZip($filename);
			if ($archive->extract(	PCLZIP_OPT_PATH,	$import_path) == 0) {
				@unlink($filename);
				continue;
			}

			$language_xml = @file_get_contents($import_path.'language.xml');

			$languageParser =& new LanguageParser();
			$languageParser->parse($language_xml);
			$languageEditor =& $languageParser->getLanguageEditor(0);

			$languageEditor->import($import_path . 'language_text.sql');

			// remove the files:
			@unlink($import_path . 'language.xml');
			@unlink($import_path . 'language_text.sql');
			@unlink($import_path . 'readme.txt');
			@unlink($filename);
		}
	}
}

?>