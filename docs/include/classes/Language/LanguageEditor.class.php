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

	// array of missing terms
	var $missingTerms;

	/**
	* Constructor.
	* 
	* Initializes db and parent properties.
	*/
	function LanguageEditor($myLang) {
		global $lang_db, $addslashes;

		$this->db = $lang_db;
		$this->addslashes = $addslashes;

		if (isset($myLang)) {
			$this->Language($myLang);
		}
		$this->missingTerms = array();
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
			$addslashes = $this->addslashes;

			$row['code']         = $addslashes($row['code']);
			$row['charset']      = $addslashes($row['charset']);
			$row['direction']    = $addslashes($row['direction']);
			$row['reg_exp']      = $addslashes($row['reg_exp']);
			$row['native_name']  = $addslashes($row['native_name']);
			$row['english_name'] = $addslashes($row['english_name']);

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
		$addslashes = $this->addslashes;

		$variable = $addslashes($variable);
		$variable = $addslashes($variable);
		$key      = $addslashes($key);
		$text     = $addslashes($text);
		$code     = $addslashes($this->getCode());


		$sql	= "UPDATE ".TABLE_PREFIX_LANG."language_text SET text='$text', revised_date=NOW() WHERE language='$code' AND `variable`='$variable' AND `key`='$key'";

		/*
		if (mysql_query($sql, $this->db)) {
			return TRUE;
		} else {
			debug(mysql_error($this->db));
			return FALSE;
		}
		*/
	}

	// public
	function insertTerm($variable, $key, $text, $context) {
		$addslashes = $this->addslashes;

		$variable = $addslashes($variable);
		$key      = $addslashes($key);
		$text     = $addslashes($text);
		$code     = $addslashes($this->getCode());
		$context  = $addslashes($context);

		$sql = "INSERT INTO ".TABLE_PREFIX_LANG."language_text VALUES('$code', '$variable', '$key', '$text', NOW(), '$context')";
	}

	// public
	function showMissingTermsFrame(){
		global $_base_path, $addslashes;
		//$terms = array_slice($this->missingTerms, 0, 20);
		$terms = $this->missingTerms;
		$terms = serialize($terms);
		$terms = urlencode($terms);

		echo '<div align="center"><iframe src="'.$_base_path.'admin/missing_language.php?terms='.$terms.SEP.'lang='.$_SESSION['lang'].'" width="99%" height="300"></div>';
	}

	// public
	function printMissingTerms($terms){
		global $addslashes; // why won't $addslashes = $this->addslashes; work?

		$counter = 0;

		$terms = unserialize(stripslashes($addslashes($terms)));

		natcasesort($terms);

		echo '<table border="0">';
		foreach($terms as $term => $garbage) {
			if (($counter % 10) == 0) {
				echo '<tr>';
				echo '<td align="center"><input type="submit" name="submit" value="Submit" class="button" /></td>';
				echo '</tr>';
			}
			$this_term = $this->getText($term);

			$style = '';
			if (empty($this_term['to'])) {
				$style = 'style="background-color: white; border: red 2px solid;"';
			} else {
				$style = 'style="background-color: white; border: yellow 1px solid;"';
			}
			echo '<tr>';
			echo '<td><strong>'.htmlspecialchars($this_term['from']).'</strong></td></tr>';
			echo '<tr><td><input type="text" name="'.$term.'" '.$style.' size="100" value="'.htmlspecialchars($this_term['to']).'" /></td>';
			echo '</tr>';

			$counter++;
		}
		echo '</table>';
	}

	// public
	function addMissingTerm($term) {
		if (!isset($this->missingTerms[$term])) {
			$this->missingTerms[$term] = '';
		}
	}

	function getText($term) {
		global $lang_db;
		$to   = $_SESSION['lang'];
		$from = 'en';

		if ($from == $to) {
			$sql	= "SELECT L.text, L.language FROM ".TABLE_PREFIX_LANG."language_text L WHERE (L.language='en') AND L.variable='_template' AND L.key='$term'";
		} else {
			$sql	= "SELECT L.text, L.language FROM ".TABLE_PREFIX_LANG."language_text L WHERE (L.language='$_SESSION[lang]' OR L.language='en') AND L.variable='_template' AND L.key='$term'";
		}

		$result	 = mysql_query($sql, $lang_db);
		$row_one = mysql_fetch_assoc($result);
		$row_two = mysql_fetch_assoc($result);

		if ($row_one && $row_two) {
			if ($row_one['language'] == $_SESSION['lang']) {
				return array('to' => $row_one['text'], 'from' => $row_two['text']);
			} else {
				return array('to' => $row_two['text'], 'from' => $row_one['text']);
			}
		} // else:

		if ($from == $to) {
			$row_two = $row_one;
			$row_one['text'] = $term . ' : ';
		}

		return array('from' => $row_one['text'], 'to' => $row_two['text']);
	}

	// public
	function importLanguagePack($sql_or_pack) {
		// move sql import class from install/ to include/classes/
		// store the lang def'n in a .ini file and use insertLang 
		// after checking if it already exists

		// use the sql class to insert the language into the db
	}

	// sends the generated language pack to the browser
	// public
	function exportLanguagePack($sql_or_pack) {
		// use a function to generate the ini file
		// use a diff fn to generate the sql dump
		// use the zipfile class to package the ini file and the sql dump
	}

}
?>