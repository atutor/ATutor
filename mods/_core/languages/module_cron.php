<?php

// this cron checks for new available languages and installs them if found.
function languages_cron() {
	global $_config;
	if (!$_config['auto_install_languages']) { return; }


	global $languageManager;

	require(AT_INCLUDE_PATH.'classes/Language/RemoteLanguageManager.class.php');
	require(AT_INCLUDE_PATH.'classes/pclzip.lib.php');

	$remoteLanguageManager =& new RemoteLanguageManager();

	$languages = $remoteLanguageManager->getAvailableLanguages();

	foreach ($languages as $codes) {
		$language = current($codes);
		if (($language->getStatus() == AT_LANG_STATUS_PUBLISHED) && !$languageManager->exists($language->getCode())) {
			// language does not exist

			$remoteLanguageManager->import($language->getCode());
		}
	}
}

?>