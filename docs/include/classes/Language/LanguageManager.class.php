<?php

require(AT_INCLUDE_PATH . 'classes/Language/Language.class.php');

class LanguageManager {

	var $availableLanguages; // private. list of available languages
	var $default_lang = 'en';
	var $default_charset = 'iso-8859-1';

	// constructor:
	function LanguageManager() {
		global $lang_db;

		$sql	= 'SELECT * FROM '.TABLE_PREFIX_LANG.'languages ORDER BY native_name';
		$result = mysql_query($sql, $lang_db);
		while($row = mysql_fetch_assoc($result)){
			$this->availableLanguages[$row['code']][$row['char_set']] =& new Language($row);
		}
	}


	// private
	function getLanguage($code, $charset = '') {
		if (!$charset) {
			return current($this->availableLanguages[$code]);
		}

		foreach ($this->availableLanguages[$code] as $language) {
			if ($language->getCharacterSet() == $charset) {
				return $language;
			}
		}
	}

	// public
	// returns a Language Object
	function getMyLanguage() {
		if (isset($_GET) && !empty($_GET['lang']) && isset($this->availableLanguages[$_GET['lang']])) {
			$language = $this->getLanguage($_GET['lang']);

			if ($language) {
				return $language;
			}

		} else if (isset($_POST) && !empty($_POST['lang']) && isset($this->availableLanguages[$_POST['lang']])) {
			$language = $this->getLanguage($_POST['lang']);

			if ($language) {
				return $language;
			}

		} else if (isset($_SESSION) && !empty($_SESSION['lang']) && isset($this->availableLanguages[$_SESSION['lang']])) {
			$language = $this->getLanguage($_SESSION['lang']);

			if ($language) {
				return $language;
			}

		} else if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {

			// Language is not defined yet :
			// 1. try to find out user's language by checking its HTTP_ACCEPT_LANGUAGE
			//    variable
			$accepted    = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
			$acceptedCnt = count($accepted);
			reset($accepted);
			for ($i = 0; $i < $acceptedCnt; $i++) {
				foreach ($this->availableLanguages as $codes) {
					foreach ($codes as $language) {
						if ($language->isMatchHttpAcceptLanguage($accepted[$i])) {
							return $language;
						} else if ($language->isMatchHttpUserAgent($accepted[$i])) {
							return $language;
						}
					}
				}
			}
		} // else: 

		// 3. Didn't catch any valid lang : we use the default settings
		if (isset($this->availableLanguages[DEFAULT_LANGUAGE])) {
			$language = $this->getLanguage(DEFAULT_LANGUAGE, DEFAULT_CHARSET);

			if ($language) {
				return $language;
			}
		}

		// fail safe
		if (isset($this->availableLanguages[$this->default_lang])) {
			$language = $this->getLanguage($this->default_lang, $this->default_charset);

			if ($language) {
				return $language;
			}
		}

		return false;
	}

}




?>