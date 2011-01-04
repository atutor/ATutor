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
// $Id: LanguageManager.class.php 10300 2010-10-06 17:30:52Z cindy $

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
//	var $default_charset = 'iso-8859-1';
	var $default_charset = 'utf-8';

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
			$this->availableLanguages[$row['language_code']][$row['char_set']] = new Language($row);
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
				if (is_array($this->availableLanguages[$code]))
					foreach ($this->availableLanguages[$code] as $language)
						return $language;
//				return current($this->availableLanguages[$code]);
			} else {
				debug('return false');
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
		if (isset($_SESSION) && isset($_SESSION['lang']) && !empty($_SESSION['lang']) && isset($this->availableLanguages[$_SESSION['lang']])) {
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
		
		$unknown_language = current($this->availableLanguages);
		
		if (!$unknown_language) {
			return FALSE;
		}

		return current($unknown_language);
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

		require_once(AT_INCLUDE_PATH.'classes/pclzip.lib.php');
		require_once(AT_INCLUDE_PATH.'../mods/_core/languages/classes/LanguagesParser.class.php');
		
		$import_path = AT_CONTENT_DIR . 'import/';

		$archive = new PclZip($filename);
		if ($archive->extract(	PCLZIP_OPT_PATH,	$import_path) == 0) {
			exit('Error : ' . $archive->errorInfo(true));
		}

		$language_xml = @file_get_contents($import_path.'language.xml');

		$languageParser = new LanguageParser();
		$languageParser->parse($language_xml);
		$languageEditor = $languageParser->getLanguageEditor(0);

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
		global $msg;
		
		$zip_file_content = @file_get_contents(AT_LIVE_LANG_PACK_URL.$language_code);
		
		if (!$zip_file_content || substr($zip_file_content, 0, 6) == "Error:") {
			$msg->addError(array('REMOTE_ERROR', $zip_file_content));
			return;
		}
		
		// write the downloaded language pack into a temporary file for pclzip to unpack
		$lang_pack_zip = AT_CONTENT_DIR . 'import/'.md5(time()).'.zip';
		$fp = fopen($lang_pack_zip, 'w');
		fwrite($fp, $zip_file_content);
		fclose($fp);
		
		$this->import($lang_pack_zip);
		@unlink($lang_pack_zip);
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