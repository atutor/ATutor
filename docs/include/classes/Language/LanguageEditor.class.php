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
* LanguageEditor
* Class for adding/editing language.
* @access	public
* @author	Heidi Hazelton
* @package	Language
*/
class LanguageEditor extends Language {
	/**
	* A reference to a valid database resource.
	* @access private
	* @var resource
	*/
	var $db;

	var $addslashes;

	/**
	* Constructor.
	* 
	* Initializes db and parent properties.
	*/
	function LanguageManager($myLang) {
		global $lang_db, $addslashes;

		$this->db =& $lang_db;
		$this->addslashes = $addslashes;

		if (isset($myLang)) {
			$parent->Language($myLang);
		}
	}

	/**
	* Inserts a new language def'n into the database.
	* @access	public
	* @param	array $row		The language def'n fields as an assoc array.
	* @return	boolean|array	Returns TRUE if the def'n was inserted correctly, 
	*							an array of error messages or FALSE, otherwise.
	*/
    function addLanguage($row) {
		if($row['code'] == '') {
			$errors[] = AT_ERROR_LANG_CODE_MISSING;
		}
		if ($row['charset'] == '') {
			$errors[] = AT_ERROR_LANG_CHARSET_MISSING;
		}
		if ($row['reg_exp'] == '') {
			$errors[] = AT_ERROR_LANG_REGEX_MISSING;
		}
		if ($row['native_name'] == '') {
			$errors[] = AT_ERROR_LANG_NNAME_MISSING;
		}
		if ($row['english_name'] == '') {
			$errors[] = AT_ERROR_LANG_ENAME_MISSING;
		}

		if (isset($errors)) {
			$row['code']         = $this->addslashes($row['code']);
			$row['charset']      = $this->addslashes($row['charset']);
			$row['direction']    = $this->addslashes($row['direction']);
			$row['reg_exp']      = $this->addslashes($row['reg_exp']);
			$row['native_name']  = $this->addslashes($row['native_name']);
			$row['english_name'] = $this->addslashes($row['english_name']);

			$sql	= "INSERT INTO ".TABLE_PREFIX."languages VALUES ('$row[code]', '$row[charset]', '$row[direction]', '$row[reg_exp]', '$row[native_name]', '$row[english_name]')";
			if (mysql_query($sql, $this->db)) {
				return TRUE;
			} else {
				return FALSE;
			}
		}

		return $errors;
    }

	// public
	function updateTerm($variable, $key, $text) {
		$variable = $this->addslashes($variable);
		$key      = $this->addslashes($key);
		$text     = $this->addslashes($text);
		$code     = $this->addslashes($this->getCode());

		$sql	= "UPDATE ".TABLE_PREFIX_LANG."language_text SET text='$text', revised_date=NOW() WHERE language='$code' AND `variable`='$variable' AND `key`='$key'";

		if (mysql_query($sql, $this->db)) {
			return TRUE;
		} else {
			debug(mysql_error($this->db));
			return FALSE;
		}
	}

	//import lang package (sql)

	//export lang package (sql)

}
?>