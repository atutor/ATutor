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
* @author	Joel Kronenberg
* @package	Language
*/
class LanguageEditor extends Language {

	var $addslashes;

	// array of missing terms
	var $missingTerms;

	// array of filters ['new', 'update']
	var $filters;

	/**
	* Constructor.
	* 
	* Initializes db and parent properties.
	*/
	function LanguageEditor($myLang) {
		global $lang_db, $addslashes;

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
	* call staticly only!
	*/
    function addLanguage($row, $db) {
		global $addslashes;

		$row['code']         = trim($row['code']);
		$row['locale']       = trim($row['locale']);
		$row['charset']      = trim($row['charset']);
		$row['reg_exp']      = trim($row['reg_exp']);
		$row['native_name']  = trim($row['native_name']);
		$row['english_name'] = trim($row['english_name']);

		if ($row['code'] == '') {
			$errors[] = AT_ERROR_LANG_CODE_MISSING;
		}
		if ($row['charset'] == '') {
			$errors[] = AT_ERROR_LANG_CHARSET_MISSING;
		}
		if ($row['native_name'] == '') {
			$errors[] = AT_ERROR_LANG_NNAME_MISSING;
		}
		if ($row['english_name'] == '') {
			$errors[] = AT_ERROR_LANG_ENAME_MISSING;
		}
		
		if (!isset($errors)) {
			$row['code']         = $addslashes($row['code']);
			$row['locale']       = $addslashes($row['locale']);
			$row['charset']      = $addslashes($row['charset']);
			$row['direction']    = $addslashes($row['direction']);
			$row['reg_exp']      = $addslashes($row['reg_exp']);
			$row['native_name']  = $addslashes($row['native_name']);
			$row['english_name'] = $addslashes($row['english_name']);

			if (!empty($row['locale'])) { 
				$row['code'] .= AT_LANGUAGE_LOCALE_SEP . strtolower($row['locale']);
			}

			$sql	= "INSERT INTO ".TABLE_PREFIX."languages VALUES ('$row[code]', '$row[charset]', '$row[direction]', '$row[reg_exp]', '$row[native_name]', '$row[english_name]', 0)";

			if (mysql_query($sql, $db)) {
				return TRUE;
			} else {
				return FALSE;
			}
		}

		return $errors;
    }

	// public
	// $row = the language info array
	// $new_exists whether the new code+locale exists already
    function updateLanguage($row, $new_exists) {
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

		if (!isset($errors)) {
			$addslashes = $this->addslashes;

			$row['code']         = strtolower($addslashes($row['code']));
			if (!empty($row['locale'])) { 
				$row['code'] .= AT_LANGUAGE_LOCALE_SEP . strtolower($addslashes($row['locale']));
			}
			$row['charset']      = strtolower($addslashes($row['charset']));
			$row['direction']    = strtolower($addslashes($row['direction']));
			$row['reg_exp']      = strtolower($addslashes($row['reg_exp']));
			$row['native_name']  = $addslashes($row['native_name']);
			$row['english_name'] = $addslashes($row['english_name']);

			if ($_POST['old_code'] == $_POST['code']) {
				$sql	= "UPDATE ".TABLE_PREFIX."languages SET char_set='$row[charset]', direction='$row[direction]', reg_exp='$row[reg_exp]', native_name='$row[native_name]', english_name='$row[english_name]' WHERE language_code='$row[code]'";
				mysql_query($sql, $this->db);

				return TRUE;
			} else if ($new_exists) {
				return $errors[] = AT_ERROR_LANG_EXISTS;
			} else {
				$sql	= "UPDATE ".TABLE_PREFIX."languages SET language_code='$row[code]', char_set='$row[charset]', direction='$row[direction]', reg_exp='$row[reg_exp]', native_name='$row[native_name]', english_name='$row[english_name]' WHERE language_code='$row[old_code]'";
				mysql_query($sql, $this->db);

				$sql = "UPDATE ".TABLE_PREFIX."language_text SET language_code='$row[code]' WHERE language_code='$row[old_code]'";
				mysql_query($sql, $this->db);

				return TRUE;
			}

		}
		return $errors;
    }

    function deleteLanguage() {
		$sql = "DELETE FROM ".TABLE_PREFIX."languages WHERE language_code='$this->code'";
		mysql_query($sql, $this->db);

		$sql = "DELETE FROM ".TABLE_PREFIX."language_text WHERE language_code='$this->code'";
		mysql_query($sql, $this->db);

		$sql = "UPDATE ".TABLE_PREFIX."members SET language='".DEFAULT_LANGUAGE."' WHERE language='$this->code'";

		mysql_query($sql, $this->db);

		cache_purge('system_langs', 'system_langs');
	}

	// public
	function updateTerm($variable, $term, $text) {
		$addslashes = $this->addslashes;

		$variable = $addslashes($variable);
		$term     = $addslashes($term);
		$text     = $addslashes($text);
		$code     = $addslashes($this->getCode());

		$sql	= "UPDATE ".TABLE_PREFIX_LANG."language_text SET text='$text', revised_date=NOW() WHERE language_code='$code' AND variable='$variable' AND term='$term'";

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
	// doesn't actually check if params is one of the possible ones.
	// possible params should be array ('new', 'update')
	function setFilter($params){
		if (!is_array($params)) {
			return;
		}

		foreach($params as $param => $garbage) {
			$this->filters[$param] = true;
		}
	}

	// private
	function checkFilter($param) {
		if ($this->filters[$param]) {
			return true;
		}
		return false;
	}

	// public
	function printTerms($terms){
		global $addslashes, $languageManager; // why won't $addslashes = $this->addslashes; work?

		$counter = 0;

		$terms = unserialize(stripslashes($addslashes($terms)));

		natcasesort($terms);

		if ($this->checkFilter('new')) {
			$new_check = ' checked="checked"';
		}
		if ($this->checkFilter('update')) {
			$update_check = ' checked="checked"';
		}

		$fromLanguage =& $languageManager->getLanguage('en');

		echo '<form method="post" action="'.$_SERVER['REQUEST_URI'].'">';
		echo '<table border="0" cellpadding="0" cellspacing="2">';
		echo '<tr>';
		echo '<td>Show: ';
		echo '<input name="filter_new" id="n" value="1" type="checkbox" '.$new_check.' /><label for="n">New Language</label>, ';
		echo '<input name="filter_update" id="u" value="1" type="checkbox" '.$update_check.' /><label for="u">Updated Language</label> ';
		echo '</td>';
		echo '</tr>';

		foreach($terms as $term => $garbage) {
			$to_term   = $this->getTerm($term);
			$from_term = $fromLanguage->getTerm($term);

			$is_new = false;
			if ($to_term === false) {
				$is_new = true;
			}

			$is_old = false;
			if ($to_term['revised_date_unix'] < $from_term['revised_date_unix']) {
				$is_old = true;
			}


			if ($this->checkFilter('new') && !$is_new) {
				continue;
			}

			if ($this->checkFilter('update') && !$is_old) {
				continue;
			}

			if (($counter % 10) == 0) {
				echo '<tr>';
				echo '<td align="center"><input type="submit" name="submit" value="Save Changes" class="button" /></td>';
				echo '</tr>';
			}

			$style = '';
			if ($is_new) {
				$style = 'style="background-color: white; border: red 2px solid;"';
			} else {
				$style = 'style="background-color: white; border: yellow 1px solid;"';
			}

			echo '<tr>';
			echo '<td><strong>[ ' . $term . ' ] '.htmlspecialchars($from_term['text']).'</strong></td></tr>';
			echo '<tr><td><input type="text" name="'.$term.'" '.$style.' size="100" value="'.htmlspecialchars($to_term['text']).'" />';
			echo '<input type="hidden" name="old['.$term.']" '.$style.' size="100" value="'.htmlspecialchars($to_term['text']).'" /></td>';
			echo '</tr>';

			$counter++;
		}
		echo '</table>';
		echo '</form>';
	}

	// public
	function updateTerms($terms) {
		global $addslashes;

		foreach($terms as $term => $text) {
			$text = $addslashes($text);
			$term = $addslashes($term);
		
			if (($text != '') && ($text != $_POST['old'][$term])) {
				$sql = "REPLACE INTO ".TABLE_PREFIX_LANG."language_text VALUES ('".$this->getCode()."', '_template', '$term', '$text', NOW(), '')";
				mysql_query($sql, $this->db);
			}
		}
	}

	// public
	function addMissingTerm($term) {
		if (!isset($this->missingTerms[$term])) {
			$this->missingTerms[$term] = '';
		}
	}


	// this method should be called staticly: LanguageEditor::import()
	// public
	function import($language_sql_file) {
		// move sql import class from install/ to include/classes/
		// store the lang def'n in a .ini file and use insertLang 
		// after checking if it already exists

		// use the sql class to insert the language into the db

		// check if this language exists before calling this method

		require(AT_INCLUDE_PATH . 'classes/sqlutility.class.php');
		$sqlUtility =& new SqlUtility();

		$sqlUtility->queryFromFile($language_sql_file, TABLE_PREFIX_LANG);

	}

	// sends the generated language pack to the browser
	// public
	function export($filename = '', $suffix = '') {
		$search  = array('"', "'", "\x00", "\x0a", "\x0d", "\x1a"); //\x08\\x09, not required
		$replace = array('\"', "\'", '\0', '\n', '\r', '\Z');

		// use a function to generate the ini file
		// use a diff fn to generate the sql dump
		// use the zipfile class to package the ini file and the sql dump
		$sql_dump = "INSERT INTO `languages` VALUES ('$this->code', '$this->characterSet', '$this->direction', '$this->regularExpression', '$this->nativeName', '$this->englishName', $this->status);\r\n\r\n";

		$sql_dump .= "INSERT INTO `language_text` VALUES ";

		$sql    = "SELECT * FROM ".TABLE_PREFIX_LANG."language_text".$suffix." WHERE language_code='$this->code' ORDER BY variable, term";
		$result = mysql_query($sql, $this->db);
		if ($row = mysql_fetch_assoc($result)) {
			do {
				$row['text']    = str_replace($search, $replace, $row['text']);
				$row['context'] = str_replace($search, $replace, $row['context']);

				$sql_dump .= "('$this->code', '$row[variable]', '$row[term]', '$row[text]', '$row[revised_date]', '$row[context]'),\r\n";
			} while ($row = mysql_fetch_assoc($result));
		} else {
			$errors[] = AT_ERROR_LANG_EMPTY;
			return $errors;
		}
		$sql_dump = substr($sql_dump, 0, -3) . ";";

		$readme = 'this is an ATutor language pack. use the administrator Language section to import this language pack or manually import the contents of the SQL file into your [table_prefix]language_text table. Note that [table_prefix] should be replaced with your correct ATutor table prefix as defined in your config.inc.php file. Additional Language Packs can be found on the http://atutor.ca website.';

		$xml = '<?xml version="1.0" encoding="iso-8859-1"?>
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
]>

<language code="'.$this->code.'">
	<atutor-version>'.VERSION.'</atutor-version>
	<charset>'.$this->characterSet.'</charset>
	<direction>'.$this->direction.'</direction>
	<reg-exp>'.$this->regularExpression.'</reg-exp>
	<native-name>'.$this->nativeName.'</native-name>
	<english-name>'.$this->englishName.'</english-name>
	<status>'.$this->status.'</status>
</language>';

		require(AT_INCLUDE_PATH . 'classes/zipfile.class.php');
		$zipfile =& new zipfile();

		$zipfile->add_file($sql_dump, 'language_text.sql');
		$zipfile->add_file($readme, 'readme.txt');
		$zipfile->add_file($xml, 'language.xml');

		if ($filename) {
			$fp = fopen($filename, 'wb+');
			fwrite($fp, $zipfile->get_file(), $zipfile->get_size());
		} else {
			$zipfile->send_file('atutor_' . VERSION . '_' . $this->code);
		}
	}

}
?>