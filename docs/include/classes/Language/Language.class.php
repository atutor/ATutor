<?php
class Language {
	// all private
	var $code;
	var $characterSet;
	var $direction;
	var $regularExpression;
	var $nativeName;
	var $englishName;

	// constructor
	function Language($language_row) {
		$this->code              = $language_row['code'];
		$this->characterSet      = $language_row['char_set'];
		$this->direction         = $language_row['direction'];
		$this->regularExpression = $language_row['reg_exp'];
		$this->nativeName        = $language_row['native_name'];
		$this->englishName       = $language_row['english_name'];
	}

	// returns whether or not the $search_string matches the regular expression
	function isMatchHttpAcceptLanguage($search_string) {
		return eregi('^(' . $this->regularExpression . ')(;q=[0-9]\\.[0-9])?$', $search_string);
	}

	// returns boolean whether or not $search_string is in HTTP_USER_AGENT
	function isMatchHttpUserAgent($search_string) {
		return eregi('(\(|\[|;[[:space:]])(' . $this->regularExpression . ')(;|\]|\))', $search_string);

	}


	function getCharacterSet() {
		return $this->characterSet;
	}

	function getDirection() {
		// return something;
	}

	function getCode() {
		return $this->code;
	}

	function getNativeName() {
		return $this->nativeName;
	}

	function getEnglishName() {
		return $this->englishName;
	}

	// public
	function sendContentTypeHeader() {
		header('Content-Type: text/html; charset=' . $this->characterSet);
	}

	// public
	function saveToSession() {
		$_SESSION['lang'] = $this->code;
	}
}
?>