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
* LanguageParser
* Class for parsing XML language info and returning a Language Object
* @access	public
* @author	Joel Kronenberg
* @package	Language
*/
class LanguageParser {

	// all private
	var $parser; // the XML handler
	var $language_row; // the language data used for creating the Language Object
	var $character_data; // tmp variable for storing the data
	var $element_path; // array of element paths (basically a stack)

	function LanguageParser() {
		$this->parser = xml_parser_create(); 

		xml_set_object($this->parser, &$this);
		xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, false); /* conform to W3C specs */
		xml_set_element_handler($this->parser, 'startElement', 'endElement');
		xml_set_character_data_handler($this->parser, 'characterData');

	}

	// public
	function parse($xml_data) {
		$this->element_path   = array();
		$this->language_row   = array();
		$this->character_data = '';
		xml_parse($this->parser, $xml_data, TRUE);
	}

	// public
	function getLanguageEditor() {
		return new LanguageEditor($this->language_row);
	}

	// private
	function startElement($parser, $name, $attributes) {
		array_push($this->element_path, $name);

		if ($this->element_path == array('language')) {
			$this->language_row['language_code'] = $attributes['code'];
		}
   }

	// private
	/* called when an element ends */
	/* removed the current element from the $path */
	function endElement($parser, $name) {
		if ($this->element_path == array('language', 'atutor-version')) {
			$this->language_row['version'] = trim($this->character_data);

		} else if ($this->element_path == array('language', 'charset')) {
			$this->language_row['char_set'] = trim($this->character_data);

		} else if ($this->element_path == array('language', 'direction')) {
			$this->language_row['direction'] = trim($this->character_data);

		} else if ($this->element_path == array('language', 'reg-exp')) {
			$this->language_row['reg_exp'] = trim($this->character_data);

		} else if ($this->element_path == array('language', 'native-name')) {
			$this->language_row['native_name'] = trim($this->character_data);

		} else if ($this->element_path == array('language', 'english-name')) {
			$this->language_row['english_name'] = trim($this->character_data);
		}

		array_pop($this->element_path);
		$this->character_data = '';
	}

	// private
   	function characterData($parser, $data){
		$this->character_data .= $data;
	}
}
?>