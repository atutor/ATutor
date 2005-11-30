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

require_once(dirname(__FILE__) . '/Language.class.php');

define('AT_LANG_STATUS_EMPTY',       0);
define('AT_LANG_STATUS_INCOMPLETE',  1);
define('AT_LANG_STATUS_COMPLETE',    2);
define('AT_LANG_STATUS_PUBLISHED',   3);

/**
* LanguageManager
* Class for managing available languages as Language Objects.
* @access	public
* @author	Joel Kronenberg
* @see		Language.class.php
* @package	Language
*/
class LanguageManager {

	/**
	* This array stores references to all the Language Objects
	* that are available in this installation.
	* @access private
	* @var array
	*/
	var $availableLanguages;

	/**
	* The fallback language if the DEFAULT_LANGUAGE isn't defined.
	* @access private
	* @var string
	*/
	var $default_lang = 'en';

	/**
	* The fallback charachter set if the DEFAULT_CHARSET isn't defined.
	* @access private
	* @var string
	*/
	var $default_charset = 'iso-8859-1';

	/**
	* The number of languages that are available. Does not include
	* character set variations.
	* @access private
	* @var integer
	*/
	var $numLanguages;

	/**
	* Constructor.
	* 
	* Initializes availableLanguages and numLanguages.
	*/
	function LanguageManager() {
		global $lang_db;

		$sql	= 'SELECT * FROM '.TABLE_PREFIX_LANG.'languages'.TABLE_SUFFIX_LANG.' ORDER BY native_name';
		$result = mysql_query($sql, $lang_db);
		while($row = mysql_fetch_assoc($result)) {
			if ((defined('TABLE_SUFFIX_LANG') && TABLE_SUFFIX_LANG) || (defined('AT_DEVEL_TRANSLATE') && AT_DEVEL_TRANSLATE)) {
				$row['status'] = AT_LANG_STATUS_PUBLISHED; // b/c the print drop down checks for it.				
			}
			//$this->availableLanguages[$row['language_code']][$row['char_set']] =& new Language($row);
			$this->availableLanguages[$row['language_code']][$row['char_set']] =& new Language($row);
		}
		$this->numLanguages = count($this->availableLanguages);
	}


	/**
	* Returns a valid Language Object based on the given language $code and optional
	* $charset, FALSE if it can't be found.
	* @access	public
	* @param	string $code		The language code of the language to return.
	* @param	string $charset		Optionally, the character set of the language to find.
	* @return	boolean|Language	Returns FALSE if the requested language code and
	*								character set cannot be found. Returns a Language Object for the
	*								specified language code and character set.
	* @see		getMyLanguage()
	*/
	function getLanguage($code, $charset = '') {
		if (!$charset) {
			if (isset($this->availableLanguages[$code])) {
				return current($this->availableLanguages[$code]);
			} else {
				return FALSE;
			}
		}

		foreach ($this->availableLanguages[$code] as $language) {
			if ($language->getCharacterSet() == $charset) {
				return $language;
			}
		}
		return FALSE;
	}

