<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

/**
* ModuleParser
* Class for parsing XML module info and returning a Module Object
* @access	public
* @author	Joel Kronenberg
* @package	Module
*/
class ModuleParser {

	// all private
	var $parser; // the XML handler
	var $rows = array(); // the module data used for creating the Module Object
	var $character_data; // tmp variable for storing the data
	var $element_path; // array of element paths (basically a stack)
	var $row_num;
	var $maintainer_num;

	var $maintainers = array();
	var $attributes;

	function ModuleParser() {
	}

	// public
	function parse($xml_data) {
		$this->element_path = array();
		$this->rows         = array();
		$this->character_data = '';
		$this->row_num        = 0;
		$this->maintainer_num = 0;

		$this->parser = xml_parser_create(''); 

		xml_set_object($this->parser, $this);
		xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, false); /* conform to W3C specs */
		xml_set_element_handler($this->parser, 'startElement', 'endElement');
		xml_set_character_data_handler($this->parser, 'characterData');
		xml_parse($this->parser, $xml_data, TRUE);
	}

	// public
	function getModule($row_num) {
		return new Module($this->rows[$row_num]);
	}

	// public
	function getNewModule($row_num) {
		//return new LanguageEditor($this->language_rows[$row_num]);
	}

	// private
	function startElement($parser, $name, $attributes) {
		array_push($this->element_path, $name);

		$this->attributes = $attributes;
   }

	// private
	/* called when an element ends */
	/* removed the current element from the $path */
	function endElement($parser, $name) {
		if ($this->element_path == array('module', 'name')) {
			if (isset($this->attributes['lang'])) {
				$this->rows[$this->row_num]['name'][$this->attributes['lang']] = trim($this->character_data);
			} else {
				$this->rows[$this->row_num]['name'][] = trim($this->character_data);
			}

		} else if ($this->element_path === array('module', 'description')) {
			if (isset($this->attributes['lang'])) {
				$this->rows[$this->row_num]['description'][$this->attributes['lang']] = trim($this->character_data);
			} else {
				$this->rows[$this->row_num]['description'][] = trim($this->character_data);
			}

		} else if ($this->element_path === array('module', 'url')) {
			$this->rows[$this->row_num]['url'] = trim($this->character_data);

		} else if ($this->element_path === array('module', 'license')) {
			$this->rows[$this->row_num]['license'] = trim($this->character_data);

		} else if ($this->element_path === array('module', 'maintainers', 'maintainer', 'name')) {
			$this->rows[$this->row_num]['maintainers'][$this->maintainer_num]['name'] = trim($this->character_data);

		} else if ($this->element_path === array('module', 'maintainers', 'maintainer', 'email')) {
			$this->rows[$this->row_num]['maintainers'][$this->maintainer_num]['email'] = trim($this->character_data);

			$this->maintainer_num++;

		} else if ($this->element_path === array('module', 'release', 'version')) {
			$this->rows[$this->row_num]['version'] = trim($this->character_data);

		} else if ($this->element_path === array('module', 'release', 'date')) {
			$this->rows[$this->row_num]['date'] = trim($this->character_data);

		} else if ($this->element_path === array('module', 'release', 'state')) {
			$this->rows[$this->row_num]['state'] = trim($this->character_data);

		} else if ($this->element_path === array('module', 'release', 'notes')) {
			$this->rows[$this->row_num]['notes'] = trim($this->character_data);

		} else if ($this->element_path === array('module')) {
			$this->row_num++;
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