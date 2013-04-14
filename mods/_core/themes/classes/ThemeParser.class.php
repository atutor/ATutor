<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

/**
* ThemeParser
* Class for parsing XML language info and returning a Theme Object
* @access	public
* @author	Shozub Qureshi
* @package	Themes
*/
class ThemeParser {

	// all private
	var $parser; // the XML handler
	var $theme_rows = array(); // the language data used for creating the Language Object
	var $character_data; // tmp variable for storing the data
	var $element_path; // array of element paths (basically a stack)
	var $row_num;

	function ThemeParser() {
		$this->parser = xml_parser_create(); 

		xml_set_object($this->parser, $this);
		xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, false); /* conform to W3C specs */
		xml_set_element_handler($this->parser, 'startElement', 'endElement');
		xml_set_character_data_handler($this->parser, 'characterData');
	}

	// public
	function parse($xml_data) {
		$this->element_path   = array();
		$this->theme_rows  = array();
		$this->character_data = '';
		xml_parse($this->parser, $xml_data, TRUE);
	}

	// private
	function startElement($parser, $name, $attributes) {
		array_push($this->element_path, $name);
   }

	// private
	/* called when an element ends */
	/* removed the current element from the $path */
	function endElement($parser, $name) {
		if ($this->element_path == array('theme', 'dir_name')) {
			$this->theme_rows['dir_name'] = trim($this->character_data);

		} else if ($this->element_path == array('theme', 'title')) {
			$this->theme_rows['title'] = trim($this->character_data);

		} else if ($this->element_path == array('theme', 'version')) {
			$this->theme_rows['version'] = trim($this->character_data);

		} else if ($this->element_path == array('theme', 'type')) {
			$this->theme_rows['type'] = trim($this->character_data);

		} else if ($this->element_path == array('theme', 'last_updated')) {
			$this->theme_rows['last_updated'] = trim($this->character_data);

		} else if ($this->element_path == array('theme', 'extra_info')) {
			$this->theme_rows['extra_info'] = trim($this->character_data);

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