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

define('AT_LANGUAGE_LOCALE_SEP', '-');

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
	var $status;

	var $db;

	// constructor
	function Language($language_row) {
		global $lang_db;

		$this->db = $lang_db;

		if (is_array($language_row)) {
			$this->code              = $language_row['language_code'];
			$this->characterSet      = $language_row['char_set'];
			$this->direction         = $language_row['direction'];
			$this->regularExpression = $language_row['reg_exp'];
			$this->nativeName        = $language_row['native_name'];
			$this->englishName       = $language_row['english_name'];
			$this->status            = $language_row['status'];

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

	function getCode() {
		return $this->code;
	}

	function getCharacterSet() {
		return $this->characterSet;
	}

	function getDirection() {
		return $this->direction;
	}

	function getRegularExpression() {
		return $this->regularExpression;
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
	function getParentCode($code = '') {
		if (!$code && isset($this)) {
			$code = $this->code;
		}
		$peices = explode(AT_LANGUAGE_LOCALE_SEP, $code, 2);
		return $peices[0];
	}

	// public
	// can be called staticly
	function getLocale($code = '') {
		if (!$code && isset($this)) {
			$code = $this->code;
		}
		$peices = explode(AT_LANGUAGE_LOCALE_SEP, $code, 2);
		return $peices[1];
	}

	
	// public
	function getTerm($term) {
		$sql = "SELECT *, UNIX_TIMESTAMP(L.revised_date) AS revised_date_unix FROM ".TABLE_PREFIX_LANG."language_text L WHERE L.language_code='".$this->getCode()."' AND L.variable='_template' AND L.term='$term'";

		$result = mysql_query($sql, $this->db);
		$row = mysql_fetch_assoc($result);
		return $row;
	}
}
?>