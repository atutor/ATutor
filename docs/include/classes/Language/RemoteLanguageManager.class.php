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

require_once(AT_INCLUDE_PATH . 'classes/Language/LanguageManager.class.php');


/**
* RemoteLanguageManager
* Class for managing available languages as Language Objects.
* @access	public
* @author	Joel Kronenberg
* @see		Language.class.php
* @package	Language
*/
class RemoteLanguageManager extends LanguageManager {

	function RemoteLanguageManager() {
		require_once(AT_INCLUDE_PATH.'classes/Language/LanguagesParser.class.php');

		$language_xml = file_get_contents('http://142.150.64.112/svn/atutor/avail_languages.php?version=1.4.2');

		$languageParser =& new LanguagesParser();
		$languageParser->parse($language_xml);

		$this->numLanguages = $languageParser->getNumLanguages();

		for ($i = 0; $i < $this->numLanguages; $i++) {
			$thisLanguage =& new Language($languageParser->getLanguage($i));

			$this->availableLanguages[$thisLanguage->getCode()][$thisLanguage->getCharacterSet()] =& $thisLanguage;
		}
		
	}
}

?>