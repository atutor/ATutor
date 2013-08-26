<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
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
	var $atutor_version;

	var $db;

	// constructor
	function Language($language_row) {
		global $db;

		$this->db = $db;

		if (is_array($language_row)) {
			$this->code              = $language_row['language_code'];
			$this->characterSet      = $language_row['char_set'];
			$this->direction         = $language_row['direction'];
			$this->regularExpression = $language_row['reg_exp'];
			$this->nativeName        = $language_row['native_name'];
			$this->englishName       = $language_row['english_name'];
			$this->status            = $language_row['status'];
			$this->atutor_version    = isset($language_row['version']) ? $language_row['version'] : VERSION;

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
		return preg_match('/^(' . $this->regularExpression . ')(;q=[0-9]\\.[0-9])?$/', $search_string);
	}

	// returns boolean whether or not $search_string is in HTTP_USER_AGENT
	function isMatchHttpUserAgent($search_string) {
		return preg_match('/(\(|\[|;[\s])(' . $this->regularExpression . ')(;|\]|\))/', $search_string);

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

	function getAtutorVersion() {
		return $this->atutor_version;
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

	function getStatus() {
		return $this->status;
	}


	// public
	function sendContentTypeHeader() {
		header('Content-Type: text/html; charset=' . $this->characterSet);
	}

	// public
	function saveToSession() {
		$_SESSION['lang'] = $this->code;
	}

	/* 
	 * public
	 * @param	member_id or login for members and admin respectively
	 * @param	1 for admin, 0 for members, all other integers are ignored. 
	 */
	function saveToPreferences($id, $is_admin) {
		global $db;
		if ($id) {
			if ($is_admin === 0) {
				$sql = "UPDATE %smembers SET language='%s', creation_date=creation_date, last_login=last_login WHERE member_id=%d";
				queryDB($sql, array(TABLE_PREFIX, $this->code, $id));
			} elseif ($is_admin === 1) {
				$sql = "UPDATE %sadmins SET language='%s', last_login=last_login WHERE login='%s'";
				queryDB($sql, array(TABLE_PREFIX, $this->code, $id));
			}
		}
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
		$sql = "SELECT *, UNIX_TIMESTAMP(L.revised_date) AS revised_date_unix FROM %slanguage_text L WHERE L.language_code='%s' AND L.variable='_template' AND L.term='%s'";
		$row = queryDB($sql, array(TABLE_PREFIX, $this->getCode(), $term), TRUE);
		return $row;
	}

	function getXML($part=FALSE) {
		if (!$part) {
			$xml = '<?xml version="1.0" encoding="UTF-8"?>
			<!-- This is an ATutor language pack - http://www.atutor.ca-->

			<!DOCTYPE language [
			   <!ELEMENT atutor-version (#PCDATA)>
			   <!ELEMENT code (#PCDATA)>
			   <!ELEMENT charset (#PCDATA)>
			   <!ELEMENT direction (#PCDATA)>
			   <!ELEMENT reg-exp (#PCDATA)>
			   <!ELEMENT native-name (#PCDATA)>
			   <!ELEMENT english-name (#PCDATA)>
			   <!ELEMENT status (#PCDATA)>

			   <!ATTLIST language code ID #REQUIRED>
			]>';
		} 

		$xml .= '<language code="'.$this->code.'">
			<atutor-version>'.VERSION.'</atutor-version>
			<charset>'.$this->characterSet.'</charset>
			<direction>'.$this->direction.'</direction>
			<reg-exp>'.$this->regularExpression.'</reg-exp>
			<native-name>'.$this->nativeName.'</native-name>
			<english-name>'.$this->englishName.'</english-name>
			<status>'.$this->status.'</status>
		</language>';

		return $xml;
	}
}
?>