	/**
	* Tries to detect the user's current language preference/setting from (in order):
	* _GET, _POST, _SESSION, HTTP_ACCEPT_LANGUAGE, HTTP_USER_AGENT. If no match can be made
	* then it tries to detect a default setting (defined in config.inc.php) or a fallback
	* setting, false if all else fails.
	* @access	public
	* @return	boolean|Language	Returns a Language Object matching the user's current session.
	*								Returns FALSE if a valid Language Object cannot be found
	*								to match the request
	* @see		getLanguage()
	*/
	function getMyLanguage() {
		if (isset($_GET) && !empty($_GET['lang']) && isset($this->availableLanguages[$_GET['lang']])) {
			$language = $this->getLanguage($_GET['lang']);

			if ($language) {
				return $language;
			}

		} 
		if (isset($_POST) && !empty($_POST['lang']) && isset($this->availableLanguages[$_POST['lang']])) {
			$language = $this->getLanguage($_POST['lang']);

			if ($language) {
				return $language;
			}

		} 
		if (isset($_SESSION) && !empty($_SESSION['lang']) && isset($this->availableLanguages[$_SESSION['lang']])) {
			$language = $this->getLanguage($_SESSION['lang']);

			if ($language) {
				return $language;
			}
		}
		if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {

			// Language is not defined yet :
			// try to find out user's language by checking its HTTP_ACCEPT_LANGUAGE
			$accepted    = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
			$acceptedCnt = count($accepted);
			reset($accepted);
			for ($i = 0; $i < $acceptedCnt; $i++) {
				foreach ($this->availableLanguages as $codes) {
					foreach ($codes as $language) {
						if ($language->isMatchHttpAcceptLanguage($accepted[$i])) {
							return $language;
						}
					}
				}
			}
		}

		if (!empty($_SERVER['HTTP_USER_AGENT'])) {

			// Language is not defined yet :
			// try to find out user's language by checking its HTTP_USER_AGENT
			foreach ($this->availableLanguages as $codes) {
				foreach ($codes as $language) {
					if ($language->isMatchHttpUserAgent($_SERVER['HTTP_USER_AGENT'])) {
						return $language;
					}
				}
			}
		}

		// Didn't catch any valid lang : we use the default settings
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

		// else pick one at random:
		reset($this->availableLanguages);
		$uknown_language = current($this->availableLanguages);
		if ($unknown_language) {
			return FALSE;
		}

		return current($uknown_language);
	}


	// public
	function printDropdown($current_language, $name, $id) {
		echo '<select name="'.$name.'" id="'.$id.'">';

		foreach ($this->availableLanguages as $codes) {
			$language = current($codes);
			if ($language->getStatus() == AT_LANG_STATUS_PUBLISHED) {
				echo '<option value="'.$language->getCode().'"';
				if ($language->getCode() == $current_language) {
					echo ' selected="selected"';
				}
				echo '>'.$language->getNativeName().'</option>';
			}
		}
		echo '</select>';
	}

	// public
	function printList($current_language, $name, $id, $url) {

		$delim = false;
		foreach ($this->availableLanguages as $codes) {
			$language = current($codes);

			if ($language->getStatus() == AT_LANG_STATUS_PUBLISHED) {

				if ($delim){
					echo ' | ';
				}

				if ($language->getCode() == $current_language) {
					echo '<strong>'.$language->getNativeName().'</strong>';
				} else {
					echo '<a href="'.$url.'lang='.$language->getCode().'">'.$language->getNativeName().'</a> ';
				}

				$delim = true;
			}
		}
	}

	// public
	function getNumLanguages() {
		return $this->numLanguages;
	}

	// public
	// checks whether or not the language exists/is available
	function exists($code, $locale = '') {
		if ($locale) {
			return isset($this->availableLanguages[$code . AT_LANGUAGE_LOCALE_SEP . $locale]);
		}
		return isset($this->availableLanguages[$code]);
	}

	
	function getXML() {
		global $lang_db;

		$lang_xml = '<?xml version="1.0" encoding="iso-8859-1"?>
		<!-- These are ATutor language packs - http://www.atutor.ca-->

		<!DOCTYPE languages [
		   <!ELEMENT language (atutor-version, code, charset, direction, reg-exp, native-name, english-name )>
		   <!ELEMENT atutor-version (#PCDATA)>
		   <!ELEMENT code (#PCDATA)>
		   <!ELEMENT charset (#PCDATA)>
		   <!ELEMENT direction (#PCDATA)>
		   <!ELEMENT reg-exp (#PCDATA)>
		   <!ELEMENT native-name (#PCDATA)>
		   <!ELEMENT english-name (#PCDATA)>
		   <!ELEMENT status (#PCDATA)>
		   <!ATTLIST language code ID #REQUIRED>
		]>

		<languages>';

		foreach ($this->availableLanguages as $codes) {
			foreach ($codes as $language) {
				$lang_xml .= $language->getXML(TRUE);
			}
		}

		$lang_xml .= "\r\n".'</languages>';

		return $lang_xml;
	}
}


?>