<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

/**
* Language
* Class for accessing information about a single language.
* @access	public
* @author	Joel Kronenberg
* @see		LanguageManager::getLanguage()
* @see		LanguageManager::getMyLanguage()
* @package	Language
*/
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
		if (is_array($language_row)) {
			$this->code              = $language_row['code'];
			$this->characterSet      = $language_row['char_set'];
			$this->direction         = $language_row['direction'];
			$this->regularExpression = $language_row['reg_exp'];
			$this->nativeName        = $language_row['native_name'];
			$this->englishName       = $language_row['english_name'];
		} else if (is_object($language_row)) {
			$this->cloneThis($language_row);
		}
	}

	// private
	// copies the properties from $from to $this Object
	function cloneThis($from) {
		$vars = get_object_vars($from);
		foreach ($vars as $key => $value) {
			$this->$key = $value;
		}
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

	function getTranslatedName() {
		if ($this->code == $_SESSION['lang']) {
			return $this->nativeName;
		}
		// this code has to be translated:
		return _AT('lang_' . str_replace('-', '_', $this->code));
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

	// public
	// returns whether or not this language is right-to-left
	// possible langues are: arabic, farsi, hebrew, urdo
	function isRTL() {
		if ($this->direction == 'rtl') {
			return true;
		} // else:

		return false;
	}

	// public
	// can be called staticly
	function findParent($code) {
		$peices = explode('-', $code, 2);
		return $peices[0];
	}
}
?>