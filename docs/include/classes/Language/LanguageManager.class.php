<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2007 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
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
		global $db;

		$sql	= 'SELECT * FROM '.TABLE_PREFIX.'languages ORDER BY native_name';
		$result = mysql_query($sql, $db);
		while($row = mysql_fetch_assoc($result)) {
			if (defined('AT_DEVEL_TRANSLATE') && AT_DEVEL_TRANSLATE) {
				$row['status'] = AT_LANG_STATUS_PUBLISHED; // b/c the print drop down checks for it.				
			}
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
		global $addslashes, $db; 

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

	function getAvailableLanguages() {
		return $this->availableLanguages;
	}

	// public
	function printDropdown($current_language, $name, $id) {
		echo '<select name="'.$name.'" id="'.$id.'">';

		foreach ($this->availableLanguages as $codes) {
			$language = current($codes);
			if ((defined('AT_DEVEL_TRANSLATE') && AT_DEVEL_TRANSLATE) || ($language->getStatus() == AT_LANG_STATUS_PUBLISHED)) {
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

	// public
	// import language pack from specified file
	function import($filename) {
		global $languageManager, $msg;

		$import_path = AT_CONTENT_DIR . 'import/';

		$archive = new PclZip($filename);
		if ($archive->extract(	PCLZIP_OPT_PATH,	$import_path) == 0) {
			exit('Error : ' . $archive->errorInfo(true));
		}

		$language_xml = @file_get_contents($import_path.'language.xml');

		$languageParser =& new LanguageParser();
		$languageParser->parse($language_xml);
		$languageEditor =& $languageParser->getLanguageEditor(0);

		if (($languageEditor->getAtutorVersion() != VERSION) 
			&& (!defined('AT_DEVEL_TRANSLATE') || !AT_DEVEL_TRANSLATE)) 
			{
				$msg->addError('LANG_WRONG_VERSION');
		}

		if (($languageEditor->getStatus() != AT_LANG_STATUS_PUBLISHED) 
			&& ($languageEditor->getStatus() != AT_LANG_STATUS_COMPLETE) 
			&& (!defined('AT_DEVEL_TRANSLATE') || !AT_DEVEL_TRANSLATE)) 
			{
				$msg->addError('LANG_NOT_COMPLETE');
		}

		if ($languageManager->exists($languageEditor->getCode())) {
			$msg->addError('LANG_EXISTS');
		}

		if (!$msg->containsErrors()) {
			$languageEditor->import($import_path . 'language_text.sql');
			$msg->addFeedback('IMPORT_LANG_SUCCESS');
		}

		// remove the files:
		@unlink($import_path . 'language.xml');
		@unlink($import_path . 'language_text.sql');
		@unlink($import_path . 'readme.txt');
		@unlink($filename);
	}

	// public
	// imports LIVE language from the atutor language database
	function liveImport($language_code) {
		global $db;

		$tmp_lang_db = mysql_connect(AT_LANG_DB_HOST, AT_LANG_DB_USER, AT_LANG_DB_PASS);
		if (!$tmp_lang_db) {
			/* AT_ERROR_NO_DB_CONNECT */
			echo 'Unable to connect to db.';
			exit;
		}
		if (!mysql_select_db('dev_atutor_langs', $tmp_lang_db)) {
			echo 'DB connection established, but database "dev_atutor_langs" cannot be selected.';
			exit;
		}

		$sql = "SELECT * FROM languages_SVN WHERE language_code='$language_code'";
		$result = mysql_query($sql, $tmp_lang_db);

		if ($row = mysql_fetch_assoc($result)) {
			$row['reg_exp'] = addslashes($row['reg_exp']);
			$row['native_name'] = addslashes($row['native_name']);
			$row['english_name'] = addslashes($row['english_name']);

			$sql = "REPLACE INTO ".TABLE_PREFIX."languages VALUES ('{$row['language_code']}', '{$row['char_set']}', '{$row['direction']}', '{$row['reg_exp']}', '{$row['native_name']}', '{$row['english_name']}', 3)";
			$result = mysql_query($sql, $db);

			$sql = "SELECT * FROM language_text_SVN WHERE language_code='$language_code'";
			$result = mysql_query($sql, $tmp_lang_db);

			$sql = "REPLACE INTO ".TABLE_PREFIX."language_text VALUES ";
			while ($row = mysql_fetch_assoc($result)) {
				$row['text'] = addslashes($row['text']);
				$row['context'] = addslashes($row['context']);
				$sql .= "('{$row['language_code']}', '{$row['variable']}', '{$row['term']}', '{$row['text']}', '{$row['revised_date']}', '{$row['context']}'),";
			}
			$sql = substr($sql, 0, -1);
			mysql_query($sql, $db);
		}
	}
	
	function getXML() {
		global $db;

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